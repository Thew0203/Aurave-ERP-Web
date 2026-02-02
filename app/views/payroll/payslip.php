<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Payslip';
$slip = $payslip ?? [];
$emp = $slip['employee'] ?? [];
$run = $slip['run'] ?? [];
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Payslip</h2>
        <a href="<?= $baseUrl ?>/payroll/runs" class="btn btn-secondary">Back</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <p><strong>Employee:</strong> <?= htmlspecialchars($emp['name'] ?? '') ?> (<?= htmlspecialchars($emp['employee_number'] ?? '') ?>)</p>
            <p><strong>Period:</strong> <?= htmlspecialchars($run['period_from'] ?? '') ?> to <?= htmlspecialchars($run['period_to'] ?? '') ?></p>
            <p><strong>Gross Salary:</strong> <?= number_format((float)($slip['gross_salary'] ?? 0), 2) ?></p>
            <p><strong>Deductions:</strong> <?= number_format((float)($slip['deductions'] ?? 0), 2) ?></p>
            <p><strong>Net Salary:</strong> <?= number_format((float)($slip['net_salary'] ?? 0), 2) ?></p>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
