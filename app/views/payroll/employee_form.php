<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Employee';
$e = $employee ?? null;
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <h2 class="mb-4"><?= $e ? 'Edit Employee' : 'Add Employee' ?></h2>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="<?= $baseUrl ?>/payroll/employees/<?= $e ? 'edit/' . (int)$e['id'] : 'create' ?>">
                <?php if ($e): ?><p><strong>Employee #:</strong> <?= htmlspecialchars($e['employee_number']) ?></p><?php endif; ?>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($e['name'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($e['email'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($e['phone'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Department</label><input type="text" name="department" class="form-control" value="<?= htmlspecialchars($e['department'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Designation</label><input type="text" name="designation" class="form-control" value="<?= htmlspecialchars($e['designation'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Join Date</label><input type="date" name="join_date" class="form-control" value="<?= htmlspecialchars($e['join_date'] ?? date('Y-m-d')) ?>"></div>
                    <div class="col-md-4"><label class="form-label">Basic Salary</label><input type="number" step="0.01" name="basic_salary" class="form-control" value="<?= htmlspecialchars($e['basic_salary'] ?? '0') ?>"></div>
                    <div class="col-md-4"><label class="form-label">Allowances</label><input type="number" step="0.01" name="allowances" class="form-control" value="<?= htmlspecialchars($e['allowances'] ?? '0') ?>"></div>
                    <div class="col-md-4"><label class="form-label">Deductions</label><input type="number" step="0.01" name="deductions" class="form-control" value="<?= htmlspecialchars($e['deductions'] ?? '0') ?>"></div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="<?= $baseUrl ?>/payroll/employees" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
