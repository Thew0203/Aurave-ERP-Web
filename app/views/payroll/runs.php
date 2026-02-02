<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Payroll Runs';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Payroll Runs</h2>
        <a href="<?= $baseUrl ?>/payroll/runs/create" class="btn btn-primary">New Payroll Run</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Period From</th><th>Period To</th><th>Run Date</th><th>Total Gross</th><th>Total Net</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($runs ?? [] as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['period_from']) ?></td>
                        <td><?= htmlspecialchars($r['period_to']) ?></td>
                        <td><?= htmlspecialchars($r['run_date']) ?></td>
                        <td><?= number_format((float)$r['total_gross'], 2) ?></td>
                        <td><?= number_format((float)$r['total_net'], 2) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($r['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($runs)): ?><tr><td colspan="6" class="text-muted">No payroll runs.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
