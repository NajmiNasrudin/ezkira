<?php
use App\Core\{CSRF, Session};

/**
 * @var string $date
 * @var array  $entries        manual entries from DB
 * @var array  $auto           auto-calculated values
 * @var array  $savedDates
 * @var array  $sections       BalanceSheet::SECTIONS
 * @var array  $platforms      Revenue::PLATFORMS
 * @var array  $catMeta        ExpenseController::CATEGORY_META
 */

// Merge auto into displayed values: manual overrides auto where set
$display = $entries;
$autoKeys = [
    ['current_asset',     'cash',          $auto['auto_cash']],
    ['non_current_asset', 'ppe',           $auto['auto_ppe']],
    ['current_asset',     'inventories',   $auto['auto_inventory']],
    ['equity',            'share_capital', $auto['auto_share_capital']],
];
foreach ($autoKeys as [$sec, $key, $autoVal]) {
    if (empty($display[$sec][$key])) {
        $display[$sec][$key] = $autoVal;
    }
}

// Retained earnings/accum losses
$retainedNet = $auto['auto_retained'];
// Show as negative equity line (accum_losses) if no manual override
if (empty($display['equity']['accum_losses']) && $retainedNet < 0) {
    $display['equity']['accum_losses'] = abs($retainedNet);
}

// Helper: get display value for form
$val = fn(string $section, string $key) =>
    number_format((float)($display[$section][$key] ?? 0), 2, '.', '');

// Which keys are auto-calculated (show badge, don't require manual input)
$autoFields = [
    'current_asset.cash'            => true,
    'non_current_asset.ppe'         => true,
    'current_asset.inventories'     => true,
    'equity.share_capital'          => true,
    'equity.accum_losses'           => true,
];
$isAuto = fn(string $sec, string $key) => !empty($autoFields["{$sec}.{$key}"]);

$pnl = $auto['_pnl'];
?>

<div class="max-w-3xl mx-auto">

    <!-- ── Page header ─────────────────────────────────────────────────── -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= __('balance_sheet') ?></h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= __('balance_sheet_subtitle') ?></p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <!-- Quick Add buttons -->
            <button type="button" onclick="openModal('modal-revenue')"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <?= __('bs_add_revenue') ?>
            </button>
            <button type="button" onclick="openModal('modal-expense')"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <?= __('bs_add_expense') ?>
            </button>
            <!-- Export dropdown -->
            <?php
                $dateYear  = (int)date('Y', strtotime($date));
                $dateMonth = (int)date('n', strtotime($date));
            ?>
            <div class="relative" id="bs-export-wrap">
                <button type="button"
                        onclick="document.getElementById('bs-export-menu').classList.toggle('hidden')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <?= __('balance_sheet_export') ?>
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="bs-export-menu" class="hidden absolute right-0 mt-1 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-20 overflow-hidden">
                    <a href="<?= BASE_URI ?>/balance-sheet/export?date=<?= urlencode($date) ?>"
                       class="flex items-center gap-2.5 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <div><p class="font-medium"><?= __('bs_export_date') ?></p><p class="text-xs text-gray-400"><?= date('d M Y', strtotime($date)) ?></p></div>
                    </a>
                    <div class="border-t border-gray-100 dark:border-gray-700"></div>
                    <a href="<?= BASE_URI ?>/balance-sheet/export?period=monthly&year=<?= $dateYear ?>&month=<?= $dateMonth ?>"
                       class="flex items-center gap-2.5 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <div><p class="font-medium"><?= __('pnl_monthly') ?></p><p class="text-xs text-gray-400"><?= date('F Y', strtotime($date)) ?></p></div>
                    </a>
                    <div class="border-t border-gray-100 dark:border-gray-700"></div>
                    <a href="<?= BASE_URI ?>/balance-sheet/export?period=annual&year=<?= $dateYear ?>"
                       class="flex items-center gap-2.5 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <div><p class="font-medium"><?= __('pnl_annual') ?></p><p class="text-xs text-gray-400"><?= __('pnl_full_year') ?> <?= $dateYear ?></p></div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Date selector ────────────────────────────────────────────────── -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form method="GET" action="<?= BASE_URI ?>/balance-sheet" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1"><?= __('balance_sheet_as_at') ?></label>
                <input type="date" name="date" value="<?= htmlspecialchars($date, ENT_QUOTES) ?>"
                       class="w-full px-3.5 py-2 rounded-xl border border-gray-300 dark:border-gray-600
                              bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm
                              focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
            <?php if (!empty($savedDates)): ?>
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1"><?= __('balance_sheet_saved_dates') ?></label>
                <select name="date" onchange="this.form.submit()"
                        class="w-full px-3.5 py-2 rounded-xl border border-gray-300 dark:border-gray-600
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm
                               focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <?php foreach ($savedDates as $d): ?>
                    <option value="<?= htmlspecialchars($d, ENT_QUOTES) ?>" <?= $d === $date ? 'selected' : '' ?>>
                        <?= date('d M Y', strtotime($d)) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition-colors">
                <?= __('load') ?>
            </button>
        </form>
    </div>

    <!-- ── P&L Summary (auto-calculated) ──────────────────────────────── -->
    <details class="mb-6 group" open>
        <summary class="flex items-center justify-between cursor-pointer bg-white dark:bg-gray-800 rounded-2xl px-5 py-4 shadow-sm border border-gray-200 dark:border-gray-700 select-none">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span class="text-sm font-semibold text-gray-800 dark:text-white"><?= __('pnl_summary') ?></span>
                <span class="text-xs bg-brand-100 dark:bg-brand-900/40 text-brand-700 dark:text-brand-300 px-2 py-0.5 rounded-full font-medium"><?= __('bs_auto_badge') ?></span>
            </div>
            <svg class="w-4 h-4 text-gray-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </summary>
        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl border-x border-b border-gray-200 dark:border-gray-700 px-5 py-4 space-y-2 -mt-px">
            <?php
            $pnlRows = [
                ['Revenue',               $pnl['revenue'],      'text-emerald-600 dark:text-emerald-400', false],
                ['Less: Cost of Sales',   -$pnl['cogs'],        'text-red-500',                           true],
                ['Gross Profit',          $pnl['gross_profit'], $pnl['gross_profit'] >= 0 ? 'text-brand-600 dark:text-brand-400 font-semibold' : 'text-red-500 font-semibold', false],
                ['Less: OPEX',            -$pnl['opex'],        'text-red-500',                           true],
                ['Less: Marketing',       -$pnl['marketing'],   'text-red-500',                           true],
                ['Net Profit / (Loss)',   $pnl['net_profit'],   $pnl['net_profit'] >= 0 ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-red-500 font-bold', false],
            ];
            foreach ($pnlRows as [$label, $amount, $cls, $indent]):
            ?>
            <div class="flex justify-between items-center <?= $indent ? 'pl-4' : '' ?>">
                <span class="text-sm text-gray-600 dark:text-gray-400"><?= $label ?></span>
                <span class="text-sm <?= $cls ?>">RM <?= number_format(abs($amount), 2) ?><?= $amount < 0 ? ' (-)' : '' ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </details>

    <!-- ── Balance Sheet form ──────────────────────────────────────────── -->
    <form method="POST" action="<?= BASE_URI ?>/balance-sheet/save">
        <?= CSRF::field() ?>
        <input type="hidden" name="as_of_date" value="<?= htmlspecialchars($date, ENT_QUOTES) ?>">

        <?php
        $assetSections  = ['non_current_asset', 'current_asset'];
        $equitySections = ['equity'];
        $liabSections   = ['non_current_liability', 'current_liability'];

        // Helper: render one section's rows
        $renderSection = function(string $sKey, array $def) use ($val, $isAuto, $auto, $retainedNet) {
            $html = '';
            foreach ($def['items'] as $itemKey => $itemLabel) {
                $isAutoField = $this && false; // placeholder — use closure
            }
        };
        ?>

        <!-- ASSETS -->
        <div class="mb-4">
            <div class="bg-brand-600 dark:bg-brand-700 text-white text-xs font-bold uppercase tracking-widest px-5 py-2.5 rounded-t-2xl">
                <?= __('bs_assets') ?>
            </div>
            <?php foreach ($assetSections as $sectionKey):
                $def = $sections[$sectionKey];
            ?>
            <div class="bg-white dark:bg-gray-800 border-x border-gray-200 dark:border-gray-700 px-5 py-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3"><?= htmlspecialchars($def['label'], ENT_QUOTES) ?></h3>
                <div class="space-y-2.5">
                    <?php foreach ($def['items'] as $itemKey => $itemLabel): ?>
                    <div class="flex items-center gap-3">
                        <label class="flex-1 text-sm text-gray-700 dark:text-gray-300">
                            <?= htmlspecialchars($itemLabel, ENT_QUOTES) ?>
                            <?php if ($isAuto($sectionKey, $itemKey)): ?>
                                <span class="ml-1.5 text-xs bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 px-1.5 py-0.5 rounded-full font-medium"><?= __('bs_auto_badge') ?></span>
                            <?php endif; ?>
                        </label>
                        <div class="relative w-36">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">RM</span>
                            <input type="number" step="0.01" min="0"
                                   name="bs[<?= $sectionKey ?>][<?= $itemKey ?>]"
                                   value="<?= $val($sectionKey, $itemKey) ?>"
                                   <?= $isAuto($sectionKey, $itemKey) ? 'data-auto="1"' : '' ?>
                                   class="w-full pl-8 pr-3 py-2 rounded-lg border text-sm text-right transition-colors
                                          <?= $isAuto($sectionKey, $itemKey)
                                              ? 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-800 dark:text-emerald-300'
                                              : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white' ?>
                                          focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-3 pt-3 border-t border-dashed border-gray-200 dark:border-gray-600 flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400"><?= htmlspecialchars($def['total_label'], ENT_QUOTES) ?></span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white" id="subtotal-<?= $sectionKey ?>">RM 0.00</span>
                </div>
            </div>
            <?php endforeach; ?>
            <div class="bg-brand-50 dark:bg-brand-900/20 border border-brand-200 dark:border-brand-700 rounded-b-2xl -mt-px px-5 py-3 flex justify-between items-center">
                <span class="text-sm font-bold text-brand-800 dark:text-brand-300"><?= __('bs_total_assets') ?></span>
                <span class="text-sm font-bold text-brand-800 dark:text-brand-300" id="total-assets">RM 0.00</span>
            </div>
        </div>

        <!-- EQUITY -->
        <div class="mb-4">
            <div class="text-white text-xs font-bold uppercase tracking-widest px-5 py-2.5 rounded-t-2xl" style="background-color:#C4A028">
                <?= __('bs_equity') ?>
            </div>
            <?php foreach ($equitySections as $sectionKey):
                $def = $sections[$sectionKey];
            ?>
            <div class="bg-white dark:bg-gray-800 border-x border-b border-gray-200 dark:border-gray-700 px-5 py-4">
                <div class="space-y-2.5">
                    <?php foreach ($def['items'] as $itemKey => $itemLabel): ?>
                    <?php
                        // For accum_losses, also show retained profit note
                        $extraNote = '';
                        if ($itemKey === 'accum_losses') {
                            if ($retainedNet >= 0) {
                                $extraNote = __('bs_retained_profit') . ': RM ' . number_format($retainedNet, 2);
                            } else {
                                $extraNote = __('bs_enter_positive');
                            }
                        }
                    ?>
                    <div class="flex items-center gap-3">
                        <label class="flex-1 text-sm text-gray-700 dark:text-gray-300">
                            <?= htmlspecialchars($itemLabel, ENT_QUOTES) ?>
                            <?php if ($isAuto($sectionKey, $itemKey)): ?>
                                <span class="ml-1.5 text-xs bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 px-1.5 py-0.5 rounded-full font-medium"><?= __('bs_auto_badge') ?></span>
                            <?php endif; ?>
                            <?php if ($extraNote): ?>
                                <span class="block text-xs text-gray-400 mt-0.5"><?= htmlspecialchars($extraNote, ENT_QUOTES) ?></span>
                            <?php endif; ?>
                        </label>
                        <div class="relative w-36">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">RM</span>
                            <input type="number" step="0.01" min="0"
                                   name="bs[<?= $sectionKey ?>][<?= $itemKey ?>]"
                                   value="<?= $val($sectionKey, $itemKey) ?>"
                                   <?= $isAuto($sectionKey, $itemKey) ? 'data-auto="1"' : '' ?>
                                   class="w-full pl-8 pr-3 py-2 rounded-lg border text-sm text-right transition-colors
                                          <?= $isAuto($sectionKey, $itemKey)
                                              ? 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-800 dark:text-emerald-300'
                                              : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white' ?>
                                          focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <!-- Retained earnings (profit) shown separately if positive -->
                    <?php if ($retainedNet > 0): ?>
                    <div class="flex items-center gap-3">
                        <label class="flex-1 text-sm text-gray-700 dark:text-gray-300">
                            <?= __('bs_retained_earnings') ?>
                            <span class="ml-1.5 text-xs bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 px-1.5 py-0.5 rounded-full font-medium"><?= __('bs_auto_badge') ?></span>
                        </label>
                        <div class="relative w-36">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">RM</span>
                            <input type="number" readonly value="<?= number_format($retainedNet, 2, '.', '') ?>"
                                   name="bs[equity][retained_earnings]"
                                   class="w-full pl-8 pr-3 py-2 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-800 dark:text-emerald-300 text-sm text-right">
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="mt-3 pt-3 border-t border-dashed border-gray-200 dark:border-gray-600 flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400"><?= htmlspecialchars($def['total_label'], ENT_QUOTES) ?></span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white" id="subtotal-<?= $sectionKey ?>">RM 0.00</span>
                </div>
            </div>
            <?php endforeach; ?>
            <div class="rounded-b-2xl border-x border-b border-gray-200 dark:border-gray-700 px-5 py-3 flex justify-between items-center bg-yellow-50 dark:bg-yellow-900/10">
                <span class="text-sm font-bold text-gray-800 dark:text-gray-200"><?= __('bs_total_equity') ?></span>
                <span class="text-sm font-bold text-gray-800 dark:text-gray-200" id="total-equity">RM 0.00</span>
            </div>
        </div>

        <!-- LIABILITIES -->
        <div class="mb-6">
            <div class="bg-red-600 dark:bg-red-700 text-white text-xs font-bold uppercase tracking-widest px-5 py-2.5 rounded-t-2xl">
                <?= __('bs_liabilities') ?>
            </div>
            <?php foreach ($liabSections as $sectionKey):
                $def = $sections[$sectionKey];
            ?>
            <div class="bg-white dark:bg-gray-800 border-x border-b border-gray-200 dark:border-gray-700 px-5 py-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3"><?= htmlspecialchars($def['label'], ENT_QUOTES) ?></h3>
                <div class="space-y-2.5">
                    <?php foreach ($def['items'] as $itemKey => $itemLabel): ?>
                    <div class="flex items-center gap-3">
                        <label class="flex-1 text-sm text-gray-700 dark:text-gray-300">
                            <?= htmlspecialchars($itemLabel, ENT_QUOTES) ?>
                        </label>
                        <div class="relative w-36">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">RM</span>
                            <input type="number" step="0.01" min="0"
                                   name="bs[<?= $sectionKey ?>][<?= $itemKey ?>]"
                                   value="<?= $val($sectionKey, $itemKey) ?>"
                                   class="w-full pl-8 pr-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm text-right
                                          focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-3 pt-3 border-t border-dashed border-gray-200 dark:border-gray-600 flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400"><?= htmlspecialchars($def['total_label'], ENT_QUOTES) ?></span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white" id="subtotal-<?= $sectionKey ?>">RM 0.00</span>
                </div>
            </div>
            <?php endforeach; ?>
            <div class="border-x border-b border-gray-200 dark:border-gray-700 px-5 py-3 flex justify-between items-center bg-red-50 dark:bg-red-900/10">
                <span class="text-sm font-bold text-gray-800 dark:text-gray-200"><?= __('bs_total_liabilities') ?></span>
                <span class="text-sm font-bold text-gray-800 dark:text-gray-200" id="total-liab">RM 0.00</span>
            </div>
            <div class="rounded-b-2xl border-x border-b border-gray-200 dark:border-gray-700 px-5 py-3 flex justify-between items-center bg-brand-600 dark:bg-brand-700">
                <span class="text-sm font-bold text-white"><?= __('bs_total_equity_liabilities') ?></span>
                <span class="text-sm font-bold text-white" id="total-eq-liab">RM 0.00</span>
            </div>
        </div>

        <div class="flex items-center justify-between mb-8">
            <p class="text-xs text-gray-400 dark:text-gray-500">
                <span class="inline-block w-2.5 h-2.5 bg-emerald-200 dark:bg-emerald-800 rounded-sm mr-1"></span>
                <?= __('bs_auto_note') ?>
            </p>
            <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl transition-colors">
                <?= __('balance_sheet_save') ?>
            </button>
        </div>
    </form>
</div>

<!-- ── Quick Add Revenue Modal ──────────────────────────────────────────── -->
<div id="modal-revenue" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white"><?= __('bs_add_revenue') ?></h3>
            <button type="button" onclick="closeModal('modal-revenue')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="<?= BASE_URI ?>/revenue/store" class="p-6 space-y-4">
            <?= CSRF::field() ?>
            <input type="hidden" name="year"  value="<?= $dateYear ?>">
            <input type="hidden" name="month" value="<?= $dateMonth ?>">
            <input type="hidden" name="entry_type" value="sale">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"><?= __('revenue_platform') ?></label>
                <select name="platform" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <?php foreach ($platforms as $key => $label): ?>
                    <option value="<?= $key ?>"><?= htmlspecialchars($label, ENT_QUOTES) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"><?= __('amount') ?> (RM)</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required placeholder="0.00"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"><?= __('date') ?></label>
                    <input type="date" name="sale_date" value="<?= htmlspecialchars($date, ENT_QUOTES) ?>" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"><?= __('description') ?> <span class="text-gray-400">(<?= __('optional') ?>)</span></label>
                <input type="text" name="description" placeholder="<?= __('revenue_desc_placeholder') ?>"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
            <input type="hidden" name="payment_method" value="online_banking">
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeModal('modal-revenue')"
                        class="flex-1 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <?= __('cancel') ?>
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    <?= __('save') ?>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ── Quick Add Expense Modal ───────────────────────────────────────────── -->
<div id="modal-expense" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white"><?= __('bs_add_expense') ?></h3>
            <button type="button" onclick="closeModal('modal-expense')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="<?= BASE_URI ?>/expenses/store" class="p-6 space-y-4">
            <?= CSRF::field() ?>
            <input type="hidden" name="year"  value="<?= $dateYear ?>">
            <input type="hidden" name="month" value="<?= $dateMonth ?>">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"><?= __('category') ?></label>
                <select name="category" id="exp-cat-select"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <optgroup label="📊 P&L — Income Statement">
                        <?php foreach ($catMeta as $key => $meta): if ($meta['group'] !== 'pnl') continue; ?>
                        <option value="<?= $key ?>"><?= htmlspecialchars($meta['label'], ENT_QUOTES) ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                    <optgroup label="🏢 Balance Sheet — Assets">
                        <?php foreach ($catMeta as $key => $meta): if ($meta['group'] !== 'asset') continue; ?>
                        <option value="<?= $key ?>"><?= htmlspecialchars($meta['label'], ENT_QUOTES) ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                    <optgroup label="💳 Other">
                        <?php foreach ($catMeta as $key => $meta): if ($meta['group'] !== 'other') continue; ?>
                        <option value="<?= $key ?>"><?= htmlspecialchars($meta['label'], ENT_QUOTES) ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                </select>
                <p id="exp-cat-hint" class="text-xs text-gray-400 dark:text-gray-500 mt-1.5"></p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"><?= __('amount') ?> (RM)</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required placeholder="0.00"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"><?= __('date') ?></label>
                    <input type="date" name="expense_date" value="<?= htmlspecialchars($date, ENT_QUOTES) ?>" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"><?= __('description') ?> <span class="text-gray-400">(<?= __('optional') ?>)</span></label>
                <input type="text" name="description" placeholder="<?= __('expense_desc_placeholder') ?>"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeModal('modal-expense')"
                        class="flex-1 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <?= __('cancel') ?>
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition-colors">
                    <?= __('save') ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Category hint messages
const CAT_HINTS = <?php
    $hints = [];
    foreach ($catMeta as $key => $meta) {
        $hints[$key] = match($meta['group']) {
            'pnl'   => '📊 ' . __('bs_hint_pnl'),
            'asset' => '🏢 ' . __('bs_hint_asset'),
            default => '💳 ' . __('bs_hint_other'),
        };
    }
    echo json_encode($hints);
?>;

// Modal helpers
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
document.querySelectorAll('[id^="modal-"]').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); });
});

// Category hint on change
const catSel = document.getElementById('exp-cat-select');
const catHint = document.getElementById('exp-cat-hint');
if (catSel && catHint) {
    function updateCatHint() { catHint.textContent = CAT_HINTS[catSel.value] || ''; }
    catSel.addEventListener('change', updateCatHint);
    updateCatHint();
}

// ── Balance Sheet totals ────────────────────────────────────────────────
const BS_SECTIONS = <?php
    $js = [];
    foreach ($sections as $sKey => $sDef) {
        $js[$sKey] = array_keys($sDef['items']);
    }
    // Add retained_earnings as equity item if positive
    if ($retainedNet > 0) {
        $js['equity'][] = 'retained_earnings';
    }
    echo json_encode($js);
?>;
const ASSET_SECTIONS  = ['non_current_asset','current_asset'];
const EQUITY_SECTIONS = ['equity'];
const LIAB_SECTIONS   = ['non_current_liability','current_liability'];

function rmFmt(n) { return 'RM ' + Math.abs(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,','); }

function calcTotals() {
    let totalAssets = 0, totalEquity = 0, totalLiab = 0;
    Object.entries(BS_SECTIONS).forEach(([section, keys]) => {
        let sub = 0;
        keys.forEach(key => {
            const inp = document.querySelector(`input[name="bs[${section}][${key}]"]`);
            let v = inp ? parseFloat(inp.value) || 0 : 0;
            if (key === 'accum_losses') v = -v;
            sub += v;
        });
        const el = document.getElementById('subtotal-' + section);
        if (el) el.textContent = rmFmt(sub);
        if (ASSET_SECTIONS.includes(section))  totalAssets += sub;
        if (EQUITY_SECTIONS.includes(section)) totalEquity += sub;
        if (LIAB_SECTIONS.includes(section))   totalLiab   += sub;
    });
    document.getElementById('total-assets').textContent  = rmFmt(totalAssets);
    document.getElementById('total-equity').textContent  = rmFmt(totalEquity);
    document.getElementById('total-liab').textContent    = rmFmt(totalLiab);
    document.getElementById('total-eq-liab').textContent = rmFmt(totalEquity + totalLiab);
}
document.querySelectorAll('input[type="number"]').forEach(i => i.addEventListener('input', calcTotals));
calcTotals();

// Close export dropdown on outside click
document.addEventListener('click', function(e) {
    var wrap = document.getElementById('bs-export-wrap');
    var menu = document.getElementById('bs-export-menu');
    if (wrap && menu && !wrap.contains(e.target)) menu.classList.add('hidden');
});
</script>
