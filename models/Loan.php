<?php

namespace Models;

use App\Core\LoanMath;

/**
 * Loans a user is paying off (Expenses → Pinjaman).
 *
 * A loan row holds the terms; each instalment paid in ezkira is a loan_payments row that
 * points at the expenses it created: the principal part as a 'liability' expense (Balance
 * Sheet only) and the interest part as an 'opex' expense (counts in P&L).
 * Instalments paid before the loan was added (paid_before) only exist as a count.
 */
class Loan
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /** All of a user's loans, each with its live state (see state()). Unfinished loans first. */
    public function forUser(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM loans WHERE user_id = ? ORDER BY loan_date DESC, id DESC');
        $stmt->execute([$userId]);
        $loans = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (!$loans) {
            return [];
        }

        $totals = $this->paymentTotals($userId);
        foreach ($loans as &$loan) {
            $loan += $this->state($loan, $totals[(int) $loan['id']] ?? null);
        }
        unset($loan);

        usort($loans, fn($a, $b) => ($a['outstanding'] <= 0) <=> ($b['outstanding'] <= 0));
        return $loans;
    }

    public function find(int $id, int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM loans WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        $loan = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$loan) {
            return null;
        }
        return $loan + $this->state($loan, $this->paymentTotals($userId, $id)[$id] ?? null);
    }

    /** Sum of recorded payments per loan, optionally only those paid on or before $asOf. */
    private function paymentTotals(int $userId, ?int $loanId = null, ?string $asOf = null): array
    {
        $sql    = 'SELECT loan_id, COUNT(*) AS n, SUM(principal) AS principal, SUM(interest) AS interest
                     FROM loan_payments WHERE user_id = ?';
        $params = [$userId];
        if ($loanId !== null) {
            $sql .= ' AND loan_id = ?';
            $params[] = $loanId;
        }
        if ($asOf !== null) {
            $sql .= ' AND pay_date <= ?';
            $params[] = $asOf;
        }
        $stmt = $this->db->prepare($sql . ' GROUP BY loan_id');
        $stmt->execute($params);

        $out = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $r) {
            $out[(int) $r['loan_id']] = ['n' => (int) $r['n'], 'principal' => (float) $r['principal'], 'interest' => (float) $r['interest']];
        }
        return $out;
    }

    /**
     * Where a loan stands: balance owed, instalments paid, interest paid so far, next due date
     * and how many instalments are left at the current instalment amount.
     */
    private function state(array $loan, ?array $paid): array
    {
        $principal   = (float) $loan['principal'];
        $rate        = (float) $loan['rate'];
        $type        = $loan['rate_type'];
        $installment = (float) $loan['installment'];

        [$beforeP, $beforeI] = LoanMath::project($principal, $installment, (int) $loan['paid_before'], $principal, $rate, $type);

        $outstanding = round(max(0, $principal - $beforeP - ($paid['principal'] ?? 0)), 2);
        $paidCount   = (int) $loan['paid_before'] + ($paid['n'] ?? 0);
        $nextDue     = $outstanding > 0
            ? (new \DateTimeImmutable($loan['first_due_date']))->modify("+{$paidCount} month")->format('Y-m-d')
            : null;

        return [
            'outstanding'   => $outstanding,
            'paid_count'    => $paidCount,
            'interest_paid' => round($beforeI + ($paid['interest'] ?? 0), 2),
            'next_due'      => $nextDue,
            'months_left'   => LoanMath::monthsLeft($outstanding, $installment, $principal, $rate, $type),
            'progress'      => $principal > 0 ? min(100, round(($principal - $outstanding) / $principal * 100)) : 100,
        ];
    }

    public function create(int $userId, array $d): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO loans (user_id, lender, principal, rate, rate_type, months, installment, loan_date, first_due_date, paid_before, add_to_cash)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $userId, $d['lender'], $d['principal'], $d['rate'], $d['rate_type'], $d['months'],
            $d['installment'], $d['loan_date'], $d['first_due_date'], $d['paid_before'], $d['add_to_cash'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, int $userId, array $d): void
    {
        $stmt = $this->db->prepare(
            'UPDATE loans SET lender = ?, principal = ?, rate = ?, rate_type = ?, months = ?, installment = ?,
                    loan_date = ?, first_due_date = ?, paid_before = ?, add_to_cash = ?
              WHERE id = ? AND user_id = ?'
        );
        $stmt->execute([
            $d['lender'], $d['principal'], $d['rate'], $d['rate_type'], $d['months'], $d['installment'],
            $d['loan_date'], $d['first_due_date'], $d['paid_before'], $d['add_to_cash'], $id, $userId,
        ]);
    }

    /** Deletes the loan and its payment rows; returns the expense ids those payments created. */
    public function delete(int $id, int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT principal_expense_id, interest_expense_id FROM loan_payments WHERE loan_id = ? AND user_id = ?'
        );
        $stmt->execute([$id, $userId]);
        $expenseIds = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_NUM) as $r) {
            array_push($expenseIds, ...array_filter(array_map('intval', $r)));
        }

        $this->db->prepare('DELETE FROM loan_payments WHERE loan_id = ? AND user_id = ?')->execute([$id, $userId]);
        $this->db->prepare('DELETE FROM loans WHERE id = ? AND user_id = ?')->execute([$id, $userId]);
        return $expenseIds;
    }

    public function payments(int $loanId, int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM loan_payments WHERE loan_id = ? AND user_id = ? ORDER BY pay_date DESC, id DESC'
        );
        $stmt->execute([$loanId, $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function recordPayment(array $loan, string $date, float $amount, float $principal, float $interest, ?int $principalExpenseId, ?int $interestExpenseId): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO loan_payments (loan_id, user_id, pay_date, amount, principal, interest, principal_expense_id, interest_expense_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$loan['id'], $loan['user_id'], $date, $amount, $principal, $interest, $principalExpenseId, $interestExpenseId]);
    }

    /** The payment an expense belongs to, if it was created by "Bayar ansuran". */
    public function paymentForExpense(int $expenseId, int $userId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, l.lender FROM loan_payments p JOIN loans l ON l.id = p.loan_id
              WHERE p.user_id = ? AND (p.principal_expense_id = ? OR p.interest_expense_id = ?) LIMIT 1'
        );
        $stmt->execute([$userId, $expenseId, $expenseId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function deletePayment(int $paymentId, int $userId): void
    {
        $this->db->prepare('DELETE FROM loan_payments WHERE id = ? AND user_id = ?')->execute([$paymentId, $userId]);
    }

    /** expense id => lender name, for marking loan rows in the expense list. */
    public function lendersByExpense(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.principal_expense_id, p.interest_expense_id, l.lender
               FROM loan_payments p JOIN loans l ON l.id = p.loan_id WHERE p.user_id = ?'
        );
        $stmt->execute([$userId]);
        $out = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $r) {
            foreach (['principal_expense_id', 'interest_expense_id'] as $k) {
                if ($r[$k]) {
                    $out[(int) $r[$k]] = $r['lender'];
                }
            }
        }
        return $out;
    }

    /**
     * Balance Sheet figures on $asOf: loan balance split into what falls due in the next
     * 12 instalments (current) and the rest (non-current), plus loan money that came into
     * the business (counted as cash).
     */
    public function balanceSheet(int $userId, string $asOf): array
    {
        $stmt = $this->db->prepare('SELECT * FROM loans WHERE user_id = ? AND loan_date <= ?');
        $stmt->execute([$userId, $asOf]);
        $loans = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $out = ['current' => 0.0, 'non_current' => 0.0, 'cash_in' => 0.0];
        if (!$loans) {
            return $out;
        }

        $totals = $this->paymentTotals($userId, null, $asOf);
        foreach ($loans as $loan) {
            $state = $this->state($loan, $totals[(int) $loan['id']] ?? null);
            [$next12] = LoanMath::project(
                $state['outstanding'], (float) $loan['installment'], 12,
                (float) $loan['principal'], (float) $loan['rate'], $loan['rate_type']
            );
            $out['current']     += $next12;
            $out['non_current'] += $state['outstanding'] - $next12;
            if ($loan['add_to_cash']) {
                $out['cash_in'] += (float) $loan['principal'];
            }
        }
        return array_map(fn($v) => round($v, 2), $out);
    }
}
