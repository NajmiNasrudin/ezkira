<?php
/**
 * @var array $log  — blast_logs row
 */
$blastId   = (int)$log['id'];
$total     = (int)$log['total_recipients'];
$sent      = (int)$log['sent_count'];
$failed    = (int)$log['failed_count'];
$status    = $log['status'];
$schedAt   = $log['scheduled_at'];
$startedAt = $log['started_at'];
$finAt     = $log['finished_at'];
$msg       = $log['custom_message'] ?? '';

$done      = $sent + $failed;
$pct       = $total > 0 ? round($done / $total * 100) : 0;
$remaining = $total - $done;

$etaStr = '';
if ($status === 'running' && $startedAt) {
    $elapsed = time() - strtotime($startedAt);
    $rate    = $done > 0 ? ($elapsed / $done) : 5;
    $eta     = max(0, (int)($remaining * $rate));
    $min     = intdiv($eta, 60);
    $sec     = $eta % 60;
    $etaStr  = $min > 0 ? "{$min}min {$sec}s" : "{$sec}s";
}

$delaySecs = (int)($log['delay_seconds'] ?? 12);
$delayLabels = [
    8  => '🟡 ' . __('blast_delay_moderate')  . ' (8–13s)',
    12 => '🟢 ' . __('blast_delay_safe')      . ' (12–17s)',
    30 => '🛡️ ' . __('blast_delay_very_safe') . ' (30–35s)',
    60 => '🔒 ' . __('blast_delay_ultra_safe'). ' (60–65s)',
];
$delayLabel = $delayLabels[$delaySecs] ?? "{$delaySecs}s";
?>

<div class="max-w-2xl mx-auto space-y-5">

    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="<?= BASE_URI ?>/blast"
           class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white"><?= __('blast_title') ?> #<?= $blastId ?></h2>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                <?= __('blast_created') ?> <?= date('d M Y, H:i', strtotime($log['created_at'])) ?>
            </p>
        </div>
    </div>

    <!-- Status Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-5">

        <!-- Status badge -->
        <div class="flex items-center justify-between">
            <div id="status-badge">
                <?php
                $badges = [
                    'queued'    => [__('blast_status_queued'),   'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'],
                    'scheduled' => [__('blast_status_scheduled'),'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
                    'running'   => [__('blast_status_running'),  'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
                    'done'      => [__('blast_status_done'),     'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
                    'failed'    => [__('blast_status_failed'),   'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
                    'stopped'   => ['⏹ Dihentikan',             'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
                ];
                [$label, $cls] = $badges[$status] ?? [$status, 'bg-gray-100 text-gray-700'];
                ?>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold <?= $cls ?>">
                    <?php if ($status === 'running'): ?>
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-ping inline-block"></span>
                    <?php endif; ?>
                    <?= htmlspecialchars($label) ?>
                </span>
            </div>

            <?php if ($schedAt && $status === 'scheduled'): ?>
            <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">
                🕐 <?= date('d M Y, H:i', strtotime($schedAt)) ?>
            </span>
            <?php endif; ?>
        </div>

        <!-- Progress bar -->
        <div>
            <div class="flex justify-between items-center text-sm mb-2">
                <span id="progress-text" class="text-gray-600 dark:text-gray-400">
                    <?= $done ?> / <?= $total ?> <?= __('blast_delivered') ?>
                </span>
                <span id="pct-text" class="font-bold text-gray-900 dark:text-white">
                    <?= $pct ?>%
                </span>
            </div>
            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-5 overflow-hidden">
                <div id="progress-bar"
                     class="h-5 rounded-full transition-all duration-500 <?= $status === 'done' ? 'bg-green-600' : ($status === 'failed' ? 'bg-red-500' : 'bg-green-500') ?>"
                     style="width:<?= $pct ?>%"></div>
            </div>
        </div>

        <!-- Stats grid -->
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 text-center">
                <p id="sent-count" class="text-2xl font-bold text-green-600 dark:text-green-400"><?= $sent ?></p>
                <p class="text-xs text-green-700 dark:text-green-500 mt-0.5 font-medium"><?= __('blast_success') ?></p>
            </div>
            <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4 text-center">
                <p id="failed-count" class="text-2xl font-bold text-red-500 dark:text-red-400"><?= $failed ?></p>
                <p class="text-xs text-red-600 dark:text-red-500 mt-0.5 font-medium"><?= __('blast_failed') ?></p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-4 text-center">
                <p id="remaining-count" class="text-2xl font-bold text-gray-700 dark:text-gray-300"><?= $remaining ?></p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-medium"><?= __('blast_remaining') ?></p>
            </div>
        </div>

        <!-- ETA / timing info -->
        <div id="timing-row" class="text-center text-sm text-gray-500 dark:text-gray-400 space-y-1">
            <?php if ($status === 'running' && $etaStr): ?>
            <p id="eta-text"><?= __('blast_eta') ?> <strong><?= $etaStr ?></strong></p>
            <?php elseif ($status === 'queued'): ?>
            <p><?= __('blast_waiting_cron') ?></p>
            <?php elseif ($status === 'done' && $startedAt && $finAt): ?>
            <?php $dur = strtotime($finAt) - strtotime($startedAt); $durMin = intdiv($dur, 60); $durSec = $dur % 60; ?>
            <p><?= __('blast_completed_in') ?> <?= $durMin > 0 ? "{$durMin}min {$durSec}s" : "{$durSec}s" ?></p>
            <?php endif; ?>
        </div>

        <!-- Stop button (only when running or queued) -->
        <?php if (in_array($status, ['running', 'queued', 'scheduled'])): ?>
        <div id="stop-wrap" class="pt-1">
            <button type="button" id="stop-btn" onclick="stopBlast()"
                    class="w-full py-2.5 text-sm font-semibold rounded-xl bg-red-600 hover:bg-red-700 text-white transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h6v4H9z"/>
                </svg>
                Hentikan Blast
            </button>
        </div>
        <?php endif; ?>

        <!-- Done: action buttons -->
        <div id="done-actions" class="<?= in_array($status, ['done','failed','stopped']) ? '' : 'hidden' ?> flex gap-3 pt-1">
            <a href="<?= BASE_URI ?>/blast"
               class="flex-1 text-center py-2.5 text-sm font-semibold rounded-xl bg-brand-700 hover:bg-brand-800 text-white transition-colors"
               style="background-color:#163020">
                <?= __('blast_new_btn') ?>
            </a>
            <button type="button"
                    onclick="document.getElementById('blast-detail-modal').classList.remove('hidden'); loadDetail(<?= $blastId ?>)"
                    class="flex-1 py-2.5 text-sm font-semibold rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <?= __('blast_view_recipients') ?>
            </button>
        </div>
    </div>

    <!-- Message preview -->
    <?php
    // Support both plain string and JSON array of variants
    $msgDecoded  = json_decode($msg, true);
    $msgVariants = (json_last_error() === JSON_ERROR_NONE && is_array($msgDecoded)) ? $msgDecoded : [$msg];
    $msgVariants = array_values(array_filter($msgVariants));
    ?>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-5 space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300"><?= __('blast_message_preview') ?></h3>
            <?php if (count($msgVariants) > 1): ?>
            <span class="text-xs bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 px-2 py-0.5 rounded-full font-medium">
                🎲 <?= count($msgVariants) ?> variasi
            </span>
            <?php endif; ?>
        </div>

        <?php foreach ($msgVariants as $vi => $variant): ?>
        <div class="<?= count($msgVariants) > 1 ? 'bg-gray-50 dark:bg-gray-700/40 rounded-xl p-3' : '' ?>">
            <?php if (count($msgVariants) > 1): ?>
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Variasi <?= $vi + 1 ?></p>
            <?php endif; ?>
            <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap leading-relaxed"><?= htmlspecialchars($variant, ENT_QUOTES) ?></p>
        </div>
        <?php endforeach; ?>

        <p class="text-xs text-gray-400 dark:text-gray-500 pt-1 border-t border-gray-100 dark:border-gray-700">
            <?= __('blast_delay_info') ?> <strong><?= $delayLabel ?></strong>
            &nbsp;·&nbsp; <?= $total ?> <?= __('blast_recipients_count') ?>
        </p>
    </div>

</div>

<!-- Recipients Modal -->
<div id="blast-detail-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-lg max-h-[80vh] flex flex-col">
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 dark:border-gray-700 shrink-0">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white"><?= __('blast_detail_title') ?></h3>
            <button type="button" onclick="document.getElementById('blast-detail-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="blast-detail-body" class="overflow-y-auto px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
            <p class="text-center py-8"><?= __('blast_detail_loading') ?></p>
        </div>
    </div>
</div>

<script>
var LANG = {
    etaPrefix:    <?= json_encode(__('blast_eta')) ?>,
    waitingCron:  <?= json_encode(__('blast_waiting_cron')) ?>,
    completedIn:  <?= json_encode(__('blast_completed_in')) ?>,
    delivered:    <?= json_encode(__('blast_delivered')) ?>,
    detailLoading: <?= json_encode(__('blast_detail_loading')) ?>,
    detailNoData:  <?= json_encode(__('blast_detail_no_data')) ?>,
    detailLoadFail: <?= json_encode(__('blast_detail_load_fail')) ?>,
    statusLabels: {
        queued:    <?= json_encode(__('blast_status_queued')) ?>,
        scheduled: <?= json_encode(__('blast_status_scheduled')) ?>,
        running:   <?= json_encode(__('blast_status_running')) ?>,
        done:      <?= json_encode(__('blast_status_done')) ?>,
        failed:    <?= json_encode(__('blast_status_failed')) ?>,
        stopped:   '⏹ Dihentikan',
    }
};

(function () {
    var blastId    = <?= $blastId ?>;
    var statusNow  = <?= json_encode($status) ?>;
    var csrfToken  = document.querySelector('meta[name="csrf-token"]')?.content || '';

    var statusCls = {
        queued:    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
        scheduled: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        running:   'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
        done:      'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
        failed:    'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        stopped:   'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
    };

    function poll() {
        fetch('<?= BASE_URI ?>/blast/' + blastId + '/status', {
            headers: { 'X-CSRF-Token': csrfToken }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            updateUI(data);
            statusNow = data.status;
            if (data.status !== 'done' && data.status !== 'failed') {
                setTimeout(poll, 3000);
            }
        })
        .catch(function () { setTimeout(poll, 5000); });
    }

    function updateUI(data) {
        var total     = parseInt(data.total_recipients) || 0;
        var sent      = parseInt(data.sent_count)  || 0;
        var failed    = parseInt(data.failed_count)|| 0;
        var done      = sent + failed;
        var remaining = total - done;
        var pct       = total > 0 ? Math.round(done / total * 100) : 0;

        document.getElementById('sent-count').textContent      = sent;
        document.getElementById('failed-count').textContent    = failed;
        document.getElementById('remaining-count').textContent = remaining;

        var bar = document.getElementById('progress-bar');
        bar.style.width = pct + '%';
        if (data.status === 'done')   { bar.classList.remove('bg-green-500'); bar.classList.add('bg-green-600'); }
        if (data.status === 'failed') { bar.classList.remove('bg-green-500'); bar.classList.add('bg-red-500'); }

        document.getElementById('progress-text').textContent = done + ' / ' + total + ' ' + LANG.delivered;
        document.getElementById('pct-text').textContent      = pct + '%';

        var label = LANG.statusLabels[data.status] || data.status;
        var cls   = statusCls[data.status] || 'bg-gray-100 text-gray-700';
        var pulse = data.status === 'running'
            ? '<span class="w-2 h-2 rounded-full bg-green-500 animate-ping inline-block"></span>' : '';
        document.getElementById('status-badge').innerHTML =
            '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold ' + cls + '">'
            + pulse + label + '</span>';

        var timingRow = document.getElementById('timing-row');
        var etaEl = document.getElementById('eta-text');
        if (data.status === 'running' && data.eta_seconds > 0) {
            var min = Math.floor(data.eta_seconds / 60);
            var sec = data.eta_seconds % 60;
            var etaStr = min > 0 ? min + 'min ' + sec + 's' : sec + 's';
            if (!etaEl) {
                timingRow.innerHTML = '<p id="eta-text">' + LANG.etaPrefix + ' <strong>' + etaStr + '</strong></p>';
            } else {
                etaEl.innerHTML = LANG.etaPrefix + ' <strong>' + etaStr + '</strong>';
            }
        } else if (data.status === 'queued') {
            timingRow.innerHTML = '<p>' + LANG.waitingCron + '</p>';
        }

        if (data.status === 'done' || data.status === 'failed' || data.status === 'stopped') {
            document.getElementById('done-actions').classList.remove('hidden');
            var sw = document.getElementById('stop-wrap');
            if (sw) sw.classList.add('hidden');
        }
    }

    if (statusNow !== 'done' && statusNow !== 'failed' && statusNow !== 'stopped') {
        setTimeout(poll, 2000);
    }

    // ---- Stop blast ----
    window.stopBlast = function () {
        if (!confirm('Pasti nak hentikan blast ini? Mesej yang belum dihantar akan dibatal.')) return;

        var btn = document.getElementById('stop-btn');
        if (btn) { btn.disabled = true; btn.textContent = 'Menghentikan...'; }

        fetch('<?= BASE_URI ?>/blast/' + blastId + '/stop', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': csrfToken,
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'csrf_token=' + encodeURIComponent(csrfToken),
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.ok) {
                // Hide stop button, show done actions
                var wrap = document.getElementById('stop-wrap');
                if (wrap) wrap.classList.add('hidden');
                document.getElementById('done-actions').classList.remove('hidden');

                // Update status badge
                var cls = 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300';
                document.getElementById('status-badge').innerHTML =
                    '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold ' + cls + '">⏹ Dihentikan</span>';
            } else {
                alert(data.message || 'Gagal hentikan blast.');
                if (btn) { btn.disabled = false; btn.textContent = 'Hentikan Blast'; }
            }
        })
        .catch(function() {
            alert('Gagal sambung ke server.');
            if (btn) { btn.disabled = false; btn.textContent = 'Hentikan Blast'; }
        });
    };

    window.loadDetail = function (id) {
        document.getElementById('blast-detail-body').innerHTML = '<p class="text-center py-8">' + LANG.detailLoading + '</p>';
        fetch('<?= BASE_URI ?>/blast/' + id + '/recipients', {
            headers: { 'X-CSRF-Token': csrfToken }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (!data.recipients || data.recipients.length === 0) {
                document.getElementById('blast-detail-body').innerHTML = '<p class="text-center py-4 text-gray-400">' + LANG.detailNoData + '</p>';
                return;
            }
            var rows = data.recipients.map(function (r) {
                var sc = r.status === 'sent' ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400';
                return '<div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">'
                    + '<div><p class="font-medium text-gray-900 dark:text-white text-xs">' + (r.name || '—') + '</p>'
                    + '<p class="text-xs text-gray-400">' + r.phone + '</p>'
                    + (r.error_msg ? '<p class="text-xs text-red-400 mt-0.5">' + r.error_msg + '</p>' : '')
                    + '</div>'
                    + '<span class="text-sm font-bold ' + sc + '">' + (r.status === 'sent' ? '✓' : '✗') + '</span>'
                    + '</div>';
            });
            document.getElementById('blast-detail-body').innerHTML = rows.join('');
        })
        .catch(function () {
            document.getElementById('blast-detail-body').innerHTML = '<p class="text-center py-4 text-red-400">' + LANG.detailLoadFail + '</p>';
        });
    };
})();
</script>
