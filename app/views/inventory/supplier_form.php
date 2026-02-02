<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Supplier';
$s = $supplier ?? null;
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <h2 class="mb-4"><?= $s ? 'Edit Supplier' : 'Add Supplier' ?></h2>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="<?= $baseUrl ?>/inventory/suppliers/<?= $s ? 'edit/' . (int)$s['id'] : 'create' ?>">
                <div class="mb-3">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($s['name'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($s['email'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($s['phone'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($s['address'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="<?= $baseUrl ?>/inventory/suppliers" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
