<?php
namespace App\Models;

use App\Core\Model;

class BomItem extends Model
{
    protected string $table = 'bom_items';

    public function __construct()
    {
        parent::__construct();
        $this->tenantId = null;
    }

    public function getByBom(int $bomHeaderId): array
    {
        return $this->fetchAll("SELECT * FROM bom_items WHERE bom_header_id = ?", [$bomHeaderId]);
    }

    public function deleteByBom(int $bomHeaderId): void
    {
        $this->query("DELETE FROM bom_items WHERE bom_header_id = ?", [$bomHeaderId]);
    }
}
