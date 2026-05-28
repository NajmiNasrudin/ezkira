<?php

namespace Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\CSRF;
use App\Core\Logger;
use App\Core\Session;
use Controllers\ExpenseController;
use Models\BalanceSheet;
use Models\Capital;
use Models\Revenue;

class BalanceSheetController extends Controller
{
    public function index(): void
    {
        $userId = Auth::id();
        $model  = new BalanceSheet();

        $date = $_GET['date'] ?? date('Y-m-d');
        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }

        $entries    = $model->getByDate($userId, $date);
        $auto       = $model->autoCalculate($userId, $date);
        $savedDates = $model->listDates($userId);

        $this->view('balance-sheet/index', [
            'date'       => $date,
            'entries'    => $entries,
            'auto'       => $auto,
            'savedDates' => $savedDates,
            'sections'   => BalanceSheet::SECTIONS,
            'platforms'  => Revenue::PLATFORMS,
            'catMeta'    => ExpenseController::CATEGORY_META,
        ], 'main', __('balance_sheet'));
    }

    // -------------------------------------------------------------------------
    // Save entries
    // -------------------------------------------------------------------------

    public function save(): void
    {
        CSRF::check();

        $userId = Auth::id();
        $date   = $_POST['as_of_date'] ?? date('Y-m-d');

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }

        // Collect submitted values
        $data = [];
        foreach (BalanceSheet::SECTIONS as $section => $def) {
            $data[$section] = [];
            foreach ($def['items'] as $key => $label) {
                $raw = $_POST['bs'][$section][$key] ?? '0';
                $data[$section][$key] = is_numeric($raw) ? (float)$raw : 0.0;
            }
        }

        (new BalanceSheet())->saveAll($userId, $date, $data);

        Logger::log('balance_sheet_save', $userId, "Balance sheet saved for {$date}");
        Session::flash('success', __('balance_sheet_saved'));
        $this->redirect('/balance-sheet?date=' . urlencode($date));
    }

    // -------------------------------------------------------------------------
    // Export CSV
    // -------------------------------------------------------------------------

    public function export(): void
    {
        $userId  = Auth::id();
        $period  = $_GET['period'] ?? 'date';   // 'date' | 'monthly' | 'annual'
        $year    = (int)($_GET['year']  ?? date('Y'));
        $month   = (int)($_GET['month'] ?? date('n'));
        $date    = $_GET['date'] ?? date('Y-m-d');

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }

        $model = new BalanceSheet();

        // Resolve entries and display label based on period
        if ($period === 'annual') {
            $result      = $model->getByYear($userId, $year);
            $resolvedDate = $result['_date'] ?? null;
            unset($result['_date']);
            $entries      = $result;
            $periodLabel  = "Annual — Year {$year}";
            $asAtLabel    = $resolvedDate
                ? date('d M Y', strtotime($resolvedDate))
                : "Year {$year}";
        } elseif ($period === 'monthly') {
            $entries     = $model->getByMonth($userId, $year, $month);
            $periodLabel = date('F Y', mktime(0, 0, 0, $month, 1, $year));
            $asAtLabel   = $periodLabel;
        } else {
            $entries     = $model->getByDate($userId, $date);
            $periodLabel = date('d M Y', strtotime($date));
            $asAtLabel   = $periodLabel;
        }

        // Merge auto-calculated values into entries (auto fills gaps, manual overrides)
        $auto = $model->autoCalculate($userId, $date);
        $entries['current_asset']['cash']          ??= 0;
        $entries['non_current_asset']['ppe']        ??= 0;
        $entries['current_asset']['inventories']    ??= 0;
        $entries['equity']['share_capital']         ??= 0;
        $entries['equity']['accum_losses']          ??= 0;

        // Use auto values for items without manual override
        if (($entries['current_asset']['cash']       ?? 0) == 0) $entries['current_asset']['cash']       = $auto['auto_cash'];
        if (($entries['non_current_asset']['ppe']    ?? 0) == 0) $entries['non_current_asset']['ppe']    = $auto['auto_ppe'];
        if (($entries['current_asset']['inventories']?? 0) == 0) $entries['current_asset']['inventories']= $auto['auto_inventory'];
        if (($entries['equity']['share_capital']     ?? 0) == 0) $entries['equity']['share_capital']     = $auto['auto_share_capital'];
        // Retained earnings: positive = profit (goes under share_capital area), negative = accumulated losses
        if (($entries['equity']['accum_losses']      ?? 0) == 0 && $auto['auto_retained'] < 0) {
            $entries['equity']['accum_losses'] = abs($auto['auto_retained']);
        }

        $user    = Auth::user();
        $company = $user['name']     ?? 'My Business';
        $pic     = $user['pic_name'] ?? '';

        $filename = 'BalanceSheet_'
            . preg_replace('/[^A-Za-z0-9_\-]/', '_', $company)
            . '_' . str_replace([' ', '—', ','], ['_', '', ''], $periodLabel)
            . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store');

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

        // Header block
        fputcsv($out, [$company]);
        fputcsv($out, ['Statement of Financial Position (Balance Sheet)']);
        fputcsv($out, ['As at: ' . $asAtLabel]);
        if (!empty($entries) && $period === 'annual') {
            fputcsv($out, ['Period: ' . $periodLabel]);
        }
        if ($pic !== '') fputcsv($out, ['Prepared by: ' . $pic]);
        fputcsv($out, ['Generated: ' . date('d M Y, H:i')]);
        fputcsv($out, ['Powered by: ' . APP_NAME]);
        fputcsv($out, []);
        fputcsv($out, ['', 'RM']);
        fputcsv($out, []);

        $totalAssets      = 0.0;
        $totalEquity      = 0.0;
        $totalLiabilities = 0.0;

        foreach (BalanceSheet::SECTIONS as $sectionKey => $def) {
            $sectionTotal = 0.0;

            // Section header (bold in accounting = ALL CAPS)
            fputcsv($out, [strtoupper($def['label']), '']);

            foreach ($def['items'] as $itemKey => $itemLabel) {
                $amount = (float)($entries[$sectionKey][$itemKey] ?? 0);
                // Accumulated losses: show as negative if entered positive
                if ($itemKey === 'accum_losses' && $amount > 0) {
                    $amount = -$amount;
                }
                $sectionTotal += $amount;
                fputcsv($out, ['    ' . $itemLabel, number_format($amount, 2)]);
            }

            fputcsv($out, []);
            fputcsv($out, [$def['total_label'], number_format($sectionTotal, 2)]);
            fputcsv($out, []);

            // Accumulate totals
            if (in_array($sectionKey, ['non_current_asset', 'current_asset'])) {
                $totalAssets += $sectionTotal;
            } elseif ($sectionKey === 'equity') {
                $totalEquity += $sectionTotal;
            } else {
                $totalLiabilities += $sectionTotal;
            }
        }

        // Summary totals
        fputcsv($out, ['TOTAL ASSETS', number_format($totalAssets, 2)]);
        fputcsv($out, []);
        fputcsv($out, ['TOTAL EQUITY', number_format($totalEquity, 2)]);
        fputcsv($out, ['TOTAL LIABILITIES', number_format($totalLiabilities, 2)]);
        fputcsv($out, []);
        fputcsv($out, ['TOTAL EQUITY AND LIABILITIES', number_format($totalEquity + $totalLiabilities, 2)]);

        fclose($out);
        exit;
    }
}
