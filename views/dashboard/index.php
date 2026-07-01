<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
<style>
.flatpickr-calendar { font-family: Inter, ui-sans-serif, sans-serif; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); border: 1px solid #e5e7eb; }
.flatpickr-day.selected, .flatpickr-day.selected:hover { background: #16a34a; border-color: #16a34a; }
.flatpickr-day:hover { background: #f0fdf4; }
.flatpickr-months .flatpickr-month, .flatpickr-weekdays { background: #16a34a; border-radius: 12px 12px 0 0; }
.flatpickr-current-month .flatpickr-monthDropdown-months, .flatpickr-current-month input.cur-year { color: #fff; }
.flatpickr-weekday { color: rgba(255,255,255,0.8) !important; }
.flatpickr-prev-month svg, .flatpickr-next-month svg { fill: #fff !important; }
.flatpickr-day.week-highlight { background: #f0fdf4; }
.flatpickr-monthSelect-month { border-radius: 8px !important; }
.flatpickr-monthSelect-month.selected { background: #16a34a !important; border-color: #16a34a !important; }
</style>
<?php
$roleLabelMap = [
    'admin'  => __('role_admin'),
    'team'   => __('role_team'),
    'client' => __('role_client'),
];
$roleColorMap = [
    'admin'  => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
    'team'   => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
    'client' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
];
$role      = $user['role'] ?? 'client';
$roleLabel = $roleLabelMap[$role] ?? 'Client';
$roleColor = $roleColorMap[$role] ?? $roleColorMap['client'];

$cards = [
    [
        'key'   => 'total_revenue',
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>',
        'color' => 'bg-emerald-500',
        'bg'    => 'bg-emerald-50 dark:bg-emerald-900/20',
        'text'  => 'text-emerald-600 dark:text-emerald-400',
        'value' => number_format($summary['total_revenue'], 2),
        'href'  => BASE_URI . '/revenue',
    ],
    [
        'key'   => 'total_expenses',
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17H5m0 0V9m0 8l8-8 4 4 6-6"/>',
        'color' => 'bg-red-500',
        'bg'    => 'bg-red-50 dark:bg-red-900/20',
        'text'  => 'text-red-600 dark:text-red-400',
        'value' => number_format($summary['total_expenses'], 2),
        'href'  => BASE_URI . '/expenses',
    ],
    [
        'key'   => 'net_profit',
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 6v1m0 6v-1m-6-4h12"/>',
        'color' => 'bg-brand-600',
        'bg'    => 'bg-brand-50 dark:bg-brand-900/20',
        'text'  => 'text-brand-600 dark:text-brand-400',
        'value' => number_format($summary['net_profit'], 2),
        'href'  => BASE_URI . '/revenue',
    ],
    [
        'key'   => 'transactions',
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
        'color' => 'bg-amber-500',
        'bg'    => 'bg-amber-50 dark:bg-amber-900/20',
        'text'  => 'text-amber-600 dark:text-amber-400',
        'value' => number_format($summary['transactions']),
        'noPrefix' => true,
        'href'  => BASE_URI . '/revenue',
    ],
];

$platformLabels = \Models\Revenue::PLATFORMS;
$categoryLabels = ['opex' => 'OPEX', 'marketing' => 'Marketing', 'cogs' => 'COGS'];
?>

<?php
$periods = [
    'daily'   => __('period_daily'),
    'weekly'  => __('period_weekly'),
    'monthly' => __('period_monthly'),
    'annual'  => __('period_annual'),
];
$exportUrl = BASE_URI . '/revenue/export-pnl?period=' . $period . '&year=' . $year . '&month=' . $month . '&week=' . $week . '&date=' . $date;
?>

<!-- Period Filter + Export -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <!-- Period Tabs + Picker -->
    <div class="flex flex-wrap items-center gap-2">
        <div class="flex items-center bg-gray-100 dark:bg-gray-800 rounded-xl p-1 gap-0.5">
            <?php foreach ($periods as $key => $label):
                $active = $period === $key;
                $href   = BASE_URI . '/dashboard?period=' . $key . '&year=' . $year . '&month=' . $month . '&week=' . $week . '&date=' . $date;
            ?>
            <a href="<?= $href ?>"
               class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors <?= $active
                   ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm'
                   : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' ?>">
                <?= $label ?>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Daily: date picker (Flatpickr calendar) -->
        <?php if ($period === 'daily'): ?>
        <div class="relative">
            <input type="text"
                   id="picker-daily"
                   value="<?= date('d M Y', strtotime($date)) ?>"
                   data-date="<?= htmlspecialchars($date, ENT_QUOTES) ?>"
                   readonly
                   placeholder="Pilih tarikh"
                   class="pl-9 pr-3 py-1.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600
                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                          focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                          transition-colors cursor-pointer w-40">
            <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>

        <!-- Weekly: Flatpickr calendar -->
        <?php elseif ($period === 'weekly'):
            $weekStartDate = date('Y-m-d', strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
        ?>
        <div class="relative">
            <input type="text"
                   id="picker-weekly"
                   value="Week <?= $week ?>, <?= $year ?>"
                   data-date="<?= $weekStartDate ?>"
                   readonly
                   placeholder="Pilih minggu"
                   class="pl-9 pr-3 py-1.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600
                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                          focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                          transition-colors cursor-pointer w-40">
            <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>

        <!-- Monthly: Flatpickr month picker -->
        <?php elseif ($period === 'monthly'): ?>
        <div class="relative">
            <input type="text"
                   id="picker-monthly"
                   value="<?= date('F Y', mktime(0,0,0,$month,1,$year)) ?>"
                   data-date="<?= $year ?>-<?= str_pad($month, 2, '0', STR_PAD_LEFT) ?>-01"
                   readonly
                   placeholder="Pilih bulan"
                   class="pl-9 pr-3 py-1.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600
                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                          focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                          transition-colors cursor-pointer w-40">
            <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>

        <!-- Annual: year select -->
        <?php elseif ($period === 'annual'): ?>
        <select id="picker-annual"
                class="px-3 py-1.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600
                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                       focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                       transition-colors cursor-pointer">
            <?php for ($y = (int)date('Y'); $y >= 2020; $y--): ?>
            <option value="<?= $y ?>" <?= $y === $year ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
        <?php endif; ?>
    </div>

    <!-- Single Export Laporan button -->
    <button type="button" onclick="openExportModal()"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 rounded-lg transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Export Laporan
    </button>
</div>

<!-- ===== EXPORT LAPORAN MODAL ===== -->
<div id="dash-export-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeExportModal()"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 dark:border-gray-700">
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Export Laporan P&amp;L</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pilih tempoh dan tarikh</p>
            </div>
            <button type="button" onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="px-6 py-5 space-y-4">
            <!-- Period tabs -->
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Tempoh Laporan</p>
                <div class="grid grid-cols-5 gap-1 bg-gray-100 dark:bg-gray-700 rounded-xl p-1">
                    <?php foreach (['daily'=>'Harian','weekly'=>'Mingguan','monthly'=>'Bulanan','annual'=>'Tahunan','range'=>'Custom'] as $pk=>$pl): ?>
                    <button type="button" id="epbtn-<?= $pk ?>" onclick="switchExportPeriod('<?= $pk ?>')"
                            class="py-1.5 text-xs font-medium rounded-lg transition-colors <?= $pk==='monthly' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' ?>">
                        <?= $pl ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Date pickers -->
            <!-- Daily -->
            <div id="ep-daily" class="hidden">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Pilih Tarikh</label>
                <input type="date" id="ep-daily-date" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>"
                       class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
            <!-- Weekly -->
            <div id="ep-weekly" class="hidden">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Pilih Minggu</label>
                <input type="week" id="ep-weekly-date" value="<?= date('Y') ?>-W<?= str_pad(date('W'), 2, '0', STR_PAD_LEFT) ?>"
                       max="<?= date('Y') ?>-W<?= str_pad(date('W'), 2, '0', STR_PAD_LEFT) ?>"
                       class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
            <!-- Monthly -->
            <div id="ep-monthly">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Pilih Bulan</label>
                <input type="month" id="ep-month-input"
                       value="<?= $year ?>-<?= str_pad($month, 2, '0', STR_PAD_LEFT) ?>"
                       max="<?= date('Y-m') ?>"
                       class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
            <!-- Annual -->
            <div id="ep-annual" class="hidden">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Pilih Tahun</label>
                <select id="ep-year-annual" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <?php for ($y=(int)date('Y'); $y>=2020; $y--): ?>
                    <option value="<?= $y ?>" <?= $y===$year ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <!-- Custom Range -->
            <div id="ep-range" class="hidden space-y-2">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Dari Tarikh</label>
                    <input type="date" id="ep-range-from" value="<?= date('Y-m-01') ?>" max="<?= date('Y-m-d') ?>"
                           class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Hingga Tarikh</label>
                    <input type="date" id="ep-range-to" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>"
                           class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <p class="text-xs text-amber-600 dark:text-amber-400">⚠ Custom range hanya tersedia untuk export sahaja (bukan view)</p>
            </div>
        </div>

        <!-- Actions -->
        <div class="px-6 pb-6 flex flex-col gap-2">
            <button type="button" onclick="doViewDashboard()"
                    class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-xl transition-colors text-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Lihat di Dashboard dahulu
            </button>
            <button type="button" onclick="doExportPnl()"
                    class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export CSV
            </button>
        </div>
    </div>
</div>
<!-- ===== END EXPORT LAPORAN MODAL ===== -->

<script>
var _epMode = 'monthly';
var _epBtnA = 'py-1.5 text-xs font-medium rounded-lg transition-colors bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm';
var _epBtnI = 'py-1.5 text-xs font-medium rounded-lg transition-colors text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300';

function openExportModal() {
    document.getElementById('dash-export-modal').classList.remove('hidden');
}
function closeExportModal() {
    document.getElementById('dash-export-modal').classList.add('hidden');
}
function switchExportPeriod(p) {
    _epMode = p;
    ['daily','weekly','monthly','annual','range'].forEach(function(k) {
        document.getElementById('epbtn-' + k).className = k === p ? _epBtnA : _epBtnI;
        document.getElementById('ep-' + k).classList.toggle('hidden', k !== p);
    });
}

function _buildExportUrl(forView) {
    var base = '<?= BASE_URI ?>';
    if (_epMode === 'daily') {
        var d = document.getElementById('ep-daily-date').value;
        if (!d) { alert('Sila pilih tarikh.'); return null; }
        if (forView) return base + '/dashboard?period=daily&date=' + d;
        return base + '/revenue/export-pnl?period=daily&date=' + d + '&year=' + d.substring(0,4) + '&month=' + parseInt(d.substring(5,7)) + '&week=1';
    }
    if (_epMode === 'weekly') {
        var w = document.getElementById('ep-weekly-date').value; // "YYYY-Www"
        if (!w) { alert('Sila pilih minggu.'); return null; }
        var yr = w.substring(0,4);
        var wk = parseInt(w.substring(6));
        if (forView) return base + '/dashboard?period=weekly&year=' + yr + '&week=' + wk;
        return base + '/revenue/export-pnl?period=weekly&year=' + yr + '&week=' + wk;
    }
    if (_epMode === 'monthly') {
        var mv = document.getElementById('ep-month-input').value; // "YYYY-MM"
        if (!mv) { alert('Sila pilih bulan.'); return null; }
        var y = mv.substring(0, 4);
        var m = parseInt(mv.substring(5, 7));
        if (forView) return base + '/dashboard?period=monthly&year=' + y + '&month=' + m;
        return base + '/revenue/export-pnl?period=monthly&year=' + y + '&month=' + m;
    }
    if (_epMode === 'annual') {
        var ya = document.getElementById('ep-year-annual').value;
        if (forView) return base + '/dashboard?period=annual&year=' + ya;
        return base + '/revenue/export-pnl?period=annual&year=' + ya;
    }
    if (_epMode === 'range') {
        if (forView) { alert('Custom range hanya untuk export sahaja.'); return null; }
        var from = document.getElementById('ep-range-from').value;
        var to   = document.getElementById('ep-range-to').value;
        if (!from || !to) { alert('Sila isi kedua-dua tarikh.'); return null; }
        if (from > to) { alert('Tarikh mula mesti sebelum tarikh tamat.'); return null; }
        return base + '/revenue/export-pnl?period=monthly&year=' + from.substring(0,4) + '&month=' + parseInt(from.substring(5,7)) + '&date_from=' + from + '&date_to=' + to;
    }
    return null;
}

function doViewDashboard() {
    var url = _buildExportUrl(true);
    if (url) { closeExportModal(); window.location.href = url; }
}
function doExportPnl() {
    var url = _buildExportUrl(false);
    if (url) { closeExportModal(); window.location.href = url; }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<script>
(function() {
    var base = '<?= BASE_URI ?>/dashboard';

    function getISOWeek(date) {
        var d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
        var day = d.getUTCDay() || 7;
        d.setUTCDate(d.getUTCDate() + 4 - day);
        var yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
        return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
    }

    // Daily
    var dailyEl = document.getElementById('picker-daily');
    if (dailyEl) {
        flatpickr(dailyEl, {
            dateFormat: 'Y-m-d',
            maxDate: 'today',
            defaultDate: dailyEl.getAttribute('data-date'),
            onChange: function(selectedDates, dateStr) {
                window.location.href = base + '?period=daily&date=' + dateStr;
            }
        });
    }

    // Weekly
    var weeklyEl = document.getElementById('picker-weekly');
    if (weeklyEl) {
        flatpickr(weeklyEl, {
            defaultDate: weeklyEl.getAttribute('data-date'),
            weekNumbers: true,
            maxDate: 'today',
            onChange: function(selectedDates) {
                if (!selectedDates.length) return;
                var d = selectedDates[0];
                var yr = d.getFullYear();
                var wk = getISOWeek(d);
                weeklyEl.value = 'Week ' + wk + ', ' + yr;
                window.location.href = base + '?period=weekly&year=' + yr + '&week=' + wk;
            }
        });
    }

    // Monthly
    var monthlyEl = document.getElementById('picker-monthly');
    if (monthlyEl) {
        flatpickr(monthlyEl, {
            plugins: [new monthSelectPlugin({ shorthand: false, dateFormat: 'F Y', altFormat: 'F Y' })],
            defaultDate: monthlyEl.getAttribute('data-date'),
            onChange: function(selectedDates) {
                if (!selectedDates.length) return;
                var d = selectedDates[0];
                var y = d.getFullYear();
                var m = d.getMonth() + 1;
                window.location.href = base + '?period=monthly&year=' + y + '&month=' + m;
            }
        });
    }

    // Annual
    var annual = document.getElementById('picker-annual');
    if (annual) {
        annual.addEventListener('change', function() {
            window.location.href = base + '?period=annual&year=' + this.value;
        });
    }
})();
</script>

<!-- Welcome Section -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            <?= __('welcome', ['name' => htmlspecialchars($user['name'] ?? '', ENT_QUOTES)]) ?>
        </h1>
        <div class="flex items-center gap-2 mt-2">
            <span class="text-sm text-gray-500 dark:text-gray-400"><?= __('your_role') ?>:</span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $roleColor ?>">
                <?= htmlspecialchars($roleLabel, ENT_QUOTES) ?>
            </span>
        </div>
    </div>
    <div class="text-sm text-gray-400 dark:text-gray-500">
        <?= date('l, j F Y') ?>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <?php foreach ($cards as $card): ?>
    <a href="<?= $card['href'] ?>" class="block bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md hover:border-brand-300 dark:hover:border-brand-600 transition-all group">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                    <?= __($card['key']) ?>
                </p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= empty($card['noPrefix']) ? __('currency') . ' ' : '' ?><?= $card['value'] ?>
                </p>
            </div>
            <div class="<?= $card['bg'] ?> p-2.5 rounded-xl group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 <?= $card['text'] ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <?= $card['icon'] ?>
                </svg>
            </div>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<!-- Charts + Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Donut Charts Card -->
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                <?= __('expenses_overview') ?>
            </h2>
            <span class="text-xs text-gray-500 dark:text-gray-400 capitalize"><?= $periods[$period] ?? '' ?></span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            <!-- Chart 1: Expenses Composition -->
            <div class="flex flex-col items-center">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">
                    <?= __('expenses_composition') ?>
                </p>
                <canvas id="chart-breakdown" width="180" height="180" class="max-w-full"></canvas>
                <!-- Legend -->
                <div class="mt-4 space-y-1.5 w-full max-w-[200px]">
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></span>
                            <span class="text-gray-600 dark:text-gray-400">OPEX</span>
                        </span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">RM <?= number_format($chartData['opex'], 2) ?></span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-500 shrink-0"></span>
                            <span class="text-gray-600 dark:text-gray-400">Marketing</span>
                        </span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">RM <?= number_format($chartData['marketing'], 2) ?></span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                            <span class="text-gray-600 dark:text-gray-400">COGS</span>
                        </span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">RM <?= number_format($chartData['cogs'], 2) ?></span>
                    </div>
                    <?php if ($targetRevenue > 0 && !$chartData['overBudget']): ?>
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-gray-200 dark:bg-gray-600 shrink-0"></span>
                            <span class="text-gray-600 dark:text-gray-400"><?= __('remaining') ?></span>
                        </span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">
                            RM <?= number_format(max(0, $targetRevenue - $chartData['total']), 2) ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Chart 2: Budget Health -->
            <div class="flex flex-col items-center">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">
                    <?= __('budget_health') ?>
                </p>
                <canvas id="chart-health" width="180" height="180" class="max-w-full"></canvas>
                <!-- Legend -->
                <div class="mt-4 space-y-1.5 w-full max-w-[200px]">
                    <?php
                    $rev    = $chartData['revenue'];
                    $expPct = $rev > 0 ? min(100, ($chartData['total'] / $rev) * 100) : 0;
                    $proPct = $rev > 0 ? max(0, 100 - $expPct) : 0;
                    ?>
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full <?= $chartData['overBudget'] ? 'bg-red-600' : 'bg-red-400' ?> shrink-0"></span>
                            <span class="text-gray-600 dark:text-gray-400"><?= __('total_expenses') ?></span>
                        </span>
                        <span class="font-medium text-gray-800 dark:text-gray-200"><?= number_format($expPct, 1) ?>%</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="text-gray-600 dark:text-gray-400"><?= __('net_profit') ?></span>
                        </span>
                        <span class="font-medium <?= $chartData['overBudget'] ? 'text-red-500' : 'text-emerald-600 dark:text-emerald-400' ?>">
                            RM <?= number_format(abs($chartData['profit']), 2) ?>
                        </span>
                    </div>
                    <?php if ($rev <= 0): ?>
                    <p class="text-xs text-gray-400 dark:text-gray-500 text-center pt-1">
                        <?= __('add_revenue_to_see') ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white"><?= __('recent_transactions') ?></h2>
            <div class="flex gap-1.5">
                <span class="inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400"><span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span><?= __('money_in') ?></span>
                <span class="inline-flex items-center gap-1 text-xs text-red-500 dark:text-red-400"><span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span><?= __('money_out') ?></span>
            </div>
        </div>

        <?php if (empty($transactions)): ?>
            <div class="flex flex-col items-center justify-center flex-1 h-40 text-center">
                <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <p class="text-sm text-gray-400 dark:text-gray-500"><?= __('no_activity') ?></p>
            </div>
        <?php else: ?>
            <div class="space-y-0 overflow-y-auto max-h-72 -mx-1 px-1 divide-y divide-gray-50 dark:divide-gray-700/50">
                <?php foreach ($transactions as $txn):
                    $isRevenue = $txn['type'] === 'revenue';
                    $sign      = $isRevenue ? '+' : '−';
                    $amtClass  = $isRevenue
                        ? 'text-emerald-600 dark:text-emerald-400 font-semibold'
                        : 'text-red-500 dark:text-red-400 font-semibold';
                    $dotClass  = $isRevenue ? 'bg-emerald-500' : 'bg-red-500';
                    $label     = $isRevenue
                        ? ($platformLabels[$txn['category']] ?? ucfirst($txn['category']))
                        : ($categoryLabels[$txn['category']] ?? ucfirst($txn['category']));
                    $desc      = !empty($txn['description']) ? $txn['description'] : '—';
                    $date      = date('j M', strtotime($txn['txn_date']));
                ?>
                <div class="flex items-center gap-3 py-2.5">
                    <span class="flex-shrink-0 w-2 h-2 rounded-full <?= $dotClass ?>"></span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                            <?= htmlspecialchars($desc, ENT_QUOTES) ?>
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            <?= $label ?> · <?= $date ?>
                        </p>
                    </div>
                    <span class="text-sm <?= $amtClass ?> tabular-nums whitespace-nowrap">
                        <?= $sign ?> RM <?= number_format((float)$txn['amount'], 2) ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Footer links -->
        <div class="flex justify-between mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 text-xs">
            <a href="<?= BASE_URI ?>/revenue" class="text-emerald-600 dark:text-emerald-400 hover:underline"><?= __('revenue') ?> →</a>
            <a href="<?= BASE_URI ?>/expenses" class="text-red-500 dark:text-red-400 hover:underline"><?= __('expenses') ?> →</a>
        </div>
    </div>
</div>

<!-- Comparison Chart — full width -->
<div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
        <div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white" id="compare-title">
                <?= __('compare_month_title') ?>
            </h2>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5" id="compare-subtitle">
                <?= $compareMonth['prev_label'] ?> vs <?= $compareMonth['cur_label'] ?>
            </p>
        </div>
        <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-xl p-1 gap-0.5">
            <button id="btn-day" onclick="switchCompare('day')"
                    class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors text-gray-500 dark:text-gray-400 hover:text-gray-700">
                <?= __('compare_day') ?>
            </button>
            <button id="btn-month" onclick="switchCompare('month')"
                    class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm">
                <?= __('compare_month') ?>
            </button>
            <button id="btn-year" onclick="switchCompare('year')"
                    class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors text-gray-500 dark:text-gray-400 hover:text-gray-700">
                <?= __('compare_year') ?>
            </button>
        </div>
    </div>
    <div class="relative" style="height:260px">
        <canvas id="chart-compare" style="width:100%;height:100%"></canvas>
    </div>
    <div class="flex flex-wrap items-center gap-4 mt-4 text-xs">
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-brand-500 inline-block"></span><span class="text-gray-600 dark:text-gray-300" id="leg-rev-a"></span></span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-brand-200 inline-block"></span><span class="text-gray-600 dark:text-gray-300" id="leg-rev-b"></span></span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-red-400 inline-block"></span><span class="text-gray-600 dark:text-gray-300" id="leg-exp-a"></span></span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-red-200 inline-block"></span><span class="text-gray-600 dark:text-gray-300" id="leg-exp-b"></span></span>
    </div>
</div>

<script>
(function () {
    var chartData = <?= json_encode($chartData) ?>;
    var targetRevenue = <?= json_encode($targetRevenue) ?>;

    function drawDonut(canvasId, segments, centerLine1, centerLine2) {
        var canvas = document.getElementById(canvasId);
        if (!canvas) return;
        var ctx = canvas.getContext('2d');
        var dpr = window.devicePixelRatio || 1;
        var size = canvas.offsetWidth || 180;
        canvas.width  = size * dpr;
        canvas.height = size * dpr;
        ctx.scale(dpr, dpr);

        var cx = size / 2, cy = size / 2;
        var outerR = cx - 10;
        var innerR = outerR * 0.58;
        var isDark = document.documentElement.classList.contains('dark');
        var emptyColor = isDark ? '#374151' : '#e5e7eb';

        ctx.clearRect(0, 0, size, size);

        var total = segments.reduce(function (s, seg) { return s + seg.value; }, 0);

        if (total <= 0) {
            ctx.beginPath();
            ctx.arc(cx, cy, outerR, 0, Math.PI * 2);
            ctx.arc(cx, cy, innerR, 0, Math.PI * 2, true);
            ctx.fillStyle = emptyColor;
            ctx.fill('evenodd');
        } else {
            var angle = -Math.PI / 2;
            segments.forEach(function (seg) {
                if (seg.value <= 0) return;
                var sweep = (seg.value / total) * 2 * Math.PI;
                ctx.beginPath();
                ctx.moveTo(cx + outerR * Math.cos(angle), cy + outerR * Math.sin(angle));
                ctx.arc(cx, cy, outerR, angle, angle + sweep);
                ctx.arc(cx, cy, innerR, angle + sweep, angle, true);
                ctx.closePath();
                ctx.fillStyle = seg.color;
                ctx.fill();
                angle += sweep;
            });
        }

        var textColor  = isDark ? '#f9fafb' : '#111827';
        var subColor   = isDark ? '#9ca3af' : '#6b7280';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        if (centerLine1) {
            ctx.fillStyle = textColor;
            ctx.font = 'bold 12px Inter, ui-sans-serif, sans-serif';
            ctx.fillText(centerLine1, cx, centerLine2 ? cy - 8 : cy);
        }
        if (centerLine2) {
            ctx.fillStyle = subColor;
            ctx.font = '10px Inter, ui-sans-serif, sans-serif';
            ctx.fillText(centerLine2, cx, cy + 9);
        }
    }

    function renderCharts() {
        var total = chartData.total;
        var remaining = targetRevenue > 0 ? Math.max(0, targetRevenue - total) : 0;
        var isDark = document.documentElement.classList.contains('dark');
        var remainColor = isDark ? '#374151' : '#e5e7eb';

        var breakdown = [
            { value: chartData.opex,      color: '#3b82f6' },
            { value: chartData.marketing, color: '#a855f7' },
            { value: chartData.cogs,      color: '#f59e0b' },
        ];
        if (remaining > 0) breakdown.push({ value: remaining, color: remainColor });
        var totalLabel = total > 0 ? 'RM ' + total.toLocaleString('en-MY', {minimumFractionDigits:0, maximumFractionDigits:0}) : 'No data';
        drawDonut('chart-breakdown', breakdown, totalLabel, 'Total Spent');

        var rev = chartData.revenue || 0;
        var expPct    = rev > 0 ? Math.min(100, (total / rev) * 100) : 0;
        var profitPct = Math.max(0, 100 - expPct);
        var overBudget = chartData.overBudget;
        var health = [
            { value: expPct,    color: overBudget ? '#ef4444' : '#f87171' },
            { value: profitPct, color: '#10b981' },
        ];
        var healthLabel = rev > 0 ? expPct.toFixed(1) + '%' : 'No data';
        var healthSub   = rev > 0 ? 'Expenses used' : 'Add revenue first';
        drawDonut('chart-health', health, healthLabel, healthSub);
    }

    window.renderDashboardCharts = function() { renderCharts(); renderCompare(); };
    document.addEventListener('DOMContentLoaded', function() { renderCharts(); renderCompare(); });
})();

// ── Comparison Chart ─────────────────────────────────────────────────────────
(function () {
    var compareMonth = <?= json_encode($compareMonth) ?>;
    var compareYear  = <?= json_encode($compareYear) ?>;
    var compareDay   = <?= json_encode($compareDay) ?>;
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var mode = 'month';

    var btnActive   = 'px-3 py-1.5 text-sm font-medium rounded-lg transition-colors bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm';
    var btnInactive = 'px-3 py-1.5 text-sm font-medium rounded-lg transition-colors text-gray-500 dark:text-gray-400 hover:text-gray-700';

    window.switchCompare = function(m) {
        mode = m;
        document.getElementById('btn-day').className   = m === 'day'   ? btnActive : btnInactive;
        document.getElementById('btn-month').className = m === 'month' ? btnActive : btnInactive;
        document.getElementById('btn-year').className  = m === 'year'  ? btnActive : btnInactive;
        renderCompare();
    };

    function renderCompare() {
        var canvas = document.getElementById('chart-compare');
        if (!canvas) return;
        var isDark = document.documentElement.classList.contains('dark');
        var W = canvas.offsetWidth  || canvas.parentElement.offsetWidth || 600;
        var H = canvas.offsetHeight || 240;
        var dpr = window.devicePixelRatio || 1;
        canvas.width  = W * dpr;
        canvas.height = H * dpr;
        canvas.style.width  = W + 'px';
        canvas.style.height = H + 'px';
        var ctx = canvas.getContext('2d');
        ctx.scale(dpr, dpr);
        ctx.clearRect(0, 0, W, H);

        var padL = 60, padR = 20, padT = 16, padB = 36;
        var chartW = W - padL - padR;
        var chartH = H - padT - padB;

        if (mode === 'day') {
            document.getElementById('compare-title').textContent = '<?= __('compare_day_title') ?>';
            document.getElementById('compare-subtitle').textContent = compareDay.month_label;
            document.getElementById('leg-rev-a').textContent = 'Revenue';
            document.getElementById('leg-rev-b').textContent = '';
            document.getElementById('leg-exp-a').textContent = 'Expenses';
            document.getElementById('leg-exp-b').textContent = '';

            drawGrouped(ctx, W, H, chartW, chartH, padL, padR, padT, padB,
                compareDay.labels,
                [
                    { data: compareDay.rev, color: '#458458' },
                    { data: compareDay.exp, color: '#f87171' },
                ],
                isDark);
        } else if (mode === 'month') {
            document.getElementById('compare-title').textContent = '<?= __('compare_month_title') ?>';
            document.getElementById('compare-subtitle').textContent = compareMonth.prev_label + ' vs ' + compareMonth.cur_label;
            document.getElementById('leg-rev-a').textContent = compareMonth.prev_label + ' Revenue';
            document.getElementById('leg-rev-b').textContent = compareMonth.cur_label  + ' Revenue';
            document.getElementById('leg-exp-a').textContent = compareMonth.prev_label + ' Expenses';
            document.getElementById('leg-exp-b').textContent = compareMonth.cur_label  + ' Expenses';

            drawGrouped(ctx, W, H, chartW, chartH, padL, padR, padT, padB,
                [compareMonth.prev_label, compareMonth.cur_label],
                [
                    { data: [compareMonth.prev_rev, compareMonth.cur_rev], color: '#458458' },
                    { data: [compareMonth.prev_exp, compareMonth.cur_exp], color: '#f87171' },
                ],
                isDark);
        } else {
            document.getElementById('compare-title').textContent = '<?= __('compare_year_title') ?>';
            document.getElementById('compare-subtitle').textContent = compareYear.last_year + ' vs ' + compareYear.this_year;
            document.getElementById('leg-rev-a').textContent = compareYear.last_year + ' Revenue';
            document.getElementById('leg-rev-b').textContent = compareYear.this_year + ' Revenue';
            document.getElementById('leg-exp-a').textContent = compareYear.last_year + ' Expenses';
            document.getElementById('leg-exp-b').textContent = compareYear.this_year + ' Expenses';

            drawGrouped(ctx, W, H, chartW, chartH, padL, padR, padT, padB,
                months,
                [
                    { data: compareYear.rev_last, color: '#7fc49e' },
                    { data: compareYear.rev_this, color: '#458458' },
                    { data: compareYear.exp_last, color: '#fca5a5' },
                    { data: compareYear.exp_this, color: '#f87171' },
                ],
                isDark);
        }
    }

    function drawGrouped(ctx, W, H, chartW, chartH, padL, padR, padT, padB, labels, datasets, isDark) {
        var gridColor  = isDark ? '#374151' : '#e5e7eb';
        var textColor  = isDark ? '#9ca3af' : '#6b7280';
        var n = labels.length;
        var ds = datasets.length;

        var maxVal = 0;
        datasets.forEach(function(d) { d.data.forEach(function(v){ if(v > maxVal) maxVal = v; }); });
        if (maxVal === 0) maxVal = 1;
        maxVal = maxVal * 1.15;

        var gridSteps = 4;
        ctx.font = '10px Inter, ui-sans-serif, sans-serif';
        ctx.fillStyle = textColor;
        ctx.textAlign = 'right';
        for (var i = 0; i <= gridSteps; i++) {
            var val = (maxVal / gridSteps) * i;
            var y   = padT + chartH - (val / maxVal) * chartH;
            ctx.strokeStyle = gridColor;
            ctx.lineWidth = 0.5;
            ctx.beginPath(); ctx.moveTo(padL, y); ctx.lineTo(padL + chartW, y); ctx.stroke();
            ctx.fillText(fmtK(val), padL - 4, y + 3);
        }

        var groupW = chartW / n;
        var barGap = 3;
        var barW   = Math.max(4, (groupW - barGap * (ds + 1)) / ds);

        labels.forEach(function(lbl, gi) {
            var groupX = padL + gi * groupW + barGap;
            datasets.forEach(function(d, di) {
                var val  = d.data[gi] || 0;
                var bh   = (val / maxVal) * chartH;
                var bx   = groupX + di * (barW + barGap);
                var by   = padT + chartH - bh;
                var r    = Math.min(3, barW / 2);
                ctx.fillStyle = d.color;
                ctx.beginPath();
                ctx.moveTo(bx + r, by);
                ctx.lineTo(bx + barW - r, by);
                ctx.quadraticCurveTo(bx + barW, by, bx + barW, by + r);
                ctx.lineTo(bx + barW, padT + chartH);
                ctx.lineTo(bx, padT + chartH);
                ctx.lineTo(bx, by + r);
                ctx.quadraticCurveTo(bx, by, bx + r, by);
                ctx.closePath();
                ctx.fill();
            });
            ctx.fillStyle = textColor;
            ctx.textAlign = 'center';
            ctx.fillText(lbl, padL + gi * groupW + groupW / 2, padT + chartH + 16);
        });
    }

    function fmtK(v) {
        if (v >= 1000000) return 'RM ' + (v/1000000).toFixed(1) + 'M';
        if (v >= 1000)    return 'RM ' + (v/1000).toFixed(1) + 'k';
        return v > 0 ? 'RM ' + v.toFixed(0) : '0';
    }

    document.addEventListener('DOMContentLoaded', renderCompare);
    window.addEventListener('resize', renderCompare);
})();
</script>
