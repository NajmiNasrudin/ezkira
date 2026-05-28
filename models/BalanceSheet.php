<?php

namespace Models;

class BalanceSheet
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    // -------------------------------------------------------------------------
    // Schema: sections → items (matches image exactly)
    // -------------------------------------------------------------------------

    public const SECTIONS = [
        'non_current_asset' => [
            'label' => 'Non-Current Asset',
            'bold'  => true,
            'items' => [
                'ppe' => 'Property, plant and equipment',
            ],
            'total_label' => 'Total non-current asset',
        ],
        'current_asset' => [
            'label' => 'Current Assets',
            'bold'  => true,
            'items' => [
                'inventories'  => 'Inventories',
                'trade_recv'   => 'Trade receivables',
                'other_recv'   => 'Other receivables, deposits and prepayments',
                'due_director' => 'Amount due from director',
                'tax_recover'  => 'Tax recoverable',
                'cash'         => 'Cash and cash equivalents',
            ],
            'total_label' => 'Total current assets',
        ],
        'equity' => [
            'label' => 'Equity',
            'bold'  => true,
            'items' => [
                'share_capital' => 'Share capital',
                'accum_losses'  => 'Accumulated losses',
            ],
            'total_label' => 'Total Equity',
        ],
        'non_current_liability' => [
            'label' => 'Non-Current Liability',
            'bold'  => true,
            'items' => [
                'bank_borrow_nc' => 'Bank borrowings',
            ],
            'total_label' => 'Total non-current liability',
        ],
        'current_liability' => [
            'label' => 'Current Liabilities',
            'bold'  => true,
            'items' => [
                'trade_pay'   => 'Trade payables',
                'other_pay'   => 'Other payables and accruals',
                'bank_borrow' => 'Bank borrowings',
                'tax_pay'     => 'Tax payable',
            ],
            'total_label' => 'Total current liabilities',
        ],
    ];

    // -------------------------------------------------------------------------
    // CRUD
    // -------------------------------------------------------------------------

    /**
     * Get all entries for a user on a specific date, keyed as section.item_key => amount.
     */
    public function getByDate(int $userId, string $date): array
    {
        $stmt = $this->db->prepare(
            'SELECT section, item_key, amount FROM balance_sheet_entries
             WHERE user_id = ? AND as_of_date = ?'
        );
        $stmt->execute([$userId, $date]);
        $result = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $result[$row['section']][$row['item_key']] = (float)$row['amount'];
        }
        return $result;
    }

    /**
     * Upsert a single line item.
     */
    public function upsert(int $userId, string $date, string $section, string $itemKey, float $amount): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO balance_sheet_entries (user_id, as_of_date, section, item_key, amount)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE amount = VALUES(amount), updated_at = NOW()'
        );
        $stmt->execute([$userId, $date, $section, $itemKey, $amount]);
    }

    /**
     * Save all items for a user on a given date in one transaction.
     * $data = ['section' => ['item_key' => amount, ...], ...]
     */
    public function saveAll(int $userId, string $date, array $data): void
    {
        $this->db->beginTransaction();
        try {
            foreach ($data as $section => $items) {
                if (!array_key_exists($section, self::SECTIONS)) continue;
                foreach ($items as $key => $amount) {
                    if (!array_key_exists($key, self::SECTIONS[$section]['items'])) continue;
                    $this->upsert($userId, $date, $section, $key, (float)$amount);
                }
            }
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // -------------------------------------------------------------------------
    // Auto-calculation from Revenue / Expense / Capital data
    // -------------------------------------------------------------------------

    /**
     * Derive balance sheet values from recorded transactions up to $asOfDate.
     *
     * Returns:
     *   auto_cash            → Cash & cash equivalents  (Capital + Revenue − All expenses)
     *   auto_ppe             → PPE (sum of 'ppe' expenses)
     *   auto_inventory       → Inventories (sum of 'inventory' expenses)
     *   auto_share_capital   → Share capital (sum of capital injections)
     *   auto_retained        → Retained earnings / accumulated losses (P&L net profit)
     *   _pnl                 → Sub-array with P&L breakdown (for P&L summary widget)
     */
    public function autoCalculate(int $userId, string $asOfDate): array
    {
        $db = $this->db;

        // ── Revenue up to date ────────────────────────────────────────────────
        $s = $db->prepare(
            "SELECT COALESCE(SUM(CASE WHEN entry_type='refund' THEN -amount ELSE amount END),0)
             FROM revenues WHERE user_id=? AND sale_date<=?"
        );
        $s->execute([$userId, $asOfDate]);
        $totalRevenue = (float)$s->fetchColumn();

        // ── Capital injections up to date ─────────────────────────────────────
        $s = $db->prepare(
            "SELECT COALESCE(SUM(amount),0) FROM capitals WHERE user_id=? AND capital_date<=?"
        );
        $s->execute([$userId, $asOfDate]);
        $totalCapital = (float)$s->fetchColumn();

        // ── Expenses by category up to date ──────────────────────────────────
        $s = $db->prepare(
            "SELECT category, COALESCE(SUM(amount),0) AS total
             FROM expenses WHERE user_id=? AND expense_date<=?
             GROUP BY category"
        );
        $s->execute([$userId, $asOfDate]);
        $byCategory = [];
        foreach ($s->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $byCategory[$row['category']] = (float)$row['total'];
        }

        $cogs      = $byCategory['cogs']      ?? 0;
        $purchases = $byCategory['purchases'] ?? 0;
        $opex      = $byCategory['opex']      ?? 0;
        $marketing = $byCategory['marketing'] ?? 0;
        $ppe       = $byCategory['ppe']       ?? 0;
        $totalExp  = array_sum($byCategory);

        // ── P&L ──────────────────────────────────────────────────────────────
        // Purchases + COGS = Total cost of sales
        $costOfSales  = $cogs + $purchases;
        $grossProfit  = $totalRevenue - $costOfSales;
        $netProfit    = $grossProfit - $opex - $marketing;

        // ── Balance sheet derived values ──────────────────────────────────────
        // Cash = Capital + Revenue − All cash paid out
        $cash = max(0, $totalCapital + $totalRevenue - $totalExp);
        // Inventories: manual-only BS field — no auto-calculation from expenses
        // (Purchases expense ≠ closing inventory; users enter stock value manually)

        return [
            'auto_cash'          => $cash,
            'auto_ppe'           => $ppe,
            'auto_inventory'     => 0,              // manual entry on Balance Sheet
            'auto_share_capital' => $totalCapital,
            'auto_retained'      => $netProfit,     // positive = profit, negative = loss
            '_pnl' => [
                'revenue'      => $totalRevenue,
                'cogs'         => $cogs,
                'purchases'    => $purchases,
                'cost_of_sales'=> $costOfSales,
                'gross_profit' => $grossProfit,
                'opex'         => $opex,
                'marketing'    => $marketing,
                'net_profit'   => $netProfit,
            ],
        ];
    }

    /**
     * Get entries using the most recent saved date within a given month.
     */
    public function getByMonth(int $userId, int $year, int $month): array
    {
        $stmt = $this->db->prepare(
            'SELECT as_of_date FROM balance_sheet_entries
             WHERE user_id = ? AND YEAR(as_of_date) = ? AND MONTH(as_of_date) = ?
             ORDER BY as_of_date DESC LIMIT 1'
        );
        $stmt->execute([$userId, $year, $month]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return [];
        return $this->getByDate($userId, $row['as_of_date']);
    }

    /**
     * Get entries using the most recent saved date within a given year.
     */
    public function getByYear(int $userId, int $year): array
    {
        $stmt = $this->db->prepare(
            'SELECT as_of_date FROM balance_sheet_entries
             WHERE user_id = ? AND YEAR(as_of_date) = ?
             ORDER BY as_of_date DESC LIMIT 1'
        );
        $stmt->execute([$userId, $year]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return [];
        return ['_date' => $row['as_of_date']] + $this->getByDate($userId, $row['as_of_date']);
    }

    /**
     * List distinct dates that have entries for this user (for the date picker).
     */
    public function listDates(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT DISTINCT as_of_date FROM balance_sheet_entries
             WHERE user_id = ? ORDER BY as_of_date DESC LIMIT 24'
        );
        $stmt->execute([$userId]);
        return array_column($stmt->fetchAll(\PDO::FETCH_ASSOC), 'as_of_date');
    }
}
