<?php
/**
 * @var array  $allUsers
 * @var array  $history
 * @var bool   $configured
 */
?>

<?php
/* Clone helpers — safe defaults when no clone */
$cloneData   = $cloneData   ?? null;
$cloneMsgs   = $cloneData   ? array_pad($cloneData['messages'],   3, '') : ['','',''];
$cloneImgs   = $cloneData   ? array_pad($cloneData['images'],     3, '') : ['','',''];
$cloneLink   = $cloneData   ? ($cloneData['blast_link']    ?? '') : '';
$cloneDelay  = $cloneData   ? ($cloneData['delay_seconds'] ?? 30) : 30;
$cloneProvider = $cloneData ? ($cloneData['provider']      ?? 'fonnte') : 'fonnte';
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

<?php if ($cloneData): ?>
<!-- Clone Notice -->
<div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700 rounded-2xl px-5 py-3.5 mb-5 flex items-center gap-3">
    <svg class="w-5 h-5 text-indigo-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
    </svg>
    <p class="text-sm text-indigo-800 dark:text-indigo-300">
        <strong>Mesej diambil dari Blast #<?= $cloneData['blast_id'] ?>.</strong>
        Pilih batch atau penerima baharu di bawah, kemudian blast.
    </p>
    <a href="<?= BASE_URI ?>/blast" class="ml-auto text-xs text-indigo-500 hover:text-indigo-700 shrink-0">✕ Mulakan baru</a>
</div>
<?php endif; ?>

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

                <!-- Provider Selector -->
                <?php
                $fonnteOk   = defined('FONNTE_TOKEN')      && trim(FONNTE_TOKEN)      !== '';
                $wasenderOk = defined('WASENDER_API_KEY')  && trim(WASENDER_API_KEY)  !== '';
                ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Provider</label>
                    <div class="grid grid-cols-2 gap-3">

                        <?php
                        // Default provider: honour clone, else configured one
                        $defaultProvider = $cloneProvider;
                        if ($defaultProvider === 'fonnte'      && !$fonnteOk   && $wasenderOk) $defaultProvider = 'wasenderapi';
                        if ($defaultProvider === 'wasenderapi' && !$wasenderOk && $fonnteOk)   $defaultProvider = 'fonnte';
                        ?>
                        <label class="provider-card flex items-center gap-3 border-2 rounded-xl px-4 py-3 cursor-pointer transition-all
                                      <?= $defaultProvider === 'fonnte' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : ($fonnteOk ? 'border-gray-200 dark:border-gray-600' : 'border-gray-200 dark:border-gray-600 opacity-60') ?>"
                               id="provider-card-fonnte">
                            <input type="radio" name="provider" value="fonnte"
                                   <?= $defaultProvider === 'fonnte' ? 'checked' : '' ?>
                                   class="sr-only" onchange="selectProvider('fonnte')">
                            <span class="text-xl">🟢</span>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">Fonnte</p>
                                <p class="text-xs <?= $fonnteOk ? 'text-green-600 dark:text-green-400' : 'text-gray-400' ?>">
                                    <?= $fonnteOk ? '✓ Token OK' : '✗ Belum set' ?>
                                </p>
                            </div>
                        </label>

                        <label class="provider-card flex items-center gap-3 border-2 rounded-xl px-4 py-3 cursor-pointer transition-all
                                      <?= $defaultProvider === 'wasenderapi' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : ($wasenderOk ? 'border-gray-200 dark:border-gray-600' : 'border-gray-200 dark:border-gray-600 opacity-60') ?>"
                               id="provider-card-wasenderapi">
                            <input type="radio" name="provider" value="wasenderapi"
                                   <?= $defaultProvider === 'wasenderapi' ? 'checked' : '' ?>
                                   class="sr-only" onchange="selectProvider('wasenderapi')">
                            <span class="text-xl">📡</span>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">WASenderAPI</p>
                                <p class="text-xs <?= $wasenderOk ? 'text-green-600 dark:text-green-400' : 'text-gray-400' ?>">
                                    <?= $wasenderOk ? '✓ API Key OK' : '✗ Belum set' ?>
                                </p>
                            </div>
                        </label>

                    </div>
                    <?php if (!$fonnteOk && !$wasenderOk): ?>
                    <p class="text-xs text-red-500 dark:text-red-400 mt-1.5">
                        ⚠️ Set FONNTE_TOKEN atau WASENDER_API_KEY dalam config.php dahulu.
                    </p>
                    <?php endif; ?>
                </div>

                <!-- 3 Image Slots -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            <?= __('blast_image_header') ?>
                            <span class="text-xs text-gray-400 font-normal ml-1">(<?= __('blast_image_optional') ?>)</span>
                        </label>
                        <span class="text-xs bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 px-2 py-0.5 rounded-full font-medium">
                            🎲 Rawak per penerima
                        </span>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <?php for ($s = 1; $s <= 3; $s++):
                            $existImg  = $cloneImgs[$s - 1] ?? '';
                            $existBase = $existImg !== '' ? basename($existImg) : '';
                            $existUrl  = $existBase !== '' ? (BASE_URI . '/blast/media/' . $existBase) : '';
                        ?>
                        <div>
                            <div id="img-zone-<?= $s ?>"
                                 onclick="document.getElementById('blast_image_<?= $s ?>').click()"
                                 ondragover="event.preventDefault(); this.classList.add('border-green-500','bg-green-50','dark:bg-green-900/10')"
                                 ondragleave="this.classList.remove('border-green-500','bg-green-50','dark:bg-green-900/10')"
                                 ondrop="handleImgDrop(event, <?= $s ?>)"
                                 class="relative flex flex-col items-center justify-center gap-1 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-3 cursor-pointer hover:border-green-400 hover:bg-green-50 dark:hover:bg-green-900/10 transition-colors h-28 overflow-hidden">

                                <img id="img-preview-<?= $s ?>" src="<?= htmlspecialchars($existUrl, ENT_QUOTES) ?>" alt=""
                                     class="<?= $existUrl !== '' ? '' : 'hidden' ?> absolute inset-0 w-full h-full object-cover rounded-xl">

                                <div id="img-ph-<?= $s ?>" class="<?= $existUrl !== '' ? 'hidden' : '' ?> flex flex-col items-center gap-1 text-gray-400 dark:text-gray-500">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-xs font-medium">Gambar <?= $s ?></p>
                                    <?php if ($s === 1): ?><p class="text-xs opacity-60">Optional</p><?php endif; ?>
                                </div>

                                <button type="button" id="img-rm-<?= $s ?>"
                                        onclick="removeImg(event,<?= $s ?>)"
                                        class="<?= $existUrl !== '' ? '' : 'hidden' ?> absolute top-1.5 right-1.5 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center shadow text-xs transition-colors z-10">
                                    ✕
                                </button>

                                <!-- Slot number badge -->
                                <span class="absolute bottom-1.5 left-1.5 w-5 h-5 rounded-full bg-gray-900/50 text-white text-xs flex items-center justify-center font-bold"><?= $s ?></span>
                            </div>
                            <input type="file" id="blast_image_<?= $s ?>" name="blast_image_<?= $s ?>"
                                   accept="image/jpeg,image/png,image/webp" class="sr-only"
                                   onchange="handleImgSelect(this, <?= $s ?>)">
                            <!-- Hidden: keep existing image when cloning (cleared if user removes or replaces) -->
                            <input type="hidden" id="existing_image_<?= $s ?>" name="existing_image_<?= $s ?>"
                                   value="<?= htmlspecialchars($existBase, ENT_QUOTES) ?>">
                        </div>
                        <?php endfor; ?>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">
                        Isi 2–3 gambar → sistem akan pilih secara rawak untuk setiap penerima.
                    </p>
                </div>

                <!-- 3 Message Variations -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Mesej <span class="text-red-500">*</span>
                        </label>
                        <span class="text-xs bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 px-2 py-0.5 rounded-full font-medium">
                            🎲 Rawak per penerima
                        </span>
                    </div>

                    <!-- Variation tabs -->
                    <div class="flex rounded-t-xl border border-gray-300 dark:border-gray-600 overflow-hidden text-xs font-semibold">
                        <?php for ($v = 1; $v <= 3; $v++): ?>
                        <button type="button" id="msg-tab-<?= $v ?>"
                                onclick="switchMsgTab(<?= $v ?>)"
                                class="flex-1 py-2 flex items-center justify-center gap-1.5 transition-colors
                                       <?= $v === 1 ? 'bg-green-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600' ?>">
                            <span id="msg-tab-dot-<?= $v ?>" class="hidden w-1.5 h-1.5 rounded-full bg-white"></span>
                            Variasi <?= $v ?><?= $v === 1 ? ' *' : '' ?>
                        </button>
                        <?php endfor; ?>
                    </div>

                    <?php for ($v = 1; $v <= 3; $v++): ?>
                    <div id="msg-panel-<?= $v ?>" class="<?= $v !== 1 ? 'hidden' : '' ?>">
                        <textarea name="custom_message_<?= $v ?>"
                                  id="msg-area-<?= $v ?>"
                                  <?= $v === 1 ? 'required' : '' ?>
                                  oninput="onMsgInput(<?= $v ?>)"
                                  rows="4"
                                  placeholder="<?= $v === 1 ? htmlspecialchars(__('blast_message_placeholder'), ENT_QUOTES) : 'Variasi ' . $v . ' (optional) — tulis mesej berbeza untuk lebih natural' ?>"
                                  class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 border-t-0 rounded-b-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 outline-none resize-none"><?= htmlspecialchars($cloneMsgs[$v - 1] ?? '', ENT_QUOTES) ?></textarea>
                    </div>
                    <?php endfor; ?>

                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        <span id="msg-filled-count">0</span>/3 variasi diisi — semakin banyak variasi, semakin natural nampak.
                    </p>
                </div>

                <!-- Link -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <?= __('blast_link_label') ?> <span class="text-xs text-gray-400 font-normal">(<?= __('optional') ?>)</span>
                    </label>
                    <input type="text" name="blast_link"
                           value="<?= htmlspecialchars($cloneLink, ENT_QUOTES) ?>"
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

                    <!-- Search + Business Type Filter -->
                    <div class="flex gap-2 mb-2">
                        <input type="text" id="recipient-search" placeholder="<?= htmlspecialchars(__('blast_search_placeholder'), ENT_QUOTES) ?>"
                               oninput="filterRecipients()"
                               class="flex-1 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 outline-none">

                        <select id="biz-type-filter" onchange="filterRecipients()"
                                class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 outline-none">
                            <option value="">Semua Bisnes</option>
                            <?php foreach ($businessTypes as $key => $label): ?>
                            <option value="<?= $key ?>"><?= htmlspecialchars($label, ENT_QUOTES) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php
                    $batchSize  = 50;
                    $numBatches = (int)ceil(count($allUsers) / $batchSize);

                    // Count users per business type
                    $typeCounts = [];
                    foreach ($allUsers as $u) {
                        $bt = $u['business_type'] ?? '';
                        if ($bt !== '') $typeCounts[$bt] = ($typeCounts[$bt] ?? 0) + 1;
                    }
                    ?>

                    <!-- Batch chips -->
                    <?php if ($numBatches > 1): ?>
                    <div class="rounded-xl border border-indigo-100 dark:border-indigo-900/40 bg-indigo-50/50 dark:bg-indigo-900/10 px-3 py-2.5 mb-2">
                        <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 mb-2 flex items-center gap-1.5">
                            <span>📦</span> Pilih Batch
                        </p>
                        <div class="flex flex-wrap gap-1.5" id="batch-chips">
                            <?php for ($b = 1; $b <= $numBatches; $b++):
                                $bStart = ($b - 1) * $batchSize;
                                $bEnd   = min($b * $batchSize - 1, count($allUsers) - 1);
                                $bCount = $bEnd - $bStart + 1;
                            ?>
                            <button type="button"
                                    id="batch-chip-<?= $b ?>"
                                    onclick="selectBatch(<?= $b ?>, <?= $bStart ?>, <?= $bEnd ?>)"
                                    data-start="<?= $bStart ?>"
                                    data-end="<?= $bEnd ?>"
                                    class="batch-chip text-xs px-2.5 py-1 rounded-full border border-indigo-200 dark:border-indigo-700 text-indigo-600 dark:text-indigo-300 bg-white dark:bg-gray-800 hover:bg-indigo-100 hover:border-indigo-400 dark:hover:bg-indigo-900/30 transition-colors font-medium">
                                Batch <?= $b ?> <span class="font-semibold">(<?= $bCount ?>)</span>
                            </button>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Quick select by business type -->
                    <?php if (!empty($typeCounts)): ?>
                    <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/40 bg-emerald-50/50 dark:bg-emerald-900/10 px-3 py-2.5 mb-2">
                        <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 mb-2 flex items-center gap-1.5">
                            <span>🏢</span> Pilih Jenis Bisnes
                        </p>
                        <div class="flex flex-wrap gap-1.5" id="biz-type-chips">
                            <?php foreach ($typeCounts as $bt => $cnt):
                                $btLabel = $businessTypes[$bt] ?? $bt;
                            ?>
                            <button type="button"
                                    onclick="selectByType('<?= $bt ?>')"
                                    class="biz-chip text-xs px-2.5 py-1 rounded-full border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300 bg-white dark:bg-gray-800 hover:bg-emerald-100 hover:border-emerald-400 dark:hover:bg-emerald-900/30 transition-colors"
                                    data-type="<?= $bt ?>">
                                <?= htmlspecialchars($btLabel, ENT_QUOTES) ?> <span class="font-semibold">(<?= $cnt ?>)</span>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div id="recipient-list" class="max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-xl divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($allUsers as $uidx => $u):
                            $bt      = $u['business_type'] ?? '';
                            $btLabel = $bt !== '' ? ($businessTypes[$bt] ?? $bt) : '';
                        ?>
                        <label class="recipient-row flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 cursor-pointer transition-colors"
                               data-name="<?= strtolower(htmlspecialchars($u['name'], ENT_QUOTES)) ?>"
                               data-phone="<?= htmlspecialchars($u['whatsapp_number'], ENT_QUOTES) ?>"
                               data-btype="<?= htmlspecialchars($bt, ENT_QUOTES) ?>"
                               data-bidx="<?= $uidx ?>">
                            <input type="checkbox" name="recipients[]" value="<?= $u['id'] ?>"
                                   class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate"><?= htmlspecialchars($u['name'], ENT_QUOTES) ?></p>
                                <p class="text-xs text-gray-400 dark:text-gray-500"><?= htmlspecialchars($u['whatsapp_number'], ENT_QUOTES) ?></p>
                            </div>
                            <?php if ($btLabel !== ''): ?>
                            <span class="text-xs bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-300 px-2 py-0.5 rounded-full whitespace-nowrap shrink-0 max-w-[90px] truncate">
                                <?= htmlspecialchars($btLabel, ENT_QUOTES) ?>
                            </span>
                            <?php endif; ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        <span id="selected-count">0</span> <?= str_replace(':total', count($allUsers), __('blast_selected_count')) ?>
                        &nbsp;·&nbsp; <span id="visible-count"><?= count($allUsers) ?></span> ditunjuk
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

                <!-- Large blast warning (hidden by default) -->
                <div id="large-blast-warning" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl p-3">
                    <p class="text-xs text-red-700 dark:text-red-400" id="large-blast-warning-text"></p>
                </div>

                <!-- Delay selector -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <?= __('blast_delay_label') ?>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2" id="delay-options">

                        <?php
                        $activeDelayCls = 'border-green-500 bg-green-50 dark:bg-green-900/20';
                        $inactDelayCls  = 'border-gray-200 dark:border-gray-600';
                        ?>

                        <!-- Ultra Selamat 60s -->
                        <label class="delay-card flex flex-col items-center gap-1 border-2 rounded-xl p-3 cursor-pointer transition-all <?= $cloneDelay === 60 ? $activeDelayCls : $inactDelayCls ?> text-center"
                               id="delay-card-60">
                            <input type="radio" name="delay_seconds" value="60" <?= $cloneDelay === 60 ? 'checked' : '' ?> class="sr-only" onchange="selectDelay(60)">
                            <span class="text-lg">🔒</span>
                            <span class="text-xs font-semibold text-gray-800 dark:text-white"><?= __('blast_delay_ultra_safe') ?></span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">60–65s</span>
                            <span class="text-xs bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 px-1.5 py-0.5 rounded-md font-medium">100+</span>
                        </label>

                        <!-- Sangat Selamat 30s -->
                        <label class="delay-card flex flex-col items-center gap-1 border-2 rounded-xl p-3 cursor-pointer transition-all <?= $cloneDelay === 30 ? $activeDelayCls : $inactDelayCls ?> text-center"
                               id="delay-card-30">
                            <input type="radio" name="delay_seconds" value="30" <?= $cloneDelay === 30 ? 'checked' : '' ?> class="sr-only" onchange="selectDelay(30)">
                            <span class="text-lg">🛡️</span>
                            <span class="text-xs font-semibold text-gray-800 dark:text-white"><?= __('blast_delay_very_safe') ?></span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">30–35s</span>
                            <span class="text-xs bg-green-500 text-white px-1.5 py-0.5 rounded-md font-medium"><?= __('blast_delay_default_badge') ?></span>
                        </label>

                        <!-- Selamat 12s -->
                        <label class="delay-card flex flex-col items-center gap-1 border-2 rounded-xl p-3 cursor-pointer transition-all <?= $cloneDelay === 12 ? $activeDelayCls : $inactDelayCls ?> text-center"
                               id="delay-card-12">
                            <input type="radio" name="delay_seconds" value="12" <?= $cloneDelay === 12 ? 'checked' : '' ?> class="sr-only" onchange="selectDelay(12)">
                            <span class="text-lg">🟢</span>
                            <span class="text-xs font-semibold text-gray-800 dark:text-white"><?= __('blast_delay_safe') ?></span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">12–17s</span>
                        </label>

                        <!-- Sederhana 8s -->
                        <label class="delay-card flex flex-col items-center gap-1 border-2 rounded-xl p-3 cursor-pointer transition-all <?= $cloneDelay === 8 ? $activeDelayCls : $inactDelayCls ?> text-center"
                               id="delay-card-8">
                            <input type="radio" name="delay_seconds" value="8" <?= $cloneDelay === 8 ? 'checked' : '' ?> class="sr-only" onchange="selectDelay(8)">
                            <span class="text-lg">🟡</span>
                            <span class="text-xs font-semibold text-gray-800 dark:text-white"><?= __('blast_delay_moderate') ?></span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">8–13s</span>
                            <span class="text-xs bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400 px-1.5 py-0.5 rounded-md font-medium">&lt;50 sahaja</span>
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
// Provider selector
// ---------------------------------------------------------------
function selectProvider(val) {
    ['fonnte', 'wasenderapi'].forEach(function(p) {
        var card = document.getElementById('provider-card-' + p);
        if (!card) return;
        if (p === val) {
            card.classList.add('border-green-500', 'bg-green-50', 'dark:bg-green-900/20');
            card.classList.remove('border-gray-200', 'dark:border-gray-600');
        } else {
            card.classList.remove('border-green-500', 'bg-green-50', 'dark:bg-green-900/20');
            card.classList.add('border-gray-200', 'dark:border-gray-600');
        }
    });
}

// ---------------------------------------------------------------
// Image slot helpers (3 slots)
// ---------------------------------------------------------------
var MAX_IMAGE_BYTES  = 2 * 1024 * 1024;
var ALLOWED_IMG_MIME = ['image/jpeg', 'image/png', 'image/webp'];

function handleImgSelect(input, slot) {
    if (input.files && input.files[0]) applyImgFile(input.files[0], slot);
}

function handleImgDrop(e, slot) {
    e.preventDefault();
    document.getElementById('img-zone-' + slot).classList.remove('border-green-500','bg-green-50','dark:bg-green-900/10');
    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
        var file = e.dataTransfer.files[0];
        var dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('blast_image_' + slot).files = dt.files;
        applyImgFile(file, slot);
    }
}

function applyImgFile(file, slot) {
    if (!ALLOWED_IMG_MIME.includes(file.type)) { alert('Format tidak disokong. Gunakan JPG, PNG, atau WebP.'); return; }
    if (file.size > MAX_IMAGE_BYTES) { alert('Saiz gambar melebihi 2MB.'); return; }
    // New file replaces any existing cloned image
    var existHidden = document.getElementById('existing_image_' + slot);
    if (existHidden) existHidden.value = '';
    var reader = new FileReader();
    reader.onload = function(ev) {
        var preview = document.getElementById('img-preview-' + slot);
        preview.src = ev.target.result;
        preview.classList.remove('hidden');
        document.getElementById('img-ph-' + slot).classList.add('hidden');
        document.getElementById('img-rm-' + slot).classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}

function removeImg(e, slot) {
    e.stopPropagation();
    document.getElementById('blast_image_' + slot).value = '';
    // Also clear the existing-image hidden input (clone reuse)
    var existHidden = document.getElementById('existing_image_' + slot);
    if (existHidden) existHidden.value = '';
    var preview = document.getElementById('img-preview-' + slot);
    preview.src = '';
    preview.classList.add('hidden');
    document.getElementById('img-ph-' + slot).classList.remove('hidden');
    document.getElementById('img-rm-' + slot).classList.add('hidden');
}

// ---------------------------------------------------------------
// Message variation tabs
// ---------------------------------------------------------------
var activeMsgTab = 1;

function switchMsgTab(tab) {
    activeMsgTab = tab;
    [1, 2, 3].forEach(function(t) {
        var btn   = document.getElementById('msg-tab-' + t);
        var panel = document.getElementById('msg-panel-' + t);
        var dot   = document.getElementById('msg-tab-dot-' + t);
        var active = (t === tab);
        panel.classList.toggle('hidden', !active);
        btn.classList.toggle('bg-green-600', active);
        btn.classList.toggle('text-white',   active);
        btn.classList.toggle('bg-white',    !active);
        btn.classList.toggle('dark:bg-gray-700', !active);
        btn.classList.toggle('text-gray-500', !active);
        btn.classList.toggle('dark:text-gray-400', !active);
    });
}

function onMsgInput(slot) {
    // Update dot indicator on tab when text is present
    var val = (document.getElementById('msg-area-' + slot)?.value || '').trim();
    var dot = document.getElementById('msg-tab-dot-' + slot);
    if (dot) dot.classList.toggle('hidden', val === '');
    updateMsgFilledCount();
}

function updateMsgFilledCount() {
    var count = 0;
    [1, 2, 3].forEach(function(s) {
        if ((document.getElementById('msg-area-' + s)?.value || '').trim() !== '') count++;
    });
    var el = document.getElementById('msg-filled-count');
    if (el) el.textContent = count;
}

// ---------------------------------------------------------------
// Init: run on page load to reflect pre-filled clone data
// ---------------------------------------------------------------
(function initClone() {
    // Sync msg filled-count and tab dots
    updateMsgFilledCount();
    [1,2,3].forEach(function(s) {
        var val = (document.getElementById('msg-area-' + s)?.value || '').trim();
        var dot = document.getElementById('msg-tab-dot-' + s);
        if (dot && val !== '') dot.classList.remove('hidden');
    });
    // Sync delay ETA
    updateDelayEta();
})();

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
    checkLargeBlastWarning();
}

function selectAllRecipients() {
    document.querySelectorAll('.recipient-row').forEach(function(row) {
        if (!row.classList.contains('hidden')) row.querySelector('input[type="checkbox"]').checked = true;
    });
    updateCount();
}

function clearAllRecipients() {
    document.querySelectorAll('input[name="recipients[]"]').forEach(function(cb) { cb.checked = false; });
    // Reset biz-type chips
    document.querySelectorAll('.biz-chip').forEach(function(c) {
        c.classList.remove('bg-emerald-100','border-emerald-500','text-emerald-800','dark:bg-emerald-900/30','dark:text-emerald-200');
    });
    // Reset batch chips
    resetBatchChips();
    updateCount();
}

function resetBatchChips() {
    document.querySelectorAll('.batch-chip').forEach(function(c) {
        c.classList.remove('bg-indigo-100','border-indigo-500','text-indigo-700','dark:bg-indigo-900/30','dark:text-indigo-300');
    });
}

// Select all recipients in a batch range (by data-bidx)
function selectBatch(batchNum, start, end) {
    // Clear all first
    document.querySelectorAll('input[name="recipients[]"]').forEach(function(cb) { cb.checked = false; });
    document.querySelectorAll('.biz-chip').forEach(function(c) {
        c.classList.remove('bg-emerald-100','border-emerald-500','text-emerald-800','dark:bg-emerald-900/30','dark:text-emerald-200');
    });
    resetBatchChips();

    // Check if clicking already-active batch → deselect (toggle off)
    var chip = document.getElementById('batch-chip-' + batchNum);
    var wasActive = chip && chip.classList.contains('bg-indigo-100');
    if (wasActive) { updateCount(); return; }

    // Select rows in this batch
    document.querySelectorAll('.recipient-row').forEach(function(row) {
        var idx = parseInt(row.getAttribute('data-bidx') || '-1');
        if (idx >= start && idx <= end) {
            row.querySelector('input[type="checkbox"]').checked = true;
        }
    });

    // Highlight active batch chip
    if (chip) {
        chip.classList.add('bg-indigo-100','border-indigo-500','text-indigo-700','dark:bg-indigo-900/30','dark:text-indigo-300');
    }

    // Reset dropdown + sync filter so rows all visible
    var sel = document.getElementById('biz-type-filter');
    if (sel) sel.value = '';
    filterRecipients();

    updateCount();
}

function filterRecipients() {
    var q     = (document.getElementById('recipient-search')?.value || '').toLowerCase();
    var btype = document.getElementById('biz-type-filter')?.value || '';
    var vis   = 0;
    document.querySelectorAll('.recipient-row').forEach(function(row) {
        var name    = row.getAttribute('data-name')  || '';
        var phone   = row.getAttribute('data-phone') || '';
        var rowType = row.getAttribute('data-btype') || '';
        var matchQ  = q === '' || name.includes(q) || phone.includes(q);
        var matchT  = btype === '' || rowType === btype;
        var hide    = !(matchQ && matchT);
        row.classList.toggle('hidden', hide);
        if (!hide) vis++;
    });
    var el = document.getElementById('visible-count');
    if (el) el.textContent = vis;
}

// Select all visible recipients of a specific business type (chip click)
function selectByType(type) {
    document.querySelectorAll('.recipient-row').forEach(function(row) {
        if ((row.getAttribute('data-btype') || '') === type) {
            row.querySelector('input[type="checkbox"]').checked = true;
            row.classList.remove('hidden');
        }
    });
    // Highlight chip
    document.querySelectorAll('.biz-chip').forEach(function(c) {
        var active = c.getAttribute('data-type') === type;
        c.classList.toggle('bg-emerald-100',       active);
        c.classList.toggle('border-emerald-500',   active);
        c.classList.toggle('text-emerald-800',     active);
        c.classList.toggle('dark:bg-emerald-900/30',  active);
        c.classList.toggle('dark:text-emerald-200',   active);
    });
    // Sync dropdown
    var sel = document.getElementById('biz-type-filter');
    if (sel) sel.value = type;
    filterRecipients();
    updateCount();
}

// ---------------------------------------------------------------
// Delay selector
// ---------------------------------------------------------------
var currentDelay = <?= (int)$cloneDelay ?>;
var totalRecipients = <?= count($allUsers) ?>;
var LARGE_BLAST_WARNING = <?= json_encode(__('blast_large_warning')) ?>;
var LARGE_BLAST_THRESHOLD = 100;

function selectDelay(val) {
    currentDelay = val;
    [60, 30, 12, 8].forEach(function(d) {
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
    checkLargeBlastWarning();
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

function checkLargeBlastWarning() {
    var count = document.querySelectorAll('input[name="recipients[]"]:checked').length;
    var warning = document.getElementById('large-blast-warning');
    var text    = document.getElementById('large-blast-warning-text');
    if (count >= LARGE_BLAST_THRESHOLD && currentDelay < 60) {
        text.innerHTML = LARGE_BLAST_WARNING.replace(':count', count);
        warning.classList.remove('hidden');
    } else {
        warning.classList.add('hidden');
    }
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
    if (count === 0) { e.preventDefault(); alert('Pilih sekurang-kurangnya satu penerima.'); return; }

    // Count filled message variations
    var msgVars = 0;
    [1,2,3].forEach(function(s) {
        if ((document.getElementById('msg-area-' + s)?.value || '').trim() !== '') msgVars++;
    });
    if (msgVars === 0) { e.preventDefault(); alert('Isikan sekurang-kurangnya Variasi 1 mesej.'); return; }

    // Count filled image slots
    var imgSlots = 0;
    [1,2,3].forEach(function(s) {
        var inp = document.getElementById('blast_image_' + s);
        if (inp && inp.files && inp.files.length > 0) imgSlots++;
    });

    var varInfo = msgVars > 1 ? ' (' + msgVars + ' variasi mesej' + (imgSlots > 1 ? ', ' + imgSlots + ' gambar' : '') + ')' : '';

    if (scheduleMode === 'later') {
        var dt = document.getElementById('scheduled_at').value;
        if (!dt) { e.preventDefault(); return; }
        if (new Date(dt) <= new Date()) { e.preventDefault(); return; }
        var dtFormatted = new Date(dt).toLocaleString('<?= \App\Core\Lang::current() === 'ms' ? 'ms-MY' : 'en-MY' ?>', {dateStyle:'medium', timeStyle:'short'});
        var msg = LANG.confirmSchedule.replace(':count', count).replace(':time', dtFormatted) + varInfo;
        if (!confirm(msg)) e.preventDefault();
    } else {
        var msg = LANG.confirmNow.replace(':count', count) + varInfo;
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
