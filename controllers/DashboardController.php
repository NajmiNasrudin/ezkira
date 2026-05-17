<?php

namespace Controllers;

use App\Core\Auth;
use App\Core\Controller;
use Models\Expense;
use Models\Revenue;

class DashboardController extends Controller
{
    public function index(): void
    {
        $user   = Auth::user();
        $userId = (int)($user['id'] ?? 0);

        $period = $_GET['period'] ?? 'monthly';
        $year   = (int)($_GET['year']  ?? date('Y'));
        $month  = (int)($_GET['month'] ?? date('n'));
        $week   = (int)($_GET['week']  ?? date('W'));
        $date   = $_GET['date'] ?? date('Y-m-d');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }

        if (!in_array($period, ['annual','monthly','weekly','daily'], true)) {
            $period = 'monthly';
        }

        $revenue = new Revenue();
        $expense = new Expense();

        $dailyDate = $period === 'daily' ? $date : '';

        // Determine expense filter parameters based on selected period
        $expYear  = $year;
        $expMonth = $period === 'monthly' ? $month : 0;
        $expWeek  = $period === 'weekly'  ? $week  : 0;

        $revTotal  = $revenue->totalByPeriod($period, $year, $month, $userId, $week, $dailyDate);
        $opex      = $expense->totalByCategory('opex',      $userId, $expYear, $expMonth, $dailyDate, $expWeek);
        $marketing = $expense->totalByCategory('marketing', $userId, $expYear, $expMonth, $dailyDate, $expWeek);
        $cogs      = $expense->totalByCategory('cogs',      $userId, $expYear, $expMonth, $dailyDate, $expWeek);
        $totalExp  = $opex + $marketing + $cogs;
        $profit    = $revTotal - $totalExp;

        $targetRevenue = $revenue->getTarget((int)date('Y'), (int)date('n'), $userId);

        // Comparison data
        $curYear       = (int)date('Y');
        $curMonth      = (int)date('n');
        $prevMonthNum  = $curMonth === 1 ? 12 : $curMonth - 1;
        $prevMonthYear = $curMonth === 1 ? $curYear - 1 : $curYear;

        $compareMonth = [
            'cur_rev'  => $revenue->totalByPeriod('monthly', $curYear,       $curMonth,      $userId),
            'prev_rev' => $revenue->totalByPeriod('monthly', $prevMonthYear, $prevMonthNum,  $userId),
            'cur_exp'  => $expense->totalByCategory('opex',      $userId, $curYear,      $curMonth)
                        + $expense->totalByCategory('marketing', $userId, $curYear,      $curMonth)
                        + $expense->totalByCategory('cogs',      $userId, $curYear,      $curMonth),
            'prev_exp' => $expense->totalByCategory('opex',      $userId, $prevMonthYear, $prevMonthNum)
                        + $expense->totalByCategory('marketing', $userId, $prevMonthYear, $prevMonthNum)
                        + $expense->totalByCategory('cogs',      $userId, $prevMonthYear, $prevMonthNum),
            'cur_label'  => date('M Y', mktime(0,0,0,$curMonth,1,$curYear)),
            'prev_label' => date('M Y', mktime(0,0,0,$prevMonthNum,1,$prevMonthYear)),
        ];

        $revThisYear = $revenue->monthlyTotals($curYear,     $userId);
        $revLastYear = $revenue->monthlyTotals($curYear - 1, $userId);
        $expThisYear = $expense->monthlyTotals($curYear,     $userId);
        $expLastYear = $expense->monthlyTotals($curYear - 1, $userId);

        $compareYear = [
            'rev_this'  => array_values($revThisYear),
            'rev_last'  => array_values($revLastYear),
            'exp_this'  => array_values($expThisYear),
            'exp_last'  => array_values($expLastYear),
            'this_year' => $curYear,
            'last_year' => $curYear - 1,
        ];

        $recentRev    = $revenue->recentTransactions($userId, 15);
        $recentExp    = $expense->recentTransactions($userId, 15);
        $transactions = array_merge($recentRev, $recentExp);
        usort($transactions, fn($a, $b) => strcmp($b['txn_date'], $a['txn_date']));
        $transactions = array_slice($transactions, 0, 15);

        $summary = [
            'total_revenue'  => $revTotal,
            'total_expenses' => $totalExp,
            'net_profit'     => $profit,
            'transactions'   => $revenue->countAll($userId),
        ];

        $this->view('dashboard/index', [
            'user'          => $user,
            'summary'       => $summary,
            'transactions'  => $transactions,
            'compareMonth'  => $compareMonth,
            'compareYear'   => $compareYear,
            'period'        => $period,
            'year'          => $year,
            'month'         => $month,
            'week'          => $week,
            'date'          => $date,
            'targetRevenue' => $targetRevenue,
            'chartData'     => [
                'opex'       => $opex,
                'marketing'  => $marketing,
                'cogs'       => $cogs,
                'total'      => $totalExp,
                'revenue'    => $revTotal,
                'profit'     => $profit,
                'overBudget' => $totalExp > $revTotal && $revTotal > 0,
            ],
        ], 'main', __('dashboard'));
    }
}
