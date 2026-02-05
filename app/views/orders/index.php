<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Orders';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <h2 class="mb-4">Order Management (OMS)</h2>
    <p class="text-muted small mb-3">Order IDs are from the database and linked to customers (foreign key). Click <strong>View</strong> to see details and <strong>update status</strong>. Allowed statuses: pending → confirmed → processing → shipped → delivered (or returned / cancelled).</p>
    <div class="mb-3">
        <form method="get" class="d-inline">
            <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                <option value="">All statuses</option>
                <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="confirmed" <?= ($_GET['status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                <option value="processing" <?= ($_GET['status'] ?? '') === 'processing' ? 'selected' : '' ?>>Processing</option>
                <option value="shipped" <?= ($_GET['status'] ?? '') === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                <option value="delivered" <?= ($_GET['status'] ?? '') === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                <option value="returned" <?= ($_GET['status'] ?? '') === 'returned' ? 'selected' : '' ?>>Returned</option>
                <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </form>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>ID</th><th>Order #</th><th>Date</th><th>Customer</th><th>Total</th><th>Status</th><th>Payment</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($orders ?? [] as $o): ?>
                    <tr>
                        <td><code><?= (int)($o['id'] ?? 0) ?></code></td>
                        <td><?= htmlspecialchars($o['order_number']) ?></td>
                        <td><?= date('Y-m-d H:i', strtotime($o['order_date'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($o['customer_name'] ?? 'Guest') ?></td>
                        <td><?= number_format((float)$o['total'], 2) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($o['current_status'] ?? '') ?></span></td>
                        <td><span class="badge bg-<?= ($o['payment_status'] ?? '') === 'paid' ? 'success' : 'warning' ?>"><?= htmlspecialchars($o['payment_status'] ?? '') ?></span></td>
                        <td>
                            <a href="<?= $baseUrl ?>/orders/view/<?= (int)$o['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#invoiceModal" data-invoice-url="<?= $baseUrl ?>/orders/invoice/<?= (int)$o['id'] ?>?embed=1">Invoice</button>
                            <?php if (($o['current_status'] ?? '') !== 'cancelled'): ?>
                            <form method="post" action="<?= $baseUrl ?>/orders/status/<?= (int)$o['id'] ?>" class="d-inline">
                                <input type="hidden" name="status" value="cancelled">
                                <input type="hidden" name="notes" value="Cancelled by admin">
                                <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('Set this order to Cancelled?');">Cancel</button>
                            </form>
                            <?php endif; ?>
                            <form method="post" action="<?= $baseUrl ?>/orders/delete/<?= (int)$o['id'] ?>" class="d-inline" onsubmit="return confirm('Permanently delete this order and its items? This cannot be undone.');">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($orders)): ?>
                    <tr><td colspan="8" class="text-muted py-4">
                        No orders yet. Orders appear here when customers place orders from the <a href="<?= $baseUrl ?>/store">Store</a>. Use <strong>View</strong> on any order to see details and update its status (pending → confirmed → processing → shipped → delivered, or returned / cancelled).
                    </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Invoice Modal -->
<div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="invoiceModalLabel">Order Invoice</h5>
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
