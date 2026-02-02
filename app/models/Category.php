<?php
namespace App\Models;

use App\Core\Model;

class Category extends Model
{
    protected string $table = 'categories';

    public function findByName(int $companyId, string $name): ?array
    {
        $name = trim($name);
        if ($name === '') {
            return null;
        }
        $this->setTenantId($companyId);
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE company_id = ? AND name = ? LIMIT 1");
        $stmt->execute([$companyId, $name]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Resolve category by name; create if not found. Returns category id or null if name empty. */
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
        $base = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($name)));
        $slug = $base ?: 'cat-' . uniqid();
        $stmt = $this->db->prepare("SELECT id FROM categories WHERE company_id = ? AND slug = ? LIMIT 1");
        $stmt->execute([$companyId, $slug]);
        if ($stmt->fetch()) {
            $slug = $base . '-' . substr(uniqid(), -6);
        }
        $this->setTenantId($companyId);
        return $this->create([
            'company_id' => $companyId,
            'name' => $name,
            'slug' => $slug,
        ]);
    }
}
