<?php

namespace Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\CSRF;
use App\Core\Session;
use Models\Expense;
use Models\Revenue;
use Models\Setting;

class ExpenseController extends Controller
{
    private const CATEGORIES = ['opex', 'marketing', 'cogs'];

    public const SUGGESTED_PCT = ['opex' => 20, 'marketing' => 10, 'cogs' => 40];

    public function index(): void
    {
        $year  = (int)($_GET['year']  ?? date('Y'));
        $month = (int)($_GET['month'] ?? date('n'));
        $month = max(1, min(12, $month));
        $year  = max(2020, min((int)date('Y') + 1, $year));

        $model         = new Expense();
        $setting       = new Setting();
        $targetRevenue = (new Revenue())->getTarget($year, $month);

        $pcts = [
            'opex'      => (float)($setting->get('expense_pct_opex')      ?? self::SUGGESTED_PCT['opex']),
            'marketing' => (float)($setting->get('expense_pct_marketing') ?? self::SUGGESTED_PCT['marketing']),
            'cogs'      => (float)($setting->get('expense_pct_cogs')      ?? self::SUGGESTED_PCT['cogs']),
        ];

        $data = [
            'year'          => $year,
            'month'         => $month,
            'targetRevenue' => $targetRevenue,
            'pcts'          => $pcts,
            'expenses' => [
                'opex'      => $model->byCategory('opex',      $year, $month),
                'marketing' => $model->byCategory('marketing', $year, $month),
                'cogs'      => $model->byCategory('cogs',      $year, $month),
            ],
            'totals' => [
                'opex'      => $model->totalByCategory('opex',      $year, $month),
                'marketing' => $model->totalByCategory('marketing', $year, $month),
                'cogs'      => $model->totalByCategory('cogs',      $year, $month),
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

        $receiptPath = null;
        $receiptName = null;

        if (!empty($_FILES['receipt']['name'])) {
            $upload = $this->uploadReceipt($_FILES['receipt']);
            if (isset($upload['error'])) {
                Session::flash('error', $upload['error']);
                $this->redirect('/expenses#' . $category);
            }
            $receiptPath = $upload['path'];
            $receiptName = $upload['name'];
        }

        (new Expense())->create([
            'user_id'      => Auth::id(),
            'category'     => $category,
            'amount'       => (float)$amount,
            'description'  => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
            'expense_date' => $date,
            'receipt_path' => $receiptPath,
            'receipt_name' => $receiptName,
        ]);

        $y = date('Y', strtotime($date));
        $m = date('n', strtotime($date));
        Session::flash('success', 'Rekod perbelanjaan berjaya ditambah.');
        $this->redirect("/expenses?year={$y}&month={$m}#" . $category);
    }

    public function delete(string $id): void
    {
        CSRF::check();

        $expense = (new Expense())->findById((int)$id);
        if (!$expense) {
            Session::flash('error', 'Rekod tidak dijumpai.');
            $this->redirect('/expenses');
        }

        if (!empty($expense['receipt_path'])) {
            $full = BASE_PATH . '/' . $expense['receipt_path'];
            if (file_exists($full)) {
                unlink($full);
            }
        }

        (new Expense())->delete((int)$id);
        Session::flash('success', 'Rekod berjaya dipadam.');
        $this->redirect('/expenses');
    }

    public function receipt(string $id): void
    {
        $expense = (new Expense())->findById((int)$id);
        if (!$expense || empty($expense['receipt_path'])) {
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
    private function uploadReceipt(array $file): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'Muat naik gagal. Sila cuba lagi.'];
        }

        if ($file['size'] > 10 * 1024 * 1024) {
            return ['error' => 'Fail terlalu besar. Maksimum 10MB.'];
        }

        $orig    = basename($file['name']);
        $ext     = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp','pdf','doc','docx','xls','xlsx','txt','zip'];

        if (!in_array($ext, $allowed, true)) {
            return ['error' => 'Jenis fail tidak dibenarkan.'];
        }

        $dir = BASE_PATH . '/uploads/receipts/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $newName = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest    = $dir . $newName;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['error' => 'Gagal simpan fail. Semak kebenaran folder.'];
        }

        return [
            'path' => 'uploads/receipts/' . $newName,
            'name' => $orig,
        ];
    }
}
