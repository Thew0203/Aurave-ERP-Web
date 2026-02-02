<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$s = $sale ?? [];
$companyName = $companyName ?? $config['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice <?= htmlspecialchars($s['invoice_number'] ?? '') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <div class="row mb-4">
        <div class="col"><h2><?= htmlspecialchars($companyName) ?></h2></div>
        <div class="col text-end"><h3>INVOICE</h3><p class="mb-0"><?= htmlspecialchars($s['invoice_number'] ?? '') ?></p></div>
    </div>
    <div class="row mb-4">
        <div class="col">
            <strong>Bill To</strong><br>
            <?= htmlspecialchars($s['customer_name'] ?? 'Walk-in Customer') ?>
        </div>
        <div class="col text-end">
            <strong>Date:</strong> <?= htmlspecialchars($s['sale_date'] ?? '') ?>
        </div>
    </div>
    <table class="table table-bordered">
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
    <div class="text-end">
        <p><strong>Subtotal:</strong> <?= number_format((float)($s['subtotal'] ?? 0), 2) ?></p>
        <p><strong>Tax:</strong> <?= number_format((float)($s['tax_amount'] ?? 0), 2) ?></p>
        <p><strong>Total:</strong> <?= number_format((float)($s['total'] ?? 0), 2) ?></p>
    </div>
</div>
<script>window.print();</script>
</body>
</html>
