<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Products';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold animate-fade-in">Products</h2>
        <a href="<?= $baseUrl ?>/inventory/products/create" class="btn btn-primary">Add Product</a>
    </div>
    <div class="card border-0 shadow-sm animate-slide-up">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Image</th><th>SKU</th><th>Name</th><th>Category</th><th>Cost</th><th>Price</th><th>Qty</th><th>Low</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($products ?? [] as $p): ?>
                    <tr>
                        <td>
                            <?php if (!empty($p['image_url'])): ?>
                            <img src="<?= $baseUrl . '/' . htmlspecialchars($p['image_url']) ?>" alt="" class="rounded" style="height:40px;width:40px;object-fit:cover;">
                            <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($p['sku'] ?? '') ?></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= htmlspecialchars($p['category_name'] ?? '-') ?></td>
                        <td><?= number_format((float)$p['cost_price'], 2) ?></td>
                        <td><?= number_format((float)$p['selling_price'], 2) ?></td>
                        <td><?= number_format((float)$p['quantity'], 2) ?></td>
                        <td><?= number_format((float)$p['low_stock_threshold'], 2) ?></td>
                        <td>
                            <a href="<?= $baseUrl ?>/inventory/products/edit/<?= (int)$p['id'] ?>">Edit</a>
                            <form method="post" action="<?= $baseUrl ?>/inventory/products/delete/<?= (int)$p['id'] ?>" class="d-inline" onsubmit="return confirm('Delete this product?');">
                                <button type="submit" class="btn btn-link text-danger p-0">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($products)): ?><tr><td colspan="9" class="text-muted">No products.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
