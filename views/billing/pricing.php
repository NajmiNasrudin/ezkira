<?php
use App\Core\CSRF;
use App\Core\Plan;

$limit        = Plan::FREE_RECEIPT_LIMIT;
$launchEnd    = Plan::billingStart()->modify('-1 day');
$prelaunch    = !Plan::billingStarted();
$isFree       = $plan['tier'] === 'free';
$isPaidPro    = $plan['paid_until'] !== null;
$monthly      = Plan::priceLabel('monthly');
$yearly       = Plan::priceLabel('yearly');
$yearlySaving = 'RM' . number_format((Plan::PRICES['monthly'] * 12 - Plan::PRICES['yearly']) / 100, 2);

if ($isFree) {
    $usagePct  = min(100, $plan['count'] / $limit * 100);
    $usageText = __('plan_usage_count', ['count' => $plan['count'], 'limit' => $limit]);
} else {
    $usagePct  = min(100, $plan['bytes'] / Plan::PRO_STORAGE_BYTES * 100);
    $usageText = __('plan_usage_bytes', ['used' => Plan::formatBytes($plan['bytes']), 'limit' => '1 GB']);
}

if ($plan['launch_free']) {
    $statusText = __('plan_status_launch', ['date' => local_date($launchEnd)]);
} elseif ($isPaidPro) {
    $statusText = __('plan_status_paid', ['date' => local_date($plan['paid_until'])]);
} else {
    $statusText = __('plan_status_free');
}

$check = '<svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
?>

<div class="mb-6">
    <h1 class="text-2xl sm:text-[1.75rem] font-extrabold tracking-tight text-gray-900 dark:text-white"><?= __('pricing_title') ?></h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-2xl"><?= __('pricing_sub') ?></p>
</div>

<!-- Current plan -->
<div class="rounded-3xl bg-white dark:bg-gray-800 shadow-sm p-5 sm:p-6 mb-5">
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400"><?= __('plan_current') ?></p>
            <p class="mt-1 flex items-center gap-2 text-lg font-bold text-gray-900 dark:text-white">
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold <?= $isFree ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' : 'bg-brand-700 text-white dark:bg-sage-300 dark:text-brand-800' ?>">
                    <?= $isFree ? __('plan_free') : __('plan_pro') ?>
                </span>
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-200"><?= $statusText ?></span>
            </p>
        </div>
        <div class="sm:w-72">
            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                <span><?= $usageText ?></span>
            </div>
            <div class="h-2.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                <div class="h-full rounded-full <?= $usagePct >= 100 ? 'bg-red-400' : ($usagePct >= 75 ? 'bg-gold-400' : 'bg-brand-400') ?>" style="width: <?= max(2, round($usagePct, 1)) ?>%"></div>
            </div>
        </div>
    </div>
</div>

<?php if ($prelaunch): ?>
<div class="mb-5 flex items-start gap-3 p-4 rounded-2xl bg-sage-100 dark:bg-gray-800 border border-sage-200 dark:border-gray-700">
    <svg class="w-5 h-5 mt-0.5 shrink-0 text-brand-600 dark:text-sage-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    <div class="text-sm">
        <p class="font-bold text-gray-900 dark:text-white"><?= __('plan_notice_launch_title') ?></p>
        <p class="text-gray-600 dark:text-gray-400 mt-0.5"><?= __('plan_notice_launch_body', ['limit' => $limit]) ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Plans -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">

    <!-- Free -->
    <div class="rounded-3xl bg-white dark:bg-gray-800 shadow-sm p-6 sm:p-7 flex flex-col">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white"><?= __('plan_free') ?></h2>
            <?php if ($isFree): ?>
            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300"><?= __('plan_current_badge') ?></span>
            <?php endif; ?>
        </div>
        <p class="mt-3 ez-num">
            <span class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">RM0</span>
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= __('plan_free_price_note') ?></p>

        <ul class="mt-6 space-y-3 text-sm text-gray-700 dark:text-gray-300 flex-1">
            <li class="flex gap-2.5"><span class="text-brand-500 dark:text-sage-300"><?= $check ?></span><?= __('plan_feat_keyin') ?></li>
            <li class="flex gap-2.5"><span class="text-brand-500 dark:text-sage-300"><?= $check ?></span><?= __('plan_feat_reports') ?></li>
            <li class="flex gap-2.5"><span class="text-brand-500 dark:text-sage-300"><?= $check ?></span><?= __('plan_feat_balance') ?></li>
            <li class="flex gap-2.5"><span class="text-brand-500 dark:text-sage-300"><?= $check ?></span><?= __('plan_feat_free_receipts', ['limit' => $limit]) ?></li>
            <li class="flex gap-2.5"><span class="text-brand-500 dark:text-sage-300"><?= $check ?></span><?= __('plan_feat_old_receipts') ?></li>
        </ul>
    </div>

    <!-- Pro -->
    <div class="relative rounded-3xl bg-brand-700 text-white shadow-soft p-6 sm:p-7 flex flex-col overflow-hidden">
        <div class="pointer-events-none absolute -right-16 -top-16 w-56 h-56 rounded-full bg-white/5"></div>
        <div class="relative flex items-center justify-between">
            <h2 class="text-lg font-bold"><?= __('plan_pro') ?></h2>
            <?php if (!$isFree): ?>
            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-white/15 text-white"><?= __('plan_current_badge') ?></span>
            <?php endif; ?>
        </div>
        <p class="relative mt-3 ez-num flex items-baseline gap-1">
            <span class="text-4xl font-extrabold tracking-tight"><?= $monthly ?></span>
            <span class="text-sm text-white/70"><?= __('plan_per_month') ?></span>
        </p>
        <p class="relative text-sm text-white/70 mt-1">
            <?= $yearly ?><?= __('plan_per_year') ?> ·
            <span class="text-gold-300 font-semibold"><?= __('plan_yearly_save', ['amount' => $yearlySaving]) ?></span>
        </p>

        <ul class="relative mt-6 space-y-3 text-sm text-white/90 flex-1">
            <li class="flex gap-2.5"><span class="text-sage-300"><?= $check ?></span><?= __('plan_feat_everything_free') ?></li>
            <li class="flex gap-2.5"><span class="text-sage-300"><?= $check ?></span><?= __('plan_feat_pro_storage') ?></li>
            <li class="flex gap-2.5"><span class="text-sage-300"><?= $check ?></span><?= __('plan_feat_compress') ?></li>
            <li class="flex gap-2.5"><span class="text-sage-300"><?= $check ?></span><?= __('plan_feat_support') ?></li>
        </ul>

        <div class="relative mt-7 space-y-2.5">
            <?php if ($checkoutOpen): ?>
                <form method="POST" action="<?= BASE_URI ?>/billing/checkout">
                    <?= CSRF::field() ?>
                    <input type="hidden" name="interval" value="yearly">
                    <button type="submit" class="w-full flex items-center justify-between gap-2 h-12 px-5 rounded-full bg-sage-300 hover:bg-sage-200 text-brand-800 font-bold text-sm transition-colors">
                        <span class="text-left"><?= __('plan_pay_yearly', ['price' => $yearly]) ?></span>
                        <span class="shrink-0 whitespace-nowrap px-2 py-0.5 rounded-full bg-brand-700 text-white text-[11px]"><?= __('plan_recommended') ?></span>
                    </button>
                </form>
                <form method="POST" action="<?= BASE_URI ?>/billing/checkout">
                    <?= CSRF::field() ?>
                    <input type="hidden" name="interval" value="monthly">
                    <button type="submit" class="w-full h-12 px-5 rounded-full bg-white/10 hover:bg-white/15 text-white font-semibold text-sm transition-colors">
                        <?= __('plan_pay_monthly', ['price' => $monthly]) ?>
                    </button>
                </form>
            <?php else: ?>
                <div class="w-full h-12 px-5 rounded-full bg-white/10 text-white/80 font-semibold text-sm flex items-center justify-center text-center">
                    <?= __('plan_checkout_soon') ?>
                </div>
            <?php endif; ?>
            <p class="text-xs text-white/60 pt-1"><?= __('plan_once_note') ?></p>
            <?php if ($prelaunch): ?>
            <p class="text-xs text-white/60"><?= __('plan_prelaunch_note') ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($payments)): ?>
<!-- Payment history -->
<div class="rounded-3xl bg-white dark:bg-gray-800 shadow-sm p-5 sm:p-6 mb-8">
    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3"><?= __('plan_history') ?></h2>
    <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
        <?php foreach ($payments as $p):
            $tz    = new DateTimeZone(Plan::TIMEZONE);
            $start = new DateTimeImmutable($p['period_start'], $tz);
            $end   = new DateTimeImmutable($p['period_end'], $tz);
        ?>
        <div class="flex items-center justify-between gap-3 py-3 text-sm">
            <div class="min-w-0">
                <p class="font-semibold text-gray-800 dark:text-gray-100">
                    <?= __('plan_pro') ?> · <?= __('plan_interval_' . ($p['plan_interval'] === 'yearly' ? 'yearly' : 'monthly')) ?>
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500"><?= __('plan_history_period') ?>: <?= local_date($start) ?> – <?= local_date($end->modify('-1 day')) ?></p>
            </div>
            <p class="ez-num font-bold text-gray-900 dark:text-white whitespace-nowrap">RM<?= number_format($p['amount_sen'] / 100, 2) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- FAQ -->
<div class="rounded-3xl bg-white dark:bg-gray-800 shadow-sm p-5 sm:p-6">
    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-2"><?= __('plan_faq_title') ?></h2>
    <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
        <?php for ($i = 1; $i <= 5; $i++): ?>
        <details class="group py-3">
            <summary class="flex items-center justify-between gap-3 cursor-pointer list-none text-sm font-semibold text-gray-800 dark:text-gray-100">
                <?= __('plan_faq_q' . $i) ?>
                <svg class="w-4 h-4 shrink-0 text-gray-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><?= __('plan_faq_a' . $i, ['limit' => $limit]) ?></p>
        </details>
        <?php endfor; ?>
    </div>
</div>
