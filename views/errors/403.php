<!DOCTYPE html>
<html lang="en" class="<?= isset($_SESSION) && !empty($_SESSION['dark_mode']) ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — <?= defined('APP_NAME') ? htmlspecialchars(APP_NAME) : 'SME Finance Monitor' ?></title>
    <script>tailwind = { config: { darkMode: 'class' } }</script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 min-h-screen flex items-center justify-center font-sans">
    <div class="text-center px-6">
        <p class="text-7xl font-black text-red-500 mb-2">403</p>
        <h1 class="text-2xl font-bold mb-2">Access Denied</h1>
        <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-sm">You do not have permission to access this page.</p>
        <a href="<?= defined('BASE_URI') ? BASE_URI : '' ?>/dashboard"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Go to Dashboard
        </a>
    </div>
</body>
</html>
