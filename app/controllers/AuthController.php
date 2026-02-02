<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Customer;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        $this->view('auth.login', ['error' => null]);
    }

    public function login(): void
    {
        $email = trim((string) $this->input('email'));
        $password = (string) $this->input('password');
        if ($email === '' || $password === '') {
            $this->view('auth.login', ['error' => 'Email and password are required.']);
            return;
        }
        $userModel = new User();
        $user = $userModel->findByEmailGlobal($email);
        if (!$user || !$userModel->verifyPassword($password, $user['password'])) {
            $this->view('auth.login', ['error' => 'Invalid email or password.']);
            return;
        }
        if (empty($user['is_active'])) {
            $this->view('auth.login', ['error' => 'Account is disabled.']);
            return;
        }
        $userModel->updateLastLogin((int) $user['id']);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['company_id'] = $user['company_id'] ? (int) $user['company_id'] : null;
        $base = $this->baseUrl();
        if ($user['role'] === 'customer') {
            $this->redirect($base . '/store');
            return;
        }
        if ($user['role'] === 'super_admin') {
            $this->redirect($base . '/dashboard/super');
            return;
        }
        if ($user['role'] === 'admin') {
            $this->redirect($base . '/dashboard/admin');
            return;
        }
        $this->redirect($base . '/dashboard');
    }

    public function registerForm(): void
    {
        $this->view('auth.register', ['error' => null, 'success' => null]);
    }

    public function register(): void
    {
        $name = trim((string) $this->input('name'));
        $email = trim((string) $this->input('email'));
        $password = (string) $this->input('password');
        $companyName = trim((string) $this->input('company_name'));
        $phone = trim((string) $this->input('phone'));
        $address = trim((string) $this->input('address'));
        if ($name === '' || $email === '' || $password === '') {
            $this->view('auth.register', ['error' => 'Name, email and password are required.']);
            return;
        }
        $isCustomerOnly = false;
        if ($companyName === '') {
            $isCustomerOnly = true;
        } elseif (in_array(strtolower($companyName), ['n/a', 'na', 'no company', 'personal', 'guest', 'user'], true)) {
            $isCustomerOnly = true;
        }
        if (strlen($password) < 6) {
            $this->view('auth.register', ['error' => 'Password must be at least 6 characters.']);
            return;
        }
        $userModel = new User();
        if ($userModel->emailExists($email)) {
            $this->view('auth.register', ['error' => 'Email already registered.']);
            return;
        }
        if ($isCustomerOnly) {
            $companyId = 1;
            $userId = $userModel->createUser([
                'company_id' => $companyId,
                'email' => $email,
                'password' => $password,
                'name' => $name,
                'phone' => $phone,
                'role' => 'customer',
                'is_active' => 1,
            ]);
            $customerModel = new Customer();
            $customerModel->setTenantId($companyId);
            $customerModel->create([
                'company_id' => $companyId,
                'user_id' => $userId,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
            ]);
            $this->view('auth.register', ['error' => null, 'success' => 'Registration successful. You can now log in and shop as a customer.']);
            return;
        }
        $companyModel = new Company();
        $slug = $this->uniqueCompanySlug($companyName, $companyModel);
        $companyId = $companyModel->create([
            'name' => $companyName,
            'slug' => $slug,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'tax_id' => null,
            'is_active' => 1,
        ]);
        $userId = $userModel->createUser([
            'company_id' => $companyId,
            'email' => $email,
            'password' => $password,
            'name' => $name,
            'phone' => $phone,
            'role' => 'admin',
            'is_active' => 1,
        ]);
        $this->seedDefaultChartOfAccounts($companyId);
        $this->view('auth.register', ['error' => null, 'success' => 'Registration successful. You can now log in as company admin.']);
    }

    private function uniqueCompanySlug(string $companyName, Company $companyModel): string
    {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $companyName));
        $slug = trim($slug, '-') ?: 'company';
        $base = $slug;
        $n = 0;
        while ($companyModel->findBySlug($slug) !== null) {
            $n++;
            $slug = $base . '-' . $n;
        }
        return $slug;
    }

    private function seedDefaultChartOfAccounts(int $companyId): void
    {
        $defaults = [
            ['code' => '1000', 'name' => 'Cash', 'type' => 'asset'],
            ['code' => '1100', 'name' => 'Accounts Receivable', 'type' => 'asset'],
            ['code' => '1200', 'name' => 'Inventory', 'type' => 'asset'],
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'liability'],
            ['code' => '3000', 'name' => 'Equity', 'type' => 'equity'],
            ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'revenue'],
            ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'expense'],
            ['code' => '6000', 'name' => 'Operating Expenses', 'type' => 'expense'],
        ];
        $coa = new \App\Models\ChartOfAccount();
        foreach ($defaults as $row) {
            $coa->create([
                'company_id' => $companyId,
                'code' => $row['code'],
                'name' => $row['name'],
                'type' => $row['type'],
                'is_system' => 1,
            ]);
        }
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        $this->redirect($this->baseUrl());
    }
}
