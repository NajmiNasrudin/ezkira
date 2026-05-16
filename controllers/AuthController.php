<?php

namespace Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\CSRF;
use App\Core\Lang;
use App\Core\Logger;
use App\Core\Mailer;
use App\Core\Session;
use Models\PasswordReset;
use Models\SessionToken;
use Models\User;

class AuthController extends Controller
{
    // -------------------------------------------------------------------------
    // Login
    // -------------------------------------------------------------------------

    public function loginForm(): void
    {
        $this->view('auth/login', [], 'auth', __('login'));
    }

    public function login(): void
    {
        CSRF::check();

        $email    = strtolower(trim(filter_input(INPUT_POST, 'email',    FILTER_SANITIZE_EMAIL) ?? ''));
        $password = trim($_POST['password'] ?? '');
        $remember = !empty($_POST['remember']);

        $errors = $this->validate(['email' => $email, 'password' => $password], [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($errors) {
            Session::flashInput($_POST);
            Session::flash('errors', $errors);
            $this->redirect('/login');
        }

        $userModel = new User();
        $user      = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            Session::flashInput($_POST);
            Session::flash('error', __('invalid_credentials'));
            $this->redirect('/login');
        }

        Auth::login($user);

        if ($remember) {
            $this->setRememberMeCookie((int)$user['id']);
        }

        Logger::log('login', (int)$user['id'], 'User logged in');
        Session::clearOldInput();
        Session::flash('success', __('login_success'));
        $this->redirect('/dashboard');
    }

    // -------------------------------------------------------------------------
    // Register
    // -------------------------------------------------------------------------

    public function registerForm(): void
    {
        $this->view('auth/register', ['businessTypes' => User::BUSINESS_TYPES], 'auth', __('register'));
    }

    public function register(): void
    {
        CSRF::check();

        $name         = trim($_POST['name']     ?? '');
        $email        = strtolower(trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? ''));
        $password     = $_POST['password']      ?? '';
        $confirm      = $_POST['password_confirmation'] ?? '';
        $whatsapp     = trim($_POST['whatsapp'] ?? '');
        $businessType = trim($_POST['business_type'] ?? '');
        $businessOther = trim($_POST['business_type_other'] ?? '');

        // If "other" is selected, use the custom value
        if ($businessType === 'other' && $businessOther !== '') {
            $businessType = 'other';
        }

        // Normalise whatsapp: ensure it starts with +60
        if ($whatsapp !== '' && !str_starts_with($whatsapp, '+')) {
            $whatsapp = '+60' . ltrim($whatsapp, '0');
        }

        $errors = $this->validate(
            [
                'name'                  => $name,
                'email'                 => $email,
                'password'              => $password,
                'password_confirmation' => $confirm,
                'whatsapp'              => $whatsapp,
            ],
            [
                'name'     => 'required|min:2|max:100',
                'email'    => 'required|email|max:255',
                'password' => 'required|min:8|confirmed',
                'whatsapp' => 'required|regex:/^\+60[0-9]{8,11}$/',
            ]
        );

        // Check email uniqueness
        if (empty($errors['email'])) {
            $userModel = new User();
            if ($userModel->emailExists($email)) {
                $errors['email'] = __('email_taken');
            }
        }

        if ($errors) {
            Session::flashInput($_POST);
            Session::flash('errors', $errors);
            $this->redirect('/register');
        }

        $userModel = $userModel ?? new User();

        try {
            $id = $userModel->create([
                'name'            => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                'email'           => $email,
                'password'        => password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]),
                'whatsapp_number' => $whatsapp,
                'role'            => 'client',
                'language'        => 'en',
                'dark_mode'       => 0,
                'business_type'   => $businessType !== '' ? htmlspecialchars($businessType, ENT_QUOTES, 'UTF-8') : null,
                'business_type_other' => ($businessType === 'other' && $businessOther !== '')
                                            ? htmlspecialchars($businessOther, ENT_QUOTES, 'UTF-8')
                                            : null,
            ]);
        } catch (\Throwable $e) {
            error_log('[Register] Create failed: ' . $e->getMessage());
            Session::flash('error', 'Ralat teknikal semasa mendaftar. Sila cuba lagi.');
            $this->redirect('/register');
        }

        $newUser = $userModel->findById($id ?? 0);

        if (!$newUser) {
            error_log('[Register] findById returned null for id=' . ($id ?? 0));
            Session::flash('error', 'Akaun berjaya dibuat tetapi gagal log masuk. Sila log masuk secara manual.');
            $this->redirect('/login');
        }

        Auth::login($newUser);

        Logger::log('register', $id, 'New user registered');
        Session::clearOldInput();
        Session::flash('success', __('register_success'));
        $this->redirect('/dashboard');
    }

    // -------------------------------------------------------------------------
    // Logout
    // -------------------------------------------------------------------------

    public function logout(): void
    {
        CSRF::check();

        $userId = Auth::id();
        $this->clearRememberMeCookie($userId);

        Auth::logout();
        Logger::log('logout', $userId, 'User logged out');

        Session::flash('success', __('logout_success'));
        $this->redirect('/login');
    }

    // -------------------------------------------------------------------------
    // Language Switch
    // -------------------------------------------------------------------------

    public function switchLang(): void
    {
        CSRF::check();

        $lang = trim($_POST['lang'] ?? '');
        if (!in_array($lang, ['en', 'ms'], true)) {
            $this->redirect('/dashboard');
        }

        Lang::set($lang);

        // Persist to DB if logged in
        if (Auth::check()) {
            (new User())->update(Auth::id(), ['language' => $lang]);
            $user             = Session::get('user');
            $user['language'] = $lang;
            Session::set('user', $user);
        }

        // Redirect back to referrer
        $ref = $_SERVER['HTTP_REFERER'] ?? '/dashboard';
        $this->redirect($ref);
    }

    // -------------------------------------------------------------------------
    // Dark Mode Toggle (AJAX)
    // -------------------------------------------------------------------------

    public function toggleTheme(): void
    {
        CSRF::check();

        $current  = Session::get('dark_mode', false);
        $newValue = !$current;

        Session::set('dark_mode', $newValue);

        if (Auth::check()) {
            (new User())->update(Auth::id(), ['dark_mode' => (int)$newValue]);
            $user              = Session::get('user');
            $user['dark_mode'] = (int)$newValue;
            Session::set('user', $user);
        }

        $this->json(['dark_mode' => $newValue]);
    }

    // -------------------------------------------------------------------------
    // Forgot Password
    // -------------------------------------------------------------------------

    public function forgotPasswordForm(): void
    {
        $this->view('auth/forgot_password', [], 'auth', __('forgot_password'));
    }

    public function forgotPassword(): void
    {
        CSRF::check();

        $email = strtolower(trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? ''));

        // Always show the same message to prevent email enumeration
        if ($email !== '') {
            $userModel = new User();
            $user      = $userModel->findByEmail($email);

            if ($user) {
                $token = bin2hex(random_bytes(32));
                (new PasswordReset())->create($email, $token);

                $resetUrl = (defined('APP_URL') ? APP_URL : '') . (defined('BASE_URI') ? BASE_URI : '') . '/reset-password?token=' . urlencode($token);

                $appName  = defined('APP_NAME') ? APP_NAME : 'EZKIRA';
                $subject  = $appName . ' — ' . __('reset_password');
                $body     = '
                    <div style="font-family:Arial,sans-serif;max-width:480px;margin:0 auto;padding:24px;">
                        <h2 style="color:#3a7049;margin-bottom:16px;">' . htmlspecialchars($appName, ENT_QUOTES) . '</h2>
                        <p style="color:#374151;margin-bottom:16px;">Hi ' . htmlspecialchars($user['name'], ENT_QUOTES) . ',</p>
                        <p style="color:#374151;margin-bottom:24px;">' . __('reset_password_hint') . '</p>
                        <a href="' . htmlspecialchars($resetUrl, ENT_QUOTES) . '"
                           style="display:inline-block;background:#3a7049;color:#ffffff;padding:12px 24px;
                                  border-radius:8px;text-decoration:none;font-weight:600;">
                            ' . __('reset_password_btn') . '
                        </a>
                        <p style="color:#6b7280;font-size:12px;margin-top:24px;">
                            Link ini sah selama 1 jam. Jika anda tidak meminta reset, abaikan emel ini.
                        </p>
                    </div>';

                Mailer::send($email, $subject, $body);
            }
        }

        Session::flash('success', __('reset_email_sent'));
        $this->redirect('/forgot-password');
    }

    // -------------------------------------------------------------------------
    // Reset Password
    // -------------------------------------------------------------------------

    public function resetPasswordForm(): void
    {
        $token = trim($_GET['token'] ?? '');

        if ($token === '') {
            Session::flash('error', __('reset_token_invalid'));
            $this->redirect('/forgot-password');
        }

        $reset = (new PasswordReset())->findByToken($token);

        if (!$reset) {
            Session::flash('error', __('reset_token_invalid'));
            $this->redirect('/forgot-password');
        }

        $this->view('auth/reset_password', ['token' => $token], 'auth', __('reset_password'));
    }

    public function resetPassword(): void
    {
        CSRF::check();

        $token    = trim($_POST['token']    ?? '');
        $password = $_POST['password']      ?? '';
        $confirm  = $_POST['password_confirmation'] ?? '';

        if ($token === '') {
            Session::flash('error', __('reset_token_invalid'));
            $this->redirect('/forgot-password');
        }

        $resetModel = new PasswordReset();
        $reset      = $resetModel->findByToken($token);

        if (!$reset) {
            Session::flash('error', __('reset_token_invalid'));
            $this->redirect('/forgot-password');
        }

        // Validate password
        if (strlen($password) < 8) {
            Session::flash('error', __('password_min'));
            $this->redirect('/reset-password?token=' . urlencode($token));
        }

        if ($password !== $confirm) {
            Session::flash('error', __('password_mismatch'));
            $this->redirect('/reset-password?token=' . urlencode($token));
        }

        // Update password
        $userModel = new User();
        $user      = $userModel->findByEmail($reset['email']);

        if (!$user) {
            Session::flash('error', __('reset_token_invalid'));
            $this->redirect('/forgot-password');
        }

        $userModel->update((int)$user['id'], [
            'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
        ]);

        $resetModel->deleteByEmail($reset['email']);

        Logger::log('password_reset', (int)$user['id'], 'Password reset via email link');
        Session::flash('success', __('reset_success'));
        $this->redirect('/login');
    }

    // -------------------------------------------------------------------------
    // Remember-Me helpers
    // -------------------------------------------------------------------------

    private function setRememberMeCookie(int $userId): void
    {
        $token     = bin2hex(random_bytes(32));
        $hash      = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + REMEMBER_ME_DAYS * 86400);

        (new SessionToken())->create($userId, $hash, $expiresAt);

        setcookie(
            'remember_token',
            $token,
            [
                'expires'  => time() + REMEMBER_ME_DAYS * 86400,
                'path'     => '/',
                'domain'   => '',
                'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }

    private function clearRememberMeCookie(?int $userId): void
    {
        if ($userId) {
            (new SessionToken())->deleteByUserId($userId);
        }

        setcookie('remember_token', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}
