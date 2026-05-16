<?php use App\Core\{CSRF, Session}; ?>

<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white"><?= __('reset_password') ?></h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= __('reset_password_hint') ?></p>
</div>

<form method="POST" action="<?= BASE_URI ?>/reset-password" novalidate>
    <?= CSRF::field() ?>
    <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '', ENT_QUOTES) ?>">

    <div class="mb-4">
        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            <?= __('new_password') ?>
        </label>
        <div class="relative">
            <input type="password"
                   id="password"
                   name="password"
                   required minlength="8"
                   class="w-full px-3.5 py-2.5 pr-11 rounded-xl border border-gray-300 dark:border-gray-600
                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                          focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                          transition-colors text-sm"
                   placeholder="<?= __('password_min_hint') ?>">
            <button type="button" onclick="togglePassword('password')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="mb-6">
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            <?= __('confirm_new_pass') ?>
        </label>
        <div class="relative">
            <input type="password"
                   id="password_confirmation"
                   name="password_confirmation"
                   required
                   class="w-full px-3.5 py-2.5 pr-11 rounded-xl border border-gray-300 dark:border-gray-600
                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                          focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                          transition-colors text-sm"
                   placeholder="••••••••">
            <button type="button" onclick="togglePassword('password_confirmation')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
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
        <?= __('reset_password_btn') ?>
    </button>
</form>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
