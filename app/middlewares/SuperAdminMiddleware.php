<?php
namespace App\Middlewares;

class SuperAdminMiddleware
{
    public function handle(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
            http_response_code(403);
            echo 'Access denied. Super Admin only.';
            exit;
        }
        return true;
    }
}
