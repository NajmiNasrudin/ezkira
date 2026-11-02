<?php
use App\Core\Plan;

$e   = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
// Names are stored HTML-escaped at sign-up, so decode first or "&" shows as "&amp;"
$plain = fn($v) => html_entity_decode((string) $v, ENT_QUOTES | ENT_HTML5, 'UTF-8');
$nameOf = fn($v) => $e($plain($v));
$rm  = fn(float $v) => 'RM' . number_format($v, 2);
$pct = fn(int $part, int $whole) => $whole > 0 ? round($part / $whole * 100) . '%' : '0%';
$fmtDate = fn(?string $d) => $d && $d > '1971' ? date('d M Y', strtotime($d)) : '-';
$fmtAgo  = function (?string $d): string {
    if (!$d || $d < '1971') return __('adm_never');
    $days = (int) floor((time() - strtotime($d)) / 86400);
    return $days <= 0 ? __('adm_today') : ($days === 1 ? __('adm_yesterday') : __('adm_days_ago', ['n' => $days]));
};
// Personal Pro invite sent from the upsell list (always in Malay — the customers' language)
$pricingUrl = (defined('APP_URL') ? rtrim(APP_URL, '/') : 'https://ezkira.com') . '/pricing';
$inviteMsg  = fn(string $who, int $n) =>
    "Hi {$who}! 👋\n\n"
  . "Terima kasih sebab guna Ezkira untuk rekod bisnes. Saya perasan awak dah simpan {$n} resit dalam Ezkira, memang rajin! 🙌\n\n"
  . "Pelan Free boleh simpan sampai " . Plan::FREE_RECEIPT_LIMIT . " resit sahaja. Dengan Ezkira Pro, awak boleh simpan resit sampai 1 GB (lebih kurang 4,000 resit):\n"
  . "• " . Plan::priceLabel('monthly') . " sebulan, atau\n"
  . "• " . Plan::priceLabel('yearly') . " setahun (jimat 2 bulan)\n\n"
  . "Rekod jualan dan belanja tetap percuma dan unlimited.\n\n"
  . "Upgrade di sini: {$pricingUrl}\n\n"
  . "Kalau ada soalan, reply je mesej ni ya 😊";

$nowKl   = Plan::now()->format('Y-m-d H:i:s');
$isPro   = fn(array $u) => !empty($u['pro_until']) && $u['pro_until'] > $nowKl;

$card  = 'bg-white dark:bg-gray-800 rounded-3xl p-5 sm:p-6 shadow-sm';
$label = 'text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400';
$big   = 'ez-num text-3xl font-extrabold text-gray-900 dark:text-white mt-1';
$sub   = 'text-xs text-gray-500 dark:text-gray-400 mt-1';
$th    = 'px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 whitespace-nowrap';
$td    = 'px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap';

$kpis = [
    [__('adm_total_signups'), $users['total'],  __('adm_signups_note', ['today' => $users['today'], 'week' => $users['last7']])],
    [__('adm_pro'),           $users['pro'],    __('adm_pro_note', ['pct' => $pct($users['pro'], $users['total'])])],
    [__('adm_free'),          $users['free'],   __('adm_free_note', ['n' => $users['pro_expired']])],
    [__('adm_active30'),      $active['month'], __('adm_active_note', ['week' => $active['week'], 'day' => $active['day']])],
];

$maxDay   = max(1, ...array_values($perDay));
$maxMonth = max(1, ...array_values($perMonth));
?>

<!-- Header -->
<div class="flex flex-wrap items-end justify-between gap-3 mb-5">
    <div>
        <h1 class="text-2xl sm:text-[1.75rem] font-extrabold tracking-tight text-gray-900 dark:text-white"><?= __('admin_dashboard') ?></h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            <?= __('adm_subtitle', ['n' => $users['staff']]) ?>
        </p>
    </div>
    <span class="px-3 py-1.5 rounded-full bg-white dark:bg-gray-800 shadow-sm text-xs font-semibold text-gray-600 dark:text-gray-300">
        <?= __('adm_updated', ['time' => Plan::now()->format('d M Y, g:i A')]) ?>
    </span>
</div>

<!-- KPI cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <?php foreach ($kpis as $i => [$name, $value, $note]): ?>
        <div class="<?= $i === 1 ? 'bg-brand-700 text-white rounded-3xl p-5 sm:p-6 shadow-sm' : $card ?>">
            <p class="<?= $i === 1 ? 'text-xs font-semibold uppercase tracking-wide text-white/70' : $label ?>"><?= $name ?></p>
            <p class="ez-num text-3xl font-extrabold mt-1 <?= $i === 1 ? 'text-white' : 'text-gray-900 dark:text-white' ?>"><?= number_format($value) ?></p>
            <p class="text-xs mt-1 <?= $i === 1 ? 'text-white/70' : 'text-gray-500 dark:text-gray-400' ?>"><?= $e($note) ?></p>
        </div>
    <?php endforeach; ?>
</div>

<!-- Revenue + activity -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <div class="<?= $card ?>">
        <p class="<?= $label ?>"><?= __('adm_pro_revenue') ?></p>
        <p class="<?= $big ?>"><?= $rm($revenue['total']) ?></p>
        <p class="<?= $sub ?>"><?= __('adm_this_month') ?> <span class="font-bold text-gray-800 dark:text-gray-200"><?= $rm($revenue['this_month']) ?></span></p>
        <div class="grid grid-cols-3 gap-2 mt-4 text-center">
            <div class="rounded-2xl bg-gray-50 dark:bg-gray-700/50 p-3">
                <p class="ez-num text-lg font-bold text-gray-900 dark:text-white"><?= $revenue['paid_count'] ?></p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400"><?= __('adm_payments') ?></p>
            </div>
            <div class="rounded-2xl bg-gray-50 dark:bg-gray-700/50 p-3">
                <p class="ez-num text-lg font-bold text-gray-900 dark:text-white"><?= $revenue['monthly'] ?></p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400"><?= __('adm_monthly') ?></p>
            </div>
            <div class="rounded-2xl bg-gray-50 dark:bg-gray-700/50 p-3">
                <p class="ez-num text-lg font-bold text-gray-900 dark:text-white"><?= $revenue['yearly'] ?></p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400"><?= __('adm_yearly') ?></p>
            </div>
        </div>
        <p class="<?= $sub ?> mt-3"><?= __('adm_checkout_note', ['pending' => $revenue['pending'], 'failed' => $revenue['failed']]) ?></p>
    </div>

    <div class="<?= $card ?>">
        <p class="<?= $label ?>"><?= __('adm_user_activity') ?></p>
        <?php
        $actRows = [
            [__('adm_active_today'), $active['day']],
            [__('adm_active7'),      $active['week']],
            [__('adm_active30'),     $active['month']],
            [__('adm_has_records'),  $active['with_records']],
            [__('adm_no_records'),   $users['total'] - $active['with_records']],
        ];
        ?>
        <div class="mt-3 space-y-3">
            <?php foreach ($actRows as [$name, $n]): ?>
                <div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-300"><?= $name ?></span>
                        <span class="ez-num font-bold text-gray-900 dark:text-white"><?= number_format($n) ?> <span class="text-xs font-medium text-gray-400"><?= $pct($n, $users['total']) ?></span></span>
                    </div>
                    <div class="h-1.5 rounded-full bg-gray-100 dark:bg-gray-700 mt-1.5 overflow-hidden">
                        <div class="h-full rounded-full bg-brand-600 dark:bg-sage-300" style="width: <?= $users['total'] ? min(100, $n / $users['total'] * 100) : 0 ?>%"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="<?= $sub ?> mt-3"><?= __('adm_active_def') ?></p>
    </div>

    <div class="<?= $card ?>">
        <p class="<?= $label ?>"><?= __('adm_app_usage') ?></p>
        <div class="grid grid-cols-2 gap-3 mt-3">
            <?php foreach ([
                [__('adm_sales_records'),   number_format($usage['revenues'])],
                [__('adm_expense_records'), number_format($usage['expenses'])],
                [__('adm_receipts_stored'), number_format($usage['receipts'])],
                [__('adm_receipt_storage'), Plan::formatBytes($usage['receipt_bytes'])],
            ] as [$name, $val]): ?>
                <div class="rounded-2xl bg-gray-50 dark:bg-gray-700/50 p-3">
                    <p class="ez-num text-lg font-bold text-gray-900 dark:text-white"><?= $val ?></p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400"><?= $name ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="<?= $sub ?> mt-3"><?= __('adm_total_sales', ['amt' => $rm($usage['revenue_amount'])]) ?></p>
        <p class="<?= $sub ?>"><?= __('adm_total_expenses', ['amt' => $rm($usage['expense_amount'])]) ?></p>
        <p class="<?= $sub ?>"><?= __('adm_google_signups', ['n' => $users['google'], 'pct' => $pct($users['google'], $users['total'])]) ?></p>
    </div>
</div>

<!-- Sign-up charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
    <div class="<?= $card ?>">
        <div class="flex items-center justify-between">
            <p class="<?= $label ?>"><?= __('adm_signups_30') ?></p>
            <span class="text-xs font-semibold text-gray-500"><?= __('adm_people', ['n' => $users['last30']]) ?></span>
        </div>
        <div class="flex items-end gap-[3px] h-36 mt-4">
            <?php foreach ($perDay as $day => $n): ?>
                <div class="flex-1 h-full flex flex-col justify-end" title="<?= $e(date('d M', strtotime($day))) ?>: <?= $n ?>">
                    <div class="rounded-t bg-brand-600 dark:bg-sage-300 <?= $n ? '' : 'opacity-20' ?>"
                         style="height: <?= $n ? max(4, $n / $maxDay * 100) : 2 ?>%"></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="flex justify-between text-[11px] text-gray-400 mt-2">
            <span><?= $e(date('d M', strtotime(array_key_first($perDay)))) ?></span>
            <span><?= __('adm_today') ?></span>
        </div>
    </div>

    <div class="<?= $card ?>">
        <div class="flex items-center justify-between">
            <p class="<?= $label ?>"><?= __('adm_signups_12') ?></p>
            <span class="text-xs font-semibold text-gray-500"><?= __('adm_this_month_n', ['n' => $users['this_month']]) ?></span>
        </div>
        <div class="flex items-end gap-1.5 h-36 mt-4">
            <?php foreach ($perMonth as $month => $n): ?>
                <div class="flex-1 h-full flex flex-col justify-end items-center" title="<?= $e(date('M Y', strtotime($month . '-01'))) ?>: <?= $n ?>">
                    <span class="ez-num text-[10px] font-bold text-gray-500 mb-1"><?= $n ?: '' ?></span>
                    <div class="w-full rounded-t bg-sage-300 dark:bg-brand-400 <?= $n ? '' : 'opacity-30' ?>"
                         style="height: <?= $n ? max(4, $n / $maxMonth * 85) : 2 ?>%"></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="flex gap-1.5 text-[10px] text-gray-400 mt-2">
            <?php foreach ($perMonth as $month => $n): ?>
                <span class="flex-1 text-center"><?= $e(date('M', strtotime($month . '-01'))) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Breakdown -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <div class="<?= $card ?> lg:col-span-2">
        <p class="<?= $label ?>"><?= __('adm_business_types') ?></p>
        <div class="mt-3 space-y-2.5">
            <?php
            // Show the 12 biggest types; free-text one-offs are grouped as "Others" ('+')
            // The form's own "other" choice joins that group too, so there is one Others row
            $otherCount = array_sum(array_map(fn($r) => (int) $r['n'], array_filter($businessTypes, fn($r) => $r['type'] === 'other')));
            $typeRows   = array_values(array_filter($businessTypes, fn($r) => $r['type'] !== 'other'));
            $shownTypes = array_slice($typeRows, 0, 12);
            $restCount  = $otherCount + array_sum(array_map(fn($r) => (int) $r['n'], array_slice($typeRows, 12)));
            if ($restCount > 0) {
                $shownTypes[] = ['type' => '+', 'n' => $restCount];
            }
            $maxType = max(1, ...array_map(fn($r) => (int) $r['n'], $shownTypes ?: [['n' => 1]]));
            ?>
            <?php foreach ($shownTypes as $row): ?>
                <div class="flex items-center gap-3 text-sm">
                    <span class="w-36 shrink-0 truncate text-gray-600 dark:text-gray-300"><?= $row['type'] === '-' ? __('adm_not_stated') : ($row['type'] === '+' ? __('adm_others') : $e(ucwords(str_replace('_', ' ', $row['type'])))) ?></span>
                    <div class="flex-1 h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                        <div class="h-full rounded-full bg-brand-600 dark:bg-sage-300" style="width: <?= $row['n'] / $maxType * 100 ?>%"></div>
                    </div>
                    <span class="ez-num w-10 text-right font-bold text-gray-900 dark:text-white"><?= (int) $row['n'] ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="<?= $card ?>">
        <p class="<?= $label ?>"><?= __('adm_language') ?></p>
        <div class="mt-3 space-y-2">
            <?php foreach ($languages as $row): ?>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-300"><?= $row['language'] === 'ms' ? 'Bahasa Melayu' : 'English' ?></span>
                    <span class="ez-num font-bold text-gray-900 dark:text-white"><?= (int) $row['n'] ?> <span class="text-xs font-medium text-gray-400"><?= $pct((int) $row['n'], $users['total']) ?></span></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
/** Shared user table: name/email, plan, sign-up, last active, records. */
$userTable = function (array $rows, string $empty) use ($e, $nameOf, $th, $td, $fmtDate, $fmtAgo, $isPro): void { ?>
    <div class="overflow-x-auto -mx-2 mt-3">
        <table class="min-w-full">
            <thead><tr>
                <th class="<?= $th ?>"><?= __('adm_col_user') ?></th><th class="<?= $th ?>"><?= __('adm_col_plan') ?></th><th class="<?= $th ?>"><?= __('adm_col_joined') ?></th>
                <th class="<?= $th ?>"><?= __('adm_col_last_active') ?></th><th class="<?= $th ?>"><?= __('adm_col_records') ?></th><th class="<?= $th ?>">WhatsApp</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            <?php if (!$rows): ?>
                <tr><td colspan="6" class="<?= $td ?> text-center text-gray-400"><?= $empty ?></td></tr>
            <?php endif; ?>
            <?php foreach ($rows as $u): ?>
                <tr>
                    <td class="<?= $td ?>">
                        <p class="font-semibold text-gray-900 dark:text-white"><?= $nameOf($u['name']) ?><?= $u['via_google'] ? ' <span class="text-[10px] font-semibold text-gray-400">G</span>' : '' ?></p>
                        <p class="text-xs text-gray-500"><?= $e($u['email']) ?></p>
                    </td>
                    <td class="<?= $td ?>">
                        <?php if ($isPro($u)): ?>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-brand-700 text-white">Pro</span>
                            <span class="block text-[11px] text-gray-400 mt-0.5"><?= __('adm_until', ['date' => $fmtDate($u['pro_until'])]) ?></span>
                        <?php else: ?>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">Free</span>
                        <?php endif; ?>
                    </td>
                    <td class="<?= $td ?>"><?= $fmtDate($u['created_at']) ?></td>
                    <td class="<?= $td ?>"><?= $fmtAgo($u['last_active']) ?></td>
                    <td class="<?= $td ?> ez-num"><?= (int) $u['n_revenues'] ?> / <?= (int) $u['n_expenses'] ?></td>
                    <td class="<?= $td ?>">
                        <?php $wa = preg_replace('/\D/', '', (string) $u['whatsapp_number']); ?>
                        <?php if ($wa): ?>
                            <a href="https://wa.me/<?= $e($wa) ?>" target="_blank" rel="noopener" class="text-brand-600 dark:text-sage-300 font-semibold hover:underline"><?= $e($u['whatsapp_number']) ?></a>
                        <?php else: ?>-<?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php }; ?>

<!-- Recent sign-ups -->
<div class="<?= $card ?> mb-5">
    <p class="<?= $label ?>"><?= __('adm_recent_signups') ?></p>
    <?php $userTable($recentUsers, __('adm_no_signups')); ?>
</div>

<!-- Most active users -->
<div class="<?= $card ?> mb-5">
    <p class="<?= $label ?>"><?= __('adm_top_users') ?></p>
    <?php $userTable($topUsers, __('adm_no_active_users')); ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
    <!-- Upsell candidates -->
    <div class="<?= $card ?>">
        <p class="<?= $label ?>"><?= __('adm_upsell_title', ['n' => Plan::FREE_RECEIPT_LIMIT]) ?></p>
        <p class="<?= $sub ?>"><?= __('adm_upsell_note') ?></p>
        <div class="mt-3 divide-y divide-gray-100 dark:divide-gray-700">
            <?php if (!$upsell): ?>
                <p class="text-sm text-gray-400 py-3"><?= __('adm_none_now') ?></p>
            <?php endif; ?>
            <?php foreach ($upsell as $u):
                $wa = preg_replace('/\D/', '', (string) $u['whatsapp_number']);
                if (str_starts_with($wa, '0')) { $wa = '60' . substr($wa, 1); }
                $msg = $inviteMsg($plain($u['name']), (int) $u['n_receipts']);
                $sent = !empty($u['invited_at']); ?>
                <div class="flex items-center justify-between gap-3 py-2.5 <?= $sent ? 'opacity-60' : '' ?>" data-invite-row>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate"><?= $nameOf($u['name']) ?></p>
                        <p class="text-xs text-gray-500 truncate"><?= $e($u['email']) ?></p>
                        <p class="text-[11px] font-semibold text-brand-600 dark:text-sage-300 mt-0.5 <?= $sent ? '' : 'hidden' ?>" data-invite-status>
                            <?= $sent ? __('adm_invite_sent', ['when' => mb_strtolower($fmtAgo($u['invited_at'])), 'n' => (int) $u['invite_count']]) : '' ?>
                        </p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <span class="ez-num text-sm font-bold text-gray-900 dark:text-white"><?= __('adm_receipts_n', ['n' => (int) $u['n_receipts']]) ?></span>
                        <?php if ($wa): ?>
                            <a href="https://wa.me/<?= $e($wa) ?>?text=<?= $e(rawurlencode($msg)) ?>" target="_blank" rel="noopener"
                               data-invite-url="<?= BASE_URI ?>/dashboard-admin/invite/<?= (int) $u['id'] ?>"
                               data-sent-label="<?= $e(__('adm_invite_again')) ?>"
                               class="px-3 py-1 rounded-full text-xs font-bold <?= $sent ? 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200' : 'bg-[#25D366] text-white' ?>">
                                <?= $sent ? __('adm_invite_again') : __('adm_invite') ?>
                            </a>
                        <?php else: ?>
                            <span class="text-xs text-gray-400"><?= __('adm_no_phone') ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Payments -->
    <div class="<?= $card ?>">
        <p class="<?= $label ?>"><?= __('adm_recent_payments') ?></p>
        <div class="mt-3 divide-y divide-gray-100 dark:divide-gray-700">
            <?php if (!$recentPayments): ?>
                <p class="text-sm text-gray-400 py-3"><?= __('adm_no_payments') ?></p>
            <?php endif; ?>
            <?php foreach ($recentPayments as $p):
                $badge = match ($p['status']) {
                    'paid'   => [__('adm_paid'), 'bg-brand-700 text-white'],
                    'failed' => [__('adm_failed'), 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300'],
                    default  => [__('adm_unpaid'), 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'],
                }; ?>
                <div class="flex items-center justify-between gap-3 py-2.5">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate"><?= $p['name'] !== null ? $nameOf($p['name']) : $e('User #' . $p['user_id']) ?></p>
                        <p class="text-xs text-gray-500 truncate">
                            <?= $p['plan_interval'] === 'yearly' ? __('adm_yearly') : __('adm_monthly') ?> · <?= $fmtDate($p['paid_at'] ?: $p['created_at']) ?><?= $p['is_test'] ? ' · TEST' : '' ?>
                        </p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="ez-num text-sm font-bold text-gray-900 dark:text-white"><?= $rm($p['amount_sen'] / 100) ?></span>
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold <?= $badge[1] ?>"><?= $badge[0] ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
// Upsell list: open the WhatsApp invite, then remember it was sent
document.querySelectorAll('[data-invite-url]').forEach(function (link) {
    link.addEventListener('click', function () {
        postJson(link.dataset.inviteUrl).then(function (res) {
            if (!res.ok) return;
            var row = link.closest('[data-invite-row]');
            var status = row.querySelector('[data-invite-status]');
            status.textContent = <?= json_encode(__('adm_invite_sent_now')) ?>;
            status.classList.remove('hidden');
            row.classList.add('opacity-60');
            link.textContent = link.dataset.sentLabel;
            link.classList.remove('bg-[#25D366]', 'text-white');
            link.classList.add('bg-gray-200', 'text-gray-700');
        }).catch(function () {});
    });
});
</script>
