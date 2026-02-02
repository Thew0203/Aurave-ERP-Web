<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'New Purchase';
$productsJson = json_encode(array_map(function($p) { return ['id' => $p['id'], 'name' => $p['name'], 'sku' => $p['sku'] ?? '', 'cost' => (float)$p['cost_price']]; }, $products ?? []));
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <h2 class="mb-4">New Purchase</h2>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="<?= $baseUrl ?>/purchasing/create">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Invoice #</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($nextInvoice ?? '') ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date</label>
                        <input type="date" name="purchase_date" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Supplier *</label>
                        <select name="supplier_id" class="form-select" required>
                            <option value="">-- Select --</option>
                            <?php foreach ($suppliers ?? [] as $s): ?>
                            <option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <h5 class="mb-2">Line Items</h5>
                <table class="table table-bordered" id="purchaseItemsTable">
                    <thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Total</th><th></th></tr></thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="items[][product_id]" class="form-select form-select-sm purchase-product">
                                    <option value="">-- Select --</option>
                                    <?php foreach ($products ?? [] as $p): ?>
                                    <option value="<?= (int)$p['id'] ?>" data-cost="<?= (float)$p['cost_price'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" step="0.001" name="items[][quantity]" class="form-control form-control-sm purchase-qty" min="0.001"></td>
                            <td><input type="number" step="0.01" name="items[][unit_price]" class="form-control form-control-sm purchase-price"></td>
                            <td class="purchase-row-total">0.00</td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger remove-purchase-row">Remove</button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="addPurchaseRow">+ Add Line</button>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Purchase</button>
                <a href="<?= $baseUrl ?>/purchasing" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
<script>
var products = <?= $productsJson ?>;
function addPurchaseRow() {
    var tbody = document.querySelector('#purchaseItemsTable tbody');
    var opt = products.map(function(p) { return '<option value="' + p.id + '" data-cost="' + p.cost + '">' + p.name + '</option>'; }).join('');
    var row = '<tr><td><select name="items[][product_id]" class="form-select form-select-sm purchase-product"><option value="">--</option>' + opt + '</select></td><td><input type="number" step="0.001" name="items[][quantity]" class="form-control form-control-sm purchase-qty" min="0.001"></td><td><input type="number" step="0.01" name="items[][unit_price]" class="form-control form-control-sm purchase-price"></td><td class="purchase-row-total">0.00</td><td><button type="button" class="btn btn-sm btn-outline-danger remove-purchase-row">Remove</button></td></tr>';
    tbody.insertAdjacentHTML('beforeend', row);
    bindPurchaseRow(tbody.lastElementChild);
}
function bindPurchaseRow(row) {
    if (!row) return;
    var sel = row.querySelector('.purchase-product');
    var qty = row.querySelector('.purchase-qty');
    var price = row.querySelector('.purchase-price');
    var totalCell = row.querySelector('.purchase-row-total');
    function update() {
        var p = parseFloat(price.value) || parseFloat(sel.options[sel.selectedIndex].getAttribute('data-cost')) || 0;
        var q = parseFloat(qty.value) || 0;
        totalCell.textContent = (p * q).toFixed(2);
        if (price.value === '') price.value = p;
    }
    sel.addEventListener('change', function() { price.value = this.options[this.selectedIndex].getAttribute('data-cost'); update(); });
    qty.addEventListener('input', update);
    price.addEventListener('input', update);
    row.querySelector('.remove-purchase-row').addEventListener('click', function() { row.remove(); });
}
document.querySelectorAll('#purchaseItemsTable tbody tr').forEach(bindPurchaseRow);
document.getElementById('addPurchaseRow').addEventListener('click', addPurchaseRow);
</script>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
