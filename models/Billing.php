<?php

namespace Models;

use PDO;

class Billing
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getProUntil(int $userId): ?string
    {
        $stmt = $this->db->prepare('SELECT pro_until FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$userId]);
        $value = $stmt->fetchColumn();
        return $value ?: null;
    }

    /**
     * Receipts currently stored for a user: multi-file receipts plus the legacy single
     * receipt column on expenses. Sizes missing from old rows are filled from disk once.
     */
    public function receiptUsage(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT r.id, r.path, r.size_bytes
               FROM expense_receipts r
               JOIN expenses e ON e.id = r.expense_id
              WHERE e.user_id = ?'
        );
        $stmt->execute([$userId]);

        $count = 0;
        $bytes = 0;
        $fill  = $this->db->prepare('UPDATE expense_receipts SET size_bytes = ? WHERE id = ?');
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $count++;
            if ($row['size_bytes'] === null) {
                $full = BASE_PATH . '/' . $row['path'];
                $size = is_file($full) ? (int) filesize($full) : 0;
                $fill->execute([$size, $row['id']]);
                $bytes += $size;
            } else {
                $bytes += (int) $row['size_bytes'];
            }
        }

        $legacy = $this->db->prepare(
            "SELECT receipt_path FROM expenses WHERE user_id = ? AND receipt_path IS NOT NULL AND receipt_path <> ''"
        );
        $legacy->execute([$userId]);
        foreach ($legacy->fetchAll(PDO::FETCH_COLUMN) as $path) {
            $count++;
            $full = BASE_PATH . '/' . $path;
            $bytes += is_file($full) ? (int) filesize($full) : 0;
        }

        return ['count' => $count, 'bytes' => $bytes];
    }

    public function createPayment(int $userId, string $interval, int $amountSen): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO payments (user_id, gateway, plan_interval, amount_sen, status) VALUES (?, 'chip', ?, ?, 'pending')"
        );
        $stmt->execute([$userId, $interval, $amountSen]);
        return (int) $this->db->lastInsertId();
    }

    public function setGatewayRef(int $paymentId, string $ref): void
    {
        $stmt = $this->db->prepare('UPDATE payments SET gateway_ref = ? WHERE id = ?');
        $stmt->execute([$ref, $paymentId]);
    }

    public function markFailed(int $paymentId): void
    {
        $stmt = $this->db->prepare("UPDATE payments SET status = 'failed' WHERE id = ? AND status = 'pending'");
        $stmt->execute([$paymentId]);
    }

    public function findPayment(int $paymentId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM payments WHERE id = ? LIMIT 1');
        $stmt->execute([$paymentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findPaymentByRef(string $ref): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM payments WHERE gateway_ref = ? LIMIT 1');
        $stmt->execute([$ref]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function paidPayments(int $userId, int $limit = 12): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM payments WHERE user_id = ? AND status = 'paid' ORDER BY paid_at DESC LIMIT " . (int) $limit
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mark a payment paid and extend the user's Pro period exactly once, even if the
     * gateway callback and the browser return arrive at the same time or repeatedly.
     * $periodEnd receives the user's resulting pro_until.
     */
    public function activatePayment(int $paymentId, bool $isTest, callable $periodFor, ?string &$periodEnd = null): bool
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('SELECT * FROM payments WHERE id = ? FOR UPDATE');
            $stmt->execute([$paymentId]);
            $payment = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$payment || $payment['status'] === 'paid') {
                $this->db->commit();
                $periodEnd = $payment['period_end'] ?? null;
                return false;
            }

            $user = $this->db->prepare('SELECT pro_until FROM users WHERE id = ? FOR UPDATE');
            $user->execute([$payment['user_id']]);
            $currentUntil = $user->fetchColumn() ?: null;

            [$start, $end] = $periodFor($payment['plan_interval'], $currentUntil);

            $this->db->prepare(
                "UPDATE payments SET status = 'paid', is_test = ?, period_start = ?, period_end = ?, paid_at = NOW() WHERE id = ?"
            )->execute([$isTest ? 1 : 0, $start, $end, $paymentId]);

            $this->db->prepare('UPDATE users SET pro_until = ? WHERE id = ?')
                ->execute([$end, $payment['user_id']]);

            $this->db->commit();
            $periodEnd = $end;
            return true;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
