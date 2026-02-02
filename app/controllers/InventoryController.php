<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\StockMovement;

class InventoryController extends Controller
{
    private function getProductModel(): Product
    {
        $m = new Product();
        $companyId = $_SESSION['company_id'] ?? null;
        $m->setTenantId($companyId !== null ? (int) $companyId : 0);
        return $m;
    }

    private function getCategoryModel(): Category
    {
        $m = new Category();
        $m->setTenantId((int) $_SESSION['company_id']);
        return $m;
    }

    private function getSupplierModel(): Supplier
    {
        $m = new Supplier();
        $m->setTenantId((int) $_SESSION['company_id']);
        return $m;
    }

    private function getStockModel(): StockMovement
    {
        $m = new StockMovement();
        $m->setTenantId((int) $_SESSION['company_id']);
        return $m;
    }

    public function products(): void
    {
        $products = $this->getProductModel()->allWithDetails();
        $this->view('inventory.products', ['pageTitle' => 'Products', 'products' => $products]);
    }

    public function productForm(): void
    {
        if (empty($_SESSION['company_id'])) {
            $this->redirect($this->baseUrl() . '/dashboard');
            return;
        }
        $categories = $this->getCategoryModel()->all();
        $suppliers = $this->getSupplierModel()->all();
        $this->view('inventory.product_form', ['pageTitle' => 'Add Product', 'product' => null, 'categories' => $categories, 'suppliers' => $suppliers]);
    }

    public function productCreate(): void
    {
        $companyId = (int) ($_SESSION['company_id'] ?? 0);
        if ($companyId <= 0) {
            $this->redirect($this->baseUrl() . '/dashboard');
            return;
        }
        $name = trim((string) $this->input('name'));
        $costPrice = (float) $this->input('cost_price');
        $sellingPrice = (float) $this->input('selling_price');
        $quantity = (float) $this->input('quantity');
        $lowStock = (float) $this->input('low_stock_threshold');
        $errors = [];
        if ($name === '') {
            $errors[] = 'Name is required.';
        }
        if ($costPrice < 0 || $sellingPrice < 0 || $quantity < 0 || $lowStock < 0) {
            $errors[] = 'Cost price, selling price, quantity, and low stock threshold must be 0 or greater.';
        }
        if (!empty($errors)) {
            $categories = $this->getCategoryModel()->all();
            $suppliers = $this->getSupplierModel()->all();
            $productFromPost = [
                'name' => $name,
                'sku' => trim((string) $this->input('sku')),
                'description' => trim((string) $this->input('description')),
                'category_name' => trim((string) $this->input('category_name')),
                'supplier_name' => trim((string) $this->input('supplier_name')),
                'unit' => trim((string) $this->input('unit')) ?: 'pcs',
                'cost_price' => $costPrice,
                'selling_price' => $sellingPrice,
                'quantity' => $quantity,
                'low_stock_threshold' => $lowStock,
                'is_raw_material' => $this->input('is_raw_material') ? 1 : 0,
            ];
            $this->view('inventory.product_form', [
                'pageTitle' => 'Add Product',
                'product' => $productFromPost,
                'categories' => $categories,
                'suppliers' => $suppliers,
                'error' => implode(' ', $errors),
            ]);
            return;
        }
        $categoryModel = $this->getCategoryModel();
        $supplierModel = $this->getSupplierModel();
        $categoryId = $categoryModel->findOrCreateByName($companyId, trim((string) $this->input('category_name')));
        $supplierId = $supplierModel->findOrCreateByName($companyId, trim((string) $this->input('supplier_name')));
        $imageUrl = $this->handleProductImageUpload();
        $data = [
            'company_id' => $companyId,
            'category_id' => $categoryId,
            'supplier_id' => $supplierId,
            'sku' => trim((string) $this->input('sku')),
            'name' => $name,
            'description' => trim((string) $this->input('description')),
            'unit' => trim((string) $this->input('unit')) ?: 'pcs',
            'cost_price' => $costPrice,
            'selling_price' => $sellingPrice,
            'quantity' => $quantity,
            'low_stock_threshold' => $lowStock,
            'is_raw_material' => $this->input('is_raw_material') ? 1 : 0,
            'is_active' => 1,
        ];
        if ($imageUrl !== null) {
            $data['image_url'] = $imageUrl;
        }
        $this->getProductModel()->create($data);
        $this->redirect($this->baseUrl() . '/inventory/products');
    }

    public function productEdit(string $id): void
    {
        if (empty($_SESSION['company_id'])) {
            $this->redirect($this->baseUrl() . '/dashboard');
            return;
        }
        $product = $this->getProductModel()->find((int) $id);
        if (!$product) {
            $this->redirect($this->baseUrl() . '/inventory/products');
            return;
        }
        $companyId = (int) $_SESSION['company_id'];
        if (!empty($product['category_id'])) {
            $cat = $this->getCategoryModel()->find((int) $product['category_id']);
            $product['category_name'] = $cat['name'] ?? '';
        } else {
            $product['category_name'] = '';
        }
        if (!empty($product['supplier_id'])) {
            $sup = $this->getSupplierModel()->find((int) $product['supplier_id']);
            $product['supplier_name'] = $sup['name'] ?? '';
        } else {
            $product['supplier_name'] = '';
        }
        $categories = $this->getCategoryModel()->all();
        $suppliers = $this->getSupplierModel()->all();
        $this->view('inventory.product_form', ['pageTitle' => 'Edit Product', 'product' => $product, 'categories' => $categories, 'suppliers' => $suppliers]);
    }

    public function productUpdate(string $id): void
    {
        $product = $this->getProductModel()->find((int) $id);
        if (!$product) {
            $this->redirect($this->baseUrl() . '/inventory/products');
            return;
        }
        $name = trim((string) $this->input('name'));
        $costPrice = (float) $this->input('cost_price');
        $sellingPrice = (float) $this->input('selling_price');
        $quantity = (float) $this->input('quantity');
        $lowStock = (float) $this->input('low_stock_threshold');
        $errors = [];
        if ($name === '') {
            $errors[] = 'Name is required.';
        }
        if ($costPrice < 0 || $sellingPrice < 0 || $quantity < 0 || $lowStock < 0) {
            $errors[] = 'Cost price, selling price, quantity, and low stock threshold must be 0 or greater.';
        }
        if (!empty($errors)) {
            $categories = $this->getCategoryModel()->all();
            $suppliers = $this->getSupplierModel()->all();
            $product['name'] = $name;
            $product['cost_price'] = $costPrice;
            $product['selling_price'] = $sellingPrice;
            $product['quantity'] = $quantity;
            $product['low_stock_threshold'] = $lowStock;
            $product['category_name'] = trim((string) $this->input('category_name'));
            $product['supplier_name'] = trim((string) $this->input('supplier_name'));
            $product['sku'] = trim((string) $this->input('sku'));
            $product['description'] = trim((string) $this->input('description'));
            $product['unit'] = trim((string) $this->input('unit')) ?: 'pcs';
            $product['is_raw_material'] = $this->input('is_raw_material') ? 1 : 0;
            $product['is_active'] = $this->input('is_active') ? 1 : 0;
            $this->view('inventory.product_form', [
                'pageTitle' => 'Edit Product',
                'product' => $product,
                'categories' => $categories,
                'suppliers' => $suppliers,
                'error' => implode(' ', $errors),
            ]);
            return;
        }
        $companyId = (int) $_SESSION['company_id'];
        $categoryModel = $this->getCategoryModel();
        $supplierModel = $this->getSupplierModel();
        $categoryId = $categoryModel->findOrCreateByName($companyId, trim((string) $this->input('category_name')));
        $supplierId = $supplierModel->findOrCreateByName($companyId, trim((string) $this->input('supplier_name')));
        $data = [
            'category_id' => $categoryId,
            'supplier_id' => $supplierId,
            'sku' => trim((string) $this->input('sku')),
            'name' => $name,
            'description' => trim((string) $this->input('description')),
            'unit' => trim((string) $this->input('unit')) ?: 'pcs',
            'cost_price' => $costPrice,
            'selling_price' => $sellingPrice,
            'quantity' => $quantity,
            'low_stock_threshold' => $lowStock,
            'is_raw_material' => $this->input('is_raw_material') ? 1 : 0,
            'is_active' => $this->input('is_active') ? 1 : 0,
        ];
        $newImage = $this->handleProductImageUpload();
        if ($newImage !== null) {
            $data['image_url'] = $newImage;
        }
        $this->getProductModel()->update((int) $id, $data);
        $this->redirect($this->baseUrl() . '/inventory/products');
    }

    private function handleProductImageUpload(): ?string
    {
        if (empty($_FILES['image']['name']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, $allowed, true)) {
            return null;
        }
        if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            return null;
        }
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION) ?: 'jpg';
        $ext = strtolower(preg_replace('/[^a-z0-9]/', '', $ext)) ?: 'jpg';
        $filename = bin2hex(random_bytes(8)) . '.' . $ext;
        $dir = dirname(__DIR__, 2) . '/public/uploads/products';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $path = $dir . '/' . $filename;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $path)) {
            return null;
        }
        return 'uploads/products/' . $filename;
    }

    public function productDelete(string $id): void
    {
        $this->getProductModel()->delete((int) $id);
        $this->redirect($this->baseUrl() . '/inventory/products');
    }

    public function stock(): void
    {
        $products = $this->getProductModel()->allWithDetails();
        $recent = $this->getStockModel()->getRecent(30);
        $valuation = $this->getProductModel()->getValuation();
        $this->view('inventory.stock', ['pageTitle' => 'Stock', 'products' => $products, 'recentMovements' => $recent, 'valuation' => $valuation]);
    }

    public function stockMovement(): void
    {
        $productId = (int) $this->input('product_id');
        $type = $this->input('type');
        $qty = (float) $this->input('quantity');
        $notes = trim((string) $this->input('notes'));
        if (!in_array($type, ['in', 'out', 'adjustment'], true) || $qty <= 0) {
            $this->redirect($this->baseUrl() . '/inventory/stock');
            return;
        }
        $product = $this->getProductModel()->find($productId);
        if (!$product) {
            $this->redirect($this->baseUrl() . '/inventory/stock');
            return;
        }
        $companyId = (int) $_SESSION['company_id'];
        $delta = $type === 'out' ? -$qty : $qty;
        $newQty = (float) $product['quantity'] + $delta;
        if ($newQty < 0) {
            $this->redirect($this->baseUrl() . '/inventory/stock');
            return;
        }
        $this->getProductModel()->adjustQuantity($productId, $delta);
        $stockModel = $this->getStockModel();
        $stockModel->create([
            'company_id' => $companyId,
            'product_id' => $productId,
            'type' => $type,
            'quantity' => $qty,
            'notes' => $notes,
            'created_by' => $_SESSION['user_id'] ?? null,
        ]);
        $this->redirect($this->baseUrl() . '/inventory/stock');
    }

    public function categories(): void
    {
        $categories = $this->getCategoryModel()->all();
        $this->view('inventory.categories', ['pageTitle' => 'Categories', 'categories' => $categories]);
    }

    public function categorySave(): void
    {
        $id = $this->input('id');
        $name = trim((string) $this->input('name'));
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
        $companyId = (int) $_SESSION['company_id'];
        if ($id) {
            $this->getCategoryModel()->update((int) $id, ['name' => $name, 'slug' => $slug]);
        } else {
            $this->getCategoryModel()->create(['company_id' => $companyId, 'name' => $name, 'slug' => $slug]);
        }
        $this->redirect($this->baseUrl() . '/inventory/categories');
    }

    public function suppliers(): void
    {
        $suppliers = $this->getSupplierModel()->all();
        $this->view('inventory.suppliers', ['pageTitle' => 'Suppliers', 'suppliers' => $suppliers]);
    }

    public function supplierForm(): void
    {
        $this->view('inventory.supplier_form', ['pageTitle' => 'Add Supplier', 'supplier' => null]);
    }

    public function supplierCreate(): void
    {
        $this->getSupplierModel()->create([
            'company_id' => (int) $_SESSION['company_id'],
            'name' => trim((string) $this->input('name')),
            'email' => trim((string) $this->input('email')),
            'phone' => trim((string) $this->input('phone')),
            'address' => trim((string) $this->input('address')),
            'is_active' => 1,
        ]);
        $this->redirect($this->baseUrl() . '/inventory/suppliers');
    }

    public function supplierEdit(string $id): void
    {
        $supplier = $this->getSupplierModel()->find((int) $id);
        if (!$supplier) {
            $this->redirect($this->baseUrl() . '/inventory/suppliers');
            return;
        }
        $this->view('inventory.supplier_form', ['pageTitle' => 'Edit Supplier', 'supplier' => $supplier]);
    }

    public function supplierUpdate(string $id): void
    {
        $this->getSupplierModel()->update((int) $id, [
            'name' => trim((string) $this->input('name')),
            'email' => trim((string) $this->input('email')),
            'phone' => trim((string) $this->input('phone')),
            'address' => trim((string) $this->input('address')),
        ]);
        $this->redirect($this->baseUrl() . '/inventory/suppliers');
    }

    public function alerts(): void
    {
        $lowStock = $this->getProductModel()->getLowStock();
        $valuation = $this->getProductModel()->getValuation();
        $this->view('inventory.alerts', ['pageTitle' => 'Low Stock Alerts', 'products' => $lowStock, 'valuation' => $valuation]);
    }
}
