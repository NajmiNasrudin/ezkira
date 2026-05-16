<?php

namespace App\Core;

class Auth
{
    public static function user(): ?array
    {
        return Session::get('user');
    }

    public static function id(): ?int
    {
        $user = Session::get('user');
        return $user ? (int)$user['id'] : null;
    }

    public static function check(): bool
    {
        return Session::has('user');
    }

    public static function role(): ?string
    {
        return Session::get('user')['role'] ?? null;
    }

    public static function hasRole(string ...$roles): bool
    {
        return in_array(self::role(), $roles, true);
    }

    /**
     * Log in a user: regenerate session, store user data, apply preferences.
     */
    public static function login(array $user): void
    {
        Session::regenerate();
        CSRF::regenerate();

        // Strip password before storing in session
        unset($user['password']);

        Session::set('user', $user);
        Session::set('dark_mode', (bool)($user['dark_mode'] ?? false));
        Session::set('lang', $user['language'] ?? APP_LANG);
    }

    /**
     * Log out: destroy session, start a fresh one.
     */
    public static function logout(): void
    {
        Session::destroy();
        Session::start();
        Session::regenerate();
    }

    /**
     * Refresh the in-session user data (e.g. after profile update).
     */
    public static function refreshUser(array $user): void
    {
        unset($user['password']);
        Session::set('user', $user);
        Session::set('dark_mode', (bool)($user['dark_mode'] ?? false));
        Session::set('lang', $user['language'] ?? APP_LANG);
    }
}
