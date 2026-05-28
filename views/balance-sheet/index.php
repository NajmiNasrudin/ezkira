<?php
use App\Core\{CSRF, Session};

// Helper: get saved value for an item
$val = fn(string $section, string $key) =>
    number_format((float)($entries[$section][$key] ?? 0), 2, '.', '');
?>

<div class="max-w-3xl mx-auto">

    <!-- Page header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= __('balance_sheet') ?></h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= __('balance_sheet_subtitle') ?></p>
        </div>
        <!-- Export button -->
        <a href="<?= BASE_URI ?>/balance-sheet/export?date=<?= urlencode($date) ?>"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl transition-colors shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            <?= __('balance_sheet_export') ?>
        </a>
    </div>

    <!-- Date selector -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form method="GET" action="<?= BASE_URI ?>/balance-sheet" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                    <?= __('balance_sheet_as_at') ?>
                </label>
                <input type="date" name="date" value="<?= htmlspecialchars($date, ENT_QUOTES) ?>"
                       class="w-full px-3.5 py-2 rounded-xl border border-gray-300 dark:border-gray-600
                              bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm
                              focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
            <?php if (!empty($savedDates)): ?>
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                    <?= __('balance_sheet_saved_dates') ?>
                </label>
                <select name="date" onchange="this.form.submit()"
                        class="w-full px-3.5 py-2 rounded-xl border border-gray-300 dark:border-gray-600
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm
                               focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <?php foreach ($savedDates as $d): ?>
                        <option value="<?= htmlspecialchars($d, ENT_QUOTES) ?>"
                                <?= $d === $date ? 'selected' : '' ?>>
                            <?= date('d M Y', strtotime($d)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <button type="submit"
                    class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600
                           text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition-colors">
                <?= __('load') ?>
            </button>
        </form>
    </div>

    <!-- Balance sheet form -->
    <form method="POST" action="<?= BASE_URI ?>/balance-sheet/save">
        <?= CSRF::field() ?>
        <input type="hidden" name="as_of_date" value="<?= htmlspecialchars($date, ENT_QUOTES) ?>">

        <?php
        $assetSections     = ['non_current_asset', 'current_asset'];
        $equitySections    = ['equity'];
        $liabSections      = ['non_current_liability', 'current_liability'];
        ?>

        <!-- ASSETS -->
        <div class="mb-4">
            <div class="bg-brand-600 dark:bg-brand-700 text-white text-xs font-bold uppercase tracking-widest px-5 py-2.5 rounded-t-2xl">
                <?= __('bs_assets') ?>
            </div>
            <?php foreach ($assetSections as $sectionKey):
                $def = $sections[$sectionKey];
            ?>
            <div class="bg-white dark:bg-gray-800 border-x border-gray-200 dark:border-gray-700 px-5 py-4 last:rounded-b-2xl last:border-b">
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
                <!-- Section subtotal -->
                <div class="mt-3 pt-3 border-t border-dashed border-gray-200 dark:border-gray-600 flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400"><?= htmlspecialchars($def['total_label'], ENT_QUOTES) ?></span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white" id="subtotal-<?= $sectionKey ?>">RM 0.00</span>
                </div>
            </div>
            <?php endforeach; ?>
            <!-- Total Assets -->
            <div class="bg-brand-50 dark:bg-brand-900/20 border border-brand-200 dark:border-brand-700 rounded-b-2xl -mt-px px-5 py-3 flex justify-between items-center">
                <span class="text-sm font-bold text-brand-800 dark:text-brand-300"><?= __('bs_total_assets') ?></span>
                <span class="text-sm font-bold text-brand-800 dark:text-brand-300" id="total-assets">RM 0.00</span>
            </div>
        </div>

        <!-- EQUITY -->
        <div class="mb-4">
            <div class="bg-gold-500 dark:bg-gold-700 text-white text-xs font-bold uppercase tracking-widest px-5 py-2.5 rounded-t-2xl"
                 style="background-color:#C4A028">
                <?= __('bs_equity') ?>
            </div>
            <?php foreach ($equitySections as $sectionKey):
                $def = $sections[$sectionKey];
            ?>
            <div class="bg-white dark:bg-gray-800 border-x border-b border-gray-200 dark:border-gray-700 px-5 py-4">
                <div class="space-y-2.5">
                    <?php foreach ($def['items'] as $itemKey => $itemLabel): ?>
                    <div class="flex items-center gap-3">
                        <label class="flex-1 text-sm text-gray-700 dark:text-gray-300">
                            <?= htmlspecialchars($itemLabel, ENT_QUOTES) ?>
                            <?php if ($itemKey === 'accum_losses'): ?>
                                <span class="text-xs text-gray-400 dark:text-gray-500">(<?= __('bs_enter_positive') ?>)</span>
                            <?php endif; ?>
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
            <!-- Total Equity -->
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
            <!-- Total Liabilities + Total Equity & Liabilities -->
            <div class="border-x border-b border-gray-200 dark:border-gray-700 px-5 py-3 flex justify-between items-center bg-red-50 dark:bg-red-900/10">
                <span class="text-sm font-bold text-gray-800 dark:text-gray-200"><?= __('bs_total_liabilities') ?></span>
                <span class="text-sm font-bold text-gray-800 dark:text-gray-200" id="total-liab">RM 0.00</span>
            </div>
            <div class="rounded-b-2xl border-x border-b border-gray-200 dark:border-gray-700 px-5 py-3 flex justify-between items-center bg-brand-600 dark:bg-brand-700">
                <span class="text-sm font-bold text-white"><?= __('bs_total_equity_liabilities') ?></span>
                <span class="text-sm font-bold text-white" id="total-eq-liab">RM 0.00</span>
            </div>
        </div>

        <!-- Save button -->
        <div class="flex justify-end">
            <button type="submit"
                    class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl transition-colors">
                <?= __('balance_sheet_save') ?>
            </button>
        </div>

    </form>
</div>

<script>
// Define sections and their items from PHP
const BS_SECTIONS = <?php
    $js = [];
    foreach ($sections as $sKey => $sDef) {
        $js[$sKey] = array_keys($sDef['items']);
    }
    echo json_encode($js);
?>;

const ASSET_SECTIONS   = ['non_current_asset', 'current_asset'];
const EQUITY_SECTIONS  = ['equity'];
const LIAB_SECTIONS    = ['non_current_liability', 'current_liability'];

function rmFmt(n) {
    return 'RM ' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function calcTotals() {
    let totalAssets = 0, totalEquity = 0, totalLiab = 0;

    Object.entries(BS_SECTIONS).forEach(([section, keys]) => {
        let sub = 0;
        keys.forEach(key => {
            const inp = document.querySelector(`input[name="bs[${section}][${key}]"]`);
            let v = inp ? parseFloat(inp.value) || 0 : 0;
            // Accumulated losses is negative equity
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

// Attach listeners
document.querySelectorAll('input[type="number"]').forEach(inp => {
    inp.addEventListener('input', calcTotals);
});

// Initial calculation on page load
calcTotals();
</script>
