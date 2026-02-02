<?php
namespace App\Models;

use App\Core\Model;

class Product extends Model
{
    protected string $table = 'products';

    public function allWithDetails(): array
    {
        $sql = "SELECT p.*, c.name AS category_name, s.name AS supplier_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN suppliers s ON p.supplier_id = s.id 
                WHERE p." . $this->tenantWhere() . " ORDER BY p.name";
        return $this->fetchAll($sql);
    }

    public function getSellable(): array
    {
        return $this->fetchAll("SELECT * FROM products WHERE " . $this->tenantWhere() . " AND is_active = 1 AND is_raw_material = 0 ORDER BY name");
    }

    /** All companies' sellable products (for public store) - includes company/vendor name */
    public function getSellableAll(): array
    {
        return $this->fetchAll("SELECT p.*, co.name AS company_name FROM products p LEFT JOIN companies co ON p.company_id = co.id WHERE p.is_active = 1 AND p.is_raw_material = 0 ORDER BY p.name");
    }

    /** Find product by id regardless of company (for store product page, cart, checkout) - includes company/vendor name */
    public function findByIdGlobal(int $id): ?array
    {
        return $this->fetchOne("SELECT p.*, co.name AS company_name FROM products p LEFT JOIN companies co ON p.company_id = co.id WHERE p.id = ?", [$id]);
    }

    /** Adjust quantity by product id (for checkout deduction from any company's product) */
    public function adjustQuantityById(int $productId, float $delta): bool
    {
        $stmt = $this->db->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
        return $stmt->execute([$delta, $productId]);
    }

    public function getRawMaterials(): array
    {
        return $this->fetchAll("SELECT * FROM products WHERE " . $this->tenantWhere() . " AND is_active = 1 AND is_raw_material = 1 ORDER BY name");
    }

    public function getFinishedForBom(): array
    {
        return $this->fetchAll("SELECT * FROM products WHERE " . $this->tenantWhere() . " AND is_active = 1 AND is_raw_material = 0 ORDER BY name");
    }

    public function adjustQuantity(int $productId, float $delta): bool
    {
        $stmt = $this->db->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ? AND " . $this->tenantColumn() . " = ?");
        return $stmt->execute([$delta, $productId, $this->tenantId]);
    }

    public function getValuation(): float
    {
        $row = $this->fetchOne("SELECT SUM(quantity * cost_price) AS total FROM products WHERE " . $this->tenantWhere());
        return (float) ($row['total'] ?? 0);
    }

    public function getLowStock(): array
    {
        return $this->fetchAll("SELECT * FROM products WHERE " . $this->tenantWhere() . " AND quantity <= low_stock_threshold AND is_active = 1 ORDER BY quantity ASC");
    }
}
