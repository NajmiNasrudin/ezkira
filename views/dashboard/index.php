<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
<style>
.flatpickr-calendar { font-family: "Plus Jakarta Sans", Inter, ui-sans-serif, sans-serif; border-radius: 18px; box-shadow: 0 16px 40px -12px rgba(22,48,32,0.25); border: 1px solid #e2e1d8; }
.flatpickr-day.selected, .flatpickr-day.selected:hover { background: #1a4a2e; border-color: #1a4a2e; }
.flatpickr-day:hover { background: #eaf2ee; }
.flatpickr-months .flatpickr-month, .flatpickr-weekdays { background: #163020; border-radius: 18px 18px 0 0; }
.flatpickr-current-month .flatpickr-monthDropdown-months, .flatpickr-current-month input.cur-year { color: #fff; }
.flatpickr-weekday { color: rgba(255,255,255,0.8) !important; }
.flatpickr-prev-month svg, .flatpickr-next-month svg { fill: #fff !important; }
.flatpickr-day.week-highlight { background: #eaf2ee; }
.flatpickr-monthSelect-month { border-radius: 10px !important; }
.flatpickr-monthSelect-month.selected { background: #1a4a2e !important; border-color: #1a4a2e !important; }
</style>
<?php
$roleLabelMap = [
    'admin'  => __('role_admin'),
    'team'   => __('role_team'),
    'client' => __('role_client'),
];
$role      = $user['role'] ?? 'client';
$roleLabel = $roleLabelMap[$role] ?? 'Client';

$platformLabels = \Models\Revenue::PLATFORMS;
$categoryLabels = ['opex' => 'OPEX', 'marketing' => 'Marketing', 'cogs' => 'COGS'];

$hour = (int) (new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur')))->format('G');
$greetingKey = $hour < 12 ? 'good_morning' : ($hour < 19 ? 'good_afternoon' : 'good_evening');
$firstName   = explode(' ', trim($user['name'] ?? ''))[0];

$netProfit   = (float) $summary['net_profit'];
$netParts    = explode('.', number_format(abs($netProfit), 2));
$revTotal    = (float) $summary['total_revenue'];
$marginPct   = $revTotal > 0 ? ($netProfit / $revTotal) * 100 : null;

$periods = [
    'daily'   => __('period_daily'),
    'weekly'  => __('period_weekly'),
    'monthly' => __('period_monthly'),
    'annual'  => __('period_annual'),
];

$quickActions = [
    ['href' => BASE_URI . '/revenue#add-sale',    'label' => __('add_sale'),     'icon' => 'M12 4v16m8-8H4'],
    ['href' => BASE_URI . '/expenses#add-expense', 'label' => __('add_expense'), 'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
    ['onclick' => 'openExportModal()',             'label' => 'Export','icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4'],
    ['href' => BASE_URI . '/balance-sheet',        'label' => __('balance_sheet'), 'icon' => 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3'],
];

$pickerClass = 'pl-10 pr-4 h-10 text-sm font-medium rounded-full border border-gray-200 dark:border-gray-700
                bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm
                focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-transparent
                transition-colors cursor-pointer w-44';
$calendarIcon = '<svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-brand-600 dark:text-sage-300 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>';
?>

<!-- Greeting -->
<div class="flex items-start justify-between gap-4 mb-5">
    <div class="min-w-0">
        <h1 class="text-2xl sm:text-[1.75rem] font-extrabold tracking-tight text-gray-900 dark:text-white truncate">
            <?= __($greetingKey) ?>, <?= htmlspecialchars($firstName, ENT_QUOTES) ?>
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            <?= __('greeting_sub') ?>
            <span class="hidden sm:inline">· <?= date('l, j F Y') ?></span>
        </p>
    </div>
    <span class="shrink-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-sage-100 text-brand-700 dark:bg-brand-900/60 dark:text-sage-300"
          title="<?= __('your_role') ?>">
        <?= htmlspecialchars($roleLabel, ENT_QUOTES) ?>
    </span>
</div>

<!-- Period Filter + Export -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
    <div class="flex flex-wrap items-center gap-2">
        <div class="grid grid-cols-4 sm:flex items-center bg-white dark:bg-gray-800 rounded-full p-1 gap-0.5 shadow-sm w-full sm:w-auto">
            <?php foreach ($periods as $key => $label):
                $active = $period === $key;
                $href   = BASE_URI . '/dashboard?period=' . $key . '&year=' . $year . '&month=' . $month . '&week=' . $week . '&date=' . $date;
            ?>
            <a href="<?= $href ?>"
               class="text-center px-4 py-2 text-sm rounded-full transition-colors <?= $active
                   ? 'bg-brand-700 text-white dark:bg-sage-300 dark:text-brand-800 font-semibold shadow-sm'
                   : 'text-gray-500 dark:text-gray-400 hover:text-brand-700 dark:hover:text-sage-300 font-medium' ?>">
                <?= $label ?>
            </a>
            <?php endforeach; ?>
        </div>

        <?php if ($period === 'daily'): ?>
        <div class="relative">
            <input type="text" id="picker-daily"
                   value="<?= date('d M Y', strtotime($date)) ?>"
                   data-date="<?= htmlspecialchars($date, ENT_QUOTES) ?>"
                   readonly placeholder="<?= htmlspecialchars(__('dash_pick_date'), ENT_QUOTES) ?>" class="<?= $pickerClass ?>">
            <?= $calendarIcon ?>
        </div>

        <?php elseif ($period === 'weekly'):
            $weekStartDate = date('Y-m-d', strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
        ?>
        <div class="relative">
            <input type="text" id="picker-weekly"
                   value="<?= htmlspecialchars(__('dash_week'), ENT_QUOTES) ?> <?= $week ?>, <?= $year ?>"
                   data-date="<?= $weekStartDate ?>"
                   readonly placeholder="<?= htmlspecialchars(__('dash_pick_week'), ENT_QUOTES) ?>" class="<?= $pickerClass ?>">
            <?= $calendarIcon ?>
        </div>

        <?php elseif ($period === 'monthly'): ?>
        <div class="relative">
            <input type="text" id="picker-monthly"
                   value="<?= date('F Y', mktime(0,0,0,$month,1,$year)) ?>"
                   data-date="<?= $year ?>-<?= str_pad($month, 2, '0', STR_PAD_LEFT) ?>-01"
                   readonly placeholder="<?= htmlspecialchars(__('dash_pick_month'), ENT_QUOTES) ?>" class="<?= $pickerClass ?>">
            <?= $calendarIcon ?>
        </div>

        <?php elseif ($period === 'annual'): ?>
        <select id="picker-annual"
                class="px-4 h-10 text-sm font-medium rounded-full border border-gray-200 dark:border-gray-700
                       bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm
                       focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-transparent
                       transition-colors cursor-pointer">
            <?php for ($y = (int)date('Y'); $y >= 2020; $y--): ?>
            <option value="<?= $y ?>" <?= $y === $year ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
        <?php endif; ?>
    </div>

    <button type="button" onclick="openExportModal()"
            class="hidden sm:inline-flex items-center justify-center gap-2 h-10 px-5 text-sm font-semibold text-white bg-brand-700 hover:bg-brand-600 dark:bg-sage-300 dark:text-brand-800 dark:hover:bg-sage-200 rounded-full transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        <?= __('export_report') ?>
    </button>
</div>

<!-- ===== EXPORT LAPORAN MODAL ===== -->
<div id="dash-export-modal" class="hidden fixed inset-0 z-[60] flex items-end sm:items-center justify-center sm:px-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeExportModal()"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-t-3xl sm:rounded-3xl shadow-2xl w-full sm:max-w-sm max-h-[90vh] overflow-y-auto">

        <div class="flex items-center justify-between px-6 pt-6 pb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white"><?= htmlspecialchars(__('dash_export_title'), ENT_QUOTES) ?></h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?= htmlspecialchars(__('dash_export_sub'), ENT_QUOTES) ?></p>
            </div>
            <button type="button" onclick="closeExportModal()"
                    class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 hover:text-gray-700 dark:text-gray-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="px-6 pb-5 space-y-4">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2"><?= htmlspecialchars(__('dash_export_period'), ENT_QUOTES) ?></p>
                <div class="grid grid-cols-5 gap-0.5 bg-gray-100 dark:bg-gray-700 rounded-full p-1">
                    <?php foreach (['daily'=>__('period_daily'),'weekly'=>__('period_weekly'),'monthly'=>__('period_monthly'),'annual'=>__('period_annual'),'range'=>__('dash_period_custom')] as $pk=>$pl): ?>
                    <button type="button" id="epbtn-<?= $pk ?>" onclick="switchExportPeriod('<?= $pk ?>')"
                            class="<?= $pk==='monthly'
                                ? 'py-1.5 text-[10px] sm:text-xs font-semibold rounded-full transition-colors leading-tight bg-brand-700 text-white dark:bg-sage-300 dark:text-brand-800 shadow-sm'
                                : 'py-1.5 text-[10px] sm:text-xs font-medium rounded-full transition-colors leading-tight text-gray-500 dark:text-gray-400 hover:text-brand-700 dark:hover:text-sage-300' ?>">
                        <?= $pl ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div id="ep-daily" class="hidden">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5"><?= htmlspecialchars(__('dash_pick_date'), ENT_QUOTES) ?></label>
                <input type="date" id="ep-daily-date" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>"
                       class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-400">
            </div>
            <div id="ep-weekly" class="hidden">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5"><?= htmlspecialchars(__('dash_pick_week'), ENT_QUOTES) ?></label>
                <input type="week" id="ep-weekly-date" value="<?= date('Y') ?>-W<?= str_pad(date('W'), 2, '0', STR_PAD_LEFT) ?>"
                       max="<?= date('Y') ?>-W<?= str_pad(date('W'), 2, '0', STR_PAD_LEFT) ?>"
                       class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-400">
            </div>
            <div id="ep-monthly">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5"><?= htmlspecialchars(__('dash_pick_month'), ENT_QUOTES) ?></label>
                <input type="month" id="ep-month-input"
                       value="<?= $year ?>-<?= str_pad($month, 2, '0', STR_PAD_LEFT) ?>"
                       max="<?= date('Y-m') ?>"
                       class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-400">
            </div>
            <div id="ep-annual" class="hidden">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5"><?= htmlspecialchars(__('dash_pick_year'), ENT_QUOTES) ?></label>
                <select id="ep-year-annual" class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-400">
                    <?php for ($y=(int)date('Y'); $y>=2020; $y--): ?>
                    <option value="<?= $y ?>" <?= $y===$year ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div id="ep-range" class="hidden space-y-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5"><?= htmlspecialchars(__('dash_date_from'), ENT_QUOTES) ?></label>
                    <input type="date" id="ep-range-from" value="<?= date('Y-m-01') ?>" max="<?= date('Y-m-d') ?>"
                           class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5"><?= htmlspecialchars(__('dash_date_to'), ENT_QUOTES) ?></label>
                    <input type="date" id="ep-range-to" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>"
                           class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
                <p class="text-xs text-amber-600 dark:text-amber-400">⚠ <?= htmlspecialchars(__('dash_range_note'), ENT_QUOTES) ?></p>
            </div>
        </div>

        <div class="px-6 pb-6 flex flex-col gap-2">
            <button type="button" onclick="doViewDashboard()"
                    class="w-full flex items-center justify-center gap-2 py-3 px-4 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-semibold rounded-full transition-colors text-sm hover:bg-gray-200 dark:hover:bg-gray-600">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <?= htmlspecialchars(__('dash_view_first'), ENT_QUOTES) ?>
            </button>
            <button type="button" onclick="doExportPnl()"
                    class="w-full flex items-center justify-center gap-2 py-3 px-4 bg-brand-700 hover:bg-brand-600 dark:bg-sage-300 dark:text-brand-800 dark:hover:bg-sage-200 text-white font-semibold rounded-full transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <?= htmlspecialchars(__('dash_export_csv'), ENT_QUOTES) ?>
            </button>
        </div>
    </div>
</div>
<!-- ===== END EXPORT LAPORAN MODAL ===== -->

<script>
var EZ_T = <?= json_encode([
    'week'            => __('dash_week'),
    'alertDate'       => __('dash_alert_date'),
    'alertWeek'       => __('dash_alert_week'),
    'alertMonth'      => __('dash_alert_month'),
    'alertRangeView'  => __('dash_alert_range_view'),
    'alertBothDates'  => __('dash_alert_both_dates'),
    'alertDateOrder'  => __('dash_alert_date_order'),
    'noData'          => __('dash_chart_no_data'),
    'totalSpent'      => __('dash_chart_total_spent'),
    'expensesUsed'    => __('dash_chart_expenses_used'),
    'addRevenueFirst' => __('dash_chart_add_revenue'),
    'nothingCompare'  => __('dash_chart_nothing_compare'),
    'revenue'         => __('dash_legend_revenue'),
    'expenses'        => __('dash_legend_expenses'),
], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
var _epMode = 'monthly';
var _epBtnA = 'py-1.5 text-[10px] sm:text-xs font-semibold rounded-full transition-colors leading-tight bg-brand-700 text-white dark:bg-sage-300 dark:text-brand-800 shadow-sm';
var _epBtnI = 'py-1.5 text-[10px] sm:text-xs font-medium rounded-full transition-colors leading-tight text-gray-500 dark:text-gray-400 hover:text-brand-700 dark:hover:text-sage-300';

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
        if (!d) { alert(EZ_T.alertDate); return null; }
        if (forView) return base + '/dashboard?period=daily&date=' + d;
        return base + '/revenue/export-pnl?period=daily&date=' + d + '&year=' + d.substring(0,4) + '&month=' + parseInt(d.substring(5,7)) + '&week=1';
    }
    if (_epMode === 'weekly') {
        var w = document.getElementById('ep-weekly-date').value; // "YYYY-Www"
        if (!w) { alert(EZ_T.alertWeek); return null; }
        var yr = w.substring(0,4);
        var wk = parseInt(w.substring(6));
        if (forView) return base + '/dashboard?period=weekly&year=' + yr + '&week=' + wk;
        return base + '/revenue/export-pnl?period=weekly&year=' + yr + '&week=' + wk;
    }
    if (_epMode === 'monthly') {
        var mv = document.getElementById('ep-month-input').value; // "YYYY-MM"
        if (!mv) { alert(EZ_T.alertMonth); return null; }
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
        if (forView) { alert(EZ_T.alertRangeView); return null; }
        var from = document.getElementById('ep-range-from').value;
        var to   = document.getElementById('ep-range-to').value;
        if (!from || !to) { alert(EZ_T.alertBothDates); return null; }
        if (from > to) { alert(EZ_T.alertDateOrder); return null; }
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
            dateFormat: 'd M Y',
            maxDate: 'today',
            disableMobile: true,
            defaultDate: dailyEl.getAttribute('data-date'),
            onChange: function(selectedDates) {
                if (!selectedDates.length) return;
                var d = selectedDates[0];
                var y   = d.getFullYear();
                var mo  = String(d.getMonth() + 1).padStart(2, '0');
                var day = String(d.getDate()).padStart(2, '0');
                window.location.href = base + '?period=daily&date=' + y + '-' + mo + '-' + day;
            }
        });
    }

    // Weekly
    var weeklyEl = document.getElementById('picker-weekly');
    if (weeklyEl) {
        flatpickr(weeklyEl, {
            defaultDate: weeklyEl.getAttribute('data-date'),
            weekNumbers: true,
            disableMobile: true,
            maxDate: 'today',
            onReady: function(selectedDates) {
                if (selectedDates.length) {
                    var d = selectedDates[0];
                    weeklyEl.value = EZ_T.week + ' ' + getISOWeek(d) + ', ' + d.getFullYear();
                }
            },
            onChange: function(selectedDates) {
                if (!selectedDates.length) return;
                var d = selectedDates[0];
                var yr = d.getFullYear();
                var wk = getISOWeek(d);
                weeklyEl.value = EZ_T.week + ' ' + wk + ', ' + yr;
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
            disableMobile: true,
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

<!-- Hero: Net profit + quick actions -->
<div class="grid grid-cols-1 lg:grid-cols-5 gap-5 mb-5">

    <!-- Net profit card -->
    <div class="lg:col-span-3 relative overflow-hidden rounded-3xl bg-brand-700 text-white p-6 sm:p-7 shadow-soft">
        <div class="pointer-events-none absolute -right-16 -top-16 w-56 h-56 rounded-full bg-white/5"></div>
        <div class="pointer-events-none absolute -right-4 -bottom-24 w-56 h-56 rounded-full bg-sage-300/10"></div>

        <div class="relative flex items-start justify-between gap-3">
            <a href="<?= BASE_URI ?>/revenue" class="inline-flex items-center gap-2 text-sm font-medium text-white/80 hover:text-white">
                <span class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 6v1m0 6v-1m-6-4h12"/></svg>
                </span>
                <?= __('net_profit') ?> · <span class="capitalize"><?= $periods[$period] ?? '' ?></span>
            </a>
            <svg class="w-6 h-6 text-sage-300 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <rect x="2" y="11" width="3.5" height="7" rx="1.2"/>
                <rect x="8.25" y="7" width="3.5" height="11" rx="1.2"/>
                <rect x="14.5" y="2.5" width="3.5" height="15.5" rx="1.2"/>
            </svg>
        </div>

        <div class="relative mt-4 flex flex-wrap items-end gap-x-4 gap-y-2">
            <p class="ez-num font-extrabold tracking-tight leading-none">
                <span class="text-xl sm:text-2xl align-top mr-0.5 text-white/80"><?= $netProfit < 0 ? '−' : '' ?><?= __('currency') ?></span><span class="text-4xl sm:text-5xl"><?= $netParts[0] ?></span><span class="text-2xl sm:text-3xl text-white/70">.<?= $netParts[1] ?></span>
            </p>
            <?php if ($marginPct !== null): ?>
            <span class="mb-1 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold <?= $marginPct >= 0 ? 'bg-sage-300/20 text-sage-200' : 'bg-red-400/20 text-red-200' ?>">
                <?= $marginPct >= 0 ? '▲' : '▼' ?> <?= number_format(abs($marginPct), 1) ?>% <?= __('margin') ?>
            </span>
            <?php endif; ?>
        </div>

        <div class="relative mt-6 grid grid-cols-2 sm:grid-cols-3 gap-2 sm:gap-3">
            <a href="<?= BASE_URI ?>/revenue" class="rounded-2xl bg-white/10 hover:bg-white/15 transition-colors p-3 sm:p-4">
                <p class="flex items-center gap-1.5 text-[11px] sm:text-xs text-white/70 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span><?= __('total_revenue') ?>
                </p>
                <p class="ez-num mt-1 text-base sm:text-lg font-bold truncate"><?= number_format($summary['total_revenue'], 2) ?></p>
            </a>
            <a href="<?= BASE_URI ?>/expenses" class="rounded-2xl bg-white/10 hover:bg-white/15 transition-colors p-3 sm:p-4">
                <p class="flex items-center gap-1.5 text-[11px] sm:text-xs text-white/70 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-300"></span><?= __('total_expenses') ?>
                </p>
                <p class="ez-num mt-1 text-base sm:text-lg font-bold truncate"><?= number_format($summary['total_expenses'], 2) ?></p>
            </a>
            <a href="<?= BASE_URI ?>/revenue" class="col-span-2 sm:col-span-1 flex sm:block items-center justify-between rounded-2xl bg-white/10 hover:bg-white/15 transition-colors px-3 py-2.5 sm:p-4">
                <p class="flex items-center gap-1.5 text-[11px] sm:text-xs text-white/70 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-gold-300"></span><?= __('dash_sales_all_time') ?>
                </p>
                <p class="ez-num sm:mt-1 text-sm sm:text-lg font-bold truncate"><?= number_format($summary['transactions']) ?></p>
            </a>
        </div>
    </div>

    <!-- Quick actions -->
    <div class="lg:col-span-2 rounded-3xl bg-white dark:bg-gray-800 p-5 sm:p-6 shadow-sm flex flex-col">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4"><?= __('quick_actions') ?></h2>
        <div class="grid grid-cols-4 gap-2 sm:gap-3 my-auto">
            <?php foreach ($quickActions as $qa):
                $tag   = isset($qa['href']) ? 'a' : 'button';
                $attrs = isset($qa['href'])
                    ? 'href="' . $qa['href'] . '"'
                    : 'type="button" onclick="' . $qa['onclick'] . '"';
            ?>
            <<?= $tag ?> <?= $attrs ?> class="group flex flex-col items-center gap-2 text-center">
                <span class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gray-50 dark:bg-gray-700/60 border border-gray-100 dark:border-gray-700 flex items-center justify-center text-brand-700 dark:text-sage-300 group-hover:bg-sage-100 dark:group-hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="<?= $qa['icon'] ?>"/>
                    </svg>
                </span>
                <span class="text-[11px] sm:text-xs font-semibold text-gray-700 dark:text-gray-300 leading-tight"><?= htmlspecialchars($qa['label'], ENT_QUOTES) ?></span>
            </<?= $tag ?>>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
$isNewUser     = (int) $summary['transactions'] === 0 && (float) $summary['total_expenses'] <= 0 && empty($transactions);
$isEmptyPeriod = !$isNewUser && (float) $summary['total_revenue'] <= 0 && (float) $summary['total_expenses'] <= 0;
?>
<?php if ($isNewUser || $isEmptyPeriod): ?>
<div class="mb-5 rounded-3xl bg-sage-100 dark:bg-gray-800 border border-sage-200 dark:border-gray-700 p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center gap-4">
    <span class="w-12 h-12 shrink-0 rounded-2xl bg-white dark:bg-gray-700 text-brand-600 dark:text-sage-300 flex items-center justify-center">
        <?php if ($isNewUser): ?>
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/></svg>
        <?php else: ?>
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <?php endif; ?>
    </span>
    <div class="flex-1 min-w-0">
        <p class="text-base font-bold text-gray-900 dark:text-white"><?= __($isNewUser ? 'dash_welcome_title' : 'dash_empty_title') ?></p>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5"><?= __($isNewUser ? 'dash_welcome_sub' : 'dash_empty_sub') ?></p>
    </div>
    <div class="flex flex-col sm:flex-row gap-2 shrink-0">
        <a href="<?= BASE_URI ?>/revenue#add-sale"
           class="inline-flex items-center justify-center gap-1.5 h-10 px-4 whitespace-nowrap rounded-full bg-brand-700 hover:bg-brand-600 dark:bg-sage-300 dark:text-brand-800 text-white text-sm font-semibold transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            <?= __('add_sale') ?>
        </a>
        <a href="<?= BASE_URI ?>/expenses#add-expense"
           class="inline-flex items-center justify-center h-10 px-4 whitespace-nowrap rounded-full bg-white dark:bg-gray-700 text-brand-700 dark:text-gray-100 text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
            <?= __('add_expense') ?>
        </a>
    </div>
</div>
<?php endif; ?>

<!-- Charts + Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

    <!-- Expenses overview -->
    <div class="lg:col-span-3 bg-white dark:bg-gray-800 rounded-3xl p-5 sm:p-6 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                <?= __('expenses_overview') ?>
            </h2>
            <span class="px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-xs font-semibold text-gray-600 dark:text-gray-300 capitalize"><?= $periods[$period] ?? '' ?></span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            <!-- Chart 1: Expenses Composition -->
            <div class="flex flex-col items-center">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">
                    <?= __('expenses_composition') ?>
                </p>
                <canvas id="chart-breakdown" width="180" height="180" class="max-w-full"></canvas>
                <div class="mt-5 space-y-2 w-full max-w-[230px]">
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-brand-600 dark:bg-sage-200 shrink-0"></span>
                            <span class="text-gray-600 dark:text-gray-400">OPEX</span>
                        </span>
                        <span class="ez-num font-bold text-gray-800 dark:text-gray-200">RM <?= number_format($chartData['opex'], 2) ?></span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-brand-300 dark:bg-sage-400 shrink-0"></span>
                            <span class="text-gray-600 dark:text-gray-400">Marketing</span>
                        </span>
                        <span class="ez-num font-bold text-gray-800 dark:text-gray-200">RM <?= number_format($chartData['marketing'], 2) ?></span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-sage-300 dark:bg-brand-400 shrink-0"></span>
                            <span class="text-gray-600 dark:text-gray-400">COGS</span>
                        </span>
                        <span class="ez-num font-bold text-gray-800 dark:text-gray-200">RM <?= number_format($chartData['cogs'], 2) ?></span>
                    </div>
                    <?php if ($targetRevenue > 0 && !$chartData['overBudget']): ?>
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-gray-200 dark:bg-gray-600 shrink-0"></span>
                            <span class="text-gray-600 dark:text-gray-400"><?= __('remaining') ?></span>
                        </span>
                        <span class="ez-num font-bold text-gray-800 dark:text-gray-200">
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
                <div class="mt-5 space-y-2 w-full max-w-[230px]">
                    <?php
                    $rev    = $chartData['revenue'];
                    $expPct = $rev > 0 ? min(100, ($chartData['total'] / $rev) * 100) : 0;
                    $proPct = $rev > 0 ? max(0, 100 - $expPct) : 0;
                    ?>
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:<?= $chartData['overBudget'] ? '#dc2626' : '#e8806f' ?>"></span>
                            <span class="text-gray-600 dark:text-gray-400"><?= __('total_expenses') ?></span>
                        </span>
                        <span class="ez-num font-bold text-gray-800 dark:text-gray-200"><?= number_format($expPct, 1) ?>%</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-brand-400 dark:bg-sage-400 shrink-0"></span>
                            <span class="text-gray-600 dark:text-gray-400"><?= __('net_profit') ?></span>
                        </span>
                        <span class="ez-num font-bold <?= $chartData['overBudget'] ? 'text-red-500' : 'text-brand-500 dark:text-sage-300' ?>">
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

        <?php if ($rev > 0): ?>
        <div class="mt-6 flex items-center gap-3 p-4 rounded-2xl <?= $chartData['overBudget'] ? 'bg-red-50 dark:bg-red-900/20' : 'bg-sage-50 dark:bg-gray-700/50' ?>">
            <span class="w-10 h-10 shrink-0 rounded-full flex items-center justify-center <?= $chartData['overBudget'] ? 'bg-red-100 text-red-500 dark:bg-red-900/40' : 'bg-white text-brand-500 dark:bg-gray-800 dark:text-sage-300' ?>">
                <?php if ($chartData['overBudget']): ?>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                <?php else: ?>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/></svg>
                <?php endif; ?>
            </span>
            <div class="min-w-0">
                <p class="text-sm font-bold <?= $chartData['overBudget'] ? 'text-red-700 dark:text-red-300' : 'text-gray-900 dark:text-white' ?>">
                    <?= $chartData['overBudget'] ? __('over_budget_msg') : __('expenses_used_of_revenue', ['pct' => number_format($expPct, 1)]) ?>
                </p>
                <?php if (!$chartData['overBudget'] && $expPct < 80): ?>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?= __('healthy_budget_msg') ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Recent Transactions -->
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-3xl p-5 sm:p-6 shadow-sm flex flex-col">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white"><?= __('recent_transactions') ?></h2>
            <div class="flex gap-2">
                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-gray-500 dark:text-gray-400"><span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span><?= __('money_in') ?></span>
                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-gray-500 dark:text-gray-400"><span class="w-2 h-2 rounded-full inline-block" style="background:#e8806f"></span><?= __('money_out') ?></span>
            </div>
        </div>

        <?php if (empty($transactions)): ?>
            <div class="flex flex-col items-center justify-center flex-1 h-40 text-center">
                <span class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-2">
                    <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </span>
                <p class="text-sm text-gray-400 dark:text-gray-500"><?= __('no_activity') ?></p>
            </div>
        <?php else: ?>
            <div class="overflow-y-auto max-h-[22rem] -mx-2 px-2 divide-y divide-gray-100 dark:divide-gray-700/60">
                <?php foreach ($transactions as $txn):
                    $isRevenue = $txn['type'] === 'revenue';
                    $sign      = $isRevenue ? '+' : '−';
                    $label     = $isRevenue
                        ? ($platformLabels[$txn['category']] ?? ucfirst($txn['category']))
                        : ($categoryLabels[$txn['category']] ?? ucfirst($txn['category']));
                    $desc      = !empty($txn['description']) ? $txn['description'] : '—';
                    $txnDate   = date('j M', strtotime($txn['txn_date']));
                ?>
                <div class="flex items-center gap-3 py-3">
                    <span class="w-10 h-10 shrink-0 rounded-full flex items-center justify-center <?= $isRevenue
                        ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400'
                        : 'bg-red-50 text-red-500 dark:bg-red-900/30 dark:text-red-400' ?>">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="<?= $isRevenue ? 'M7 17L17 7M17 7H9m8 0v8' : 'M17 7L7 17M7 17h8m-8 0V9' ?>"/>
                        </svg>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate">
                            <?= htmlspecialchars($desc, ENT_QUOTES) ?>
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 truncate"><?= $label ?></p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="ez-num text-sm font-bold whitespace-nowrap <?= $isRevenue ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-gray-100' ?>">
                            <?= $sign ?> RM <?= number_format((float)$txn['amount'], 2) ?>
                        </p>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500"><?= $txnDate ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="flex justify-between mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 text-xs font-semibold">
            <a href="<?= BASE_URI ?>/revenue" class="text-brand-600 dark:text-sage-300 hover:underline"><?= __('revenue') ?> →</a>
            <a href="<?= BASE_URI ?>/expenses" class="text-brand-600 dark:text-sage-300 hover:underline"><?= __('expenses') ?> →</a>
        </div>
    </div>
</div>

<!-- Comparison Chart — full width -->
<div class="mt-5 bg-white dark:bg-gray-800 rounded-3xl p-5 sm:p-6 shadow-sm">
    <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white" id="compare-title">
                <?= __('compare_month_title') ?>
            </h2>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5" id="compare-subtitle">
                <?= $compareMonth['prev_label'] ?> vs <?= $compareMonth['cur_label'] ?>
            </p>
        </div>
        <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-full p-1 gap-0.5">
            <button id="btn-day" onclick="switchCompare('day')"
                    class="px-4 py-1.5 text-sm font-medium rounded-full transition-colors text-gray-500 dark:text-gray-400 hover:text-brand-700 dark:hover:text-sage-300">
                <?= __('compare_day') ?>
            </button>
            <button id="btn-month" onclick="switchCompare('month')"
                    class="px-4 py-1.5 text-sm font-semibold rounded-full transition-colors bg-brand-700 text-white dark:bg-sage-300 dark:text-brand-800 shadow-sm">
                <?= __('compare_month') ?>
            </button>
            <button id="btn-year" onclick="switchCompare('year')"
                    class="px-4 py-1.5 text-sm font-medium rounded-full transition-colors text-gray-500 dark:text-gray-400 hover:text-brand-700 dark:hover:text-sage-300">
                <?= __('compare_year') ?>
            </button>
        </div>
    </div>
    <div class="relative" style="height:260px">
        <canvas id="chart-compare" style="width:100%;height:100%"></canvas>
    </div>
    <div class="flex flex-wrap items-center gap-4 mt-4 text-xs">
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full inline-block" style="background:#a8c3a8"></span><span class="text-gray-600 dark:text-gray-300" id="leg-rev-a"></span></span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full inline-block" style="background:#3a7a58"></span><span class="text-gray-600 dark:text-gray-300" id="leg-rev-b"></span></span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full inline-block" style="background:#f3c4b9"></span><span class="text-gray-600 dark:text-gray-300" id="leg-exp-a"></span></span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full inline-block" style="background:#e8806f"></span><span class="text-gray-600 dark:text-gray-300" id="leg-exp-b"></span></span>
    </div>
</div>

<script>
var EZ_CHART_FONT = '"Plus Jakarta Sans", Inter, ui-sans-serif, sans-serif';

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
        canvas.style.width  = size + 'px';
        canvas.style.height = size + 'px';
        ctx.scale(dpr, dpr);

        var cx = size / 2, cy = size / 2;
        var outerR = cx - 6;
        var innerR = outerR * 0.66;
        var isDark = document.documentElement.classList.contains('dark');
        var emptyColor = isDark ? '#353b35' : '#edece5';

        ctx.clearRect(0, 0, size, size);

        var total = segments.reduce(function (s, seg) { return s + seg.value; }, 0);
        var visible = segments.filter(function (seg) { return seg.value > 0; }).length;
        var gap = visible > 1 ? 0.045 : 0;

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
                var pad = sweep > gap * 2 ? gap / 2 : 0;
                var a0 = angle + pad, a1 = angle + sweep - pad;
                ctx.beginPath();
                ctx.moveTo(cx + outerR * Math.cos(a0), cy + outerR * Math.sin(a0));
                ctx.arc(cx, cy, outerR, a0, a1);
                ctx.arc(cx, cy, innerR, a1, a0, true);
                ctx.closePath();
                ctx.fillStyle = seg.color;
                ctx.fill();
                angle += sweep;
            });
        }

        var textColor  = isDark ? '#f5f4ef' : '#161c18';
        var subColor   = isDark ? '#9a9b92' : '#6d7068';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        if (centerLine1) {
            ctx.fillStyle = textColor;
            ctx.font = '800 15px ' + EZ_CHART_FONT;
            ctx.fillText(centerLine1, cx, centerLine2 ? cy - 9 : cy);
        }
        if (centerLine2) {
            ctx.fillStyle = subColor;
            ctx.font = '500 10.5px ' + EZ_CHART_FONT;
            ctx.fillText(centerLine2, cx, cy + 10);
        }
    }

    function renderCharts() {
        var total = chartData.total;
        var remaining = targetRevenue > 0 ? Math.max(0, targetRevenue - total) : 0;
        var isDark = document.documentElement.classList.contains('dark');
        var remainColor = isDark ? '#353b35' : '#e2e1d8';

        var breakdown = [
            { value: chartData.opex,      color: isDark ? '#cfdccf' : '#1a4a2e' },
            { value: chartData.marketing, color: isDark ? '#7fa584' : '#5f9d7d' },
            { value: chartData.cogs,      color: isDark ? '#3a7a58' : '#a8c3a8' },
        ];
        if (remaining > 0) breakdown.push({ value: remaining, color: remainColor });
        var totalLabel = total > 0 ? 'RM ' + total.toLocaleString('en-MY', {minimumFractionDigits:0, maximumFractionDigits:0}) : EZ_T.noData;
        drawDonut('chart-breakdown', breakdown, totalLabel, EZ_T.totalSpent);

        var rev = chartData.revenue || 0;
        var expPct    = rev > 0 ? Math.min(100, (total / rev) * 100) : 0;
        var profitPct = rev > 0 ? Math.max(0, 100 - expPct) : 0;
        var overBudget = chartData.overBudget;
        var health = [
            { value: expPct,    color: overBudget ? '#dc2626' : '#e8806f' },
            { value: profitPct, color: isDark ? '#7fa584' : '#3a7a58' },
        ];
        var healthLabel = rev > 0 ? expPct.toFixed(1) + '%' : EZ_T.noData;
        var healthSub   = rev > 0 ? EZ_T.expensesUsed : EZ_T.addRevenueFirst;
        drawDonut('chart-health', health, healthLabel, healthSub);
    }

    function renderAll() {
        renderCharts();
        if (window.renderCompareChart) window.renderCompareChart();
    }
    window.renderDashboardCharts = renderAll;
    document.addEventListener('DOMContentLoaded', renderAll);
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(renderAll);
    }
})();

// ── Comparison Chart ─────────────────────────────────────────────────────────
(function () {
    var compareMonth = <?= json_encode($compareMonth) ?>;
    var compareYear  = <?= json_encode($compareYear) ?>;
    var compareDay   = <?= json_encode($compareDay) ?>;
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var mode = 'month';

    var C_REV = '#3a7a58', C_REV_PREV = '#a8c3a8', C_EXP = '#e8806f', C_EXP_PREV = '#f3c4b9';

    var btnActive   = 'px-4 py-1.5 text-sm font-semibold rounded-full transition-colors bg-brand-700 text-white dark:bg-sage-300 dark:text-brand-800 shadow-sm';
    var btnInactive = 'px-4 py-1.5 text-sm font-medium rounded-full transition-colors text-gray-500 dark:text-gray-400 hover:text-brand-700 dark:hover:text-sage-300';

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
            document.getElementById('leg-rev-a').textContent = EZ_T.revenue;
            document.getElementById('leg-rev-b').textContent = '';
            document.getElementById('leg-exp-a').textContent = EZ_T.expenses;
            document.getElementById('leg-exp-b').textContent = '';

            drawGrouped(ctx, W, H, chartW, chartH, padL, padR, padT, padB,
                compareDay.labels,
                [
                    { data: compareDay.rev, color: C_REV },
                    { data: compareDay.exp, color: C_EXP },
                ],
                isDark);
        } else if (mode === 'month') {
            document.getElementById('compare-title').textContent = '<?= __('compare_month_title') ?>';
            document.getElementById('compare-subtitle').textContent = compareMonth.prev_label + ' vs ' + compareMonth.cur_label;
            document.getElementById('leg-rev-a').textContent = compareMonth.prev_label + ' ' + EZ_T.revenue;
            document.getElementById('leg-rev-b').textContent = compareMonth.cur_label  + ' ' + EZ_T.revenue;
            document.getElementById('leg-exp-a').textContent = compareMonth.prev_label + ' ' + EZ_T.expenses;
            document.getElementById('leg-exp-b').textContent = compareMonth.cur_label  + ' ' + EZ_T.expenses;

            drawGrouped(ctx, W, H, chartW, chartH, padL, padR, padT, padB,
                [compareMonth.prev_label, compareMonth.cur_label],
                [
                    { data: [compareMonth.prev_rev, compareMonth.cur_rev], color: C_REV },
                    { data: [compareMonth.prev_exp, compareMonth.cur_exp], color: C_EXP },
                ],
                isDark);
        } else {
            document.getElementById('compare-title').textContent = '<?= __('compare_year_title') ?>';
            document.getElementById('compare-subtitle').textContent = compareYear.last_year + ' vs ' + compareYear.this_year;
            document.getElementById('leg-rev-a').textContent = compareYear.last_year + ' ' + EZ_T.revenue;
            document.getElementById('leg-rev-b').textContent = compareYear.this_year + ' ' + EZ_T.revenue;
            document.getElementById('leg-exp-a').textContent = compareYear.last_year + ' ' + EZ_T.expenses;
            document.getElementById('leg-exp-b').textContent = compareYear.this_year + ' ' + EZ_T.expenses;

            drawGrouped(ctx, W, H, chartW, chartH, padL, padR, padT, padB,
                months,
                [
                    { data: compareYear.rev_last, color: C_REV_PREV },
                    { data: compareYear.rev_this, color: C_REV },
                    { data: compareYear.exp_last, color: C_EXP_PREV },
                    { data: compareYear.exp_this, color: C_EXP },
                ],
                isDark);
        }
    }

    function drawGrouped(ctx, W, H, chartW, chartH, padL, padR, padT, padB, labels, datasets, isDark) {
        var gridColor  = isDark ? '#353b35' : '#e2e1d8';
        var textColor  = isDark ? '#9a9b92' : '#6d7068';
        var n = labels.length;
        var ds = datasets.length;

        var dataMax = 0;
        datasets.forEach(function(d) { d.data.forEach(function(v){ if(v > dataMax) dataMax = v; }); });

        if (dataMax <= 0) {
            ctx.fillStyle = textColor;
            ctx.textAlign = 'center';
            ctx.font = '600 13px ' + EZ_CHART_FONT;
            ctx.fillText(EZ_T.nothingCompare, W / 2, padT + chartH / 2);
            return;
        }

        var gridSteps = 4;
        // Round the axis step to 1/2/2.5/5 x 10^n so tick labels are distinct and readable
        var rawStep = (dataMax * 1.1) / gridSteps;
        var mag     = Math.pow(10, Math.floor(Math.log10(rawStep)));
        var norm    = rawStep / mag;
        var step    = (norm <= 1 ? 1 : norm <= 2 ? 2 : norm <= 2.5 ? 2.5 : norm <= 5 ? 5 : 10) * mag;
        step = Math.max(step, 1);
        var maxVal = step * gridSteps;

        ctx.font = '500 10px ' + EZ_CHART_FONT;
        ctx.fillStyle = textColor;
        ctx.textAlign = 'right';
        ctx.setLineDash([3, 4]);
        for (var i = 0; i <= gridSteps; i++) {
            var val = (maxVal / gridSteps) * i;
            var y   = padT + chartH - (val / maxVal) * chartH;
            ctx.strokeStyle = gridColor;
            ctx.lineWidth = 1;
            ctx.beginPath(); ctx.moveTo(padL, y); ctx.lineTo(padL + chartW, y); ctx.stroke();
            ctx.fillText(fmtK(val), padL - 6, y + 3);
        }
        ctx.setLineDash([]);

        var groupW = chartW / n;
        var barGap = 3;
        var barW   = Math.max(4, Math.min(56, (groupW - barGap * (ds + 1)) / ds));
        var groupInner = ds * barW + (ds - 1) * barGap;

        labels.forEach(function(lbl, gi) {
            var groupX = padL + gi * groupW + (groupW - groupInner) / 2;
            datasets.forEach(function(d, di) {
                var val  = d.data[gi] || 0;
                var bh   = (val / maxVal) * chartH;
                var bx   = groupX + di * (barW + barGap);
                var by   = padT + chartH - bh;
                var r    = Math.min(8, barW / 2, bh);
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
            ctx.fillText(lbl, padL + gi * groupW + groupW / 2, padT + chartH + 18);
        });
    }

    function fmtK(v) {
        if (v >= 1000000) return 'RM ' + parseFloat((v/1000000).toFixed(2)) + 'M';
        if (v >= 1000)    return 'RM ' + parseFloat((v/1000).toFixed(2)) + 'k';
        return v > 0 ? 'RM ' + parseFloat(v.toFixed(2)) : '0';
    }

    window.renderCompareChart = renderCompare;
    window.addEventListener('resize', renderCompare);
})();
</script>
