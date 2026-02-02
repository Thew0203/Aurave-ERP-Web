<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Stock';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <h2 class="mb-4">Stock Management</h2>
    <p class="text-muted">Total inventory value: <strong><?= number_format($valuation ?? 0, 2) ?></strong></p>
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Products &amp; Stock</h5></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>SKU</th><th>Name</th><th>Qty</th><th>Threshold</th><th>Action</th></tr></thead>
                        <tbody>
                            <?php foreach ($products ?? [] as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['sku'] ?? '') ?></td>
                                <td><?= htmlspecialchars($p['name']) ?></td>
                                <td><?= number_format((float)$p['quantity'], 2) ?></td>
                                <td><?= number_format((float)$p['low_stock_threshold'], 2) ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#movementModal" data-product-id="<?= (int)$p['id'] ?>" data-product-name="<?= htmlspecialchars($p['name']) ?>">Stock In/Out</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><h5 class="mb-0">Recent Movements</h5></div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php foreach (array_slice($recentMovements ?? [], 0, 15) as $m): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><?= htmlspecialchars($m['product_name'] ?? '') ?> <span class="badge bg-<?= $m['type'] === 'in' ? 'success' : ($m['type'] === 'out' ? 'danger' : 'secondary') ?>"><?= htmlspecialchars($m['type']) ?></span> <?= number_format((float)$m['quantity'], 2) ?></span>
                            <small class="text-muted"><?= date('M j, H:i', strtotime($m['created_at'])) ?></small>
                        </li>
                        <?php endforeach; ?>
                        <?php if (empty($recentMovements)): ?><li class="list-group-item text-muted">No movements.</li><?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="movementModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= $baseUrl ?>/inventory/stock-movement">
                <div class="modal-header">
                    <h5 class="modal-title">Stock Movement - <span id="movementProductName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="product_id" id="movementProductId">
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="in">Stock In</option>
                            <option value="out">Stock Out</option>
                            <option value="adjustment">Adjustment</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" step="0.001" name="quantity" class="form-control" required min="0.001">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.getElementById('movementModal').addEventListener('show.bs.modal', function(e) {
    var btn = e.relatedTarget;
    document.getElementById('movementProductId').value = btn.getAttribute('data-product-id');
    document.getElementById('movementProductName').textContent = btn.getAttribute('data-product-name');
});
</script>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
