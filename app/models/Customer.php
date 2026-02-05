<?php
namespace App\Models;

use App\Core\Model;

class Customer extends Model
{
    protected string $table = 'customers';

    public function getByCompany(): array
    {
        return $this->all('name');
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->fetchOne("SELECT * FROM customers WHERE user_id = ? AND " . $this->tenantWhere(), [$userId]);
    }

    /** Find customer by ID from any company (for order display) */
    public function findGlobal(int $id): ?array
    {
        return $this->fetchOne("SELECT * FROM customers WHERE id = ?", [$id]);
    }

    /** Get all customer records for a user (across companies - one per vendor they ordered from) */
    public function getAllByUserId(int $userId): array
    {
        return $this->fetchAll("SELECT * FROM customers WHERE user_id = ? ORDER BY company_id", [$userId]);
    }

    /** Update all customer records for a user (keep in sync with user profile) */
    public function updateAllByUserId(int $userId, array $data): void
    {
        $allowed = ['name', 'email', 'phone', 'address'];
        $sets = [];
        $params = [];
        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) {
                $sets[] = "`$col` = ?";
                $params[] = $data[$col];
            }
        }
        if (empty($sets)) return;
        $params[] = $userId;
        $this->query("UPDATE customers SET " . implode(', ', $sets) . " WHERE user_id = ?", $params);
    }

    /** Super Admin: total customer count across all companies */
    public function getCountGlobal(): int
    {
        $row = $this->fetchOne("SELECT COUNT(*) AS n FROM customers");
        return (int) ($row['n'] ?? 0);
    }
}
