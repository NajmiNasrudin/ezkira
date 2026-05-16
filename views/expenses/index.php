<?php
/**
 * @var int    $year
 * @var int    $month
 * @var float  $targetRevenue
 * @var array  $pcts          ['opex'=>float, 'marketing'=>float, 'cogs'=>float]
 * @var array  $expenses      ['opex'=>[...], 'marketing'=>[...], 'cogs'=>[...]]
 * @var array  $totals        ['opex'=>float, 'marketing'=>float, 'cogs'=>float]
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
        'subtitle' => 'Operational Expenses',
        'pct'      => $pcts['opex'],
        'ring'     => 'ring-blue-500',
        'bg'       => 'bg-blue-50 dark:bg-blue-900/20',
        'badge'    => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
        'bar'      => 'bg-blue-500',
        'btn'      => 'bg-blue-600 hover:bg-blue-700',
        'text'     => 'text-blue-600 dark:text-blue-400',
    ],
    'marketing' => [
        'label'    => 'Marketing Expenses',
        'subtitle' => 'Advertising & Promotions',
        'pct'      => $pcts['marketing'],
        'ring'     => 'ring-purple-500',
        'bg'       => 'bg-purple-50 dark:bg-purple-900/20',
        'badge'    => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
        'bar'      => 'bg-purple-500',
        'btn'      => 'bg-purple-600 hover:bg-purple-700',
        'text'     => 'text-purple-600 dark:text-purple-400',
    ],
    'cogs' => [
        'label'    => 'COGS',
        'subtitle' => 'Cost of Goods Sold',
        'pct'      => $pcts['cogs'],
        'ring'     => 'ring-amber-500',
        'bg'       => 'bg-amber-50 dark:bg-amber-900/20',
        'badge'    => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
        'bar'      => 'bg-amber-500',
        'btn'      => 'bg-amber-600 hover:bg-amber-700',
        'text'     => 'text-amber-600 dark:text-amber-400',
    ],
];

function fmtMoney(float $v): string {
    return 'RM ' . number_format($v, 2);
}
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white"><?= __('expenses') ?></h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5"><?= __('expenses_subtitle') ?></p>
    </div>
    <div class="flex items-center gap-2">
        <!-- Configure % button -->
        <button onclick="document.getElementById('budget-pct-modal').classList.remove('hidden')"
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
    <!-- Prev arrow -->
    <a href="<?= BASE_URI ?>/expenses?year=<?= $prevYear ?>&month=<?= $prevMonth ?>"
       class="p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>

    <!-- Month/Year picker -->
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

    <!-- Next arrow -->
    <a href="<?= BASE_URI ?>/expenses?year=<?= $nextYear ?>&month=<?= $nextMonth ?>"
       class="p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
</div>

<!-- Summary Cards Row -->
<?php
    $totalSpent  = array_sum($totals);
    $netProfit   = $targetRevenue - $totalSpent;
    $profitPct   = $targetRevenue > 0 ? ($netProfit / $targetRevenue) * 100 : 0;
    $totalPctUsed = $targetRevenue > 0 ? ($totalSpent / $targetRevenue) * 100 : 0;
?>
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <?php foreach ($cats as $key => $cat):
        $spent  = $totals[$key];
        $target = $targetRevenue * ($cat['pct'] / 100);
        $pct    = $target > 0 ? min(($spent / $target) * 100, 100) : 0;
        $over   = $target > 0 && $spent > $target;
        $warn   = !$over && $target > 0 && ($spent / $target) >= 0.8;
        $barColor = $over ? 'bg-red-500' : ($warn ? 'bg-yellow-500' : $cat['bar']);
    ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
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

        <!-- Progress Bar -->
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

        <!-- Profit bar: shows total expense consumption + profit remainder -->
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

<!-- Expense Sections -->
<?php foreach ($cats as $key => $cat):
    $entries = $expenses[$key];
    $count   = count($entries);
?>
<div id="section-<?= $key ?>" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mb-6 overflow-hidden">

    <!-- Section Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 <?= $cat['bg'] ?>">
        <div class="flex items-center gap-3">
            <div class="w-2 h-8 rounded-full <?= $cat['bar'] ?>"></div>
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white"><?= $cat['label'] ?></h3>
                <p class="text-xs text-gray-500 dark:text-gray-400"><?= $count ?> <?= __('records') ?></p>
            </div>
        </div>
        <button onclick="toggleExpenseForm('<?= $key ?>')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white rounded-lg transition-colors <?= $cat['btn'] ?>">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <?= __('add_expense') ?>
        </button>
    </div>

    <!-- Add Expense Form (hidden by default) -->
    <div id="form-<?= $key ?>" class="hidden border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 px-6 py-5">
        <form method="POST" action="<?= BASE_URI ?>/expenses/store" enctype="multipart/form-data">
            <?= \App\Core\CSRF::field() ?>
            <input type="hidden" name="category" value="<?= $key ?>">
            <input type="hidden" name="year"     value="<?= $year ?>">
            <input type="hidden" name="month"    value="<?= $month ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Amount -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('amount') ?> (RM) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="amount" step="0.01" min="0.01" required
                           placeholder="0.00"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('expense_date') ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="expense_date" required
                           value="<?= date('Y-m-d') ?>"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('description') ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="description" required maxlength="500"
                           placeholder="<?= __('expense_desc_placeholder') ?>"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>

                <!-- Receipt Upload -->
                <div class="md:col-span-2 lg:col-span-4">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('receipt') ?> <span class="text-gray-400">(<?= __('optional') ?>)</span>
                    </label>
                    <label class="flex items-center gap-3 w-full px-3 py-2.5 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 cursor-pointer hover:border-brand-400 transition-colors">
                        <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        <span id="file-label-<?= $key ?>" class="text-sm text-gray-500 dark:text-gray-400 truncate">
                            <?= __('upload_receipt_hint') ?>
                        </span>
                        <input type="file" name="receipts[]" class="hidden" multiple
                               accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip"
                               onchange="updateFileLabel('<?= $key ?>', this)">
                    </label>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1"><?= __('receipt_hint') ?></p>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-4">
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors <?= $cat['btn'] ?>">
                    <?= __('save_expense') ?>
                </button>
                <button type="button" onclick="toggleExpenseForm('<?= $key ?>')"
                        class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                    <?= __('cancel') ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Expense Table -->
    <?php if ($count > 0): ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-gray-100 dark:border-gray-700">
                    <th class="px-6 py-3 text-left font-medium"><?= __('date') ?></th>
                    <th class="px-6 py-3 text-left font-medium"><?= __('description') ?></th>
                    <th class="px-6 py-3 text-right font-medium"><?= __('amount') ?></th>
                    <th class="px-6 py-3 text-center font-medium"><?= __('receipt') ?></th>
                    <th class="px-6 py-3 text-center font-medium"><?= __('added_by') ?></th>
                    <th class="px-6 py-3 text-center font-medium"><?= __('action') ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($entries as $row): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-6 py-3 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                        <?= date('d M Y', strtotime($row['expense_date'])) ?>
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
                    <td class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase" colspan="2">
                        <?= __('total') ?>
                    </td>
                    <td class="px-6 py-3 text-right font-bold text-gray-900 dark:text-white">
                        <?= fmtMoney($totals[$key]) ?>
                    </td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php else: ?>
    <div class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">
        <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-sm"><?= __('no_expenses_yet') ?></p>
    </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<!-- ===== LIABILITY SECTION ===== -->
<?php
    $liabilityEntries = $expenses['liability'] ?? [];
    $liabilityCount   = count($liabilityEntries);
    $liabilityTotal   = $totals['liability'] ?? 0.0;
?>
<div id="section-liability" class="bg-white dark:bg-gray-800 rounded-xl border border-rose-200 dark:border-rose-900/50 shadow-sm mb-6 overflow-hidden">

    <!-- Section Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-rose-100 dark:border-rose-900/40 bg-rose-50 dark:bg-rose-900/20">
        <div class="flex items-center gap-3">
            <div class="w-2 h-8 rounded-full bg-rose-500"></div>
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white"><?= __('liability') ?></h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    <?= __('liability_subtitle') ?> &bull; <?= $liabilityCount ?> <?= __('records') ?>
                </p>
            </div>
        </div>
        <button onclick="toggleExpenseForm('liability')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white rounded-lg transition-colors bg-rose-600 hover:bg-rose-700">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <?= __('add_liability') ?>
        </button>
    </div>

    <!-- Add Liability Form (hidden by default) -->
    <div id="form-liability" class="hidden border-b border-rose-100 dark:border-rose-900/40 bg-rose-50/60 dark:bg-rose-900/10 px-6 py-5">
        <form method="POST" action="<?= BASE_URI ?>/expenses/store" enctype="multipart/form-data">
            <?= \App\Core\CSRF::field() ?>
            <input type="hidden" name="category" value="liability">
            <input type="hidden" name="year"     value="<?= $year ?>">
            <input type="hidden" name="month"    value="<?= $month ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Amount -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('amount') ?> (RM) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="amount" step="0.01" min="0.01" required
                           placeholder="0.00"
                           class="w-full px-3 py-2 text-sm border border-rose-200 dark:border-rose-800 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none">
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('expense_date') ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="expense_date" required
                           value="<?= date('Y-m-d') ?>"
                           class="w-full px-3 py-2 text-sm border border-rose-200 dark:border-rose-800 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none">
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('description') ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="description" required maxlength="500"
                           placeholder="<?= __('liability_desc_placeholder') ?>"
                           class="w-full px-3 py-2 text-sm border border-rose-200 dark:border-rose-800 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none">
                </div>

                <!-- File Upload (multiple) -->
                <div class="md:col-span-2 lg:col-span-4">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('receipt') ?> <span class="text-gray-400">(<?= __('optional') ?>)</span>
                    </label>
                    <label class="flex items-center gap-3 w-full px-3 py-2.5 border border-dashed border-rose-300 dark:border-rose-700 rounded-lg bg-white dark:bg-gray-800 cursor-pointer hover:border-rose-400 transition-colors">
                        <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        <span id="file-label-liability" class="text-sm text-gray-500 dark:text-gray-400 truncate">
                            <?= __('upload_files_hint') ?>
                        </span>
                        <input type="file" name="receipts[]" class="hidden" multiple
                               accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip"
                               onchange="updateFileLabel('liability', this)">
                    </label>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1"><?= __('receipt_hint') ?></p>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-4">
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors bg-rose-600 hover:bg-rose-700">
                    <?= __('save_expense') ?>
                </button>
                <button type="button" onclick="toggleExpenseForm('liability')"
                        class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                    <?= __('cancel') ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Liability Table -->
    <?php if ($liabilityCount > 0): ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-rose-100 dark:border-rose-900/40">
                    <th class="px-6 py-3 text-left font-medium"><?= __('date') ?></th>
                    <th class="px-6 py-3 text-left font-medium"><?= __('description') ?></th>
                    <th class="px-6 py-3 text-right font-medium"><?= __('amount') ?></th>
                    <th class="px-6 py-3 text-center font-medium"><?= __('receipt') ?></th>
                    <th class="px-6 py-3 text-center font-medium"><?= __('added_by') ?></th>
                    <th class="px-6 py-3 text-center font-medium"><?= __('action') ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50 dark:divide-rose-900/20">
                <?php foreach ($liabilityEntries as $row): ?>
                <tr class="hover:bg-rose-50/40 dark:hover:bg-rose-900/10 transition-colors">
                    <td class="px-6 py-3 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                        <?= date('d M Y', strtotime($row['expense_date'])) ?>
                    </td>
                    <td class="px-6 py-3 text-gray-900 dark:text-gray-100 max-w-xs">
                        <?= htmlspecialchars($row['description'], ENT_QUOTES) ?>
                    </td>
                    <td class="px-6 py-3 text-right font-semibold text-rose-700 dark:text-rose-400 whitespace-nowrap">
                        <?= fmtMoney((float)$row['amount']) ?>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <?php if (!empty($row['receipts'])): ?>
                            <div class="flex flex-col items-start gap-1 min-w-[100px]">
                                <?php foreach ($row['receipts'] as $rcpt): ?>
                                <div class="flex items-center gap-1 w-full">
                                    <a href="<?= BASE_URI ?>/expenses/file/<?= $rcpt['id'] ?>"
                                       target="_blank"
                                       class="flex-1 inline-flex items-center gap-1 text-xs text-rose-600 dark:text-rose-400 hover:underline truncate max-w-[90px]"
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
                                    class="text-rose-500 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300 transition-colors p-1 rounded hover:bg-rose-50 dark:hover:bg-rose-900/20"
                                    title="<?= __('edit') ?>">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <!-- Delete -->
                            <form method="POST" action="<?= BASE_URI ?>/expenses/<?= $row['id'] ?>/delete"
                                  onsubmit="return confirm('<?= __('confirm_delete_liability') ?>')">
                                <?= \App\Core\CSRF::field() ?>
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
                <tr class="bg-rose-50 dark:bg-rose-900/20 border-t border-rose-200 dark:border-rose-900/40">
                    <td class="px-6 py-3 text-xs font-semibold text-rose-600 dark:text-rose-400 uppercase" colspan="2">
                        <?= __('total') ?>
                    </td>
                    <td class="px-6 py-3 text-right font-bold text-rose-700 dark:text-rose-300">
                        <?= fmtMoney($liabilityTotal) ?>
                    </td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php else: ?>
    <div class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">
        <svg class="w-10 h-10 mx-auto mb-2 opacity-40 text-rose-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>
        <p class="text-sm"><?= __('no_liability_yet') ?></p>
    </div>
    <?php endif; ?>
</div>
<!-- ===== END LIABILITY SECTION ===== -->

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
    // Update total
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

function toggleExpenseForm(cat) {
    const form = document.getElementById('form-' + cat);
    form.classList.toggle('hidden');
    if (!form.classList.contains('hidden')) {
        form.querySelector('input[name="amount"]').focus();
    }
}

function updateFileLabel(cat, input) {
    const label = document.getElementById('file-label-' + cat);
    if (input.files.length === 0) {
        label.textContent = '<?= __('upload_receipt_hint') ?>';
    } else if (input.files.length === 1) {
        label.textContent = input.files[0].name;
    } else {
        label.textContent = input.files.length + ' files selected';
    }
}

// Live calc in target modal
document.querySelector('input[name="target_revenue"]')?.addEventListener('input', function() {
    const v = parseFloat(this.value) || 0;
    document.getElementById('calc-opex').textContent = (v * 0.20).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    document.getElementById('calc-mkt').textContent  = (v * 0.10).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    document.getElementById('calc-cogs').textContent = (v * 0.40).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
});

// Auto-open form if anchor matches
window.addEventListener('load', function() {
    const hash = location.hash.replace('#', '');
    if (['opex','marketing','cogs','liability'].includes(hash)) {
        const form = document.getElementById('form-' + hash);
        if (form) form.classList.remove('hidden');
        document.getElementById('section-' + hash)?.scrollIntoView({ behavior: 'smooth' });
    }
});

var _editCsrf = <?= json_encode(\App\Core\CSRF::generate()) ?>;

function openEditExpense(row) {
    document.getElementById('edit-expense-id').value      = row.id;
    document.getElementById('edit-expense-amount').value  = row.amount;
    document.getElementById('edit-expense-desc').value    = row.description;
    document.getElementById('edit-expense-date').value    = row.expense_date;
    document.getElementById('edit-expense-cat').value     = row.category;
    document.getElementById('edit-expense-action').action = '<?= BASE_URI ?>/expenses/' + row.id + '/update';

    // Reset file input label
    document.getElementById('edit-file-label').textContent = '<?= __('upload_files_hint') ?>';
    document.getElementById('edit-file-input').value = '';

    // Render existing receipts
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
</script>

<!-- Edit Expense Modal -->
<div id="edit-expense-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100 dark:border-gray-700 shrink-0">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white"><?= __('edit') ?? 'Edit Expense' ?></h3>
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
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                        <select id="edit-expense-cat" name="category"
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                            <option value="opex">OPEX</option>
                            <option value="marketing">Marketing</option>
                            <option value="cogs">COGS</option>
                            <option value="liability"><?= __('liability') ?></option>
                        </select>
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

                        <!-- Existing receipts list -->
                        <div id="edit-receipts-list" class="space-y-1.5 mb-3"></div>

                        <!-- Add new files -->
                        <label class="flex items-center gap-3 w-full px-3 py-2.5 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 cursor-pointer hover:border-brand-400 transition-colors">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span id="edit-file-label" class="text-sm text-gray-500 dark:text-gray-400 truncate flex-1">
                                <?= __('upload_files_hint') ?>
                            </span>
                            <input type="file" name="receipts[]" id="edit-file-input" class="hidden" multiple
                                   accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip"
                                   onchange="updateEditFileLabel(this)">
                        </label>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1"><?= __('receipt_hint') ?></p>
                    </div>
                </div>

                <!-- Actions inside form so submit works -->
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
