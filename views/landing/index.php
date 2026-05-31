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
                <a href="#features"   class="hover:text-white transition-colors">Ciri-ciri</a>
                <a href="#how-it-works" class="hover:text-white transition-colors">Cara Guna</a>
                <a href="#showcase"   class="hover:text-white transition-colors">Dashboard</a>
                <a href="#faq"        class="hover:text-white transition-colors">FAQ</a>
            </div>

            <!-- CTA buttons -->
            <div class="hidden md:flex items-center gap-3">
                <a href="<?= $siteBase ?>/login"
                   class="text-sm font-semibold text-slate-300 hover:text-white transition-colors px-4 py-2">
                    Log Masuk
                </a>
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
        <div id="mobile-menu" class="hidden md:hidden py-4 border-t border-white/10">
            <div class="flex flex-col gap-3 text-sm font-medium text-slate-300">
                <a href="#features"     class="py-2 hover:text-white" onclick="closeMobile()">Ciri-ciri</a>
                <a href="#how-it-works" class="py-2 hover:text-white" onclick="closeMobile()">Cara Guna</a>
                <a href="#showcase"     class="py-2 hover:text-white" onclick="closeMobile()">Dashboard</a>
                <a href="#faq"          class="py-2 hover:text-white" onclick="closeMobile()">FAQ</a>
                <div class="flex gap-3 pt-2">
                    <a href="<?= $siteBase ?>/login"    class="flex-1 text-center py-2.5 btn-outline-white rounded-xl">Log Masuk</a>
                    <a href="<?= $siteBase ?>/register" class="flex-1 text-center py-2.5 btn-gold rounded-xl">Cuba Percuma</a>
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
                    Platform Kewangan untuk PKS Malaysia
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
                       class="btn-gold btn-gold-shadow inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl text-base">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Cuba Percuma — Tiada Bayaran
                    </a>
                    <a href="#showcase"
                       class="btn-outline-white inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl text-base font-semibold">
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
            <span class="inline-block text-xs font-bold tracking-widest uppercase label-gold mb-4">Adakah Ini Masalah Anda?</span>
            <h2 class="text-4xl sm:text-5xl font-black text-slate-900 mb-5">Masih Urus Kewangan<br><span class="gradient-text">Secara Manual?</span></h2>
            <p class="text-slate-500 text-lg max-w-xl mx-auto">Ramai pemilik bisnes masih bergelut dengan masalah yang sama. Anda tidak bersendirian.</p>
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
            foreach ($problems as $p):
            ?>
            <div class="relative bg-red-50 border border-red-100 rounded-2xl p-6 card-hover">
                <div class="text-3xl mb-4"><?= $p['icon'] ?></div>
                <div class="absolute top-4 right-4 text-red-400 font-bold text-lg">✕</div>
                <h3 class="font-bold text-slate-800 text-base mb-2"><?= htmlspecialchars($p['title']) ?></h3>
                <p class="text-slate-500 text-sm leading-relaxed"><?= htmlspecialchars($p['desc']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Transition arrow -->
        <div class="text-center mt-16">
            <div class="inline-flex flex-col items-center gap-2">
                <p class="text-slate-500 text-sm font-medium">Ezkira menyelesaikan semua masalah ini</p>
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
            <span class="inline-block text-xs font-bold tracking-widest uppercase label-gold mb-4">Ciri-Ciri Platform</span>
            <h2 class="text-4xl sm:text-5xl font-black text-slate-900 mb-5">Semua Yang Anda Perlukan<br><span class="gradient-text">Dalam Satu Platform</span></h2>
            <p class="text-slate-500 text-lg max-w-xl mx-auto">Direka khas untuk pemilik bisnes Malaysia yang mahu urus kewangan dengan lebih profesional dan efisien.</p>
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
            foreach ($features as $f):
                $cls   = $colorMap[$f['color']] ?? 'border';
                $style = $inlineStyle[$f['color']] ?? '';
            ?>
            <div class="bg-white border border-slate-100 rounded-2xl p-6 card-hover shadow-sm">
                <div class="w-12 h-12 <?= $cls ?> rounded-xl flex items-center justify-center mb-4" style="<?= $style ?>">
                    <?= $f['icon'] ?>
                </div>
                <h3 class="font-bold text-slate-800 text-sm mb-2"><?= htmlspecialchars($f['title']) ?></h3>
                <p class="text-slate-500 text-sm leading-relaxed"><?= $f['desc'] ?></p>
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
            <span class="inline-block text-xs font-bold tracking-widest uppercase label-gold mb-4">Cara Penggunaan</span>
            <h2 class="text-4xl sm:text-5xl font-black text-slate-900 mb-5">Mulakan Dalam<br><span class="gradient-text">3 Langkah Mudah</span></h2>
            <p class="text-slate-500 text-lg">Setup pantas — tidak perlukan latihan khas atau pengetahuan perakaunan.</p>
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
                foreach ($steps as $s):
                ?>
                <div class="flex flex-col items-center text-center">
                    <div class="relative mb-8">
                        <div class="w-24 h-24 border-2 rounded-3xl flex items-center justify-center mb-0" style="<?= $stepBgStyle[$s['color']] ?>">
                            <svg class="w-10 h-10" style="<?= $stepIconStyle ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><?= $s['icon'] ?></svg>
                        </div>
                        <span class="absolute -top-3 -right-3 w-8 h-8 text-xs font-black rounded-xl flex items-center justify-center" style="<?= $stepNumStyle ?>"><?= $s['num'] ?></span>
                    </div>
                    <h3 class="font-bold text-slate-800 text-base mb-3"><?= htmlspecialchars($s['title']) ?></h3>
                    <p class="text-slate-500 text-sm leading-relaxed"><?= htmlspecialchars($s['desc']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="text-center mt-16">
            <a href="<?= $siteBase ?>/register"
               class="btn-gold btn-gold-shadow inline-flex items-center gap-2 px-8 py-4 rounded-2xl text-base">
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
                <span class="inline-block text-xs font-bold tracking-widest uppercase label-gold mb-4">Kenapa Ezkira</span>
                <h2 class="text-4xl sm:text-5xl font-black text-white mb-6">Lebih Masa Untuk<br><span class="gradient-text">Fokus Kepada Bisnes</span></h2>
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
                foreach ($benefits as $b):
                ?>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-5 hover:bg-white/10 transition-colors">
                    <div class="text-2xl mb-3"><?= $b['icon'] ?></div>
                    <h3 class="font-bold text-white text-sm mb-1.5"><?= htmlspecialchars($b['title']) ?></h3>
                    <p class="text-slate-400 text-xs leading-relaxed"><?= htmlspecialchars($b['desc']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================ -->
<!-- SECTION 6: DASHBOARD SHOWCASE                                -->
<!-- ============================================================ -->
<?php
// Screenshot tabs config — save files to assets/img/screenshots/
$screenshots = [
    ['file' => 'dashboard.png',  'label' => 'Dashboard',        'badge' => 'Gambaran Keseluruhan'],
    ['file' => 'expenses.png',   'label' => 'Perbelanjaan',      'badge' => 'Expense Tracking'],
    ['file' => 'reports.png',    'label' => 'Laporan P&L',       'badge' => 'Auto-generated'],
];
$hasAny = false;
foreach ($screenshots as $sc) {
    if (file_exists(BASE_PATH . '/assets/img/screenshots/' . $sc['file'])) { $hasAny = true; break; }
}
?>
<section id="showcase" class="py-24 bg-cream section-fade">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block text-xs font-bold tracking-widest uppercase label-gold mb-4">Platform Preview</span>
            <h2 class="text-4xl sm:text-5xl font-black text-slate-900 mb-5">Dashboard Yang <span class="gradient-text">Profesional & Lengkap</span></h2>
            <p class="text-slate-500 text-lg max-w-xl mx-auto">Semua data kewangan bisnes anda dalam paparan yang jelas, interaktif dan mudah difahami.</p>
        </div>

        <?php if ($hasAny): ?>
        <!-- Real screenshot tabs -->
        <div class="max-w-5xl mx-auto">
            <!-- Tab buttons -->
            <div class="flex flex-wrap gap-2 justify-center mb-8">
                <?php foreach ($screenshots as $i => $sc):
                    if (!file_exists(BASE_PATH . '/assets/img/screenshots/' . $sc['file'])) continue;
                ?>
                <button onclick="switchTab(<?= $i ?>)"
                        id="tab-btn-<?= $i ?>"
                        class="text-sm font-semibold px-5 py-2 rounded-full border-2 transition-all <?= $i === 0 ? 'text-white border-transparent' : 'text-slate-500 border-slate-200 hover:border-slate-300' ?>"
                        style="<?= $i === 0 ? 'background:#163020' : '' ?>">
                    <?= htmlspecialchars($sc['label']) ?>
                </button>
                <?php endforeach; ?>
            </div>

            <!-- Screenshot panels -->
            <?php foreach ($screenshots as $i => $sc):
                if (!file_exists(BASE_PATH . '/assets/img/screenshots/' . $sc['file'])) continue;
            ?>
            <div id="tab-panel-<?= $i ?>" class="<?= $i !== 0 ? 'hidden' : '' ?>">
                <!-- Browser chrome frame -->
                <div class="rounded-2xl overflow-hidden shadow-2xl border border-slate-200">
                    <!-- Fake browser bar -->
                    <div class="flex items-center gap-2 px-4 py-3" style="background:#1e293b">
                        <span class="w-3 h-3 rounded-full bg-red-400"></span>
                        <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                        <span class="w-3 h-3 rounded-full bg-green-400"></span>
                        <div class="flex-1 mx-4">
                            <div class="flex items-center gap-2 bg-slate-700 rounded-md px-3 py-1 max-w-xs mx-auto">
                                <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span class="text-slate-300 text-xs">ezkira.com/<?= strtolower(str_replace(' ', '', $sc['label'])) ?></span>
                            </div>
                        </div>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full" style="background:rgba(201,168,76,0.2);color:#C9A84C"><?= htmlspecialchars($sc['badge']) ?></span>
                    </div>
                    <!-- Screenshot -->
                    <img src="<?= $baseUri ?>/assets/img/screenshots/<?= $sc['file'] ?>"
                         alt="<?= htmlspecialchars($sc['label']) ?> — Ezkira"
                         class="w-full block"
                         loading="lazy">
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <script>
        function switchTab(idx) {
            var panels = document.querySelectorAll('[id^="tab-panel-"]');
            var btns   = document.querySelectorAll('[id^="tab-btn-"]');
            panels.forEach(function(p) { p.classList.add('hidden'); });
            btns.forEach(function(b) {
                b.style.background = '';
                b.classList.remove('text-white', 'border-transparent');
                b.classList.add('text-slate-500', 'border-slate-200');
            });
            var panel = document.getElementById('tab-panel-' + idx);
            var btn   = document.getElementById('tab-btn-'   + idx);
            if (panel) panel.classList.remove('hidden');
            if (btn) {
                btn.style.background = '#163020';
                btn.classList.add('text-white', 'border-transparent');
                btn.classList.remove('text-slate-500', 'border-slate-200');
            }
        }
        </script>

        <?php else: ?>
        <!-- Fallback: HTML mockup cards (while screenshots not yet uploaded) -->
        <div class="grid lg:grid-cols-3 gap-5">

            <!-- P&L Report Card -->
            <div class="lg:col-span-2 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm card-hover">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="font-bold text-slate-800">Laporan Untung &amp; Rugi (P&amp;L)</h3>
                        <p class="text-slate-500 text-xs mt-0.5">Januari – Mei 2026</p>
                    </div>
                    <span class="text-xs bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full font-semibold">✓ Auto-generated</span>
                </div>
                <div class="space-y-3">
                    <?php
                    $pnl = [
                        ['label'=>'Jualan Kasar',          'value'=>'RM 124,580', 'cls'=>'text-slate-800 font-bold', 'bar'=>100, 'color'=>'bg-emerald-600'],
                        ['label'=>'Kos Barang Dijual (COGS)', 'value'=>'- RM 42,000','cls'=>'text-red-600', 'bar'=>34, 'color'=>'bg-red-400'],
                        ['label'=>'Untung Kasar',           'value'=>'RM 82,580',  'cls'=>'text-emerald-700 font-semibold', 'bar'=>66, 'color'=>'bg-emerald-400'],
                        ['label'=>'Perbelanjaan Operasi',   'value'=>'- RM 28,500','cls'=>'text-red-600', 'bar'=>23, 'color'=>'bg-orange-400'],
                        ['label'=>'EBITDA',                 'value'=>'RM 54,080',  'cls'=>'text-slate-700 font-semibold', 'bar'=>43, 'color'=>'bg-blue-400'],
                        ['label'=>'Untung Bersih',          'value'=>'RM 54,080',  'cls'=>'text-emerald-700 font-bold text-base', 'bar'=>43, 'color'=>'bg-emerald-500'],
                    ];
                    foreach ($pnl as $row):
                    ?>
                    <div class="flex items-center gap-3">
                        <p class="text-sm text-slate-500 w-44 shrink-0"><?= htmlspecialchars($row['label']) ?></p>
                        <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full <?= $row['color'] ?> rounded-full" style="width:<?= $row['bar'] ?>%"></div>
                        </div>
                        <p class="text-sm <?= $row['cls'] ?> w-28 text-right shrink-0"><?= $row['value'] ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Balance Sheet Card -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm card-hover">
                <h3 class="font-bold text-slate-800 mb-1">Kunci Kira-Kira</h3>
                <p class="text-slate-500 text-xs mb-5">31 Mei 2026</p>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">ASET</p>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm"><span class="text-slate-600">Tunai &amp; Bank</span><span class="font-semibold text-slate-800">RM 38,200</span></div>
                            <div class="flex justify-between text-sm"><span class="text-slate-600">Stok</span><span class="font-semibold text-slate-800">RM 15,400</span></div>
                            <div class="flex justify-between text-sm"><span class="text-slate-600">Aset Tetap</span><span class="font-semibold text-slate-800">RM 22,000</span></div>
                            <div class="flex justify-between text-sm font-bold border-t pt-2"><span class="text-slate-800">Jumlah Aset</span><span class="text-blue-700">RM 75,600</span></div>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">LIABILITI</p>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm"><span class="text-slate-600">Hutang Semasa</span><span class="font-semibold text-red-600">RM 8,500</span></div>
                            <div class="flex justify-between text-sm font-bold border-t pt-2"><span class="text-slate-800">Ekuiti Pemilik</span><span class="text-emerald-600">RM 67,100</span></div>
                        </div>
                    </div>
                    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-3 text-center">
                        <p class="text-xs text-emerald-600 font-semibold">Nisbah Kecairan: <span class="text-emerald-700 font-black">4.5x</span></p>
                        <p class="text-xs text-emerald-500 mt-0.5">Kedudukan kewangan sihat ✓</p>
                    </div>
                </div>
            </div>

            <!-- Revenue Analytics -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm card-hover">
                <h3 class="font-bold text-slate-800 mb-1">Analitik Pendapatan</h3>
                <p class="text-slate-500 text-xs mb-5">Mengikut kategori</p>
                <div class="space-y-3">
                    <?php
                    $revenues = [
                        ['label'=>'Jualan Online','pct'=>55,'val'=>'RM 68,520','color'=>'bg-emerald-600'],
                        ['label'=>'Walk-in',      'pct'=>28,'val'=>'RM 34,882','color'=>'bg-emerald-700'],
                        ['label'=>'Wholesale',    'pct'=>17,'val'=>'RM 21,178','color'=>'bg-emerald-400'],
                    ];
                    foreach ($revenues as $r):
                    ?>
                    <div>
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="text-slate-600 font-medium"><?= $r['label'] ?></span>
                            <span class="text-slate-800 font-bold"><?= $r['val'] ?></span>
                        </div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full <?= $r['color'] ?> rounded-full" style="width:<?= $r['pct'] ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-500">Jumlah Pendapatan</span>
                    <span class="font-black text-slate-900">RM 124,580</span>
                </div>
            </div>

            <!-- Expense Analytics -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm card-hover">
                <h3 class="font-bold text-slate-800 mb-1">Analitik Perbelanjaan</h3>
                <p class="text-slate-500 text-xs mb-5">Breakdown kategori</p>
                <div class="space-y-3">
                    <?php
                    $exps = [
                        ['label'=>'COGS',          'pct'=>60,'val'=>'RM 42,000','color'=>'bg-red-500'],
                        ['label'=>'Gaji',          'pct'=>20,'val'=>'RM 14,000','color'=>'bg-orange-400'],
                        ['label'=>'Marketing',     'pct'=>11,'val'=>'RM 7,700', 'color'=>'bg-yellow-400'],
                        ['label'=>'Operasi Lain',  'pct'=>9, 'val'=>'RM 6,800', 'color'=>'bg-slate-400'],
                    ];
                    foreach ($exps as $e):
                    ?>
                    <div>
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="text-slate-600 font-medium"><?= $e['label'] ?></span>
                            <span class="text-slate-800 font-bold"><?= $e['val'] ?></span>
                        </div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full <?= $e['color'] ?> rounded-full" style="width:<?= $e['pct'] ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-500">Jumlah Perbelanjaan</span>
                    <span class="font-black text-slate-900">RM 70,500</span>
                </div>
            </div>

            <!-- Costing Analysis -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm card-hover">
                <h3 class="font-bold text-slate-800 mb-1">Analisis Costing</h3>
                <p class="text-slate-500 text-xs mb-5">Margin untung produk</p>
                <div class="space-y-4">
                    <?php
                    $products = [
                        ['name'=>'Produk A','cost'=>'RM 12','price'=>'RM 35','margin'=>66,'cls'=>'text-emerald-600'],
                        ['name'=>'Produk B','cost'=>'RM 28','price'=>'RM 55','margin'=>49,'cls'=>'text-slate-700'],
                        ['name'=>'Produk C','cost'=>'RM 5', 'price'=>'RM 18','margin'=>72,'cls'=>'text-emerald-600'],
                    ];
                    foreach ($products as $p):
                    ?>
                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-semibold text-slate-700"><?= $p['name'] ?></span>
                                <span class="<?= $p['cls'] ?> font-bold"><?= $p['margin'] ?>% margin</span>
                            </div>
                            <div class="text-xs text-slate-400">Kos: <?= $p['cost'] ?> &nbsp;→&nbsp; Jual: <?= $p['price'] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-5 rounded-xl p-3" style="background:#F0EBD8;border:1px solid #E8D47A">
                    <p class="text-xs font-medium text-center" style="color:#163020">📊 BEP dicapai pada RM 18,500/bulan</p>
                </div>
            </div>

        </div><!-- end grid -->
        <?php endif; ?>
    </div>
</section>

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
            <h2 class="text-3xl font-black text-slate-900 mb-3">Apa Kata Pengguna Kami</h2>
            <p class="text-slate-500">Bisnes nyata. Hasil nyata.</p>
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
            <span class="inline-block text-xs font-bold tracking-widest uppercase label-gold mb-4">Soalan Lazim</span>
            <h2 class="text-4xl font-black text-slate-900 mb-4">Ada Soalan?<br><span class="gradient-text">Kami Ada Jawapannya</span></h2>
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
            <p class="text-slate-500 text-sm">Masih ada soalan? <a href="https://wa.me/60122541050?text=Saya%20ada%20soalan%20tentang%20Ezkira" class="font-semibold hover:underline" style="color:#C9A84C" target="_blank" rel="noopener noreferrer">Hubungi kami di WhatsApp →</a></p>
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
                <h4 class="text-white font-semibold text-sm mb-4">Platform</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#features"     class="hover:text-white transition-colors">Ciri-Ciri</a></li>
                    <li><a href="#how-it-works" class="hover:text-white transition-colors">Cara Guna</a></li>
                    <li><a href="#showcase"     class="hover:text-white transition-colors">Dashboard Demo</a></li>
                    <li><a href="#faq"          class="hover:text-white transition-colors">FAQ</a></li>
                </ul>
            </div>

            <!-- Akaun links -->
            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Akaun</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="<?= $siteBase ?>/register" class="hover:text-white transition-colors">Daftar Percuma</a></li>
                    <li><a href="<?= $siteBase ?>/login"    class="hover:text-white transition-colors">Log Masuk</a></li>
                    <li><a href="https://wa.me/60122541050?text=Saya%20ingin%20tahu%20lebih%20lanjut%20tentang%20Ezkira" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors">Hubungi Kami</a></li>
                </ul>
                <div class="mt-6 pt-4 border-t border-slate-800">
                    <p class="text-xs mb-1">📞 <a href="tel:+60122541050" class="hover:text-white">+6012-2541050</a></p>
                    <p class="text-xs">✉️ <a href="mailto:bizbuddyhq@gmail.com" class="hover:text-white">bizbuddyhq@gmail.com</a></p>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-800 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-slate-500">© <?= date('Y') ?> <?= htmlspecialchars($appName) ?>. Hak cipta terpelihara.</p>
            <p class="text-xs text-slate-500">Dibina untuk usahawan Malaysia 🇲🇾</p>
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
    });

    window.closeMobile = function () {
        menuOpen = false;
        mobileMenu.classList.add('hidden');
        hamOpen.classList.remove('hidden');
        hamClose.classList.add('hidden');
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

</body>
</html>
