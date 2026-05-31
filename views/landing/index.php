<?php
/**
 * Ezkira Landing Page — standalone, no layout dependency.
 * Variables available: BASE_URI (string), APP_URL (string), APP_NAME (string)
 */
$appUrl   = 'https://ezkira.com';
$baseUri  = defined('BASE_URI') ? BASE_URI : '';
$appName  = defined('APP_NAME') ? APP_NAME : 'Ezkira';
$siteBase = 'https://ezkira.com'; // All CTA links point to live site
?>
<!DOCTYPE html>
<html lang="ms" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Primary SEO -->
    <title><?= htmlspecialchars($appName) ?> — Urus Kewangan Bisnes Tanpa Pening Kepala</title>
    <meta name="description" content="Track untung rugi, urus costing, simpan resit dan hasilkan laporan kewangan secara automatik. Platform pengurusan kewangan bisnes untuk SME, peniaga online dan pengusaha Malaysia.">
    <meta name="keywords" content="pengurusan kewangan bisnes, SME malaysia, tracking untung rugi, laporan kewangan automatik, expense tracking, costing management, ezkira">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= $appUrl ?>/">

    <!-- Open Graph -->
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="<?= $appUrl ?>/">
    <meta property="og:title"       content="<?= htmlspecialchars($appName) ?> — Urus Kewangan Bisnes Tanpa Pening Kepala">
    <meta property="og:description" content="Track untung rugi, urus costing, simpan resit dan jana laporan kewangan automatik dalam satu platform yang mudah.">
    <meta property="og:image"       content="<?= $appUrl ?>/assets/img/og-image.png">
    <meta property="og:site_name"   content="<?= htmlspecialchars($appName) ?>">
    <meta property="og:locale"      content="ms_MY">

    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?= htmlspecialchars($appName) ?> — Urus Kewangan Bisnes Tanpa Pening Kepala">
    <meta name="twitter:description" content="Track untung rugi, urus costing dan jana laporan kewangan automatik.">
    <meta name="twitter:image"       content="<?= $appUrl ?>/assets/img/og-image.png">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= $baseUri ?>/assets/img/logo-mark.svg">

    <!-- Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script>
        window.tailwind_config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe',
                            300: '#93c5fd', 400: '#60a5fa', 500: '#3b82f6',
                            600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af', 900: '#1e3a8a',
                        },
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
                    },
                    keyframes: {
                        float: { '0%,100%': { transform: 'translateY(0px)' }, '50%': { transform: 'translateY(-12px)' } },
                        fadeInUp: { '0%': { opacity: '0', transform: 'translateY(24px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                    }
                }
            }
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = window.tailwind_config;</script>

    <style>
        /* ── Brand palette ─────────────────────────────────────────── */
        :root {
            --green:       #163020;   /* primary dark */
            --green-mid:   #1e4a2e;   /* medium forest */
            --green-light: #2d6a42;   /* lighter forest */
            --gold:        #C9A84C;   /* accent gold */
            --gold-light:  #E8D47A;   /* soft gold */
            --gold-dark:   #A88030;   /* deep gold */
            --cream:       #F7F4EE;   /* warm off-white */
            --cream-dark:  #EDE8DC;   /* card border */
        }
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; background: #fff; }
        .hero-gradient { background: linear-gradient(135deg, var(--green) 0%, var(--green-mid) 50%, var(--green) 100%); }
        .gradient-text { background: linear-gradient(135deg, var(--gold-light), var(--gold)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
        .faq-body { max-height: 0; overflow: hidden; transition: max-height 0.35s ease; }
        .faq-body.open { max-height: 300px; }
        .faq-icon { transition: transform 0.3s ease; }
        .faq-item.open .faq-icon { transform: rotate(45deg); }
        .section-fade { opacity: 0; transform: translateY(32px); transition: opacity 0.6s ease, transform 0.6s ease; }
        .section-fade.visible { opacity: 1; transform: translateY(0); }
        .chart-bar { animation: growUp 1s ease-out forwards; transform-origin: bottom; }
        @keyframes growUp { from { transform: scaleY(0); } to { transform: scaleY(1); } }
        .glow-gold { box-shadow: 0 0 40px rgba(201, 168, 76, 0.20); }
        .nav-blur { backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }
        /* Gold CTA button */
        .btn-gold { background: var(--gold); color: var(--green); font-weight: 700; transition: background 0.2s, transform 0.2s; }
        .btn-gold:hover { background: var(--gold-light); transform: translateY(-2px); }
        .btn-gold-shadow { box-shadow: 0 12px 28px rgba(201,168,76,0.30); }
        /* Dark green outlined button */
        .btn-outline-white { border: 1.5px solid rgba(255,255,255,0.25); color: white; transition: border-color 0.2s, background 0.2s; }
        .btn-outline-white:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }
        /* Section label accent */
        .label-gold { color: var(--gold); }
        /* Light section bg */
        .bg-cream { background: var(--cream); }
        ::-webkit-scrollbar { width: 6px; } ::-webkit-scrollbar-track { background: #f1f5f9; } ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    </style>
</head>

<body class="bg-white text-slate-800 antialiased">

<!-- ============================================================ -->
<!-- NAVIGATION                                                    -->
<!-- ============================================================ -->
<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- Logo -->
            <a href="<?= $baseUri ?>/" class="flex items-center gap-2.5 shrink-0">
                <img src="<?= $baseUri ?>/assets/img/logo.svg" alt="<?= htmlspecialchars($appName) ?>" class="h-8 w-auto">
            </a>

            <!-- Desktop nav links -->
            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300" id="nav-links">
                <a href="#features"   class="hover:text-white transition-colors" data-i18n="nav_features">Ciri-ciri</a>
                <a href="#how-it-works" class="hover:text-white transition-colors" data-i18n="nav_howto">Cara Guna</a>
                <a href="#showcase"   class="hover:text-white transition-colors" data-i18n="nav_dashboard">Dashboard</a>
                <a href="#faq"        class="hover:text-white transition-colors" data-i18n="nav_faq">FAQ</a>
            </div>

            <!-- Lang toggle -->
            <div class="hidden md:flex items-center bg-white/10 rounded-lg p-0.5 gap-0.5 mr-1">
                <button onclick="setLang('ms')" id="lang-btn-ms"
                    class="text-xs font-black px-2.5 py-1 rounded-md transition-all cursor-pointer text-white" style="background:rgba(201,168,76,0.8);color:#163020">BM</button>
                <button onclick="setLang('en')" id="lang-btn-en"
                    class="text-xs font-black px-2.5 py-1 rounded-md transition-all cursor-pointer text-slate-400 hover:text-white">EN</button>
            </div>
            <!-- CTA buttons -->
            <div class="hidden md:flex items-center gap-3">
                <a href="<?= $siteBase ?>/login"
                   class="text-sm font-semibold text-slate-300 hover:text-white transition-colors px-4 py-2" data-i18n="nav_login">Log Masuk</a>
                <a href="<?= $siteBase ?>/register"
                   class="btn-gold btn-gold-shadow text-sm px-5 py-2.5 rounded-xl">
                    Cuba Percuma
                </a>
            </div>

            <!-- Mobile hamburger -->
            <button id="hamburger" class="md:hidden text-slate-300 hover:text-white p-2 rounded-lg" aria-label="Menu">
                <svg id="ham-open"   class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg id="ham-close" class="w-6 h-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden py-4 border-t border-white/10" style="background:#163020">
            <div class="flex flex-col gap-3 text-sm font-medium text-slate-300">
                <a href="#features"     class="py-2 hover:text-white" onclick="closeMobile()" data-i18n="nav_features">Ciri-ciri</a>
                <a href="#how-it-works" class="py-2 hover:text-white" onclick="closeMobile()" data-i18n="nav_howto">Cara Guna</a>
                <a href="#showcase"     class="py-2 hover:text-white" onclick="closeMobile()" data-i18n="nav_dashboard">Dashboard</a>
                <a href="#faq"          class="py-2 hover:text-white" onclick="closeMobile()" data-i18n="nav_faq">FAQ</a>
                <div class="flex gap-1 pb-2">
                    <button onclick="setLang('ms')" id="lang-btn-ms-mob"
                        class="flex-1 text-center py-1.5 text-xs font-black rounded-lg cursor-pointer transition-all" style="background:rgba(201,168,76,0.9);color:#163020">BM</button>
                    <button onclick="setLang('en')" id="lang-btn-en-mob"
                        class="flex-1 text-center py-1.5 text-xs font-black rounded-lg text-slate-300 border border-white/20 cursor-pointer hover:bg-white/10 transition-all">EN</button>
                </div>
                <div class="flex gap-3 pt-2">
                    <a href="<?= $siteBase ?>/login"    class="flex-1 text-center py-2.5 btn-outline-white rounded-xl" data-i18n="nav_login">Log Masuk</a>
                    <a href="<?= $siteBase ?>/register" class="flex-1 text-center py-2.5 btn-gold rounded-xl" data-i18n="nav_register">Cuba Percuma</a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- ============================================================ -->
<!-- SECTION 1: HERO                                              -->
<!-- ============================================================ -->
<section class="hero-gradient min-h-screen flex items-center pt-16 overflow-hidden relative">

    <!-- Background decorations -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full blur-3xl" style="background:rgba(201,168,76,0.08)"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 rounded-full blur-3xl"  style="background:rgba(201,168,76,0.05)"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full blur-3xl" style="background:rgba(46,74,46,0.15)"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28 relative">
        <div class="grid lg:grid-cols-2 gap-16 items-center">

            <!-- Left: Copy -->
            <div class="text-center lg:text-left">
                <div class="inline-flex items-center gap-2 text-xs font-semibold px-4 py-2 rounded-full mb-6" style="background:rgba(201,168,76,0.12);border:1px solid rgba(201,168,76,0.25);color:#E8D47A">
                    <span class="w-2 h-2 rounded-full animate-pulse-slow" style="background:#C9A84C"></span>
                    <span data-i18n="hero_badge">Platform Kewangan untuk PKS Malaysia</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-[1.1] tracking-tight mb-6">
                    Urus Kewangan Bisnes<br>
                    <span class="gradient-text">Tanpa Pening Kepala</span>
                </h1>

                <p class="text-slate-400 text-lg leading-relaxed mb-10 max-w-xl mx-auto lg:mx-0">
                    Track untung rugi, urus costing, simpan resit dan hasilkan laporan kewangan secara automatik dalam satu platform yang mudah digunakan.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="<?= $siteBase ?>/register"
                       class="btn-gold btn-gold-shadow inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl text-base"
                       data-i18n="hero_cta1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Cuba Percuma — Tiada Bayaran
                    </a>
                    <a href="#showcase"
                       class="btn-outline-white inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl text-base font-semibold"
                       data-i18n="hero_cta2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Lihat Demo
                    </a>
                </div>

                <p class="text-slate-500 text-sm mt-6">
                    ✓ Tiada kad kredit diperlukan &nbsp;·&nbsp; ✓ Setup dalam 2 minit &nbsp;·&nbsp; ✓ Data selamat &amp; peribadi
                </p>
            </div>

            <!-- Right: Dashboard mockup -->
            <div class="relative hidden lg:block">
                <div class="relative glow-gold rounded-3xl overflow-hidden" style="animation: float 6s ease-in-out infinite;">

                    <!-- Main dashboard card -->
                    <div class="bg-slate-800/80 border border-white/10 rounded-3xl p-6 backdrop-blur-sm">

                        <!-- Top bar -->
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <p class="text-slate-400 text-xs">Dashboard Kewangan</p>
                                <p class="text-white font-bold text-sm">Mei 2026</p>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-green-400"></span>
                            </div>
                        </div>

                        <!-- Stat cards row -->
                        <div class="grid grid-cols-3 gap-3 mb-6">
                            <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-3">
                                <p class="text-emerald-400 text-xs mb-1">Pendapatan</p>
                                <p class="text-white font-bold text-sm">RM 24,580</p>
                                <p class="text-emerald-400 text-xs">↑ 18.4%</p>
                            </div>
                            <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-3">
                                <p class="text-red-400 text-xs mb-1">Perbelanjaan</p>
                                <p class="text-white font-bold text-sm">RM 11,230</p>
                                <p class="text-red-400 text-xs">↑ 5.2%</p>
                            </div>
                            <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-3">
                                <p class="text-blue-400 text-xs mb-1">Untung Bersih</p>
                                <p class="text-white font-bold text-sm">RM 13,350</p>
                                <p class="text-blue-400 text-xs">↑ 32.1%</p>
                            </div>
                        </div>

                        <!-- Mini bar chart -->
                        <div class="bg-slate-900/50 rounded-2xl p-4">
                            <p class="text-slate-400 text-xs mb-4">Pendapatan vs Perbelanjaan (6 bulan)</p>
                            <div class="flex items-end gap-1.5 h-20">
                                <?php
                                $bars = [
                                    ['rev'=>60, 'exp'=>35], ['rev'=>72, 'exp'=>40], ['rev'=>55, 'exp'=>38],
                                    ['rev'=>80, 'exp'=>45], ['rev'=>68, 'exp'=>42], ['rev'=>90, 'exp'=>48],
                                ];
                                foreach ($bars as $b):
                                ?>
                                <div class="flex-1 flex flex-col items-center gap-0.5">
                                    <div class="w-full flex gap-0.5 items-end h-16">
                                        <div class="flex-1 bg-emerald-500/70 rounded-t chart-bar" style="height:<?= $b['rev'] ?>%"></div>
                                        <div class="flex-1 bg-red-400/60 rounded-t chart-bar"    style="height:<?= $b['exp'] ?>%"></div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="flex items-center gap-4 mt-3">
                                <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 bg-emerald-500/70 rounded-sm"></span><span class="text-slate-400 text-xs">Pendapatan</span></div>
                                <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 bg-red-400/60 rounded-sm"></span><span class="text-slate-400 text-xs">Perbelanjaan</span></div>
                            </div>
                        </div>

                        <!-- P&L preview -->
                        <div class="mt-3 bg-slate-900/50 rounded-2xl p-4">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-slate-400 text-xs font-medium">Ringkasan P&amp;L</p>
                                <span class="text-xs bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded-full">Untung ↑</span>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between text-xs"><span class="text-slate-400">Jualan Kasar</span><span class="text-white font-medium">RM 24,580</span></div>
                                <div class="flex justify-between text-xs"><span class="text-slate-400">COGS</span><span class="text-red-400 font-medium">- RM 8,400</span></div>
                                <div class="border-t border-white/10 pt-2 flex justify-between text-xs font-bold"><span class="text-slate-300">Untung Bersih</span><span class="text-emerald-400">RM 13,350</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Floating badges -->
                <div class="absolute -top-4 -right-4 bg-white rounded-2xl shadow-xl px-4 py-3 border border-slate-100" style="animation: float 5s ease-in-out infinite 1s;">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800">Laporan Siap!</p>
                            <p class="text-xs text-slate-500">P&amp;L dijana automatik</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-4 -left-4 bg-white rounded-2xl shadow-xl px-4 py-3 border border-slate-100" style="animation: float 7s ease-in-out infinite 2s;">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#F0EBD8">
                            <svg class="w-4 h-4" style="color:#163020" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800">Untung Naik 32%</p>
                            <p class="text-xs text-slate-500">berbanding bulan lalu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wave divider -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 80" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" class="w-full h-16 fill-white">
            <path d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z"/>
        </svg>
    </div>
</section>

<!-- ============================================================ -->
<!-- SECTION 2: PROBLEM                                           -->
<!-- ============================================================ -->
<section id="problem" class="py-24 bg-white section-fade">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block text-xs font-bold tracking-widest uppercase label-gold mb-4" data-i18n="problem_label">Adakah Ini Masalah Anda?</span>
            <h2 class="text-4xl sm:text-5xl font-black text-slate-900 mb-5" data-i18n="problem_h2">Masih Urus Kewangan<br><span class="gradient-text">Secara Manual?</span></h2>
            <p class="text-slate-500 text-lg max-w-xl mx-auto" data-i18n="problem_subtitle">Ramai pemilik bisnes masih bergelut dengan masalah yang sama. Anda tidak bersendirian.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php
            $problems = [
                ['icon'=>'🗂️', 'title'=>'Resit Berselerak',        'desc'=>'Resit fizikal dan digital bertaburan tanpa sistem penyimpanan yang teratur.'],
                ['icon'=>'📊', 'title'=>'Spreadsheet Serabut',      'desc'=>'Formula Excel yang rumit, data tidak konsisten dan susah nak kemaskini setiap hari.'],
                ['icon'=>'❓', 'title'=>'Tak Tahu Untung Sebenar',  'desc'=>'Duit masuk banyak tapi tak tahu sama ada bisnes sebenarnya untung atau rugi.'],
                ['icon'=>'😰', 'title'=>'Susah Urus Cukai',         'desc'=>'Masa cukai tiba, panik cari dokumen dan kelam kabut siapkan penyata kewangan.'],
                ['icon'=>'⏳', 'title'=>'Ambil Masa Buat Laporan',  'desc'=>'Berjam-jam habis untuk susun data dan siapkan laporan kewangan setiap bulan.'],
                ['icon'=>'🔀', 'title'=>'Data Kewangan Tak Tersusun','desc'=>'Tiada gambaran jelas tentang perbelanjaan, pendapatan dan kedudukan kewangan semasa.'],
            ];
            foreach ($problems as $idx => $p):
            ?>
            <div class="relative bg-red-50 border border-red-100 rounded-2xl p-6 card-hover">
                <div class="text-3xl mb-4"><?= $p['icon'] ?></div>
                <div class="absolute top-4 right-4 text-red-400 font-bold text-lg">✕</div>
                <h3 class="font-bold text-slate-800 text-base mb-2" data-i18n="pain_<?= $idx ?>_title"><?= htmlspecialchars($p['title']) ?></h3>
                <p class="text-slate-500 text-sm leading-relaxed" data-i18n="pain_<?= $idx ?>_desc"><?= htmlspecialchars($p['desc']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Transition arrow -->
        <div class="text-center mt-16">
            <div class="inline-flex flex-col items-center gap-2">
                <p class="text-slate-500 text-sm font-medium" data-i18n="problem_cta_text">Ezkira menyelesaikan semua masalah ini</p>
                <div class="w-8 h-8 rounded-full flex items-center justify-center animate-bounce" style="background:#C9A84C">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================ -->
<!-- SECTION 3: FEATURES                                          -->
<!-- ============================================================ -->
<section id="features" class="py-24 bg-cream section-fade">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block text-xs font-bold tracking-widest uppercase label-gold mb-4" data-i18n="feat_label">Ciri-Ciri Platform</span>
            <h2 class="text-4xl sm:text-5xl font-black text-slate-900 mb-5" data-i18n="feat_h2">Semua Yang Anda Perlukan<br><span class="gradient-text">Dalam Satu Platform</span></h2>
            <p class="text-slate-500 text-lg max-w-xl mx-auto" data-i18n="feat_subtitle">Direka khas untuk pemilik bisnes Malaysia yang mahu urus kewangan dengan lebih profesional dan efisien.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <?php
            $features = [
                [
                    'icon' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>',
                    'color' => 'blue', 'title' => 'Costing Management',
                    'desc'  => 'Kira kos produk, margin untung dan BEP dengan mudah dan tepat.',
                ],
                [
                    'icon' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
                    'color' => 'red', 'title' => 'Expense Tracking',
                    'desc'  => 'Rekod semua perbelanjaan bisnes dengan kategori dan pantau budget anda.',
                ],
                [
                    'icon' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                    'color' => 'emerald', 'title' => 'Revenue Tracking',
                    'desc'  => 'Rekod semua jualan dan pendapatan, set target dan pantau pencapaian.',
                ],
                [
                    'icon' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
                    'color' => 'purple', 'title' => 'Receipt Storage',
                    'desc'  => 'Simpan dan urus resit digital dalam satu tempat yang selamat dan tersusun.',
                ],
                [
                    'icon' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
                    'color' => 'orange', 'title' => 'Profit & Loss Report',
                    'desc'  => 'Penyata P&amp;L dijana secara automatik — sedia untuk akauntan atau cukai.',
                ],
                [
                    'icon' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>',
                    'color' => 'teal', 'title' => 'Balance Sheet',
                    'desc'  => 'Kunci kira-kira automatik — aset, liabiliti dan ekuiti pemilik sekilas pandang.',
                ],
                [
                    'icon' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>',
                    'color' => 'blue', 'title' => 'Financial Dashboard',
                    'desc'  => 'Papan pemuka visual dengan carta dan KPI kewangan terkini bisnes anda.',
                ],
                [
                    'icon' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>',
                    'color' => 'indigo', 'title' => 'Monthly Business Insights',
                    'desc'  => 'Ringkasan bulanan prestasi bisnes dengan trend dan cadangan tindakan.',
                ],
            ];
            $colorMap = [
                'blue'   => 'border',  // handled via inline style
                'red'    => 'bg-red-50 text-red-600 border-red-100',
                'emerald'=> 'bg-emerald-50 text-emerald-700 border-emerald-100',
                'purple' => 'bg-purple-50 text-purple-600 border-purple-100',
                'orange' => 'bg-orange-50 text-orange-600 border-orange-100',
                'teal'   => 'bg-teal-50 text-teal-600 border-teal-100',
                'indigo' => 'border',  // handled via inline style
            ];
            $inlineStyle = ['blue' => 'background:#F0EBD8;color:#163020;border-color:#E8D47A', 'indigo' => 'background:#EDF7F0;color:#163020;border-color:#C9A84C'];
            foreach ($features as $fidx => $f):
                $cls   = $colorMap[$f['color']] ?? 'border';
                $style = $inlineStyle[$f['color']] ?? '';
            ?>
            <div class="bg-white border border-slate-100 rounded-2xl p-6 card-hover shadow-sm">
                <div class="w-12 h-12 <?= $cls ?> rounded-xl flex items-center justify-center mb-4" style="<?= $style ?>">
                    <?= $f['icon'] ?>
                </div>
                <h3 class="font-bold text-slate-800 text-sm mb-2" data-i18n="feat_<?= $fidx ?>_title"><?= htmlspecialchars($f['title']) ?></h3>
                <p class="text-slate-500 text-sm leading-relaxed" data-i18n="feat_<?= $fidx ?>_desc"><?= $f['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================ -->
<!-- SECTION 4: HOW IT WORKS                                      -->
<!-- ============================================================ -->
<section id="how-it-works" class="py-24 bg-white section-fade">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block text-xs font-bold tracking-widest uppercase label-gold mb-4" data-i18n="howto_label">Cara Penggunaan</span>
            <h2 class="text-4xl sm:text-5xl font-black text-slate-900 mb-5" data-i18n="howto_h2">Mulakan Dalam<br><span class="gradient-text">3 Langkah Mudah</span></h2>
            <p class="text-slate-500 text-lg" data-i18n="howto_subtitle">Setup pantas — tidak perlukan latihan khas atau pengetahuan perakaunan.</p>
        </div>

        <div class="relative">
            <!-- Connector line (desktop) -->
            <div class="hidden md:block absolute top-12 left-0 right-0 h-0.5" style="width:calc(100% - 8rem); left:4rem; background:linear-gradient(90deg,#E8D47A,#C9A84C,#E8D47A)"></div>

            <div class="grid md:grid-cols-3 gap-10">
                <?php
                $steps = [
                    ['num'=>'01', 'color'=>'blue',    'title'=>'Masukkan Jualan & Perbelanjaan', 'desc'=>'Rekod semua transaksi bisnes anda secara mudah. Kategorikan perbelanjaan dan rekod setiap jualan.', 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'],
                    ['num'=>'02', 'color'=>'indigo',  'title'=>'Upload Resit', 'desc'=>'Ambil gambar resit atau upload terus. Semua resit disimpan dengan selamat dan boleh dicari bila-bila masa.', 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>'],
                    ['num'=>'03', 'color'=>'emerald', 'title'=>'Lihat Laporan Automatik', 'desc'=>'P&L, Balance Sheet dan semua laporan kewangan dijana secara automatik — sedia untuk akauntan atau cukai.', 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
                ];
                // All steps use brand green/gold palette
                $stepNumStyle = 'background:#163020;color:#C9A84C';
                $stepBgStyle  = ['blue'=>'background:#F0EBD8;border-color:#E8D47A','indigo'=>'background:#EDF7F0;border-color:#C9A84C','emerald'=>'background:#EDF7F0;border-color:#C9A84C'];
                $stepIconStyle= 'color:#163020';
                foreach ($steps as $sidx => $s):
                ?>
                <div class="flex flex-col items-center text-center">
                    <div class="relative mb-8">
                        <div class="w-24 h-24 border-2 rounded-3xl flex items-center justify-center mb-0" style="<?= $stepBgStyle[$s['color']] ?>">
                            <svg class="w-10 h-10" style="<?= $stepIconStyle ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><?= $s['icon'] ?></svg>
                        </div>
                        <span class="absolute -top-3 -right-3 w-8 h-8 text-xs font-black rounded-xl flex items-center justify-center" style="<?= $stepNumStyle ?>"><?= $s['num'] ?></span>
                    </div>
                    <h3 class="font-bold text-slate-800 text-base mb-3" data-i18n="step_<?= $sidx ?>_title"><?= htmlspecialchars($s['title']) ?></h3>
                    <p class="text-slate-500 text-sm leading-relaxed" data-i18n="step_<?= $sidx ?>_desc"><?= htmlspecialchars($s['desc']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="text-center mt-16">
            <a href="<?= $siteBase ?>/register"
               class="btn-gold btn-gold-shadow inline-flex items-center gap-2 px-8 py-4 rounded-2xl text-base"
               data-i18n="howto_cta">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Mula Sekarang — Percuma
            </a>
        </div>
    </div>
</section>

<!-- ============================================================ -->
<!-- SECTION 5: BENEFITS                                          -->
<!-- ============================================================ -->
<section id="benefits" class="py-24 section-fade" style="background: linear-gradient(135deg, #163020 0%, #1e4a2e 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <span class="inline-block text-xs font-bold tracking-widest uppercase label-gold mb-4" data-i18n="benefits_label">Kenapa Ezkira</span>
                <h2 class="text-4xl sm:text-5xl font-black text-white mb-6" data-i18n="benefits_h2">Lebih Masa Untuk<br><span class="gradient-text">Fokus Kepada Bisnes</span></h2>
                <p class="text-slate-400 text-lg leading-relaxed mb-10">
                    Jangan habiskan masa berharga anda dengan kerja pentadbiran kewangan. Biar Ezkira uruskan bahagian yang membosankan, supaya anda boleh fokus mengembangkan bisnes.
                </p>
                <a href="<?= $siteBase ?>/register"
                   class="btn-gold btn-gold-shadow inline-flex items-center gap-2 px-7 py-3.5 rounded-xl text-sm">
                    Cuba Percuma Sekarang →
                </a>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <?php
                $benefits = [
                    ['icon'=>'⚡', 'title'=>'Jimat Masa',             'desc'=>'Kurangkan masa pengurusan kewangan sehingga 80%.'],
                    ['icon'=>'🤖', 'title'=>'Kurang Kerja Manual',    'desc'=>'Automasi pengiraan, laporan dan penyusunan data.'],
                    ['icon'=>'📁', 'title'=>'Rekod Tersusun',         'desc'=>'Semua dokumen dan data kewangan di satu tempat.'],
                    ['icon'=>'🧮', 'title'=>'Mudah Urus Akaun',      'desc'=>'Data siap untuk akauntan — jimat kos profesional.'],
                    ['icon'=>'🧾', 'title'=>'Mudah Urus Cukai',       'desc'=>'Semua rekod tersedia untuk pengemukaan cukai tahunan.'],
                    ['icon'=>'🎯', 'title'=>'Keputusan Lebih Tepat',  'desc'=>'Data real-time untuk buat keputusan bisnes yang bijak.'],
                ];
                foreach ($benefits as $bidx => $b):
                ?>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-5 hover:bg-white/10 transition-colors">
                    <div class="text-2xl mb-3"><?= $b['icon'] ?></div>
                    <h3 class="font-bold text-white text-sm mb-1.5" data-i18n="ben_<?= $bidx ?>_title"><?= htmlspecialchars($b['title']) ?></h3>
                    <p class="text-slate-400 text-xs leading-relaxed" data-i18n="ben_<?= $bidx ?>_desc"><?= htmlspecialchars($b['desc']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================ -->
<!-- SECTION 6: DASHBOARD SHOWCASE (HTML Mockup)                  -->
<!-- ============================================================ -->
<section id="showcase" class="py-24 bg-cream section-fade">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="inline-block text-xs font-bold tracking-widest uppercase label-gold mb-4" data-i18n="showcase_label">Platform Preview</span>
            <h2 class="text-4xl sm:text-5xl font-black text-slate-900 mb-4" data-i18n="showcase_h2">Dashboard Yang <span class="gradient-text">Profesional &amp; Lengkap</span></h2>
            <p class="text-slate-500 text-lg max-w-xl mx-auto" data-i18n="showcase_subtitle">Semua data kewangan bisnes anda dalam paparan yang jelas, interaktif dan mudah difahami.</p>
        </div>

        <!-- Tab buttons -->
        <div class="flex flex-wrap gap-2 justify-center mb-6">
            <button onclick="showTab('dash')" id="tab-dash" class="mock-tab px-5 py-2 text-sm font-bold rounded-full border-2 transition-all cursor-pointer active-tab">📊 Dashboard</button>
            <button onclick="showTab('exp')"  id="tab-exp"  class="mock-tab px-5 py-2 text-sm font-bold rounded-full border-2 transition-all cursor-pointer inactive-tab">💰 Perbelanjaan</button>
            <button onclick="showTab('bs')"   id="tab-bs"   class="mock-tab px-5 py-2 text-sm font-bold rounded-full border-2 transition-all cursor-pointer inactive-tab">📋 Balance Sheet</button>
        </div>

        <!-- Browser frame -->
        <div class="rounded-2xl overflow-hidden shadow-2xl border border-slate-200">
            <!-- Chrome bar -->
            <div class="flex items-center gap-2 px-4 py-2.5" style="background:#1e293b">
                <span class="w-3 h-3 rounded-full bg-red-400 cursor-pointer hover:opacity-80 transition-opacity"></span>
                <span class="w-3 h-3 rounded-full bg-yellow-400 cursor-pointer hover:opacity-80 transition-opacity"></span>
                <span class="w-3 h-3 rounded-full bg-green-400 cursor-pointer hover:opacity-80 transition-opacity"></span>
                <div class="flex-1 mx-3">
                    <div class="flex items-center gap-1.5 bg-slate-700 rounded px-3 py-1 max-w-xs mx-auto">
                        <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span class="text-slate-300 text-xs" id="url-bar">ezkira.com/dashboard</span>
                    </div>
                </div>
            </div>

            <!-- App nav -->
            <div class="flex items-center justify-between px-6 py-3" style="background:#163020">
                <img src="<?= $baseUri ?>/assets/img/logo.svg" alt="Ezkira" class="h-6 w-auto brightness-200">
                <div class="hidden sm:flex items-center gap-5 text-xs font-semibold">
                    <span id="nav-dashboard" class="mock-nav-item cursor-pointer pb-0.5 transition-colors border-b-2" style="color:#C9A84C;border-color:#C9A84C">Dashboard</span>
                    <span id="nav-revenue"   class="mock-nav-item cursor-pointer text-slate-300 hover:text-white pb-0.5 border-b-2 border-transparent transition-colors">Revenue</span>
                    <span id="nav-expenses"  class="mock-nav-item cursor-pointer text-slate-300 hover:text-white pb-0.5 border-b-2 border-transparent transition-colors">Expenses</span>
                    <span id="nav-balance"   class="mock-nav-item cursor-pointer text-slate-300 hover:text-white pb-0.5 border-b-2 border-transparent transition-colors">Balance Sheet</span>
                    <span id="nav-profile"   class="mock-nav-item cursor-pointer text-slate-300 hover:text-white pb-0.5 border-b-2 border-transparent transition-colors">My Profile</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-400 cursor-pointer hover:text-white transition-colors">BM</span>
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-black cursor-pointer hover:opacity-80 transition-opacity" style="background:#C9A84C;color:#163020">T</div>
                </div>
            </div>

            <!-- ── DASHBOARD PANEL ───────────────────────── -->
            <div id="panel-dash" class="bg-slate-50 p-4">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div class="lg:col-span-2 space-y-4">
                        <!-- Expenses Overview -->
                        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 card-hover">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-bold text-slate-800 text-sm">Expenses Overview</h4>
                                <span class="text-xs text-slate-400 border border-slate-200 rounded-md px-2 py-0.5 cursor-pointer hover:bg-slate-50 transition-colors">Monthly ▾</span>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider text-center mb-3">EXPENSES COMPOSITION</p>
                                    <div class="flex justify-center mb-3">
                                        <div class="relative w-24 h-24">
                                            <div class="w-24 h-24 rounded-full" style="background:conic-gradient(#3b82f6 0% 10%,#a855f7 10% 11%,#f97316 11% 52%,#e5e7eb 52% 100%)"></div>
                                            <div class="absolute inset-3 bg-white rounded-full flex flex-col items-center justify-center">
                                                <p class="text-xs font-black text-slate-800 leading-none">RM 893</p>
                                                <p class="text-slate-400" style="font-size:9px">Total Spent</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="space-y-1 text-xs">
                                        <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></span><span class="text-slate-600 flex-1">OPEX</span><span class="font-semibold">RM 92</span></div>
                                        <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-purple-500 shrink-0"></span><span class="text-slate-600 flex-1">Marketing</span><span class="font-semibold">RM 0</span></div>
                                        <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-orange-500 shrink-0"></span><span class="text-slate-600 flex-1">COGS</span><span class="font-semibold">RM 801</span></div>
                                        <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-slate-200 shrink-0"></span><span class="text-slate-600 flex-1">Remaining</span><span class="font-semibold">RM 2,107</span></div>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider text-center mb-3">BUDGET HEALTH</p>
                                    <div class="flex justify-center mb-3">
                                        <div class="relative w-24 h-24">
                                            <div class="w-24 h-24 rounded-full" style="background:conic-gradient(#ef4444 0% 44%,#10b981 44% 100%)"></div>
                                            <div class="absolute inset-3 bg-white rounded-full flex flex-col items-center justify-center">
                                                <p class="text-xs font-black text-slate-800 leading-none">43.6%</p>
                                                <p class="text-slate-400" style="font-size:9px">Expenses used</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="space-y-1 text-xs">
                                        <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-400 shrink-0"></span><span class="text-slate-600 flex-1">Total Expenses</span><span class="font-semibold">43.6%</span></div>
                                        <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span><span class="text-slate-600 flex-1">Net Profit</span><span class="font-bold text-emerald-600">RM 1,156.90</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Daily Overview -->
                        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 card-hover">
                            <div class="flex items-center justify-between mb-1">
                                <div><h4 class="font-bold text-slate-800 text-sm">Daily Overview</h4><p class="text-xs text-slate-400">May 2026</p></div>
                                <div class="flex gap-1">
                                    <button class="text-xs px-2.5 py-1 rounded-md font-semibold text-white cursor-pointer" style="background:#163020">Day</button>
                                    <button class="text-xs px-2.5 py-1 rounded-md font-semibold text-slate-500 hover:bg-slate-100 transition-colors cursor-pointer">Month</button>
                                    <button class="text-xs px-2.5 py-1 rounded-md font-semibold text-slate-500 hover:bg-slate-100 transition-colors cursor-pointer">Year</button>
                                </div>
                            </div>
                            <div class="flex items-end gap-px h-20 mt-4 px-1">
<?php
$rev = [1,1,1,60,40,2,1,5,1,4,3,2,5,1,6,3,5,1,95,65,4,3,2,4,1,2,5,2,1,3,1];
$exp = [0,0,0,35,10,0,0,0,0,0,0,0,0,0,0,0,0,0,75,15,0,0,0,0,0,0,0,2,0,0,0];
foreach ($rev as $di => $rv):
    $ev = $exp[$di]??0;
?>
                                <div class="flex-1 flex gap-px items-end h-full group">
                                    <div class="flex-1 rounded-t transition-all group-hover:opacity-70 cursor-pointer" style="height:<?= max(2,$rv) ?>%;background:#163020" title="Day <?= $di+1 ?>: RM <?= $rv*10 ?>"></div>
                                    <?php if($ev>0):?><div class="w-1 rounded-t group-hover:opacity-70 cursor-pointer" style="height:<?= $ev ?>%;background:#f87171"></div><?php endif;?>
                                </div>
<?php endforeach; ?>
                            </div>
                            <div class="flex gap-4 mt-2 text-xs text-slate-500">
                                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm" style="background:#163020"></span>Revenue</span>
                                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-red-400"></span>Expenses</span>
                            </div>
                        </div>
                    </div>
                    <!-- Recent Transactions -->
                    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 card-hover">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-bold text-slate-800 text-sm">Recent Transactions</h4>
                            <div class="flex gap-2 text-xs">
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>In</span>
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-400"></span>Out</span>
                            </div>
                        </div>
                        <div class="divide-y divide-slate-50">
<?php
$txns=[
    ['n'=>'Raw materials',     'c'=>'COGS · 27 May',             'a'=>'− RM 44.30',  'in'=>false],
    ['n'=>'Agent',             'c'=>'Agent Setia Alam · 19 May', 'a'=>'+ RM 748.00', 'in'=>true],
    ['n'=>'Raw material (NSK)','c'=>'COGS · 18 May',             'a'=>'− RM 360.80', 'in'=>false],
    ['n'=>'Outlet meru',       'c'=>'Walk-in · 17 May',          'a'=>'+ RM 57.00',  'in'=>true],
    ['n'=>'Outlet meru',       'c'=>'Walk-in · 16 May',          'a'=>'+ RM 89.00',  'in'=>true],
    ['n'=>'Outlet meru',       'c'=>'Walk-in · 15 May',          'a'=>'+ RM 110.00', 'in'=>true],
];
foreach($txns as $t):
?>
                            <div class="flex items-center gap-2.5 py-2.5 px-2 -mx-2 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors group">
                                <span class="w-2 h-2 rounded-full shrink-0 <?= $t['in']?'bg-emerald-500':'bg-red-400' ?>"></span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-slate-800 truncate"><?= htmlspecialchars($t['n']) ?></p>
                                    <p class="text-xs text-slate-400"><?= htmlspecialchars($t['c']) ?></p>
                                </div>
                                <span class="text-xs font-bold shrink-0 <?= $t['in']?'text-emerald-600':'text-red-500' ?>"><?= $t['a'] ?></span>
                            </div>
<?php endforeach; ?>
                        </div>
                        <div class="flex justify-between mt-3 pt-3 border-t border-slate-100">
                            <span class="text-xs font-semibold cursor-pointer hover:underline" style="color:#163020">Revenue →</span>
                            <span class="text-xs font-semibold text-red-500 cursor-pointer hover:underline">Expenses →</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── EXPENSES PANEL ────────────────────────── -->
            <div id="panel-exp" class="hidden bg-slate-50 p-4">
                <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Expenses</h3>
                        <p class="text-xs text-slate-500">Track OPEX, Marketing &amp; COGS against your revenue targets</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button class="text-xs font-bold text-white px-4 py-2 rounded-lg cursor-pointer hover:opacity-90 transition-opacity" style="background:#163020">+ Add Expense</button>
                        <button class="text-xs font-semibold text-slate-600 border border-slate-200 bg-white px-3 py-2 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors">↓ Export</button>
                        <button class="text-xs font-semibold text-slate-600 border border-slate-200 bg-white px-3 py-2 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors">⚙ Configure Budget %</button>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-4 mb-5">
                    <button class="text-slate-400 hover:text-slate-700 cursor-pointer text-xl leading-none">‹</button>
                    <div class="text-center"><p class="font-bold text-slate-800 text-sm">May 2026</p><p class="text-xs text-slate-400">Current Month</p></div>
                    <button class="text-slate-400 hover:text-slate-700 cursor-pointer text-xl leading-none">›</button>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
<?php
$bc=[
    ['lbl'=>'25% of target','bg'=>'bg-blue-100 text-blue-700',    't'=>'OPEX',             's'=>'Rent, Utilities, Salaries','v'=>'RM 92.00', 'tg'=>'RM 750.00', 'p'=>12,'b'=>'bg-blue-500'],
    ['lbl'=>'5% of target', 'bg'=>'bg-purple-100 text-purple-700','t'=>'Marketing Expenses','s'=>'Ads, Promotions',           'v'=>'RM 0.00',  'tg'=>'RM 150.00', 'p'=>0, 'b'=>'bg-purple-400'],
    ['lbl'=>'40% of target','bg'=>'bg-yellow-100 text-yellow-700','t'=>'COGS',              's'=>'Raw Materials, Production',  'v'=>'RM 801.10','tg'=>'RM 1,200.00','p'=>67,'b'=>'bg-yellow-400'],
    ['lbl'=>'70.2% Net Profit','bg'=>'bg-emerald-100 text-emerald-700','t'=>'Expected Profit','s'=>'Revenue after expenses','v'=>'RM 2,106.90','tg'=>'RM 3,000.00','p'=>70,'b'=>'bg-emerald-500','pr'=>true],
];
foreach($bc as $c):
?>
                    <div class="bg-white border border-slate-100 rounded-xl p-3 shadow-sm card-hover cursor-pointer">
                        <span class="inline-block text-xs font-bold px-2 py-0.5 rounded-full mb-2 <?= $c['bg'] ?>"><?= $c['lbl'] ?></span>
                        <p class="font-bold text-slate-800 text-sm"><?= $c['t'] ?></p>
                        <p class="text-xs text-slate-400 mb-2"><?= $c['s'] ?></p>
                        <p class="font-black text-slate-900 <?= ($c['pr']??false)?'text-emerald-600':'' ?>"><?= $c['v'] ?></p>
                        <p class="text-xs text-slate-400">/ <?= $c['tg'] ?></p>
                        <div class="mt-2 h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full <?= $c['b'] ?> rounded-full" style="width:<?= $c['p'] ?>%"></div></div>
                    </div>
<?php endforeach; ?>
                </div>
                <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-1 h-4 rounded-full" style="background:#163020"></span>
                            <div><p class="font-bold text-slate-800 text-sm">Expenses</p><p class="text-xs text-slate-400">May 2026 · 4 records</p></div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="border border-slate-200 rounded-lg px-3 py-1.5 flex items-center gap-1.5 cursor-text">
                                <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <span class="text-xs text-slate-400">Cari deskripsi...</span>
                            </div>
                            <span class="font-bold text-slate-800 text-sm">RM 893.10</span>
                        </div>
                    </div>
                    <table class="w-full text-xs">
                        <thead><tr class="border-b border-slate-100 bg-slate-50">
                            <th class="text-left px-4 py-2 text-slate-400 font-semibold uppercase tracking-wider">Date</th>
                            <th class="text-left px-4 py-2 text-slate-400 font-semibold uppercase tracking-wider">Category</th>
                            <th class="text-left px-4 py-2 text-slate-400 font-semibold uppercase tracking-wider hidden sm:table-cell">Description</th>
                            <th class="text-right px-4 py-2 text-slate-400 font-semibold uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-2 hidden sm:table-cell"></th>
                        </tr></thead>
                        <tbody>
<?php
$er=[
    ['d'=>'27 May 2026','cat'=>'COGS','cc'=>'bg-yellow-100 text-yellow-700','desc'=>'Raw materials',      'a'=>'RM 44.30'],
    ['d'=>'18 May 2026','cat'=>'COGS','cc'=>'bg-yellow-100 text-yellow-700','desc'=>'Raw material (NSK)',  'a'=>'RM 360.80'],
    ['d'=>'15 May 2026','cat'=>'OPEX','cc'=>'bg-blue-100 text-blue-700',    'desc'=>'Grab express',        'a'=>'RM 92.00'],
    ['d'=>'04 May 2026','cat'=>'COGS','cc'=>'bg-yellow-100 text-yellow-700','desc'=>'Raw materials (NSK)', 'a'=>'RM 396.00'],
];
foreach($er as $r):
?>
                        <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors cursor-pointer group">
                            <td class="px-4 py-3 text-slate-500"><?= $r['d'] ?></td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full font-bold <?= $r['cc'] ?>"><?= $r['cat'] ?></span></td>
                            <td class="px-4 py-3 font-medium text-slate-700 group-hover:text-slate-900 hidden sm:table-cell"><?= htmlspecialchars($r['desc']) ?></td>
                            <td class="px-4 py-3 font-bold text-slate-800 text-right"><?= $r['a'] ?></td>
                            <td class="px-4 py-3 text-center hidden sm:table-cell"><span class="text-slate-400 hover:text-slate-700 cursor-pointer mr-2 transition-colors">✏</span><span class="text-red-400 hover:text-red-600 cursor-pointer transition-colors">🗑</span></td>
                        </tr>
<?php endforeach; ?>
                        <tr class="bg-slate-50 border-t-2 border-slate-200"><td class="px-4 py-2 font-bold text-slate-500 text-xs uppercase tracking-wider" colspan="3">TOTAL</td><td class="px-4 py-2 font-black text-slate-800 text-right" colspan="2">RM 893.10</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ── BALANCE SHEET PANEL ───────────────────── -->
            <div id="panel-bs" class="hidden bg-slate-50 p-4">
                <div class="max-w-2xl mx-auto space-y-4">
                    <div class="flex items-start justify-between">
                        <div><h3 class="text-lg font-black text-slate-900">Balance Sheet</h3><p class="text-xs text-slate-400">Statement of Financial Position</p></div>
                        <button class="text-xs font-bold text-white px-4 py-2 rounded-lg cursor-pointer hover:opacity-90 transition-opacity" style="background:#163020">↓ Download CSV ▾</button>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                        <p class="text-xs text-slate-500 mb-2">As at date</p>
                        <div class="flex gap-2">
                            <div class="flex-1 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 cursor-text bg-slate-50">31/05/2026</div>
                            <button class="px-4 py-2 text-sm font-semibold rounded-lg border border-slate-200 bg-white text-slate-700 cursor-pointer hover:bg-slate-50 transition-colors">Load</button>
                        </div>
                    </div>
                    <!-- P&L Summary -->
                    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="flex items-center justify-between px-4 py-3 cursor-pointer hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <span class="font-bold text-slate-800 text-sm">P&amp;L Summary</span>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Auto</span>
                            </div>
                            <span class="text-slate-400">∧</span>
                        </div>
                        <div class="border-t border-slate-100 px-4 py-3 space-y-1.5">
<?php
$pls=[
    ['l'=>'Revenue',             'v'=>'RM 2,050.00', 'c'=>'text-emerald-600 font-bold','indent'=>false],
    ['l'=>'Less: Cost of Sales', 'v'=>'RM 801.10 (−)','c'=>'text-red-500',             'indent'=>true],
    ['l'=>'Gross Profit',        'v'=>'RM 1,248.90',  'c'=>'font-bold text-slate-800', 'indent'=>false,'border'=>true],
    ['l'=>'Less: OPEX',          'v'=>'RM 92.00 (−)', 'c'=>'text-red-500',             'indent'=>true],
    ['l'=>'Less: Marketing',     'v'=>'RM 0.00',      'c'=>'text-slate-500',           'indent'=>true],
    ['l'=>'Net Profit / (Loss)', 'v'=>'RM 1,156.90',  'c'=>'text-emerald-600 font-black','indent'=>false,'border'=>true],
];
foreach($pls as $p):
?>
                            <div class="flex justify-between text-xs py-0.5 <?= ($p['indent']??false)?'pl-5':'' ?> <?= ($p['border']??false)?'border-t border-slate-200 pt-2 mt-1':'' ?>">
                                <span class="text-slate-600"><?= htmlspecialchars($p['l']) ?></span>
                                <span class="<?= $p['c'] ?>"><?= $p['v'] ?></span>
                            </div>
<?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Assets -->
                    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="px-4 py-3 font-bold text-white text-xs uppercase tracking-widest" style="background:#163020">ASSETS</div>
                        <div class="px-4 py-4 space-y-2">
                            <p class="font-bold text-slate-700 text-xs mb-2">Non-Current Asset</p>
                            <div class="flex justify-between text-xs items-center py-1.5 hover:bg-slate-50 rounded px-1 cursor-pointer transition-colors border-b border-slate-50">
                                <span class="text-slate-600">Property, plant and equipment <span class="text-xs font-semibold px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-600 ml-1">Auto</span></span>
                                <span class="font-bold text-slate-800">RM 0.00</span>
                            </div>
                            <div class="flex justify-between text-xs font-bold py-1.5 border-b border-slate-200">
                                <span class="text-slate-500">Total non-current asset</span><span class="text-slate-800">RM 0.00</span>
                            </div>
                            <p class="font-bold text-slate-700 text-xs mt-3 mb-2">Current Assets</p>
<?php foreach(['Inventories','Trade receivables','Other receivables'] as $a): ?>
                            <div class="flex justify-between text-xs items-center py-1.5 hover:bg-slate-50 rounded px-1 cursor-pointer transition-colors border-b border-slate-50">
                                <span class="text-slate-600"><?= htmlspecialchars($a) ?> <span class="text-xs font-semibold px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-600 ml-1">Auto</span></span>
                                <span class="font-bold text-slate-800">RM 0.00</span>
                            </div>
<?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /browser frame -->

        <div class="text-center mt-8">
            <a href="<?= $siteBase ?>/register" class="btn-gold btn-gold-shadow inline-flex items-center gap-2 px-8 py-3.5 rounded-xl text-sm" data-i18n="showcase_cta">Cuba Sendiri — Percuma →</a>
        </div>
    </div>
</section>

<style>
.active-tab   { color:#fff!important; border-color:transparent!important; background:#163020!important; }
.inactive-tab { color:#475569; border-color:#e2e8f0; background:#fff; }
.inactive-tab:hover { border-color:#163020; color:#163020; }
</style>
<script>
function showTab(name) {
    var urls  = {dash:'dashboard', exp:'expenses', bs:'balance-sheet'};
    var navs  = {dash:'dashboard', exp:'expenses', bs:'balance'};
    ['dash','exp','bs'].forEach(function(t) {
        var p = document.getElementById('panel-'+t);
        var b = document.getElementById('tab-'+t);
        if (p) p.classList.toggle('hidden', t !== name);
        if (b) { b.className = b.className.replace('active-tab','inactive-tab'); }
    });
    var btn = document.getElementById('tab-'+name);
    if (btn) btn.className = btn.className.replace('inactive-tab','active-tab');
    var bar = document.getElementById('url-bar');
    if (bar) bar.textContent = 'ezkira.com/' + (urls[name]||name);
    document.querySelectorAll('.mock-nav-item').forEach(function(el) {
        el.style.color=''; el.style.borderBottomColor='transparent';
    });
    var nav = document.getElementById('nav-'+navs[name]);
    if (nav) { nav.style.color='#C9A84C'; nav.style.borderBottomColor='#C9A84C'; }
}
</script>

<!-- ============================================================ -->
<!-- SECTION 7: SOCIAL PROOF                                      -->
<!-- ============================================================ -->
<section id="social-proof" class="py-24 bg-white section-fade">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-20">
            <?php
            $stats = [
                ['num'=>'2,500+', 'label'=>'Pemilik Bisnes', 'icon'=>'👤'],
                ['num'=>'RM 50M+', 'label'=>'Transaksi Direkod', 'icon'=>'💰'],
                ['num'=>'98%',     'label'=>'Kepuasan Pengguna', 'icon'=>'⭐'],
                ['num'=>'15 min',  'label'=>'Masa Setup Purata', 'icon'=>'⚡'],
            ];
            foreach ($stats as $s):
            ?>
            <div class="text-center p-6 bg-slate-50 rounded-2xl">
                <div class="text-3xl mb-2"><?= $s['icon'] ?></div>
                <div class="text-3xl font-black text-slate-900 mb-1"><?= $s['num'] ?></div>
                <div class="text-sm text-slate-500"><?= htmlspecialchars($s['label']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Testimonials -->
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-slate-900 mb-3" data-i18n="proof_h2">Apa Kata Pengguna Kami</h2>
            <p class="text-slate-500" data-i18n="proof_subtitle">Bisnes nyata. Hasil nyata.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <?php
            $testimonials = [
                [
                    'name'   => 'Aisyah Rahimi',
                    'role'   => 'Pemilik Kedai Pakaian Online',
                    'avatar' => 'AR',
                    'color'  => 'bg-purple-500',
                    'quote'  => '"Dulu saya guna Excel dan selalu keliru. Dengan Ezkira, dalam 5 minit saya dah boleh tengok untung rugi kedai saya. Laporan P&L pun boleh terus hantar ke akauntan!"',
                    'stars'  => 5,
                ],
                [
                    'name'   => 'Hafiz Mustaffa',
                    'role'   => 'Pengusaha F&B (2 cawangan)',
                    'avatar' => 'HM',
                    'color'  => 'bg-emerald-600',
                    'quote'  => '"Saya mula guna Ezkira masa nak sediakan dokumen untuk loan bank. Akauntan cakap rekod kewangan saya sangat tersusun. Loan pun approved! Highly recommended."',
                    'stars'  => 5,
                ],
                [
                    'name'   => 'Siti Norzahra',
                    'role'   => 'Freelancer & Service Provider',
                    'avatar' => 'SN',
                    'color'  => 'bg-emerald-500',
                    'quote'  => '"Sebagai freelancer, saya tak ada masa nak belajar perakaunan. Ezkira memudahkan segalanya. Kini saya tahu dengan tepat berapa yang saya earn setiap bulan."',
                    'stars'  => 5,
                ],
            ];
            foreach ($testimonials as $t):
            ?>
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm card-hover">
                <div class="flex mb-4">
                    <?php for ($i = 0; $i < $t['stars']; $i++): ?>
                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <?php endfor; ?>
                </div>
                <p class="text-slate-600 text-sm leading-relaxed mb-6 italic"><?= $t['quote'] ?></p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 <?= $t['color'] ?> rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0">
                        <?= $t['avatar'] ?>
                    </div>
                    <div>
                        <p class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($t['name']) ?></p>
                        <p class="text-slate-400 text-xs"><?= htmlspecialchars($t['role']) ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================ -->
<!-- SECTION 8: FAQ                                               -->
<!-- ============================================================ -->
<section id="faq" class="py-24 bg-cream section-fade">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block text-xs font-bold tracking-widest uppercase label-gold mb-4" data-i18n="faq_label">Soalan Lazim</span>
            <h2 class="text-4xl font-black text-slate-900 mb-4" data-i18n="faq_h2">Ada Soalan?<br><span class="gradient-text">Kami Ada Jawapannya</span></h2>
        </div>

        <div class="space-y-3" id="faq-list">
            <?php
            $faqs = [
                [
                    'q' => 'Adakah data kewangan saya selamat?',
                    'a' => 'Ya, 100% selamat. Data anda disulitkan menggunakan penyulitan peringkat bank. Kami tidak berkongsi maklumat anda dengan mana-mana pihak ketiga. Backup data dilakukan secara automatik setiap hari untuk memastikan data anda tidak hilang.',
                ],
                [
                    'q' => 'Boleh saya guna Ezkira untuk persediaan cukai?',
                    'a' => 'Ya! Ezkira menjana laporan kewangan yang lengkap termasuk P&L dan Balance Sheet yang boleh dikemukakan terus kepada akauntan atau digunakan untuk pengemukaan cukai pendapatan perniagaan (Form B/Business). Semua rekod disusun mengikut kategori yang mudah difahami oleh akauntan.',
                ],
                [
                    'q' => 'Laporan apa yang boleh saya jana dengan Ezkira?',
                    'a' => 'Ezkira menjana Laporan Untung & Rugi (P&L), Kunci Kira-Kira (Balance Sheet), Ringkasan Perbelanjaan mengikut kategori, Analitik Pendapatan, Analisis Costing dan Laporan Prestasi Bulanan. Semua laporan boleh diekspot dalam format yang mudah dikongsi.',
                ],
                [
                    'q' => 'Macam mana penyimpanan resit berfungsi?',
                    'a' => 'Anda boleh upload gambar resit terus dari telefon atau komputer. Setiap resit dikaitkan dengan transaksi perbelanjaan berkaitan dan disimpan dalam cloud dengan selamat. Anda boleh cari dan akses resit bila-bila masa menggunakan carian nama atau tarikh.',
                ],
                [
                    'q' => 'Adakah terdapat tempoh percubaan percuma?',
                    'a' => 'Ya! Anda boleh cuba Ezkira secara percuma tanpa memerlukan kad kredit. Daftar sekarang dan mula gunakan semua ciri platform. Tiada komitmen, tiada bayaran tersembunyi.',
                ],
                [
                    'q' => 'Adakah Ezkira sesuai untuk PKS dan perniagaan kecil?',
                    'a' => 'Ezkira direka khas untuk PKS, peniaga online, peniaga runcit, pemberi perkhidmatan dan pengusaha bebas (freelancer) di Malaysia. Antara muka yang mesra pengguna memudahkan pemilik bisnes yang tidak mahir perakaunan untuk menguruskan kewangan mereka dengan berkesan.',
                ],
                [
                    'q' => 'Bagaimana Costing Management berfungsi?',
                    'a' => 'Modul Costing Management membolehkan anda mengira kos sebenar produk atau perkhidmatan anda — termasuk kos bahan mentah, buruh, overhead dan kos tidak langsung. Sistem akan kira margin untung, harga jualan minimum dan Break-Even Point (BEP) secara automatik.',
                ],
                [
                    'q' => 'Bolehkah saya berkongsi data dengan akauntan saya?',
                    'a' => 'Ya. Anda boleh menjana laporan kewangan dan mengeksportnya untuk dikongsi dengan akauntan anda. Laporan Ezkira mengikuti format standard perakaunan yang mudah difahami oleh mana-mana profesional kewangan. Ini menjimatkan masa dan kos perundingan akauntan.',
                ],
            ];
            foreach ($faqs as $idx => $faq):
            ?>
            <div class="faq-item bg-white border border-slate-200 rounded-2xl overflow-hidden" data-idx="<?= $idx ?>">
                <button class="w-full flex items-center justify-between px-6 py-5 text-left gap-4"
                        onclick="toggleFaq(this)">
                    <span class="font-semibold text-slate-800 text-sm"><?= htmlspecialchars($faq['q']) ?></span>
                    <span class="faq-icon text-slate-400 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </span>
                </button>
                <div class="faq-body">
                    <p class="px-6 pb-5 text-slate-500 text-sm leading-relaxed"><?= htmlspecialchars($faq['a']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-12">
            <p class="text-slate-500 text-sm" data-i18n="faq_contact">Masih ada soalan? <a href="https://wa.me/60122541050?text=Saya%20ada%20soalan%20tentang%20Ezkira" class="font-semibold hover:underline" style="color:#C9A84C" target="_blank" rel="noopener noreferrer">Hubungi kami di WhatsApp →</a></p>
        </div>
    </div>
</section>

<!-- ============================================================ -->
<!-- SECTION 9: FINAL CTA                                         -->
<!-- ============================================================ -->
<section id="cta" class="py-24 section-fade" style="background: linear-gradient(135deg, #163020 0%, #1e4a2e 50%, #163020 100%);">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative">
        <!-- Glow -->
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
            <div class="w-96 h-96 rounded-full blur-3xl" style="background:rgba(201,168,76,0.12)"></div>
        </div>

        <div class="relative">
            <div class="inline-flex items-center gap-2 text-xs font-bold px-4 py-2 rounded-full mb-6" style="background:rgba(201,168,76,0.12);border:1px solid rgba(201,168,76,0.25);color:#E8D47A">
                <span class="w-2 h-2 rounded-full animate-pulse-slow" style="background:#C9A84C"></span>
                Mula Hari Ini — Percuma
            </div>

            <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white mb-6 leading-tight">
                Ketahui Keadaan<br>Kewangan Bisnes<br><span class="gradient-text">Anda Hari Ini</span>
            </h2>

            <p class="text-slate-400 text-xl mb-10 max-w-xl mx-auto">
                Semua data kewangan dalam satu platform yang mudah digunakan. Mulakan percuma — tiada kad kredit diperlukan.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?= $siteBase ?>/register"
                   class="btn-gold btn-gold-shadow inline-flex items-center justify-center gap-2 px-10 py-5 rounded-2xl text-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Mulakan Percuma Sekarang
                </a>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-6 mt-10 text-sm text-slate-400">
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Tiada Kad Kredit</span>
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Setup 2 Minit</span>
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Data 100% Selamat</span>
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Sokongan Bahasa Melayu</span>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================ -->
<!-- FOOTER                                                        -->
<!-- ============================================================ -->
<footer class="text-slate-400 pt-16 pb-8" style="background:#163020">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">

            <!-- Brand -->
            <div class="lg:col-span-2">
                <a href="<?= $baseUri ?>/" class="flex items-center gap-2.5 mb-4">
                    <img src="<?= $baseUri ?>/assets/img/logo.svg" alt="<?= htmlspecialchars($appName) ?>" class="h-8 w-auto brightness-200">
                </a>
                <p class="text-slate-400 text-sm leading-relaxed max-w-xs mb-5">
                    Platform pengurusan kewangan bisnes yang direka khas untuk usahawan dan PKS Malaysia.
                </p>
                <div class="flex items-center gap-3">
                    <a href="https://wa.me/60122541050" target="_blank" rel="noopener noreferrer"
                       class="w-9 h-9 bg-emerald-600 hover:bg-emerald-500 rounded-lg flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                    <a href="mailto:bizbuddyhq@gmail.com"
                       class="w-9 h-9 bg-slate-700 hover:bg-slate-600 rounded-lg flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Platform links -->
            <div>
                <h4 class="text-white font-semibold text-sm mb-4" data-i18n="footer_platform">Platform</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#features"     class="hover:text-white transition-colors">Ciri-Ciri</a></li>
                    <li><a href="#how-it-works" class="hover:text-white transition-colors" data-i18n="nav_howto">Cara Guna</a></li>
                    <li><a href="#showcase"     class="hover:text-white transition-colors">Dashboard Demo</a></li>
                    <li><a href="#faq"          class="hover:text-white transition-colors" data-i18n="nav_faq">FAQ</a></li>
                </ul>
            </div>

            <!-- Akaun links -->
            <div>
                <h4 class="text-white font-semibold text-sm mb-4" data-i18n="footer_account">Akaun</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="<?= $siteBase ?>/register" class="hover:text-white transition-colors" data-i18n="footer_register">Daftar Percuma</a></li>
                    <li><a href="<?= $siteBase ?>/login"    class="hover:text-white transition-colors" data-i18n="footer_login">Log Masuk</a></li>
                    <li><a href="https://wa.me/60122541050?text=Saya%20ingin%20tahu%20lebih%20lanjut%20tentang%20Ezkira" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors" data-i18n="footer_contact">Hubungi Kami</a></li>
                </ul>
                <div class="mt-6 pt-4 border-t border-slate-800">
                    <p class="text-xs mb-1">📞 <a href="tel:+60122541050" class="hover:text-white">+6012-2541050</a></p>
                    <p class="text-xs">✉️ <a href="mailto:bizbuddyhq@gmail.com" class="hover:text-white">bizbuddyhq@gmail.com</a></p>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-800 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-slate-500">© <?= date('Y') ?> <?= htmlspecialchars($appName) ?>. Hak cipta terpelihara.</p>
            <p class="text-xs text-slate-500" data-i18n="footer_built">Dibina untuk usahawan Malaysia 🇲🇾</p>
        </div>
    </div>
</footer>

<!-- ============================================================ -->
<!-- JAVASCRIPT                                                    -->
<!-- ============================================================ -->
<script>
(function () {
    'use strict';

    // ── Sticky nav ─────────────────────────────────────────────
    var navbar = document.getElementById('navbar');
    function updateNav() {
        if (window.scrollY > 20) {
            navbar.classList.add('nav-blur', 'shadow-lg'); navbar.style.background = 'rgba(22,48,32,0.95)';
            navbar.classList.remove('bg-transparent');
        } else {
            navbar.classList.remove('nav-blur', 'shadow-lg'); navbar.style.background = '';
            navbar.classList.add('bg-transparent');
        }
    }
    updateNav();
    window.addEventListener('scroll', updateNav, { passive: true });

    // ── Mobile hamburger ───────────────────────────────────────
    var hamburger   = document.getElementById('hamburger');
    var mobileMenu  = document.getElementById('mobile-menu');
    var hamOpen     = document.getElementById('ham-open');
    var hamClose    = document.getElementById('ham-close');
    var menuOpen    = false;

    hamburger.addEventListener('click', function () {
        menuOpen = !menuOpen;
        mobileMenu.classList.toggle('hidden', !menuOpen);
        hamOpen.classList.toggle('hidden', menuOpen);
        hamClose.classList.toggle('hidden', !menuOpen);
        // Solid nav background when menu is open
        navbar.style.background = menuOpen ? '#163020' : (window.scrollY > 20 ? 'rgba(22,48,32,0.95)' : '');
    });

    window.closeMobile = function () {
        menuOpen = false;
        mobileMenu.classList.add('hidden');
        hamOpen.classList.remove('hidden');
        hamClose.classList.add('hidden');
        navbar.style.background = window.scrollY > 20 ? 'rgba(22,48,32,0.95)' : '';
    };

    // ── Smooth scroll for anchor links ─────────────────────────
    document.querySelectorAll('a[href^="#"]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (!target) return;
            e.preventDefault();
            var offset = 72; // nav height
            var top = target.getBoundingClientRect().top + window.scrollY - offset;
            window.scrollTo({ top: top, behavior: 'smooth' });
        });
    });

    // ── FAQ accordion ─────────────────────────────────────────
    window.toggleFaq = function (btn) {
        var item = btn.closest('.faq-item');
        var body = item.querySelector('.faq-body');
        var isOpen = item.classList.contains('open');

        // Close all
        document.querySelectorAll('.faq-item').forEach(function (el) {
            el.classList.remove('open');
            el.querySelector('.faq-body').classList.remove('open');
            el.querySelector('.faq-body').style.maxHeight = '';
            el.style.borderColor = '';
        });

        if (!isOpen) {
            item.classList.add('open');
            body.classList.add('open');
            body.style.maxHeight = body.scrollHeight + 'px';
            item.style.borderColor = '#C9A84C';
        }
    };

    // ── IntersectionObserver: fade-in sections ─────────────────
    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08 });

    document.querySelectorAll('.section-fade').forEach(function (el) {
        observer.observe(el);
    });

})();
</script>


<script>
// ── i18n translations ──────────────────────────────────────────
var LANG_EN = {
    nav_features:'Features', nav_howto:'How It Works', nav_dashboard:'Dashboard', nav_faq:'FAQ',
    nav_login:'Login', nav_register:'Try Free',
    hero_badge:'Financial Platform for Malaysian SMEs',
    hero_h1:'Manage Your Business Finances<br><span class="gradient-text">Without the Headache</span>',
    hero_subtitle:'Track profit &amp; loss, manage costing, store receipts and automatically generate financial reports in one platform.',
    hero_cta1:'<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> Try Free &#8212; No Payment',
    hero_cta2:'<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> View Demo',
    hero_trust:'&#10003; No credit card required &nbsp;&middot;&nbsp; &#10003; 2-minute setup &nbsp;&middot;&nbsp; &#10003; Safe &amp; private data',
    problem_label:'Is This Your Problem?',
    problem_h2:'Still Managing Your Finances<br><span class="gradient-text">Manually?</span>',
    problem_subtitle:'Many business owners still struggle with the same problems. You are not alone.',
    problem_cta_text:'Ezkira solves all these problems',
    pain_0_title:'Scattered Receipts',        pain_0_desc:'Physical and digital receipts scattered without an organised storage system.',
    pain_1_title:'Messy Spreadsheets',         pain_1_desc:'Complex Excel formulas, inconsistent data and hard to update daily.',
    pain_2_title:"Don't Know Actual Profit",   pain_2_desc:"Money comes in but you don't know if the business is actually profitable.",
    pain_3_title:'Hard to Manage Tax',         pain_3_desc:'When tax time comes, panic searching for documents to prepare statements.',
    pain_4_title:'Reports Take Too Long',      pain_4_desc:'Hours wasted arranging data and preparing monthly financial reports.',
    pain_5_title:'Unorganised Financial Data', pain_5_desc:'No clear picture of expenses, income and current financial position.',
    feat_label:'Platform Features',
    feat_h2:'Everything You Need<br><span class="gradient-text">In One Platform</span>',
    feat_subtitle:'Designed for Malaysian business owners who want to manage finances more professionally.',
    feat_0_title:'Costing Management',    feat_0_desc:'Calculate product costs, profit margin and break-even point easily.',
    feat_1_title:'Expense Tracking',      feat_1_desc:'Record all business expenses with categories and monitor your budget.',
    feat_2_title:'Revenue Tracking',      feat_2_desc:'Record all sales and income, set targets and monitor achievements.',
    feat_3_title:'Receipt Storage',       feat_3_desc:'Save and manage digital receipts in one safe and organised place.',
    feat_4_title:'Profit & Loss Report',  feat_4_desc:'P&L statement automatically generated — ready for accountant or tax.',
    feat_5_title:'Balance Sheet',         feat_5_desc:'Automatic balance sheet — assets, liabilities and equity at a glance.',
    feat_6_title:'Financial Dashboard',   feat_6_desc:'Visual dashboard with charts and financial KPIs for your business.',
    feat_7_title:'Monthly Business Insights', feat_7_desc:'Monthly performance summary with trends and action recommendations.',
    howto_label:'How To Use',
    howto_h2:'Get Started In<br><span class="gradient-text">3 Easy Steps</span>',
    howto_subtitle:'Fast setup — no special training or accounting knowledge required.',
    step_0_title:'Enter Sales &amp; Expenses',  step_0_desc:'Record all your business transactions easily. Categorise expenses and record every sale.',
    step_1_title:'Upload Receipts',             step_1_desc:'Take a photo of receipts or upload directly. All receipts saved securely and searchable anytime.',
    step_2_title:'View Automatic Reports',      step_2_desc:'P&L, Balance Sheet and all financial reports generated automatically.',
    howto_cta:'<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> Start Now &#8212; Free',
    benefits_label:'Why Ezkira',
    benefits_h2:'More Time To<br><span class="gradient-text">Focus On Your Business</span>',
    benefits_subtitle:"Don't spend your precious time on financial administration. Let Ezkira handle the boring parts so you can focus on growing your business.",
    benefits_cta:'Try Free Now →',
    ben_0_title:'Save Time',                ben_0_desc:'Reduce financial management time by up to 80%.',
    ben_1_title:'Less Manual Work',          ben_1_desc:'Automate calculations, reports and data organisation.',
    ben_2_title:'Organised Records',         ben_2_desc:'All financial documents and data in one place.',
    ben_3_title:'Easy Account Management',   ben_3_desc:'Data ready for accountant — save professional costs.',
    ben_4_title:'Easy Tax Management',       ben_4_desc:'All records available for annual tax submission.',
    ben_5_title:'Better Decisions',          ben_5_desc:'Real-time data for making smart business decisions.',
    showcase_label:'Platform Preview',
    showcase_h2:'Professional &amp; Complete <span class="gradient-text">Dashboard</span>',
    showcase_subtitle:'All your business financial data in a clear, interactive and easy-to-understand view.',
    showcase_cta:'Try It Yourself &#8212; Free →',
    proof_h2:'What Our Users Say', proof_subtitle:'Real businesses. Real results.',
    stat_0:'Business Owners', stat_1:'Transactions Recorded', stat_2:'User Satisfaction', stat_3:'Average Setup Time',
    testimonial_0_name:'Aisyah Rahimi',  testimonial_0_role:'Online Fashion Shop Owner',
    testimonial_1_name:'Hafiz Mustaffa', testimonial_1_role:'F&amp;B Entrepreneur (2 outlets)',
    testimonial_2_name:'Siti Norzahra',  testimonial_2_role:'Freelancer &amp; Service Provider',
    faq_label:'Frequently Asked Questions',
    faq_h2:'Have Questions?<br><span class="gradient-text">We Have Answers</span>',
    faq_0_q:'Is my financial data safe?',
    faq_1_q:'Can I use Ezkira for tax preparation?',
    faq_2_q:'What reports can I generate with Ezkira?',
    faq_3_q:'How does receipt storage work?',
    faq_4_q:'Is there a free trial period?',
    faq_5_q:'Is Ezkira suitable for SMEs and small businesses?',
    faq_6_q:'How does Costing Management work?',
    faq_7_q:'Can I share data with my accountant?',
    faq_contact:'Still have questions? <a href="https://wa.me/60122541050" class="font-semibold hover:underline" style="color:#C9A84C" target="_blank">Contact us on WhatsApp →</a>',
    cta_h2:'Know Your Business<br>Financial Position<br><span class="gradient-text">Today</span>',
    cta_subtitle:'All financial data in one easy-to-use platform. Start free &#8212; no credit card required.',
    cta_btn:'Start Free Now',
    footer_tagline:'Business finance management platform designed for Malaysian entrepreneurs and SMEs.',
    footer_platform:'Platform', footer_account:'Account',
    footer_register:'Register Free', footer_login:'Login', footer_contact:'Contact Us',
    footer_built:'Built for Malaysian entrepreneurs 🇲🇾',
};

var _orig = {}, _lang = localStorage.getItem('ezlang') || 'ms';

function setLang(lang) {
    _lang = lang;
    localStorage.setItem('ezlang', lang);
    document.querySelectorAll('[data-i18n]').forEach(function(el) {
        var k = el.getAttribute('data-i18n');
        if (!_orig[k]) _orig[k] = el.innerHTML;
        el.innerHTML = (lang === 'en' && LANG_EN[k] !== undefined) ? LANG_EN[k] : (_orig[k] || el.innerHTML);
    });
    // Update toggle button styles
    ['ms','en'].forEach(function(l) {
        ['','  -mob'].forEach(function(sfx) {
            var btn = document.getElementById('lang-btn-' + l + sfx.trim());
            if (!btn) return;
            if (l === lang) {
                btn.style.background = 'rgba(201,168,76,0.9)'; btn.style.color = '#163020';
                btn.classList.remove('text-slate-400','text-slate-300');
            } else {
                btn.style.background = ''; btn.style.color = '';
                btn.classList.add(l==='en' ? 'text-slate-400' : 'text-slate-300');
            }
        });
    });
    // Update html lang attribute
    document.documentElement.lang = lang === 'en' ? 'en' : 'ms';
}

// Init
(function() {
    document.querySelectorAll('[data-i18n]').forEach(function(el) { _orig[el.getAttribute('data-i18n')] = el.innerHTML; });
    setLang(_lang);
})();
</script>

</body>
</html>
