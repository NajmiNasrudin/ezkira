<?php

namespace Models;

use App\Core\Plan;
use PDO;

/**
 * Read-only numbers for the owner's admin dashboard (/dashboard-admin).
 * Only 'client' accounts are counted as customers; admin/team accounts are left out.
 * "Active" = logged an action (login, save, etc.) or keyed in a sale/expense in the window.
 */
class AdminStats
{
    private PDO $db;
    private bool $billing;
    private string $now;

    public function __construct(bool $billingReady)
    {
        $this->db      = getDB();
        $this->billing = $billingReady;
        $this->now     = Plan::now()->format('Y-m-d H:i:s');
    }

    private function value(string $sql, array $params = []): float
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (float) $stmt->fetchColumn();
    }

    private function rows(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** SQL for every (user id, time) a user did something; used by the active-user counts. */
    private const ACTIVITY_SQL =
        "SELECT user_id AS uid, created_at FROM activity_logs WHERE user_id IS NOT NULL
         UNION ALL SELECT user_id, created_at FROM revenues
         UNION ALL SELECT user_id, created_at FROM expenses";

    public function users(): array
    {
        $c = "role = 'client'";
        $proActive  = $this->billing ? (int) $this->value("SELECT COUNT(*) FROM users WHERE $c AND pro_until > ?", [$this->now]) : 0;
        $proExpired = $this->billing ? (int) $this->value("SELECT COUNT(*) FROM users WHERE $c AND pro_until IS NOT NULL AND pro_until <= ?", [$this->now]) : 0;
        $total      = (int) $this->value("SELECT COUNT(*) FROM users WHERE $c");

        return [
            'total'       => $total,
            'pro'         => $proActive,
            'free'        => $total - $proActive,
            'pro_expired' => $proExpired,
            'today'       => (int) $this->value("SELECT COUNT(*) FROM users WHERE $c AND created_at >= CURDATE()"),
            'last7'       => (int) $this->value("SELECT COUNT(*) FROM users WHERE $c AND created_at >= NOW() - INTERVAL 7 DAY"),
            'last30'      => (int) $this->value("SELECT COUNT(*) FROM users WHERE $c AND created_at >= NOW() - INTERVAL 30 DAY"),
            'this_month'  => (int) $this->value("SELECT COUNT(*) FROM users WHERE $c AND created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')"),
            'google'      => (int) $this->value("SELECT COUNT(*) FROM users WHERE $c AND google_id IS NOT NULL AND google_id <> ''"),
            'staff'       => (int) $this->value("SELECT COUNT(*) FROM users WHERE role <> 'client'"),
        ];
    }

    public function active(): array
    {
        $count = fn(string $interval) => (int) $this->value(
            "SELECT COUNT(DISTINCT a.uid) FROM (" . self::ACTIVITY_SQL . ") a
               JOIN users u ON u.id = a.uid AND u.role = 'client'
              WHERE a.created_at >= NOW() - INTERVAL $interval"
        );

        $withRecords = (int) $this->value(
            "SELECT COUNT(*) FROM users u WHERE u.role = 'client'
                AND (EXISTS (SELECT 1 FROM revenues r WHERE r.user_id = u.id)
                  OR EXISTS (SELECT 1 FROM expenses e WHERE e.user_id = u.id))"
        );

        return [
            'day'          => $count('1 DAY'),
            'week'         => $count('7 DAY'),
            'month'        => $count('30 DAY'),
            'with_records' => $withRecords,
        ];
    }

    public function revenue(): array
    {
        if (!$this->billing) {
            return ['total' => 0, 'this_month' => 0, 'paid_count' => 0, 'monthly' => 0, 'yearly' => 0, 'pending' => 0, 'failed' => 0];
        }
        $paid = "status = 'paid' AND is_test = 0";

        return [
            'total'      => $this->value("SELECT COALESCE(SUM(amount_sen), 0) FROM payments WHERE $paid") / 100,
            'this_month' => $this->value("SELECT COALESCE(SUM(amount_sen), 0) FROM payments WHERE $paid AND paid_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')") / 100,
            'paid_count' => (int) $this->value("SELECT COUNT(*) FROM payments WHERE $paid"),
            'monthly'    => (int) $this->value("SELECT COUNT(*) FROM payments WHERE $paid AND plan_interval = 'monthly'"),
            'yearly'     => (int) $this->value("SELECT COUNT(*) FROM payments WHERE $paid AND plan_interval = 'yearly'"),
            'pending'    => (int) $this->value("SELECT COUNT(*) FROM payments WHERE status = 'pending' AND created_at >= NOW() - INTERVAL 30 DAY"),
            'failed'     => (int) $this->value("SELECT COUNT(*) FROM payments WHERE status = 'failed' AND created_at >= NOW() - INTERVAL 30 DAY"),
        ];
    }

    public function usage(): array
    {
        // size_bytes only exists once the billing migration has run
        $receipts = $this->rows(
            $this->billing
                ? "SELECT COUNT(*) AS n, COALESCE(SUM(size_bytes), 0) AS bytes FROM expense_receipts"
                : "SELECT COUNT(*) AS n, 0 AS bytes FROM expense_receipts"
        )[0] ?? ['n' => 0, 'bytes' => 0];

        return [
            'revenues'       => (int) $this->value("SELECT COUNT(*) FROM revenues"),
            'expenses'       => (int) $this->value("SELECT COUNT(*) FROM expenses"),
            'receipts'       => (int) $receipts['n'],
            'receipt_bytes'  => (int) $receipts['bytes'],
            'revenue_amount' => $this->value("SELECT COALESCE(SUM(amount), 0) FROM revenues"),
            'expense_amount' => $this->value("SELECT COALESCE(SUM(amount), 0) FROM expenses"),
        ];
    }

    /** Sign-ups per day for the last $days days, oldest first, zero-filled. */
    public function signupsPerDay(int $days = 30): array
    {
        $found = [];
        foreach ($this->rows(
            "SELECT DATE(created_at) AS d, COUNT(*) AS n FROM users
              WHERE role = 'client' AND created_at >= CURDATE() - INTERVAL " . ($days - 1) . " DAY
              GROUP BY DATE(created_at)"
        ) as $r) {
            $found[$r['d']] = (int) $r['n'];
        }

        // Use the database's own "today" so the buckets line up with created_at
        $today = (string) $this->rows('SELECT CURDATE() AS d')[0]['d'];
        $out   = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("$today -$i day"));
            $out[$d] = $found[$d] ?? 0;
        }
        return $out;
    }

    /** Sign-ups per month for the last 12 months, oldest first, zero-filled. */
    public function signupsPerMonth(): array
    {
        $found = [];
        foreach ($this->rows(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS m, COUNT(*) AS n FROM users
              WHERE role = 'client' AND created_at >= DATE_FORMAT(CURDATE() - INTERVAL 11 MONTH, '%Y-%m-01')
              GROUP BY m"
        ) as $r) {
            $found[$r['m']] = (int) $r['n'];
        }

        $first = substr((string) $this->rows('SELECT CURDATE() AS d')[0]['d'], 0, 7) . '-01';
        $out   = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = date('Y-m', strtotime("$first -$i month"));
            $out[$m] = $found[$m] ?? 0;
        }
        return $out;
    }

    public function businessTypes(): array
    {
        return $this->rows(
            "SELECT COALESCE(NULLIF(business_type, ''), '-') AS type, COUNT(*) AS n
               FROM users WHERE role = 'client' GROUP BY type ORDER BY n DESC"
        );
    }

    public function languages(): array
    {
        return $this->rows("SELECT language, COUNT(*) AS n FROM users WHERE role = 'client' GROUP BY language ORDER BY n DESC");
    }

    private function userColumns(): string
    {
        $pro = $this->billing ? 'u.pro_until' : 'NULL AS pro_until';
        return "u.id, u.name, u.email, u.whatsapp_number, u.business_type, u.created_at, $pro,
                (u.google_id IS NOT NULL AND u.google_id <> '') AS via_google,
                (SELECT COUNT(*) FROM revenues r WHERE r.user_id = u.id) AS n_revenues,
                (SELECT COUNT(*) FROM expenses e WHERE e.user_id = u.id) AS n_expenses,
                GREATEST(
                    COALESCE((SELECT MAX(created_at) FROM activity_logs l WHERE l.user_id = u.id), '1970-01-01'),
                    COALESCE((SELECT MAX(created_at) FROM revenues r2 WHERE r2.user_id = u.id), '1970-01-01'),
                    COALESCE((SELECT MAX(created_at) FROM expenses e2 WHERE e2.user_id = u.id), '1970-01-01')
                ) AS last_active";
    }

    public function recentUsers(int $limit = 20): array
    {
        return $this->rows(
            "SELECT {$this->userColumns()} FROM users u WHERE u.role = 'client'
              ORDER BY u.created_at DESC LIMIT " . (int) $limit
        );
    }

    public function topUsers(int $limit = 10): array
    {
        return $this->rows(
            "SELECT * FROM (SELECT {$this->userColumns()} FROM users u WHERE u.role = 'client') t
              WHERE n_revenues + n_expenses > 0
              ORDER BY n_revenues + n_expenses DESC LIMIT " . (int) $limit
        );
    }

    /** Free users who already store at least the free receipt limit: likely Pro buyers. */
    public function upsellCandidates(int $limit = 20): array
    {
        $proFilter = $this->billing ? 'AND (u.pro_until IS NULL OR u.pro_until <= ?)' : '';
        $params    = $this->billing ? [$this->now] : [];
        $params[]  = Plan::FREE_RECEIPT_LIMIT;

        return $this->rows(
            "SELECT u.id, u.name, u.email, u.whatsapp_number, COUNT(r.id) AS n_receipts
               FROM users u
               JOIN expenses e ON e.user_id = u.id
               JOIN expense_receipts r ON r.expense_id = e.id
              WHERE u.role = 'client' $proFilter
              GROUP BY u.id, u.name, u.email, u.whatsapp_number
             HAVING n_receipts >= ?
              ORDER BY n_receipts DESC LIMIT " . (int) $limit,
            $params
        );
    }

    public function recentPayments(int $limit = 20): array
    {
        if (!$this->billing) {
            return [];
        }
        return $this->rows(
            "SELECT p.*, u.name, u.email FROM payments p
               LEFT JOIN users u ON u.id = p.user_id
              ORDER BY p.created_at DESC LIMIT " . (int) $limit
        );
    }
}
