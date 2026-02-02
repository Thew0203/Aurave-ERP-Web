<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Company';
$c = $company ?? null;
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <h2 class="mb-4"><?= $c ? 'Edit Company' : 'Add Company' ?></h2>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="<?= $baseUrl ?>/companies/<?= $c ? 'edit/' . (int)$c['id'] : 'create' ?>">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($c['name'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Slug *</label><input type="text" name="slug" class="form-control" required value="<?= htmlspecialchars($c['slug'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($c['email'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($c['phone'] ?? '') ?>"></div>
                    <div class="col-12"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($c['address'] ?? '') ?></textarea></div>
                    <div class="col-md-6"><label class="form-label">Tax ID</label><input type="text" name="tax_id" class="form-control" value="<?= htmlspecialchars($c['tax_id'] ?? '') ?>"></div>
                    <?php if ($c): ?>
                    <div class="col-12"><div class="form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" <?= !empty($c['is_active']) ? 'checked' : '' ?>><label class="form-check-label">Active</label></div></div>
                    <?php endif; ?>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="<?= $baseUrl ?>/companies" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
