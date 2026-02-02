<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Employees';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Employees</h2>
        <a href="<?= $baseUrl ?>/payroll/employees/create" class="btn btn-primary">Add Employee</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>#</th><th>Name</th><th>Department</th><th>Designation</th><th>Basic Salary</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($employees ?? [] as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['employee_number']) ?></td>
                        <td><?= htmlspecialchars($e['name']) ?></td>
                        <td><?= htmlspecialchars($e['department'] ?? '') ?></td>
                        <td><?= htmlspecialchars($e['designation'] ?? '') ?></td>
                        <td><?= number_format((float)$e['basic_salary'], 2) ?></td>
                        <td><a href="<?= $baseUrl ?>/payroll/employees/edit/<?= (int)$e['id'] ?>">Edit</a></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($employees)): ?><tr><td colspan="6" class="text-muted">No employees.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
