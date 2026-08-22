<?php

/**
 * Creates the dedicated E2E database if it does not exist yet.
 * Called by Playwright's global setup before the app server boots.
 */

require __DIR__.'/../vendor/autoload.php';

$host = getenv('DB_HOST') ?: '127.0.0.1';
$user = getenv('DB_USERNAME') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '12345678';
$name = getenv('DB_DATABASE') ?: 'octavia_e2e';

$m = new mysqli($host, $user, $pass);
$m->query("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
echo $m->error ? "FAILED: {$m->error}\n" : "E2E database '{$name}' ready.\n";
