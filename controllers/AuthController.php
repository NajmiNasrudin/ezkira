<?php

namespace Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\CSRF;
use App\Core\Lang;
use App\Core\Logger;
use App\Core\Session;
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
        $this->view('auth/register', [], 'auth', __('register'));
    }

    public function register(): void
    {
        CSRF::check();

        $name      = trim($_POST['name']     ?? '');
        $email     = strtolower(trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? ''));
        $password  = $_POST['password']      ?? '';
        $confirm   = $_POST['password_confirmation'] ?? '';
        $whatsapp  = trim($_POST['whatsapp'] ?? '');

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
        $id = $userModel->create([
            'name'            => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            'email'           => $email,
            'password'        => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
            'whatsapp_number' => $whatsapp,
            'role'            => 'client',
            'language'        => 'en',
            'dark_mode'       => 0,
        ]);

        $newUser = $userModel->findById($id);
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
            $user             = Session::get('user');
            $user['dark_mode'] = (int)$newValue;
            Session::set('user', $user);
        }

        $this->json(['dark_mode' => $newValue]);
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
