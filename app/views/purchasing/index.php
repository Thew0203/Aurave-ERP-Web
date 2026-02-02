<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Purchasing';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Purchasing</h2>
        <a href="<?= $baseUrl ?>/purchasing/create" class="btn btn-primary">New Purchase</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Invoice</th><th>Date</th><th>Supplier</th><th>Total</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($purchases ?? [] as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['invoice_number']) ?></td>
                        <td><?= htmlspecialchars($p['purchase_date']) ?></td>
                        <td><?= htmlspecialchars($p['supplier_name'] ?? '') ?></td>
                        <td><?= number_format((float)$p['total'], 2) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($p['status']) ?></span></td>
                        <td><a href="<?= $baseUrl ?>/purchasing/view/<?= (int)$p['id'] ?>">View</a></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($purchases)): ?><tr><td colspan="6" class="text-muted">No purchases.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
