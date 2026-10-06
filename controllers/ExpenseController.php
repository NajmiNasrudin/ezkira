<?php

namespace Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\CSRF;
use App\Core\Plan;
use App\Core\ReceiptImage;
use App\Core\Session;
use Models\Expense;
use Models\Revenue;
use Models\Setting;

class ExpenseController extends Controller
{
    private const CATEGORIES = ['opex', 'marketing', 'cogs', 'purchases', 'ppe', 'liability'];

    // Category metadata: label, group, BS section mapping
    public const CATEGORY_META = [
        // ── P&L (Income Statement) ──────────────────────────────
        'cogs'      => ['label' => 'Cost of Goods Sold (COGS)',     'group' => 'pnl',         'bs_section' => null],
        'purchases' => ['label' => 'Purchases',                     'group' => 'pnl',         'bs_section' => null],
        'opex'      => ['label' => 'Operating Expenses (OPEX)',     'group' => 'pnl',         'bs_section' => null],
        'marketing' => ['label' => 'Marketing & Advertising',       'group' => 'pnl',         'bs_section' => null],
        // ── Balance Sheet — Assets ──────────────────────────────
        'ppe'       => ['label' => 'Property, Plant & Equipment',   'group' => 'asset',       'bs_section' => 'non_current_asset'],
        // ── Balance Sheet — Liabilities ─────────────────────────
        'liability' => ['label' => 'Liability / Loan Repayment',    'group' => 'bs_liability','bs_section' => null],
    ];

    public const SUGGESTED_PCT = ['opex' => 20, 'marketing' => 10, 'cogs' => 40];

    public function index(): void
    {
        $year  = (int)($_GET['year']  ?? date('Y'));
        $month = (int)($_GET['month'] ?? date('n'));
        $month = max(1, min(12, $month));
        $year  = max(2020, min((int)date('Y') + 1, $year));

        $model         = new Expense();
        $setting       = new Setting();
        $targetRevenue = (new Revenue())->getTarget($year, $month, Auth::id());

        $pcts = [
            'opex'      => (float)($setting->get('expense_pct_opex')      ?? self::SUGGESTED_PCT['opex']),
            'marketing' => (float)($setting->get('expense_pct_marketing') ?? self::SUGGESTED_PCT['marketing']),
            'cogs'      => (float)($setting->get('expense_pct_cogs')      ?? self::SUGGESTED_PCT['cogs']),
        ];

        $userId = Auth::id();

        // Build unified list with receipts attached
        $allRaw      = $model->allByMonth($userId, $year, $month);
        $allExpenses = [];
        foreach ($allRaw as $e) {
            $e['receipts']  = $model->getReceipts((int)$e['id']);
            $allExpenses[]  = $e;
        }

        $data = [
            'plan'          => Plan::status($userId),
            'year'          => $year,
            'month'         => $month,
            'targetRevenue' => $targetRevenue,
            'pcts'          => $pcts,
            'allExpenses'   => $allExpenses,
            'totals' => [
                'opex'      => $model->totalByCategory('opex',      $userId, $year, $month),
                'marketing' => $model->totalByCategory('marketing', $userId, $year, $month),
                'cogs'      => $model->totalByCategory('cogs',      $userId, $year, $month),
                'purchases' => $model->totalByCategory('purchases', $userId, $year, $month),
                'ppe'       => $model->totalByCategory('ppe',       $userId, $year, $month),
                'liability' => $model->totalByCategory('liability', $userId, $year, $month),
            ],
        ];

        $this->view('expenses/index', $data, 'main', __('expenses'));
    }

    public function store(): void
    {
        CSRF::check();

        $category    = $_POST['category']     ?? '';
        $amount      = trim($_POST['amount']  ?? '');
        $description = trim($_POST['description'] ?? '');
        $date        = $_POST['expense_date'] ?? date('Y-m-d');
        $year        = $_POST['year']  ?? date('Y', strtotime($date));
        $month       = $_POST['month'] ?? date('n', strtotime($date));
        $qs          = "?year={$year}&month={$month}";

        if (!in_array($category, self::CATEGORIES, true)) {
            Session::flash('error', 'Kategori tidak sah.');
            $this->redirect('/expenses' . $qs);
        }

        if (!is_numeric($amount) || (float)$amount <= 0) {
            Session::flash('error', 'Jumlah mesti nombor positif.');
            $this->redirect('/expenses' . $qs . '#' . $category);
        }

        if ($description === '') {
            Session::flash('error', 'Keterangan diperlukan.');
            $this->redirect('/expenses' . $qs . '#' . $category);
        }

        $expenseModel = new Expense();
        $expenseId = $expenseModel->create([
            'user_id'      => Auth::id(),
            'category'     => $category,
            'amount'       => (float)$amount,
            'description'  => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
            'expense_date' => $date,
            'receipt_path' => null,
            'receipt_name' => null,
        ]);

        $this->storeUploadedReceipts($expenseModel, $expenseId);

        $y = date('Y', strtotime($date));
        $m = date('n', strtotime($date));
        Session::flash('success', 'Rekod perbelanjaan berjaya ditambah.');
        $this->redirect("/expenses?year={$y}&month={$m}#" . $category);
    }

    public function update(string $id): void
    {
        CSRF::check();

        $expense = (new Expense())->findById((int)$id);
        if (!$expense || (int)$expense['user_id'] !== Auth::id()) {
            Session::flash('error', 'Rekod tidak dijumpai.');
            $this->redirect('/expenses');
        }

        $category    = $_POST['category']     ?? $expense['category'];
        $amount      = trim($_POST['amount']  ?? '');
        $description = trim($_POST['description'] ?? '');
        $date        = $_POST['expense_date'] ?? $expense['expense_date'];
        $year        = $_POST['year']  ?? date('Y');
        $month       = $_POST['month'] ?? date('n');

        if (!in_array($category, self::CATEGORIES, true)) {
            Session::flash('error', 'Kategori tidak sah.');
            $this->redirect("/expenses?year={$year}&month={$month}");
        }

        if (!is_numeric($amount) || (float)$amount <= 0) {
            Session::flash('error', 'Jumlah mesti nombor positif.');
            $this->redirect("/expenses?year={$year}&month={$month}");
        }

        if ($description === '') {
            Session::flash('error', 'Keterangan diperlukan.');
            $this->redirect("/expenses?year={$year}&month={$month}");
        }

        $expenseModel = new Expense();
        $expenseModel->update((int)$id, [
            'category'     => $category,
            'amount'       => (float)$amount,
            'description'  => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
            'expense_date' => $date,
            'user_id'      => Auth::id(),
        ]);

        $this->storeUploadedReceipts($expenseModel, (int)$id);

        Session::flash('success', 'Rekod berjaya dikemaskini.');
        $this->redirect("/expenses?year={$year}&month={$month}#" . $category);
    }

    public function delete(string $id): void
    {
        CSRF::check();

        $expense = (new Expense())->findById((int)$id);
        if (!$expense || (int)$expense['user_id'] !== Auth::id()) {
            Session::flash('error', 'Rekod tidak dijumpai.');
            $this->redirect('/expenses');
        }

        $expenseModel = new Expense();

        // Delete old single receipt
        if (!empty($expense['receipt_path'])) {
            $full = BASE_PATH . '/' . $expense['receipt_path'];
            if (file_exists($full)) unlink($full);
        }

        // Delete new multiple receipts
        $receipts = $expenseModel->deleteReceipts((int)$id);
        foreach ($receipts as $r) {
            $full = BASE_PATH . '/' . $r['path'];
            if (file_exists($full)) unlink($full);
        }

        $expenseModel->delete((int)$id);
        Session::flash('success', 'Rekod berjaya dipadam.');
        $this->redirect('/expenses');
    }

    public function receipt(string $id): void
    {
        $expense = (new Expense())->findById((int)$id);
        if (!$expense || empty($expense['receipt_path']) || (int)$expense['user_id'] !== Auth::id()) {
            http_response_code(404);
            exit('Fail tidak dijumpai.');
        }

        $full = BASE_PATH . '/' . $expense['receipt_path'];
        if (!file_exists($full)) {
            http_response_code(404);
            exit('Fail tidak dijumpai.');
        }

        $mime = mime_content_type($full) ?: 'application/octet-stream';
        $name = $expense['receipt_name'] ?? basename($full);

        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . rawurlencode($name) . '"');
        header('Content-Length: ' . filesize($full));
        readfile($full);
        exit;
    }

    // Serve a file from expense_receipts table
    public function receiptFile(string $id): void
    {
        $expenseModel = new Expense();
        $receipt      = $expenseModel->findReceiptById((int)$id);
        if (!$receipt) {
            http_response_code(404);
            exit('Fail tidak dijumpai.');
        }
        $parentExpense = $expenseModel->findById((int)$receipt['expense_id']);
        if (!$parentExpense || (int)$parentExpense['user_id'] !== Auth::id()) {
            http_response_code(403);
            exit('Forbidden.');
        }

        $full = BASE_PATH . '/' . $receipt['path'];
        if (!file_exists($full)) {
            http_response_code(404);
            exit('Fail tidak dijumpai.');
        }

        $mime = mime_content_type($full) ?: 'application/octet-stream';
        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . rawurlencode($receipt['name']) . '"');
        header('Content-Length: ' . filesize($full));
        readfile($full);
        exit;
    }

    // Delete a single receipt from expense_receipts table
    public function deleteReceipt(string $id): void
    {
        CSRF::check();

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                  && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        $model   = new Expense();
        $receipt = $model->findReceiptById((int)$id);
        $deleted = false;

        if ($receipt) {
            $expense = $model->findById((int)$receipt['expense_id']);
            if ($expense && (int)$expense['user_id'] === Auth::id()) {
                $full = BASE_PATH . '/' . $receipt['path'];
                if (file_exists($full)) unlink($full);
                $model->deleteReceiptById((int)$id);
                $deleted = true;
            }
        }

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => $deleted]);
            exit;
        }

        $year  = $_POST['year']  ?? date('Y');
        $month = $_POST['month'] ?? date('n');
        $cat   = $_POST['category'] ?? '';
        Session::flash('success', 'Fail berjaya dipadam.');
        $this->redirect("/expenses?year={$year}&month={$month}" . ($cat ? '#' . $cat : ''));
    }

    public function export(): void
    {
        $userId = Auth::id();
        $mode   = $_GET['mode'] ?? 'month';

        if ($mode === 'range') {
            $from = $_GET['from'] ?? date('Y-m-01');
            $to   = $_GET['to']   ?? date('Y-m-d');
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = date('Y-m-01');
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))   $to   = date('Y-m-d');
            if ($from > $to) [$from, $to] = [$to, $from];
        } else {
            $year  = (int)($_GET['year']  ?? date('Y'));
            $month = (int)($_GET['month'] ?? date('n'));
            $month = max(1, min(12, $month));
            $from  = sprintf('%04d-%02d-01', $year, $month);
            $to    = date('Y-m-t', strtotime($from));
        }

        $expenseModel   = new Expense();
        $expenses       = $expenseModel->getByDateRange($userId, $from, $to);
        $categoryLabels = [
            'opex'      => 'OPEX',
            'marketing' => 'Marketing',
            'cogs'      => 'COGS',
            'liability' => 'Liability',
        ];

        // ── Build CSV in memory ───────────────────────────────────────────────
        $csvBuffer = "\xEF\xBB\xBF";  // UTF-8 BOM
        $csvBuffer .= implode(',', ['Date', 'Category', 'Description', 'Amount (RM)', 'Attachments']) . "\r\n";

        $grandTotal   = 0.0;
        $allReceipts  = [];   // [ ['path'=>..., 'name'=>..., 'zipName'=>...], ... ]

        foreach ($expenses as $e) {
            $amt        = (float)$e['amount'];
            $grandTotal += $amt;
            $catLabel   = $categoryLabels[$e['category']] ?? strtoupper($e['category']);

            // Collect receipts for this expense
            $receipts      = $expenseModel->getReceipts((int)$e['id']);
            $receiptNames  = [];

            foreach ($receipts as $idx => $r) {
                $origExt   = strtolower(pathinfo($r['name'], PATHINFO_EXTENSION));
                // Safe zip filename: date_category_index.ext
                $safeName  = $e['expense_date'] . '_' . strtolower($catLabel) . '_' . ($idx + 1) . '.' . $origExt;
                $receiptNames[] = $safeName;
                $allReceipts[]  = [
                    'path'    => BASE_PATH . '/' . $r['path'],
                    'zipName' => 'receipts/' . $safeName,
                ];
            }

            $attachStr = empty($receiptNames) ? '' : implode('; ', $receiptNames);

            // Escape CSV fields manually to avoid issues with commas in descriptions
            $csvBuffer .= $this->csvRow([
                $e['expense_date'],
                $catLabel,
                $e['description'],
                number_format($amt, 2, '.', ''),
                $attachStr,
            ]);
        }

        $csvBuffer .= $this->csvRow(['', '', 'TOTAL', number_format($grandTotal, 2, '.', ''), '']);

        $baseName = 'expenses_' . $from . '_to_' . $to;

        // ── If no attachments → serve CSV directly ────────────────────────────
        if (empty($allReceipts)) {
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $baseName . '.csv"');
            header('Cache-Control: no-cache');
            echo $csvBuffer;
            exit;
        }

        // ── With attachments → serve ZIP ──────────────────────────────────────
        if (!class_exists('ZipArchive')) {
            // Fallback: serve CSV only
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $baseName . '.csv"');
            header('Cache-Control: no-cache');
            echo $csvBuffer;
            exit;
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'ezkira_exp_');
        $zip     = new \ZipArchive();
        $zip->open($tmpFile, \ZipArchive::OVERWRITE);

        // Add CSV
        $zip->addFromString('expenses.csv', $csvBuffer);

        // Add receipt files
        foreach ($allReceipts as $r) {
            if (file_exists($r['path'])) {
                $zip->addFile($r['path'], $r['zipName']);
            }
        }

        $zip->close();

        $zipSize = filesize($tmpFile);
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $baseName . '.zip"');
        header('Content-Length: ' . $zipSize);
        header('Cache-Control: no-cache');
        readfile($tmpFile);
        unlink($tmpFile);
        exit;
    }

    private function csvRow(array $fields): string
    {
        $escaped = array_map(function ($f) {
            $f = str_replace('"', '""', $f);
            return '"' . $f . '"';
        }, $fields);
        return implode(',', $escaped) . "\r\n";
    }

    public function saveBudgetPct(): void
    {
        CSRF::check();

        $setting = new Setting();
        foreach (self::CATEGORIES as $cat) {
            $val = trim($_POST['pct_' . $cat] ?? '');
            if (!is_numeric($val)) continue;
            $val = max(0, min(100, (float)$val));
            $setting->set('expense_pct_' . $cat, (string)$val);
        }

        Session::flash('success', __('budget_pct_saved'));
        $this->redirect('/expenses');
    }

    // -------------------------------------------------------------------------

    private const RECEIPT_MIMES = [
        'image/jpeg'      => 'jpg',
        'image/png'       => 'png',
        'image/webp'      => 'webp',
        'image/gif'       => 'gif',
        'application/pdf' => 'pdf',
    ];
    private const RECEIPT_MAX_BYTES = 10 * 1024 * 1024;

    /**
     * Save files from $_FILES['receipts'] against an expense, within the user's plan.
     * The expense itself is always kept; skipped files are reported in a flash message.
     */
    private function storeUploadedReceipts(Expense $expenseModel, int $expenseId): void
    {
        if (empty($_FILES['receipts']['name'][0])) {
            return;
        }

        $userId  = (int) Auth::id();
        $status  = Plan::status($userId);
        $files   = $_FILES['receipts'];
        $saved   = 0;
        $limitKey = null;
        $errors  = [];

        for ($i = 0, $n = count($files['name']); $i < $n; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) continue;

            $prepared = $this->prepareReceipt([
                'name'     => $files['name'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
            ]);
            if (isset($prepared['error'])) {
                $errors[] = $prepared['error'];
                continue;
            }

            $limitKey = Plan::uploadBlocker($status, $prepared['size']);
            if ($limitKey !== null) {
                $this->discardPrepared($prepared);
                continue;
            }

            $stored = $this->moveReceipt($prepared);
            if ($stored === null) {
                $errors[] = 'Gagal simpan fail. Sila cuba lagi.';
                continue;
            }

            $expenseModel->addReceipt($expenseId, $stored['path'], $stored['name'], $stored['size']);
            $status['count']++;
            $status['bytes'] += $stored['size'];
            $saved++;
        }
        Plan::forget($userId);

        if ($limitKey !== null) {
            Session::flash('info', __($limitKey, ['limit' => Plan::FREE_RECEIPT_LIMIT]));
        } elseif ($errors) {
            Session::flash('info', implode(' ', array_unique($errors)));
        }
    }

    /** Validate and (for photos) compress an upload into a temp file ready to store. */
    private function prepareReceipt(array $file): array
    {
        if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE
            || $file['size'] > self::RECEIPT_MAX_BYTES) {
            return ['error' => 'Fail terlalu besar. Maksimum 10MB.'];
        }
        if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            return ['error' => 'Muat naik gagal. Sila cuba lagi.'];
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']) ?: '';
        if (!isset(self::RECEIPT_MIMES[$mime])) {
            return ['error' => 'Jenis fail tidak dibenarkan. Guna gambar (JPG, PNG, WebP) atau PDF.'];
        }

        $name       = basename($file['name']);
        $compressed = ReceiptImage::compress($file['tmp_name'], $mime);
        if ($compressed !== null) {
            return [
                'tmp'       => $compressed,
                'temporary' => true,
                'ext'       => 'jpg',
                'name'      => pathinfo($name, PATHINFO_FILENAME) . '.jpg',
                'size'      => (int) filesize($compressed),
            ];
        }

        return [
            'tmp'       => $file['tmp_name'],
            'temporary' => false,
            'ext'       => self::RECEIPT_MIMES[$mime],
            'name'      => $name,
            'size'      => (int) $file['size'],
        ];
    }

    private function moveReceipt(array $prepared): ?array
    {
        $dir = BASE_PATH . '/uploads/receipts/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $newName = bin2hex(random_bytes(16)) . '.' . $prepared['ext'];
        $dest    = $dir . $newName;
        $ok = $prepared['temporary']
            ? rename($prepared['tmp'], $dest)
            : move_uploaded_file($prepared['tmp'], $dest);
        if (!$ok) {
            $this->discardPrepared($prepared);
            return null;
        }
        @chmod($dest, 0644);

        return ['path' => 'uploads/receipts/' . $newName, 'name' => $prepared['name'], 'size' => $prepared['size']];
    }

    private function discardPrepared(array $prepared): void
    {
        if ($prepared['temporary'] && is_file($prepared['tmp'])) {
            @unlink($prepared['tmp']);
        }
    }
}
