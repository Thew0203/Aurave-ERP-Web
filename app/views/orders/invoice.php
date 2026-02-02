<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$o = $order ?? [];
$companyName = $companyName ?? $config['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order <?= htmlspecialchars($o['order_number'] ?? '') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <div class="row mb-4">
        <div class="col"><h2><?= htmlspecialchars($companyName) ?></h2></div>
        <div class="col text-end"><h3>ORDER INVOICE</h3><p class="mb-0"><?= htmlspecialchars($o['order_number'] ?? '') ?></p></div>
    </div>
    <div class="row mb-4">
        <div class="col">
            <strong>Ship To</strong><br>
            <?= htmlspecialchars($o['shipping_name'] ?? '') ?><br>
            <?= htmlspecialchars($o['shipping_phone'] ?? '') ?><br>
            <?= htmlspecialchars($o['shipping_address'] ?? '') ?>
        </div>
        <div class="col text-end">
            <strong>Date:</strong> <?= date('Y-m-d H:i', strtotime($o['order_date'] ?? '')) ?>
        </div>
    </div>
    <table class="table table-bordered">
        <thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead>
        <tbody>
            <?php foreach ($o['items'] ?? [] as $i): ?>
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
        <p><strong>Subtotal:</strong> <?= number_format((float)($o['subtotal'] ?? 0), 2) ?></p>
        <p><strong>Shipping:</strong> <?= number_format((float)($o['shipping_amount'] ?? 0), 2) ?></p>
        <p><strong>Total:</strong> <?= number_format((float)($o['total'] ?? 0), 2) ?></p>
    </div>
</div>
<script>window.print();</script>
</body>
</html>
