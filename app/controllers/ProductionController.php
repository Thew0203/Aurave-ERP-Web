<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\BomHeader;
use App\Models\BomItem;
use App\Models\ProductionOrder;
use App\Models\Product;
use App\Models\StockMovement;

class ProductionController extends Controller
{
    private function companyId(): int { return (int) $_SESSION['company_id']; }
    private function userId(): ?int { return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null; }

    private function bom(): BomHeader { $m = new BomHeader(); $m->setTenantId($this->companyId()); return $m; }
    private function bomItem(): BomItem { $m = new BomItem(); return $m; }
    private function prodOrder(): ProductionOrder { $m = new ProductionOrder(); $m->setTenantId($this->companyId()); return $m; }
    private function product(): Product { $m = new Product(); $m->setTenantId($this->companyId()); return $m; }
    private function stock(): StockMovement { $m = new StockMovement(); $m->setTenantId($this->companyId()); return $m; }

    public function bomList(): void
    {
        $list = $this->bom()->getWithProduct();
        $this->view('production.bom_list', ['pageTitle' => 'Bill of Materials', 'boms' => $list]);
    }

    public function bomForm(): void
    {
        $products = $this->product()->getFinishedForBom();
        $raw = $this->product()->getRawMaterials();
        $this->view('production.bom_form', ['pageTitle' => 'Add BOM', 'bom' => null, 'products' => $products, 'rawMaterials' => $raw]);
    }

    public function bomCreate(): void
    {
        $productId = (int) $this->input('product_id');
        $name = trim((string) $this->input('name'));
        $version = trim((string) $this->input('version')) ?: '1.0';
        if (!$productId || !$name) {
            $this->redirect($this->baseUrl() . '/production/bom/create');
            return;
        }
        $id = $this->bom()->create([
            'company_id' => $this->companyId(),
            'product_id' => $productId,
            'name' => $name,
            'version' => $version,
            'is_active' => 1,
        ]);
        $items = $this->input('bom_items');
        if (is_array($items)) {
            foreach ($items as $row) {
                if (empty($row['product_id']) || empty($row['quantity'])) continue;
                $this->bomItem()->create([
                    'bom_header_id' => $id,
                    'product_id' => (int) $row['product_id'],
                    'quantity' => (float) $row['quantity'],
                    'unit' => $row['unit'] ?? 'pcs',
                ]);
            }
        }
        $this->redirect($this->baseUrl() . '/production/bom');
    }

    public function bomEdit(string $id): void
    {
        $bom = $this->bom()->getWithItems((int) $id);
        if (!$bom) { $this->redirect($this->baseUrl() . '/production/bom'); return; }
        $products = $this->product()->getFinishedForBom();
        $raw = $this->product()->getRawMaterials();
        $this->view('production.bom_form', ['pageTitle' => 'Edit BOM', 'bom' => $bom, 'products' => $products, 'rawMaterials' => $raw]);
    }

    public function bomUpdate(string $id): void
    {
        $bom = $this->bom()->find((int) $id);
        if (!$bom) { $this->redirect($this->baseUrl() . '/production/bom'); return; }
        $this->bom()->update((int) $id, [
            'name' => trim((string) $this->input('name')),
            'version' => trim((string) $this->input('version')) ?: '1.0',
            'is_active' => $this->input('is_active') ? 1 : 0,
        ]);
        $this->bomItem()->deleteByBom((int) $id);
        $items = $this->input('bom_items');
        if (is_array($items)) {
            foreach ($items as $row) {
                if (empty($row['product_id']) || empty($row['quantity'])) continue;
                $this->bomItem()->create([
                    'bom_header_id' => (int) $id,
                    'product_id' => (int) $row['product_id'],
                    'quantity' => (float) $row['quantity'],
                    'unit' => $row['unit'] ?? 'pcs',
                ]);
            }
        }
        $this->redirect($this->baseUrl() . '/production/bom');
    }

    public function orders(): void
    {
        $list = $this->prodOrder()->getList();
        $this->view('production.orders', ['pageTitle' => 'Production Orders', 'orders' => $list]);
    }

    public function orderForm(): void
    {
        $boms = $this->bom()->getWithProduct();
        $nextNumber = $this->prodOrder()->getNextOrderNumber();
        $this->view('production.order_form', ['pageTitle' => 'New Production Order', 'boms' => $boms, 'nextNumber' => $nextNumber]);
    }

    public function orderCreate(): void
    {
        $bomId = (int) $this->input('bom_header_id');
        $quantity = (float) $this->input('quantity');
        $notes = trim((string) $this->input('notes'));
        $bom = $this->bom()->getWithItems($bomId);
        if (!$bom || $quantity <= 0) {
            $this->redirect($this->baseUrl() . '/production/orders/create');
            return;
        }
        $orderNumber = $this->prodOrder()->getNextOrderNumber();
        $orderId = $this->prodOrder()->create([
            'company_id' => $this->companyId(),
            'bom_header_id' => $bomId,
            'product_id' => (int) $bom['product_id'],
            'order_number' => $orderNumber,
            'quantity' => $quantity,
            'status' => 'draft',
            'notes' => $notes,
            'created_by' => $this->userId(),
        ]);
        $this->redirect($this->baseUrl() . '/production/orders/view/' . $orderId);
    }

    public function orderView(string $id): void
    {
        $order = $this->prodOrder()->getWithDetails((int) $id);
        if (!$order) { $this->redirect($this->baseUrl() . '/production/orders'); return; }
        $this->view('production.order_view', ['pageTitle' => 'Production Order', 'order' => $order]);
    }

    public function orderStatus(string $id): void
    {
        $order = $this->prodOrder()->find((int) $id);
        if (!$order) { $this->redirect($this->baseUrl() . '/production/orders'); return; }
        $status = $this->input('status');
        $valid = ['draft', 'confirmed', 'in_progress', 'completed', 'cancelled'];
        if (!in_array($status, $valid, true)) {
            $this->redirect($this->baseUrl() . '/production/orders/view/' . $id);
            return;
        }
        $this->prodOrder()->update((int) $id, ['status' => $status]);
        if ($status === 'in_progress' && empty($order['started_at'])) {
            $this->prodOrder()->update((int) $id, ['started_at' => date('Y-m-d H:i:s')]);
        }
        if ($status === 'completed') {
            $this->prodOrder()->update((int) $id, ['completed_at' => date('Y-m-d H:i:s')]);
            $bom = $this->bom()->getWithItems((int) $order['bom_header_id']);
            $companyId = $this->companyId();
            $qty = (float) $order['quantity'];
            foreach ($bom['items'] ?? [] as $item) {
                $consumeQty = (float) $item['quantity'] * $qty;
                $this->product()->adjustQuantity((int) $item['product_id'], -$consumeQty);
                $this->stock()->create([
                    'company_id' => $companyId,
                    'product_id' => (int) $item['product_id'],
                    'type' => 'production',
                    'quantity' => $consumeQty,
                    'reference_type' => 'production_order',
                    'reference_id' => (int) $id,
                    'notes' => 'Consumption for PO ' . $order['order_number'],
                    'created_by' => $this->userId(),
                ]);
            }
            $fgProductId = (int) $order['product_id'];
            $this->product()->adjustQuantity($fgProductId, $qty);
            $this->stock()->create([
                'company_id' => $companyId,
                'product_id' => $fgProductId,
                'type' => 'production',
                'quantity' => $qty,
                'reference_type' => 'production_order',
                'reference_id' => (int) $id,
                'notes' => 'Finished goods from PO ' . $order['order_number'],
                'created_by' => $this->userId(),
            ]);
        }
        $this->redirect($this->baseUrl() . '/production/orders/view/' . $id);
    }
}
