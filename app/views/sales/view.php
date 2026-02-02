<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Sale';
$s = $sale ?? [];
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Sale: <?= htmlspecialchars($s['invoice_number'] ?? '') ?></h2>
        <div>
            <a href="<?= $baseUrl ?>/sales/invoice/<?= (int)($s['id'] ?? 0) ?>" class="btn btn-outline-primary">Invoice</a>
            <a href="<?= $baseUrl ?>/sales" class="btn btn-secondary">Back</a>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <p><strong>Date:</strong> <?= htmlspecialchars($s['sale_date'] ?? '') ?></p>
            <p><strong>Customer:</strong> <?= htmlspecialchars($s['customer_name'] ?? 'Walk-in') ?></p>
            <p><strong>Subtotal:</strong> <?= number_format((float)($s['subtotal'] ?? 0), 2) ?></p>
            <p><strong>Tax:</strong> <?= number_format((float)($s['tax_amount'] ?? 0), 2) ?></p>
            <p><strong>Total:</strong> <?= number_format((float)($s['total'] ?? 0), 2) ?></p>
            <p><strong>Status:</strong> <span class="badge bg-secondary"><?= htmlspecialchars($s['status'] ?? '') ?></span></p>
        </div>
    </div>
    <div class="card border-0 shadow-sm mt-3">
        <div class="card-header bg-white"><h5 class="mb-0">Items</h5></div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead>
                <tbody>
                    <?php foreach ($s['items'] ?? [] as $i): ?>
                    <tr>
                        <td><?= htmlspecialchars($i['product_name'] ?? '') ?></td>
                        <td><?= number_format((float)$i['quantity'], 2) ?></td>
                        <td><?= number_format((float)$i['unit_price'], 2) ?></td>
                        <td><?= number_format((float)$i['total'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
