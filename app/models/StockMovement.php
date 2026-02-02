<?php
namespace App\Models;

use App\Core\Model;

class StockMovement extends Model
{
    protected string $table = 'stock_movements';

    public function getByProduct(int $productId, int $limit = 50): array
    {
        return $this->fetchAll(
            "SELECT * FROM stock_movements WHERE company_id = ? AND product_id = ? ORDER BY created_at DESC LIMIT ?",
            [$this->tenantId, $productId, $limit]
        );
    }

    public function getRecent(int $limit = 100): array
    {
        return $this->fetchAll(
            "SELECT sm.*, p.name AS product_name, p.sku FROM stock_movements sm JOIN products p ON sm.product_id = p.id WHERE sm." . $this->tenantWhere() . " ORDER BY sm.created_at DESC LIMIT ?",
            [$limit]
        );
    }
}
