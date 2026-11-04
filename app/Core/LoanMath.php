<?php

namespace App\Core;

/**
 * Loan arithmetic for Expenses → Pinjaman. Pure functions, no database.
 *
 * Two interest types, as Malaysian lenders quote them:
 *   'flat'     — interest = principal × rate × years, spread evenly (TEKUN, personal loans, hire purchase)
 *   'reducing' — interest each month on the balance still owed (bank term loans)
 * Rates are % per year. Amounts are rounded to sen.
 */
class LoanMath
{
    public const TYPES = ['reducing', 'flat'];

    /** Monthly instalment the lender would normally charge. */
    public static function installment(float $principal, float $rate, int $months, string $type): float
    {
        if ($months <= 0 || $principal <= 0) {
            return 0.0;
        }
        if ($type === 'flat') {
            return round($principal / $months + $principal * $rate / 1200, 2);
        }
        $r = $rate / 1200;
        if ($r <= 0) {
            return round($principal / $months, 2);
        }
        return round($principal * $r / (1 - (1 + $r) ** -$months), 2);
    }

    /** Interest part of a payment made while $balance is still owed. */
    public static function interestFor(float $balance, float $principal, float $rate, string $type): float
    {
        if ($balance <= 0) {
            return 0.0;
        }
        return round($type === 'flat' ? $principal * $rate / 1200 : $balance * $rate / 1200, 2);
    }

    /**
     * Split a payment into [principal, interest]. Interest is paid first; the principal part
     * never goes past what is still owed, so the last payment can't push the balance negative.
     */
    public static function split(float $amount, float $balance, float $principal, float $rate, string $type): array
    {
        $interest = min($amount, self::interestFor($balance, $principal, $rate, $type));
        $toPrincipal = round(min($balance, max(0, $amount - $interest)), 2);
        // Rounding leaves a few sen at the end of the schedule; lenders fold that into the last instalment
        if ($toPrincipal > 0 && $balance - $toPrincipal < 1.0) {
            $toPrincipal = round($balance, 2);
        }
        return [$toPrincipal, round($interest, 2)];
    }

    /**
     * Run $count scheduled instalments from $balance. Returns [principal repaid, interest paid].
     * Used for instalments paid before the loan was added to ezkira, and for the 12-month
     * current-liability portion on the Balance Sheet.
     */
    public static function project(float $balance, float $installment, int $count, float $principal, float $rate, string $type): array
    {
        $paidPrincipal = 0.0;
        $paidInterest  = 0.0;
        for ($i = 0; $i < $count && $balance > 0.004; $i++) {
            [$p, $int] = self::split($installment, $balance, $principal, $rate, $type);
            if ($p <= 0) {
                break; // instalment doesn't even cover interest — stop instead of looping forever
            }
            $balance       -= $p;
            $paidPrincipal += $p;
            $paidInterest  += $int;
        }
        return [round($paidPrincipal, 2), round($paidInterest, 2)];
    }

    /** How many more scheduled instalments until the balance is cleared (null = never at this instalment). */
    public static function monthsLeft(float $balance, float $installment, float $principal, float $rate, string $type): ?int
    {
        $n = 0;
        while ($balance > 0.004) {
            [$p] = self::split($installment, $balance, $principal, $rate, $type);
            if ($p <= 0 || ++$n > 1200) {
                return null;
            }
            $balance -= $p;
        }
        return $n;
    }
}
