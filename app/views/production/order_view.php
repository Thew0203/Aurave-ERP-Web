<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Production Order';
$o = $order ?? [];
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Production Order: <?= htmlspecialchars($o['order_number'] ?? '') ?></h2>
        <a href="<?= $baseUrl ?>/production/orders" class="btn btn-secondary">Back</a>
    </div>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <p><strong>Product:</strong> <?= htmlspecialchars($o['product_name'] ?? '') ?></p>
            <p><strong>Quantity:</strong> <?= number_format((float)($o['quantity'] ?? 0), 2) ?></p>
            <p><strong>Status:</strong> <span class="badge bg-secondary"><?= htmlspecialchars($o['status'] ?? '') ?></span></p>
            <?php if (!empty($o['started_at'])): ?><p><strong>Started:</strong> <?= htmlspecialchars($o['started_at']) ?></p><?php endif; ?>
            <?php if (!empty($o['completed_at'])): ?><p><strong>Completed:</strong> <?= htmlspecialchars($o['completed_at']) ?></p><?php endif; ?>
            <form method="post" action="<?= $baseUrl ?>/production/orders/status/<?= (int)($o['id'] ?? 0) ?>" class="mt-3 d-inline">
                <input type="hidden" name="status" id="newStatus">
                <?php if (in_array($o['status'] ?? '', ['draft', 'confirmed'])): ?>
                <button type="submit" class="btn btn-primary btn-sm" onclick="document.getElementById('newStatus').value='in_progress'">Start Production</button>
                <?php endif; ?>
                <?php if (($o['status'] ?? '') === 'in_progress'): ?>
                <button type="submit" class="btn btn-success btn-sm" onclick="document.getElementById('newStatus').value='completed'">Complete</button>
                <?php endif; ?>
                <?php if (in_array($o['status'] ?? '', ['draft'])): ?>
                <button type="submit" class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('newStatus').value='confirmed'">Confirm</button>
                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="document.getElementById('newStatus').value='cancelled'">Cancel</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <?php if (!empty($o['consumption'])): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white"><h5 class="mb-0">Material Consumption</h5></div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Product</th><th>Quantity</th></tr></thead>
                <tbody>
                    <?php foreach ($o['consumption'] as $c): ?>
                    <tr><td><?= htmlspecialchars($c['product_name'] ?? '') ?></td><td><?= number_format((float)$c['quantity'], 2) ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
