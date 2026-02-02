<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Customers';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <h2 class="mb-4">Customers</h2>
    <p class="text-muted small mb-3">Customer IDs are from the database and linked to orders (foreign key). Each customer belongs to your company; IDs do not change.</p>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="<?= $baseUrl ?>/sales/customers" class="mb-4">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2"><input type="text" name="name" class="form-control" placeholder="Name" required></div>
                    <div class="col-md-2"><input type="email" name="email" class="form-control" placeholder="Email"></div>
                    <div class="col-md-2"><input type="text" name="phone" class="form-control" placeholder="Phone"></div>
                    <div class="col-md-2"><input type="text" name="address" class="form-control" placeholder="Address"></div>
                    <div class="col-md-2"><button type="submit" class="btn btn-primary">Add Customer</button></div>
                </div>
            </form>
            <table class="table table-hover mb-0">
                <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>User ID</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($customers ?? [] as $c): ?>
                    <tr>
                        <td><code><?= (int)($c['id'] ?? 0) ?></code></td>
                        <td><?= htmlspecialchars($c['name']) ?></td>
                        <td><?= htmlspecialchars($c['email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($c['phone'] ?? '') ?></td>
                        <td><?= htmlspecialchars($c['address'] ?? '') ?></td>
                        <td><?= !empty($c['user_id']) ? '<code>' . (int)$c['user_id'] . '</code>' : '<span class="text-muted">—</span>' ?></td>
                        <td>
                            <a href="<?= $baseUrl ?>/sales/customers/edit/<?= (int)$c['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="post" action="<?= $baseUrl ?>/sales/customers/delete/<?= (int)$c['id'] ?>" class="d-inline" onsubmit="return confirm('Delete this customer? Orders linked to this customer will keep the order but lose the customer link.');">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($customers)): ?><tr><td colspan="7" class="text-muted">No customers.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
