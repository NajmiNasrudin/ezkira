<?php
use App\Core\CSRF;
use App\Core\Session;

$lang     = Session::get('lang', 'en');
$isDark   = (bool) Session::get('dark_mode');
$moreOnly = ['/balance-sheet', '/blast'];
?>

<header class="sticky top-0 z-30 bg-gray-50/85 dark:bg-gray-950/85 backdrop-blur-md">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 lg:h-20 flex items-center justify-between gap-3">

        <!-- Mobile brand -->
        <a href="<?= BASE_URI ?>/dashboard" class="lg:hidden flex items-center gap-2.5 shrink-0">
            <?php if ($hasSiteLogo): ?>
                <img src="<?= BASE_URI ?>/<?= htmlspecialchars($siteLogo, ENT_QUOTES) ?>"
                     alt="Logo" class="h-9 w-auto max-w-[140px] object-contain rounded-lg">
            <?php else: ?>
                <img src="<?= BASE_URI ?>/assets/img/logo-mark.svg" alt="ezkira" class="w-9 h-9 rounded-xl">
                <span class="text-base font-extrabold tracking-wide leading-none">
                    <span class="text-gold-500">ez</span><span class="text-brand-700 dark:text-white">kira</span>
                </span>
            <?php endif; ?>
        </a>

        <!-- Desktop page title -->
        <p class="hidden lg:block text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
            <?= htmlspecialchars($pageTitle ?? '', ENT_QUOTES) ?>
        </p>

        <!-- Right controls -->
        <div class="flex items-center gap-2">

            <!-- Language (desktop) -->
            <form method="POST" action="<?= BASE_URI ?>/set-lang" class="hidden lg:block">
                <?= CSRF::field() ?>
                <input type="hidden" name="lang" value="<?= $lang === 'en' ? 'ms' : 'en' ?>">
                <button type="submit" title="<?= __('switch_language') ?>"
                        class="h-10 px-3.5 text-xs font-bold rounded-full bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 shadow-sm hover:text-brand-600 dark:hover:text-sage-300 transition-colors">
                    <?= $lang === 'en' ? 'BM' : 'EN' ?>
                </button>
            </form>

            <!-- Dark Mode Toggle -->
            <button type="button" id="theme-toggle" onclick="toggleDarkMode()"
                    class="w-10 h-10 flex items-center justify-center rounded-full bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 shadow-sm hover:text-brand-600 dark:hover:text-sage-300 transition-colors"
                    aria-label="Toggle dark mode">
                <svg id="icon-sun" class="w-5 h-5 <?= $isDark ? 'block' : 'hidden' ?>"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg id="icon-moon" class="w-5 h-5 <?= $isDark ? 'hidden' : 'block' ?>"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>

            <!-- User / More menu -->
            <div class="relative" id="user-menu-wrapper">
                <button type="button" id="user-menu-btn" onclick="toggleUserMenu()"
                        class="flex items-center gap-2 h-10 pl-1 pr-1 lg:pr-3 rounded-full bg-white dark:bg-gray-800 shadow-sm hover:shadow transition-shadow"
                        aria-label="<?= __('menu_more') ?>">
                    <?php if (!empty($user['profile_image'])): ?>
                        <img src="<?= BASE_URI ?>/<?= htmlspecialchars($user['profile_image'], ENT_QUOTES) ?>"
                             alt="Avatar" class="w-8 h-8 rounded-full object-cover">
                    <?php else: ?>
                        <span class="w-8 h-8 rounded-full bg-brand-600 flex items-center justify-center text-white text-sm font-bold">
                            <?= strtoupper(mb_substr($user['name'] ?? 'U', 0, 1)) ?>
                        </span>
                    <?php endif; ?>
                    <span class="hidden lg:block text-sm font-semibold text-gray-700 dark:text-gray-200 max-w-28 truncate">
                        <?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES) ?>
                    </span>
                    <svg class="hidden lg:block w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="user-dropdown"
                     class="hidden absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700 p-2 z-50">
                    <div class="px-3 py-2.5 mb-1">
                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                            <?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES) ?>
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            <?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES) ?>
                        </p>
                        <span class="inline-flex items-center mt-1.5 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-sage-100 text-brand-700 dark:bg-brand-900/60 dark:text-sage-300">
                            <?= htmlspecialchars($roleLabel, ENT_QUOTES) ?>
                        </span>
                    </div>

                    <!-- Pages not in the mobile bottom bar -->
                    <div class="lg:hidden border-t border-gray-100 dark:border-gray-700 pt-1 mt-1">
                        <?php foreach ($moreOnly as $href): if (!isset($navLinks[$href])) continue; ?>
                            <a href="<?= BASE_URI . $href ?>"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?= $isNavActive($href) ? 'bg-sage-100 text-brand-700 dark:bg-brand-900/50 dark:text-sage-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/60' ?>">
                                <svg class="w-5 h-5 text-brand-600 dark:text-sage-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="<?= $navIcons[$href] ?>"/>
                                </svg>
                                <?= htmlspecialchars($navLinks[$href], ENT_QUOTES) ?>
                            </a>
                        <?php endforeach; ?>
                        <form method="POST" action="<?= BASE_URI ?>/set-lang">
                            <?= CSRF::field() ?>
                            <input type="hidden" name="lang" value="<?= $lang === 'en' ? 'ms' : 'en' ?>">
                            <button type="submit"
                                    class="flex w-full items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/60">
                                <svg class="w-5 h-5 text-brand-600 dark:text-sage-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                                </svg>
                                <?= $lang === 'en' ? 'Bahasa Melayu' : 'English' ?>
                            </button>
                        </form>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-700 pt-1 mt-1">
                        <a href="https://wa.me/60122541050?text=Hi%2C%20saya%20perlukan%20bantuan%20dengan%20EZKIRA."
                           target="_blank" rel="noopener"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/60">
                            <span class="w-5 h-5 rounded-full bg-[#25D366] text-white flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                            </span>
                            <?= __('whatsapp_support') ?>
                        </a>
                        <a href="<?= BASE_URI ?>/profile"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/60">
                            <svg class="w-5 h-5 text-brand-600 dark:text-sage-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="<?= $navIcons['/profile'] ?>"/>
                            </svg>
                            <?= __('profile') ?>
                        </a>
                        <form method="POST" action="<?= BASE_URI ?>/logout">
                            <?= CSRF::field() ?>
                            <button type="submit"
                                    class="flex w-full items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <?= __('logout') ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
