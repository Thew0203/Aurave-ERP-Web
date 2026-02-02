<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'My Orders';
$orderMessage = $_SESSION['order_message'] ?? '';
if ($orderMessage) { unset($_SESSION['order_message']); }
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container py-4">
    <h2 class="mb-4">My Orders</h2>
    <?php if ($orderMessage): ?>
    <div class="alert alert-info"><?= htmlspecialchars($orderMessage) ?></div>
    <?php endif; ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Order #</th><th>Vendor</th><th>Date</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($orders ?? [] as $o): ?>
                    <tr<?= ($o['current_status'] ?? '') === 'cancelled' ? ' class="order-row-cancelled" data-cancelled="1"' : '' ?>>
                        <td><?= htmlspecialchars($o['order_number']) ?></td>
                        <td><small class="text-muted"><?= htmlspecialchars($o['company_name'] ?? 'Unknown') ?></small></td>
                        <td><?= date('Y-m-d H:i', strtotime($o['order_date'] ?? '')) ?></td>
                        <td><?= number_format((float)$o['total'], 2) ?></td>
                        <td><span class="badge bg-<?= ($o['current_status'] ?? '') === 'cancelled' ? 'danger' : (($o['current_status'] ?? '') === 'delivered' ? 'success' : 'secondary') ?>"><?= htmlspecialchars($o['current_status'] ?? '') ?></span></td>
                        <td>
                            <a href="<?= $baseUrl ?>/store/orders/track/<?= (int)$o['id'] ?>" class="btn btn-sm btn-outline-primary">Track</a>
                            <?php if (($o['current_status'] ?? '') === 'pending'): ?>
                            <form method="post" action="<?= $baseUrl ?>/store/orders/cancel/<?= (int)$o['id'] ?>" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($orders)): ?><tr><td colspan="6" class="text-muted">No orders yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <a href="<?= $baseUrl ?>/store" class="btn btn-secondary mt-3">Continue Shopping</a>
</div>
<script>
(function() {
    var tbody = document.querySelector('.table tbody');
    var cancelledRows = document.querySelectorAll('.order-row-cancelled');
    if (cancelledRows.length === 0) return;
    cancelledRows.forEach(function(row) {
        setTimeout(function() {
            row.style.transition = 'opacity 0.5s ease';
            row.style.opacity = '0';
            setTimeout(function() {
                row.remove();
                if (tbody && tbody.querySelectorAll('tr').length === 0) {
                    var empty = document.createElement('tr');
                    empty.innerHTML = '<td colspan="6" class="text-muted">No orders yet.</td>';
                    tbody.appendChild(empty);
                }
            }, 500);
        }, 5000);
    });
})();
</script>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
