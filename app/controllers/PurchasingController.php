<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\StockMovement;

class PurchasingController extends Controller
{
    private function companyId(): int { return (int) $_SESSION['company_id']; }
    private function userId(): ?int { return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null; }

    private function purchase(): Purchase { $m = new Purchase(); $m->setTenantId($this->companyId()); return $m; }
    private function purchaseItem(): PurchaseItem { $m = new PurchaseItem(); $m->setTenantId($this->companyId()); return $m; }
    private function supplier(): Supplier { $m = new Supplier(); $m->setTenantId($this->companyId()); return $m; }
    private function product(): Product { $m = new Product(); $m->setTenantId($this->companyId()); return $m; }
    private function stock(): StockMovement { $m = new StockMovement(); $m->setTenantId($this->companyId()); return $m; }

    public function index(): void
    {
        $list = $this->purchase()->getList();
        $this->view('purchasing.index', ['pageTitle' => 'Purchasing', 'purchases' => $list]);
    }

    public function create(): void
    {
        $suppliers = $this->supplier()->all();
        $products = $this->product()->allWithDetails();
        $nextInv = $this->purchase()->getNextInvoiceNumber();
        $this->view('purchasing.create', ['pageTitle' => 'New Purchase', 'suppliers' => $suppliers, 'products' => $products, 'nextInvoice' => $nextInv]);
    }

    public function store(): void
    {
        $supplierId = (int) $this->input('supplier_id');
        $purchaseDate = $this->input('purchase_date') ?: date('Y-m-d');
        $notes = trim((string) $this->input('notes'));
        $items = $this->input('items');
        if (!$supplierId || !is_array($items) || empty($items)) {
            $this->redirect($this->baseUrl() . '/purchasing/create');
            return;
        }
        $subtotal = 0;
        $validItems = [];
        foreach ($items as $row) {
            if (empty($row['product_id']) || empty($row['quantity']) || (float)$row['quantity'] <= 0) continue;
            $product = $this->product()->find((int) $row['product_id']);
            if (!$product) continue;
            $qty = (float) $row['quantity'];
            $price = (float) ($row['unit_price'] ?? $product['cost_price']);
            $total = $qty * $price;
            $subtotal += $total;
            $validItems[] = ['product' => $product, 'quantity' => $qty, 'unit_price' => $price, 'total' => $total];
        }
        if (empty($validItems)) {
            $this->redirect($this->baseUrl() . '/purchasing/create');
            return;
        }
        $taxAmount = 0;
        $discount = 0;
        $total = $subtotal + $taxAmount - $discount;
        $invoiceNumber = $this->purchase()->getNextInvoiceNumber();
        $purchaseId = $this->purchase()->create([
            'company_id' => $this->companyId(),
            'supplier_id' => $supplierId,
            'invoice_number' => $invoiceNumber,
            'purchase_date' => $purchaseDate,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discount,
            'total' => $total,
            'status' => 'received',
            'notes' => $notes,
            'created_by' => $this->userId(),
        ]);
        foreach ($validItems as $row) {
            $this->purchaseItem()->create([
                'purchase_id' => $purchaseId,
                'product_id' => (int) $row['product']['id'],
                'quantity' => $row['quantity'],
                'unit_price' => $row['unit_price'],
                'total' => $row['total'],
            ]);
            $this->product()->adjustQuantity((int) $row['product']['id'], $row['quantity']);
            $this->stock()->create([
                'company_id' => $this->companyId(),
                'product_id' => (int) $row['product']['id'],
                'type' => 'purchase',
                'quantity' => $row['quantity'],
                'reference_type' => 'purchase',
                'reference_id' => $purchaseId,
                'notes' => 'Purchase ' . $invoiceNumber,
                'created_by' => $this->userId(),
            ]);
        }
        $this->redirect($this->baseUrl() . '/purchasing/view/' . $purchaseId);
    }

    public function viewPurchase(string $id): void
    {
        $purchase = $this->purchase()->getWithItems((int) $id);
        if (!$purchase) { $this->redirect($this->baseUrl() . '/purchasing'); return; }
        $this->view('purchasing.view', ['pageTitle' => 'Purchase', 'purchase' => $purchase]);
    }

    public function suppliers(): void
    {
        $list = $this->supplier()->all();
        $this->view('purchasing.suppliers', ['pageTitle' => 'Suppliers', 'suppliers' => $list]);
    }
}
