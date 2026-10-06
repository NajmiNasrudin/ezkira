<?php
use App\Core\Auth;
use App\Core\Plan;

$planStatus = Plan::status((int) Auth::id());
$planLimit  = Plan::FREE_RECEIPT_LIMIT;
$notice     = null;

if (!Plan::billingStarted()) {
    $notice = [
        'tone'    => 'info',
        'title'   => __('plan_notice_launch_title'),
        'body'    => __('plan_notice_launch_body', ['limit' => $planLimit]),
        'cta'     => __('plan_see_pricing'),
        'dismiss' => 'ez-plan-notice-launch-2026-11',
    ];
} elseif ($planStatus['tier'] === 'free' && $planStatus['count'] >= $planLimit) {
    $notice = ['tone' => 'warn', 'title' => __('plan_notice_at_limit', ['limit' => $planLimit]), 'body' => '', 'cta' => __('plan_upgrade'), 'dismiss' => null];
} elseif ($planStatus['tier'] === 'free' && $planStatus['count'] >= $planLimit - 5) {
    $notice = ['tone' => 'info', 'title' => __('plan_notice_near_limit', ['count' => $planStatus['count'], 'limit' => $planLimit]), 'body' => '', 'cta' => __('plan_upgrade'), 'dismiss' => null];
} elseif ($planStatus['paid_until'] !== null && $planStatus['paid_until'] <= Plan::now()->modify('+7 days')) {
    $notice = ['tone' => 'warn', 'title' => __('plan_notice_expiring', ['date' => local_date($planStatus['paid_until'])]), 'body' => '', 'cta' => __('plan_renew'), 'dismiss' => null];
}

if ($notice === null) {
    return;
}
$warn = $notice['tone'] === 'warn';
?>
<div id="plan-notice" class="mb-5 flex items-start gap-3 p-4 sm:p-5 rounded-3xl border <?= $warn
    ? 'bg-gold-50 border-gold-200 dark:bg-gray-800 dark:border-gold-700/60'
    : 'bg-sage-100 border-sage-200 dark:bg-gray-800 dark:border-gray-700' ?>">
    <span class="w-10 h-10 shrink-0 rounded-2xl flex items-center justify-center <?= $warn ? 'bg-white text-gold-600 dark:bg-gray-700 dark:text-gold-300' : 'bg-white text-brand-600 dark:bg-gray-700 dark:text-sage-300' ?>">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="<?= $warn
                ? 'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z'
                : 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9' ?>"/>
        </svg>
    </span>
    <div class="flex-1 min-w-0">
        <p class="text-sm font-bold text-gray-900 dark:text-white"><?= $notice['title'] ?></p>
        <?php if ($notice['body'] !== ''): ?>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5"><?= $notice['body'] ?></p>
        <?php endif; ?>
        <a href="<?= BASE_URI ?>/pricing"
           class="inline-flex items-center gap-1 mt-2.5 h-9 px-4 rounded-full bg-brand-700 hover:bg-brand-600 dark:bg-sage-300 dark:text-brand-800 text-white text-sm font-semibold transition-colors">
            <?= $notice['cta'] ?>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
    <?php if ($notice['dismiss']): ?>
    <button type="button" aria-label="Tutup"
            onclick="try { localStorage.setItem('<?= $notice['dismiss'] ?>', '1'); } catch (e) {} document.getElementById('plan-notice').remove();"
            class="w-8 h-8 shrink-0 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-white/60 dark:hover:bg-gray-700">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <script>
        try { if (localStorage.getItem('<?= $notice['dismiss'] ?>')) document.getElementById('plan-notice').remove(); } catch (e) {}
    </script>
    <?php endif; ?>
</div>
