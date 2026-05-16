<?php use App\Core\{CSRF, Session}; ?>

<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white"><?= __('forgot_password') ?></h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= __('forgot_password_hint') ?></p>
</div>

<form method="POST" action="<?= BASE_URI ?>/forgot-password" novalidate>
    <?= CSRF::field() ?>

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

    <button type="submit"
            class="w-full py-2.5 px-4 bg-brand-600 hover:bg-brand-700 active:bg-brand-800
                   text-white font-semibold rounded-xl transition-colors text-sm
                   focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2
                   dark:focus:ring-offset-gray-800 mb-4">
        <?= __('send_reset_link') ?>
    </button>

    <p class="text-center text-sm text-gray-500 dark:text-gray-400">
        <a href="<?= BASE_URI ?>/login" class="text-brand-600 dark:text-brand-400 hover:underline font-medium">
            <?= __('back_to_login') ?>
        </a>
    </p>
</form>
