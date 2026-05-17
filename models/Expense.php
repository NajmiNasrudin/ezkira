<?php

namespace Models;

class Expense
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function monthlyTotals(int $year, int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT MONTH(expense_date) AS m, COALESCE(SUM(amount), 0) AS total
             FROM expenses WHERE YEAR(expense_date) = ? AND user_id = ?
             GROUP BY MONTH(expense_date)'
        );
        $stmt->execute([$year, $userId]);
        $result = array_fill(1, 12, 0.0);
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $result[(int)$row['m']] = (float)$row['total'];
        }
        return $result;
    }

    public function recentTransactions(int $userId, int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, amount, category, description, expense_date AS txn_date, "expense" AS type
             FROM expenses
             WHERE user_id = ?
             ORDER BY expense_date DESC, created_at DESC
             LIMIT ?'
        );
        $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit,  \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Returns daily expense totals (opex+marketing+cogs) keyed by 'Y-m-d' for a given month.
     * Only returns rows that have data; caller fills zeros for missing days.
     */
    public function dailyTotals(int $year, int $month, int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT DATE_FORMAT(expense_date,'%Y-%m-%d') AS d, COALESCE(SUM(amount),0) AS total
             FROM expenses
             WHERE user_id = ? AND category IN ('opex','marketing','cogs')
               AND YEAR(expense_date) = ? AND MONTH(expense_date) = ?
             GROUP BY DATE(expense_date)"
        );
        $stmt->execute([$userId, $year, $month]);
        $result = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $r) {
            $result[$r['d']] = (float)$r['total'];
        }
        return $result;
    }

    public function byCategory(string $category, int $userId, int $year = 0, int $month = 0): array
    {
        $where  = 'e.category = ? AND e.user_id = ?';
        $params = [$category, $userId];
        if ($year > 0 && $month > 0) {
            $where .= ' AND YEAR(e.expense_date) = ? AND MONTH(e.expense_date) = ?';
            $params[] = $year;
            $params[] = $month;
        }
        $stmt = $this->db->prepare(
            "SELECT e.*, u.name AS added_by
             FROM expenses e
             JOIN users u ON e.user_id = u.id
             WHERE {$where}
             ORDER BY e.expense_date DESC, e.created_at DESC"
        );
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function totalByCategory(string $category, int $userId, int $year = 0, int $month = 0, string $date = '', int $week = 0): float
    {
        $where  = 'category = ? AND user_id = ?';
        $params = [$category, $userId];

        if ($date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            // Daily — exact date
            $where .= ' AND expense_date = ?';
            $params[] = $date;
        } elseif ($year > 0 && $week > 0) {
            // Weekly — ISO week number within a year
            $where .= ' AND YEAR(expense_date) = ? AND WEEK(expense_date, 3) = ?';
            $params[] = $year;
            $params[] = $week;
        } elseif ($year > 0 && $month > 0) {
            // Monthly
            $where .= ' AND YEAR(expense_date) = ? AND MONTH(expense_date) = ?';
            $params[] = $year;
            $params[] = $month;
        } elseif ($year > 0) {
            // Annual — full year
            $where .= ' AND YEAR(expense_date) = ?';
            $params[] = $year;
        }
        // else: no date filter (all-time) — should not happen on dashboard

        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE {$where}"
        );
        $stmt->execute($params);
        return (float) $stmt->fetchColumn();
    }

    public function countAll(int $userId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM expenses WHERE user_id = ?');
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO expenses
             (user_id, category, amount, description, expense_date, receipt_path, receipt_name)
             VALUES (:user_id, :category, :amount, :description, :expense_date, :receipt_path, :receipt_name)'
        );
        $stmt->execute([
            ':user_id'      => $data['user_id'],
            ':category'     => $data['category'],
            ':amount'       => $data['amount'],
            ':description'  => $data['description'],
            ':expense_date' => $data['expense_date'],
            ':receipt_path' => $data['receipt_path'] ?? null,
            ':receipt_name' => $data['receipt_name'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM expenses WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE expenses SET category=?, amount=?, description=?, expense_date=?, updated_at=NOW()
             WHERE id=? AND user_id=?'
        );
        return $stmt->execute([
            $data['category'],
            $data['amount'],
            $data['description'],
            $data['expense_date'],
            $id,
            $data['user_id'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM expenses WHERE id = ?');
        return $stmt->execute([$id]);
    }

    // -------------------------------------------------------------------------
    // Multiple Receipts
    // -------------------------------------------------------------------------

    public function addReceipt(int $expenseId, string $path, string $name): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO expense_receipts (expense_id, path, name) VALUES (?, ?, ?)'
        );
        $stmt->execute([$expenseId, $path, $name]);
    }

    public function getReceipts(int $expenseId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM expense_receipts WHERE expense_id = ? ORDER BY id ASC'
        );
        $stmt->execute([$expenseId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function deleteReceipts(int $expenseId): array
    {
        $receipts = $this->getReceipts($expenseId);
        $stmt = $this->db->prepare('DELETE FROM expense_receipts WHERE expense_id = ?');
        $stmt->execute([$expenseId]);
        return $receipts; // return paths so controller can delete files
    }

    public function findReceiptById(int $receiptId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM expense_receipts WHERE id = ?');
        $stmt->execute([$receiptId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function deleteReceiptById(int $receiptId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM expense_receipts WHERE id = ?');
        return $stmt->execute([$receiptId]);
    }
}
