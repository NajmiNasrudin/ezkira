<?php

namespace App\Core;

class Lang
{
    private static array $strings = [];
    private static string $current = 'en';

    public static function load(): void
    {
        self::$current = Session::get('lang', APP_LANG);

        // Validate — only allow known languages
        if (!in_array(self::$current, ['en', 'ms'], true)) {
            self::$current = 'en';
        }

        $file = BASE_PATH . '/lang/' . self::$current . '.php';
        if (!file_exists($file)) {
            $file = BASE_PATH . '/lang/en.php';
            self::$current = 'en';
        }

        self::$strings = require $file;
    }

    /**
     * Get a translated string.
     * Supports :placeholder replacements: __('welcome', ['name' => 'Ali'])
     */
    public static function get(string $key, array $replace = []): string
    {
        $str = self::$strings[$key] ?? $key;

        foreach ($replace as $k => $v) {
            $str = str_replace(':' . $k, htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'), $str);
        }

        return $str;
    }

    public static function current(): string
    {
        return self::$current;
    }

    public static function set(string $lang): void
    {
        if (!in_array($lang, ['en', 'ms'], true)) {
            return;
        }
        Session::set('lang', $lang);
        self::$current = $lang;
        self::load();
    }
}

