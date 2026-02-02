<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Dashboard';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <h2 class="mb-4 fw-bold animate-fade-in">Dashboard</h2>
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm animate-slide-up animation-delay-1">
                <div class="card-body">
                    <h6 class="text-muted">Sales This Month</h6>
                    <h3 class="mb-0"><?= number_format($salesThisMonth ?? 0, 2) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm animate-slide-up animation-delay-2">
                <div class="card-body">
                    <h6 class="text-muted">Inventory Value</h6>
                    <h3 class="mb-0"><?= number_format($inventoryValue ?? 0, 2) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm animate-slide-up animation-delay-3">
                <div class="card-body">
                    <h6 class="text-muted">Total Orders</h6>
                    <h3 class="mb-0"><?= (int) ($ordersCount ?? 0) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm animate-slide-up animation-delay-4">
                <div class="card-body">
                    <h6 class="text-muted">Pending Orders</h6>
                    <h3 class="mb-0"><?= (int) ($pendingCount ?? 0) ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm animate-slide-up animation-delay-2">
                <div class="card-header bg-white"><h5 class="mb-0 fw-bold">Recent Sales</h5></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Invoice</th><th>Date</th><th>Total</th><th></th></tr></thead>
                        <tbody>
                            <?php foreach ($recentSales ?? [] as $s): ?>
                            <tr>
                                <td><?= htmlspecialchars($s['invoice_number']) ?></td>
                                <td><?= htmlspecialchars($s['sale_date']) ?></td>
                                <td><?= number_format((float)$s['total'], 2) ?></td>
                                <td><a href="<?= $baseUrl ?>/sales/view/<?= (int)$s['id'] ?>" class="text-decoration-none">View</a></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentSales)): ?><tr><td colspan="4" class="text-muted">No sales yet.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm animate-slide-up animation-delay-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Low Stock Alerts</h5>
                    <a href="<?= $baseUrl ?>/inventory/alerts" class="text-decoration-none small">View all</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Product</th><th>Qty</th><th>Threshold</th><th></th></tr></thead>
                        <tbody>
                            <?php foreach ($lowStock ?? [] as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['name']) ?></td>
                                <td><?= number_format((float)$p['quantity'], 2) ?></td>
                                <td><?= number_format((float)$p['low_stock_threshold'], 2) ?></td>
                                <td><a href="<?= $baseUrl ?>/inventory/stock" class="text-decoration-none small">Stock</a></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($lowStock)): ?><tr><td colspan="4" class="text-muted">No low stock.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0 fw-bold">Company & People Overview</h5>
                    <div class="d-flex gap-2">
                        <a href="<?= $baseUrl ?>/sales/customers" class="btn btn-outline-primary btn-sm">Customers (<?= (int)($customersCount ?? 0) ?>)</a>
                        <a href="<?= $baseUrl ?>/payroll/employees" class="btn btn-outline-primary btn-sm">Employees (<?= (int)($employeesCount ?? 0) ?>)</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <p class="px-3 pt-2 text-muted small mb-0">Users in this company (admins, staff, customers) — linked to Sales customers and Payroll employees.</p>
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Name</th><th>Email</th><th>Role</th></tr></thead>
                        <tbody>
                            <?php foreach ($companyUsers ?? [] as $u): ?>
                            <tr>
                                <td><?= htmlspecialchars($u['name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($u['role'] ?? '') ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($companyUsers)): ?><tr><td colspan="3" class="text-muted">No users in this company.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
