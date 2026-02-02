<?php
namespace App\Models;

use App\Core\Model;

class BomHeader extends Model
{
    protected string $table = 'bom_headers';

    public function getWithProduct(): array
    {
        return $this->fetchAll(
            "SELECT b.*, p.name AS product_name, p.sku FROM bom_headers b JOIN products p ON b.product_id = p.id WHERE b." . $this->tenantWhere() . " ORDER BY b.name"
        );
    }

    public function getWithItems(int $id): ?array
    {
        $bom = $this->find($id);
        if (!$bom) return null;
        $bom['items'] = $this->fetchAll(
            "SELECT bi.*, p.name AS product_name, p.sku FROM bom_items bi JOIN products p ON bi.product_id = p.id WHERE bi.bom_header_id = ?",
            [$id]
        );
        return $bom;
    }
}
