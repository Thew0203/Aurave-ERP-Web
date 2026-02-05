<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Order';
$o = $order ?? [];
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="mb-0">Order: <?= htmlspecialchars($o['order_number'] ?? '') ?></h2>
        <div class="d-flex gap-2">
            <a href="<?= $baseUrl ?>/orders" class="btn btn-secondary">Back</a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#invoiceModal" data-invoice-url="<?= $baseUrl ?>/orders/invoice/<?= (int)($o['id'] ?? 0) ?>?embed=1">Invoice</button>
            <?php if (($o['current_status'] ?? '') !== 'cancelled'): ?>
            <form method="post" action="<?= $baseUrl ?>/orders/status/<?= (int)($o['id'] ?? 0) ?>" class="d-inline">
                <input type="hidden" name="status" value="cancelled">
                <input type="hidden" name="notes" value="Cancelled by admin">
                <button type="submit" class="btn btn-warning" onclick="return confirm('Set this order to Cancelled?');">Cancel order</button>
            </form>
            <?php endif; ?>
            <form method="post" action="<?= $baseUrl ?>/orders/delete/<?= (int)($o['id'] ?? 0) ?>" class="d-inline" onsubmit="return confirm('Permanently delete this order and its items? This cannot be undone.');">
                <button type="submit" class="btn btn-danger">Delete order</button>
            </form>
        </div>
    </div>
    <p class="text-muted small mb-3">Order ID <strong><code><?= (int)($o['id'] ?? 0) ?></code></strong> and Customer ID <strong><code><?= !empty($o['customer_id']) ? (int)$o['customer_id'] : '—' ?></code></strong> are from the database (foreign keys). All data is linked to your company.</p>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <p><strong>Date:</strong> <?= date('Y-m-d H:i', strtotime($o['order_date'] ?? '')) ?></p>
            <p><strong>Customer:</strong> <?= htmlspecialchars($o['customer_name'] ?? 'Guest') ?><?= !empty($o['customer_email']) ? ' (' . htmlspecialchars($o['customer_email']) . ')' : '' ?></p>
            <p><strong>Shipping:</strong> <?= htmlspecialchars($o['shipping_name'] ?? '') ?>, <?= htmlspecialchars($o['shipping_phone'] ?? '') ?>, <?= htmlspecialchars($o['shipping_address'] ?? '') ?></p>
            <p><strong>Total:</strong> <?= number_format((float)($o['total'] ?? 0), 2) ?></p>
            <p><strong>Payment:</strong> <?= htmlspecialchars($o['payment_method'] ?? '') ?> - <span class="badge bg-<?= ($o['payment_status'] ?? '') === 'paid' ? 'success' : (($o['payment_status'] ?? '') === 'failed' || ($o['payment_status'] ?? '') === 'refunded' ? 'danger' : 'warning') ?>"><?= htmlspecialchars($o['payment_status'] ?? '') ?></span>
                <?php if (($o['current_status'] ?? '') !== 'cancelled'): ?>
                <form method="post" action="<?= $baseUrl ?>/orders/payment/<?= (int)($o['id'] ?? 0) ?>" class="d-inline ms-2">
                    <select name="payment_status" class="form-select form-select-sm d-inline-block w-auto">
                        <option value="pending" <?= ($o['payment_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="paid" <?= ($o['payment_status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="failed" <?= ($o['payment_status'] ?? '') === 'failed' ? 'selected' : '' ?>>Failed</option>
                        <option value="refunded" <?= ($o['payment_status'] ?? '') === 'refunded' ? 'selected' : '' ?>>Refunded</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-primary ms-1">Update</button>
                </form>
                <?php endif; ?>
            </p>
            <p><strong>Status:</strong> <span class="badge bg-secondary"><?= htmlspecialchars($o['current_status'] ?? '') ?></span></p>
            <p class="text-muted small mb-2">Allowed statuses: <strong>pending</strong>, confirmed, processing, shipped, delivered, returned, cancelled.</p>
            <form method="post" action="<?= $baseUrl ?>/orders/status/<?= (int)($o['id'] ?? 0) ?>" class="mt-3">
                <div class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label">Update status</label>
                        <select name="status" class="form-select form-select-sm" required>
                            <option value="">-- Select new status --</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="returned">Returned</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="notes" class="form-control form-control-sm" placeholder="Notes">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white"><h5 class="mb-0">Items</h5></div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
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
        </div>
    </div>
    <?php if (!empty($o['history'])): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white"><h5 class="mb-0">Status History</h5></div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                <?php foreach ($o['history'] as $h): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="badge bg-secondary"><?= htmlspecialchars($h['status']) ?></span>
                    <small><?= htmlspecialchars($h['created_at']) ?></small>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Invoice Modal -->
<div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="invoiceModalLabel">Order Invoice — <?= htmlspecialchars($o['order_number'] ?? '') ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="min-height: 70vh;">
                <iframe id="invoiceIframe" style="width:100%; height:75vh; border:none;" title="Order Invoice"></iframe>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('invoiceModal').addEventListener('show.bs.modal', function(e) {
    var btn = e.relatedTarget;
    var url = btn && btn.getAttribute('data-invoice-url');
    var iframe = document.getElementById('invoiceIframe');
    if (url && iframe) iframe.src = url;
});
document.getElementById('invoiceModal').addEventListener('hidden.bs.modal', function() {
    var iframe = document.getElementById('invoiceIframe');
    if (iframe) iframe.src = 'about:blank';
});
</script>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
