<?php
namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!empty($_SESSION['user_id'])) {
            $role = $_SESSION['role'] ?? 'customer';
            $base = $this->baseUrl();
            if ($role === 'customer') {
                $this->redirect($base . '/store');
                return;
            }
            if ($role === 'super_admin') {
                $this->redirect($base . '/dashboard/super');
                return;
            }
            if (in_array($role, ['admin', 'staff'], true)) {
                $this->redirect($base . '/dashboard');
                return;
            }
            $this->redirect($base . '/dashboard');
            return;
        }
        $this->view('home.index', ['pageTitle' => 'Aruave – Electronics & IT ERP']);
    }
}
