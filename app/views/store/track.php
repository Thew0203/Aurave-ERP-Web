<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Order';
$o = $order ?? [];
$orderMessage = $_SESSION['order_message'] ?? '';
if ($orderMessage) { unset($_SESSION['order_message']); }
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container py-4">
    <?php if ($orderMessage): ?>
    <div class="alert alert-info"><?= htmlspecialchars($orderMessage) ?></div>
    <?php elseif (($o['current_status'] ?? '') === 'pending'): ?>
    <div class="alert alert-success">Thank you! Your order has been placed and is awaiting confirmation from the vendor.</div>
    <?php elseif (($o['current_status'] ?? '') === 'cancelled'): ?>
    <div class="alert alert-warning">This order has been cancelled.</div>
    <?php elseif (($o['current_status'] ?? '') === 'delivered'): ?>
    <div class="alert alert-success">This order has been delivered!</div>
    <?php else: ?>
    <div class="alert alert-info">Your order is being processed. Status: <?= htmlspecialchars($o['current_status'] ?? '') ?></div>
    <?php endif; ?>
    <h2 class="mb-4">Order: <?= htmlspecialchars($o['order_number'] ?? '') ?></h2>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <p><strong>Vendor:</strong> <?= htmlspecialchars($o['company_name'] ?? 'Unknown') ?></p>
            <p><strong>Status:</strong> <span class="badge bg-<?= ($o['current_status'] ?? '') === 'cancelled' ? 'danger' : (($o['current_status'] ?? '') === 'delivered' ? 'success' : 'secondary') ?>"><?= htmlspecialchars($o['current_status'] ?? '') ?></span></p>
            <p><strong>Total:</strong> <?= number_format((float)($o['total'] ?? 0), 2) ?></p>
            <p><strong>Shipping to:</strong> <?= htmlspecialchars($o['shipping_name'] ?? '') ?>, <?= htmlspecialchars($o['shipping_address'] ?? '') ?></p>
            <?php if (($o['current_status'] ?? '') === 'pending'): ?>
            <form method="post" action="<?= $baseUrl ?>/store/orders/cancel/<?= (int)($o['id'] ?? 0) ?>" class="mt-3" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                <button type="submit" class="btn btn-danger">Cancel Order</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white"><h5 class="mb-0">Items</h5></div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                <tbody>
                    <?php foreach ($o['items'] ?? [] as $i): ?>
                    <tr>
                        <td><?= htmlspecialchars($i['product_name'] ?? '') ?></td>
                        <td><?= number_format((float)$i['quantity'], 2) ?></td>
                        <td><?= number_format((float)$i['unit_price'], 2) ?></td>
                        <td><?= number_format((float)$i['total'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (!empty($o['history'])): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white"><h5 class="mb-0">Order History</h5></div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                <?php foreach ($o['history'] as $h): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        <span class="badge bg-<?= $h['status'] === 'cancelled' ? 'danger' : ($h['status'] === 'delivered' ? 'success' : 'secondary') ?>"><?= htmlspecialchars($h['status']) ?></span>
                        <?php if (!empty($h['notes'])): ?>
                        <small class="text-muted ms-2"><?= htmlspecialchars($h['notes']) ?></small>
                        <?php endif; ?>
                    </span>
                    <small class="text-muted"><?= date('Y-m-d H:i', strtotime($h['created_at'])) ?></small>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>
    <a href="<?= $baseUrl ?>/store/orders" class="btn btn-primary mt-3">My Orders</a>
    <a href="<?= $baseUrl ?>/store" class="btn btn-secondary mt-3">Continue Shopping</a>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
