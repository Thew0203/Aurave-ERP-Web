<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'BOM';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Bill of Materials</h2>
        <a href="<?= $baseUrl ?>/production/bom/create" class="btn btn-primary">Add BOM</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Name</th><th>Product</th><th>SKU</th><th>Version</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($boms ?? [] as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['name']) ?></td>
                        <td><?= htmlspecialchars($b['product_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($b['sku'] ?? '') ?></td>
                        <td><?= htmlspecialchars($b['version'] ?? '') ?></td>
                        <td><a href="<?= $baseUrl ?>/production/bom/edit/<?= (int)$b['id'] ?>">Edit</a></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($boms)): ?><tr><td colspan="5" class="text-muted">No BOMs.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
