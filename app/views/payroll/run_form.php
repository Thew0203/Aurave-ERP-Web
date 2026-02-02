<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'New Payroll Run';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <h2 class="mb-4">New Payroll Run</h2>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="<?= $baseUrl ?>/payroll/runs/create">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Period From *</label>
                        <input type="date" name="period_from" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Period To *</label>
                        <input type="date" name="period_to" class="form-control" required>
                    </div>
                </div>
                <p class="mt-3 text-muted small">Payslips will be generated for all active employees based on their current salary structure.</p>
                <button type="submit" class="btn btn-primary">Run Payroll</button>
                <a href="<?= $baseUrl ?>/payroll/runs" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
