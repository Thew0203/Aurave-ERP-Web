<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\StockMovement;

class SalesController extends Controller
{
    private function companyId(): int { return (int) $_SESSION['company_id']; }
    private function userId(): ?int { return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null; }

    private function sale(): Sale { $m = new Sale(); $m->setTenantId($this->companyId()); return $m; }
    private function saleItem(): SaleItem { $m = new SaleItem(); $m->setTenantId($this->companyId()); return $m; }
    private function customer(): Customer { $m = new Customer(); $m->setTenantId($this->companyId()); return $m; }
    private function product(): Product { $m = new Product(); $m->setTenantId($this->companyId()); return $m; }
    private function stock(): StockMovement { $m = new StockMovement(); $m->setTenantId($this->companyId()); return $m; }

    public function index(): void
    {
        $list = $this->sale()->getList();
        $this->view('sales.index', ['pageTitle' => 'Sales', 'sales' => $list]);
    }

    public function create(): void
    {
        $customers = $this->customer()->getByCompany();
        $products = $this->product()->getSellable();
        $nextInv = $this->sale()->getNextInvoiceNumber();
        $this->view('sales.create', ['pageTitle' => 'New Sale', 'customers' => $customers, 'products' => $products, 'nextInvoice' => $nextInv]);
    }

    public function store(): void
    {
        $customerId = $this->input('customer_id') ? (int) $this->input('customer_id') : null;
        $saleDate = $this->input('sale_date') ?: date('Y-m-d');
        $notes = trim((string) $this->input('notes'));
        $items = $this->input('items');
        if (!is_array($items) || empty($items)) {
            $this->redirect($this->baseUrl() . '/sales/create');
            return;
        }
        $subtotal = 0;
        $validItems = [];
        foreach ($items as $row) {
            if (empty($row['product_id']) || empty($row['quantity']) || (float)$row['quantity'] <= 0) continue;
            $product = $this->product()->find((int) $row['product_id']);
            if (!$product) continue;
            $qty = (float) $row['quantity'];
            $price = (float) ($row['unit_price'] ?? $product['selling_price']);
            $total = $qty * $price;
            $subtotal += $total;
            $validItems[] = ['product' => $product, 'quantity' => $qty, 'unit_price' => $price, 'total' => $total];
        }
        if (empty($validItems)) {
            $this->redirect($this->baseUrl() . '/sales/create');
            return;
        }
        $taxRate = 0;
        $taxAmount = round($subtotal * $taxRate, 2);
        $discount = 0;
        $total = $subtotal + $taxAmount - $discount;
        $invoiceNumber = $this->sale()->getNextInvoiceNumber();
        $saleId = $this->sale()->create([
            'company_id' => $this->companyId(),
            'customer_id' => $customerId,
            'invoice_number' => $invoiceNumber,
            'sale_date' => $saleDate,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discount,
            'total' => $total,
            'status' => 'confirmed',
            'notes' => $notes,
            'created_by' => $this->userId(),
        ]);
        foreach ($validItems as $row) {
            $this->saleItem()->create([
                'sale_id' => $saleId,
                'product_id' => (int) $row['product']['id'],
                'quantity' => $row['quantity'],
                'unit_price' => $row['unit_price'],
                'total' => $row['total'],
            ]);
            $this->product()->adjustQuantity((int) $row['product']['id'], -$row['quantity']);
            $this->stock()->create([
                'company_id' => $this->companyId(),
                'product_id' => (int) $row['product']['id'],
                'type' => 'sale',
                'quantity' => $row['quantity'],
                'reference_type' => 'sale',
                'reference_id' => $saleId,
                'notes' => 'Sale ' . $invoiceNumber,
                'created_by' => $this->userId(),
            ]);
        }
        $this->redirect($this->baseUrl() . '/sales/view/' . $saleId);
    }

    public function viewSale(string $id): void
    {
        $sale = $this->sale()->getWithItems((int) $id);
        if (!$sale) { $this->redirect($this->baseUrl() . '/sales'); return; }
        $this->view('sales.view', ['pageTitle' => 'Sale', 'sale' => $sale]);
    }

    public function invoice(string $id): void
    {
        $sale = $this->sale()->getWithItems((int) $id);
        if (!$sale) { $this->redirect($this->baseUrl() . '/sales'); return; }
        $config = require (defined('APP_PATH') ? APP_PATH : dirname(__DIR__)) . '/config/app.php';
        $this->view('sales.invoice', ['pageTitle' => 'Invoice', 'sale' => $sale, 'companyName' => $config['name']]);
    }

    public function customers(): void
    {
        $list = $this->customer()->getByCompany();
        $this->view('sales.customers', ['pageTitle' => 'Customers', 'customers' => $list]);
    }

    public function customerSave(): void
    {
        $id = $this->input('id');
        $name = trim((string) $this->input('name'));
        $email = trim((string) $this->input('email'));
        $phone = trim((string) $this->input('phone'));
        $address = trim((string) $this->input('address'));
        $companyId = $this->companyId();
        if ($id) {
            $this->customer()->update((int) $id, ['name' => $name, 'email' => $email, 'phone' => $phone, 'address' => $address]);
        } else {
            $this->customer()->create(['company_id' => $companyId, 'name' => $name, 'email' => $email, 'phone' => $phone, 'address' => $address]);
        }
        $this->redirect($this->baseUrl() . '/sales/customers');
    }

    public function customerEdit(string $id): void
    {
        $customer = $this->customer()->find((int) $id);
        if (!$customer) {
            $this->redirect($this->baseUrl() . '/sales/customers');
            return;
        }
        $this->view('sales.customer_edit', ['pageTitle' => 'Edit Customer', 'customer' => $customer]);
    }

    public function customerUpdate(string $id): void
    {
        $customer = $this->customer()->find((int) $id);
        if (!$customer) {
            $this->redirect($this->baseUrl() . '/sales/customers');
            return;
        }
        $name = trim((string) $this->input('name'));
        $email = trim((string) $this->input('email'));
        $phone = trim((string) $this->input('phone'));
        $address = trim((string) $this->input('address'));
        $this->customer()->update((int) $id, ['name' => $name, 'email' => $email, 'phone' => $phone, 'address' => $address]);
        $this->redirect($this->baseUrl() . '/sales/customers');
    }

    public function customerDelete(string $id): void
    {
        $customer = $this->customer()->find((int) $id);
        if (!$customer) {
            $this->redirect($this->baseUrl() . '/sales/customers');
            return;
        }
        $this->customer()->delete((int) $id);
        $this->redirect($this->baseUrl() . '/sales/customers');
    }
}
