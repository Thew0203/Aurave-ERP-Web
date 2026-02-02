<?php
namespace App\Models;

use App\Core\Model;

class Supplier extends Model
{
    protected string $table = 'suppliers';

    public function findByName(int $companyId, string $name): ?array
    {
        $name = trim($name);
        if ($name === '') {
            return null;
        }
        $stmt = $this->db->prepare("SELECT * FROM suppliers WHERE company_id = ? AND name = ? LIMIT 1");
        $stmt->execute([$companyId, $name]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Resolve supplier by name; create if not found. Returns supplier id or null if name empty. */
    public function findOrCreateByName(int $companyId, string $name): ?int
    {
        $name = trim($name);
        if ($name === '') {
            return null;
        }
        $existing = $this->findByName($companyId, $name);
        if ($existing) {
            return (int) $existing['id'];
        }
        $this->setTenantId($companyId);
        return $this->create([
            'company_id' => $companyId,
            'name' => $name,
            'is_active' => 1,
        ]);
    }
}
