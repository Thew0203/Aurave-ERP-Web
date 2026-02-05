<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$o = $order ?? [];
$companyName = $companyName ?? $config['name'];
$embed = isset($_GET['embed']) && $_GET['embed'] === '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Invoice - <?= htmlspecialchars($o['order_number'] ?? '') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @media print { .no-print, .area-a, .area-b, .area-c { display: none !important; } body { background: #fff !important; } .invoice-card { box-shadow: none !important; border: 1px solid #dee2e6 !important; } .invoice-wrap { max-width: 100% !important; } }
        @media (max-width: 900px) { .area-a, .area-c { display: none !important; } .invoice-card { border-radius: 12px !important; } }
        body { background: #e8eef4; min-height: 100vh; padding: 1.5rem 0; margin: 0; font-family: system-ui, sans-serif; }
        .invoice-wrap { max-width: 960px; margin: 0 auto; display: flex; gap: 0; flex-wrap: wrap; }
        .area-a { width: 140px; flex-shrink: 0; background: linear-gradient(180deg, #1e3a5f 0%, #2563eb 100%); color: #fff; border-radius: 12px 0 0 12px; padding: 1.25rem 0.75rem; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; }
        .area-a .side-label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: .9; margin-bottom: 0.5rem; }
        .area-a .side-value { font-weight: 600; font-size: 0.85rem; text-align: center; }
        .area-a .divider { width: 60%; height: 1px; background: rgba(255,255,255,.3); margin: 0.75rem 0; }
        .invoice-main { flex: 1; min-width: 0; }
        .invoice-card { background: #fff; border-radius: 0 12px 12px 0; box-shadow: 0 4px 20px rgba(0,0,0,.08); overflow: hidden; }
        .invoice-header { background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%); color: #fff; padding: 1.5rem 2rem; }
        .invoice-header h1 { font-size: 1.5rem; font-weight: 700; margin: 0; }
        .invoice-header .badge { background: rgba(255,255,255,.25); font-size: 0.75rem; padding: 0.35rem 0.65rem; }
        .invoice-body { padding: 2rem; }
        .ship-block { background: #f8fafc; border-radius: 8px; padding: 1rem 1.25rem; border-left: 4px solid #2563eb; }
        .ship-block strong { color: #475569; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .table { margin-bottom: 0; }
        .table thead th { background: #f1f5f9; font-weight: 600; color: #334155; border-bottom: 2px solid #e2e8f0; }
        .totals-block { background: #f8fafc; border-radius: 8px; padding: 1rem 1.5rem; max-width: 280px; margin-left: auto; }
        .totals-block .total-row { font-size: 1.1rem; font-weight: 700; color: #1e3a5f; border-top: 2px solid #e2e8f0; padding-top: 0.5rem; margin-top: 0.25rem; }
        .invoice-footer { padding: 1rem 2rem; background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 0.875rem; color: #64748b; text-align: center; }
        .area-c { width: 140px; flex-shrink: 0; background: linear-gradient(180deg, #2563eb 0%, #1e3a5f 100%); color: #fff; border-radius: 0 12px 12px 0; padding: 1.25rem 0.75rem; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; }
        .area-c .side-label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: .9; margin-bottom: 0.35rem; }
        .area-c .side-value { font-weight: 600; font-size: 0.8rem; text-align: center; }
        .area-c .divider { width: 60%; height: 1px; background: rgba(255,255,255,.3); margin: 0.6rem 0; }
        .area-b { width: 100%; background: linear-gradient(90deg, #1e3a5f 0%, #2563eb 50%, #1e3a5f 100%); color: #fff; padding: 0.75rem 1.5rem; border-radius: 0 0 12px 12px; display: flex; align-items: center; justify-content: center; gap: 1.5rem; flex-wrap: wrap; font-size: 0.8rem; }
        .area-b i { opacity: .9; }
        .area-b .item { display: flex; align-items: center; gap: 0.35rem; }
        .no-print { text-align: center; margin-top: 0.75rem; }
    </style>
</head>
<body>
<div class="invoice-wrap">
    <!-- Area A: Left sidebar -->
    <div class="area-a no-print">
        <span class="side-label">Order</span>
        <span class="side-value"><?= htmlspecialchars($o['order_number'] ?? '—') ?></span>
        <div class="divider"></div>
        <span class="side-label">Status</span>
        <span class="side-value"><?= htmlspecialchars($o['current_status'] ?? '—') ?></span>
        <div class="divider"></div>
        <span class="side-label">Items</span>
        <span class="side-value"><?= count($o['items'] ?? []) ?></span>
    </div>
    <!-- Main invoice -->
    <div class="invoice-main">
        <div class="invoice-card">
            <div class="invoice-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h1><?= htmlspecialchars($companyName) ?></h1>
                    <span class="badge">Vendor Invoice</span>
                </div>
                <div class="text-end">
                    <div class="h5 mb-0">ORDER INVOICE</div>
                    <div class="opacity-90"><?= htmlspecialchars($o['order_number'] ?? '') ?></div>
                    <small>Date: <?= date('M j, Y H:i', strtotime($o['order_date'] ?? 'now')) ?></small>
                </div>
            </div>
            <div class="invoice-body">
                <div class="row mb-4">
                    <div class="col-md-7">
                        <div class="ship-block">
                            <strong>Ship To</strong>
                            <div class="mt-1">
                                <?= htmlspecialchars($o['shipping_name'] ?? '—') ?><br>
                                <?= htmlspecialchars($o['shipping_phone'] ?? '') ?><br>
                                <?= htmlspecialchars($o['shipping_address'] ?? '') ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <strong>Order Date</strong><br>
                        <?= date('F j, Y', strtotime($o['order_date'] ?? '')) ?><br>
                        <small class="text-muted"><?= date('H:i', strtotime($o['order_date'] ?? '')) ?></small>
                    </div>
                </div>
                <table class="table table-bordered align-middle">
                    <thead><tr><th>Product</th><th class="text-center">Qty</th><th class="text-end">Unit Price</th><th class="text-end">Total</th></tr></thead>
                    <tbody>
                        <?php foreach ($o['items'] ?? [] as $i): ?>
                        <tr>
                            <td><?= htmlspecialchars($i['product_name'] ?? '') ?></td>
                            <td class="text-center"><?= number_format((float)$i['quantity'], 2) ?></td>
                            <td class="text-end"><?= number_format((float)$i['unit_price'], 2) ?></td>
                            <td class="text-end"><?= number_format((float)$i['total'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="totals-block">
                    <div class="d-flex justify-content-between"><span>Subtotal</span><span><?= number_format((float)($o['subtotal'] ?? 0), 2) ?></span></div>
                    <div class="d-flex justify-content-between"><span>Shipping</span><span><?= number_format((float)($o['shipping_amount'] ?? 0), 2) ?></span></div>
                    <?php if (!empty($o['tax_amount']) && (float)$o['tax_amount'] != 0): ?>
                    <div class="d-flex justify-content-between"><span>Tax</span><span><?= number_format((float)$o['tax_amount'], 2) ?></span></div>
                    <?php endif; ?>
                    <?php if (!empty($o['discount_amount']) && (float)$o['discount_amount'] != 0): ?>
                    <div class="d-flex justify-content-between"><span>Discount</span><span>-<?= number_format((float)$o['discount_amount'], 2) ?></span></div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between total-row"><span>Total</span><span><?= number_format((float)($o['total'] ?? 0), 2) ?></span></div>
                </div>
            </div>
            <div class="invoice-footer">
                Thank you for your order. — <?= htmlspecialchars($companyName) ?>
            </div>
        </div>
        <!-- Area B: Bottom bar -->
        <div class="area-b no-print">
            <span class="item"><i class="bi bi-shield-check"></i> Secure order</span>
            <span class="item"><i class="bi bi-truck"></i> Vendor / Seller</span>
            <span class="item"><i class="bi bi-receipt"></i> <?= htmlspecialchars($companyName) ?></span>
        </div>
    </div>
    <!-- Area C: Right sidebar -->
    <div class="area-c no-print">
        <span class="side-label">Payment</span>
        <span class="side-value"><?= htmlspecialchars($o['payment_status'] ?? '—') ?></span>
        <div class="divider"></div>
        <span class="side-label">Total</span>
        <span class="side-value"><?= number_format((float)($o['total'] ?? 0), 2) ?></span>
    </div>
</div>
<div class="no-print text-center mt-2">
    <a href="javascript:window.print()" class="btn btn-primary btn-sm">Print invoice</a>
</div>
<?php if (!$embed): ?>
<script>window.onload = function() { window.print(); }</script>
<?php endif; ?>
</body>
</html>
