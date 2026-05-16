<?php use App\Core\{CSRF, Session}; ?>

<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white"><?= __('sign_in_account') ?></h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= __('no_account') ?>
        <a href="<?= BASE_URI ?>/register" class="text-brand-600 dark:text-brand-400 hover:underline font-medium"><?= __('register') ?></a>
    </p>
</div>

<form method="POST" action="<?= BASE_URI ?>/login" novalidate>
    <?= CSRF::field() ?>

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

    <!-- Password -->
    <div class="mb-4">
        <div class="flex items-center justify-between mb-1.5">
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                <?= __('password') ?>
            </label>
        </div>
        <div class="relative">
            <input type="password"
                   id="password"
                   name="password"
                   required autocomplete="current-password"
                   class="w-full px-3.5 py-2.5 pr-11 rounded-xl border border-gray-300 dark:border-gray-600
                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                          placeholder-gray-400 dark:placeholder-gray-500
                          focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                          transition-colors text-sm"
                   placeholder="••••••••">
            <button type="button" onclick="togglePassword('password')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Remember me -->
    <div class="flex items-center mb-6">
        <input type="checkbox" id="remember" name="remember" value="1"
               class="w-4 h-4 text-brand-600 border-gray-300 dark:border-gray-600 rounded focus:ring-brand-500">
        <label for="remember" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
            <?= __('remember_me') ?>
        </label>
    </div>

    <button type="submit"
            class="w-full py-2.5 px-4 bg-brand-600 hover:bg-brand-700 active:bg-brand-800
                   text-white font-semibold rounded-xl transition-colors text-sm
                   focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2
                   dark:focus:ring-offset-gray-800">
        <?= __('login') ?>
    </button>
</form>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
