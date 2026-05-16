<?php

/**
 * Global helper functions — NO namespace so they are callable from anywhere.
 */

/**
 * Translate a key using the loaded language strings.
 * Usage: __('welcome', ['name' => 'Ali'])
 */
function __(string $key, array $replace = []): string
{
    return \App\Core\Lang::get($key, $replace);
}
