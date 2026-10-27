<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
// Serve existing static files from public/ ourselves instead of `return false`,
// which relies on the server's document root being public/ (not guaranteed
// when started via `php artisan serve` on Windows).
$file = realpath(__DIR__.'/public'.$uri);

if ($uri !== '/' && $file !== false && is_file($file) && str_starts_with($file, realpath(__DIR__.'/public'))) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        return false;
    }

    $mimes = [
        'css' => 'text/css', 'js' => 'application/javascript', 'json' => 'application/json',
        'svg' => 'image/svg+xml', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
        'gif' => 'image/gif', 'ico' => 'image/x-icon', 'webp' => 'image/webp', 'txt' => 'text/plain',
        'woff' => 'font/woff', 'woff2' => 'font/woff2', 'ttf' => 'font/ttf',
    ];
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    header('Content-Type: '.($mimes[$ext] ?? mime_content_type($file) ?: 'application/octet-stream'));
    header('Content-Length: '.filesize($file));
    readfile($file);

    return true;
}

require_once __DIR__.'/public/index.php';
