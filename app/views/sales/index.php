<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Sales';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold animate-fade-in">Sales</h2>
        <a href="<?= $baseUrl ?>/sales/create" class="btn btn-primary">New Sale</a>
    </div>
    <div class="card border-0 shadow-sm animate-slide-up">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Invoice</th><th>Date</th><th>Customer</th><th>Total</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($sales ?? [] as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['invoice_number']) ?></td>
                        <td><?= htmlspecialchars($s['sale_date']) ?></td>
                        <td><?= htmlspecialchars($s['customer_name'] ?? 'Walk-in') ?></td>
                        <td><?= number_format((float)$s['total'], 2) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($s['status']) ?></span></td>
                        <td>
                            <a href="<?= $baseUrl ?>/sales/view/<?= (int)$s['id'] ?>">View</a>
                            <a href="<?= $baseUrl ?>/sales/invoice/<?= (int)$s['id'] ?>" class="ms-2">Invoice</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($sales)): ?><tr><td colspan="6" class="text-muted">No sales.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
