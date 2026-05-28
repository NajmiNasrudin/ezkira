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
            Session::flash('error', __('register_failed'));
            $this->redirect('/register');
        }

        $newUser = $userModel->findById($id ?? 0);

        if (!$newUser) {
            error_log('[Register] findById returned null for id=' . ($id ?? 0));
            Session::flash('error', 'Akaun berjaya dibuat tetapi gagal log masuk. Sila log masuk secara manual.');
            $this->redirect('/login');
        }

        Auth::login($newUser);

        // Send WA greeting (fails silently if not configured)
        sendWhatsAppGreeting($whatsapp, $name);

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
    // Google OAuth
    // -------------------------------------------------------------------------

    public function googleLogin(): void
    {
        if (!defined('GOOGLE_CLIENT_ID') || GOOGLE_CLIENT_ID === '') {
            Session::flash('error', __('google_not_configured'));
            $this->redirect('/login');
        }

        $state = bin2hex(random_bytes(16));
        Session::set('google_oauth_state', $state);

        $params = http_build_query([
            'client_id'     => GOOGLE_CLIENT_ID,
            'redirect_uri'  => $this->googleRedirectUri(),
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'state'         => $state,
            'access_type'   => 'online',
            'prompt'        => 'select_account',
        ]);

        $this->redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $params);
    }

    public function googleCallback(): void
    {
        $state = $_GET['state'] ?? '';
        $code  = $_GET['code']  ?? '';
        $error = $_GET['error'] ?? '';

        if ($error !== '' || $state === '' || $state !== Session::get('google_oauth_state')) {
            Session::set('google_oauth_state', null);
            Session::flash('error', __('google_login_failed'));
            $this->redirect('/login');
        }

        Session::set('google_oauth_state', null);

        // Exchange code for tokens
        $tokenData = $this->googlePost('https://oauth2.googleapis.com/token', [
            'code'          => $code,
            'client_id'     => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri'  => $this->googleRedirectUri(),
            'grant_type'    => 'authorization_code',
        ]);

        if (empty($tokenData['access_token'])) {
            error_log('[GoogleCallback] Token exchange failed: ' . json_encode($tokenData));
            Session::flash('error', __('google_login_failed'));
            $this->redirect('/login');
        }

        // Fetch user info from Google
        $userInfo = $this->googleGet('https://openidconnect.googleapis.com/v1/userinfo', $tokenData['access_token']);

        if (empty($userInfo['sub'])) {
            error_log('[GoogleCallback] UserInfo fetch failed: ' . json_encode($userInfo));
            Session::flash('error', __('google_login_failed'));
            $this->redirect('/login');
        }

        $googleId = (string)$userInfo['sub'];
        $email    = strtolower(trim($userInfo['email'] ?? ''));
        $name     = trim($userInfo['name'] ?? '');

        if ($email === '') {
            Session::flash('error', __('google_no_email'));
            $this->redirect('/login');
        }

        $userModel = new User();

        // Try to find existing user by google_id first, then by email
        $user = $userModel->findByGoogleId($googleId)
             ?? $userModel->findByEmail($email);

        if ($user) {
            // Link google_id if not yet set
            if (empty($user['google_id'])) {
                $userModel->update((int)$user['id'], ['google_id' => $googleId]);
                $user['google_id'] = $googleId;
            }
            Auth::login($user);
            Logger::log('login', (int)$user['id'], 'Google login');
            Session::clearOldInput();
            Session::flash('success', __('login_success'));
            $this->redirect('/dashboard');
        }

        // New user — store pending data and redirect to complete profile
        Session::set('google_pending', [
            'google_id' => $googleId,
            'email'     => $email,
            'name'      => $name,
        ]);

        $this->redirect('/auth/google/complete');
    }

    public function googleComplete(): void
    {
        $pending = Session::get('google_pending');
        if (!$pending) {
            $this->redirect('/register');
        }

        $this->view('auth/google_complete', [
            'pending'       => $pending,
            'businessTypes' => User::BUSINESS_TYPES,
        ], 'auth', __('create_account'));
    }

    public function googleCompleteSave(): void
    {
        CSRF::check();

        $pending = Session::get('google_pending');
        if (!$pending) {
            $this->redirect('/register');
        }

        $whatsapp      = trim($_POST['whatsapp']            ?? '');
        $businessType  = trim($_POST['business_type']       ?? '');
        $businessOther = trim($_POST['business_type_other'] ?? '');

        // Normalise WhatsApp: strip leading zeros/spaces, prepend +60
        if ($whatsapp !== '' && !str_starts_with($whatsapp, '+')) {
            $whatsapp = '+60' . ltrim($whatsapp, '0');
        }

        $errors = $this->validate(
            ['whatsapp' => $whatsapp],
            ['whatsapp' => 'required|regex:/^\+60[0-9]{8,11}$/']
        );

        if ($errors) {
            Session::flash('errors', $errors);
            $this->redirect('/auth/google/complete');
        }

        $userModel = new User();

        try {
            $id = $userModel->create([
                'name'            => htmlspecialchars($pending['name'], ENT_QUOTES, 'UTF-8'),
                'email'           => $pending['email'],
                // Unusable password — Google users authenticate via OAuth only
                'password'        => password_hash(bin2hex(random_bytes(32)), PASSWORD_BCRYPT, ['cost' => 10]),
                'whatsapp_number' => $whatsapp,
                'google_id'       => $pending['google_id'],
                'role'            => 'client',
                'language'        => 'en',
                'dark_mode'       => 0,
                'business_type'   => $businessType !== ''
                                        ? htmlspecialchars($businessType, ENT_QUOTES, 'UTF-8')
                                        : null,
                'business_type_other' => ($businessType === 'other' && $businessOther !== '')
                                            ? htmlspecialchars($businessOther, ENT_QUOTES, 'UTF-8')
                                            : null,
            ]);
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            error_log('[GoogleComplete] Create failed: ' . $msg);
            Session::flash('error', 'Ralat teknikal: ' . htmlspecialchars(substr($msg, 0, 300), ENT_QUOTES, 'UTF-8'));
            $this->redirect('/auth/google/complete');
        }

        $newUser = $userModel->findById($id ?? 0);

        if (!$newUser) {
            Session::set('google_pending', null);
            Session::flash('error', __('register_auto_login_failed'));
            $this->redirect('/login');
        }

        Session::set('google_pending', null);
        Auth::login($newUser);

        // Send WA greeting (fails silently if not configured)
        sendWhatsAppGreeting($whatsapp, $pending['name']);

        Logger::log('register', $id, 'New user registered via Google');
        Session::clearOldInput();
        Session::flash('success', __('register_success'));
        $this->redirect('/dashboard');
    }

    // -------------------------------------------------------------------------
    // Google OAuth — private helpers
    // -------------------------------------------------------------------------

    private function googleRedirectUri(): string
    {
        $base = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
        $sub  = defined('BASE_URI') ? BASE_URI : '';
        return $base . $sub . '/auth/google/callback';
    }

    private function googlePost(string $url, array $data): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode((string)($response ?: '{}'), true) ?? [];
    }

    private function googleGet(string $url, string $accessToken): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $accessToken],
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode((string)($response ?: '{}'), true) ?? [];
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
