<?php
/**
 * @var int    $year
 * @var int    $month
 * @var float  $targetRevenue
 * @var array  $pcts          ['opex'=>float, 'marketing'=>float, 'cogs'=>float]
 * @var array  $allExpenses   All expenses for the month (all categories), with receipts
 * @var array  $totals        ['opex'=>float, 'marketing'=>float, 'cogs'=>float, 'ppe'=>float, 'inventory'=>float, 'liability'=>float]
 */
?>
<!-- Flatpickr: month picker -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
<?php

// Month navigator helpers
$prevMonth = $month - 1 < 1  ? 12 : $month - 1;
$prevYear  = $month - 1 < 1  ? $year - 1 : $year;
$nextMonth = $month + 1 > 12 ? 1  : $month + 1;
$nextYear  = $month + 1 > 12 ? $year + 1 : $year;
$isCurrentMonth = ($year === (int)date('Y') && $month === (int)date('n'));

$suggested = ['opex' => 20, 'marketing' => 10, 'cogs' => 40];

$cats = [
    'opex' => [
        'label'    => 'OPEX',
        'subtitle' => __('opex_subtitle'),
        'pct'      => $pcts['opex'],
        'badge'    => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
        'bar'      => 'bg-blue-500',
    ],
    'marketing' => [
        'label'    => 'Marketing Expenses',
        'subtitle' => __('marketing_subtitle'),
        'pct'      => $pcts['marketing'],
        'badge'    => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
        'bar'      => 'bg-purple-500',
    ],
    'cogs' => [
        'label'    => 'COGS',
        'subtitle' => __('cogs_subtitle'),
        'pct'      => $pcts['cogs'],
        'badge'    => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
        'bar'      => 'bg-amber-500',
    ],
];

// Category display metadata (for unified list)
$catMeta = [
    'opex'      => ['label' => 'OPEX',       'badge' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'],
    'marketing' => ['label' => 'Marketing',  'badge' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300'],
    'cogs'      => ['label' => 'COGS',       'badge' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'],
    'purchases' => ['label' => 'Purchases',  'badge' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300'],
    'ppe'       => ['label' => 'Aset',       'badge' => 'bg-teal-100 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300'],
    'liability' => ['label' => 'Liability',  'badge' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300'],
];

function fmtMoney(float $v): string {
    return 'RM ' . number_format($v, 2);
}

$grandTotal  = array_sum($totals);
$totalSpent  = $totals['opex'] + $totals['marketing'] + $totals['cogs'] + ($totals['liability'] ?? 0);
$netProfit   = $targetRevenue - $totalSpent;
$profitPct   = $targetRevenue > 0 ? ($netProfit / $targetRevenue) * 100 : 0;
$totalPctUsed = $targetRevenue > 0 ? ($totalSpent / $targetRevenue) * 100 : 0;
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white"><?= __('expenses') ?></h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5"><?= __('expenses_subtitle') ?></p>
    </div>
    <div class="flex items-center gap-2 flex-wrap justify-end">
        <!-- Add Expense button -->
        <button type="button" onclick="document.getElementById('add-expense-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <?= __('add_expense') ?>
        </button>
        <!-- Export Daily button -->
        <button type="button" onclick="openDailyExport()"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Export Harian
        </button>
        <!-- Export CSV button -->
        <button type="button" onclick="document.getElementById('export-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            <?= __('export_csv') ?>
        </button>
        <!-- Configure % button -->
        <button type="button" onclick="document.getElementById('budget-pct-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
            </svg>
            <?= __('configure_budget_pct') ?>
        </button>
        <!-- Target display -->
        <?php if ($targetRevenue > 0): ?>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                <?= __('target') ?>: <strong class="text-gray-700 dark:text-gray-200"><?= fmtMoney($targetRevenue) ?></strong>
                <a href="<?= BASE_URI ?>/revenue" class="ml-1 text-xs text-brand-600 dark:text-brand-400 hover:underline"><?= __('change') ?></a>
            </span>
        <?php else: ?>
            <a href="<?= BASE_URI ?>/revenue"
               class="text-sm text-brand-600 dark:text-brand-400 hover:underline">
                <?= __('set_target_in_revenue') ?> →
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Month Navigator -->
<div class="flex items-center justify-center gap-3 mb-6">
    <a href="<?= BASE_URI ?>/expenses?year=<?= $prevYear ?>&month=<?= $prevMonth ?>"
       class="p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>

    <div class="text-center">
        <div class="relative flex items-center gap-1.5 cursor-pointer justify-center"
             onclick="document.getElementById('exp-month-picker').click()">
            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <input id="exp-month-picker"
                   type="text"
                   readonly
                   data-year="<?= $year ?>"
                   data-month="<?= $month ?>"
                   value="<?= date('F Y', mktime(0,0,0,$month,1,$year)) ?>"
                   class="text-base font-semibold text-gray-900 dark:text-white bg-transparent border-none outline-none cursor-pointer
                          hover:text-brand-600 dark:hover:text-brand-400 transition-colors min-w-[120px] text-center">
            <svg class="w-3 h-3 text-gray-400 dark:text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <?php if (!$isCurrentMonth): ?>
            <a href="<?= BASE_URI ?>/expenses?year=<?= date('Y') ?>&month=<?= date('n') ?>"
               class="text-xs text-brand-600 dark:text-brand-400 hover:underline"><?= __('current_month') ?></a>
        <?php else: ?>
            <span class="text-xs text-gray-400 dark:text-gray-500"><?= __('current_month') ?></span>
        <?php endif; ?>
    </div>

    <a href="<?= BASE_URI ?>/expenses?year=<?= $nextYear ?>&month=<?= $nextMonth ?>"
       class="p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
</div>

<!-- Summary Cards Row -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <?php foreach ($cats as $key => $cat):
        $spent  = $totals[$key];
        $target = $targetRevenue * ($cat['pct'] / 100);
        $pct    = $target > 0 ? min(($spent / $target) * 100, 100) : 0;
        $over   = $target > 0 && $spent > $target;
        $warn   = !$over && $target > 0 && ($spent / $target) >= 0.8;
        $barColor = $over ? 'bg-red-500' : ($warn ? 'bg-yellow-500' : $cat['bar']);
    ?>
    <div id="card-<?= $key ?>"
         data-cat="<?= $key ?>"
         onclick="filterByCategory('<?= $key ?>')"
         class="exp-card bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 p-5 shadow-sm cursor-pointer hover:border-gray-400 dark:hover:border-gray-500 transition-all select-none">
        <div class="flex items-start justify-between mb-3">
            <div>
                <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full <?= $cat['badge'] ?> mb-1">
                    <?= $cat['pct'] ?>% <?= __('of_target') ?>
                </span>
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm"><?= $cat['label'] ?></h3>
                <p class="text-xs text-gray-500 dark:text-gray-400"><?= $cat['subtitle'] ?></p>
            </div>
            <?php if ($over): ?>
                <span class="text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-2 py-0.5 rounded-full">Over</span>
            <?php elseif ($warn): ?>
                <span class="text-xs font-medium text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/20 px-2 py-0.5 rounded-full">Near</span>
            <?php endif; ?>
        </div>

        <div class="mb-1 flex items-end justify-between">
            <span class="text-xl font-bold text-gray-900 dark:text-white"><?= fmtMoney($spent) ?></span>
            <span class="text-xs text-gray-400 dark:text-gray-500">
                / <?= $target > 0 ? fmtMoney($target) : __('no_target') ?>
            </span>
        </div>

        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 mt-2">
            <div class="<?= $barColor ?> h-2 rounded-full transition-all duration-500"
                 style="width: <?= $target > 0 ? number_format($pct, 1) : 0 ?>%"></div>
        </div>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
            <?= $target > 0 ? number_format($pct, 1) . '% ' . __('used') : __('set_target_to_see') ?>
        </p>
    </div>
    <?php endforeach; ?>

    <!-- Net Profit Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <div>
                <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full mb-1
                    <?= $netProfit >= 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' ?>">
                    <?= $targetRevenue > 0 ? number_format(abs($profitPct), 1) . '%' : '—' ?>
                    <?= $netProfit >= 0 ? __('net_profit') : 'Loss' ?>
                </span>
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm"><?= __('expected_profit') ?></h3>
                <p class="text-xs text-gray-500 dark:text-gray-400"><?= __('after_all_expenses') ?></p>
            </div>
            <?php if ($targetRevenue > 0): ?>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full
                    <?= $netProfit >= 0 ? 'text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20' : 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20' ?>">
                    <?= $netProfit >= 0 ? '↑' : '↓' ?>
                </span>
            <?php endif; ?>
        </div>

        <div class="mb-1 flex items-end justify-between">
            <span class="text-xl font-bold <?= $netProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' ?>">
                <?= $targetRevenue > 0 ? fmtMoney($netProfit) : '—' ?>
            </span>
            <span class="text-xs text-gray-400 dark:text-gray-500">
                / <?= $targetRevenue > 0 ? fmtMoney($targetRevenue) : __('no_target') ?>
            </span>
        </div>

        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 mt-2 overflow-hidden flex">
            <?php if ($targetRevenue > 0): ?>
                <div class="bg-gray-400 dark:bg-gray-500 h-2 transition-all duration-500"
                     style="width: <?= min(number_format($totalPctUsed, 1), 100) ?>%"></div>
                <?php if ($netProfit > 0): ?>
                <div class="bg-green-500 h-2 transition-all duration-500"
                     style="width: <?= number_format(min($profitPct, 100 - $totalPctUsed), 1) ?>%"></div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
            <?= $targetRevenue > 0
                ? number_format($totalPctUsed, 1) . '% ' . __('used') . ' · ' . number_format(max($profitPct, 0), 1) . '% ' . __('profit_margin')
                : __('set_target_in_revenue') ?>
        </p>
    </div>
</div>

<?php
$ppeTot       = $totals['ppe']       ?? 0;
$liabilityTot = $totals['liability'] ?? 0;
$bsTot        = $ppeTot + $liabilityTot;
?>
<?php if ($bsTot > 0 || true): // always show so user knows these categories exist ?>
<div class="mb-6 rounded-xl border border-teal-200 dark:border-teal-800 bg-teal-50 dark:bg-teal-900/20 px-4 py-3 flex flex-wrap items-center gap-4">
    <div class="flex items-center gap-2">
        <svg class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-xs font-semibold text-teal-700 dark:text-teal-300">Rekod Balance Sheet (bukan P&amp;L)</span>
    </div>
    <div class="flex flex-wrap gap-4 text-sm">
        <span class="flex items-center gap-1.5">
            <span class="inline-block w-2.5 h-2.5 rounded-full bg-teal-500"></span>
            <span class="text-gray-600 dark:text-gray-300">Aset Tetap (PPE):</span>
            <strong class="text-teal-700 dark:text-teal-300"><?= fmtMoney($ppeTot) ?></strong>
        </span>
        <?php if ($liabilityTot > 0): ?>
        <span class="flex items-center gap-1.5">
            <span class="inline-block w-2.5 h-2.5 rounded-full bg-rose-500"></span>
            <span class="text-gray-600 dark:text-gray-300">Liability:</span>
            <strong class="text-rose-600 dark:text-rose-400"><?= fmtMoney($liabilityTot) ?></strong>
        </span>
        <?php endif; ?>
        <span class="text-xs text-gray-400 dark:text-gray-500 italic self-center">
            → Nilai ini auto-masuk ke Balance Sheet (PPE &amp; Liabilities)
        </span>
    </div>
</div>
<?php endif; ?>

<!-- ===== UNIFIED EXPENSE LIST ===== -->
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

    <!-- Section Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-2 h-8 rounded-full bg-brand-500"></div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-semibold text-gray-900 dark:text-white"><?= __('expenses') ?></h3>
                    <!-- Active filter badge -->
                    <span id="active-filter-badge" class="hidden items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                        <span id="active-filter-label"></span>
                        <button type="button" onclick="resetFilter()" class="ml-0.5 hover:opacity-70">✕</button>
                    </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    <?= date('F Y', mktime(0,0,0,$month,1,$year)) ?> &bull;
                    <span id="filter-count"><?= count($allExpenses) ?></span> <?= __('records') ?>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <!-- Search box -->
            <div class="relative">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                <input type="text" id="exp-search"
                       placeholder="Cari deskripsi..."
                       oninput="filterExpenses()"
                       class="pl-8 pr-8 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 outline-none w-44 transition-all focus:w-56">
                <button type="button" id="exp-search-clear"
                        onclick="clearSearch()"
                        class="hidden absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-sm leading-none">✕</button>
            </div>
            <span id="filter-total" class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                <?= fmtMoney($grandTotal) ?>
            </span>
        </div>
    </div>

    <?php if (!empty($allExpenses)): ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-gray-100 dark:border-gray-700">
                    <th class="px-6 py-3 text-left font-medium"><?= __('date') ?></th>
                    <th class="px-6 py-3 text-left font-medium"><?= __('category') ?></th>
                    <th class="px-6 py-3 text-left font-medium"><?= __('description') ?></th>
                    <th class="px-6 py-3 text-right font-medium"><?= __('amount') ?></th>
                    <th class="px-6 py-3 text-center font-medium"><?= __('receipt') ?></th>
                    <th class="px-6 py-3 text-center font-medium"><?= __('added_by') ?></th>
                    <th class="px-6 py-3 text-center font-medium"><?= __('action') ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($allExpenses as $row):
                    $cm = $catMeta[$row['category']] ?? [
                        'label' => ($row['category'] ? strtoupper($row['category']) : 'Tiada Kategori'),
                        'badge'  => ($row['category']
                            ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
                            : 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300'),
                    ];
                ?>
                <tr class="exp-row hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors"
                    data-cat="<?= htmlspecialchars($row['category'], ENT_QUOTES) ?>"
                    data-amount="<?= $row['amount'] ?>"
                    data-desc="<?= strtolower(htmlspecialchars($row['description'], ENT_QUOTES)) ?>">
                    <td class="px-6 py-3 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                        <?= date('d M Y', strtotime($row['expense_date'])) ?>
                    </td>
                    <td class="px-6 py-3 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $cm['badge'] ?>">
                            <?= $cm['label'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-3 text-gray-900 dark:text-gray-100 max-w-xs">
                        <?= htmlspecialchars($row['description'], ENT_QUOTES) ?>
                    </td>
                    <td class="px-6 py-3 text-right font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                        <?= fmtMoney((float)$row['amount']) ?>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <?php if (!empty($row['receipts'])): ?>
                            <div class="flex flex-col items-start gap-1 min-w-[100px]">
                                <?php foreach ($row['receipts'] as $rcpt): ?>
                                <div class="flex items-center gap-1 w-full">
                                    <a href="<?= BASE_URI ?>/expenses/file/<?= $rcpt['id'] ?>"
                                       target="_blank"
                                       class="flex-1 inline-flex items-center gap-1 text-xs text-brand-600 dark:text-brand-400 hover:underline truncate max-w-[90px]"
                                       title="<?= htmlspecialchars($rcpt['name'], ENT_QUOTES) ?>">
                                        <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                        <span class="truncate"><?= htmlspecialchars(mb_strimwidth($rcpt['name'], 0, 14, '…'), ENT_QUOTES) ?></span>
                                    </a>
                                    <form method="POST" action="<?= BASE_URI ?>/expenses/receipt/<?= $rcpt['id'] ?>/delete"
                                          onsubmit="return confirm('Delete this file?')" class="shrink-0">
                                        <?= \App\Core\CSRF::field() ?>
                                        <input type="hidden" name="year"  value="<?= $year ?>">
                                        <input type="hidden" name="month" value="<?= $month ?>">
                                        <button type="submit" class="text-red-400 hover:text-red-600 dark:text-red-500 dark:hover:text-red-400 text-xs leading-none px-0.5 rounded transition-colors" title="Remove file">×</button>
                                    </form>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <span class="text-gray-300 dark:text-gray-600">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-3 text-center text-xs text-gray-500 dark:text-gray-400">
                        <?= htmlspecialchars($row['added_by'], ENT_QUOTES) ?>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <!-- Edit -->
                            <button type="button"
                                    onclick="openEditExpense(<?= htmlspecialchars(json_encode($row), ENT_QUOTES) ?>)"
                                    class="text-brand-600 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300 transition-colors p-1 rounded hover:bg-brand-50 dark:hover:bg-brand-900/20"
                                    title="<?= __('edit') ?>">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <!-- Delete -->
                            <form method="POST" action="<?= BASE_URI ?>/expenses/<?= $row['id'] ?>/delete"
                                  onsubmit="return confirm('<?= __('confirm_delete_expense') ?>')">
                                <?= \App\Core\CSRF::field() ?>
                                <input type="hidden" name="year"  value="<?= $year ?>">
                                <input type="hidden" name="month" value="<?= $month ?>">
                                <button type="submit"
                                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20"
                                        title="<?= __('delete') ?>">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="bg-gray-50 dark:bg-gray-700/30 border-t border-gray-200 dark:border-gray-700">
                    <td class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase" colspan="3">
                        <?= __('total') ?>
                    </td>
                    <td class="px-6 py-3 text-right font-bold text-gray-900 dark:text-white">
                        <span id="footer-total"><?= fmtMoney($grandTotal) ?></span>
                    </td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php else: ?>
    <div class="px-6 py-14 text-center text-gray-400 dark:text-gray-500">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-sm mb-3"><?= __('no_expenses_yet') ?></p>
        <button type="button" onclick="document.getElementById('add-expense-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <?= __('add_expense') ?>
        </button>
    </div>
    <?php endif; ?>
</div>
<!-- ===== END UNIFIED EXPENSE LIST ===== -->

<!-- ===== ADD EXPENSE MODAL ===== -->
<div id="add-expense-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100 dark:border-gray-700 shrink-0">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white"><?= __('add_expense') ?></h3>
            <button type="button" onclick="document.getElementById('add-expense-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Scrollable body -->
        <div class="overflow-y-auto flex-1 px-6 py-5">
            <form method="POST" action="<?= BASE_URI ?>/expenses/store" enctype="multipart/form-data">
                <?= \App\Core\CSRF::field() ?>
                <input type="hidden" name="year"  value="<?= $year ?>">
                <input type="hidden" name="month" value="<?= $month ?>">

                <div class="space-y-4">
                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <?= __('category') ?> <span class="text-red-500">*</span>
                        </label>
                        <select name="category" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                            <option value="" disabled selected><?= __('select_category') ?></option>
                            <optgroup label="📊 P&amp;L — Expenses">
                                <option value="cogs">Cost of Goods Sold (COGS)</option>

                                <option value="opex">Operating Expenses (OPEX)</option>
                                <option value="marketing">Marketing &amp; Advertising</option>
                            </optgroup>
                            <optgroup label="🏢 Balance Sheet — Aset (CapEx)">
                                <option value="ppe">Aset Tetap / PPE (mesin, perabot, kenderaan)</option>
                            </optgroup>
                            <optgroup label="📋 Balance Sheet — Liabilities">
                                <option value="liability"><?= __('liability') ?> / Loan Repayment</option>
                            </optgroup>
                        </select>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            💡 Pilih <strong>Aset Tetap/PPE</strong> untuk belian aset (mesin, meja, kenderaan) — ia masuk Balance Sheet, bukan P&amp;L
                        </p>
                    </div>
                    <!-- Amount -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <?= __('amount') ?> (RM) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="amount" step="0.01" min="0.01" required
                               placeholder="0.00"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                    <!-- Date -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <?= __('expense_date') ?> <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="expense_date" required
                               value="<?= date('Y-m-d') ?>"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <?= __('description') ?> <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="description" required maxlength="500"
                               placeholder="<?= __('expense_desc_placeholder') ?>"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                    <!-- Receipt -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <?= __('receipt') ?> <span class="text-gray-400">(<?= __('optional') ?>)</span>
                        </label>
                        <label class="flex items-center gap-3 w-full px-3 py-2.5 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 cursor-pointer hover:border-brand-400 transition-colors">
                            <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            <span id="add-file-label" class="text-sm text-gray-500 dark:text-gray-400 truncate flex-1">
                                <?= __('upload_files_hint') ?>
                            </span>
                            <input type="file" name="receipts[]" class="sr-only" multiple
                                   accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip"
                                   onchange="updateAddFileLabel(this)">
                        </label>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1"><?= __('receipt_hint') ?></p>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit"
                            class="flex-1 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 rounded-lg transition-colors">
                        <?= __('save_expense') ?>
                    </button>
                    <button type="button"
                            onclick="document.getElementById('add-expense-modal').classList.add('hidden')"
                            class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <?= __('cancel') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- ===== END ADD EXPENSE MODAL ===== -->

<!-- ===== EXPORT HARIAN MODAL ===== -->
<div id="daily-export-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
    <div class="absolute inset-0 bg-black/50" onclick="document.getElementById('daily-export-modal').classList.add('hidden')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 w-full max-w-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Export Laporan Harian</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pilih tarikh untuk export</p>
            </div>
            <button type="button" onclick="document.getElementById('daily-export-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <input type="date" id="daily-export-date"
               value="<?= date('Y-m-d') ?>"
               max="<?= date('Y-m-d') ?>"
               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 mb-4">
        <button type="button" onclick="doDailyExport()"
                class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl transition-colors text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download CSV
        </button>
    </div>
</div>
<!-- ===== END EXPORT HARIAN MODAL ===== -->

<!-- ===== EXPORT CSV MODAL ===== -->
<div id="export-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
    <div class="absolute inset-0 bg-black/50" onclick="document.getElementById('export-modal').classList.add('hidden')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 w-full max-w-sm">

        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white"><?= __('export_csv') ?></h3>
            <button type="button" onclick="document.getElementById('export-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Mode toggle -->
        <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-xl p-1 gap-0.5 mb-5">
            <button type="button" id="exp-btn-daily" onclick="switchExportMode('daily')"
                    class="flex-1 py-1.5 text-sm font-medium rounded-lg transition-colors text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                Harian
            </button>
            <button type="button" id="exp-btn-month" onclick="switchExportMode('month')"
                    class="flex-1 py-1.5 text-sm font-medium rounded-lg transition-colors bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm">
                <?= __('export_by_month') ?>
            </button>
            <button type="button" id="exp-btn-range" onclick="switchExportMode('range')"
                    class="flex-1 py-1.5 text-sm font-medium rounded-lg transition-colors text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                <?= __('export_by_range') ?>
            </button>
        </div>

        <!-- By Daily -->
        <div id="exp-daily-panel" class="hidden">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pilih Tarikh</label>
            <input type="date" id="exp-daily-date"
                   value="<?= date('Y-m-d') ?>"
                   max="<?= date('Y-m-d') ?>"
                   class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Export semua rekod untuk satu hari sahaja</p>
        </div>

        <!-- By Month -->
        <div id="exp-month-panel">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"><?= __('select_month') ?></label>
            <div class="flex gap-2">
                <select id="exp-month" class="flex-1 px-3 py-2 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= $m === (int)date('n') ? 'selected' : '' ?>>
                            <?= date('F', mktime(0,0,0,$m,1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <select id="exp-year" class="w-24 px-3 py-2 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <?php for ($y = (int)date('Y'); $y >= 2020; $y--): ?>
                        <option value="<?= $y ?>" <?= $y === (int)date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>

        <!-- By Date Range -->
        <div id="exp-range-panel" class="hidden">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"><?= __('date_from') ?></label>
                <input type="date" id="exp-from"
                       value="<?= date('Y-m-01') ?>"
                       max="<?= date('Y-m-d') ?>"
                       class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"><?= __('date_to') ?></label>
                <input type="date" id="exp-to"
                       value="<?= date('Y-m-d') ?>"
                       max="<?= date('Y-m-d') ?>"
                       class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
        </div>

        <button type="button" onclick="doExport()"
                class="mt-5 w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl transition-colors text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            <?= __('download_csv') ?>
        </button>
    </div>
</div>
<!-- ===== END EXPORT CSV MODAL ===== -->

<!-- Configure Budget % Modal -->
<div id="budget-pct-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
    <div class="absolute inset-0 bg-black/50" onclick="document.getElementById('budget-pct-modal').classList.add('hidden')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1"><?= __('configure_budget_pct') ?></h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-5"><?= __('configure_budget_pct_hint') ?></p>

        <form method="POST" action="<?= BASE_URI ?>/expenses/budget-pct">
            <?= \App\Core\CSRF::field() ?>

            <?php
            $catLabels = [
                'opex'      => ['name' => 'OPEX',               'sub' => 'Operational Expenses',    'color' => 'text-blue-600 dark:text-blue-400'],
                'marketing' => ['name' => 'Marketing Expenses',  'sub' => 'Advertising & Promotions','color' => 'text-purple-600 dark:text-purple-400'],
                'cogs'      => ['name' => 'COGS',               'sub' => 'Cost of Goods Sold',       'color' => 'text-amber-600 dark:text-amber-400'],
            ];
            ?>

            <div class="space-y-4 mb-6">
                <?php foreach ($catLabels as $key => $info): ?>
                <div class="flex items-center justify-between gap-4 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold <?= $info['color'] ?>"><?= $info['name'] ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= $info['sub'] ?></p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            <?= __('suggested') ?>: <?= $suggested[$key] ?>%
                        </p>
                    </div>
                    <div class="shrink-0 flex items-center gap-1.5">
                        <input type="number" name="pct_<?= $key ?>"
                               value="<?= number_format($pcts[$key], 1) ?>"
                               min="0" max="100" step="0.5" required
                               class="w-20 px-2 py-1.5 text-sm text-right font-semibold border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none"
                               oninput="updateLiveCalc('<?= $key ?>', this.value)">
                        <span class="text-sm text-gray-500 dark:text-gray-400">%</span>
                    </div>
                </div>
                <!-- Live calc -->
                <p class="text-xs text-gray-400 dark:text-gray-500 px-3 -mt-2" id="calc-<?= $key ?>">
                    <?php
                    $calcAmt = $targetRevenue * ($pcts[$key] / 100);
                    echo $targetRevenue > 0
                        ? '= ' . fmtMoney($calcAmt) . ' ' . __('per_month')
                        : __('set_target_to_see_amount');
                    ?>
                </p>
                <?php endforeach; ?>
            </div>

            <!-- Total % indicator -->
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-5 px-1">
                <span><?= __('total_allocated') ?>:</span>
                <span id="total-pct" class="font-semibold text-gray-700 dark:text-gray-200">
                    <?= number_format(array_sum($pcts), 1) ?>%
                </span>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 rounded-lg transition-colors">
                    <?= __('save_changes') ?>
                </button>
                <button type="button"
                        onclick="document.getElementById('budget-pct-modal').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <?= __('cancel') ?>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Expense Modal -->
<div id="edit-expense-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100 dark:border-gray-700 shrink-0">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white"><?= __('edit') ?></h3>
            <button type="button" onclick="document.getElementById('edit-expense-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Scrollable body -->
        <div class="overflow-y-auto flex-1 px-6 py-5">
            <form id="edit-expense-action" method="POST" action="" enctype="multipart/form-data">
                <?= \App\Core\CSRF::field() ?>
                <input type="hidden" id="edit-expense-id" name="id" value="">
                <input type="hidden" name="year"  value="<?= $year ?>">
                <input type="hidden" name="month" value="<?= $month ?>">

                <div class="space-y-4">
                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1"><?= __('category') ?></label>
                        <select id="edit-expense-cat" name="category" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                            <option value="" disabled>— Pilih Kategori —</option>
                            <optgroup label="📊 P&amp;L — Expenses">
                                <option value="cogs">Cost of Goods Sold (COGS)</option>
                                <option value="opex">Operating Expenses (OPEX)</option>
                                <option value="marketing">Marketing &amp; Advertising</option>
                            </optgroup>
                            <optgroup label="🏢 Balance Sheet — Aset (CapEx)">
                                <option value="ppe">Aset Tetap / Property, Plant &amp; Equipment (PPE)</option>
                            </optgroup>
                            <optgroup label="📋 Balance Sheet — Liabilities">
                                <option value="liability"><?= __('liability') ?> / Loan Repayment</option>
                            </optgroup>
                        </select>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            💡 PPE = aset tetap (mesin, peralatan, perabot) — masuk Balance Sheet, bukan P&amp;L
                        </p>
                    </div>
                    <!-- Amount -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1"><?= __('amount') ?> (RM)</label>
                        <input type="number" id="edit-expense-amount" name="amount" step="0.01" min="0.01" required
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1"><?= __('description') ?></label>
                        <input type="text" id="edit-expense-desc" name="description" required
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                    <!-- Date -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1"><?= __('expense_date') ?></label>
                        <input type="date" id="edit-expense-date" name="expense_date" required
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>

                    <!-- Attachments -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2"><?= __('receipt') ?></label>
                        <div id="edit-receipts-list" class="space-y-1.5 mb-3"></div>
                        <label class="flex items-center gap-3 w-full px-3 py-2.5 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 cursor-pointer hover:border-brand-400 transition-colors">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span id="edit-file-label" class="text-sm text-gray-500 dark:text-gray-400 truncate flex-1">
                                <?= __('upload_files_hint') ?>
                            </span>
                            <input type="file" name="receipts[]" id="edit-file-input" class="sr-only" multiple
                                   accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip"
                                   onchange="updateEditFileLabel(this)">
                        </label>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1"><?= __('receipt_hint') ?></p>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit"
                            class="flex-1 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 rounded-lg transition-colors">
                        <?= __('save_changes') ?>
                    </button>
                    <button type="button"
                            onclick="document.getElementById('edit-expense-modal').classList.add('hidden')"
                            class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <?= __('cancel') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<script>
// Expenses month/year picker
(function() {
    var input = document.getElementById('exp-month-picker');
    var initYear  = parseInt(input.getAttribute('data-year'));
    var initMonth = parseInt(input.getAttribute('data-month'));

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
            window.location.href = window.BASE_URI + '/expenses?year=' + y + '&month=' + m;
        }
    });
})();

var _targetRevenue = <?= json_encode($targetRevenue) ?>;

function updateAddFileLabel(input) {
    var label = document.getElementById('add-file-label');
    if (input.files.length === 0) {
        label.textContent = '<?= __('upload_files_hint') ?>';
    } else if (input.files.length === 1) {
        label.textContent = input.files[0].name;
    } else {
        label.textContent = input.files.length + ' files selected';
    }
}

function updateLiveCalc(cat, val) {
    var pct = parseFloat(val) || 0;
    var el  = document.getElementById('calc-' + cat);
    if (el) {
        if (_targetRevenue > 0) {
            var amt = _targetRevenue * (pct / 100);
            el.textContent = '= RM ' + amt.toLocaleString('en-MY', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' <?= __('per_month') ?>';
        } else {
            el.textContent = '<?= __('set_target_to_see_amount') ?>';
        }
    }
    var total = 0;
    ['opex','marketing','cogs'].forEach(function(k) {
        var inp = document.querySelector('input[name="pct_' + k + '"]');
        if (inp) total += parseFloat(inp.value) || 0;
    });
    var totalEl = document.getElementById('total-pct');
    if (totalEl) {
        totalEl.textContent = total.toFixed(1) + '%';
        totalEl.className = total > 100
            ? 'font-semibold text-red-500'
            : 'font-semibold text-gray-700 dark:text-gray-200';
    }
}

// Export modal helpers
var _exportMode = 'month';
var _expBtnActive   = 'flex-1 py-1.5 text-sm font-medium rounded-lg transition-colors bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm';
var _expBtnInactive = 'flex-1 py-1.5 text-sm font-medium rounded-lg transition-colors text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300';

function switchExportMode(m) {
    _exportMode = m;
    document.getElementById('exp-btn-daily').className = m === 'daily' ? _expBtnActive : _expBtnInactive;
    document.getElementById('exp-btn-month').className = m === 'month' ? _expBtnActive : _expBtnInactive;
    document.getElementById('exp-btn-range').className = m === 'range' ? _expBtnActive : _expBtnInactive;
    document.getElementById('exp-daily-panel').classList.toggle('hidden', m !== 'daily');
    document.getElementById('exp-month-panel').classList.toggle('hidden', m !== 'month');
    document.getElementById('exp-range-panel').classList.toggle('hidden', m !== 'range');
}

function doExport() {
    var base = '<?= BASE_URI ?>/expenses/export';
    var url;
    if (_exportMode === 'daily') {
        var d = document.getElementById('exp-daily-date').value;
        if (!d) { alert('Sila pilih tarikh.'); return; }
        url = base + '?mode=range&from=' + d + '&to=' + d;
    } else if (_exportMode === 'month') {
        var m = document.getElementById('exp-month').value;
        var y = document.getElementById('exp-year').value;
        url = base + '?mode=month&year=' + y + '&month=' + m;
    } else {
        var from = document.getElementById('exp-from').value;
        var to   = document.getElementById('exp-to').value;
        if (!from || !to) { alert('Sila pilih tarikh.'); return; }
        if (from > to)    { alert('Tarikh mula mesti sebelum tarikh tamat.'); return; }
        url = base + '?mode=range&from=' + from + '&to=' + to;
    }
    window.location.href = url;
    document.getElementById('export-modal').classList.add('hidden');
}

function openDailyExport() {
    document.getElementById('daily-export-date').value = '<?= date('Y-m-d') ?>';
    document.getElementById('daily-export-modal').classList.remove('hidden');
}

function doDailyExport() {
    var d = document.getElementById('daily-export-date').value;
    if (!d) { alert('Sila pilih tarikh.'); return; }
    var url = '<?= BASE_URI ?>/expenses/export?mode=range&from=' + d + '&to=' + d;
    window.location.href = url;
    document.getElementById('daily-export-modal').classList.add('hidden');
}

// Edit expense modal
var _editCsrf = <?= json_encode(\App\Core\CSRF::generate()) ?>;

function openEditExpense(row) {
    document.getElementById('edit-expense-id').value      = row.id;
    document.getElementById('edit-expense-amount').value  = row.amount;
    document.getElementById('edit-expense-desc').value    = row.description;
    document.getElementById('edit-expense-date').value    = row.expense_date;
    var catEl = document.getElementById('edit-expense-cat');
    catEl.value = row.category || '';
    // If category value doesn't match any option (e.g. null/old records), force blank select
    if (catEl.value !== (row.category || '')) { catEl.value = ''; }
    document.getElementById('edit-expense-action').action = '<?= BASE_URI ?>/expenses/' + row.id + '/update';

    document.getElementById('edit-file-label').textContent = '<?= __('upload_files_hint') ?>';
    document.getElementById('edit-file-input').value = '';

    var list = document.getElementById('edit-receipts-list');
    list.innerHTML = '';
    var receipts = row.receipts || [];
    if (receipts.length === 0) {
        list.innerHTML = '<p class="text-xs text-gray-400 dark:text-gray-500 italic">No attachments yet.</p>';
    } else {
        receipts.forEach(function(r) {
            var item = document.createElement('div');
            item.className = 'receipt-item flex items-center justify-between gap-2 px-3 py-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg';
            item.setAttribute('data-id', r.id);
            item.innerHTML =
                '<a href="<?= BASE_URI ?>/expenses/file/' + r.id + '" target="_blank" ' +
                '   class="flex items-center gap-1.5 text-xs text-brand-600 dark:text-brand-400 hover:underline truncate flex-1 min-w-0">' +
                '  <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">' +
                '    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>' +
                '  </svg>' +
                '  <span class="truncate">' + escHtml(r.name) + '</span>' +
                '</a>' +
                '<button type="button" onclick="deleteReceiptInModal(this, ' + r.id + ')" ' +
                '        class="shrink-0 p-1 rounded text-gray-400 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" ' +
                '        title="Remove">' +
                '  <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">' +
                '    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>' +
                '  </svg>' +
                '</button>';
            list.appendChild(item);
        });
    }

    document.getElementById('edit-expense-modal').classList.remove('hidden');
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function updateEditFileLabel(input) {
    var label = document.getElementById('edit-file-label');
    if (input.files.length === 0) {
        label.textContent = '<?= __('upload_files_hint') ?>';
    } else if (input.files.length === 1) {
        label.textContent = input.files[0].name;
    } else {
        label.textContent = input.files.length + ' files selected';
    }
}

function deleteReceiptInModal(btn, receiptId) {
    if (!confirm('<?= __('confirm_delete_expense') ?? 'Delete this file?' ?>')) return;
    btn.disabled = true;
    fetch('<?= BASE_URI ?>/expenses/receipt/' + receiptId + '/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'csrf_token=' + encodeURIComponent(_editCsrf)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.ok) {
            var item = btn.closest('.receipt-item');
            if (item) {
                item.style.opacity = '0';
                item.style.transition = 'opacity 0.2s';
                setTimeout(function() {
                    item.remove();
                    var list = document.getElementById('edit-receipts-list');
                    if (list && list.children.length === 0) {
                        list.innerHTML = '<p class="text-xs text-gray-400 dark:text-gray-500 italic">No attachments yet.</p>';
                    }
                }, 200);
            }
        } else {
            btn.disabled = false;
            alert('Failed to delete file.');
        }
    })
    .catch(function() {
        btn.disabled = false;
        alert('Network error. Please try again.');
    });
}

// ---------------------------------------------------------------
// Category filter — click summary card to filter expense rows
// ---------------------------------------------------------------
var activeFilter = '';
var grandTotal   = <?= $grandTotal ?>;

// Category label map for the badge
var catLabels = {
    opex:      'OPEX',
    marketing: 'Marketing',
    cogs:      'COGS',
    purchases: 'Purchases',
    ppe:       'PPE',
    liability: 'Liability',
};

// Ring colours per category (Tailwind border classes)
var catRing = {
    opex:      'border-blue-500',
    marketing: 'border-purple-500',
    cogs:      'border-amber-500',
    purchases: 'border-cyan-500',
    ppe:       'border-teal-500',
    liability: 'border-rose-500',
};

// Core filter — applies BOTH category filter AND search query simultaneously
function filterExpenses() {
    var q     = (document.getElementById('exp-search')?.value || '').toLowerCase().trim();
    var total = 0, count = 0;

    document.querySelectorAll('.exp-row').forEach(function(row) {
        var matchCat  = (activeFilter === '') || (row.getAttribute('data-cat') === activeFilter);
        var matchDesc = (q === '') || (row.getAttribute('data-desc') || '').includes(q);
        var show      = matchCat && matchDesc;
        row.classList.toggle('hidden', !show);
        if (show) {
            total += parseFloat(row.getAttribute('data-amount') || 0);
            count++;
        }
    });

    var fc = document.getElementById('filter-count');
    if (fc) fc.textContent = count;
    updateTotals(total);

    // Show/hide clear button on search box
    var clr = document.getElementById('exp-search-clear');
    if (clr) clr.classList.toggle('hidden', q === '');
}

function clearSearch() {
    var inp = document.getElementById('exp-search');
    if (inp) inp.value = '';
    filterExpenses();
}

function filterByCategory(cat) {
    if (activeFilter === cat) {
        activeFilter = '';
        // Reset all cards
        document.querySelectorAll('.exp-card').forEach(function(card) {
            card.classList.remove(
                'border-blue-500','border-purple-500','border-amber-500',
                'border-cyan-500','border-teal-500','border-rose-500'
            );
            card.classList.add('border-gray-200');
            card.style.opacity = '1';
        });
        // Hide filter badge
        var badge = document.getElementById('active-filter-badge');
        if (badge) { badge.classList.add('hidden'); badge.classList.remove('inline-flex'); }
    } else {
        activeFilter = cat;
        // Highlight active card, dim others
        document.querySelectorAll('.exp-card').forEach(function(card) {
            var c = card.getAttribute('data-cat');
            card.classList.remove(
                'border-blue-500','border-purple-500','border-amber-500',
                'border-cyan-500','border-teal-500','border-rose-500','border-gray-200'
            );
            if (c === cat) {
                card.classList.add(catRing[cat] || 'border-brand-500');
                card.style.opacity = '1';
            } else {
                card.classList.add('border-gray-200');
                card.style.opacity = '0.45';
            }
        });
        // Show filter badge
        var badge = document.getElementById('active-filter-badge');
        var label = document.getElementById('active-filter-label');
        if (badge && label) {
            label.textContent = catLabels[cat] || cat;
            badge.classList.remove('hidden');
            badge.classList.add('inline-flex');
        }
    }
    filterExpenses(); // re-run with current search + new category
}

function resetFilter() {
    activeFilter = '';
    document.querySelectorAll('.exp-card').forEach(function(card) {
        card.classList.remove(
            'border-blue-500','border-purple-500','border-amber-500',
            'border-cyan-500','border-teal-500','border-rose-500'
        );
        card.classList.add('border-gray-200');
        card.style.opacity = '1';
    });
    var badge = document.getElementById('active-filter-badge');
    if (badge) { badge.classList.add('hidden'); badge.classList.remove('inline-flex'); }
    filterExpenses();
}

function updateTotals(total) {
    var fmt = 'RM ' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    var ft = document.getElementById('filter-total');
    var fh = document.getElementById('footer-total');
    if (ft) ft.textContent = fmt;
    if (fh) fh.textContent = fmt;
}
</script>
