<?php

use Illuminate\Contracts\Console\Kernel;

/**
 * Creates the dedicated E2E database if it does not exist yet.
 * Called by Playwright's global setup before the app server boots.
 *
 * Boots the framework so credentials come from .env via config(),
 * instead of relying on shell environment inheritance.
 */

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$host = config('database.connections.mysql.host', '127.0.0.1');
$user = config('database.connections.mysql.username', 'root');
$pass = config('database.connections.mysql.password', '');
$name = env('DB_DATABASE_E2E', 'octavia_e2e');

$m = new mysqli($host, $user, $pass);
$m->query("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
echo $m->error ? "FAILED: {$m->error}\n" : "E2E database '{$name}' ready.\n";
