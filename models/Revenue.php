<?php

namespace Models;

class Revenue
{
    private \PDO $db;

    public const PLATFORMS = [
        'shopee'   => 'Shopee',
        'lazada'   => 'Lazada',
        'tiktok'   => 'TikTok Shop',
        'website'  => 'Website',
        'walkin'   => 'Walk-in / Counter',
        'whatsapp' => 'WhatsApp',
        'other'    => 'Other',
    ];

    public const ENTRY_TYPES = ['sale', 'refund'];

    public const PAYMENT_METHODS = [
        'cash'           => 'Cash',
        'online_banking' => 'Online Banking',
        'card'           => 'Debit / Credit Card',
        'ewallet'        => 'E-Wallet',
        'other'          => 'Others',
    ];

    public function __construct()
    {
        $this->db = getDB();
    }

    // -------------------------------------------------------------------------
    // Entries
    // -------------------------------------------------------------------------

    public function monthlyTotals(int $year, int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT MONTH(sale_date) AS m,
                    COALESCE(SUM(CASE WHEN entry_type = 'refund' THEN -amount ELSE amount END), 0) AS total
             FROM revenues WHERE YEAR(sale_date) = ? AND user_id = ?
             GROUP BY MONTH(sale_date)"
        );
        $stmt->execute([$year, $userId]);
        $result = array_fill(1, 12, 0.0);
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $result[(int)$row['m']] = (float)$row['total'];
        }
        return $result;
    }

    public function recentTransactions(int $userId, int $limit = 10, string $period = '', int $year = 0, int $month = 0, int $week = 0, string $date = ''): array
    {
        $where  = 'user_id = ?';
        $params = [$userId];

        if ($date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $where .= ' AND sale_date = ?';
            $params[] = $date;
        } elseif ($period === 'weekly' && $year > 0 && $week > 0) {
            $where .= ' AND YEAR(sale_date) = ? AND WEEK(sale_date, 3) = ?';
            $params[] = $year;
            $params[] = $week;
        } elseif ($period === 'monthly' && $year > 0 && $month > 0) {
            $where .= ' AND YEAR(sale_date) = ? AND MONTH(sale_date) = ?';
            $params[] = $year;
            $params[] = $month;
        } elseif ($period === 'annual' && $year > 0) {
            $where .= ' AND YEAR(sale_date) = ?';
            $params[] = $year;
        }

        $limit = max(1, (int)$limit);
        $stmt  = $this->db->prepare(
            "SELECT id, amount, entry_type, platform AS category, description, sale_date AS txn_date, 'revenue' AS type
             FROM revenues WHERE {$where}
             ORDER BY sale_date DESC, created_at DESC LIMIT {$limit}"
        );
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function byPeriod(string $period, int $year, int $month, int $userId, int $week = 0, string $date = ''): array
    {
        [$where, $params] = $this->periodWhere($period, $year, $month, $week, $userId, $date);
        $stmt = $this->db->prepare(
            "SELECT r.*, u.name AS added_by
             FROM revenues r
             JOIN users u ON r.user_id = u.id
             WHERE {$where}
             ORDER BY r.sale_date DESC, r.created_at DESC"
        );
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function totalByPeriod(string $period, int $year, int $month, int $userId, int $week = 0, string $date = ''): float
    {
        [$where, $params] = $this->periodWhere($period, $year, $month, $week, $userId, $date);
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(CASE WHEN entry_type = 'refund' THEN -amount ELSE amount END), 0)
             FROM revenues WHERE {$where}"
        );
        $stmt->execute($params);
        return (float) $stmt->fetchColumn();
    }

    public function platformBreakdown(string $period, int $year, int $month, int $userId, int $week = 0, string $date = ''): array
    {
        [$where, $params] = $this->periodWhere($period, $year, $month, $week, $userId, $date);
        $stmt = $this->db->prepare(
            "SELECT platform, COALESCE(SUM(CASE WHEN entry_type = 'refund' THEN -amount ELSE amount END), 0) AS total
             FROM revenues WHERE {$where}
             GROUP BY platform ORDER BY total DESC"
        );
        $stmt->execute($params);
        // Filter out platforms with zero or negative total only if there are positive ones
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function dailyTotals(int $year, int $month, int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT sale_date, COALESCE(SUM(CASE WHEN entry_type = 'refund' THEN -amount ELSE amount END), 0) AS total
             FROM revenues
             WHERE YEAR(sale_date) = ? AND MONTH(sale_date) = ? AND user_id = ?
             GROUP BY sale_date ORDER BY sale_date ASC"
        );
        $stmt->execute([$year, $month, $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO revenues (user_id, platform, entry_type, payment_method, amount, description, sale_date)
             VALUES (:user_id, :platform, :entry_type, :payment_method, :amount, :description, :sale_date)'
        );
        $stmt->execute([
            ':user_id'        => $data['user_id'],
            ':platform'       => $data['platform'],
            ':entry_type'     => $data['entry_type']     ?? 'sale',
            ':payment_method' => $data['payment_method'] ?? 'cash',
            ':amount'         => $data['amount'],
            ':description'    => $data['description'],
            ':sale_date'      => $data['sale_date'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM revenues WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE revenues SET platform=:platform, entry_type=:entry_type, payment_method=:payment_method,
             amount=:amount, description=:description, sale_date=:sale_date
             WHERE id=:id AND user_id=:user_id'
        );
        return $stmt->execute([
            ':platform'       => $data['platform'],
            ':entry_type'     => $data['entry_type']     ?? 'sale',
            ':payment_method' => $data['payment_method'] ?? 'cash',
            ':amount'         => $data['amount'],
            ':description'    => $data['description'],
            ':sale_date'      => $data['sale_date'],
            ':id'             => $id,
            ':user_id'        => $data['user_id'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM revenues WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function countAll(int $userId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM revenues WHERE user_id = ?');
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    // -------------------------------------------------------------------------
    // Monthly Targets
    // -------------------------------------------------------------------------

    public function getTarget(int $year, int $month, int $userId): float
    {
        $stmt = $this->db->prepare(
            'SELECT target_amount FROM revenue_targets WHERE user_id = ? AND year = ? AND month = ? LIMIT 1'
        );
        $stmt->execute([$userId, $year, $month]);
        $row = $stmt->fetch();
        return $row ? (float) $row['target_amount'] : 0;
    }

    public function setTarget(int $year, int $month, float $amount, int $userId): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO revenue_targets (user_id, year, month, target_amount)
             VALUES (:u, :y, :m, :a)
             ON DUPLICATE KEY UPDATE target_amount = :a2, updated_at = NOW()'
        );
        $stmt->execute([':u' => $userId, ':y' => $year, ':m' => $month, ':a' => $amount, ':a2' => $amount]);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function periodWhere(string $period, int $year, int $month, int $week, int $userId, string $date = ''): array
    {
        $dailyDate = ($date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) ? $date : date('Y-m-d');
        return match($period) {
            'annual'  => ['YEAR(sale_date) = ? AND user_id = ?', [$year, $userId]],
            'weekly'  => ['YEAR(sale_date) = ? AND WEEK(sale_date, 1) = ? AND user_id = ?', [$year, $week, $userId]],
            'daily'   => ['sale_date = ? AND user_id = ?', [$dailyDate, $userId]],
            default   => ['YEAR(sale_date) = ? AND MONTH(sale_date) = ? AND user_id = ?', [$year, $month, $userId]],
        };
    }
}
