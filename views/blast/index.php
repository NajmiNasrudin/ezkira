<?php
/**
 * @var array  $allUsers
 * @var array  $history
 * @var bool   $configured
 */
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <svg class="w-7 h-7 text-green-500" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            <?= __('blast_title') ?>
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5"><?= __('blast_subtitle') ?></p>
    </div>
</div>

<?php if (!$configured): ?>
<!-- API Not Configured Warning -->
<div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-2xl p-5 mb-6">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <p class="font-semibold text-amber-800 dark:text-amber-300"><?= __('blast_api_not_configured') ?></p>
            <p class="text-sm text-amber-700 dark:text-amber-400 mt-1"><?= __('blast_api_config_hint') ?></p>
            <pre class="mt-2 text-xs bg-amber-100 dark:bg-amber-900/40 rounded-lg p-3 text-amber-900 dark:text-amber-200 overflow-x-auto">define('FONNTE_TOKEN', 'your_token_here');</pre>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Left: Compose Blast -->
    <div class="lg:col-span-2 space-y-5">

        <!-- Stats bar -->
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center shadow-sm">
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= count($allUsers) ?></p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?= __('blast_users_with_phone') ?></p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center shadow-sm">
                <p class="text-2xl font-bold text-green-600 dark:text-green-400"><?= array_sum(array_column($history, 'sent_count')) ?></p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?= __('blast_total_success') ?></p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center shadow-sm">
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= count($history) ?></p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?= __('blast_total_sent') ?></p>
            </div>
        </div>

        <!-- Compose Form -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm"><?= __('blast_new') ?></h3>
            </div>
            <form method="POST" action="<?= BASE_URI ?>/blast/send" id="blast-form" enctype="multipart/form-data" class="px-6 py-5 space-y-5">
                <?= \App\Core\CSRF::field() ?>
                <input type="hidden" name="provider" value="fonnte">

                <!-- Image Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('blast_image_header') ?> <span class="text-xs text-gray-400 font-normal">(<?= __('blast_image_optional') ?>)</span>
                    </label>

                    <div id="image-drop-zone"
                         onclick="document.getElementById('blast_image').click()"
                         ondragover="event.preventDefault(); this.classList.add('border-green-500','bg-green-50','dark:bg-green-900/10')"
                         ondragleave="this.classList.remove('border-green-500','bg-green-50','dark:bg-green-900/10')"
                         ondrop="handleImageDrop(event)"
                         class="relative flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-5 cursor-pointer hover:border-green-400 hover:bg-green-50 dark:hover:bg-green-900/10 transition-colors">

                        <img id="image-preview" src="" alt="Preview"
                             class="hidden max-h-40 rounded-lg object-contain shadow-sm">

                        <div id="image-placeholder" class="flex flex-col items-center gap-1 text-gray-400 dark:text-gray-500">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm font-medium"><?= __('blast_image_placeholder') ?></p>
                            <p class="text-xs"><?= __('blast_image_types') ?></p>
                        </div>

                        <button type="button" id="image-remove-btn"
                                onclick="removeImage(event)"
                                class="hidden absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow text-xs transition-colors">
                            ✕
                        </button>
                    </div>

                    <input type="file" id="blast_image" name="blast_image"
                           accept="image/jpeg,image/png,image/webp"
                           class="sr-only"
                           onchange="handleImageSelect(this)">

                    <p id="image-filename" class="text-xs text-gray-400 dark:text-gray-500 mt-1 hidden"></p>
                </div>

                <!-- Custom Message -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('blast_message_label') ?> <span class="text-red-500">*</span>
                    </label>
                    <textarea name="custom_message" required rows="4"
                              placeholder="<?= htmlspecialchars(__('blast_message_placeholder'), ENT_QUOTES) ?>"
                              class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none resize-none"></textarea>
                </div>

                <!-- Link -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('blast_link_label') ?> <span class="text-xs text-gray-400 font-normal">(<?= __('optional') ?>)</span>
                    </label>
                    <input type="text" name="blast_link"
                           placeholder="https://ezkira.com/promo"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1"><?= __('blast_link_hint') ?></p>
                </div>

                <!-- Recipients -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            <?= __('blast_recipients_label') ?> <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="selectAllRecipients()"
                                    class="text-xs text-green-600 hover:text-green-800 font-medium"><?= __('blast_select_all') ?></button>
                            <span class="text-gray-300 dark:text-gray-600">|</span>
                            <button type="button" onclick="clearAllRecipients()"
                                    class="text-xs text-gray-500 hover:text-gray-700"><?= __('blast_clear_all') ?></button>
                        </div>
                    </div>

                    <?php if (empty($allUsers)): ?>
                    <div class="text-center py-8 text-gray-400 dark:text-gray-500 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl">
                        <p class="text-sm"><?= __('blast_no_recipients') ?></p>
                    </div>
                    <?php else: ?>
                    <input type="text" id="recipient-search" placeholder="<?= htmlspecialchars(__('blast_search_placeholder'), ENT_QUOTES) ?>"
                           oninput="filterRecipients(this.value)"
                           class="w-full mb-2 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 outline-none">

                    <div id="recipient-list" class="max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-xl divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($allUsers as $u): ?>
                        <label class="recipient-row flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 cursor-pointer transition-colors"
                               data-name="<?= strtolower(htmlspecialchars($u['name'], ENT_QUOTES)) ?>"
                               data-phone="<?= htmlspecialchars($u['whatsapp_number'], ENT_QUOTES) ?>">
                            <input type="checkbox" name="recipients[]" value="<?= $u['id'] ?>"
                                   class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate"><?= htmlspecialchars($u['name'], ENT_QUOTES) ?></p>
                                <p class="text-xs text-gray-400 dark:text-gray-500"><?= htmlspecialchars($u['whatsapp_number'], ENT_QUOTES) ?></p>
                            </div>
                            <span class="text-xs text-gray-400 dark:text-gray-500"><?= htmlspecialchars($u['role'], ENT_QUOTES) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        <span id="selected-count">0</span> <?= str_replace(':total', count($allUsers), __('blast_selected_count')) ?>
                    </p>
                    <?php endif; ?>
                </div>

                <!-- Schedule toggle -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <?= __('blast_send_time') ?>
                    </label>
                    <div class="flex rounded-xl border border-gray-300 dark:border-gray-600 overflow-hidden text-sm">
                        <button type="button" id="btn-now"
                                onclick="setScheduleMode('now')"
                                class="flex-1 py-2 font-semibold transition-colors bg-green-600 text-white">
                            <?= __('blast_send_now') ?>
                        </button>
                        <button type="button" id="btn-later"
                                onclick="setScheduleMode('later')"
                                class="flex-1 py-2 font-semibold transition-colors bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <?= __('blast_schedule_btn') ?>
                        </button>
                    </div>

                    <div id="schedule-picker" class="hidden mt-3">
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">
                            <?= __('blast_schedule_time_label') ?>
                        </label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                               min="<?= date('Y-m-d\TH:i') ?>"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            <?= __('blast_schedule_cron_hint') ?>
                        </p>
                    </div>
                </div>

                <!-- Delay selector -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <?= __('blast_delay_label') ?>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2" id="delay-options">

                        <!-- Sangat Selamat (default) -->
                        <label class="delay-card flex flex-col items-center gap-1 border-2 rounded-xl p-3 cursor-pointer transition-all border-green-500 bg-green-50 dark:bg-green-900/20 text-center"
                               id="delay-card-12">
                            <input type="radio" name="delay_seconds" value="12" checked class="sr-only" onchange="selectDelay(12)">
                            <span class="text-lg">🛡️</span>
                            <span class="text-xs font-semibold text-gray-800 dark:text-white"><?= __('blast_delay_very_safe') ?></span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">12–17s</span>
                            <span class="text-xs bg-green-500 text-white px-1.5 py-0.5 rounded-md font-medium"><?= __('blast_delay_default_badge') ?></span>
                        </label>

                        <!-- Selamat -->
                        <label class="delay-card flex flex-col items-center gap-1 border-2 rounded-xl p-3 cursor-pointer transition-all border-gray-200 dark:border-gray-600 text-center"
                               id="delay-card-8">
                            <input type="radio" name="delay_seconds" value="8" class="sr-only" onchange="selectDelay(8)">
                            <span class="text-lg">🟢</span>
                            <span class="text-xs font-semibold text-gray-800 dark:text-white"><?= __('blast_delay_safe') ?></span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">8–13s</span>
                        </label>

                        <!-- Sederhana -->
                        <label class="delay-card flex flex-col items-center gap-1 border-2 rounded-xl p-3 cursor-pointer transition-all border-gray-200 dark:border-gray-600 text-center"
                               id="delay-card-5">
                            <input type="radio" name="delay_seconds" value="5" class="sr-only" onchange="selectDelay(5)">
                            <span class="text-lg">🟡</span>
                            <span class="text-xs font-semibold text-gray-800 dark:text-white"><?= __('blast_delay_moderate') ?></span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">5–10s</span>
                        </label>

                        <!-- Pantas -->
                        <label class="delay-card flex flex-col items-center gap-1 border-2 rounded-xl p-3 cursor-pointer transition-all border-gray-200 dark:border-gray-600 text-center"
                               id="delay-card-3">
                            <input type="radio" name="delay_seconds" value="3" class="sr-only" onchange="selectDelay(3)">
                            <span class="text-lg">⚡</span>
                            <span class="text-xs font-semibold text-gray-800 dark:text-white"><?= __('blast_delay_fast') ?></span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">3–8s</span>
                            <span class="text-xs bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 px-1.5 py-0.5 rounded-md font-medium"><?= __('blast_delay_ban_risk') ?></span>
                        </label>

                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">
                        <?= str_replace(':count', count($allUsers), __('blast_delay_eta_hint')) ?><span id="delay-eta-val">~</span>
                    </p>
                </div>

                <!-- Info note -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-3 text-xs text-blue-700 dark:text-blue-400">
                    <strong>ℹ️</strong> <?= __('blast_info_note') ?>
                </div>

                <button type="submit" id="blast-btn"
                        <?= !$configured ? 'disabled' : '' ?>
                        class="w-full py-2.5 text-sm font-semibold text-white rounded-xl bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    <span id="blast-btn-text"><?= __('blast_submit') ?></span>
                </button>
            </form>
        </div>
    </div>

    <!-- Right: Blast History -->
    <div class="space-y-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm"><?= __('blast_history') ?></h3>
            </div>
            <?php if (empty($history)): ?>
            <div class="px-5 py-8 text-center text-gray-400 dark:text-gray-500 text-sm">
                <?= __('blast_no_history') ?>
            </div>
            <?php else: ?>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($history as $log):
                    $logStatus = $log['status'] ?? 'done';
                    $statusKey = match($logStatus) {
                        'queued'    => 'blast_status_queued',
                        'scheduled' => 'blast_status_scheduled',
                        'running'   => 'blast_status_running',
                        'failed'    => 'blast_status_failed',
                        default     => null,
                    };
                    $statusBadge = match($logStatus) {
                        'queued'    => '<span class="text-xs px-1.5 py-0.5 rounded-md bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300 font-medium">' . __('blast_status_queued') . '</span>',
                        'scheduled' => '<span class="text-xs px-1.5 py-0.5 rounded-md bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 font-medium">' . __('blast_status_scheduled') . '</span>',
                        'running'   => '<span class="text-xs px-1.5 py-0.5 rounded-md bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 font-medium animate-pulse">' . __('blast_status_running') . '</span>',
                        'failed'    => '<span class="text-xs px-1.5 py-0.5 rounded-md bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 font-medium">' . __('blast_status_failed') . '</span>',
                        default     => '',
                    };
                    $clickable = in_array($logStatus, ['done','failed','running','queued','scheduled']);
                    $href = BASE_URI . '/blast/' . $log['id'] . '/progress';
                ?>
                <div class="px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors cursor-pointer"
                     onclick="window.location='<?= $href ?>'">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2">
                            <?= $statusBadge ?>
                            <span class="text-xs font-medium text-gray-900 dark:text-white">#<?= $log['id'] ?></span>
                        </div>
                        <span class="text-xs text-gray-400 dark:text-gray-500">
                            <?php if ($logStatus === 'scheduled' && $log['scheduled_at']): ?>
                                🕐 <?= date('d M, H:i', strtotime($log['scheduled_at'])) ?>
                            <?php else: ?>
                                <?= date('d M, H:i', strtotime($log['created_at'])) ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="flex items-center gap-3 text-xs">
                        <span class="text-green-600 dark:text-green-400 font-medium">✓ <?= $log['sent_count'] ?> <?= __('blast_success') ?></span>
                        <?php if ($log['failed_count'] > 0): ?>
                        <span class="text-red-500 dark:text-red-400">✗ <?= $log['failed_count'] ?> <?= __('blast_failed') ?></span>
                        <?php endif; ?>
                        <span class="text-gray-400 dark:text-gray-500 ml-auto">/ <?= $log['total_recipients'] ?> <?= __('total') ?></span>
                    </div>
                    <div class="mt-2 w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                        <?php $pct = $log['total_recipients'] > 0 ? ($log['sent_count'] / $log['total_recipients'] * 100) : 0; ?>
                        <div class="<?= $logStatus === 'running' ? 'bg-green-400' : 'bg-green-500' ?> h-1.5 rounded-full transition-all" style="width:<?= number_format($pct, 1) ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Blast Detail Modal -->
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
// PHP lang strings passed to JS
var LANG = {
    confirmNow:      <?= json_encode(__('blast_confirm_now')) ?>,
    confirmSchedule: <?= json_encode(__('blast_confirm_schedule')) ?>,
    submitNow:       <?= json_encode(__('blast_submit')) ?>,
    submitSchedule:  <?= json_encode(__('blast_submit_schedule')) ?>,
    detailLoading:   <?= json_encode(__('blast_detail_loading')) ?>,
    detailNoData:    <?= json_encode(__('blast_detail_no_data')) ?>,
    detailLoadFail:  <?= json_encode(__('blast_detail_load_fail')) ?>,
};

// ---------------------------------------------------------------
// Image upload helpers
// ---------------------------------------------------------------
var MAX_IMAGE_BYTES = 2 * 1024 * 1024;
var ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

function handleImageSelect(input) {
    if (input.files && input.files[0]) applyImageFile(input.files[0]);
}

function handleImageDrop(e) {
    e.preventDefault();
    var dz = document.getElementById('image-drop-zone');
    dz.classList.remove('border-green-500','bg-green-50','dark:bg-green-900/10');
    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
        var file = e.dataTransfer.files[0];
        var dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('blast_image').files = dt.files;
        applyImageFile(file);
    }
}

function applyImageFile(file) {
    if (!ALLOWED_IMAGE_TYPES.includes(file.type)) { alert('Format tidak disokong. Sila gunakan JPG, PNG, atau WebP.'); return; }
    if (file.size > MAX_IMAGE_BYTES) { alert('Saiz gambar melebihi 2MB.'); return; }
    var reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('image-preview').src = e.target.result;
        document.getElementById('image-preview').classList.remove('hidden');
        document.getElementById('image-placeholder').classList.add('hidden');
        document.getElementById('image-remove-btn').classList.remove('hidden');
        var kb = (file.size / 1024).toFixed(0);
        var fn = document.getElementById('image-filename');
        fn.textContent = file.name + ' (' + kb + ' KB)';
        fn.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}

function removeImage(e) {
    e.stopPropagation();
    document.getElementById('blast_image').value = '';
    document.getElementById('image-preview').classList.add('hidden');
    document.getElementById('image-preview').src = '';
    document.getElementById('image-placeholder').classList.remove('hidden');
    document.getElementById('image-remove-btn').classList.add('hidden');
    var fn = document.getElementById('image-filename');
    fn.classList.add('hidden'); fn.textContent = '';
}

// ---------------------------------------------------------------
// Track selected count
// ---------------------------------------------------------------
document.querySelectorAll('input[name="recipients[]"]').forEach(function(cb) {
    cb.addEventListener('change', updateCount);
});

function updateCount() {
    var count = document.querySelectorAll('input[name="recipients[]"]:checked').length;
    var el = document.getElementById('selected-count');
    if (el) el.textContent = count;
    updateDelayEta();
}

function selectAllRecipients() {
    document.querySelectorAll('.recipient-row input[type="checkbox"]').forEach(function(cb) {
        if (!cb.closest('.recipient-row').classList.contains('hidden')) cb.checked = true;
    });
    updateCount();
}

function clearAllRecipients() {
    document.querySelectorAll('input[name="recipients[]"]').forEach(function(cb) { cb.checked = false; });
    updateCount();
}

function filterRecipients(q) {
    q = q.toLowerCase();
    document.querySelectorAll('.recipient-row').forEach(function(row) {
        var name  = row.getAttribute('data-name') || '';
        var phone = row.getAttribute('data-phone') || '';
        row.classList.toggle('hidden', q !== '' && !name.includes(q) && !phone.includes(q));
    });
}

// ---------------------------------------------------------------
// Delay selector
// ---------------------------------------------------------------
var currentDelay = 12;
var totalRecipients = <?= count($allUsers) ?>;

function selectDelay(val) {
    currentDelay = val;
    [12, 8, 5, 3].forEach(function(d) {
        var card = document.getElementById('delay-card-' + d);
        if (!card) return;
        if (d === val) {
            card.classList.remove('border-gray-200', 'dark:border-gray-600');
            card.classList.add('border-green-500', 'bg-green-50', 'dark:bg-green-900/20');
        } else {
            card.classList.remove('border-green-500', 'bg-green-50', 'dark:bg-green-900/20');
            card.classList.add('border-gray-200', 'dark:border-gray-600');
        }
    });
    updateDelayEta();
}

function updateDelayEta() {
    var count = document.querySelectorAll('input[name="recipients[]"]:checked').length || totalRecipients;
    var avgDelay = currentDelay + 2.5;
    var totalSecs = count * avgDelay;
    var h = Math.floor(totalSecs / 3600);
    var m = Math.floor((totalSecs % 3600) / 60);
    var s = Math.floor(totalSecs % 60);
    var str = h > 0 ? h + 'j ' + m + 'min' : m > 0 ? m + 'min ' + s + 's' : s + 's';
    var el = document.getElementById('delay-eta-val');
    if (el) el.textContent = str;
}

// ---------------------------------------------------------------
// Schedule mode toggle
// ---------------------------------------------------------------
var scheduleMode = 'now';

function setScheduleMode(mode) {
    scheduleMode = mode;
    var picker   = document.getElementById('schedule-picker');
    var btnNow   = document.getElementById('btn-now');
    var btnLater = document.getElementById('btn-later');
    var btnText  = document.getElementById('blast-btn-text');
    var dtInput  = document.getElementById('scheduled_at');

    if (mode === 'later') {
        picker.classList.remove('hidden');
        btnNow.classList.remove('bg-green-600','text-white');
        btnNow.classList.add('bg-white','dark:bg-gray-700','text-gray-600','dark:text-gray-300');
        btnLater.classList.remove('bg-white','dark:bg-gray-700','text-gray-600','dark:text-gray-300');
        btnLater.classList.add('bg-blue-600','text-white');
        btnText.textContent = LANG.submitSchedule;
        dtInput.required = true;
    } else {
        picker.classList.add('hidden');
        btnLater.classList.remove('bg-blue-600','text-white');
        btnLater.classList.add('bg-white','dark:bg-gray-700','text-gray-600','dark:text-gray-300');
        btnNow.classList.remove('bg-white','dark:bg-gray-700','text-gray-600','dark:text-gray-300');
        btnNow.classList.add('bg-green-600','text-white');
        btnText.textContent = LANG.submitNow;
        dtInput.required = false;
        dtInput.value = '';
    }
}

// Confirm before sending
document.getElementById('blast-form').addEventListener('submit', function(e) {
    var count = document.querySelectorAll('input[name="recipients[]"]:checked').length;
    if (count === 0) { e.preventDefault(); alert('<?= __('blast_recipients_label') ?>'); return; }

    if (scheduleMode === 'later') {
        var dt = document.getElementById('scheduled_at').value;
        if (!dt) { e.preventDefault(); return; }
        if (new Date(dt) <= new Date()) { e.preventDefault(); return; }
        var dtFormatted = new Date(dt).toLocaleString('<?= App\Core\Lang::get() === 'ms' ? 'ms-MY' : 'en-MY' ?>', {dateStyle:'medium', timeStyle:'short'});
        var msg = LANG.confirmSchedule.replace(':count', count).replace(':time', dtFormatted);
        if (!confirm(msg)) e.preventDefault();
    } else {
        var msg = LANG.confirmNow.replace(':count', count);
        if (!confirm(msg)) e.preventDefault();
    }
});

function viewBlastDetail(id) {
    document.getElementById('blast-detail-modal').classList.remove('hidden');
    document.getElementById('blast-detail-body').innerHTML = '<p class="text-center py-8">' + LANG.detailLoading + '</p>';

    fetch('<?= BASE_URI ?>/blast/' + id + '/recipients', {
        headers: { 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || '' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var rows = data.recipients.map(function(r) {
            var statusCls = r.status === 'sent' ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400';
            var statusIcon = r.status === 'sent' ? '✓' : '✗';
            return '<div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">'
                + '<div><p class="font-medium text-gray-900 dark:text-white text-xs">' + (r.name || '—') + '</p>'
                + '<p class="text-xs text-gray-400">' + r.phone + '</p>'
                + (r.error_msg ? '<p class="text-xs text-red-400 mt-0.5">' + r.error_msg + '</p>' : '')
                + '</div>'
                + '<span class="text-sm font-bold ' + statusCls + '">' + statusIcon + '</span>'
                + '</div>';
        });
        document.getElementById('blast-detail-body').innerHTML = rows.length
            ? rows.join('')
            : '<p class="text-center py-4 text-gray-400">' + LANG.detailNoData + '</p>';
    })
    .catch(function() {
        document.getElementById('blast-detail-body').innerHTML = '<p class="text-center py-4 text-red-400">' + LANG.detailLoadFail + '</p>';
    });
}
</script>
