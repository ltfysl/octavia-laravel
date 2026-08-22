<?php

/**
 * Router for PHP's built-in server used by the Playwright E2E suite.
 *
 * Forces the E2E environment (dedicated database, sync queue) BEFORE the
 * application boots. Dotenv is immutable, so these pre-set variables win
 * over .env — guaranteeing E2E never touches development data.
 */
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__.'/../public'.$_SERVER['REQUEST_URI'];

    if (is_file($file)) {
        return false;
    }
}

$force = [
    'DB_DATABASE' => 'octavia_e2e',
    'QUEUE_CONNECTION' => 'sync',
];

foreach ($force as $key => $value) {
    putenv("$key=$value");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

require __DIR__.'/../public/index.php';
