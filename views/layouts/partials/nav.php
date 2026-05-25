<?php
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Session;

$user        = Auth::user();
$role        = $user['role'] ?? 'guest';
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$currentPath = rtrim($currentPath, '/') ?: '/';

$roleLabel = match($role) {
    'admin'  => __('role_admin'),
    'team'   => __('role_team'),
    default  => __('role_client'),
};

$roleColor = match($role) {
    'admin'  => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
    'team'   => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
    default  => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
};

$navLinks = [
    '/dashboard' => __('dashboard'),
    '/revenue'   => __('revenue'),
    '/expenses'  => __('expenses'),
    '/blast'     => 'WhatsApp',
    '/profile'   => __('profile'),
];

function navActive(string $path, string $current): string {
    return str_starts_with($current, $path)
        ? 'text-brand-600 dark:text-brand-400 font-semibold border-b-2 border-brand-600 dark:border-brand-400'
        : 'text-gray-600 dark:text-gray-400 hover:text-brand-600 dark:hover:text-brand-400 font-medium';
}
?>

<header class="sticky top-0 z-50 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- Brand -->
            <a href="<?= BASE_URI ?>/dashboard"
               class="flex items-center gap-2.5 shrink-0">
                <div class="w-8 h-8 bg-brand-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <rect x="1.5" y="11" width="4" height="7.5" rx="1"/>
                        <rect x="7.5" y="6.5" width="4" height="12" rx="1"/>
                        <rect x="13.5" y="2.5" width="4" height="16" rx="1"/>
                    </svg>
                </div>
                <span class="text-sm font-bold text-gray-900 dark:text-white hidden sm:block leading-tight tracking-wide">
                    <?= htmlspecialchars(APP_NAME, ENT_QUOTES) ?>
                </span>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center gap-6">
                <?php foreach ($navLinks as $href => $label): ?>
                    <a href="<?= BASE_URI . $href ?>"
                       class="text-sm pb-0.5 transition-colors <?= navActive($href, $currentPath) ?>">
                        <?= htmlspecialchars($label, ENT_QUOTES) ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <!-- Right Controls -->
            <div class="flex items-center gap-3">

                <!-- Language Switcher -->
                <form method="POST" action="<?= BASE_URI ?>/set-lang" class="hidden sm:block">
                    <?= CSRF::field() ?>
                    <?php $lang = Session::get('lang', 'en'); ?>
                    <input type="hidden" name="lang" value="<?= $lang === 'en' ? 'ms' : 'en' ?>">
                    <button type="submit"
                            class="text-xs font-semibold px-2.5 py-1 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
                            title="Switch language">
                        <?= $lang === 'en' ? 'BM' : 'EN' ?>
                    </button>
                </form>

                <!-- Dark Mode Toggle -->
                <button id="theme-toggle" onclick="toggleDarkMode()"
                        class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        aria-label="Toggle dark mode">
                    <!-- Sun (shown in dark mode) -->
                    <svg id="icon-sun" class="w-5 h-5 <?= Session::get('dark_mode') ? 'block' : 'hidden' ?>"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <!-- Moon (shown in light mode) -->
                    <svg id="icon-moon" class="w-5 h-5 <?= Session::get('dark_mode') ? 'hidden' : 'block' ?>"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                <!-- User Avatar Dropdown -->
                <div class="relative" id="user-menu-wrapper">
                    <button id="user-menu-btn"
                            onclick="toggleUserMenu()"
                            class="flex items-center gap-2 p-1 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <?php if (!empty($user['profile_image'])): ?>
                            <img src="<?= BASE_URI ?>/<?= htmlspecialchars($user['profile_image'], ENT_QUOTES) ?>"
                                 alt="Avatar" class="w-8 h-8 rounded-full object-cover ring-2 ring-brand-200 dark:ring-brand-800">
                        <?php else: ?>
                            <div class="w-8 h-8 rounded-full bg-brand-600 flex items-center justify-center text-white text-sm font-semibold ring-2 ring-brand-200 dark:ring-brand-800">
                                <?= strtoupper(mb_substr($user['name'] ?? 'U', 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <span class="hidden sm:block text-sm font-medium text-gray-700 dark:text-gray-300 max-w-24 truncate">
                            <?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES) ?>
                        </span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="user-dropdown"
                         class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50">
                        <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                <?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES) ?>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                <?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES) ?>
                            </p>
                            <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-medium <?= $roleColor ?>">
                                <?= htmlspecialchars($roleLabel, ENT_QUOTES) ?>
                            </span>
                        </div>
                        <a href="<?= BASE_URI ?>/profile"
                           class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <?= __('profile') ?>
                        </a>
                        <div class="border-t border-gray-100 dark:border-gray-700 mt-1 pt-1">
                            <form method="POST" action="<?= BASE_URI ?>/logout">
                                <?= CSRF::field() ?>
                                <button type="submit"
                                        class="flex w-full items-center gap-2 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    <?= __('logout') ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Hamburger (mobile) -->
                <button id="hamburger" onclick="toggleMobileMenu()"
                        class="md:hidden p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200 dark:border-gray-800 py-3 space-y-1">
            <?php foreach ($navLinks as $href => $label): ?>
                <a href="<?= BASE_URI . $href ?>"
                   class="block px-3 py-2 rounded-lg text-sm transition-colors <?= str_starts_with($currentPath, $href) ? 'bg-brand-50 dark:bg-brand-900/20 text-brand-600 dark:text-brand-400 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800' ?>">
                    <?= htmlspecialchars($label, ENT_QUOTES) ?>
                </a>
            <?php endforeach; ?>
            <!-- Mobile language switcher -->
            <form method="POST" action="<?= BASE_URI ?>/set-lang" class="px-3 pt-1">
                <?= CSRF::field() ?>
                <?php $lang = Session::get('lang', 'en'); ?>
                <input type="hidden" name="lang" value="<?= $lang === 'en' ? 'ms' : 'en' ?>">
                <button type="submit" class="text-sm text-gray-600 dark:text-gray-400 hover:text-brand-600">
                    <?= $lang === 'en' ? '🌐 Bahasa Melayu' : '🌐 English' ?>
                </button>
            </form>
        </div>
    </div>
</header>
