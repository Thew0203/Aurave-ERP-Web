<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    private function companyId(): int { return (int) $_SESSION['company_id']; }
    private function userId(): ?int { return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null; }

    private function order(): Order
    {
        $m = new Order();
        $m->setTenantId($this->companyId());
        return $m;
    }

    public function index(): void
    {
        $list = $this->order()->getList();
        $filter = $this->input('status');
        if ($filter !== null && $filter !== '') {
            $list = array_filter($list, fn($o) => $o['current_status'] === $filter);
        }
        $this->view('orders.index', ['pageTitle' => 'Order Management', 'orders' => array_values($list)]);
    }

    public function viewOrder(string $id): void
    {
        $order = $this->order()->getWithItems((int) $id);
        if (!$order) {
            $this->redirect($this->baseUrl() . '/orders');
            return;
        }
        $this->view('orders.view', ['pageTitle' => 'Order', 'order' => $order]);
    }

    public function updateStatus(string $id): void
    {
        $order = $this->order()->find((int) $id);
        if (!$order) {
            $this->redirect($this->baseUrl() . '/orders');
            return;
        }
        $status = $this->input('status');
        $notes = trim((string) $this->input('notes'));
        $valid = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'returned', 'cancelled'];
        if (!in_array($status, $valid, true)) {
            $this->redirect($this->baseUrl() . '/orders/view/' . $id);
            return;
        }
        $this->order()->updateStatus((int) $id, $status, $notes, $this->userId());
        $this->redirect($this->baseUrl() . '/orders/view/' . $id);
    }

    public function invoice(string $id): void
    {
        $order = $this->order()->getWithItems((int) $id);
        if (!$order) {
            $this->redirect($this->baseUrl() . '/orders');
            return;
        }
        $config = require dirname(__DIR__, 2) . '/config/app.php';
        $this->view('orders.invoice', ['pageTitle' => 'Order Invoice', 'order' => $order, 'companyName' => $config['name']]);
    }

    public function orderDelete(string $id): void
    {
        $order = $this->order()->find((int) $id);
        if (!$order) {
            $this->redirect($this->baseUrl() . '/orders');
            return;
        }
        $this->order()->delete((int) $id);
        $this->redirect($this->baseUrl() . '/orders');
    }
}
