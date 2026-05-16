<!DOCTYPE html>
<html lang="<?= htmlspecialchars(\App\Core\Session::get('lang', 'en'), ENT_QUOTES) ?>"
      class="<?= \App\Core\Session::get('dark_mode') ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= \App\Core\CSRF::generate() ?>">
    <title><?= htmlspecialchars($pageTitle ?? APP_NAME, ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars(APP_NAME, ENT_QUOTES) ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:  '#f0f7f2',
                            100: '#d9ede0',
                            200: '#b3dbc1',
                            300: '#7fc49e',
                            400: '#52a872',
                            500: '#458458',
                            600: '#3a7049',
                            700: '#2d5a3a',
                            800: '#1f402a',
                            900: '#122518',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URI ?>/assets/css/app.css">
</head>
<body class="bg-gradient-to-br from-brand-500 via-brand-700 to-brand-900 dark:from-gray-900 dark:via-gray-900 dark:to-gray-950 min-h-screen flex items-center justify-center font-sans antialiased px-4 py-12">

    <div class="w-full max-w-md">
        <!-- Logo / Brand -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-white dark:bg-gray-800 rounded-2xl shadow-lg mb-4">
                <svg class="w-8 h-8 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white dark:text-gray-100"><?= htmlspecialchars(APP_NAME) ?></h1>
            <p class="text-brand-200 dark:text-gray-400 text-sm mt-1">Finance Monitoring for SMEs</p>
        </div>

        <!-- Language + Dark mode controls -->
        <div class="flex justify-end gap-2 mb-4">
            <form method="POST" action="<?= BASE_URI ?>/lang/switch" class="inline">
                <?= \App\Core\CSRF::field() ?>
                <?php $currentLang = \App\Core\Session::get('lang', 'en'); ?>
                <input type="hidden" name="lang" value="<?= $currentLang === 'en' ? 'ms' : 'en' ?>">
                <button type="submit"
                        class="text-xs font-medium px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-colors">
                    <?= $currentLang === 'en' ? 'BM' : 'EN' ?>
                </button>
            </form>
            <button onclick="toggleDarkMode()"
                    class="p-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-colors"
                    id="theme-toggle" aria-label="Toggle dark mode">
                <?php if (\App\Core\Session::get('dark_mode')): ?>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <?php else: ?>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                <?php endif; ?>
            </button>
        </div>

        <!-- Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8">
            <?php include BASE_PATH . '/views/layouts/partials/flash.php'; ?>
            <?= $content ?>
        </div>

    </div>

    <script>window.BASE_URI = '<?= BASE_URI ?>';</script>
    <script src="<?= BASE_URI ?>/assets/js/app.js"></script>
</body>
</html>
