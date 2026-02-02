<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Customer;
use App\Models\Company;

class ProfileController extends Controller
{
    private function requireLogin(): ?int
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        if (!$userId) {
            $this->redirect($this->baseUrl() . '/auth/login');
            return null;
        }
        return $userId;
    }

    public function index(): void
    {
        $userId = $this->requireLogin();
        if ($userId === null) return;

        $userModel = new User();
        $user = $userModel->findById($userId);
        if (!$user) {
            $this->redirect($this->baseUrl());
            return;
        }

        $role = $user['role'] ?? '';
        $customer = null;
        $company = null;

        if ($role === 'customer') {
            $customerModel = new Customer();
            $customers = $customerModel->getAllByUserId($userId);
            $customer = $customers[0] ?? null;
            if (!$customer && !empty($customers)) {
                $customer = $customers[0];
            }
            if (!$customer) {
                $customer = ['name' => $user['name'], 'email' => $user['email'], 'phone' => $user['phone'], 'address' => ''];
            }
        } elseif (in_array($role, ['admin', 'staff'], true) && !empty($user['company_id'])) {
            $companyModel = new Company();
            $company = $companyModel->find((int) $user['company_id']);
        }

        $this->view('profile.index', [
            'pageTitle' => 'My Profile',
            'user' => $user,
            'customer' => $customer,
            'company' => $company,
        ]);
    }

    public function update(): void
    {
        $userId = $this->requireLogin();
        if ($userId === null) return;

        $userModel = new User();
        $user = $userModel->findById($userId);
        if (!$user) {
            $this->redirect($this->baseUrl());
            return;
        }

        $role = $user['role'] ?? '';
        $name = trim((string) $this->input('name'));
        $email = trim((string) $this->input('email'));
        $phone = trim((string) $this->input('phone'));
        $address = trim((string) $this->input('address'));

        if ($name === '') {
            $this->redirectWithError($role, 'Name is required.');
            return;
        }

        if ($email === '') {
            $this->redirectWithError($role, 'Email is required.');
            return;
        }

        if ($userModel->emailExistsExcept($email, $userId)) {
            $this->redirectWithError($role, 'Email is already used by another account.');
            return;
        }

        $userModel->update($userId, ['name' => $name, 'email' => $email, 'phone' => $phone]);

        if ($role === 'customer') {
            $customerModel = new Customer();
            $customerModel->updateAllByUserId($userId, ['name' => $name, 'email' => $email, 'phone' => $phone, 'address' => $address]);
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['profile_success'] = 'Profile updated successfully.';

        $this->redirect($this->baseUrl() . '/profile');
    }

    public function changePassword(): void
    {
        $userId = $this->requireLogin();
        if ($userId === null) return;

        $current = (string) $this->input('current_password');
        $newPass = (string) $this->input('new_password');
        $confirm = (string) $this->input('confirm_password');

        $userModel = new User();
        $user = $userModel->findById($userId);
        if (!$user) {
            $this->redirect($this->baseUrl());
            return;
        }

        if (!$userModel->verifyPassword($current, $user['password'])) {
            $this->redirectWithError($user['role'] ?? '', 'Current password is incorrect.');
            return;
        }

        if (strlen($newPass) < 6) {
            $this->redirectWithError($user['role'] ?? '', 'New password must be at least 6 characters.');
            return;
        }

        if ($newPass !== $confirm) {
            $this->redirectWithError($user['role'] ?? '', 'New passwords do not match.');
            return;
        }

        $hash = password_hash($newPass, PASSWORD_DEFAULT);
        $userModel->query("UPDATE users SET password = ? WHERE id = ?", [$hash, $userId]);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['profile_success'] = 'Password changed successfully.';

        $this->redirect($this->baseUrl() . '/profile');
    }

    private function redirectWithError(string $role, string $msg): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['profile_error'] = $msg;
        $this->redirect($this->baseUrl() . '/profile');
    }
}
