<?php
namespace App\Models;

use App\Core\Model;
use App\Models\Product;

class ProductionOrder extends Model
{
    protected string $table = 'production_orders';

    public function getList(): array
    {
        return $this->fetchAll(
            "SELECT po.*, p.name AS product_name, b.name AS bom_name FROM production_orders po 
             JOIN products p ON po.product_id = p.id 
             JOIN bom_headers b ON po.bom_header_id = b.id 
             WHERE po." . $this->tenantWhere() . " ORDER BY po.created_at DESC"
        );
    }

    public function getNextOrderNumber(): string
    {
        $row = $this->fetchOne("SELECT MAX(CAST(SUBSTRING(order_number, 5) AS UNSIGNED)) AS n FROM production_orders WHERE " . $this->tenantWhere());
        $n = (int) ($row['n'] ?? 0) + 1;
        return 'PO-' . str_pad((string) $n, 5, '0', STR_PAD_LEFT);
    }

    public function getWithDetails(int $id): ?array
    {
        $order = $this->find($id);
        if (!$order) return null;
        $order['product_name'] = (new Product())->find($order['product_id'])['name'] ?? '';
        $order['consumption'] = $this->fetchAll("SELECT pc.*, p.name AS product_name FROM production_order_consumption pc JOIN products p ON pc.product_id = p.id WHERE pc.production_order_id = ?", [$id]);
        return $order;
    }
}
