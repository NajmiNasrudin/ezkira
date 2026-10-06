<?php
use App\Core\Auth;
use App\Core\CSRF;

$user        = Auth::user();
$role        = $user['role'] ?? 'guest';
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$currentPath = rtrim($currentPath, '/') ?: '/';

$roleLabel = match($role) {
    'admin'  => __('role_admin'),
    'team'   => __('role_team'),
    default  => __('role_client'),
};

$navIcons = [
    '/dashboard'     => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    '/revenue'       => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
    '/expenses'      => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z',
    '/balance-sheet' => 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3',
    '/profile'       => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    '/blast'         => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
];

$navLinks = [
    '/dashboard'    => __('dashboard'),
    '/revenue'      => __('revenue'),
    '/expenses'     => __('expenses'),
    '/balance-sheet'=> __('balance_sheet'),
    '/profile'      => __('profile'),
];

// WhatsApp Blast — admin only
if ($role === 'admin') {
    $navLinks['/blast'] = 'WhatsApp';
}

$isNavActive = fn(string $path): bool => str_starts_with($currentPath, $path);
?>

<!-- Desktop Sidebar -->
<aside class="hidden lg:flex fixed inset-y-0 left-0 z-40 w-64 flex-col bg-brand-700 text-white">
    <!-- Brand -->
    <a href="<?= BASE_URI ?>/dashboard" class="flex items-center gap-3 px-6 h-20 shrink-0">
        <img src="<?= asset_url('assets/img/logo-mark.svg') ?>" alt="ezkira" class="w-10 h-10 rounded-xl ring-1 ring-white/20">
        <span class="text-lg font-extrabold tracking-wide leading-none">
            <span class="text-gold-400">ez</span><span class="text-white">kira</span>
        </span>
    </a>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-4 py-2 space-y-1">
        <?php foreach ($navLinks as $href => $label): $active = $isNavActive($href); ?>
            <a href="<?= BASE_URI . $href ?>"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm transition-colors <?= $active
                   ? 'bg-sage-200 text-brand-800 font-bold shadow-sm'
                   : 'text-white/70 hover:text-white hover:bg-white/10 font-medium' ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="<?= $active ? '2.2' : '1.8' ?>">
                    <path stroke-linecap="round" stroke-linejoin="round" d="<?= $navIcons[$href] ?>"/>
                </svg>
                <?= htmlspecialchars($label, ENT_QUOTES) ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- User card -->
    <div class="p-4 shrink-0">
        <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/10">
            <?php if (!empty($user['profile_image'])): ?>
                <img src="<?= BASE_URI ?>/<?= htmlspecialchars($user['profile_image'], ENT_QUOTES) ?>"
                     alt="Avatar" class="w-10 h-10 rounded-full object-cover ring-2 ring-white/20">
            <?php else: ?>
                <div class="w-10 h-10 rounded-full bg-sage-300 text-brand-800 flex items-center justify-center text-sm font-bold">
                    <?= strtoupper(mb_substr($user['name'] ?? 'U', 0, 1)) ?>
                </div>
            <?php endif; ?>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold truncate"><?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES) ?></p>
                <p class="text-xs text-white/60 truncate"><?= htmlspecialchars($roleLabel, ENT_QUOTES) ?></p>
            </div>
            <form method="POST" action="<?= BASE_URI ?>/logout">
                <?= CSRF::field() ?>
                <button type="submit" title="<?= __('logout') ?>"
                        class="p-2 rounded-xl text-white/60 hover:text-white hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
