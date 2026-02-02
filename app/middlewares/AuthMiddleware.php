<?php
namespace App\Middlewares;

class AuthMiddleware
{
    public function handle(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . $this->baseUrl() . '/auth/login');
            exit;
        }
        return true;
    }

    private function baseUrl(): string
    {
        $config = require dirname(__DIR__) . '/config/app.php';
        return $config['url'];
    }
}
