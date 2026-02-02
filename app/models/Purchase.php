<?php
namespace App\Models;

use App\Core\Model;

class Purchase extends Model
{
    protected string $table = 'purchases';

    public function getList(): array
    {
        return $this->fetchAll(
            "SELECT pu.*, s.name AS supplier_name FROM purchases pu JOIN suppliers s ON pu.supplier_id = s.id WHERE pu." . $this->tenantWhere() . " ORDER BY pu.purchase_date DESC, pu.id DESC"
        );
    }

    public function getNextInvoiceNumber(): string
    {
        $year = date('Y');
        $row = $this->fetchOne("SELECT MAX(CAST(SUBSTRING(invoice_number, 8) AS UNSIGNED)) AS n FROM purchases WHERE " . $this->tenantWhere() . " AND invoice_number LIKE ?", ['PUR-' . $year . '-%']);
        $n = (int) ($row['n'] ?? 0) + 1;
        return 'PUR-' . $year . '-' . str_pad((string) $n, 5, '0', STR_PAD_LEFT);
    }

    public function getWithItems(int $id): ?array
    {
        $purchase = $this->find($id);
        if (!$purchase) return null;
        $purchase['items'] = $this->fetchAll("SELECT pi.*, p.name AS product_name, p.sku FROM purchase_items pi JOIN products p ON pi.product_id = p.id WHERE pi.purchase_id = ?", [$id]);
        $purchase['supplier_name'] = (new Supplier())->find($purchase['supplier_id'])['name'] ?? '';
        return $purchase;
    }
}
