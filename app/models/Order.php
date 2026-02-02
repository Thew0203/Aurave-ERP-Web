<?php
namespace App\Models;

use App\Core\Model;

class Order extends Model
{
    protected string $table = 'orders';

    public function getList(): array
    {
        return $this->fetchAll(
            "SELECT o.*, c.name AS customer_name FROM orders o LEFT JOIN customers c ON o.customer_id = c.id WHERE o." . $this->tenantWhere() . " ORDER BY o.created_at DESC"
        );
    }

    public function getNextOrderNumber(): string
    {
        $row = $this->fetchOne("SELECT MAX(CAST(SUBSTRING(order_number, 5) AS UNSIGNED)) AS n FROM orders WHERE " . $this->tenantWhere());
        $n = (int) ($row['n'] ?? 0) + 1;
        return 'ORD' . str_pad((string) $n, 6, '0', STR_PAD_LEFT);
    }

    public function getWithItems(int $id): ?array
    {
        $order = $this->find($id);
        if (!$order) return null;
        $order['items'] = $this->fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$id]);
        $cust = $order['customer_id'] ? (new Customer())->find($order['customer_id']) : null;
        $order['customer_name'] = $cust ? ($cust['name'] ?? 'Guest') : 'Guest';
        $order['customer_email'] = $cust ? ($cust['email'] ?? '') : '';
        $order['history'] = $this->fetchAll("SELECT * FROM order_status_history WHERE order_id = ? ORDER BY created_at DESC", [$id]);
        return $order;
    }

    public function getByUser(int $userId): array
    {
        return $this->fetchAll("SELECT * FROM orders WHERE user_id = ? AND " . $this->tenantWhere() . " ORDER BY created_at DESC", [$userId]);
    }

    /** Get all orders placed by user across ALL companies (for customer's My Orders page) */
    public function getByUserGlobal(int $userId): array
    {
        return $this->fetchAll(
            "SELECT o.*, c.name AS customer_name, co.name AS company_name 
             FROM orders o 
             LEFT JOIN customers c ON o.customer_id = c.id 
             LEFT JOIN companies co ON o.company_id = co.id 
             WHERE o.user_id = ? 
             ORDER BY o.created_at DESC",
            [$userId]
        );
    }

    /** Get order with items from any company (for customer tracking) */
    public function getWithItemsGlobal(int $id): ?array
    {
        $order = $this->fetchOne("SELECT o.*, co.name AS company_name FROM orders o LEFT JOIN companies co ON o.company_id = co.id WHERE o.id = ?", [$id]);
        if (!$order) return null;
        $order['items'] = $this->fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$id]);
        $cust = $order['customer_id'] ? (new Customer())->findGlobal($order['customer_id']) : null;
        $order['customer_name'] = $cust ? ($cust['name'] ?? 'Guest') : 'Guest';
        $order['customer_email'] = $cust ? ($cust['email'] ?? '') : '';
        $order['history'] = $this->fetchAll("SELECT * FROM order_status_history WHERE order_id = ? ORDER BY created_at DESC", [$id]);
        return $order;
    }

    public function updateStatus(int $orderId, string $status, ?string $notes = null, ?int $userId = null): void
    {
        $this->update($orderId, ['current_status' => $status]);
        $this->query(
            "INSERT INTO order_status_history (order_id, status, notes, created_by) VALUES (?, ?, ?, ?)",
            [$orderId, $status, $notes, $userId]
        );
    }
}
