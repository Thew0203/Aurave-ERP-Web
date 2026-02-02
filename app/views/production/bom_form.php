<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'BOM';
$bom = $bom ?? null;
$items = $bom['items'] ?? [];
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <h2 class="mb-4"><?= $bom ? 'Edit BOM' : 'Add BOM' ?></h2>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="<?= $baseUrl ?>/production/bom/<?= $bom ? 'edit/' . (int)$bom['id'] : 'create' ?>">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Finished Product *</label>
                        <select name="product_id" class="form-select" required <?= $bom ? 'disabled' : '' ?>>
                            <option value="">--</option>
                            <?php foreach ($products ?? [] as $p): ?>
                            <option value="<?= (int)$p['id'] ?>" <?= ($bom && (int)$bom['product_id'] === (int)$p['id']) ? 'selected' : '' ?>><?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['sku']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($bom): ?><input type="hidden" name="product_id" value="<?= (int)$bom['product_id'] ?>"><?php endif; ?>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($bom['name'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Version</label>
                        <input type="text" name="version" class="form-control" value="<?= htmlspecialchars($bom['version'] ?? '1.0') ?>">
                    </div>
                    <?php if ($bom): ?>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" <?= !empty($bom['is_active']) ? 'checked' : '' ?>>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <h5 class="mb-2">BOM Items (Raw Materials)</h5>
                <table class="table table-bordered" id="bomItemsTable">
                    <thead><tr><th>Product</th><th>Quantity</th><th>Unit</th><th></th></tr></thead>
                    <tbody>
                        <?php foreach ($items as $i): ?>
                        <tr>
                            <td>
                                <select name="bom_items[][product_id]" class="form-select form-select-sm">
                                    <?php foreach ($rawMaterials ?? [] as $r): ?>
                                    <option value="<?= (int)$r['id'] ?>" <?= (int)($i['product_id'] ?? 0) === (int)$r['id'] ? 'selected' : '' ?>><?= htmlspecialchars($r['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" step="0.001" name="bom_items[][quantity]" class="form-control form-control-sm" value="<?= htmlspecialchars($i['quantity'] ?? '') ?>"></td>
                            <td><input type="text" name="bom_items[][unit]" class="form-control form-control-sm" value="<?= htmlspecialchars($i['unit'] ?? 'pcs') ?>"></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger remove-row">Remove</button></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($items)): ?>
                        <tr>
                            <td>
                                <select name="bom_items[][product_id]" class="form-select form-select-sm">
                                    <option value="">--</option>
                                    <?php foreach ($rawMaterials ?? [] as $r): ?>
                                    <option value="<?= (int)$r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" step="0.001" name="bom_items[][quantity]" class="form-control form-control-sm" placeholder="Qty"></td>
                            <td><input type="text" name="bom_items[][unit]" class="form-control form-control-sm" value="pcs"></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger remove-row">Remove</button></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="addBomRow">+ Add Row</button>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="<?= $baseUrl ?>/production/bom" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
var rawOptions = <?= json_encode(array_map(function($r) { return ['id' => $r['id'], 'name' => $r['name']]; }, $rawMaterials ?? [])) ?>;
document.getElementById('addBomRow').addEventListener('click', function() {
    var tbody = document.querySelector('#bomItemsTable tbody');
    var opt = rawOptions.map(function(r) { return '<option value="' + r.id + '">' + r.name + '</option>'; }).join('');
    var row = '<tr><td><select name="bom_items[][product_id]" class="form-select form-select-sm"><option value="">--</option>' + opt + '</select></td><td><input type="number" step="0.001" name="bom_items[][quantity]" class="form-control form-control-sm"></td><td><input type="text" name="bom_items[][unit]" class="form-control form-control-sm" value="pcs"></td><td><button type="button" class="btn btn-sm btn-outline-danger remove-row">Remove</button></td></tr>';
    tbody.insertAdjacentHTML('beforeend', row);
});
document.getElementById('bomItemsTable').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-row')) e.target.closest('tr').remove();
});
</script>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
