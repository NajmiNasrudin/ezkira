<?php use App\Core\{CSRF, Session}; ?>

<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white"><?= __('google_complete_title') ?></h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= __('google_complete_hint') ?></p>
</div>

<!-- Google account preview -->
<div class="flex items-center gap-3 p-3.5 mb-6 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600">
    <div class="w-9 h-9 rounded-full bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 text-brand-600 dark:text-brand-400" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
        </svg>
    </div>
    <div class="min-w-0">
        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">
            <?= htmlspecialchars($pending['name'], ENT_QUOTES) ?>
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
            <?= htmlspecialchars($pending['email'], ENT_QUOTES) ?>
        </p>
    </div>
    <span class="ml-auto shrink-0 inline-flex items-center gap-1 text-xs text-green-600 dark:text-green-400 font-medium">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
        Google
    </span>
</div>

<form method="POST" action="<?= BASE_URI ?>/auth/google/complete" novalidate>
    <?= CSRF::field() ?>

    <!-- WhatsApp -->
    <div class="mb-4">
        <label for="whatsapp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            <?= __('whatsapp') ?>
        </label>
        <div class="flex">
            <span class="inline-flex items-center px-3.5 rounded-l-xl border border-r-0 border-gray-300 dark:border-gray-600
                         bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm font-medium">
                +60
            </span>
            <input type="tel"
                   id="whatsapp"
                   name="whatsapp"
                   value="<?= htmlspecialchars(ltrim(Session::old('whatsapp', ''), '+60'), ENT_QUOTES) ?>"
                   required autocomplete="tel"
                   class="flex-1 px-3.5 py-2.5 rounded-r-xl border border-gray-300 dark:border-gray-600
                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                          placeholder-gray-400 dark:placeholder-gray-500
                          focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                          transition-colors text-sm"
                   placeholder="112345678">
        </div>
        <p class="mt-1 text-xs text-gray-400"><?= __('whatsapp_hint') ?></p>
    </div>

    <!-- Business Type -->
    <div class="mb-6">
        <label for="business_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            <?= __('business_type') ?>
        </label>
        <select id="business_type" name="business_type"
                onchange="toggleOtherField(this.value)"
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                       focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                       transition-colors text-sm">
            <option value="">— <?= __('business_type') ?> —</option>
            <?php foreach ($businessTypes as $key => $label): ?>
                <option value="<?= htmlspecialchars($key, ENT_QUOTES) ?>"
                    <?= Session::old('business_type') === $key ? 'selected' : '' ?>>
                    <?= htmlspecialchars($label, ENT_QUOTES) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div id="other-field" class="mt-2 <?= Session::old('business_type') === 'other' ? '' : 'hidden' ?>">
            <input type="text"
                   id="business_type_other"
                   name="business_type_other"
                   value="<?= htmlspecialchars(Session::old('business_type_other', ''), ENT_QUOTES) ?>"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                          placeholder-gray-400 dark:placeholder-gray-500
                          focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                          transition-colors text-sm"
                   placeholder="<?= __('business_type_other') ?>">
        </div>
    </div>

    <button type="submit"
            class="w-full py-2.5 px-4 bg-brand-600 hover:bg-brand-700 active:bg-brand-800
                   text-white font-semibold rounded-xl transition-colors text-sm
                   focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2
                   dark:focus:ring-offset-gray-800">
        <?= __('google_complete_btn') ?>
    </button>
</form>

<script>
function toggleOtherField(value) {
    const otherField = document.getElementById('other-field');
    if (value === 'other') {
        otherField.classList.remove('hidden');
        document.getElementById('business_type_other').focus();
    } else {
        otherField.classList.add('hidden');
    }
}

// Auto-prepend +60 on submit
document.querySelector('form').addEventListener('submit', function () {
    const wa = document.getElementById('whatsapp');
    if (wa.value && !wa.value.startsWith('+')) {
        wa.value = '+60' + wa.value.replace(/^0+/, '');
    }
});
</script>
