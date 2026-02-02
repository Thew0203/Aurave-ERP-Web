<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Production Orders';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Production Orders</h2>
        <a href="<?= $baseUrl ?>/production/orders/create" class="btn btn-primary">New Order</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Order #</th><th>Product</th><th>BOM</th><th>Qty</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($orders ?? [] as $o): ?>
                    <tr>
                        <td><?= htmlspecialchars($o['order_number']) ?></td>
                        <td><?= htmlspecialchars($o['product_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($o['bom_name'] ?? '') ?></td>
                        <td><?= number_format((float)$o['quantity'], 2) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($o['status']) ?></span></td>
                        <td><a href="<?= $baseUrl ?>/production/orders/view/<?= (int)$o['id'] ?>">View</a></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($orders)): ?><tr><td colspan="6" class="text-muted">No production orders.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
