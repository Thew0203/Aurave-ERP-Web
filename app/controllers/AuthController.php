<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Customer;
use App\Models\LoginEvent;
use App\Models\LoginVerificationToken;
use App\Services\SecurityMailer;

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

        $this->sendLoginSecurityEmail($user);

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

    private function sendLoginSecurityEmail(array $user): void
    {
        $config = require (defined('APP_PATH') ? APP_PATH : dirname(__DIR__)) . '/config/app.php';
        $isDebug = !empty($config['debug']);
        $userEmail = $user['email'] ?? '';

        try {
            if (!class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
                if ($isDebug) $_SESSION['mailer_debug'] = 'Login email skipped: PHPMailer not loaded.';
                return;
            }
            $loginEventModel = new LoginEvent();
            $tokenModel = new LoginVerificationToken();
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            $eventId = $loginEventModel->createEvent((int) $user['id'], $ip, $userAgent);
            // Link valid for 10 minutes (600 seconds) — after that, verify-login shows "This link has expired."
            $expiresAt = date('Y-m-d H:i:s', time() + 600);
            $tokens = [
                'yes' => bin2hex(random_bytes(32)),
                'no' => bin2hex(random_bytes(32)),
            ];
            foreach ($tokens as $action => $token) {
                $tokenModel->createToken($eventId, $action, $token, $expiresAt);
            }
            $loginEvent = $loginEventModel->findById($eventId);
            if ($loginEvent) {
                $sent = SecurityMailer::sendLoginAlert($user, $loginEvent, $tokens);
                if ($isDebug) {
                    $_SESSION['mailer_debug'] = $sent
                        ? 'Login email sent to ' . $userEmail . '. Check that inbox (and Spam).'
                        : 'Login email could not be sent to ' . $userEmail . '. Check PHP error log (SecurityMailer).';
                }
            } elseif ($isDebug) {
                $_SESSION['mailer_debug'] = 'Login event created but email not sent (event lookup failed).';
            }
        } catch (\Throwable $e) {
            error_log('SecurityMailer send failed: ' . $e->getMessage());
            if (strpos($e->getMessage(), 'login_events') !== false || strpos($e->getMessage(), 'doesn\'t exist') !== false) {
                error_log('SecurityMailer: Run database/migrations/security_mailer.sql to create login_events and login_verification_tokens tables.');
            }
            if ($isDebug) {
                $msg = $e->getMessage();
                if (strpos($msg, 'login_events') !== false || strpos($msg, 'doesn\'t exist') !== false) {
                    $_SESSION['mailer_debug'] = 'Login email failed: missing database tables. Import database/migrations/security_mailer.sql in phpMyAdmin, then try again.';
                } else {
                    $_SESSION['mailer_debug'] = 'Login email failed: ' . htmlspecialchars($msg);
                }
            }
        }
    }

    public function verifyLogin(): void
    {
        $token = trim((string) $this->input('token'));
        $action = trim((string) $this->input('action'));
        if ($token === '' || !in_array($action, ['yes', 'no'], true)) {
            $this->view('auth.verify_result', [
                'pageTitle' => 'Verification',
                'action' => 'yes',
                'message' => 'Invalid or expired link.',
            ]);
            return;
        }
        $tokenModel = new LoginVerificationToken();
        $row = $tokenModel->findByToken($token);
        if (!$row || $row['action'] !== $action) {
            $this->view('auth.verify_result', [
                'pageTitle' => 'Verification',
                'action' => 'yes',
                'message' => 'Invalid or expired link.',
            ]);
            return;
        }
        if ($row['clicked_at'] !== null) {
            $this->view('auth.verify_result', [
                'pageTitle' => 'Verification',
                'action' => $action,
                'message' => 'This link has already been used.',
            ]);
            return;
        }
        // Expired if past expires_at (set to 10 minutes when token was created)
        if (strtotime($row['expires_at']) < time()) {
            $this->view('auth.verify_result', [
                'pageTitle' => 'Verification',
                'action' => 'yes',
                'message' => 'This link has expired.',
            ]);
            return;
        }
        $tokenModel->markClicked((int) $row['id'], $_SERVER['REMOTE_ADDR'] ?? null);
        $this->view('auth.verify_result', [
            'pageTitle' => 'Verification',
            'action' => $action,
            'message' => $action === 'yes'
                ? 'Thank you. Your response has been recorded.'
                : 'We have recorded that this login was not recognized by you.',
        ]);
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
