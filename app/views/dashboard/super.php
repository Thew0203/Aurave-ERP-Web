<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Super Admin';
include dirname(__DIR__) . '/layout/header.php';
$uc = $userCounts ?? [];
?>
<div class="container-fluid py-4">
    <h2 class="mb-4 fw-bold animate-fade-in">Super Admin Dashboard</h2>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm animate-slide-up animation-delay-1">
                <div class="card-body">
                    <h6 class="text-muted">Companies</h6>
                    <h3 class="mb-0"><?= (int)($companiesCount ?? 0) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm animate-slide-up animation-delay-2">
                <div class="card-body">
                    <h6 class="text-muted">Vendors (Admins)</h6>
                    <h3 class="mb-0"><?= (int)($uc['admin'] ?? 0) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm animate-slide-up animation-delay-3">
                <div class="card-body">
                    <h6 class="text-muted">Customers</h6>
                    <h3 class="mb-0"><?= (int)($customersCount ?? 0) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm animate-slide-up animation-delay-4">
                <div class="card-body">
                    <h6 class="text-muted">Total Users</h6>
                    <h3 class="mb-0"><?= (int)($uc['total'] ?? 0) ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm animate-slide-up animation-delay-1">
                <div class="card-body">
                    <h6 class="text-muted">Orders</h6>
                    <h3 class="mb-0"><?= (int)($ordersCount ?? 0) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm animate-slide-up animation-delay-2">
                <div class="card-body">
                    <h6 class="text-muted">Pending Orders</h6>
                    <h3 class="mb-0"><?= (int)($ordersPending ?? 0) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm animate-slide-up animation-delay-3">
                <div class="card-body">
                    <h6 class="text-muted">Products</h6>
                    <h3 class="mb-0"><?= (int)($productsCount ?? 0) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm animate-slide-up animation-delay-4">
                <div class="card-body">
                    <h6 class="text-muted">Sales This Month</h6>
                    <h3 class="mb-0"><?= number_format($salesThisMonth ?? 0, 2) ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm animate-slide-up">
                <div class="card-body">
                    <h6 class="text-muted">Inventory Value</h6>
                    <h3 class="mb-0"><?= number_format($inventoryValue ?? 0, 2) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm animate-slide-up">
                <div class="card-body">
                    <h6 class="text-muted">Total Sales</h6>
                    <h3 class="mb-0"><?= (int)($salesCount ?? 0) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm animate-slide-up">
                <div class="card-header bg-white"><h5 class="mb-0 fw-bold">Recent Registered Users</h5></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Company</th><th>Registered</th></tr></thead>
                        <tbody>
                            <?php foreach ($recentUsers ?? [] as $u): ?>
                            <tr>
                                <td><?= htmlspecialchars($u['name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($u['role'] ?? '') ?></span></td>
                                <td><?= htmlspecialchars($u['company_name'] ?? '-') ?></td>
                                <td class="small text-muted"><?= htmlspecialchars($u['created_at'] ?? '') ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentUsers)): ?><tr><td colspan="5" class="text-muted">No users yet.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm animate-slide-up">
                <div class="card-header bg-white"><h5 class="mb-0 fw-bold">Recent Orders</h5></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Order</th><th>Vendor</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php foreach ($recentOrders ?? [] as $o): ?>
                            <tr>
                                <td><?= htmlspecialchars($o['order_number'] ?? '') ?></td>
                                <td><?= htmlspecialchars($o['company_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($o['customer_name'] ?? $o['customer_email'] ?? 'Guest') ?></td>
                                <td><?= number_format((float)($o['total'] ?? 0), 2) ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($o['current_status'] ?? '') ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentOrders)): ?><tr><td colspan="5" class="text-muted">No orders yet.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm animate-slide-up">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Companies (Tenants)</h5>
            <a href="<?= $baseUrl ?>/companies/create" class="btn btn-primary btn-sm">Add Company</a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>ID</th><th>Name</th><th>Slug</th><th>Email</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($companies ?? [] as $c): ?>
                    <tr>
                        <td><?= (int)$c['id'] ?></td>
                        <td><?= htmlspecialchars($c['name']) ?></td>
                        <td><?= htmlspecialchars($c['slug']) ?></td>
                        <td><?= htmlspecialchars($c['email'] ?? '') ?></td>
                        <td><a href="<?= $baseUrl ?>/companies/edit/<?= (int)$c['id'] ?>">Edit</a></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($companies)): ?><tr><td colspan="5" class="text-muted">No companies.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
