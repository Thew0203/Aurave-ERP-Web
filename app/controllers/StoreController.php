<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockMovement;

class StoreController extends Controller
{
    private function companyId(): int
    {
        return (int) ($_SESSION['company_id'] ?? 1);
    }

    private function productModel(): Product
    {
        $m = new Product();
        $m->setTenantId($this->companyId());
        return $m;
    }

    private function customer(): Customer
    {
        $m = new Customer();
        $m->setTenantId($this->companyId());
        return $m;
    }

    private function order(): Order
    {
        $m = new Order();
        $m->setTenantId($this->companyId());
        return $m;
    }

    public function index(): void
    {
        $productModel = new Product();
        $products = $productModel->getSellableAll();
        $this->view('store.index', ['pageTitle' => 'Store', 'products' => $products]);
    }

    public function product(string $id): void
    {
        $productModel = new Product();
        $product = $productModel->findByIdGlobal((int) $id);
        if (!$product || !empty($product['is_raw_material']) || empty($product['is_active'])) {
            $this->redirect($this->baseUrl() . '/store');
            return;
        }
        $this->view('store.product', ['pageTitle' => $product['name'], 'product' => $product]);
    }

    public function cart(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        $ids = array_keys($cart);
        $products = [];
        $productModel = new Product();
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $p = $productModel->findByIdGlobal((int) $id);
                if ($p) {
                    $p['cart_qty'] = (float) ($cart[$id] ?? 0);
                    $products[] = $p;
                }
            }
        }
        $this->view('store.cart', ['pageTitle' => 'Cart', 'products' => $products]);
    }

    public function cartAdd(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $id = (int) $this->input('product_id');
        $qty = (float) $this->input('quantity');
        if ($id <= 0 || $qty <= 0) {
            $this->redirect($this->baseUrl() . '/store');
            return;
        }
        $productModel = new Product();
        $product = $productModel->findByIdGlobal($id);
        if (!$product || !empty($product['is_raw_material'])) {
            $this->redirect($this->baseUrl() . '/store');
            return;
        }
        $available = (float) ($product['quantity'] ?? 0);
        $currentInCart = (float) ($_SESSION['cart'][$id] ?? 0);
        $newTotal = $currentInCart + $qty;
        if ($newTotal > $available) {
            $newTotal = max(0, $available);
        }
        $_SESSION['cart'] = $_SESSION['cart'] ?? [];
        if ($newTotal <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id] = $newTotal;
        }
        if ($this->isAjax()) {
            $this->json(['ok' => true, 'cart_count' => array_sum($_SESSION['cart'])]);
        }
        $this->redirect($this->baseUrl() . '/store/cart');
    }

    public function cartUpdate(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $id = (int) $this->input('product_id');
        $qty = (float) $this->input('quantity');
        $_SESSION['cart'] = $_SESSION['cart'] ?? [];
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $productModel = new Product();
            $product = $productModel->findByIdGlobal($id);
            $available = $product ? (float) ($product['quantity'] ?? 0) : 0;
            $_SESSION['cart'][$id] = min($qty, $available);
            if ($_SESSION['cart'][$id] <= 0) {
                unset($_SESSION['cart'][$id]);
            }
        }
        if ($this->isAjax()) {
            $this->json(['ok' => true]);
        }
        $this->redirect($this->baseUrl() . '/store/cart');
    }

    public function cartRemove(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $id = (int) $this->input('product_id');
        $_SESSION['cart'] = $_SESSION['cart'] ?? [];
        unset($_SESSION['cart'][$id]);
        if ($this->isAjax()) {
            $this->json(['ok' => true]);
        }
        $this->redirect($this->baseUrl() . '/store/cart');
    }

    /** Only customers can place orders. Admin/staff view and control orders in Orders (OMS). */
    private function requireCustomerForCheckout(): bool
    {
        $role = $_SESSION['role'] ?? '';
        if ($role !== 'customer') {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['cart_message'] = $role ? 'Only customers can place orders. You are logged in as staff or admin. Use a customer account (e.g. register as customer) to place orders.' : 'Please sign in with a customer account to place an order.';
            return false;
        }
        return true;
    }

    public function checkout(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            $this->redirect($this->baseUrl() . '/store/cart');
            return;
        }
        if (!$this->requireCustomerForCheckout()) {
            $this->redirect($this->baseUrl() . '/store/cart');
            return;
        }
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $customer = $userId ? $this->customer()->findByUserId($userId) : null;
        $productModel = new Product();
        $products = [];
        $subtotal = 0;
        foreach ($cart as $id => $qty) {
            $p = $productModel->findByIdGlobal((int) $id);
            if (!$p || (float) $qty <= 0) continue;
            $p['cart_qty'] = (float) $qty;
            $p['line_total'] = (float) $qty * (float) $p['selling_price'];
            $subtotal += $p['line_total'];
            $products[] = $p;
        }
        if (empty($products)) {
            $this->redirect($this->baseUrl() . '/store/cart');
            return;
        }
        $this->view('store.checkout', [
            'pageTitle' => 'Checkout',
            'products' => $products,
            'subtotal' => $subtotal,
            'tax' => 0,
            'shipping' => 0,
            'total' => $subtotal,
            'customer' => $customer,
        ]);
    }

    public function checkoutSubmit(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            $this->redirect($this->baseUrl() . '/store/cart');
            return;
        }
        if (!$this->requireCustomerForCheckout()) {
            $this->redirect($this->baseUrl() . '/store/cart');
            return;
        }
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $shippingName = trim((string) $this->input('shipping_name'));
        $shippingPhone = trim((string) $this->input('shipping_phone'));
        $shippingAddress = trim((string) $this->input('shipping_address'));
        $shippingCity = trim((string) $this->input('shipping_city'));
        $shippingZip = trim((string) $this->input('shipping_zip'));
        $notes = trim((string) $this->input('notes'));
        $paymentMethod = trim((string) $this->input('payment_method')) ?: 'cod';
        $productModel = new Product();

        // Group cart items by vendor (product's company_id)
        $itemsByVendor = [];
        foreach ($cart as $id => $qty) {
            $p = $productModel->findByIdGlobal((int) $id);
            if (!$p || (float) $qty <= 0) continue;
            $vendorId = (int) ($p['company_id'] ?? 1);
            $price = (float) $p['selling_price'];
            $total = (float) $qty * $price;
            $itemsByVendor[$vendorId][] = ['product' => $p, 'quantity' => (float) $qty, 'unit_price' => $price, 'total' => $total];
        }
        if (empty($itemsByVendor)) {
            $this->redirect($this->baseUrl() . '/store/cart');
            return;
        }

        // Check stock for all items
        foreach ($itemsByVendor as $vendorId => $items) {
            foreach ($items as $row) {
                $available = (float) ($row['product']['quantity'] ?? 0);
                if ($available < $row['quantity']) {
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    $_SESSION['cart_error'] = 'Insufficient stock for "' . $row['product']['name'] . '" (requested ' . $row['quantity'] . ', available ' . $available . '). Please update your cart.';
                    $this->redirect($this->baseUrl() . '/store/cart');
                    return;
                }
            }
        }

        $lastOrderId = null;
        $customerModel = new Customer();
        $orderItemModel = new OrderItem();
        $orderItemModel->setTenantId(null);

        // Create one order per vendor (product's company)
        foreach ($itemsByVendor as $vendorCompanyId => $items) {
            // Find or create customer record in vendor's company
            $customerModel->setTenantId($vendorCompanyId);
            $cust = $customerModel->findByUserId($userId);
            if (!$cust) {
                // Create customer record in this vendor's company
                $userName = $_SESSION['user_name'] ?? $shippingName;
                $userEmail = $_SESSION['email'] ?? '';
                $custId = $customerModel->create([
                    'company_id' => $vendorCompanyId,
                    'user_id' => $userId,
                    'name' => $userName,
                    'email' => $userEmail,
                    'phone' => $shippingPhone,
                    'address' => $shippingAddress,
                ]);
                $customerId = $custId;
            } else {
                $customerId = (int) $cust['id'];
            }

            // Calculate totals for this vendor's items
            $subtotal = array_sum(array_column($items, 'total'));
            $taxAmount = 0;
            $shippingAmount = 0;
            $discountAmount = 0;
            $total = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

            // Get order number for this vendor's company
            $orderModel = new Order();
            $orderModel->setTenantId($vendorCompanyId);
            $orderNumber = $orderModel->getNextOrderNumber();

            // Create order in vendor's company - this is where the vendor (admin) will see it
            $orderId = $orderModel->create([
                'company_id' => $vendorCompanyId,
                'customer_id' => $customerId,
                'user_id' => $userId ?: null,
                'order_number' => $orderNumber,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'current_status' => 'pending',
                'shipping_name' => $shippingName,
                'shipping_phone' => $shippingPhone,
                'shipping_address' => $shippingAddress,
                'shipping_city' => $shippingCity,
                'shipping_zip' => $shippingZip,
                'notes' => $notes,
            ]);

            // Create order items and update stock
            foreach ($items as $row) {
                $orderItemModel->create([
                    'order_id' => $orderId,
                    'product_id' => (int) $row['product']['id'],
                    'product_name' => $row['product']['name'],
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'total' => $row['total'],
                ]);
                $productModel->adjustQuantityById((int) $row['product']['id'], -$row['quantity']);
                $stockModel = new StockMovement();
                $stockModel->setTenantId($vendorCompanyId);
                $stockModel->create([
                    'company_id' => $vendorCompanyId,
                    'product_id' => (int) $row['product']['id'],
                    'type' => 'sale',
                    'quantity' => $row['quantity'],
                    'reference_type' => 'order',
                    'reference_id' => $orderId,
                    'notes' => 'Order ' . $orderNumber,
                    'created_by' => $userId ?: null,
                ]);
            }

            // Add initial status history
            $orderModel->updateStatus($orderId, 'pending', 'Order placed by customer', $userId ?: null);
            $lastOrderId = $orderId;
        }

        $_SESSION['cart'] = [];
        $this->redirect($this->baseUrl() . '/store/orders');
    }

    public function myOrders(): void
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        if (!$userId) {
            $this->redirect($this->baseUrl() . '/auth/login');
            return;
        }
        // Get orders from ALL companies where this user placed orders (not just session company)
        $orderModel = new Order();
        $orders = $orderModel->getByUserGlobal($userId);
        $this->view('store.orders', ['pageTitle' => 'My Orders', 'orders' => $orders]);
    }

    public function orderTrack(string $id): void
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        // Get order from any company (customer may have ordered from multiple vendors)
        $orderModel = new Order();
        $order = $orderModel->getWithItemsGlobal((int) $id);
        if (!$order) {
            $this->redirect($this->baseUrl() . '/store/orders');
            return;
        }
        // Only allow the customer who placed the order to view it
        if ($userId && (int) $order['user_id'] !== $userId) {
            $this->redirect($this->baseUrl() . '/store/orders');
            return;
        }
        $this->view('store.track', ['pageTitle' => 'Order ' . $order['order_number'], 'order' => $order]);
    }

    /** Customer can cancel their own pending orders */
    public function orderCancel(string $id): void
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        if (!$userId) {
            $this->redirect($this->baseUrl() . '/auth/login');
            return;
        }
        $orderModel = new Order();
        $order = $orderModel->getWithItemsGlobal((int) $id);
        if (!$order) {
            $this->redirect($this->baseUrl() . '/store/orders');
            return;
        }
        // Only allow the customer who placed the order to cancel it
        if ((int) $order['user_id'] !== $userId) {
            $this->redirect($this->baseUrl() . '/store/orders');
            return;
        }
        // Only allow cancellation of pending orders
        if ($order['current_status'] !== 'pending') {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['order_message'] = 'Only pending orders can be cancelled. This order is already ' . $order['current_status'] . '.';
            $this->redirect($this->baseUrl() . '/store/orders/track/' . $id);
            return;
        }
        // Cancel the order
        $orderModel->setTenantId((int) $order['company_id']);
        $orderModel->updateStatus((int) $id, 'cancelled', 'Cancelled by customer', $userId);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['order_message'] = 'Order ' . $order['order_number'] . ' has been cancelled.';
        $this->redirect($this->baseUrl() . '/store/orders');
    }
}
