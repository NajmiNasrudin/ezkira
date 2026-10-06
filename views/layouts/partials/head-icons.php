<?php
// Error pages can render from scripts that never loaded app/helpers.php
$iconUrl = fn(string $p): string => function_exists('asset_url')
    ? asset_url($p)
    : (defined('BASE_URI') ? BASE_URI : '') . '/' . $p;
?>
    <link rel="icon" type="image/svg+xml" href="<?= $iconUrl('assets/img/logo-mark.svg') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $iconUrl('assets/img/icons/favicon-32.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $iconUrl('assets/img/icons/apple-touch-icon.png') ?>">
    <link rel="manifest" href="<?= $iconUrl('assets/site.webmanifest') ?>">
    <meta name="apple-mobile-web-app-title" content="ezkira">
    <meta name="theme-color" content="#163020">
