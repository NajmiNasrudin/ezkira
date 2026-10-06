<?php
$bottomTabs = [
    ['/dashboard', __('tab_home')],
    ['/revenue',   __('tab_revenue')],
    null, // centre quick-add button
    ['/expenses',  __('tab_expenses')],
    ['/profile',   __('tab_profile')],
];
?>

<!-- Pure CSS toggle (checkbox + label) for the quick-add sheet — no JS dependency -->
<style>
#quick-add-toggle { position:fixed; bottom:0; left:0; width:1px; height:1px; opacity:0; pointer-events:none; }
#quick-add-sheet, #quick-add-backdrop { display:none; }
#quick-add-toggle:checked ~ #quick-add-sheet { display:block; }
#quick-add-toggle:checked ~ #quick-add-backdrop { display:block; }
#quick-add-toggle:checked ~ nav .quick-add-icon { transform: rotate(45deg); }
</style>

<div class="lg:hidden">
    <input type="checkbox" id="quick-add-toggle">

    <label for="quick-add-toggle" id="quick-add-backdrop" class="fixed inset-0 z-40 bg-black/30 backdrop-blur-[2px]"></label>

    <div id="quick-add-sheet" class="ez-quick-sheet fixed left-4 right-4 z-40">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-soft p-3 grid grid-cols-2 gap-3 max-w-sm mx-auto">
            <a href="<?= BASE_URI ?>/revenue#add-sale"
               class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-sage-50 dark:bg-gray-700/60 hover:bg-sage-100 dark:hover:bg-gray-700 transition-colors">
                <span class="w-11 h-11 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="<?= $navIcons['/revenue'] ?>"/></svg>
                </span>
                <span class="text-sm font-semibold text-gray-800 dark:text-gray-100 text-center"><?= __('add_sale') ?></span>
            </a>
            <a href="<?= BASE_URI ?>/expenses#add-expense"
               class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-sage-50 dark:bg-gray-700/60 hover:bg-sage-100 dark:hover:bg-gray-700 transition-colors">
                <span class="w-11 h-11 rounded-full bg-red-100 dark:bg-red-900/40 text-red-500 dark:text-red-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="<?= $navIcons['/expenses'] ?>"/></svg>
                </span>
                <span class="text-sm font-semibold text-gray-800 dark:text-gray-100 text-center"><?= __('add_expense') ?></span>
            </a>
        </div>
    </div>

    <nav class="ez-bottom-nav fixed bottom-0 inset-x-0 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-t border-gray-100 dark:border-gray-800">
        <div class="grid grid-cols-5 items-end h-[68px] max-w-md mx-auto px-2">
            <?php foreach ($bottomTabs as $tab): ?>
                <?php if ($tab === null): ?>
                    <div class="flex justify-center">
                        <label for="quick-add-toggle" aria-label="<?= __('quick_add') ?>"
                               class="-translate-y-4 w-14 h-14 rounded-full bg-brand-700 dark:bg-sage-300 text-white dark:text-brand-800 shadow-soft ring-4 ring-gray-50 dark:ring-gray-950 flex items-center justify-center cursor-pointer active:scale-95 transition-transform">
                            <svg class="quick-add-icon w-6 h-6 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/>
                            </svg>
                        </label>
                    </div>
                <?php else:
                    [$href, $label] = $tab;
                    $active = $isNavActive($href);
                ?>
                    <a href="<?= BASE_URI . $href ?>"
                       class="flex flex-col items-center justify-center gap-1 h-full pb-1.5 <?= $active ? 'text-brand-700 dark:text-sage-300' : 'text-gray-400 dark:text-gray-500' ?>">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="<?= $active ? '2.2' : '1.7' ?>">
                            <path stroke-linecap="round" stroke-linejoin="round" d="<?= $navIcons[$href] ?>"/>
                        </svg>
                        <span class="text-[10.5px] leading-none truncate max-w-full <?= $active ? 'font-bold' : 'font-medium' ?>"><?= htmlspecialchars($label, ENT_QUOTES) ?></span>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </nav>
</div>
