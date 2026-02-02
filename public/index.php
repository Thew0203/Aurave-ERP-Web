<?php
declare(strict_types=1);

if (file_exists(dirname(__DIR__) . '/.env')) {
    $lines = file(dirname(__DIR__) . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (preg_match('/^([^=]+)=(.*)$/', $line, $m)) {
            putenv(trim($m[1]) . '=' . trim($m[2], " \t\"'"));
        }
    }
}

if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    require dirname(__DIR__) . '/vendor/autoload.php';
} else {
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $base = dirname(__DIR__) . '/app/';
        if (strncmp($prefix, $class, strlen($prefix)) !== 0) return;
        $rel = str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        $file = $base . $rel;
        if (file_exists($file)) require $file;
    });
}

$config = require dirname(__DIR__) . '/app/config/app.php';
$isProduction = ($config['env'] ?? 'production') === 'production' && empty($config['debug']);
if ($isProduction) {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL);
}
if (!empty($config['timezone'])) {
    date_default_timezone_set($config['timezone']);
}
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', (string)($config['session_lifetime'] ?? 7200));
    session_start();
}

$router = require dirname(__DIR__) . '/app/routes.php';
$router->dispatch();
