<?php
/**
 * Contextual help drawer — included from main.php layout.
 * Detects current page from REQUEST_URI and shows the relevant guide.
 */

$lang   = \App\Core\Session::get('lang', 'en');
$isMy   = ($lang === 'ms');

// Normalise current path
$rawPath  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = rtrim(BASE_URI, '/');
$page     = '/' . ltrim(substr($rawPath, strlen($basePath)), '/');
$page     = strtok($page, '?') ?: '/';   // strip query string

// ─── Help sections per page ───────────────────────────────────────────────────
// Each section: ['icon'=>'...', 'title'=>'...', 'body'=>'...']
// Body supports basic HTML (<b>, <ul><li>, <p>)

if (str_starts_with($page, '/revenue')) {

    $pageLabel = $isMy ? 'Pendapatan' : 'Revenue';
    $sections  = [
        [
            'icon'  => '➕',
            'title' => $isMy ? 'Cara Tambah Jualan' : 'How to Add a Sale',
            'body'  => $isMy
                ? '<p>Klik butang <b>Tambah Jualan</b> di atas. Isi platform, jumlah, tarikh, dan penerangan (pilihan). Klik Simpan.</p><p class="mt-2">Rekod akan muncul dalam senarai bawah dan carta bulanan.</p>'
                : '<p>Click the <b>Add Sale</b> button at the top. Fill in platform, amount, date, and an optional description. Click Save.</p><p class="mt-2">The entry appears in the list below and updates the monthly chart.</p>',
        ],
        [
            'icon'  => '🎯',
            'title' => $isMy ? 'Sasaran Pendapatan Bulanan' : 'Monthly Revenue Target',
            'body'  => $isMy
                ? '<p>Tetapkan sasaran pendapatan untuk bulan semasa di bahagian <b>Sasaran</b>. Ini digunakan untuk mengira peratusan perbelanjaan dan keuntungan di halaman Perbelanjaan.</p>'
                : '<p>Set a revenue target for the current month in the <b>Target</b> section. This is used to calculate expense budget percentages and expected profit on the Expenses page.</p>',
        ],
        [
            'icon'  => '💰',
            'title' => $isMy ? 'Platform Jualan' : 'Sale Platforms',
            'body'  => $isMy
                ? '<ul class="list-disc list-inside space-y-1"><li><b>Shopee / TikTok Shop / Lazada</b> — jualan e-commerce</li><li><b>Instagram / Facebook</b> — jualan media sosial</li><li><b>Walk-in</b> — jualan terus / luar talian</li><li><b>WhatsApp</b> — pesanan melalui WA</li><li><b>Lain-lain</b> — platform lain</li></ul>'
                : '<ul class="list-disc list-inside space-y-1"><li><b>Shopee / TikTok Shop / Lazada</b> — e-commerce sales</li><li><b>Instagram / Facebook</b> — social media sales</li><li><b>Walk-in</b> — direct / offline sales</li><li><b>WhatsApp</b> — orders via WA</li><li><b>Others</b> — any other platform</li></ul>',
        ],
        [
            'icon'  => '📥',
            'title' => $isMy ? 'Export CSV' : 'Export CSV',
            'body'  => $isMy
                ? '<p>Klik butang <b>Export</b> untuk muat turun senarai jualan dalam format CSV. Pilih sama ada export bulanan atau laporan P&amp;L tahunan.</p>'
                : '<p>Click the <b>Export</b> button to download the sale list as a CSV file. Choose between a monthly export or an annual P&amp;L report.</p>',
        ],
    ];

} elseif (str_starts_with($page, '/expenses')) {

    $pageLabel = $isMy ? 'Perbelanjaan' : 'Expenses';
    $sections  = [
        [
            'icon'  => '➕',
            'title' => $isMy ? 'Cara Tambah Perbelanjaan' : 'How to Add an Expense',
            'body'  => $isMy
                ? '<p>Klik butang <b>Tambah Perbelanjaan</b> di atas. Pilih kategori, isi jumlah, tarikh, dan penerangan. Lampirkan resit (pilihan). Klik Simpan.</p>'
                : '<p>Click the <b>Add Expense</b> button at the top. Choose a category, fill in the amount, date, and description. Attach a receipt (optional). Click Save.</p>',
        ],
        [
            'icon'  => '📂',
            'title' => $isMy ? 'Kategori Perbelanjaan' : 'Expense Categories',
            'body'  => $isMy
                ? '<p class="text-xs font-semibold text-gray-500 uppercase mb-1.5">📊 P&amp;L — Kos</p><ul class="space-y-1.5 mb-3"><li><b class="text-amber-600">COGS</b> — Kos barangan dijual (bahan mentah, kos pengeluaran)</li><li><b class="text-cyan-600">Purchases</b> — Pembelian barangan untuk dijual semula</li><li><b class="text-blue-600">OPEX</b> — Kos operasi (sewa, gaji, utiliti)</li><li><b class="text-purple-600">Marketing</b> — Iklan &amp; promosi</li></ul><p class="text-xs font-semibold text-gray-500 uppercase mb-1.5">🏢 Lembaran Imbangan</p><ul class="space-y-1.5"><li><b class="text-teal-600">PPE</b> — Aset tetap (mesin, kenderaan, peralatan)</li><li><b class="text-rose-600">Liabiliti</b> — Bayaran balik pinjaman / hutang</li></ul><p class="mt-2 text-xs text-gray-400">💡 Nilai <b>Inventori</b> (stok yang belum dijual) diisi secara manual dalam Lembaran Imbangan.</p>'
                : '<p class="text-xs font-semibold text-gray-500 uppercase mb-1.5">📊 P&amp;L — Costs</p><ul class="space-y-1.5 mb-3"><li><b class="text-amber-600">COGS</b> — Cost of goods sold (materials, production costs)</li><li><b class="text-cyan-600">Purchases</b> — Goods bought for resale</li><li><b class="text-blue-600">OPEX</b> — Operating costs (rent, salaries, utilities)</li><li><b class="text-purple-600">Marketing</b> — Advertising &amp; promotions</li></ul><p class="text-xs font-semibold text-gray-500 uppercase mb-1.5">🏢 Balance Sheet</p><ul class="space-y-1.5"><li><b class="text-teal-600">PPE</b> — Fixed assets (machinery, vehicles, equipment)</li><li><b class="text-rose-600">Liability</b> — Loan / debt repayments</li></ul><p class="mt-2 text-xs text-gray-400">💡 <b>Inventory</b> value (unsold stock) is entered manually in the Balance Sheet.</p>',
        ],
        [
            'icon'  => '📊',
            'title' => $isMy ? 'Had Perbelanjaan (%)' : 'Budget Percentages',
            'body'  => $isMy
                ? '<p>Klik <b>Tetapkan %</b> untuk set had perbelanjaan berdasarkan peratusan sasaran pendapatan. Contoh: COGS 40% bermakna had COGS ialah 40% daripada sasaran pendapatan bulan tersebut.</p><p class="mt-2">Bar merah = melebihi had. Bar kuning = hampir had.</p>'
                : '<p>Click <b>Configure %</b> to set spending limits as a percentage of your revenue target. Example: COGS 40% means the COGS limit is 40% of that month\'s revenue target.</p><p class="mt-2">Red bar = over budget. Yellow bar = near limit.</p>',
        ],
        [
            'icon'  => '📎',
            'title' => $isMy ? 'Lampiran Resit' : 'Receipt Attachments',
            'body'  => $isMy
                ? '<p>Setiap perbelanjaan boleh ada beberapa lampiran (JPG, PNG, PDF, Excel, dll.). Saiz maksimum 10MB setiap fail. Klik ikon fail untuk melihat atau muat turun.</p>'
                : '<p>Each expense can have multiple attachments (JPG, PNG, PDF, Excel, etc.). Maximum 10 MB per file. Click the file icon to view or download.</p>',
        ],
    ];

} elseif (str_starts_with($page, '/balance-sheet')) {

    $pageLabel = $isMy ? 'Lembaran Imbangan' : 'Balance Sheet';
    $sections  = [
        [
            'icon'  => '📋',
            'title' => $isMy ? 'Apa itu Lembaran Imbangan?' : 'What is a Balance Sheet?',
            'body'  => $isMy
                ? '<p>Lembaran Imbangan menunjukkan kedudukan kewangan perniagaan pada satu tarikh tertentu:</p><ul class="list-disc list-inside mt-2 space-y-1"><li><b>Aset</b> — Apa yang dimiliki perniagaan</li><li><b>Ekuiti</b> — Modal pemilik &amp; keuntungan terkumpul</li><li><b>Liabiliti</b> — Hutang &amp; tanggungan</li></ul><p class="mt-2">Formula: <b>Aset = Ekuiti + Liabiliti</b></p>'
                : '<p>The Balance Sheet shows the financial position of the business at a specific date:</p><ul class="list-disc list-inside mt-2 space-y-1"><li><b>Assets</b> — What the business owns</li><li><b>Equity</b> — Owner\'s capital &amp; retained earnings</li><li><b>Liabilities</b> — Debts &amp; obligations</li></ul><p class="mt-2">Formula: <b>Assets = Equity + Liabilities</b></p>',
        ],
        [
            'icon'  => '🤖',
            'title' => $isMy ? 'Medan Auto-Kira' : 'Auto-Calculated Fields',
            'body'  => $isMy
                ? '<p>Medan bertanda <span class="inline-block bg-emerald-100 text-emerald-700 text-xs font-semibold px-1.5 py-0.5 rounded">Auto</span> dikira secara automatik:</p><ul class="list-disc list-inside mt-2 space-y-1"><li><b>Tunai</b> = Modal + Pendapatan − Semua Perbelanjaan</li><li><b>PPE</b> = Jumlah perbelanjaan kategori PPE</li><li><b>Modal Saham</b> = Jumlah modal yang direkod</li></ul><p class="mt-2 text-xs text-gray-400">💡 <b>Inventori</b> (nilai stok belum dijual) perlu diisi secara manual — nilai ini berbeza daripada kos pembelian (Purchases) dalam P&amp;L.</p>'
                : '<p>Fields marked <span class="inline-block bg-emerald-100 text-emerald-700 text-xs font-semibold px-1.5 py-0.5 rounded">Auto</span> are calculated automatically:</p><ul class="list-disc list-inside mt-2 space-y-1"><li><b>Cash</b> = Capital + Revenue − All Expenses</li><li><b>PPE</b> = Total PPE-category expenses</li><li><b>Share Capital</b> = Total recorded capital</li></ul><p class="mt-2 text-xs text-gray-400">💡 <b>Inventories</b> (value of unsold stock) must be entered manually — it differs from the cost of Purchases recorded in P&amp;L.</p>',
        ],
        [
            'icon'  => '💾',
            'title' => $isMy ? 'Cara Simpan' : 'How to Save',
            'body'  => $isMy
                ? '<p>Pilih tarikh "Setakat" di bahagian atas, isi medan manual (seperti Belum Terima, Pinjaman), kemudian klik <b>Simpan Lembaran Imbangan</b>.</p><p class="mt-2">Setiap tarikh disimpan secara berasingan — anda boleh buat snapshot untuk bulan-bulan berlainan.</p>'
                : '<p>Choose an "As of" date at the top, fill in manual fields (like Trade Receivables, Loans), then click <b>Save Balance Sheet</b>.</p><p class="mt-2">Each date is saved separately — you can create snapshots for different months.</p>',
        ],
        [
            'icon'  => '📥',
            'title' => $isMy ? 'Export' : 'Export',
            'body'  => $isMy
                ? '<p>Klik butang <b>Export</b> untuk muat turun dalam format CSV. Tiga pilihan tersedia:</p><ul class="list-disc list-inside mt-1 space-y-1"><li><b>Tarikh</b> — snapshot untuk tarikh yang dipilih</li><li><b>Bulanan</b> — data bulan terpilih</li><li><b>Tahunan</b> — data tahun penuh</li></ul>'
                : '<p>Click the <b>Export</b> button to download as CSV. Three options are available:</p><ul class="list-disc list-inside mt-1 space-y-1"><li><b>Date</b> — snapshot for the selected date</li><li><b>Monthly</b> — data for the chosen month</li><li><b>Annual</b> — full year data</li></ul>',
        ],
    ];

} elseif (str_starts_with($page, '/capital')) {

    $pageLabel = $isMy ? 'Modal' : 'Capital';
    $sections  = [
        [
            'icon'  => '💰',
            'title' => $isMy ? 'Apa itu Modal?' : 'What is Capital?',
            'body'  => $isMy
                ? '<p>Modal ialah wang atau aset yang dimasukkan oleh pemilik ke dalam perniagaan. Ini berbeza daripada pendapatan — modal adalah pelaburan, bukan hasil jualan.</p>'
                : '<p>Capital is money or assets injected by the owner into the business. This is different from revenue — capital is an investment, not from sales.</p>',
        ],
        [
            'icon'  => '➕',
            'title' => $isMy ? 'Cara Rekod Modal' : 'How to Record Capital',
            'body'  => $isMy
                ? '<p>Klik <b>Tambah Modal</b>, isi jumlah, tarikh, dan penerangan (contoh: "Modal awal", "Suntikan modal"). Modal yang direkod akan muncul secara automatik dalam Lembaran Imbangan di bawah <b>Modal Saham</b>.</p>'
                : '<p>Click <b>Add Capital</b>, fill in the amount, date, and description (e.g., "Initial capital", "Capital injection"). Recorded capital automatically appears in the Balance Sheet under <b>Share Capital</b>.</p>',
        ],
    ];

} elseif (str_starts_with($page, '/profile')) {

    $pageLabel = $isMy ? 'Profil' : 'Profile';
    $sections  = [
        [
            'icon'  => '👤',
            'title' => $isMy ? 'Kemaskini Maklumat Peribadi' : 'Update Personal Info',
            'body'  => $isMy
                ? '<p>Kemas kini nama, nombor WhatsApp, dan bahasa pilihan di tab <b>Maklumat Peribadi</b>. Nombor WA perlu bermula dengan kod negara (contoh: 601XXXXXXXX).</p>'
                : '<p>Update your name, WhatsApp number, and preferred language in the <b>Personal Info</b> tab. WA number must start with the country code (e.g., 601XXXXXXXX).</p>',
        ],
        [
            'icon'  => '🔒',
            'title' => $isMy ? 'Tukar Kata Laluan' : 'Change Password',
            'body'  => $isMy
                ? '<p>Pergi ke tab <b>Kata Laluan</b>. Masukkan kata laluan semasa, kemudian kata laluan baru (minimum 8 aksara, mesti ada huruf besar, nombor, dan simbol).</p>'
                : '<p>Go to the <b>Password</b> tab. Enter your current password, then your new password (minimum 8 characters, must include an uppercase letter, number, and symbol).</p>',
        ],
        [
            'icon'  => '📷',
            'title' => $isMy ? 'Gambar Profil' : 'Profile Picture',
            'body'  => $isMy
                ? '<p>Klik gambar profil di tab <b>Maklumat Peribadi</b> untuk muat naik foto baharu. Format yang diterima: JPG, PNG, WebP. Saiz maksimum: 2MB.</p>'
                : '<p>Click the profile picture in the <b>Personal Info</b> tab to upload a new photo. Accepted formats: JPG, PNG, WebP. Maximum size: 2 MB.</p>',
        ],
        [
            'icon'  => '💬',
            'title' => $isMy ? 'Pesan Selamat Datang WA (Admin)' : 'WA Welcome Message (Admin)',
            'body'  => $isMy
                ? '<p>Admin boleh set pesan WA automatik untuk ahli baru di tab <b>Pesan WA</b>. Gunakan <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{name}</code> dalam teks untuk masukkan nama pengguna. Toggle untuk aktifkan atau nyahaktifkan.</p>'
                : '<p>Admins can set an automatic WA message for new members in the <b>WA Greeting</b> tab. Use <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{name}</code> in the text to insert the user\'s name. Toggle to enable or disable.</p>',
        ],
    ];

} elseif (str_starts_with($page, '/dashboard') || $page === '/') {

    $pageLabel = $isMy ? 'Papan Pemuka' : 'Dashboard';
    $sections  = [
        [
            'icon'  => '📊',
            'title' => $isMy ? 'Kad Ringkasan' : 'Summary Cards',
            'body'  => $isMy
                ? '<p>Empat kad utama menunjukkan prestasi perniagaan anda untuk tempoh yang dipilih:</p><ul class="list-disc list-inside mt-2 space-y-1"><li><b>Pendapatan</b> — Jumlah jualan</li><li><b>Perbelanjaan</b> — Jumlah semua kos</li><li><b>Untung Bersih</b> — Pendapatan tolak perbelanjaan</li><li><b>Margin</b> — Peratusan keuntungan</li></ul>'
                : '<p>Four main cards show your business performance for the selected period:</p><ul class="list-disc list-inside mt-2 space-y-1"><li><b>Revenue</b> — Total sales</li><li><b>Expenses</b> — Total of all costs</li><li><b>Net Profit</b> — Revenue minus expenses</li><li><b>Margin</b> — Profit percentage</li></ul>',
        ],
        [
            'icon'  => '📈',
            'title' => $isMy ? 'Carta Prestasi' : 'Performance Chart',
            'body'  => $isMy
                ? '<p>Carta menunjukkan pendapatan (hijau) vs perbelanjaan (merah) mengikut tempoh yang dipilih — harian, mingguan, bulanan, atau tahunan.</p><p class="mt-2">Gunakan pemilih tempoh di atas carta untuk menukar pandangan.</p>'
                : '<p>The chart shows revenue (green) vs expenses (red) by the selected period — daily, weekly, monthly, or annual.</p><p class="mt-2">Use the period selector above the chart to switch views.</p>',
        ],
        [
            'icon'  => '🔔',
            'title' => $isMy ? 'Aktiviti Terkini' : 'Recent Activity',
            'body'  => $isMy
                ? '<p>Senarai tindakan terkini oleh semua pengguna dalam sistem — tambah jualan, perbelanjaan, kemaskini profil, dll.</p>'
                : '<p>A list of recent actions by all users in the system — adding sales, expenses, profile updates, etc.</p>',
        ],
        [
            'icon'  => '🧭',
            'title' => $isMy ? 'Navigasi' : 'Navigation',
            'body'  => $isMy
                ? '<p>Gunakan menu navigasi di atas untuk ke halaman:</p><ul class="list-disc list-inside mt-1 space-y-1"><li><b>Pendapatan</b> — Rekod jualan</li><li><b>Modal</b> — Rekod modal</li><li><b>Perbelanjaan</b> — Rekod kos</li><li><b>Lembaran Imbangan</b> — Laporan kewangan</li></ul>'
                : '<p>Use the top navigation to go to:</p><ul class="list-disc list-inside mt-1 space-y-1"><li><b>Revenue</b> — Record sales</li><li><b>Capital</b> — Record capital</li><li><b>Expenses</b> — Record costs</li><li><b>Balance Sheet</b> — Financial report</li></ul>',
        ],
    ];

} else {

    $pageLabel = $isMy ? 'Panduan' : 'Help Guide';
    $sections  = [
        [
            'icon'  => '🧭',
            'title' => $isMy ? 'Panduan Umum' : 'General Guide',
            'body'  => $isMy
                ? '<p>Gunakan menu navigasi di atas untuk berpindah antara halaman. Setiap halaman mempunyai panduan tersendiri — klik butang <b>?</b> pada mana-mana halaman untuk membacanya.</p>'
                : '<p>Use the top navigation menu to move between pages. Each page has its own guide — click the <b>?</b> button on any page to read it.</p>',
        ],
        [
            'icon'  => '💡',
            'title' => $isMy ? 'Aliran Kerja Disyorkan' : 'Recommended Workflow',
            'body'  => $isMy
                ? '<ol class="list-decimal list-inside space-y-1"><li>Rekod <b>Modal</b> awal perniagaan</li><li>Tetapkan <b>Sasaran Pendapatan</b> di halaman Pendapatan</li><li>Rekod <b>Jualan</b> setiap hari</li><li>Rekod <b>Perbelanjaan</b> mengikut kategori</li><li>Semak <b>Lembaran Imbangan</b> setiap akhir bulan</li><li>Export laporan untuk simpanan atau audit</li></ol>'
                : '<ol class="list-decimal list-inside space-y-1"><li>Record initial <b>Capital</b></li><li>Set a <b>Revenue Target</b> on the Revenue page</li><li>Record <b>Sales</b> daily</li><li>Record <b>Expenses</b> by category</li><li>Check the <b>Balance Sheet</b> at month-end</li><li>Export reports for record-keeping or audit</li></ol>',
        ],
    ];
}

$drawerTitle = ($isMy ? 'Panduan — ' : 'Guide — ') . $pageLabel;
?>

<!-- ── Help Floating Button ───────────────────────────────────────────────── -->
<button type="button" id="help-btn"
        onclick="toggleHelpDrawer()"
        title="<?= $isMy ? 'Panduan Halaman Ini' : 'Page Help Guide' ?>"
        class="fixed bottom-24 right-6 z-40 flex items-center justify-center w-12 h-12 rounded-full shadow-lg
               bg-brand-600 hover:bg-brand-700 text-white transition-all duration-200 hover:scale-110
               focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
</button>

<!-- ── Help Drawer Overlay ────────────────────────────────────────────────── -->
<div id="help-overlay"
     onclick="closeHelpDrawer()"
     class="hidden fixed inset-0 z-40 bg-black/40 backdrop-blur-sm transition-opacity"></div>

<!-- ── Help Drawer Panel ──────────────────────────────────────────────────── -->
<aside id="help-drawer"
       class="fixed top-0 right-0 z-50 h-full w-80 max-w-[92vw] bg-white dark:bg-gray-900
              shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300 ease-in-out">

    <!-- Header -->
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800 bg-brand-600 shrink-0">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-white/80 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h2 class="text-sm font-semibold text-white"><?= htmlspecialchars($drawerTitle, ENT_QUOTES) ?></h2>
        </div>
        <button type="button" onclick="closeHelpDrawer()"
                class="p-1 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Scrollable content -->
    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3">

        <?php foreach ($sections as $i => $sec): ?>
        <div class="rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
            <!-- Accordion trigger -->
            <button type="button"
                    onclick="toggleSection('help-sec-<?= $i ?>')"
                    class="w-full flex items-center justify-between gap-3 px-4 py-3
                           bg-gray-50 dark:bg-gray-800/60 hover:bg-gray-100 dark:hover:bg-gray-700/60
                           text-left transition-colors group">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="text-lg leading-none shrink-0"><?= $sec['icon'] ?></span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                        <?= htmlspecialchars($sec['title'], ENT_QUOTES) ?>
                    </span>
                </div>
                <svg id="help-sec-<?= $i ?>-icon"
                     class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0 transition-transform duration-200"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <!-- Body (hidden by default, first one open) -->
            <div id="help-sec-<?= $i ?>"
                 class="<?= $i === 0 ? '' : 'hidden' ?> px-4 py-3 text-sm text-gray-600 dark:text-gray-400
                         leading-relaxed border-t border-gray-100 dark:border-gray-800
                         bg-white dark:bg-gray-900/50">
                <?= $sec['body'] ?>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Footer note -->
        <p class="text-xs text-gray-400 dark:text-gray-600 text-center pt-2 pb-1">
            <?= $isMy ? 'Perlu lebih bantuan?' : 'Need more help?' ?>
            <a href="https://wa.me/60122541050?text=<?= rawurlencode($isMy ? 'Hi, saya perlukan bantuan dengan EZKIRA.' : 'Hi, I need help with EZKIRA.') ?>"
               target="_blank" rel="noopener"
               class="text-brand-600 dark:text-brand-400 hover:underline font-medium">
                <?= $isMy ? 'Hubungi kami' : 'Contact us' ?>
            </a>
        </p>
    </div>
</aside>

<script>
function toggleHelpDrawer() {
    var open = !document.getElementById('help-drawer').classList.contains('translate-x-full');
    if (open) { closeHelpDrawer(); } else { openHelpDrawer(); }
}
function openHelpDrawer() {
    document.getElementById('help-drawer').classList.remove('translate-x-full');
    document.getElementById('help-overlay').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeHelpDrawer() {
    document.getElementById('help-drawer').classList.add('translate-x-full');
    document.getElementById('help-overlay').classList.add('hidden');
    document.body.style.overflow = '';
}
function toggleSection(id) {
    var body = document.getElementById(id);
    var icon = document.getElementById(id + '-icon');
    var hidden = body.classList.contains('hidden');
    body.classList.toggle('hidden', !hidden);
    if (icon) icon.style.transform = hidden ? 'rotate(180deg)' : '';
}
// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeHelpDrawer();
});
</script>
