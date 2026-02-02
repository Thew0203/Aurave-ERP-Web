<?php
namespace App\Middlewares;

class RoleMiddleware
{
    private array $allowedRoles;

    public function __construct(array $allowedRoles = [])
    {
        $this->allowedRoles = $allowedRoles;
    }

    public function handle(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $role = $_SESSION['role'] ?? null;
        if ($role === null || !in_array($role, $this->allowedRoles, true)) {
            http_response_code(403);
            echo 'Access denied.';
            exit;
        }
        return true;
    }
}
