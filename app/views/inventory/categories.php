<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Categories';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <h2 class="mb-4">Categories</h2>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="<?= $baseUrl ?>/inventory/categories" class="row g-2 align-items-end mb-4">
                <div class="col-auto">
                    <input type="hidden" name="id" id="catId">
                    <input type="text" name="name" id="catName" class="form-control" placeholder="Category name" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Add / Update</button>
                </div>
            </form>
            <table class="table table-hover mb-0">
                <thead><tr><th>ID</th><th>Name</th><th>Slug</th></tr></thead>
                <tbody>
                    <?php foreach ($categories ?? [] as $c): ?>
                    <tr>
                        <td><?= (int)$c['id'] ?></td>
                        <td><?= htmlspecialchars($c['name']) ?></td>
                        <td><?= htmlspecialchars($c['slug']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($categories)): ?><tr><td colspan="3" class="text-muted">No categories.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
