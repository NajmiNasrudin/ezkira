<?php use App\Core\{CSRF, Session}; ?>

<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white"><?= __('create_account') ?></h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= __('have_account') ?>
        <a href="<?= BASE_URI ?>/login" class="text-brand-600 dark:text-brand-400 hover:underline font-medium"><?= __('login') ?></a>
    </p>
</div>

<form method="POST" action="<?= BASE_URI ?>/register" novalidate>
    <?= CSRF::field() ?>

    <!-- Full Name -->
    <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            <?= __('full_name') ?>
        </label>
        <input type="text"
               id="name"
               name="name"
               value="<?= Session::old('name') ?>"
               required autocomplete="name"
               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                      placeholder-gray-400 dark:placeholder-gray-500
                      focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                      transition-colors text-sm"
               placeholder="Ahmad bin Ali">
    </div>

    <!-- Email -->
    <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            <?= __('email') ?>
        </label>
        <input type="email"
               id="email"
               name="email"
               value="<?= Session::old('email') ?>"
               required autocomplete="email"
               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                      placeholder-gray-400 dark:placeholder-gray-500
                      focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                      transition-colors text-sm"
               placeholder="you@example.com">
    </div>

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
                   value="<?= ltrim(Session::old('whatsapp', ''), '+60') ?>"
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
    <div class="mb-4">
        <label for="business_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            <?= __('business_type') ?>
        </label>
        <select id="business_type" name="business_type"
                onchange="toggleOtherField(this.value)"
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                       focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                       transition-colors text-sm">
            <option value="">— Pilih jenis perniagaan —</option>
            <?php foreach ($businessTypes as $key => $label): ?>
                <option value="<?= htmlspecialchars($key, ENT_QUOTES) ?>"
                    <?= Session::old('business_type') === $key ? 'selected' : '' ?>>
                    <?= htmlspecialchars($label, ENT_QUOTES) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <!-- "Other" free-text field -->
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

    <!-- Password -->
    <div class="mb-4">
        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            <?= __('password') ?>
        </label>
        <div class="relative">
            <input type="password"
                   id="password"
                   name="password"
                   required autocomplete="new-password" minlength="8"
                   class="w-full px-3.5 py-2.5 pr-11 rounded-xl border border-gray-300 dark:border-gray-600
                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                          placeholder-gray-400 dark:placeholder-gray-500
                          focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                          transition-colors text-sm"
                   placeholder="Min. 8 characters">
            <button type="button" onclick="togglePassword('password')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Confirm Password -->
    <div class="mb-6">
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            <?= __('confirm_password') ?>
        </label>
        <div class="relative">
            <input type="password"
                   id="password_confirmation"
                   name="password_confirmation"
                   required autocomplete="new-password"
                   class="w-full px-3.5 py-2.5 pr-11 rounded-xl border border-gray-300 dark:border-gray-600
                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                          placeholder-gray-400 dark:placeholder-gray-500
                          focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                          transition-colors text-sm"
                   placeholder="Repeat password">
            <button type="button" onclick="togglePassword('password_confirmation')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>
    </div>

    <button type="submit"
            class="w-full py-2.5 px-4 bg-brand-600 hover:bg-brand-700 active:bg-brand-800
                   text-white font-semibold rounded-xl transition-colors text-sm
                   focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2
                   dark:focus:ring-offset-gray-800">
        <?= __('register') ?>
    </button>
</form>

<!-- Divider -->
<div class="flex items-center gap-3 my-5">
    <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700"></div>
    <span class="text-xs text-gray-400 dark:text-gray-500 font-medium uppercase tracking-wide"><?= __('or') ?></span>
    <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700"></div>
</div>

<!-- Google Sign-In Button -->
<a href="<?= BASE_URI ?>/auth/google"
   class="group relative flex items-center justify-center gap-3 w-full py-2.5 px-4 rounded-xl
          border border-gray-300 dark:border-gray-600
          bg-white dark:bg-gray-800
          hover:bg-gray-50 dark:hover:bg-gray-750 hover:border-gray-400 dark:hover:border-gray-500
          hover:shadow-md
          text-gray-700 dark:text-gray-200 font-medium text-sm
          transition-all duration-200
          focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
    <svg width="18" height="18" viewBox="0 0 48 48" style="flex-shrink:0">
        <path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9.1 3.6l6.8-6.8C35.8 2.5 30.3 0 24 0 14.6 0 6.6 5.4 2.7 13.3l7.9 6.1C12.5 13 17.8 9.5 24 9.5z"/>
        <path fill="#4285F4" d="M46.6 24.5c0-1.6-.1-3.1-.4-4.5H24v8.5h12.7c-.6 3-2.3 5.5-4.8 7.2l7.5 5.8c4.4-4 7.2-10 7.2-17z"/>
        <path fill="#FBBC05" d="M10.6 28.6A14.7 14.7 0 0 1 9.5 24c0-1.6.3-3.2.7-4.6l-7.9-6.1A24 24 0 0 0 0 24c0 3.9.9 7.5 2.7 10.7l7.9-6.1z"/>
        <path fill="#34A853" d="M24 48c6.3 0 11.6-2.1 15.5-5.7l-7.5-5.8c-2.1 1.4-4.8 2.2-8 2.2-6.2 0-11.5-4.2-13.4-9.9l-7.9 6.1C6.6 42.6 14.6 48 24 48z"/>
    </svg>
    <span><?= __('sign_in_google') ?></span>
</a>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

function toggleOtherField(value) {
    const otherField = document.getElementById('other-field');
    if (value === 'other') {
        otherField.classList.remove('hidden');
        document.getElementById('business_type_other').focus();
    } else {
        otherField.classList.add('hidden');
    }
}

// Auto-prepend +60 handling: strip it from display, re-add on submit
document.querySelector('form').addEventListener('submit', function(e) {
    const wa = document.getElementById('whatsapp');
    if (wa.value && !wa.value.startsWith('+')) {
        wa.value = '+60' + wa.value.replace(/^0+/, '');
    }
});
</script>
