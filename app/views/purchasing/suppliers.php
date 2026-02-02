<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Suppliers';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <h2 class="mb-4">Suppliers</h2>
    <p><a href="<?= $baseUrl ?>/inventory/suppliers">Manage suppliers in Inventory</a></p>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Name</th><th>Email</th><th>Phone</th></tr></thead>
                <tbody>
                    <?php foreach ($suppliers ?? [] as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['name']) ?></td>
                        <td><?= htmlspecialchars($s['email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($s['phone'] ?? '') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($suppliers)): ?><tr><td colspan="3" class="text-muted">No suppliers.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
