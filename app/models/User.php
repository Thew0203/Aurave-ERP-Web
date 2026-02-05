<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email, ?int $companyId = null): ?array
    {
        $sql = "SELECT * FROM users WHERE email = ? AND (company_id <=> ?) LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email, $companyId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Find user by email globally (any company or super_admin). Used for login. */
    public function findByEmailGlobal(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Check if email is already used by any user (enforces one account per email for SaaS). */
    public function emailExists(string $email): bool
    {
        return $this->findByEmailGlobal($email) !== null;
    }

    /** Check if email is used by another user (exclude given user id). */
    public function emailExistsExcept(string $email, int $excludeUserId): bool
    {
        $u = $this->fetchOne("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $excludeUserId]);
        return $u !== null;
    }

    /** Find user by ID (users table; no tenant filter). */
    public function findById(int $id): ?array
    {
        return $this->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
    }

    public function createUser(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->create($data);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function updateLastLogin(int $userId): void
    {
        $stmt = $this->db->prepare("UPDATE users SET last_login_at = NOW() WHERE id = ?");
        $stmt->execute([$userId]);
    }

    public function getByCompany(int $companyId): array
    {
        return $this->fetchAll("SELECT * FROM users WHERE company_id = ? ORDER BY name", [$companyId]);
    }

    /** Super Admin: counts by role (all users across system) */
    public function getCountsGlobal(): array
    {
        $row = $this->fetchOne("SELECT
            COUNT(*) AS total,
            SUM(role = 'super_admin') AS super_admin,
            SUM(role = 'admin') AS admin,
            SUM(role = 'staff') AS staff,
            SUM(role = 'customer') AS customer
            FROM users WHERE is_active = 1");
        return [
            'total' => (int) ($row['total'] ?? 0),
            'super_admin' => (int) ($row['super_admin'] ?? 0),
            'admin' => (int) ($row['admin'] ?? 0),
            'staff' => (int) ($row['staff'] ?? 0),
            'customer' => (int) ($row['customer'] ?? 0),
        ];
    }

    /** Super Admin: recent registered users with company name */
    public function getRecentGlobal(int $limit = 15): array
    {
        return $this->fetchAll(
            "SELECT u.id, u.email, u.name, u.role, u.created_at, co.name AS company_name
             FROM users u LEFT JOIN companies co ON u.company_id = co.id
             WHERE u.id > 1
             ORDER BY u.created_at DESC LIMIT " . (int) $limit
        );
    }
}
