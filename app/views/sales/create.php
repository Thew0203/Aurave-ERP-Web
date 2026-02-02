<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'New Sale';
$productsJson = json_encode(array_map(function($p) { return ['id' => $p['id'], 'name' => $p['name'], 'sku' => $p['sku'] ?? '', 'price' => (float)$p['selling_price']]; }, $products ?? []));
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <h2 class="mb-4">New Sale (POS)</h2>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="<?= $baseUrl ?>/sales/create">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Invoice #</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($nextInvoice ?? '') ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date</label>
                        <input type="date" name="sale_date" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-select">
                            <option value="">Walk-in</option>
                            <?php foreach ($customers ?? [] as $c): ?>
                            <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <h5 class="mb-2">Line Items</h5>
                <table class="table table-bordered" id="saleItemsTable">
                    <thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Total</th><th></th></tr></thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="items[][product_id]" class="form-select form-select-sm sale-product" required>
                                    <option value="">-- Select --</option>
                                    <?php foreach ($products ?? [] as $p): ?>
                                    <option value="<?= (int)$p['id'] ?>" data-price="<?= (float)$p['selling_price'] ?>"><?= htmlspecialchars($p['name']) ?> (<?= number_format((float)$p['selling_price'], 2) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" step="0.001" name="items[][quantity]" class="form-control form-control-sm sale-qty" required min="0.001"></td>
                            <td><input type="number" step="0.01" name="items[][unit_price]" class="form-control form-control-sm sale-price"></td>
                            <td class="sale-row-total">0.00</td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger remove-sale-row">Remove</button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="addSaleRow">+ Add Line</button>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Sale</button>
                <a href="<?= $baseUrl ?>/sales" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
<script>
var products = <?= $productsJson ?>;
function addSaleRow() {
    var tbody = document.querySelector('#saleItemsTable tbody');
    var opt = products.map(function(p) { return '<option value="' + p.id + '" data-price="' + p.price + '">' + p.name + ' (' + p.price.toFixed(2) + ')</option>'; }).join('');
    var row = '<tr><td><select name="items[][product_id]" class="form-select form-select-sm sale-product"><option value="">-- Select --</option>' + opt + '</select></td><td><input type="number" step="0.001" name="items[][quantity]" class="form-control form-control-sm sale-qty" min="0.001"></td><td><input type="number" step="0.01" name="items[][unit_price]" class="form-control form-control-sm sale-price"></td><td class="sale-row-total">0.00</td><td><button type="button" class="btn btn-sm btn-outline-danger remove-sale-row">Remove</button></td></tr>';
    tbody.insertAdjacentHTML('beforeend', row);
    bindSaleRow(tbody.lastElementChild);
}
function bindSaleRow(row) {
    if (!row) return;
    var sel = row.querySelector('.sale-product');
    var qty = row.querySelector('.sale-qty');
    var price = row.querySelector('.sale-price');
    var totalCell = row.querySelector('.sale-row-total');
    function update() {
        var p = parseFloat(price.value) || parseFloat(sel.options[sel.selectedIndex].getAttribute('data-price')) || 0;
        var q = parseFloat(qty.value) || 0;
        totalCell.textContent = (p * q).toFixed(2);
        if (price.value === '') price.value = p;
    }
    sel.addEventListener('change', function() { price.value = this.options[this.selectedIndex].getAttribute('data-price'); update(); });
    qty.addEventListener('input', update);
    price.addEventListener('input', update);
    row.querySelector('.remove-sale-row').addEventListener('click', function() { row.remove(); });
}
document.querySelectorAll('#saleItemsTable tbody tr').forEach(bindSaleRow);
document.getElementById('addSaleRow').addEventListener('click', addSaleRow);
</script>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
