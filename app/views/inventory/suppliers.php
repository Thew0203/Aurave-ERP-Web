<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Suppliers';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Suppliers</h2>
        <a href="<?= $baseUrl ?>/inventory/suppliers/create" class="btn btn-primary">Add Supplier</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($suppliers ?? [] as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['name']) ?></td>
                        <td><?= htmlspecialchars($s['email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($s['phone'] ?? '') ?></td>
                        <td><a href="<?= $baseUrl ?>/inventory/suppliers/edit/<?= (int)$s['id'] ?>">Edit</a></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($suppliers)): ?><tr><td colspan="4" class="text-muted">No suppliers.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
