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
        <!-- P&L Download dropdown -->
        <div class="relative" id="pnl-export-wrap">
            <button type="button"
                    onclick="document.getElementById('pnl-export-menu').classList.toggle('hidden')"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <?= __('export_pnl') ?>
                <svg class="w-3.5 h-3.5 ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <!-- Dropdown menu -->
            <div id="pnl-export-menu"
                 class="hidden absolute right-0 mt-1 w-52 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-20 overflow-hidden">
                <a href="<?= BASE_URI ?>/revenue/export-pnl?period=monthly&year=<?= $year ?>&month=<?= $month ?>"
                   class="flex items-center gap-2.5 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <div>
                        <p class="font-medium"><?= __('pnl_monthly') ?></p>
                        <p class="text-xs text-gray-400"><?= $monthName ?></p>
                    </div>
                </a>
                <div class="border-t border-gray-100 dark:border-gray-700"></div>
                <a href="<?= BASE_URI ?>/revenue/export-pnl?period=annual&year=<?= $year ?>"
                   class="flex items-center gap-2.5 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <div>
                        <p class="font-medium"><?= __('pnl_annual') ?></p>
                        <p class="text-xs text-gray-400"><?= __('pnl_full_year') ?> <?= $year ?></p>
                    </div>
                </a>
            </div>
        </div>
        <button type="button" onclick="document.getElementById('target-modal').classList.remove('hidden')"
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
             style="width: <?= number_format(min(100, $pct), 2) ?>%"></div>
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

<!-- Sales History Card -->
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm mb-6 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
        <h3 class="font-semibold text-gray-900 dark:text-white text-sm"><?= __('sales_history') ?></h3>
        <button type="button" onclick="toggleSaleForm()"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white rounded-lg bg-emerald-600 hover:bg-emerald-700 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <?= __('add_sale') ?> / <?= __('entry_type_refund') ?>
        </button>
    </div>

    <!-- Add Sale / Refund Form -->
    <div id="sale-form" class="hidden border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 px-6 py-5">
        <form method="POST" action="<?= BASE_URI ?>/revenue/store" id="add-sale-form">
            <?= \App\Core\CSRF::field() ?>
            <input type="hidden" name="entry_type" id="add-entry-type" value="sale">

            <!-- Sale / Refund toggle -->
            <div class="flex items-center gap-3 mb-4">
                <span class="text-xs font-medium text-gray-600 dark:text-gray-400"><?= __('entry_type') ?>:</span>
                <div class="inline-flex rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden text-sm font-medium">
                    <button type="button" id="add-btn-sale"
                            onclick="setEntryType('add','sale')"
                            class="px-4 py-1.5 bg-emerald-600 text-white transition-colors">
                        <?= __('entry_type_sale') ?>
                    </button>
                    <button type="button" id="add-btn-refund"
                            onclick="setEntryType('add','refund')"
                            class="px-4 py-1.5 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 transition-colors">
                        <?= __('entry_type_refund') ?>
                    </button>
                </div>
                <!-- refund hint shown when refund selected -->
                <span id="add-refund-hint" class="hidden text-xs text-red-500 dark:text-red-400">
                    <?= __('refund_hint') ?? 'Amount will be deducted from revenue' ?>
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
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
                <!-- Payment Method -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('payment_method') ?> <span class="text-red-500">*</span>
                    </label>
                    <select name="payment_method" required
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                        <?php foreach (\Models\Revenue::PAYMENT_METHODS as $pmKey => $pmLabel): ?>
                            <option value="<?= $pmKey ?>"><?= htmlspecialchars($pmLabel, ENT_QUOTES) ?></option>
                        <?php endforeach; ?>
                    </select>
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
                <button type="submit" id="add-submit-btn"
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
                    <th class="px-6 py-3 text-left font-medium"><?= __('payment_method') ?></th>
                    <th class="px-6 py-3 text-left font-medium"><?= __('notes') ?></th>
                    <th class="px-6 py-3 text-right font-medium"><?= __('amount') ?></th>
                    <th class="px-6 py-3 text-center font-medium"><?= __('added_by') ?></th>
                    <th class="px-6 py-3 text-center font-medium"><?= __('action') ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($entries as $row):
                    $key      = $row['platform'];
                    $clr      = $platformColors[$key] ?? $platformColors['other'];
                    $pLabel   = $platforms_list[$key] ?? $key;
                    $isRefund = ($row['entry_type'] ?? 'sale') === 'refund';
                ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors <?= $isRefund ? 'bg-red-50/40 dark:bg-red-900/10' : '' ?>">
                    <td class="px-6 py-3 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                        <?= date('d M Y', strtotime($row['sale_date'])) ?>
                    </td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2 py-0.5 rounded-full <?= $clr['badge'] ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= $clr['dot'] ?>"></span>
                                <?= htmlspecialchars($pLabel, ENT_QUOTES) ?>
                            </span>
                            <?php if ($isRefund): ?>
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                </svg>
                                <?= __('entry_type_refund') ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <?php
                        $pm      = $row['payment_method'] ?? 'cash';
                        $pmLabel = \Models\Revenue::PAYMENT_METHODS[$pm] ?? 'Cash';
                        $pmStyles = [
                            'cash'           => ['path' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'cls' => 'text-green-700 bg-green-50 dark:text-green-400 dark:bg-green-900/20'],
                            'online_banking' => ['path' => 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z',            'cls' => 'text-blue-700 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/20'],
                            'card'           => ['path' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',           'cls' => 'text-indigo-700 bg-indigo-50 dark:text-indigo-400 dark:bg-indigo-900/20'],
                            'ewallet'        => ['path' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',                       'cls' => 'text-pink-700 bg-pink-50 dark:text-pink-400 dark:bg-pink-900/20'],
                            'other'          => ['path' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'cls' => 'text-gray-600 bg-gray-100 dark:text-gray-400 dark:bg-gray-700'],
                        ];
                        $pmStyle = $pmStyles[$pm] ?? $pmStyles['other'];
                    ?>
                    <td class="px-6 py-3 whitespace-nowrap">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2 py-0.5 rounded-full <?= $pmStyle['cls'] ?>">
                            <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $pmStyle['path'] ?>"/>
                            </svg>
                            <?= htmlspecialchars($pmLabel, ENT_QUOTES) ?>
                        </span>
                    </td>
                    <td class="px-6 py-3 text-gray-600 dark:text-gray-400 max-w-xs truncate">
                        <?= $row['description'] !== '' ? htmlspecialchars($row['description'], ENT_QUOTES) : '<span class="text-gray-300 dark:text-gray-600">—</span>' ?>
                    </td>
                    <td class="px-6 py-3 text-right font-semibold whitespace-nowrap <?= $isRefund ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' ?>">
                        <?= $isRefund ? '−' : '' ?>RM <?= number_format((float)$row['amount'], 2) ?>
                    </td>
                    <td class="px-6 py-3 text-center text-xs text-gray-500 dark:text-gray-400">
                        <?= htmlspecialchars($row['added_by'], ENT_QUOTES) ?>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <!-- Edit -->
                            <button type="button"
                                    onclick="openEditSale(<?= htmlspecialchars(json_encode($row), ENT_QUOTES) ?>)"
                                    class="text-brand-600 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300 p-1 rounded hover:bg-brand-50 dark:hover:bg-brand-900/20 transition-colors"
                                    title="Edit">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <!-- Delete -->
                            <form method="POST" action="<?= BASE_URI ?>/revenue/<?= $row['id'] ?>/delete"
                                  onsubmit="return confirm('<?= __('confirm_delete_sale') ?>')">
                                <?= \App\Core\CSRF::field() ?>
                                <button type="submit"
                                        class="text-red-500 hover:text-red-700 dark:text-red-400 p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
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
                    <td class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase" colspan="4"><?= __('total') ?></td>
                    <td class="px-6 py-3 text-right font-bold <?= $total < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' ?>">
                        <?= $total < 0 ? '−' : '' ?>RM <?= number_format(abs($total), 2) ?>
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

<!-- ================================================================ -->
<!-- Capital Section                                                  -->
<!-- ================================================================ -->
<div id="capital" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm mb-6 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
        <div>
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm"><?= __('capital') ?></h3>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5"><?= __('capital_subtitle') ?></p>
        </div>
        <button type="button" onclick="toggleCapitalForm()"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white rounded-lg bg-violet-600 hover:bg-violet-700 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <?= __('add_capital') ?>
        </button>
    </div>

    <!-- Add Capital Form -->
    <div id="capital-form" class="hidden border-b border-gray-100 dark:border-gray-700 bg-violet-50/50 dark:bg-violet-900/10 px-6 py-5">
        <form method="POST" action="<?= BASE_URI ?>/revenue/capital/store">
            <?= \App\Core\CSRF::field() ?>
            <input type="hidden" name="year"  value="<?= $year ?>">
            <input type="hidden" name="month" value="<?= $month ?>">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Amount -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('amount') ?> (RM) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="amount" step="0.01" min="0.01" required placeholder="0.00"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none">
                </div>
                <!-- Date -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('capital_date') ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="capital_date" required value="<?= date('Y-m-d') ?>"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none">
                </div>
                <!-- Description -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('notes') ?> <span class="text-gray-400">(<?= __('optional') ?>)</span>
                    </label>
                    <input type="text" name="description" maxlength="500"
                           placeholder="<?= __('capital_notes_placeholder') ?>"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none">
                </div>
            </div>
            <div class="flex items-center gap-3 mt-4">
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white rounded-lg bg-violet-600 hover:bg-violet-700 transition-colors">
                    <?= __('save_capital') ?>
                </button>
                <button type="button" onclick="toggleCapitalForm()"
                        class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 transition-colors">
                    <?= __('cancel') ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Capital Table -->
    <?php if (!empty($capitalEntries)): ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-gray-100 dark:border-gray-700">
                    <th class="px-6 py-3 text-left font-medium"><?= __('date') ?></th>
                    <th class="px-6 py-3 text-left font-medium"><?= __('notes') ?></th>
                    <th class="px-6 py-3 text-right font-medium"><?= __('amount') ?></th>
                    <th class="px-6 py-3 text-center font-medium"><?= __('added_by') ?></th>
                    <th class="px-6 py-3 text-center font-medium"><?= __('action') ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($capitalEntries as $row): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-6 py-3 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                        <?= date('d M Y', strtotime($row['capital_date'])) ?>
                    </td>
                    <td class="px-6 py-3 text-gray-600 dark:text-gray-400 max-w-xs truncate">
                        <?= $row['description'] !== '' ? htmlspecialchars($row['description'], ENT_QUOTES) : '<span class="text-gray-300 dark:text-gray-600">—</span>' ?>
                    </td>
                    <td class="px-6 py-3 text-right font-semibold text-violet-700 dark:text-violet-400 whitespace-nowrap">
                        RM <?= number_format((float)$row['amount'], 2) ?>
                    </td>
                    <td class="px-6 py-3 text-center text-xs text-gray-500 dark:text-gray-400">
                        <?= htmlspecialchars($row['added_by'], ENT_QUOTES) ?>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <!-- Edit -->
                            <button type="button"
                                    onclick="openEditCapital(<?= htmlspecialchars(json_encode($row), ENT_QUOTES) ?>)"
                                    class="text-violet-600 hover:text-violet-800 dark:text-violet-400 dark:hover:text-violet-300 p-1 rounded hover:bg-violet-50 dark:hover:bg-violet-900/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <!-- Delete -->
                            <form method="POST" action="<?= BASE_URI ?>/revenue/capital/<?= $row['id'] ?>/delete"
                                  onsubmit="return confirm('<?= __('confirm_delete_capital') ?>')">
                                <?= \App\Core\CSRF::field() ?>
                                <button type="submit"
                                        class="text-red-500 hover:text-red-700 dark:text-red-400 p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
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
                <tr class="bg-violet-50/60 dark:bg-violet-900/20 border-t border-gray-200 dark:border-gray-700">
                    <td class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase" colspan="2"><?= __('total_capital') ?></td>
                    <td class="px-6 py-3 text-right font-bold text-violet-700 dark:text-violet-400">
                        RM <?= number_format($capitalTotal, 2) ?>
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
                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm"><?= __('no_capital_yet') ?></p>
    </div>
    <?php endif; ?>
</div>

<!-- Edit Capital Modal -->
<div id="edit-capital-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white"><?= __('capital') ?></h3>
            <button type="button" onclick="document.getElementById('edit-capital-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="edit-capital-form" method="POST" action="" class="px-6 py-5">
            <?= \App\Core\CSRF::field() ?>
            <input type="hidden" name="year"  value="<?= $year ?>">
            <input type="hidden" name="month" value="<?= $month ?>">
            <div class="grid grid-cols-2 gap-4">
                <!-- Amount -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('amount') ?> (RM) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="edit-capital-amount" name="amount" step="0.01" min="0.01" required
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none">
                </div>
                <!-- Date -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('capital_date') ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="edit-capital-date" name="capital_date" required
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none">
                </div>
                <!-- Notes -->
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('notes') ?> <span class="text-gray-400">(<?= __('optional') ?>)</span>
                    </label>
                    <input type="text" id="edit-capital-notes" name="description" maxlength="500"
                           placeholder="<?= __('capital_notes_placeholder') ?>"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none">
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="submit"
                        class="flex-1 py-2 text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 rounded-lg transition-colors">
                    <?= __('save_changes') ?>
                </button>
                <button type="button"
                        onclick="document.getElementById('edit-capital-modal').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <?= __('cancel') ?>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Sale Modal -->
<div id="edit-sale-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 dark:border-gray-700">
            <h3 id="edit-modal-title" class="text-base font-semibold text-gray-900 dark:text-white">Edit Sale</h3>
            <button type="button" onclick="document.getElementById('edit-sale-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="edit-sale-form" method="POST" action="" class="px-6 py-5">
            <?= \App\Core\CSRF::field() ?>
            <input type="hidden" name="year"  value="<?= $year ?>">
            <input type="hidden" name="month" value="<?= $month ?>">
            <input type="hidden" name="entry_type" id="edit-entry-type" value="sale">

            <!-- Sale / Refund toggle -->
            <div class="flex items-center gap-3 mb-4">
                <span class="text-xs font-medium text-gray-600 dark:text-gray-400"><?= __('entry_type') ?>:</span>
                <div class="inline-flex rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden text-sm font-medium">
                    <button type="button" id="edit-btn-sale"
                            onclick="setEntryType('edit','sale')"
                            class="px-4 py-1.5 bg-emerald-600 text-white transition-colors">
                        <?= __('entry_type_sale') ?>
                    </button>
                    <button type="button" id="edit-btn-refund"
                            onclick="setEntryType('edit','refund')"
                            class="px-4 py-1.5 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 transition-colors">
                        <?= __('entry_type_refund') ?>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <!-- Amount -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('amount') ?> (RM) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="edit-sale-amount" name="amount" step="0.01" min="0.01" required
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>
                <!-- Sale Date -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('sale_date') ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="edit-sale-date" name="sale_date" required
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <!-- Platform -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('platform') ?> <span class="text-red-500">*</span>
                    </label>
                    <select id="edit-sale-platform" name="platform" required
                            onchange="togglePlatformOther('edit-platform-other', this.value)"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                        <?php foreach ($platforms_list as $k => $lbl): ?>
                            <option value="<?= $k ?>"><?= htmlspecialchars($lbl, ENT_QUOTES) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="platform_custom" id="edit-platform-other"
                           placeholder="e.g. Facebook Shop, Direct Order..."
                           maxlength="100"
                           class="hidden mt-2 w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>
                <!-- Payment Method -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('payment_method') ?> <span class="text-red-500">*</span>
                    </label>
                    <select id="edit-sale-payment" name="payment_method" required
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                        <?php foreach (\Models\Revenue::PAYMENT_METHODS as $pmKey => $pmLabel): ?>
                            <option value="<?= $pmKey ?>"><?= htmlspecialchars($pmLabel, ENT_QUOTES) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Notes -->
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('notes') ?> <span class="text-gray-400">(<?= __('optional') ?>)</span>
                    </label>
                    <input type="text" id="edit-sale-notes" name="description" maxlength="500"
                           placeholder="<?= __('sale_notes_placeholder') ?>"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="submit"
                        class="flex-1 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 rounded-lg transition-colors">
                    <?= __('save_changes') ?>
                </button>
                <button type="button"
                        onclick="document.getElementById('edit-sale-modal').classList.add('hidden')"
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
var _labelSale   = <?= json_encode(__('entry_type_sale')) ?>;
var _labelRefund = <?= json_encode(__('entry_type_refund')) ?>;

// Toggle Sale / Refund for Add or Edit form (prefix = 'add' | 'edit')
function setEntryType(prefix, type) {
    document.getElementById(prefix + '-entry-type').value = type;

    var saleBtn   = document.getElementById(prefix + '-btn-sale');
    var refundBtn = document.getElementById(prefix + '-btn-refund');

    if (type === 'sale') {
        saleBtn.className   = 'px-4 py-1.5 bg-emerald-600 text-white transition-colors';
        refundBtn.className = 'px-4 py-1.5 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 transition-colors';
    } else {
        refundBtn.className = 'px-4 py-1.5 bg-red-600 text-white transition-colors';
        saleBtn.className   = 'px-4 py-1.5 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 transition-colors';
    }

    // Show/hide refund hint in Add form
    if (prefix === 'add') {
        var hint = document.getElementById('add-refund-hint');
        if (hint) hint.classList.toggle('hidden', type !== 'refund');
    }

    // Update edit modal title
    if (prefix === 'edit') {
        var title = document.getElementById('edit-modal-title');
        if (title) title.textContent = 'Edit ' + (type === 'refund' ? _labelRefund : _labelSale);
    }
}

function toggleSaleForm() {
    var form = document.getElementById('sale-form');
    form.classList.toggle('hidden');
    if (!form.classList.contains('hidden')) {
        // Reset to Sale mode
        setEntryType('add', 'sale');
        form.querySelector('input[name="amount"]').focus();
        var sel = document.getElementById('add-platform-select');
        if (sel) { sel.value = sel.options[0].value; togglePlatformOther('add-platform-other', sel.value); }
    }
}

// Known platform keys from PHP
var _knownPlatforms = <?= json_encode(array_keys(\Models\Revenue::PLATFORMS)) ?>;

function openEditSale(row) {
    document.getElementById('edit-sale-amount').value = row.amount;
    document.getElementById('edit-sale-date').value   = row.sale_date;
    document.getElementById('edit-sale-notes').value  = row.description || '';
    document.getElementById('edit-sale-form').action  = '<?= BASE_URI ?>/revenue/' + row.id + '/update';

    // Set entry type toggle
    var entryType = row.entry_type || 'sale';
    setEntryType('edit', entryType);

    // Set payment method
    var pmSel = document.getElementById('edit-sale-payment');
    if (pmSel) pmSel.value = row.payment_method || 'cash';

    // Platform: if stored value is a known key, select it; otherwise select 'other' + show custom
    var sel = document.getElementById('edit-sale-platform');
    var customInput = document.getElementById('edit-platform-other');
    if (_knownPlatforms.includes(row.platform)) {
        sel.value = row.platform;
        customInput.classList.add('hidden');
        customInput.required = false;
        customInput.value = '';
    } else {
        sel.value = 'other';
        customInput.classList.remove('hidden');
        customInput.required = true;
        customInput.value = row.platform;
    }

    document.getElementById('edit-sale-modal').classList.remove('hidden');
    document.getElementById('edit-sale-amount').focus();
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

// Capital form toggle
function toggleCapitalForm() {
    var form = document.getElementById('capital-form');
    form.classList.toggle('hidden');
    if (!form.classList.contains('hidden')) {
        form.querySelector('input[name="amount"]').focus();
    }
}

function openEditCapital(row) {
    document.getElementById('edit-capital-amount').value = row.amount;
    document.getElementById('edit-capital-date').value   = row.capital_date;
    document.getElementById('edit-capital-notes').value  = row.description || '';
    document.getElementById('edit-capital-form').action  = '<?= BASE_URI ?>/revenue/capital/' + row.id + '/update';
    document.getElementById('edit-capital-modal').classList.remove('hidden');
    document.getElementById('edit-capital-amount').focus();
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

// Close P&L dropdown on outside click
document.addEventListener('click', function(e) {
    var wrap = document.getElementById('pnl-export-wrap');
    var menu = document.getElementById('pnl-export-menu');
    if (wrap && menu && !wrap.contains(e.target)) {
        menu.classList.add('hidden');
    }
});
</script>
