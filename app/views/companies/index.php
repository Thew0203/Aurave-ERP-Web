<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Companies';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Companies (Tenants)</h2>
        <a href="<?= $baseUrl ?>/companies/create" class="btn btn-primary">Add Company</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>ID</th><th>Name</th><th>Slug</th><th>Email</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($companies ?? [] as $c): ?>
                    <tr>
                        <td><?= (int)$c['id'] ?></td>
                        <td><?= htmlspecialchars($c['name']) ?></td>
                        <td><?= htmlspecialchars($c['slug']) ?></td>
                        <td><?= htmlspecialchars($c['email'] ?? '') ?></td>
                        <td><a href="<?= $baseUrl ?>/companies/edit/<?= (int)$c['id'] ?>">Edit</a></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($companies)): ?><tr><td colspan="5" class="text-muted">No companies.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
