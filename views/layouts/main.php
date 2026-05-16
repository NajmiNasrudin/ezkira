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
                        accent: {
                            50:  '#fdf2f3',
                            100: '#fce4e6',
                            200: '#f9c5ca',
                            300: '#f49aa2',
                            400: '#ec6472',
                            500: '#712F38',
                            600: '#5e2730',
                            700: '#4a1e26',
                            800: '#36151b',
                            900: '#210d11',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URI ?>/assets/css/app.css">
</head>
<body class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 min-h-screen flex flex-col font-sans antialiased">

    <?php include BASE_PATH . '/views/layouts/partials/nav.php'; ?>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php include BASE_PATH . '/views/layouts/partials/flash.php'; ?>
        <?= $content ?>
    </main>

    <?php include BASE_PATH . '/views/layouts/partials/footer.php'; ?>

    <script>window.BASE_URI = '<?= BASE_URI ?>';</script>
    <script src="<?= BASE_URI ?>/assets/js/app.js"></script>
</body>
</html>
