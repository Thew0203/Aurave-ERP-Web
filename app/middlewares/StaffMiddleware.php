<?php
namespace App\Middlewares;

class StaffMiddleware
{
    public function handle(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $role = $_SESSION['role'] ?? '';
        if (!in_array($role, ['super_admin', 'admin', 'staff'], true)) {
            http_response_code(403);
            echo 'Access denied.';
            exit;
        }
        return true;
    }
}
