<?php
/**
 * ezkira landing page (2026 redesign). Standalone HTML shell, no app layout.
 * BM is the default; [data-en] holds the English version swapped in by the language toggle.
 */
$appUrl  = defined('APP_URL') ? rtrim(APP_URL, '/') : 'https://ezkira.com';
$base    = defined('BASE_URI') ? BASE_URI : '';
$preview = !empty($landingPreview);

/** Bilingual inline text: BM shown by default, EN swapped in by JS. */
$L = fn(string $ms, string $en): string =>
    '<span data-en="' . htmlspecialchars($en, ENT_QUOTES, 'UTF-8') . '">' . $ms . '</span>';

$icon = fn(string $d, string $cls = 'w-5 h-5'): string =>
    '<svg class="' . $cls . '" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="' . $d . '"/></svg>';

$I = [
    'home'    => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    'trend'   => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
    'receipt' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z',
    'scale'   => 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3',
    'user'    => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    'plus'    => 'M12 5v14m7-7H5',
    'camera'  => 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9zM15 13a3 3 0 11-6 0 3 3 0 016 0z',
    'doc'     => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'chart'   => 'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055zM20.488 9H15V3.512A9.025 9.025 0 0120.488 9z',
    'wallet'  => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
    'shield'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    'check'   => 'M5 13l4 4L19 7',
    'arrow'   => 'M13 7l5 5m0 0l-5 5m5-5H6',
    'down'    => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4',
    'bolt'    => 'M13 10V3L4 14h7v7l9-11h-7z',
    'globe'   => 'M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129',
    'lock'    => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
];
$logoUrl = function_exists('asset_url') ? asset_url('assets/img/logo-mark.svg') : $base . '/assets/img/logo-mark.svg';
?>
<!DOCTYPE html>
<html lang="ms" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ezkira — Kira Untung Bisnes Tanpa Pening Kepala</title>
    <meta name="description" content="Rekod jualan, perbelanjaan dan resit di satu tempat. ezkira kira untung rugi, bajet dan Kunci Kira-Kira secara automatik untuk peniaga online dan PKS Malaysia.">
    <?php if ($preview): ?>
    <meta name="robots" content="noindex, nofollow">
    <?php else: ?>
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= $appUrl ?>/">
    <?php endif; ?>
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= $appUrl ?>/">
    <meta property="og:title" content="ezkira — Kira Untung Bisnes Tanpa Pening Kepala">
    <meta property="og:description" content="Rekod jualan, perbelanjaan dan resit. ezkira kira untung rugi dan laporan kewangan secara automatik.">
    <meta property="og:image" content="<?= $appUrl ?>/assets/img/icons/icon-512.png">
    <meta property="og:locale" content="ms_MY">
    <?php include __DIR__ . '/../layouts/partials/head-icons.php'; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#eaf2ee', 100: '#cce0d5', 200: '#99c2ac', 300: '#5f9d7d', 400: '#3a7a58', 500: '#245e40', 600: '#1a4a2e', 700: '#163020', 800: '#0f2318', 900: '#091610' },
                        sage:  { 50: '#f2f6f1', 100: '#e3ece2', 200: '#cfdccf', 300: '#a8c3a8', 400: '#7fa584', 500: '#5f8a66' },
                        gold:  { 50: '#fdf8e7', 100: '#faefc4', 200: '#f4d87a', 300: '#eabc35', 400: '#d4a820', 500: '#C4A028' },
                        gray:  { 50: '#f5f4ef', 100: '#edece5', 200: '#e2e1d8', 300: '#cecdc3', 400: '#9a9b92', 500: '#6d7068', 600: '#51554e', 700: '#353b35', 800: '#1f2621', 900: '#161c18' },
                    },
                    boxShadow: {
                        soft: '0 10px 30px -12px rgba(22,48,32,0.18)',
                        float: '0 40px 80px -30px rgba(22,48,32,0.35)',
                    },
                },
            },
        };
    </script>
    <style>
        body { font-feature-settings: "tnum" 0; }
        .ez-num { font-variant-numeric: tabular-nums; }
        .phone { border-radius: 2.4rem; border: 7px solid #111; box-shadow: 0 40px 80px -30px rgba(22,48,32,0.45); }
        .phone-notch { width: 34%; height: 18px; border-radius: 0 0 12px 12px; background: #111; }
        .blob { filter: blur(0); border-radius: 9999px; }
        details summary::-webkit-details-marker { display: none; }
        #ez-mobile-toggle:checked ~ #ez-mobile-menu { display: block; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">

<?php if ($preview): ?>
<div class="bg-gold-100 text-gold-500 text-center text-xs font-semibold py-1.5" style="color:#6b5412">
    Pratonton landing page baharu — belum dipaparkan kepada pengunjung.
</div>
<?php endif; ?>

<!-- ============ NAV ============ -->
<header class="sticky top-0 z-40 bg-gray-50/85 backdrop-blur-md border-b border-gray-200/70">
    <input type="checkbox" id="ez-mobile-toggle" class="hidden">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
        <a href="<?= $base ?>/" class="flex items-center gap-2.5 shrink-0">
            <img src="<?= $logoUrl ?>" alt="ezkira" class="w-9 h-9 rounded-xl">
            <span class="text-lg font-extrabold tracking-wide"><span style="color:#C4A028">ez</span><span class="text-brand-700">kira</span></span>
        </a>
        <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-gray-600">
            <a href="#ciri" class="hover:text-brand-700"><?= $L('Ciri-ciri', 'Features') ?></a>
            <a href="#rupa-app" class="hover:text-brand-700"><?= $L('Rupa App', 'The App') ?></a>
            <a href="#harga" class="hover:text-brand-700"><?= $L('Harga', 'Pricing') ?></a>
            <a href="#soalan" class="hover:text-brand-700"><?= $L('Soalan Lazim', 'FAQ') ?></a>
        </nav>
        <div class="flex items-center gap-2">
            <div class="hidden sm:flex items-center rounded-full bg-white shadow-sm p-0.5 text-xs font-bold">
                <button type="button" data-lang-btn="ms" onclick="ezSetLang('ms')" class="px-2.5 py-1 rounded-full">BM</button>
                <button type="button" data-lang-btn="en" onclick="ezSetLang('en')" class="px-2.5 py-1 rounded-full">EN</button>
            </div>
            <a href="<?= $base ?>/login" class="hidden sm:inline-flex h-10 items-center px-4 text-sm font-semibold text-gray-700 hover:text-brand-700"><?= $L('Log Masuk', 'Log in') ?></a>
            <a href="<?= $base ?>/register" class="inline-flex h-10 items-center px-5 rounded-full bg-brand-700 hover:bg-brand-600 text-white text-sm font-semibold shadow-sm"><?= $L('Daftar Percuma', 'Sign up free') ?></a>
            <label for="ez-mobile-toggle" class="md:hidden w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm cursor-pointer" aria-label="Menu">
                <?= $icon('M4 6h16M4 12h16M4 18h16') ?>
            </label>
        </div>
    </div>
    <div id="ez-mobile-menu" class="hidden md:hidden border-t border-gray-200 bg-gray-50 px-4 py-3 space-y-1 text-sm font-semibold">
        <a href="#ciri" class="block px-3 py-2.5 rounded-xl hover:bg-white" onclick="ezCloseMenu()"><?= $L('Ciri-ciri', 'Features') ?></a>
        <a href="#rupa-app" class="block px-3 py-2.5 rounded-xl hover:bg-white" onclick="ezCloseMenu()"><?= $L('Rupa App', 'The App') ?></a>
        <a href="#harga" class="block px-3 py-2.5 rounded-xl hover:bg-white" onclick="ezCloseMenu()"><?= $L('Harga', 'Pricing') ?></a>
        <a href="#soalan" class="block px-3 py-2.5 rounded-xl hover:bg-white" onclick="ezCloseMenu()"><?= $L('Soalan Lazim', 'FAQ') ?></a>
        <a href="<?= $base ?>/login" class="block px-3 py-2.5 rounded-xl hover:bg-white"><?= $L('Log Masuk', 'Log in') ?></a>
        <div class="flex gap-2 px-3 pt-2">
            <button type="button" data-lang-btn="ms" onclick="ezSetLang('ms')" class="px-3 py-1.5 rounded-full text-xs font-bold bg-white">BM</button>
            <button type="button" data-lang-btn="en" onclick="ezSetLang('en')" class="px-3 py-1.5 rounded-full text-xs font-bold bg-white">EN</button>
        </div>
    </div>
</header>

<main>
<!-- ============ HERO ============ -->
<section class="relative overflow-hidden">
    <div class="blob absolute -right-40 -top-24 w-[520px] h-[520px] bg-sage-200/70"></div>
    <div class="blob absolute right-10 bottom-0 w-[340px] h-[340px] bg-gold-100/80"></div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 pt-12 sm:pt-16 pb-16 grid lg:grid-cols-12 gap-10 lg:gap-8 items-center">
        <div class="lg:col-span-5">
            <span class="inline-flex items-center gap-2 pl-1 pr-3 py-1 rounded-full bg-white shadow-sm text-xs font-semibold text-brand-700">
                <span class="px-2 py-0.5 rounded-full bg-brand-700 text-white text-[10px] tracking-wide"><?= $L('BAHARU', 'NEW') ?></span>
                <?= $L('Rupa baharu ezkira, lebih ringkas &amp; laju', 'The new ezkira — simpler and faster') ?>
            </span>
            <h1 class="mt-5 text-[2.6rem] leading-[1.08] sm:text-6xl lg:text-[3.4rem] font-extrabold tracking-tight text-gray-900">
                <?= $L('Kira untung bisnes,<br><span class="text-brand-400">tanpa pening kepala.</span>', 'Know your real profit,<br><span class="text-brand-400">without the headache.</span>') ?>
            </h1>
            <p class="mt-5 text-base sm:text-lg text-gray-600 leading-relaxed max-w-lg">
                <?= $L(
                    'Rekod jualan, perbelanjaan dan resit di satu tempat. ezkira kira untung rugi, bajet dan Kunci Kira-Kira secara automatik — dibina untuk peniaga online dan PKS Malaysia.',
                    'Record sales, expenses and receipts in one place. ezkira works out your profit, budget and balance sheet automatically — built for Malaysian online sellers and SMEs.'
                ) ?>
            </p>
            <div class="mt-7 flex flex-col sm:flex-row gap-3">
                <a href="<?= $base ?>/register" class="inline-flex items-center justify-center gap-2 h-12 px-6 rounded-full bg-brand-700 hover:bg-brand-600 text-white font-semibold shadow-soft">
                    <?= $L('Daftar Percuma', 'Sign up free') ?> <?= $icon($I['arrow'], 'w-4 h-4') ?>
                </a>
                <a href="#rupa-app" class="inline-flex items-center justify-center gap-2 h-12 px-6 rounded-full bg-white hover:bg-gray-100 text-gray-800 font-semibold shadow-sm">
                    <?= $L('Lihat rupa app', 'See the app') ?>
                </a>
            </div>
            <ul class="mt-7 flex flex-wrap gap-x-5 gap-y-2 text-sm text-gray-600">
                <li class="flex items-center gap-1.5"><span class="text-brand-400"><?= $icon($I['check'], 'w-4 h-4') ?></span><?= $L('Key-in percuma selamanya', 'Key-in free forever') ?></li>
                <li class="flex items-center gap-1.5"><span class="text-brand-400"><?= $icon($I['check'], 'w-4 h-4') ?></span><?= $L('Log masuk dengan Google', 'Sign in with Google') ?></li>
                <li class="flex items-center gap-1.5"><span class="text-brand-400"><?= $icon($I['check'], 'w-4 h-4') ?></span>BM &amp; English</li>
            </ul>
        </div>

        <!-- Desktop app mockup: the real dashboard layout -->
        <div class="lg:col-span-7">
            <div class="relative rounded-[1.6rem] bg-white shadow-float ring-1 ring-black/5 overflow-hidden">
                <div class="flex items-center gap-2 px-4 h-9 bg-gray-100 border-b border-gray-200">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#ff5f57]"></span><span class="w-2.5 h-2.5 rounded-full bg-[#febc2e]"></span><span class="w-2.5 h-2.5 rounded-full bg-[#28c840]"></span>
                    <span class="ml-3 px-3 py-0.5 rounded-full bg-white text-[10px] text-gray-500">ezkira.com/dashboard</span>
                </div>
                <div class="flex text-[10px] sm:text-[11px]">
                    <aside class="hidden sm:flex w-36 shrink-0 flex-col gap-1 bg-brand-700 p-3 text-white/70">
                        <div class="flex items-center gap-1.5 mb-3"><img src="<?= $logoUrl ?>" alt="" class="w-6 h-6 rounded-md ring-1 ring-white/20"><span class="font-extrabold text-xs"><span style="color:#D4A820">ez</span><span class="text-white">kira</span></span></div>
                        <span class="flex items-center gap-1.5 px-2 py-1.5 rounded-lg bg-sage-200 text-brand-800 font-bold"><?= $icon($I['home'], 'w-3.5 h-3.5') ?><?= $L('Papan Pemuka', 'Dashboard') ?></span>
                        <span class="flex items-center gap-1.5 px-2 py-1.5"><?= $icon($I['trend'], 'w-3.5 h-3.5') ?><?= $L('Pendapatan', 'Revenue') ?></span>
                        <span class="flex items-center gap-1.5 px-2 py-1.5"><?= $icon($I['receipt'], 'w-3.5 h-3.5') ?><?= $L('Perbelanjaan', 'Expenses') ?></span>
                        <span class="flex items-center gap-1.5 px-2 py-1.5"><?= $icon($I['scale'], 'w-3.5 h-3.5') ?><?= $L('Kunci Kira-Kira', 'Balance Sheet') ?></span>
                        <span class="flex items-center gap-1.5 px-2 py-1.5"><?= $icon($I['user'], 'w-3.5 h-3.5') ?><?= $L('Profil', 'Profile') ?></span>
                    </aside>
                    <div class="flex-1 min-w-0 p-3 sm:p-4 bg-gray-50 space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm sm:text-base font-extrabold text-gray-900"><?= $L('Selamat pagi, Aina', 'Good morning, Aina') ?></p>
                                <p class="text-gray-500"><?= $L('Jom pantau kewangan bisnes anda', "Let's keep your finances on track") ?></p>
                            </div>
                            <div class="hidden sm:flex rounded-full bg-white p-0.5 shadow-sm">
                                <span class="px-2 py-1 text-gray-500"><?= $L('Harian', 'Daily') ?></span>
                                <span class="px-2 py-1 rounded-full bg-brand-700 text-white font-semibold"><?= $L('Bulanan', 'Monthly') ?></span>
                                <span class="px-2 py-1 text-gray-500"><?= $L('Tahunan', 'Annual') ?></span>
                            </div>
                        </div>
                        <div class="grid grid-cols-5 gap-3">
                            <div class="col-span-5 sm:col-span-3 relative overflow-hidden rounded-2xl bg-brand-700 text-white p-3.5">
                                <div class="absolute -right-8 -top-8 w-28 h-28 rounded-full bg-white/5"></div>
                                <p class="text-white/75"><?= $L('Keuntungan Bersih · Bulanan', 'Net Profit · Monthly') ?></p>
                                <p class="ez-num mt-1 font-extrabold leading-none"><span class="text-xs text-white/75 align-top">RM</span><span class="text-2xl sm:text-3xl">8,420</span><span class="text-base text-white/70">.50</span></p>
                                <span class="inline-block mt-1.5 px-1.5 py-0.5 rounded-full bg-sage-300/20 text-sage-200 font-bold">▲ 33.9% Margin</span>
                                <div class="mt-3 grid grid-cols-2 gap-2">
                                    <div class="rounded-xl bg-white/10 p-2"><p class="text-white/70"><?= $L('Pendapatan', 'Revenue') ?></p><p class="ez-num font-bold text-xs sm:text-sm">24,860.00</p></div>
                                    <div class="rounded-xl bg-white/10 p-2"><p class="text-white/70"><?= $L('Perbelanjaan', 'Expenses') ?></p><p class="ez-num font-bold text-xs sm:text-sm">16,439.50</p></div>
                                </div>
                            </div>
                            <div class="col-span-5 sm:col-span-2 rounded-2xl bg-white p-3 shadow-sm flex sm:flex-col items-center gap-3">
                                <div class="relative w-20 h-20 shrink-0 rounded-full" style="background:conic-gradient(#1a4a2e 0 30%, #f4f4ef 30% 31%, #5f9d7d 31% 50%, #f4f4ef 50% 51%, #a8c3a8 51% 100%)">
                                    <div class="absolute inset-[22%] rounded-full bg-white flex flex-col items-center justify-center">
                                        <span class="ez-num text-[10px] font-extrabold">RM16.4k</span>
                                    </div>
                                </div>
                                <ul class="space-y-1 w-full">
                                    <li class="flex justify-between"><span class="flex items-center gap-1"><i class="w-1.5 h-1.5 rounded-full bg-brand-600"></i>OPEX</span><b class="ez-num">30%</b></li>
                                    <li class="flex justify-between"><span class="flex items-center gap-1"><i class="w-1.5 h-1.5 rounded-full bg-brand-300"></i>Marketing</span><b class="ez-num">19%</b></li>
                                    <li class="flex justify-between"><span class="flex items-center gap-1"><i class="w-1.5 h-1.5 rounded-full bg-sage-300"></i>COGS</span><b class="ez-num">51%</b></li>
                                </ul>
                            </div>
                        </div>
                        <div class="rounded-2xl bg-white p-3 shadow-sm">
                            <p class="font-bold text-gray-900 mb-1.5"><?= $L('Transaksi Terkini', 'Recent Transactions') ?></p>
                            <?php foreach ([
                                ['in', 'Shopee #10482', 'Shopee', '+ RM 189.00'],
                                ['out', $L('Iklan Facebook', 'Facebook Ads'), 'Marketing', '− RM 250.00'],
                                ['in', 'TikTok Live', 'TikTok Shop', '+ RM 1,240.00'],
                            ] as [$dir, $desc, $cat, $amt]): ?>
                            <div class="flex items-center gap-2 py-1.5 border-t border-gray-100 first:border-0">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center <?= $dir === 'in' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-500' ?>"><?= $icon($dir === 'in' ? 'M7 17L17 7M17 7H9m8 0v8' : 'M17 7L7 17M7 17h8m-8 0V9', 'w-3 h-3') ?></span>
                                <span class="flex-1 min-w-0"><span class="block font-semibold text-gray-800 truncate"><?= $desc ?></span><span class="text-gray-400"><?= $cat ?></span></span>
                                <span class="ez-num font-bold <?= $dir === 'in' ? 'text-emerald-600' : 'text-gray-900' ?>"><?= $amt ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales channels -->
    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 pb-14">
        <p class="text-center text-xs font-semibold uppercase tracking-[0.18em] text-gray-400"><?= $L('Rekod jualan dari mana-mana saluran', 'Record sales from every channel') ?></p>
        <div class="mt-5 flex flex-wrap justify-center gap-x-8 gap-y-3 text-lg sm:text-xl font-extrabold text-gray-400">
            <span>Shopee</span><span>Lazada</span><span>TikTok Shop</span><span>Website</span><span>WhatsApp</span><span><?= $L('Walk-in / Kaunter', 'Walk-in / Counter') ?></span>
        </div>
    </div>
</section>

<!-- ============ FEATURES ============ -->
<section id="ciri" class="py-16 sm:py-24">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="rounded-[2rem] bg-white shadow-sm px-5 sm:px-10 py-12 sm:py-16">
            <div class="text-center max-w-2xl mx-auto">
                <p class="text-xs font-bold tracking-[0.18em] text-brand-400"><?= $L('KENAPA EZKIRA', 'WHY EZKIRA') ?></p>
                <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold tracking-tight"><?= $L('Semua yang bisnes anda perlu, dalam satu app', 'Everything your business needs, in one app') ?></h2>
                <p class="mt-3 text-gray-600"><?= $L('Tak perlu lagi Excel berselerak atau buku tiga lima. Rekod sekali, ezkira kira selebihnya.', 'No more scattered spreadsheets or notebooks. Record once, ezkira does the maths.') ?></p>
            </div>
            <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ([
                    ['trend', 'bg-brand-700 text-white', 'Jualan Pelbagai Saluran', 'Multi-channel Sales',
                        'Shopee, Lazada, TikTok Shop, WhatsApp atau kaunter — termasuk refund dan kaedah bayaran.',
                        'Shopee, Lazada, TikTok Shop, WhatsApp or the counter — refunds and payment methods included.'],
                    ['wallet', 'bg-sage-200 text-brand-700', 'Kawal Perbelanjaan', 'Control Spending',
                        'Asingkan COGS, OPEX, marketing dan aset. Tetapkan bajet % dan nampak bila belanja terlebih.',
                        'Split COGS, OPEX, marketing and assets. Set budget % and see when you overspend.'],
                    ['camera', 'bg-gold-100 text-gold-500', 'Resit Digital', 'Digital Receipts',
                        'Snap resit terus dari telefon. Gambar dikecilkan automatik dan boleh dimuat turun sebagai ZIP.',
                        'Snap receipts straight from your phone. Photos shrink automatically and download as a ZIP.'],
                    ['doc', 'bg-brand-100 text-brand-600', 'Laporan Untung Rugi', 'Profit & Loss Reports',
                        'P&L harian, mingguan, bulanan, tahunan atau ikut tarikh — export CSV untuk akauntan.',
                        'Daily, weekly, monthly, yearly or custom-range P&L — export CSV for your accountant.'],
                    ['scale', 'bg-sage-100 text-brand-600', 'Kunci Kira-Kira', 'Balance Sheet',
                        'Aset, liabiliti dan ekuiti dikira daripada transaksi anda. Rekod modal bila perlu.',
                        'Assets, liabilities and equity calculated from your transactions. Record capital when needed.'],
                    ['chart', 'bg-gray-100 text-gray-700', 'Dashboard & Sasaran', 'Dashboard & Targets',
                        'Bandingkan bulan ini dengan bulan lepas, tetapkan sasaran jualan dan pantau margin untung.',
                        'Compare this month with last, set sales targets and watch your profit margin.'],
                ] as [$ic, $tone, $tMs, $tEn, $dMs, $dEn]): ?>
                <div class="rounded-3xl border border-gray-200/80 bg-gray-50/60 p-6 hover:bg-white hover:shadow-soft transition">
                    <span class="w-12 h-12 rounded-2xl flex items-center justify-center <?= $tone ?>"><?= $icon($I[$ic], 'w-6 h-6') ?></span>
                    <h3 class="mt-5 text-lg font-bold text-gray-900"><?= $L($tMs, $tEn) ?></h3>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed"><?= $L($dMs, $dEn) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-8 flex flex-wrap justify-center gap-2 text-xs font-semibold text-gray-600">
                <?php foreach ([
                    ['globe', 'Bahasa Melayu &amp; English', 'Bahasa Melayu &amp; English'],
                    ['bolt', 'Mod gelap', 'Dark mode'],
                    ['lock', 'Log masuk Google', 'Google sign-in'],
                    ['home', '&quot;Add to Home Screen&quot; macam app', '&quot;Add to Home Screen&quot; like an app'],
                ] as [$ic, $ms, $en]): ?>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-gray-100"><?= $icon($I[$ic], 'w-3.5 h-3.5') ?><?= $L($ms, $en) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ============ SHOWCASE: PHONES ============ -->
<section id="rupa-app" class="pb-16 sm:pb-24 overflow-hidden">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 grid lg:grid-cols-12 gap-12 items-center">
        <div class="lg:col-span-4">
            <p class="text-xs font-bold tracking-[0.18em] text-brand-400"><?= $L('RUPA APP SEBENAR', 'THE ACTUAL APP') ?></p>
            <h2 class="mt-3 text-5xl sm:text-6xl font-extrabold leading-[0.98] tracking-tight">
                <?= $L('Urus bisnes<br>dari <span class="text-brand-400">poket</span><br><span style="color:#C4A028">anda.</span>', 'Run your<br>business from<br>your <span class="text-brand-400">pocket.</span>') ?>
            </h2>
            <div class="mt-5 w-16 h-1.5 rounded-full bg-brand-400"></div>
            <p class="mt-5 text-gray-600 leading-relaxed"><?= $L('Direka untuk telefon dahulu. Tambah jualan dalam beberapa saat — terus di kaunter, atau semasa live.', 'Designed phone-first. Add a sale in seconds — right at the counter, or mid-live.') ?></p>
            <div class="mt-6 flex flex-col sm:flex-row gap-3">
                <a href="<?= $base ?>/register" class="inline-flex items-center justify-center gap-2 h-12 px-6 rounded-full bg-brand-700 hover:bg-brand-600 text-white font-semibold"><?= $L('Cuba Percuma', 'Try it free') ?> <?= $icon($I['arrow'], 'w-4 h-4') ?></a>
                <a href="#ciri" class="inline-flex items-center justify-center h-12 px-6 rounded-full bg-white shadow-sm font-semibold text-gray-800 hover:bg-gray-100"><?= $L('Terokai ciri', 'Explore features') ?></a>
            </div>
            <div class="mt-8 grid grid-cols-3 gap-2 text-center">
                <?php foreach ([
                    ['check', 'Key-in percuma', 'Free key-in', 'Selamanya', 'Forever'],
                    ['lock', 'Tiada caj auto', 'No auto-charge', 'Bayar bila perlu', 'Pay when needed'],
                    ['shield', 'Data peribadi', 'Private data', 'Hanya anda lihat', 'Only you see it'],
                ] as [$ic, $tMs, $tEn, $sMs, $sEn]): ?>
                <div class="rounded-2xl bg-white shadow-sm p-3">
                    <span class="mx-auto w-9 h-9 rounded-full bg-sage-100 text-brand-600 flex items-center justify-center"><?= $icon($I[$ic], 'w-4 h-4') ?></span>
                    <p class="mt-2 text-[11px] font-bold text-gray-900 leading-tight"><?= $L($tMs, $tEn) ?></p>
                    <p class="text-[10px] text-gray-500 leading-tight mt-0.5"><?= $L($sMs, $sEn) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="lg:col-span-8 relative">
            <div class="blob absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[560px] h-[560px] bg-sage-200/60"></div>
            <div class="relative flex justify-center items-end gap-3 sm:gap-5">

                <!-- Phone 1: Revenue -->
                <div class="phone hidden md:block w-[210px] bg-gray-50 overflow-hidden -rotate-3 translate-y-6">
                    <div class="flex justify-center"><div class="phone-notch"></div></div>
                    <div class="px-3 pb-3 pt-2 text-[10px] space-y-2">
                        <div class="flex items-center justify-between"><p class="text-sm font-extrabold"><?= $L('Pendapatan', 'Revenue') ?></p><span class="w-6 h-6 rounded-full bg-brand-700 text-white flex items-center justify-center"><?= $icon($I['plus'], 'w-3 h-3') ?></span></div>
                        <div class="rounded-2xl bg-white p-2.5 shadow-sm">
                            <div class="flex justify-between"><span class="text-gray-500"><?= $L('Sasaran Oktober', 'October target') ?></span><b class="ez-num">83%</b></div>
                            <p class="ez-num text-base font-extrabold mt-0.5">RM 24,860 <span class="text-[9px] font-semibold text-gray-400">/ 30,000</span></p>
                            <div class="mt-1.5 h-1.5 rounded-full bg-gray-100"><div class="h-full w-[83%] rounded-full bg-brand-400"></div></div>
                        </div>
                        <div class="flex gap-1 overflow-hidden">
                            <span class="px-2 py-1 rounded-full bg-brand-700 text-white font-semibold"><?= $L('Semua', 'All') ?></span>
                            <span class="px-2 py-1 rounded-full bg-white">Shopee</span><span class="px-2 py-1 rounded-full bg-white">TikTok</span>
                        </div>
                        <?php foreach ([['Shopee', '#10482', 'FPX', '+189.00', false], ['TikTok Shop', 'Live 9pm', 'E-Wallet', '+1,240.00', false], ['WhatsApp', $L('Tempahan Puan Siti', 'Order from Mrs Siti'), 'Cash', '+75.00', false], ['Shopee', 'Refund #10377', 'FPX', '−39.90', true]] as [$ch, $d, $pm, $amt, $refund]): ?>
                        <div class="rounded-xl bg-white px-2.5 py-2 shadow-sm flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center <?= $refund ? 'bg-red-50 text-red-500' : 'bg-emerald-50 text-emerald-600' ?>"><?= $icon($refund ? 'M17 7L7 17M7 17h8m-8 0V9' : 'M7 17L17 7M17 7H9m8 0v8', 'w-3 h-3') ?></span>
                            <span class="flex-1 min-w-0"><b class="block truncate"><?= $ch ?></b><span class="block truncate text-gray-400"><?= $d ?> · <?= $pm ?></span></span>
                            <b class="ez-num <?= $refund ? 'text-red-500' : 'text-emerald-600' ?>"><?= $amt ?></b>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Phone 2: Dashboard (centre) -->
                <div class="phone relative z-10 w-[240px] bg-gray-50 overflow-hidden">
                    <div class="flex justify-center"><div class="phone-notch"></div></div>
                    <div class="px-3 pt-2 text-[10px] space-y-2.5">
                        <div class="flex items-center justify-between"><img src="<?= $logoUrl ?>" alt="" class="w-7 h-7 rounded-lg"><span class="w-7 h-7 rounded-full bg-brand-600 text-white font-bold flex items-center justify-center">A</span></div>
                        <div><p class="text-sm font-extrabold"><?= $L('Selamat pagi, Aina', 'Good morning, Aina') ?></p><p class="text-gray-500"><?= $L('Jom pantau kewangan bisnes anda', "Let's keep your finances on track") ?></p></div>
                        <div class="relative overflow-hidden rounded-2xl bg-brand-700 text-white p-3">
                            <div class="absolute -right-6 -top-6 w-20 h-20 rounded-full bg-white/5"></div>
                            <p class="text-white/75"><?= $L('Keuntungan Bersih', 'Net Profit') ?></p>
                            <p class="ez-num font-extrabold leading-none mt-1"><span class="text-[10px] text-white/75 align-top">RM</span><span class="text-2xl">8,420</span><span class="text-sm text-white/70">.50</span></p>
                            <div class="mt-2.5 grid grid-cols-2 gap-1.5">
                                <div class="rounded-lg bg-white/10 p-1.5"><p class="text-[9px] text-white/70"><?= $L('Pendapatan', 'Revenue') ?></p><b class="ez-num">24,860.00</b></div>
                                <div class="rounded-lg bg-white/10 p-1.5"><p class="text-[9px] text-white/70"><?= $L('Perbelanjaan', 'Expenses') ?></p><b class="ez-num">16,439.50</b></div>
                            </div>
                        </div>
                        <div class="rounded-2xl bg-white p-2.5 shadow-sm">
                            <p class="font-bold mb-2"><?= $L('Tindakan Pantas', 'Quick Actions') ?></p>
                            <div class="grid grid-cols-4 gap-1 text-center text-[8.5px] font-semibold text-gray-600">
                                <?php foreach ([['plus', $L('Jualan', 'Sale')], ['receipt', $L('Belanja', 'Expense')], ['down', 'Export'], ['scale', $L('Kira-Kira', 'Balance')]] as [$ic, $lbl]): ?>
                                <span><span class="mx-auto mb-1 w-8 h-8 rounded-xl bg-gray-50 border border-gray-100 text-brand-700 flex items-center justify-center"><?= $icon($I[$ic], 'w-4 h-4') ?></span><?= $lbl ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="rounded-2xl bg-sage-50 p-2.5 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-white text-brand-500 flex items-center justify-center"><?= $icon($I['check'], 'w-3.5 h-3.5') ?></span>
                            <p class="font-bold leading-tight"><?= $L('Perbelanjaan guna 66.1% daripada pendapatan', 'Expenses used 66.1% of revenue') ?></p>
                        </div>
                    </div>
                    <div class="mt-3 grid grid-cols-5 items-end bg-white px-1 pb-2 pt-1 text-[8px] text-gray-400 border-t border-gray-100">
                        <span class="flex flex-col items-center text-brand-700 font-bold"><?= $icon($I['home'], 'w-4 h-4') ?><?= $L('Utama', 'Home') ?></span>
                        <span class="flex flex-col items-center"><?= $icon($I['trend'], 'w-4 h-4') ?><?= $L('Hasil', 'Revenue') ?></span>
                        <span class="flex justify-center"><span class="-translate-y-2 w-9 h-9 rounded-full bg-brand-700 text-white flex items-center justify-center ring-4 ring-gray-50"><?= $icon($I['plus'], 'w-4 h-4') ?></span></span>
                        <span class="flex flex-col items-center"><?= $icon($I['receipt'], 'w-4 h-4') ?><?= $L('Belanja', 'Expenses') ?></span>
                        <span class="flex flex-col items-center"><?= $icon($I['user'], 'w-4 h-4') ?><?= $L('Profil', 'Profile') ?></span>
                    </div>
                </div>

                <!-- Phone 3: Expenses with receipts -->
                <div class="phone hidden md:block w-[210px] bg-gray-50 overflow-hidden rotate-3 translate-y-6">
                    <div class="flex justify-center"><div class="phone-notch"></div></div>
                    <div class="px-3 pb-3 pt-2 text-[10px] space-y-2">
                        <div class="flex items-center justify-between"><p class="text-sm font-extrabold"><?= $L('Perbelanjaan', 'Expenses') ?></p><span class="w-6 h-6 rounded-full bg-brand-700 text-white flex items-center justify-center"><?= $icon($I['plus'], 'w-3 h-3') ?></span></div>
                        <div class="rounded-2xl bg-white p-2.5 shadow-sm space-y-1.5">
                            <p class="font-bold"><?= $L('Bajet bulan ini', "This month's budget") ?></p>
                            <?php foreach ([['COGS', 'bg-sage-300', 'w-[88%]', '88%'], ['OPEX', 'bg-brand-600', 'w-[64%]', '64%'], ['Marketing', 'bg-gold-400', 'w-[97%]', '97%']] as [$c, $col, $w, $p]): ?>
                            <div><div class="flex justify-between"><span><?= $c ?></span><b class="ez-num"><?= $p ?></b></div><div class="h-1.5 rounded-full bg-gray-100"><div class="h-full rounded-full <?= $col ?> <?= $w ?>"></div></div></div>
                            <?php endforeach; ?>
                        </div>
                        <?php foreach ([['COGS', $L('Stok kain supplier', 'Fabric stock'), '3,400.00', 2], ['Marketing', $L('Iklan Facebook', 'Facebook Ads'), '250.00', 1], ['OPEX', $L('Sewa kedai', 'Shop rent'), '1,800.00', 1]] as [$c, $d, $amt, $n]): ?>
                        <div class="rounded-xl bg-white px-2.5 py-2 shadow-sm">
                            <div class="flex items-center justify-between"><b class="truncate"><?= $d ?></b><b class="ez-num">−<?= $amt ?></b></div>
                            <div class="mt-1 flex items-center justify-between text-gray-400">
                                <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 font-semibold"><?= $c ?></span>
                                <span class="flex items-center gap-1"><?= $icon($I['camera'], 'w-3 h-3') ?><?= $n ?> <?= $L('resit', 'receipt' . ($n > 1 ? 's' : '')) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <div class="rounded-xl border border-dashed border-sage-300 bg-sage-50 px-2.5 py-2 text-center text-brand-600 font-semibold">
                            <?= $L('Gambar resit dikecilkan automatik', 'Receipt photos shrink automatically') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ SHOWCASE: REPORTS ============ -->
<section class="pb-16 sm:pb-24">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center max-w-2xl mx-auto">
            <p class="text-xs font-bold tracking-[0.18em] text-brand-400"><?= $L('LAPORAN AUTOMATIK', 'AUTOMATIC REPORTS') ?></p>
            <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold tracking-tight"><?= $L('Laporan sedia untuk akauntan anda', 'Reports ready for your accountant') ?></h2>
            <p class="mt-3 text-gray-600"><?= $L('Tak perlu susun semula hujung tahun. Pilih tempoh, tekan export — siap.', 'No more year-end scramble. Pick a period, press export — done.') ?></p>
        </div>
        <div class="mt-10 grid lg:grid-cols-2 gap-5">
            <!-- P&L -->
            <div class="rounded-3xl bg-white shadow-sm p-5 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-lg font-bold"><?= $L('Penyata Untung Rugi', 'Profit &amp; Loss') ?></p>
                        <p class="text-xs text-gray-500"><?= $L('Oktober 2026', 'October 2026') ?></p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 h-9 px-4 rounded-full bg-brand-700 text-white text-xs font-semibold"><?= $icon($I['down'], 'w-4 h-4') ?>Export CSV</span>
                </div>
                <div class="mt-4 flex gap-1.5 text-[11px] font-semibold">
                    <?php foreach ([['Harian', 'Daily'], ['Mingguan', 'Weekly'], ['Bulanan', 'Monthly'], ['Tahunan', 'Annual'], ['Ikut tarikh', 'Custom']] as $i => [$ms, $en]): ?>
                    <span class="px-2.5 py-1 rounded-full <?= $i === 2 ? 'bg-brand-700 text-white' : 'bg-gray-100 text-gray-500' ?>"><?= $L($ms, $en) ?></span>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4 text-sm divide-y divide-gray-100">
                    <?php foreach ([
                        [$L('Jualan', 'Sales'), '25,014.80', ''],
                        [$L('Refund', 'Refunds'), '(154.80)', 'text-red-500'],
                        [$L('Kos barang dijual (COGS)', 'Cost of goods sold (COGS)'), '(8,384.00)', 'text-gray-600'],
                        [$L('Belanja operasi (OPEX)', 'Operating expenses (OPEX)'), '(4,932.00)', 'text-gray-600'],
                        [$L('Marketing', 'Marketing'), '(3,123.50)', 'text-gray-600'],
                    ] as [$k, $v, $cls]): ?>
                    <div class="flex justify-between py-2"><span class="text-gray-600"><?= $k ?></span><span class="ez-num font-semibold <?= $cls ?>">RM <?= $v ?></span></div>
                    <?php endforeach; ?>
                    <div class="flex justify-between py-2.5 font-extrabold"><span><?= $L('Untung bersih', 'Net profit') ?></span><span class="ez-num text-brand-500">RM 8,420.50</span></div>
                </div>
            </div>
            <!-- Balance sheet -->
            <div class="rounded-3xl bg-white shadow-sm p-5 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-lg font-bold"><?= $L('Kunci Kira-Kira', 'Balance Sheet') ?></p>
                        <p class="text-xs text-gray-500"><?= $L('Setakat 31 Oktober 2026', 'As at 31 October 2026') ?></p>
                    </div>
                    <span class="px-3 py-1.5 rounded-full bg-sage-100 text-brand-700 text-xs font-bold"><?= $L('Seimbang ✓', 'Balanced ✓') ?></span>
                </div>
                <div class="mt-4 grid sm:grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl bg-gray-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-gray-500"><?= $L('Aset', 'Assets') ?></p>
                        <div class="mt-2 space-y-1.5">
                            <div class="flex justify-between"><span><?= $L('Tunai &amp; bank', 'Cash &amp; bank') ?></span><span class="ez-num font-semibold">32,580</span></div>
                            <div class="flex justify-between"><span><?= $L('Stok', 'Inventory') ?></span><span class="ez-num font-semibold">6,200</span></div>
                            <div class="flex justify-between"><span><?= $L('Peralatan', 'Equipment') ?> <span class="ml-1 px-1 rounded bg-sage-200 text-[9px] font-bold text-brand-700">AUTO</span></span><span class="ez-num font-semibold">4,500</span></div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-gray-200 flex justify-between font-extrabold"><span><?= $L('Jumlah', 'Total') ?></span><span class="ez-num">RM 43,280</span></div>
                    </div>
                    <div class="rounded-2xl bg-gray-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-gray-500"><?= $L('Liabiliti &amp; Ekuiti', 'Liabilities &amp; Equity') ?></p>
                        <div class="mt-2 space-y-1.5">
                            <div class="flex justify-between"><span><?= $L('Pinjaman', 'Loan') ?></span><span class="ez-num font-semibold">8,000</span></div>
                            <div class="flex justify-between"><span><?= $L('Modal', 'Capital') ?></span><span class="ez-num font-semibold">15,000</span></div>
                            <div class="flex justify-between"><span><?= $L('Untung terkumpul', 'Retained earnings') ?> <span class="ml-1 px-1 rounded bg-sage-200 text-[9px] font-bold text-brand-700">AUTO</span></span><span class="ez-num font-semibold">20,280</span></div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-gray-200 flex justify-between font-extrabold"><span><?= $L('Jumlah', 'Total') ?></span><span class="ez-num">RM 43,280</span></div>
                    </div>
                </div>
                <p class="mt-3 text-xs text-gray-500"><?= $L('Medan bertanda AUTO dikira terus daripada jualan dan perbelanjaan anda.', 'Fields marked AUTO are calculated straight from your sales and expenses.') ?></p>
            </div>
        </div>
    </div>
</section>

<!-- ============ HOW IT WORKS ============ -->
<section class="pb-16 sm:pb-24">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="rounded-[2rem] bg-brand-700 text-white px-5 sm:px-10 py-12 sm:py-14 relative overflow-hidden">
            <div class="blob absolute -right-24 -top-24 w-80 h-80 bg-white/5"></div>
            <div class="relative text-center max-w-xl mx-auto">
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight"><?= $L('Mula dalam 3 langkah', 'Get started in 3 steps') ?></h2>
                <p class="mt-3 text-white/70"><?= $L('Tiada latihan perakaunan diperlukan.', 'No accounting training needed.') ?></p>
            </div>
            <div class="relative mt-10 grid md:grid-cols-3 gap-4">
                <?php foreach ([
                    ['Daftar akaun', 'Create an account', 'Guna emel atau terus log masuk dengan Google. Siap dalam seminit.', 'Use your email or sign in with Google. Done in a minute.'],
                    ['Rekod jualan &amp; belanja', 'Record sales &amp; spending', 'Tekan butang + untuk tambah jualan atau perbelanjaan, dan snap resit sekali.', 'Tap + to add a sale or expense, and snap the receipt too.'],
                    ['Lihat untung &amp; export', 'See profit &amp; export', 'Dashboard kira untung serta-merta. Export P&amp;L bila akauntan minta.', 'The dashboard works out profit instantly. Export the P&amp;L when your accountant asks.'],
                ] as $i => [$tMs, $tEn, $dMs, $dEn]): ?>
                <div class="rounded-3xl bg-white/10 p-6">
                    <span class="w-10 h-10 rounded-full bg-sage-300 text-brand-800 font-extrabold flex items-center justify-center"><?= $i + 1 ?></span>
                    <h3 class="mt-4 text-lg font-bold"><?= $L($tMs, $tEn) ?></h3>
                    <p class="mt-1.5 text-sm text-white/75 leading-relaxed"><?= $L($dMs, $dEn) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ============ PRICING ============ -->
<section id="harga" class="pb-16 sm:pb-24">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center max-w-2xl mx-auto">
            <p class="text-xs font-bold tracking-[0.18em] text-brand-400"><?= $L('HARGA', 'PRICING') ?></p>
            <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold tracking-tight"><?= $L('Harga mesra peniaga kecil', 'Pricing that suits small businesses') ?></h2>
            <p class="mt-3 text-gray-600"><?= $L('Key-in sentiasa percuma. Naik taraf hanya bila anda perlu simpan banyak resit.', 'Key-in is always free. Upgrade only when you need to store lots of receipts.') ?></p>
        </div>
        <div class="mt-10 grid md:grid-cols-2 gap-5 max-w-4xl mx-auto">
            <div class="rounded-3xl bg-white shadow-sm p-7 flex flex-col">
                <p class="text-lg font-bold"><?= $L('Percuma', 'Free') ?></p>
                <p class="mt-3 text-4xl font-extrabold">RM0</p>
                <p class="text-sm text-gray-500"><?= $L('Percuma selamanya', 'Free forever') ?></p>
                <ul class="mt-6 space-y-3 text-sm text-gray-700 flex-1">
                    <?php foreach ([['Jualan &amp; perbelanjaan tanpa had', 'Unlimited sales &amp; expenses'], ['Dashboard, P&amp;L dan export', 'Dashboard, P&amp;L and export'], ['Kunci Kira-Kira', 'Balance Sheet'], ['Simpan sehingga 20 resit', 'Store up to 20 receipts']] as [$ms, $en]): ?>
                    <li class="flex gap-2.5"><span class="text-brand-400"><?= $icon($I['check']) ?></span><?= $L($ms, $en) ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="<?= $base ?>/register" class="mt-7 inline-flex items-center justify-center h-12 rounded-full bg-gray-100 hover:bg-gray-200 font-semibold text-gray-900"><?= $L('Daftar Percuma', 'Sign up free') ?></a>
            </div>
            <div class="relative rounded-3xl bg-brand-700 text-white shadow-float p-7 flex flex-col overflow-hidden">
                <div class="blob absolute -right-16 -top-16 w-56 h-56 bg-white/5"></div>
                <div class="relative flex items-center justify-between"><p class="text-lg font-bold">Pro</p><span class="px-2.5 py-1 rounded-full bg-gold-300 text-brand-800 text-[11px] font-bold"><?= $L('Untuk resit banyak', 'For lots of receipts') ?></span></div>
                <p class="relative mt-3"><span class="text-4xl font-extrabold">RM5.70</span><span class="text-white/70"><?= $L('/bulan', '/month') ?></span></p>
                <p class="relative text-sm text-white/70"><?= $L('atau RM57/tahun — jimat RM11.40', 'or RM57/year — save RM11.40') ?></p>
                <ul class="relative mt-6 space-y-3 text-sm text-white/90 flex-1">
                    <?php foreach ([['Semua dalam pelan Percuma', 'Everything in Free'], ['Storan resit 1 GB (lebih kurang 4,000 resit)', '1 GB receipt storage (about 4,000 receipts)'], ['Bayar sekali — tiada caj automatik', 'One-off payment — no auto-charge'], ['Kad atau FPX melalui CHIP', 'Card or FPX via CHIP']] as [$ms, $en]): ?>
                    <li class="flex gap-2.5"><span class="text-sage-300"><?= $icon($I['check']) ?></span><?= $L($ms, $en) ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="<?= $base ?>/register" class="relative mt-7 inline-flex items-center justify-center h-12 rounded-full bg-sage-300 hover:bg-sage-200 font-bold text-brand-800"><?= $L('Mula dengan Percuma', 'Start free') ?></a>
                <p class="relative mt-3 text-xs text-white/60 text-center"><?= $L('Pelan Pro bermula 1 November 2026. Sebelum itu, semua ciri percuma.', 'Pro starts on 1 November 2026. Until then, every feature is free.') ?></p>
            </div>
        </div>
    </div>
</section>

<!-- ============ FAQ ============ -->
<section id="soalan" class="pb-16 sm:pb-24">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <h2 class="text-center text-3xl sm:text-4xl font-extrabold tracking-tight"><?= $L('Soalan lazim', 'Frequently asked questions') ?></h2>
        <div class="mt-8 rounded-3xl bg-white shadow-sm divide-y divide-gray-100">
            <?php foreach ([
                ['Betul ke percuma?', 'Is it really free?',
                 'Ya. Rekod jualan, perbelanjaan, dashboard, laporan P&amp;L dan Kunci Kira-Kira semuanya percuma. Pelan Pro (RM5.70 sebulan) hanya diperlukan untuk simpan lebih daripada 20 resit.',
                 'Yes. Sales, expenses, the dashboard, P&amp;L reports and the balance sheet are all free. Pro (RM5.70 a month) is only needed to store more than 20 receipts.'],
                ['Perlu tahu perakaunan ke?', 'Do I need to know accounting?',
                 'Tak perlu. Pilih kategori bila rekod perbelanjaan, dan ezkira susun untung rugi serta Kunci Kira-Kira untuk anda.',
                 'No. Pick a category when you record an expense, and ezkira arranges your P&amp;L and balance sheet for you.'],
                ['Boleh guna di telefon?', 'Does it work on my phone?',
                 'Boleh. ezkira direka untuk telefon dahulu, dan anda boleh &quot;Add to Home Screen&quot; supaya ia dibuka macam app.',
                 'Yes. ezkira is designed phone-first, and you can &quot;Add to Home Screen&quot; so it opens like an app.'],
                ['Data saya selamat?', 'Is my data safe?',
                 'Setiap akaun hanya boleh lihat rekod dan resit sendiri, dan semua sambungan ke ezkira disulitkan (HTTPS).',
                 'Each account can only see its own records and receipts, and every connection to ezkira is encrypted (HTTPS).'],
                ['Boleh hantar laporan kepada akauntan?', 'Can I send reports to my accountant?',
                 'Boleh. Export penyata untung rugi dan senarai perbelanjaan dalam CSV, beserta semua resit dalam satu fail ZIP.',
                 'Yes. Export your P&amp;L and expense list as CSV, with all receipts bundled in one ZIP file.'],
                ['Ada dalam Bahasa Inggeris?', 'Is it available in English?',
                 'Ada. Tukar antara Bahasa Melayu dan English bila-bila masa. Mod gelap pun ada.',
                 'Yes. Switch between Bahasa Melayu and English any time. There is a dark mode too.'],
            ] as [$qMs, $qEn, $aMs, $aEn]): ?>
            <details class="group px-5 sm:px-6 py-4">
                <summary class="flex items-center justify-between gap-4 cursor-pointer list-none font-semibold text-gray-900">
                    <?= $L($qMs, $qEn) ?>
                    <span class="w-8 h-8 shrink-0 rounded-full bg-gray-100 flex items-center justify-center transition-transform group-open:rotate-45"><?= $icon($I['plus'], 'w-4 h-4') ?></span>
                </summary>
                <p class="mt-3 text-sm text-gray-600 leading-relaxed"><?= $L($aMs, $aEn) ?></p>
            </details>
            <?php endforeach; ?>
        </div>
        <p class="mt-6 text-center text-sm text-gray-600">
            <?= $L('Ada soalan lain?', 'Have another question?') ?>
            <a href="https://wa.me/60122541050?text=Hi%2C%20saya%20ada%20soalan%20tentang%20ezkira" target="_blank" rel="noopener" class="font-semibold text-brand-500 hover:underline"><?= $L('WhatsApp kami →', 'WhatsApp us →') ?></a>
        </p>
    </div>
</section>

<!-- ============ FINAL CTA ============ -->
<section class="pb-16 sm:pb-24">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="relative overflow-hidden rounded-[2rem] bg-sage-200 px-6 py-12 sm:py-16 text-center">
            <div class="blob absolute -left-20 -bottom-24 w-72 h-72 bg-sage-300/60"></div>
            <div class="blob absolute -right-16 -top-20 w-64 h-64 bg-gold-100/80"></div>
            <div class="relative">
                <img src="<?= $logoUrl ?>" alt="" class="mx-auto w-14 h-14 rounded-2xl shadow-soft">
                <h2 class="mt-5 text-3xl sm:text-4xl font-extrabold tracking-tight text-brand-800"><?= $L('Mula kira untung bisnes anda hari ini', 'Start knowing your real profit today') ?></h2>
                <p class="mt-3 text-brand-700/80"><?= $L('Daftar dalam seminit. Key-in percuma, selamanya.', 'Sign up in a minute. Key-in is free, forever.') ?></p>
                <a href="<?= $base ?>/register" class="mt-7 inline-flex items-center gap-2 h-12 px-7 rounded-full bg-brand-700 hover:bg-brand-600 text-white font-semibold shadow-soft"><?= $L('Daftar Percuma', 'Sign up free') ?> <?= $icon($I['arrow'], 'w-4 h-4') ?></a>
            </div>
        </div>
    </div>
</section>
</main>

<!-- ============ FOOTER ============ -->
<footer class="border-t border-gray-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-8 text-sm">
        <div class="lg:col-span-2">
            <a href="<?= $base ?>/" class="flex items-center gap-2.5">
                <img src="<?= $logoUrl ?>" alt="ezkira" class="w-9 h-9 rounded-xl">
                <span class="text-lg font-extrabold tracking-wide"><span style="color:#C4A028">ez</span><span class="text-brand-700">kira</span></span>
            </a>
            <p class="mt-3 text-gray-600 max-w-xs"><?= $L('Platform pengurusan kewangan untuk peniaga online dan PKS Malaysia.', 'Finance management for Malaysian online sellers and SMEs.') ?></p>
        </div>
        <div>
            <p class="font-bold text-gray-900"><?= $L('Produk', 'Product') ?></p>
            <ul class="mt-3 space-y-2 text-gray-600">
                <li><a href="#ciri" class="hover:text-brand-700"><?= $L('Ciri-ciri', 'Features') ?></a></li>
                <li><a href="#harga" class="hover:text-brand-700"><?= $L('Harga', 'Pricing') ?></a></li>
                <li><a href="#soalan" class="hover:text-brand-700"><?= $L('Soalan Lazim', 'FAQ') ?></a></li>
                <li><a href="<?= $base ?>/register" class="hover:text-brand-700"><?= $L('Daftar Percuma', 'Sign up free') ?></a></li>
                <li><a href="<?= $base ?>/login" class="hover:text-brand-700"><?= $L('Log Masuk', 'Log in') ?></a></li>
            </ul>
        </div>
        <div>
            <p class="font-bold text-gray-900"><?= $L('Hubungi', 'Contact') ?></p>
            <ul class="mt-3 space-y-2 text-gray-600">
                <li><a href="https://wa.me/60122541050" target="_blank" rel="noopener" class="hover:text-brand-700">WhatsApp +6012-254 1050</a></li>
                <li><a href="mailto:bizbuddyhq@gmail.com" class="hover:text-brand-700">bizbuddyhq@gmail.com</a></li>
            </ul>
        </div>
    </div>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 pb-8 text-xs text-gray-500 flex flex-col sm:flex-row justify-between gap-2">
        <span>&copy; <?= date('Y') ?> ezkira. <?= $L('Hak cipta terpelihara.', 'All rights reserved.') ?> by <span class="font-semibold text-brand-600">NajmiNasrudin</span></span>
        <span><?= $L('Dibina untuk usahawan Malaysia', 'Built for Malaysian entrepreneurs') ?></span>
    </div>
</footer>

<script>
function ezCloseMenu() { document.getElementById('ez-mobile-toggle').checked = false; }

function ezSetLang(lang) {
    document.querySelectorAll('[data-en]').forEach(function (el) {
        if (el.dataset.ms === undefined) el.dataset.ms = el.innerHTML;
        el.innerHTML = lang === 'en' ? el.dataset.en : el.dataset.ms;
    });
    document.querySelectorAll('[data-lang-btn]').forEach(function (b) {
        var on = b.getAttribute('data-lang-btn') === lang;
        b.style.background = on ? '#163020' : '';
        b.style.color = on ? '#fff' : '';
    });
    document.documentElement.lang = lang === 'en' ? 'en' : 'ms';
    try { localStorage.setItem('ezlang', lang); } catch (e) {}
}

(function () {
    var lang = 'ms';
    try { lang = localStorage.getItem('ezlang') || 'ms'; } catch (e) {}
    ezSetLang(lang === 'en' ? 'en' : 'ms');
})();
</script>
</body>
</html>
