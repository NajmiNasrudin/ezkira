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
            <button type="button" onclick="switchTab('info')"
                    id="tab-info"
                    class="pb-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap <?= $tabClass('info') ?>">
                <?= __('personal_info') ?>
            </button>
            <button type="button" onclick="switchTab('avatar')"
                    id="tab-avatar"
                    class="pb-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap <?= $tabClass('avatar') ?>">
                <?= __('profile_photo') ?>
            </button>
            <button type="button" onclick="switchTab('password')"
                    id="tab-password"
                    class="pb-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap <?= $tabClass('password') ?>">
                <?= __('change_password') ?>
            </button>
            <button type="button" onclick="switchTab('preferences')"
                    id="tab-preferences"
                    class="pb-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap <?= $tabClass('preferences') ?>">
                <?= __('preferences') ?>
            </button>
            <?php if (($user['role'] ?? '') === 'admin'): ?>
            <button type="button" onclick="switchTab('branding')"
                    id="tab-branding"
                    class="pb-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap <?= $tabClass('branding') ?>">
                <?= __('branding') ?>
            </button>
            <button type="button" onclick="switchTab('wa_greeting')"
                    id="tab-wa_greeting"
                    class="pb-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap <?= $tabClass('wa_greeting') ?>">
                <?= __('wa_greeting') ?>
            </button>
            <?php endif; ?>
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

                    <!-- Business Type (full-width) -->
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            <?= __('business_type') ?>
                        </label>
                        <?php
                            $currentBizType = Session::old('business_type', $user['business_type'] ?? '');
                        ?>
                        <select name="business_type"
                                onchange="toggleProfileOtherField(this.value)"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                       focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                                       transition-colors text-sm">
                            <option value="">— Pilih jenis perniagaan —</option>
                            <?php foreach ($businessTypes as $key => $label): ?>
                                <option value="<?= htmlspecialchars($key, ENT_QUOTES) ?>"
                                    <?= $currentBizType === $key ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($label, ENT_QUOTES) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="profile-other-field" class="mt-2 <?= $currentBizType === 'other' ? '' : 'hidden' ?>">
                            <input type="text"
                                   name="business_type_other"
                                   value="<?= htmlspecialchars(Session::old('business_type_other', $user['business_type_other'] ?? ''), ENT_QUOTES) ?>"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                          focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                                          transition-colors text-sm"
                                   placeholder="<?= __('business_type_other') ?>">
                        </div>
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
    <!-- Tab: Branding (admin only) -->
    <?php if (($user['role'] ?? '') === 'admin'):
        $siteLogo = (new \Models\Setting())->get('site_logo', '');
    ?>
    <div id="panel-branding" class="tab-panel <?= $activeTab !== 'branding' ? 'hidden' : '' ?>">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 space-y-6">

            <!-- Current logo preview -->
            <div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3"><?= __('site_logo_current') ?></h3>
                <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-600">
                    <?php if (!empty($siteLogo) && file_exists(BASE_PATH . '/' . $siteLogo)): ?>
                        <img src="<?= BASE_URI ?>/<?= htmlspecialchars($siteLogo, ENT_QUOTES) ?>"
                             alt="Logo" class="h-10 w-auto max-w-[200px] object-contain">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800 dark:text-white"><?= basename($siteLogo) ?></p>
                            <p class="text-xs text-green-600 dark:text-green-400 mt-0.5">✓ Custom logo active</p>
                        </div>
                        <form method="POST" action="<?= BASE_URI ?>/profile/logo/remove">
                            <?= CSRF::field() ?>
                            <button type="submit"
                                    onclick="return confirm('<?= __('site_logo_remove') ?>?')"
                                    class="text-xs text-red-500 hover:text-red-700 font-medium px-3 py-1.5 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <?= __('site_logo_remove') ?>
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="flex items-center gap-3">
                            <img src="<?= BASE_URI ?>/assets/img/logo-mark.svg" alt="Default" class="w-9 h-9 rounded-lg">
                            <span class="text-xs font-bold" style="color:#C4A028">ez</span><span class="text-xs font-bold" style="color:#163020">kira</span>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-gray-500"><?= __('site_logo_default') ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Upload new logo -->
            <form method="POST" action="<?= BASE_URI ?>/profile/logo" enctype="multipart/form-data">
                <?= CSRF::field() ?>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3"><?= __('site_logo_upload') ?></h3>

                <div id="logo-drop-zone"
                     onclick="document.getElementById('logo-file').click()"
                     class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 cursor-pointer hover:border-brand-400 hover:bg-brand-50 dark:hover:bg-brand-900/10 transition-colors">
                    <img id="logo-preview" src="" alt="" class="hidden h-12 w-auto max-w-[200px] object-contain mb-1">
                    <div id="logo-placeholder" class="flex flex-col items-center gap-1 text-gray-400">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm font-medium"><?= __('blast_image_placeholder') ?></p>
                    </div>
                    <p id="logo-filename" class="text-xs text-gray-400 hidden"></p>
                </div>
                <input type="file" id="logo-file" name="site_logo" accept="image/jpeg,image/png,image/webp,image/svg+xml" class="sr-only" onchange="previewLogo(this)">
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2"><?= __('site_logo_hint') ?></p>

                <div class="flex justify-end mt-4">
                    <button type="submit"
                            class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl transition-colors">
                        <?= __('site_logo_upload') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tab: WA Auto Greeting (admin only) -->
    <?php if (($user['role'] ?? '') === 'admin'):
        $setting       = new \Models\Setting();
        $greetEnabled  = (bool)(int)$setting->get('wa_greeting_enabled', '0');
        $greetMessage  = $setting->get('wa_greeting_message', '');
    ?>
    <div id="panel-wa_greeting" class="tab-panel <?= $activeTab !== 'wa_greeting' ? 'hidden' : '' ?>">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 space-y-6">

            <!-- Header -->
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white"><?= __('wa_greeting') ?></h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= __('wa_greeting_subtitle') ?></p>
                <p class="text-xs text-amber-600 dark:text-amber-400 mt-2 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <?= __('wa_greeting_requires') ?>
                </p>
            </div>

            <form method="POST" action="<?= BASE_URI ?>/profile/greeting">
                <?= CSRF::field() ?>

                <!-- Enable toggle -->
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-600 mb-5">
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-white"><?= __('wa_greeting_enabled') ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?= __('wa_greeting_subtitle') ?></p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="wa_greeting_enabled" value="1"
                               id="wa_greeting_toggle"
                               onchange="document.getElementById('wa_greeting_form_fields').classList.toggle('hidden', !this.checked)"
                               <?= $greetEnabled ? 'checked' : '' ?>
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand-500 dark:peer-focus:ring-brand-600 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-600"></div>
                    </label>
                </div>

                <!-- Message fields (hidden when disabled) -->
                <div id="wa_greeting_form_fields" class="space-y-4 <?= !$greetEnabled ? 'hidden' : '' ?>">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            <?= __('wa_greeting_message') ?>
                        </label>
                        <textarea name="wa_greeting_message" rows="5"
                                  placeholder="<?= htmlspecialchars(__('wa_greeting_placeholder'), ENT_QUOTES) ?>"
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                         bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                         focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                                         transition-colors text-sm resize-y font-mono"><?= htmlspecialchars($greetMessage, ENT_QUOTES) ?></textarea>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5"><?= __('wa_greeting_hint') ?></p>
                    </div>

                    <!-- Live preview -->
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1"><?= __('wa_greeting_preview') ?></p>
                        <div id="greeting-preview"
                             class="text-sm bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-3 text-gray-700 dark:text-gray-300 whitespace-pre-wrap break-words min-h-[48px]">
                            <?= htmlspecialchars(str_replace(['{name}','{nama}'], __('wa_greeting_preview_name') ?: 'Ali', $greetMessage), ENT_QUOTES) ?>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-5">
                    <button type="submit"
                            class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl transition-colors">
                        <?= __('save_changes') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

</div>

<script src="<?= BASE_URI ?>/assets/js/profile.js"></script>
<script>
// Initialise with server-determined tab
switchTab('<?= htmlspecialchars($activeTab, ENT_QUOTES) ?>');

function previewLogo(input) {
    if (!input.files || !input.files[0]) return;
    var file = input.files[0];
    var reader = new FileReader();
    reader.onload = function(e) {
        var preview = document.getElementById('logo-preview');
        var placeholder = document.getElementById('logo-placeholder');
        var filename = document.getElementById('logo-filename');
        preview.src = e.target.result;
        preview.classList.remove('hidden');
        placeholder.classList.add('hidden');
        filename.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
        filename.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}

function toggleProfileOtherField(value) {
    const field = document.getElementById('profile-other-field');
    if (value === 'other') {
        field.classList.remove('hidden');
    } else {
        field.classList.add('hidden');
    }
}

// WA Greeting live preview
(function () {
    const ta = document.querySelector('textarea[name="wa_greeting_message"]');
    const preview = document.getElementById('greeting-preview');
    if (!ta || !preview) return;
    const previewName = '<?= addslashes(__("wa_greeting_preview_name") ?: "Ali") ?>';
    ta.addEventListener('input', function () {
        const raw = this.value || '';
        preview.textContent = raw.replace(/\{name\}/g, previewName).replace(/\{nama\}/g, previewName) || '—';
    });
})();
</script>
