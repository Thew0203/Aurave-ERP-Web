<?php
namespace App\Models;

use App\Core\Model;

class Sale extends Model
{
    protected string $table = 'sales';

    public function getList(): array
    {
        return $this->fetchAll(
            "SELECT s.*, c.name AS customer_name FROM sales s LEFT JOIN customers c ON s.customer_id = c.id WHERE s." . $this->tenantWhere() . " ORDER BY s.sale_date DESC, s.id DESC"
        );
    }

    public function getNextInvoiceNumber(): string
    {
        $year = date('Y');
        $row = $this->fetchOne("SELECT MAX(CAST(SUBSTRING(invoice_number, 8) AS UNSIGNED)) AS n FROM sales WHERE " . $this->tenantWhere() . " AND invoice_number LIKE ?", ['INV-' . $year . '-%']);
        $n = (int) ($row['n'] ?? 0) + 1;
        return 'INV-' . $year . '-' . str_pad((string) $n, 5, '0', STR_PAD_LEFT);
    }

    public function getWithItems(int $id): ?array
    {
        $sale = $this->find($id);
        if (!$sale) return null;
        $sale['items'] = $this->fetchAll("SELECT si.*, p.name AS product_name, p.sku FROM sale_items si JOIN products p ON si.product_id = p.id WHERE si.sale_id = ?", [$id]);
        $sale['customer_name'] = $sale['customer_id'] ? (new Customer())->find($sale['customer_id'])['name'] ?? 'Walk-in' : 'Walk-in';
        return $sale;
    }

    public function getTotalByMonth(int $year, int $month): float
    {
        $row = $this->fetchOne("SELECT COALESCE(SUM(total), 0) AS t FROM sales WHERE " . $this->tenantWhere() . " AND status IN ('confirmed','paid') AND YEAR(sale_date) = ? AND MONTH(sale_date) = ?", [$year, $month]);
        return (float) ($row['t'] ?? 0);
    }

    /** Super Admin: total sales count */
    public function getCountGlobal(): int
    {
        $row = $this->fetchOne("SELECT COUNT(*) AS n FROM sales");
        return (int) ($row['n'] ?? 0);
    }

    /** Super Admin: total sales this month across all companies */
    public function getTotalByMonthGlobal(int $year, int $month): float
    {
        $row = $this->fetchOne("SELECT COALESCE(SUM(total), 0) AS t FROM sales WHERE status IN ('confirmed','paid') AND YEAR(sale_date) = ? AND MONTH(sale_date) = ?", [$year, $month]);
        return (float) ($row['t'] ?? 0);
    }
}
