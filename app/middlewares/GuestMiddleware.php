<?php
namespace App\Middlewares;

class GuestMiddleware
{
    public function handle(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!empty($_SESSION['user_id'])) {
            $role = $_SESSION['role'] ?? 'customer';
            $config = require dirname(__DIR__) . '/config/app.php';
            $base = $config['url'];
            if ($role === 'customer') {
                header('Location: ' . $base . '/store');
            } else {
                header('Location: ' . $base . '/dashboard');
            }
            exit;
        }
        return true;
    }
}
