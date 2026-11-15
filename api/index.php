<?php

/**
 * Vercel Serverless Function Entry Point for Laravel
 *
 * In Vercel's serverless environment, the root filesystem is read-only
 * except for the `/tmp` directory. We ensure temporary storage directories
 * exist in `/tmp` before dispatching the request to Laravel's public index.
 */

$tmpDirs = [
    '/tmp/views',
    '/tmp/cache',
    '/tmp/sessions',
    '/tmp/logs',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

require __DIR__ . '/../public/index.php';
