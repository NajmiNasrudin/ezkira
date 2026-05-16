<?php
use App\Core\{CSRF, Session};

$activeTab = Session::getFlash('tab') ?? 'info';
$errors    = Session::getFlash('errors') ?? [];
// Re-read flash if not already consumed by flash.php (flash.php runs before this)
// errors were consumed in flash.php — but the tab flash was not (it's a separate key)

$tabClass = fn(string $tab) => $activeTab === $tab
    ? 'border-brand-600 text-brand-600 dark:border-brand-400 dark:text-brand-400'
    : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600';
?>

<div class="max-w-3xl mx-auto">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= __('edit_profile') ?></h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your account settings and preferences.</p>
    </div>

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
        <nav class="flex gap-6 -mb-px overflow-x-auto">
            <button onclick="switchTab('info')"
                    id="tab-info"
                    class="pb-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap <?= $tabClass('info') ?>">
                <?= __('personal_info') ?>
            </button>
            <button onclick="switchTab('avatar')"
                    id="tab-avatar"
                    class="pb-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap <?= $tabClass('avatar') ?>">
                <?= __('profile_photo') ?>
            </button>
            <button onclick="switchTab('password')"
                    id="tab-password"
                    class="pb-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap <?= $tabClass('password') ?>">
                <?= __('change_password') ?>
            </button>
            <button onclick="switchTab('preferences')"
                    id="tab-preferences"
                    class="pb-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap <?= $tabClass('preferences') ?>">
                <?= __('preferences') ?>
            </button>
        </nav>
    </div>

    <!-- Tab: Personal Info -->
    <div id="panel-info" class="tab-panel <?= $activeTab !== 'info' ? 'hidden' : '' ?>">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <form method="POST" action="<?= BASE_URI ?>/profile/update" novalidate>
                <?= CSRF::field() ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Company Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            <?= __('company_name') ?>
                        </label>
                        <input type="text" name="name"
                               value="<?= htmlspecialchars(Session::old('name', $user['name'] ?? ''), ENT_QUOTES) ?>"
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                                      transition-colors text-sm">
                    </div>

                    <!-- PIC Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            <?= __('pic_name') ?>
                        </label>
                        <input type="text" name="pic_name"
                               value="<?= htmlspecialchars(Session::old('pic_name', $user['pic_name'] ?? ''), ENT_QUOTES) ?>"
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                                      transition-colors text-sm">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            <?= __('email') ?>
                        </label>
                        <input type="email" name="email"
                               value="<?= htmlspecialchars(Session::old('email', $user['email'] ?? ''), ENT_QUOTES) ?>"
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                                      transition-colors text-sm">
                    </div>

                    <!-- WhatsApp -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            <?= __('whatsapp') ?>
                        </label>
                        <input type="tel" name="whatsapp"
                               value="<?= htmlspecialchars(Session::old('whatsapp', $user['whatsapp_number'] ?? ''), ENT_QUOTES) ?>"
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                                      transition-colors text-sm"
                               placeholder="+60112345678">
                    </div>
                </div>

                <div class="mt-5 flex justify-end">
                    <button type="submit"
                            class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl transition-colors">
                        <?= __('save_changes') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tab: Avatar -->
    <div id="panel-avatar" class="tab-panel <?= $activeTab !== 'avatar' ? 'hidden' : '' ?>">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 mb-6">
                <!-- Current avatar preview -->
                <div class="relative shrink-0">
                    <?php if (!empty($user['profile_image'])): ?>
                        <img id="avatar-preview"
                             src="<?= BASE_URI ?>/<?= htmlspecialchars($user['profile_image'], ENT_QUOTES) ?>"
                             alt="Profile photo"
                             class="w-24 h-24 rounded-2xl object-cover ring-4 ring-brand-100 dark:ring-brand-900">
                    <?php else: ?>
                        <div id="avatar-preview-placeholder"
                             class="w-24 h-24 rounded-2xl bg-brand-600 flex items-center justify-center text-white text-3xl font-bold ring-4 ring-brand-100 dark:ring-brand-900">
                            <?= strtoupper(mb_substr($user['name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <img id="avatar-preview" src="" alt="" class="w-24 h-24 rounded-2xl object-cover ring-4 ring-brand-100 dark:ring-brand-900 hidden">
                    <?php endif; ?>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mb-1"><?= __('profile_photo') ?></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3"><?= __('photo_hint') ?></p>
                </div>
            </div>

            <form method="POST" action="<?= BASE_URI ?>/profile/avatar" enctype="multipart/form-data">
                <?= CSRF::field() ?>
                <div class="flex items-center gap-4">
                    <label for="avatar-input"
                           class="cursor-pointer flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium
                                  hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <?= __('upload_photo') ?>
                        <input type="file" id="avatar-input" name="avatar"
                               accept="image/jpeg,image/png,image/webp" class="sr-only">
                    </label>
                    <button type="submit"
                            class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl transition-colors">
                        <?= __('save_changes') ?>
                    </button>
                </div>
                <p id="file-chosen" class="mt-2 text-xs text-gray-400 hidden"></p>
            </form>
        </div>
    </div>

    <!-- Tab: Password -->
    <div id="panel-password" class="tab-panel <?= $activeTab !== 'password' ? 'hidden' : '' ?>">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <form method="POST" action="<?= BASE_URI ?>/profile/password" novalidate>
                <?= CSRF::field() ?>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            <?= __('current_password') ?>
                        </label>
                        <input type="password" name="current_password" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                                      transition-colors text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            <?= __('new_password') ?>
                        </label>
                        <input type="password" name="new_password" required minlength="8"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                                      transition-colors text-sm"
                               placeholder="Min. 8 characters">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            <?= __('confirm_new_pass') ?>
                        </label>
                        <input type="password" name="new_password_confirmation" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                                      transition-colors text-sm">
                    </div>
                </div>

                <div class="mt-5 flex justify-end">
                    <button type="submit"
                            class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl transition-colors">
                        <?= __('change_password') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tab: Preferences -->
    <div id="panel-preferences" class="tab-panel <?= $activeTab !== 'preferences' ? 'hidden' : '' ?>">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <form method="POST" action="<?= BASE_URI ?>/profile/preferences">
                <?= CSRF::field() ?>

                <!-- Language -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        <?= __('language_pref') ?>
                    </label>
                    <div class="flex gap-3">
                        <?php foreach (['en' => __('lang_en'), 'ms' => __('lang_ms')] as $code => $label): ?>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="language" value="<?= $code ?>"
                                       <?= ($user['language'] ?? 'en') === $code ? 'checked' : '' ?>
                                       class="w-4 h-4 text-brand-600 border-gray-300 focus:ring-brand-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300"><?= htmlspecialchars($label, ENT_QUOTES) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Dark Mode -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        <?= __('dark_mode') ?>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer w-fit">
                        <input type="checkbox" name="dark_mode" value="1"
                               id="dark-mode-pref"
                               <?= !empty($user['dark_mode']) ? 'checked' : '' ?>
                               class="w-4 h-4 text-brand-600 border-gray-300 rounded focus:ring-brand-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300"><?= __('enable_dark_mode') ?></span>
                    </label>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl transition-colors">
                        <?= __('save_changes') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= BASE_URI ?>/assets/js/profile.js"></script>
<script>
// Initialise with server-determined tab
switchTab('<?= htmlspecialchars($activeTab, ENT_QUOTES) ?>');
</script>
