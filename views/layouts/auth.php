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
                            50:  '#eaf2ee',
                            100: '#cce0d5',
                            200: '#99c2ac',
                            300: '#5f9d7d',
                            400: '#3a7a58',
                            500: '#245e40',
                            600: '#1a4a2e',
                            700: '#163020',
                            800: '#0f2318',
                            900: '#091610',
                        },
                        gold: {
                            50:  '#fdf8e7',
                            100: '#faefc4',
                            200: '#f4d87a',
                            300: '#eabc35',
                            400: '#d4a820',
                            500: '#C4A028',
                            600: '#a88820',
                            700: '#8a6e18',
                            800: '#6b5412',
                            900: '#4c3c0c',
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
            <?php $siteLogo = (new \Models\Setting())->get('site_logo', ''); ?>
            <div class="inline-flex items-center justify-center mb-4">
                <?php if (!empty($siteLogo) && file_exists(BASE_PATH . '/' . $siteLogo)): ?>
                    <img src="<?= BASE_URI ?>/<?= htmlspecialchars($siteLogo, ENT_QUOTES) ?>"
                         alt="Logo" class="h-24 w-auto max-w-[200px] object-contain rounded-2xl shadow-lg">
                <?php else: ?>
                    <img src="<?= BASE_URI ?>/assets/img/logo.svg"
                         alt="ezkira" class="w-24 h-24 rounded-2xl shadow-lg">
                <?php endif; ?>
            </div>
            <p class="text-brand-200 dark:text-gray-400 text-sm mt-1">Finance Monitoring for SMEs</p>
        </div>

        <!-- Language + Dark mode controls -->
        <div class="flex justify-end gap-2 mb-4">
            <form method="POST" action="<?= BASE_URI ?>/set-lang" class="inline">
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
