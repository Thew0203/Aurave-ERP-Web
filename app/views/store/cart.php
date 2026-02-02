<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Cart';
$subtotal = 0;
foreach ($products ?? [] as $p) {
    $subtotal += (float) $p['selling_price'] * (float) ($p['cart_qty'] ?? 0);
}
$cartError = $_SESSION['cart_error'] ?? '';
$cartMessage = $_SESSION['cart_message'] ?? '';
if ($cartError) { unset($_SESSION['cart_error']); }
if ($cartMessage) { unset($_SESSION['cart_message']); }
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container py-4">
    <h2 class="mb-4">Shopping Cart</h2>
    <?php if ($cartError): ?>
    <div class="alert alert-warning"><?= htmlspecialchars($cartError) ?></div>
    <?php endif; ?>
    <?php if ($cartMessage): ?>
    <div class="alert alert-info"><?= htmlspecialchars($cartMessage) ?></div>
    <?php endif; ?>
    <?php if (empty($products)): ?>
    <p class="text-muted">Your cart is empty.</p>
    <a href="<?= $baseUrl ?>/store" class="btn btn-primary">Continue Shopping</a>
    <?php else: ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                    <?php $lineTotal = (float)$p['selling_price'] * (float)($p['cart_qty'] ?? 0); ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($p['name']) ?>
                            <?php if (!empty($p['company_name'])): ?><br><small class="text-muted"><?= htmlspecialchars($p['company_name']) ?></small><?php endif; ?>
                        </td>
                        <td><?= number_format((float)$p['selling_price'], 2) ?></td>
                        <td>
                            <form method="post" action="<?= $baseUrl ?>/store/cart/update" class="d-inline">
                                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                <input type="number" name="quantity" class="form-control form-control-sm d-inline-block w-auto" value="<?= (float)($p['cart_qty'] ?? 0) ?>" min="0.001" max="<?= (float)($p['quantity'] ?? 0) ?>" step="0.001" onchange="this.form.submit()" title="Max in stock: <?= (float)($p['quantity'] ?? 0) ?>">
                            </form>
                            <small class="text-muted">In stock: <?= number_format((float)($p['quantity'] ?? 0), 2) ?></small>
                        </td>
                        <td><?= number_format($lineTotal, 2) ?></td>
                        <td>
                            <form method="post" action="<?= $baseUrl ?>/store/cart/remove" class="d-inline">
                                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            <strong>Subtotal: <?= number_format($subtotal, 2) ?></strong>
            <a href="<?= $baseUrl ?>/store/checkout" class="btn btn-primary float-end">Checkout</a>
        </div>
    </div>
    <a href="<?= $baseUrl ?>/store" class="btn btn-secondary mt-3">Continue Shopping</a>
    <?php endif; ?>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
