<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Product';
$p = $product ?? null;
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <h2 class="mb-4"><?= $p ? 'Edit Product' : 'Add Product' ?></h2>
    <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="<?= $baseUrl ?>/inventory/products/<?= $p ? 'edit/' . (int)$p['id'] : 'create' ?>" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Product Image</label>
                        <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                        <small class="text-muted">JPEG, PNG, GIF or WebP. Max 2MB.</small>
                        <?php if ($p && !empty($p['image_url'])): ?>
                        <div class="mt-2">
                            <img src="<?= $baseUrl . '/' . htmlspecialchars($p['image_url']) ?>" alt="Current" class="img-thumbnail" style="max-height:120px;">
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" class="form-control" value="<?= htmlspecialchars($p['sku'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($p['name'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($p['description'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Category</label>
                        <input type="text" name="category_name" class="form-control" value="<?= htmlspecialchars($p['category_name'] ?? '') ?>" placeholder="e.g. Raw Materials, Arduino">
                        <small class="text-muted">Type name; leave blank for none. New category is created on save.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Supplier</label>
                        <input type="text" name="supplier_name" class="form-control" value="<?= htmlspecialchars($p['supplier_name'] ?? '') ?>" placeholder="e.g. Company Name or ID">
                        <small class="text-muted">Type name; leave blank for none. New supplier is created on save.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Unit</label>
                        <input type="text" name="unit" class="form-control" value="<?= htmlspecialchars($p['unit'] ?? 'pcs') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cost Price</label>
                        <input type="number" step="0.01" min="0" name="cost_price" class="form-control" value="<?= htmlspecialchars($p['cost_price'] ?? '0') ?>" title="Cost to you (for margin calculation)">
                        <small class="text-muted">Used for inventory value & margin.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Selling Price</label>
                        <input type="number" step="0.01" min="0" name="selling_price" class="form-control" value="<?= htmlspecialchars($p['selling_price'] ?? '0') ?>" title="Price shown in store & sales">
                        <small class="text-muted">Used in store, cart, checkout & sales.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Quantity</label>
                        <input type="number" step="0.001" min="0" name="quantity" class="form-control" value="<?= htmlspecialchars($p['quantity'] ?? '0') ?>" title="Current stock (decremented on sale, incremented on purchase)">
                        <small class="text-muted">Decremented on order/sale; incremented on purchase/stock-in.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Low Stock Threshold</label>
                        <input type="number" step="0.001" min="0" name="low_stock_threshold" class="form-control" value="<?= htmlspecialchars($p['low_stock_threshold'] ?? '5') ?>" title="Alert when quantity is at or below this">
                        <small class="text-muted">Shows in Low Stock Alerts when quantity ≤ this.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label d-block">Options</label>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" name="is_raw_material" value="1" class="form-check-input" <?= ($p && !empty($p['is_raw_material'])) ? 'checked' : '' ?>>
                            <label class="form-check-label">Raw Material</label>
                        </div>
                        <small class="text-muted d-block mt-1">If checked, product is <strong>hidden from the store</strong> (BOM/production only). Uncheck to show in store.</small>
                        <?php if ($p): ?>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" <?= empty($p['is_active']) ? '' : 'checked' ?>>
                            <label class="form-check-label">Active</label>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="<?= $baseUrl ?>/inventory/products" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
