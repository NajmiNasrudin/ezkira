<?php
/**
 * @var int    $year
 * @var int    $month
 * @var float  $target
 * @var float  $total
 * @var float  $pct
 * @var array  $entries
 * @var array  $platforms     [['platform'=>'shopee','total'=>X], ...]
 * @var array  $daily         [['sale_date'=>'...','total'=>X], ...]
 * @var array  $platforms_list
 */
?>
<!-- Flatpickr: month picker -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
<?php

$monthName = date('F Y', mktime(0, 0, 0, $month, 1, $year));
$prevMonth = $month === 1 ? 12 : $month - 1;
$prevYear  = $month === 1 ? $year - 1 : $year;
$nextMonth = $month === 12 ? 1 : $month + 1;
$nextYear  = $month === 12 ? $year + 1 : $year;
$isCurrentMonth = ($year === (int)date('Y') && $month === (int)date('n'));

$over  = $target > 0 && $total > $target;
$warn  = !$over && $target > 0 && $pct >= 80;
$barColor = $over ? 'bg-emerald-500' : ($warn ? 'bg-emerald-400' : 'bg-brand-500');

$platformColors = [
    'shopee'   => ['dot' => 'bg-orange-400', 'badge' => 'bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
    'lazada'   => ['dot' => 'bg-blue-500',   'badge' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
    'tiktok'   => ['dot' => 'bg-pink-500',   'badge' => 'bg-pink-50 text-pink-700 dark:bg-pink-900/30 dark:text-pink-300'],
    'website'  => ['dot' => 'bg-indigo-500', 'badge' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'],
    'walkin'   => ['dot' => 'bg-green-500',  'badge' => 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
    'whatsapp' => ['dot' => 'bg-teal-500',   'badge' => 'bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300'],
    'other'    => ['dot' => 'bg-gray-400',   'badge' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'],
];
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white"><?= __('revenue') ?></h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5"><?= __('revenue_subtitle') ?></p>
    </div>
    <div class="flex items-center gap-2">
        <a href="<?= BASE_URI ?>/revenue/export-pnl?period=monthly&year=<?= $year ?>&month=<?= $month ?>"
           class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 rounded-lg transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            <?= __('export_pnl') ?>
        </a>
        <button onclick="document.getElementById('target-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <?= __('set_target') ?>
        </button>
    </div>
</div>

<!-- Month Navigator -->
<div class="flex items-center justify-between mb-5">
    <!-- Prev arrow -->
    <a href="<?= BASE_URI ?>/revenue?year=<?= $prevYear ?>&month=<?= $prevMonth ?>"
       class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>

    <!-- Month/Year picker (Flatpickr) -->
    <div class="relative flex items-center gap-1.5 cursor-pointer" onclick="document.getElementById('rev-month-picker').click()">
        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <input id="rev-month-picker"
               type="text"
               readonly
               data-year="<?= $year ?>"
               data-month="<?= $month ?>"
               value="<?= $monthName ?>"
               class="text-lg font-semibold text-gray-900 dark:text-white bg-transparent border-none outline-none cursor-pointer
                      hover:text-brand-600 dark:hover:text-brand-400 transition-colors min-w-[130px] text-center">
        <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </div>

    <!-- Next arrow -->
    <a href="<?= BASE_URI ?>/revenue?year=<?= $nextYear ?>&month=<?= $nextMonth ?>"
       class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
</div>

<!-- Progress Card -->
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-4">
        <div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">
                <?= __('monthly_revenue') ?>
            </p>
            <p class="text-4xl font-bold text-gray-900 dark:text-white">
                RM <?= number_format($total, 2) ?>
            </p>
            <?php if ($target > 0): ?>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                <?= __('target') ?>: RM <?= number_format($target, 2) ?>
                <?php if ($over): ?>
                    <span class="ml-2 text-emerald-600 dark:text-emerald-400 font-medium">
                        +RM <?= number_format($total - $target, 2) ?> <?= __('over_target') ?>
                    </span>
                <?php endif; ?>
            </p>
            <?php else: ?>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1"><?= __('no_target_set') ?></p>
            <?php endif; ?>
        </div>
        <?php if ($target > 0): ?>
        <div class="text-right">
            <span class="text-3xl font-bold <?= $over ? 'text-emerald-600 dark:text-emerald-400' : 'text-brand-600 dark:text-brand-400' ?>">
                <?= number_format($pct, 1) ?>%
            </span>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5"><?= __('achieved') ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Progress Bar -->
    <?php if ($target > 0): ?>
    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-3 mb-1">
        <div class="<?= $barColor ?> h-3 rounded-full transition-all duration-700"
             style="width: <?= number_format($pct, 2) ?>%"></div>
    </div>
    <div class="flex justify-between text-xs text-gray-400 dark:text-gray-500">
        <span>RM 0</span>
        <span>RM <?= number_format($target, 2) ?></span>
    </div>
    <?php endif; ?>
</div>

<!-- Platform Breakdown -->
<?php if (!empty($platforms)): ?>
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-<?= min(count($platforms), 6) ?> gap-3 mb-6">
    <?php foreach ($platforms as $p):
        $key   = $p['platform'];
        $label = $platforms_list[$key] ?? $key;
        $clr   = $platformColors[$key] ?? $platformColors['other'];
        $pPct  = $total > 0 ? ($p['total'] / $total) * 100 : 0;
    ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
        <div class="flex items-center gap-2 mb-2">
            <span class="w-2.5 h-2.5 rounded-full <?= $clr['dot'] ?>"></span>
            <span class="text-xs font-medium text-gray-600 dark:text-gray-400 truncate"><?= htmlspecialchars($label, ENT_QUOTES) ?></span>
        </div>
        <p class="text-base font-bold text-gray-900 dark:text-white">RM <?= number_format((float)$p['total'], 0) ?></p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5"><?= number_format($pPct, 1) ?>%</p>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Add Sale Row -->
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm mb-6 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
        <h3 class="font-semibold text-gray-900 dark:text-white text-sm"><?= __('sales_history') ?></h3>
        <button onclick="toggleSaleForm()"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white rounded-lg bg-emerald-600 hover:bg-emerald-700 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <?= __('add_sale') ?>
        </button>
    </div>

    <!-- Add Sale Form -->
    <div id="sale-form" class="hidden border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 px-6 py-5">
        <form method="POST" action="<?= BASE_URI ?>/revenue/store">
            <?= \App\Core\CSRF::field() ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Amount -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('amount') ?> (RM) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="amount" step="0.01" min="0.01" required placeholder="0.00"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>
                <!-- Platform -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('platform') ?> <span class="text-red-500">*</span>
                    </label>
                    <select name="platform" id="add-platform-select" required
                            onchange="togglePlatformOther('add-platform-other', this.value)"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                        <?php foreach ($platforms_list as $key => $label): ?>
                            <option value="<?= $key ?>"><?= htmlspecialchars($label, ENT_QUOTES) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="platform_custom" id="add-platform-other"
                           placeholder="e.g. Facebook Shop, Direct Order..."
                           maxlength="100"
                           class="hidden mt-2 w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>
                <!-- Date -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('sale_date') ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="sale_date" required value="<?= date('Y-m-d') ?>"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>
                <!-- Description -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('notes') ?> <span class="text-gray-400">(<?= __('optional') ?>)</span>
                    </label>
                    <input type="text" name="description" maxlength="500" placeholder="<?= __('sale_notes_placeholder') ?>"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>
            </div>
            <div class="flex items-center gap-3 mt-4">
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white rounded-lg bg-emerald-600 hover:bg-emerald-700 transition-colors">
                    <?= __('save_sale') ?>
                </button>
                <button type="button" onclick="toggleSaleForm()"
                        class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 transition-colors">
                    <?= __('cancel') ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Sales Table -->
    <?php if (!empty($entries)): ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-gray-100 dark:border-gray-700">
                    <th class="px-6 py-3 text-left font-medium"><?= __('date') ?></th>
                    <th class="px-6 py-3 text-left font-medium"><?= __('platform') ?></th>
                    <th class="px-6 py-3 text-left font-medium"><?= __('notes') ?></th>
                    <th class="px-6 py-3 text-right font-medium"><?= __('amount') ?></th>
                    <th class="px-6 py-3 text-center font-medium"><?= __('added_by') ?></th>
                    <th class="px-6 py-3 text-center font-medium"><?= __('action') ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($entries as $row):
                    $key = $row['platform'];
                    $clr = $platformColors[$key] ?? $platformColors['other'];
                    $pLabel = $platforms_list[$key] ?? $key;
                ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-6 py-3 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                        <?= date('d M Y', strtotime($row['sale_date'])) ?>
                    </td>
                    <td class="px-6 py-3">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2 py-0.5 rounded-full <?= $clr['badge'] ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= $clr['dot'] ?>"></span>
                            <?= htmlspecialchars($pLabel, ENT_QUOTES) ?>
                        </span>
                    </td>
                    <td class="px-6 py-3 text-gray-600 dark:text-gray-400 max-w-xs truncate">
                        <?= $row['description'] !== '' ? htmlspecialchars($row['description'], ENT_QUOTES) : '<span class="text-gray-300 dark:text-gray-600">—</span>' ?>
                    </td>
                    <td class="px-6 py-3 text-right font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                        RM <?= number_format((float)$row['amount'], 2) ?>
                    </td>
                    <td class="px-6 py-3 text-center text-xs text-gray-500 dark:text-gray-400">
                        <?= htmlspecialchars($row['added_by'], ENT_QUOTES) ?>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <form method="POST" action="<?= BASE_URI ?>/revenue/<?= $row['id'] ?>/delete"
                              onsubmit="return confirm('<?= __('confirm_delete_sale') ?>')">
                            <?= \App\Core\CSRF::field() ?>
                            <button type="submit"
                                    class="text-red-500 hover:text-red-700 dark:text-red-400 p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="bg-gray-50 dark:bg-gray-700/30 border-t border-gray-200 dark:border-gray-700">
                    <td class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase" colspan="3"><?= __('total') ?></td>
                    <td class="px-6 py-3 text-right font-bold text-gray-900 dark:text-white">
                        RM <?= number_format($total, 2) ?>
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php else: ?>
    <div class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">
        <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
        </svg>
        <p class="text-sm"><?= __('no_revenue_yet') ?></p>
    </div>
    <?php endif; ?>
</div>

<!-- Set Monthly Target Modal -->
<div id="target-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
    <div class="absolute inset-0 bg-black/50" onclick="document.getElementById('target-modal').classList.add('hidden')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 w-full max-w-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1"><?= __('set_monthly_target') ?></h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            <?= htmlspecialchars($monthName, ENT_QUOTES) ?>
        </p>
        <form method="POST" action="<?= BASE_URI ?>/revenue/target">
            <?= \App\Core\CSRF::field() ?>
            <input type="hidden" name="year"  value="<?= $year ?>">
            <input type="hidden" name="month" value="<?= $month ?>">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <?= __('target_amount') ?> (RM)
                </label>
                <input type="number" name="target_amount" step="0.01" min="0"
                       value="<?= htmlspecialchars((string)$target, ENT_QUOTES) ?>"
                       placeholder="e.g. 50000"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
            </div>
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 rounded-lg transition-colors">
                    <?= __('save_changes') ?>
                </button>
                <button type="button"
                        onclick="document.getElementById('target-modal').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <?= __('cancel') ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<script>
function toggleSaleForm() {
    var form = document.getElementById('sale-form');
    form.classList.toggle('hidden');
    if (!form.classList.contains('hidden')) {
        form.querySelector('input[name="amount"]').focus();
        // Reset platform select + custom input
        var sel = document.getElementById('add-platform-select');
        if (sel) { sel.value = sel.options[0].value; togglePlatformOther('add-platform-other', sel.value); }
    }
}

function togglePlatformOther(inputId, val) {
    var input = document.getElementById(inputId);
    if (!input) return;
    if (val === 'other') {
        input.classList.remove('hidden');
        input.required = true;
        input.focus();
    } else {
        input.classList.add('hidden');
        input.required = false;
        input.value = '';
    }
}

// Revenue month/year picker
(function() {
    var input = document.getElementById('rev-month-picker');
    var initYear  = parseInt(input.getAttribute('data-year'));
    var initMonth = parseInt(input.getAttribute('data-month')); // 1-based

    flatpickr(input, {
        plugins: [
            new monthSelectPlugin({
                shorthand: false,
                dateFormat: 'F Y',
                altFormat:  'F Y',
                theme:      'light',
            })
        ],
        defaultDate: new Date(initYear, initMonth - 1, 1),
        maxDate: new Date(),
        disableMobile: true,
        onChange: function(selectedDates) {
            if (!selectedDates.length) return;
            var d = selectedDates[0];
            var y = d.getFullYear();
            var m = d.getMonth() + 1;
            window.location.href = window.BASE_URI + '/revenue?year=' + y + '&month=' + m;
        }
    });
})();
</script>
