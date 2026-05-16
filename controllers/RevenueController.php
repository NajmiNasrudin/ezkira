<?php

namespace Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\CSRF;
use App\Core\Session;
use Models\Revenue;

class RevenueController extends Controller
{
    public function index(): void
    {
        $model  = new Revenue();
        $year   = (int)($_GET['year']  ?? date('Y'));
        $month  = (int)($_GET['month'] ?? date('n'));

        $month  = max(1, min(12, $month));
        $year   = max(2020, min((int)date('Y') + 1, $year));

        $target    = $model->getTarget($year, $month);
        $entries   = $model->byPeriod('monthly', $year, $month);
        $total     = $model->totalByPeriod('monthly', $year, $month);
        $platforms = $model->platformBreakdown('monthly', $year, $month);
        $daily     = $model->dailyTotals($year, $month);

        $pct = $target > 0 ? min(100, ($total / $target) * 100) : 0;

        $this->view('revenue/index', [
            'year'      => $year,
            'month'     => $month,
            'target'    => $target,
            'total'     => $total,
            'pct'       => $pct,
            'entries'   => $entries,
            'platforms' => $platforms,
            'daily'     => $daily,
            'platforms_list' => Revenue::PLATFORMS,
        ], 'main', __('revenue'));
    }

    public function store(): void
    {
        CSRF::check();

        $platform    = $_POST['platform']    ?? 'other';
        $amount      = trim($_POST['amount'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $date        = $_POST['sale_date']   ?? date('Y-m-d');

        if (!array_key_exists($platform, Revenue::PLATFORMS)) {
            $platform = 'other';
        }

        if (!is_numeric($amount) || (float)$amount <= 0) {
            Session::flash('error', __('revenue_amount_invalid'));
            $this->redirect('/revenue');
        }

        (new Revenue())->create([
            'user_id'     => Auth::id(),
            'platform'    => $platform,
            'amount'      => (float)$amount,
            'description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
            'sale_date'   => $date,
        ]);

        $y = date('Y', strtotime($date));
        $m = date('n', strtotime($date));

        Session::flash('success', __('revenue_added'));
        $this->redirect("/revenue?year={$y}&month={$m}");
    }

    public function delete(string $id): void
    {
        CSRF::check();

        $entry = (new Revenue())->findById((int)$id);
        if (!$entry) {
            Session::flash('error', 'Rekod tidak dijumpai.');
            $this->redirect('/revenue');
        }

        (new Revenue())->delete((int)$id);
        Session::flash('success', __('revenue_deleted'));
        $this->redirect('/revenue');
    }

    public function setTarget(): void
    {
        CSRF::check();

        $year   = (int)($_POST['year']   ?? date('Y'));
        $month  = (int)($_POST['month']  ?? date('n'));
        $amount = trim($_POST['target_amount'] ?? '');

        if (!is_numeric($amount) || (float)$amount < 0) {
            Session::flash('error', __('revenue_target_invalid'));
            $this->redirect('/revenue');
        }

        (new Revenue())->setTarget($year, $month, (float)$amount);
        Session::flash('success', __('revenue_target_saved'));
        $this->redirect("/revenue?year={$year}&month={$month}");
    }

    public function exportPnl(): void
    {
        $period = $_GET['period'] ?? 'monthly';
        $year   = (int)($_GET['year']  ?? date('Y'));
        $month  = (int)($_GET['month'] ?? date('n'));
        $week   = (int)($_GET['week']  ?? date('W'));

        $user        = Auth::user();
        $companyName = $user['name']     ?? 'My Business';
        $picName     = $user['pic_name'] ?? '';

        $revenue = new Revenue();
        $revTotal = $revenue->totalByPeriod($period, $year, $month, $week);
        $revByPlatform = $revenue->platformBreakdown($period, $year, $month, $week);

        $expense  = new \Models\Expense();
        $opex     = $expense->totalByCategory('opex');
        $marketing = $expense->totalByCategory('marketing');
        $cogs     = $expense->totalByCategory('cogs');
        $totalExp = $opex + $marketing + $cogs;
        $netProfit = $revTotal - $totalExp;

        $periodLabel = match($period) {
            'annual'  => "Year {$year}",
            'weekly'  => "Week {$week}, {$year}",
            'daily'   => date('d M Y'),
            default   => date('F Y', mktime(0,0,0,$month,1,$year)),
        };

        $filename = 'PnL_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $companyName) . '_' . str_replace([' ', ','], ['_', ''], $periodLabel) . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store');

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

        fputcsv($out, [$companyName . ' — Profit & Loss Statement']);
        fputcsv($out, ['Period:', $periodLabel]);
        if ($picName !== '') {
            fputcsv($out, ['Prepared by:', $picName]);
        }
        fputcsv($out, ['Generated:', date('d M Y, H:i')]);
        fputcsv($out, ['Powered by:', APP_NAME]);
        fputcsv($out, []);

        fputcsv($out, ['REVENUE']);
        fputcsv($out, ['Platform', 'Amount (RM)']);
        foreach ($revByPlatform as $row) {
            $label = Revenue::PLATFORMS[$row['platform']] ?? ucfirst($row['platform']);
            fputcsv($out, [$label, number_format((float)$row['total'], 2)]);
        }
        fputcsv($out, ['TOTAL REVENUE', number_format($revTotal, 2)]);
        fputcsv($out, []);

        fputcsv($out, ['EXPENSES']);
        fputcsv($out, ['Category', 'Amount (RM)']);
        fputcsv($out, ['OPEX',               number_format($opex, 2)]);
        fputcsv($out, ['Marketing Expenses', number_format($marketing, 2)]);
        fputcsv($out, ['COGS',               number_format($cogs, 2)]);
        fputcsv($out, ['TOTAL EXPENSES',     number_format($totalExp, 2)]);
        fputcsv($out, []);

        fputcsv($out, ['NET PROFIT', number_format($netProfit, 2)]);
        fputcsv($out, ['PROFIT MARGIN', $revTotal > 0 ? number_format(($netProfit / $revTotal) * 100, 2) . '%' : 'N/A']);

        fclose($out);
        exit;
    }
}
