<?php

namespace Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\CSRF;
use App\Core\Logger;
use App\Core\Session;
use Models\Setting;
use Models\User;

class ProfileController extends Controller
{
    public function index(): void
    {
        $this->view('profile/index', [
            'user'          => Auth::user(),
            'businessTypes' => \Models\User::BUSINESS_TYPES,
        ], 'main', __('edit_profile'));
    }

    // -------------------------------------------------------------------------
    // Update personal info
    // -------------------------------------------------------------------------

    public function update(): void
    {
        CSRF::check();

        $userId   = Auth::id();
        $name             = trim($_POST['name']     ?? '');
        $picName          = trim($_POST['pic_name'] ?? '');
        $email            = strtolower(trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? ''));
        $whatsapp         = trim($_POST['whatsapp'] ?? '');
        $businessType     = trim($_POST['business_type'] ?? '');
        $businessOther    = trim($_POST['business_type_other'] ?? '');

        if ($whatsapp !== '' && !str_starts_with($whatsapp, '+')) {
            $whatsapp = '+60' . ltrim($whatsapp, '0');
        }

        $errors = $this->validate(
            ['name' => $name, 'pic_name' => $picName, 'email' => $email, 'whatsapp' => $whatsapp],
            [
                'name'     => 'required|min:2|max:100',
                'pic_name' => 'required|min:2|max:100',
                'email'    => 'required|email|max:255',
                'whatsapp' => 'required|regex:/^\+60[0-9]{8,11}$/',
            ]
        );

        $userModel = new User();

        if (empty($errors['email']) && $userModel->emailExists($email, $userId)) {
            $errors['email'] = __('email_taken');
        }

        if ($errors) {
            Session::flashInput($_POST);
            Session::flash('errors', $errors);
            $this->redirect('/profile');
        }

        $userModel->update($userId, [
            'name'                => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            'pic_name'            => htmlspecialchars($picName, ENT_QUOTES, 'UTF-8'),
            'email'               => $email,
            'whatsapp_number'     => $whatsapp,
            'business_type'       => $businessType !== '' ? htmlspecialchars($businessType, ENT_QUOTES, 'UTF-8') : null,
            'business_type_other' => ($businessType === 'other' && $businessOther !== '')
                                        ? htmlspecialchars($businessOther, ENT_QUOTES, 'UTF-8')
                                        : null,
        ]);

        $updated = $userModel->findById($userId);
        Auth::refreshUser($updated);

        Logger::log('profile_update', $userId, 'Profile information updated');
        Session::flash('success', __('profile_updated'));
        $this->redirect('/profile');
    }

    // -------------------------------------------------------------------------
    // Change password
    // -------------------------------------------------------------------------

    public function password(): void
    {
        CSRF::check();

        $userId      = Auth::id();
        $current     = $_POST['current_password'] ?? '';
        $new         = $_POST['new_password']      ?? '';
        $confirm     = $_POST['new_password_confirmation'] ?? '';

        $errors = $this->validate(
            [
                'new_password'              => $new,
                'new_password_confirmation' => $confirm,
            ],
            [
                'new_password' => 'required|min:8|confirmed',
            ]
        );

        if (empty($current)) {
            $errors['current_password'] = __('field_required', ['field' => __('current_password')]);
        }

        if (!$errors) {
            $userModel = new User();
            $user      = $userModel->findById($userId);

            if (!$user || !password_verify($current, $user['password'])) {
                $errors['current_password'] = __('current_password_wrong');
            }
        }

        if ($errors) {
            Session::flash('errors', $errors);
            Session::flash('tab', 'password');
            $this->redirect('/profile');
        }

        (new User())->update($userId, [
            'password' => password_hash($new, PASSWORD_BCRYPT, ['cost' => 12]),
        ]);

        Logger::log('password_change', $userId, 'Password changed');
        Session::flash('success', __('password_changed'));
        Session::flash('tab', 'password');
        $this->redirect('/profile');
    }

    // -------------------------------------------------------------------------
    // Upload avatar
    // -------------------------------------------------------------------------

    public function avatar(): void
    {
        CSRF::check();

        $userId = Auth::id();

        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] === UPLOAD_ERR_NO_FILE) {
            Session::flash('error', __('no_file_selected'));
            Session::flash('tab', 'avatar');
            $this->redirect('/profile');
        }

        $file = $_FILES['avatar'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', __('upload_failed'));
            Session::flash('tab', 'avatar');
            $this->redirect('/profile');
        }

        if ($file['size'] > UPLOAD_MAX_SIZE) {
            Session::flash('error', __('file_too_large'));
            Session::flash('tab', 'avatar');
            $this->redirect('/profile');
        }

        // Check MIME type using finfo (not $_FILES['type'] — user-supplied)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);

        if (!in_array($mime, ALLOWED_IMG_TYPES, true)) {
            Session::flash('error', __('invalid_file_type'));
            Session::flash('tab', 'avatar');
            $this->redirect('/profile');
        }

        // Extension from real MIME type
        $ext = match($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => 'jpg',
        };

        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $destDir  = BASE_PATH . '/uploads/profiles/';
        $destPath = $destDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            Session::flash('error', __('upload_failed'));
            Session::flash('tab', 'avatar');
            $this->redirect('/profile');
        }

        // Delete old avatar
        $userModel = new User();
        $current   = Auth::user();
        if (!empty($current['profile_image'])) {
            $oldFile = BASE_PATH . '/' . ltrim($current['profile_image'], '/');
            if (file_exists($oldFile) && is_file($oldFile)) {
                unlink($oldFile);
            }
        }

        $imagePath = 'uploads/profiles/' . $filename;
        $userModel->update($userId, ['profile_image' => $imagePath]);

        $updated = $userModel->findById($userId);
        Auth::refreshUser($updated);

        Logger::log('avatar_upload', $userId, 'Profile photo updated');
        Session::flash('success', __('avatar_updated'));
        Session::flash('tab', 'avatar');
        $this->redirect('/profile');
    }

    // -------------------------------------------------------------------------
    // Upload site logo (admin only)
    // -------------------------------------------------------------------------

    public function uploadLogo(): void
    {
        CSRF::check();
        if (!Auth::hasRole('admin')) { http_response_code(403); exit; }

        if (!isset($_FILES['site_logo']) || $_FILES['site_logo']['error'] === UPLOAD_ERR_NO_FILE) {
            Session::flash('error', __('no_file_selected'));
            Session::flash('tab', 'branding');
            $this->redirect('/profile');
        }

        $file = $_FILES['site_logo'];
        if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > UPLOAD_MAX_SIZE) {
            Session::flash('error', __('file_too_large'));
            Session::flash('tab', 'branding');
            $this->redirect('/profile');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
        if (!in_array($mime, $allowed, true)) {
            Session::flash('error', __('invalid_file_type'));
            Session::flash('tab', 'branding');
            $this->redirect('/profile');
        }

        $ext = match($mime) {
            'image/jpeg'     => 'jpg',
            'image/png'      => 'png',
            'image/webp'     => 'webp',
            'image/svg+xml'  => 'svg',
            default          => 'png',
        };

        $uploadDir = BASE_PATH . '/uploads/logos/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $destPath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            Session::flash('error', __('upload_failed'));
            Session::flash('tab', 'branding');
            $this->redirect('/profile');
        }

        // Delete old logo
        $setting = new Setting();
        $oldLogo = $setting->get('site_logo', '');
        if ($oldLogo && file_exists(BASE_PATH . '/' . $oldLogo)) {
            unlink(BASE_PATH . '/' . $oldLogo);
        }

        $setting->set('site_logo', 'uploads/logos/' . $filename);
        Logger::log('logo_upload', Auth::id(), 'Site logo updated');
        Session::flash('success', __('site_logo_updated'));
        Session::flash('tab', 'branding');
        $this->redirect('/profile');
    }

    public function removeLogo(): void
    {
        CSRF::check();
        if (!Auth::hasRole('admin')) { http_response_code(403); exit; }

        $setting = new Setting();
        $oldLogo = $setting->get('site_logo', '');
        if ($oldLogo && file_exists(BASE_PATH . '/' . $oldLogo)) {
            unlink(BASE_PATH . '/' . $oldLogo);
        }
        $setting->set('site_logo', null);

        Logger::log('logo_remove', Auth::id(), 'Site logo removed');
        Session::flash('success', __('site_logo_removed'));
        Session::flash('tab', 'branding');
        $this->redirect('/profile');
    }

    // -------------------------------------------------------------------------
    // Save preferences (lang + dark mode)
    // -------------------------------------------------------------------------

    public function preferences(): void
    {
        CSRF::check();

        $userId   = Auth::id();
        $lang     = trim($_POST['language'] ?? 'en');
        $darkMode = !empty($_POST['dark_mode']) ? 1 : 0;

        if (!in_array($lang, ['en', 'ms'], true)) {
            $lang = 'en';
        }

        (new User())->update($userId, ['language' => $lang, 'dark_mode' => $darkMode]);

        Session::set('lang', $lang);
        Session::set('dark_mode', (bool)$darkMode);

        $user             = Session::get('user');
        $user['language'] = $lang;
        $user['dark_mode'] = $darkMode;
        Session::set('user', $user);

        Logger::log('preferences_update', $userId, 'Preferences updated');
        Session::flash('success', __('preferences_saved'));
        Session::flash('tab', 'preferences');
        $this->redirect('/profile');
    }
}
