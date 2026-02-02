<?php
namespace App\Models;

use App\Core\Model;

class Company extends Model
{
    protected string $table = 'companies';

    public function __construct()
    {
        parent::__construct();
        $this->tenantId = null;
    }

    public function all(string $orderBy = 'name', string $dir = 'ASC'): array
    {
        $orderBy = preg_replace('/[^a-z0-9_]/i', '', $orderBy);
        $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';
        return $this->fetchAll("SELECT * FROM companies ORDER BY {$orderBy} {$dir}");
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->fetchOne("SELECT * FROM companies WHERE slug = ?", [$slug]);
    }
}
