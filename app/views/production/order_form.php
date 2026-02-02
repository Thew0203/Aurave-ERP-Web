<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'New Production Order';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <h2 class="mb-4">New Production Order</h2>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="<?= $baseUrl ?>/production/orders/create">
                <div class="mb-3">
                    <label class="form-label">Order Number</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($nextNumber ?? '') ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">BOM *</label>
                    <select name="bom_header_id" class="form-select" required>
                        <option value="">-- Select BOM --</option>
                        <?php foreach ($boms ?? [] as $b): ?>
                        <option value="<?= (int)$b['id'] ?>"><?= htmlspecialchars($b['name']) ?> (<?= htmlspecialchars($b['product_name'] ?? '') ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quantity *</label>
                    <input type="number" step="0.001" name="quantity" class="form-control" required min="0.001">
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
                <a href="<?= $baseUrl ?>/production/orders" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
