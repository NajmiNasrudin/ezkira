<!DOCTYPE html>
<html lang="<?= htmlspecialchars(\App\Core\Session::get('lang', 'en'), ENT_QUOTES) ?>"
      class="<?= \App\Core\Session::get('dark_mode') ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= \App\Core\CSRF::generate() ?>">
    <?php include __DIR__ . '/partials/head-icons.php'; ?>
    <title><?= htmlspecialchars($pageTitle ?? APP_NAME, ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars(APP_NAME, ENT_QUOTES) ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        // Primary — dark forest green (from ezkira logo)
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
                        // Soft sage accents
                        sage: {
                            50:  '#f2f6f1',
                            100: '#e3ece2',
                            200: '#cfdccf',
                            300: '#a8c3a8',
                            400: '#7fa584',
                            500: '#5f8a66',
                        },
                        // Secondary — gold (from ezkira logo), used sparingly
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
                        // Warm, green-tinted neutrals so every page sits on the cream theme
                        gray: {
                            50:  '#f5f4ef',
                            100: '#edece5',
                            200: '#e2e1d8',
                            300: '#cecdc3',
                            400: '#9a9b92',
                            500: '#6d7068',
                            600: '#51554e',
                            700: '#353b35',
                            800: '#1f2621',
                            900: '#161c18',
                            950: '#0d120f',
                        },
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    borderRadius: {
                        'xl':  '0.875rem',
                        '2xl': '1.375rem',
                        '3xl': '1.75rem',
                    },
                    boxShadow: {
                        'sm':   '0 1px 2px rgba(22,48,32,0.04), 0 1px 3px rgba(22,48,32,0.04)',
                        'soft': '0 8px 24px -12px rgba(22,48,32,0.18)',
                    },
                }
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URI ?>/assets/css/app.css?v=<?= filemtime(BASE_PATH . '/assets/css/app.css') ?>">
</head>
<body class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 min-h-screen font-sans antialiased">

    <?php include BASE_PATH . '/views/layouts/partials/nav.php'; ?>

    <div class="lg:pl-64 min-h-screen flex flex-col">
        <?php include BASE_PATH . '/views/layouts/partials/topbar.php'; ?>

        <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-4 lg:pt-6 pb-10 lg:pb-12">
            <?php include BASE_PATH . '/views/layouts/partials/flash.php'; ?>
            <?= $content ?>
        </main>

        <?php include BASE_PATH . '/views/layouts/partials/footer.php'; ?>
    </div>

    <?php include BASE_PATH . '/views/layouts/partials/bottom-nav.php'; ?>

    <?php include BASE_PATH . '/views/layouts/partials/help-drawer.php'; ?>

    <script>window.BASE_URI = '<?= BASE_URI ?>';</script>
    <script src="<?= BASE_URI ?>/assets/js/app.js?v=<?= filemtime(BASE_PATH . '/assets/js/app.js') ?>"></script>
</body>
</html>
