<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Low Stock';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <h2 class="mb-4">Low Stock Alerts</h2>
    <p class="text-muted">Inventory value: <strong><?= number_format($valuation ?? 0, 2) ?></strong></p>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>SKU</th><th>Product</th><th>Current Qty</th><th>Threshold</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($products ?? [] as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['sku'] ?? '') ?></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td class="text-danger"><?= number_format((float)$p['quantity'], 2) ?></td>
                        <td><?= number_format((float)$p['low_stock_threshold'], 2) ?></td>
                        <td><a href="<?= $baseUrl ?>/inventory/stock">Adjust Stock</a></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($products)): ?><tr><td colspan="5" class="text-muted">No low stock items.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
