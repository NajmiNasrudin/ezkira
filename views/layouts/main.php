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
                        // Secondary — gold (from ezkira logo)
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

    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/60122541050?text=Hi%2C%20saya%20perlukan%20bantuan%20dengan%20EZKIRA."
       target="_blank" rel="noopener"
       class="fixed bottom-6 right-6 z-50 flex items-center justify-center w-14 h-14 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-110"
       title="Hubungi kami di WhatsApp">
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.118 1.528 5.852L.057 23.998l6.305-1.654A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.997a9.953 9.953 0 01-5.073-1.381l-.364-.216-3.742.982.999-3.648-.237-.374A9.953 9.953 0 012.003 12C2.003 6.476 6.476 2.003 12 2.003S21.997 6.476 21.997 12 17.524 21.997 12 21.997z"/>
        </svg>
    </a>

    <script>window.BASE_URI = '<?= BASE_URI ?>';</script>
    <script src="<?= BASE_URI ?>/assets/js/app.js"></script>
</body>
</html>
