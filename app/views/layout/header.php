<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$userName = $_SESSION['user_name'] ?? 'Guest';
$role = $_SESSION['role'] ?? '';
$isStaff = in_array($role, ['super_admin', 'admin', 'staff'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($config['tagline'] ?? 'Electronics & IT Industry ERP') ?>">
    <title><?= htmlspecialchars($pageTitle ?? $config['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>/assets/css/app.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<?php if (!empty($_SESSION['mailer_debug'])): ?>
<div class="container-fluid py-2">
    <div class="alert alert-info alert-dismissible fade show mb-0" role="alert">
        <?= htmlspecialchars($_SESSION['mailer_debug']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
<?php unset($_SESSION['mailer_debug']); endif; ?>
<nav class="navbar navbar-expand-lg navbar-dark navbar-aruave">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= $baseUrl ?>"><i class="bi bi-cpu me-1"></i><?= htmlspecialchars($config['name']) ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <?php if ($role === 'customer'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>/store">Store</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>/store/orders">My Orders</a></li>
                    <?php elseif ($isStaff): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>/dashboard">Dashboard</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Inventory</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= $baseUrl ?>/inventory/products">Products</a></li>
                                <li><a class="dropdown-item" href="<?= $baseUrl ?>/inventory/stock">Stock</a></li>
                                <li><a class="dropdown-item" href="<?= $baseUrl ?>/inventory/categories">Categories</a></li>
                                <li><a class="dropdown-item" href="<?= $baseUrl ?>/inventory/suppliers">Suppliers</a></li>
                                <li><a class="dropdown-item" href="<?= $baseUrl ?>/inventory/alerts">Low Stock</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Production</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= $baseUrl ?>/production/bom">BOM</a></li>
                                <li><a class="dropdown-item" href="<?= $baseUrl ?>/production/orders">Orders</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>/sales">Sales</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>/purchasing">Purchasing</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Accounting</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= $baseUrl ?>/accounting/chart">Chart of Accounts</a></li>
                                <li><a class="dropdown-item" href="<?= $baseUrl ?>/accounting/journal">Journal</a></li>
                                <li><a class="dropdown-item" href="<?= $baseUrl ?>/accounting/pl">P&L</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Payroll</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= $baseUrl ?>/payroll/employees">Employees</a></li>
                                <li><a class="dropdown-item" href="<?= $baseUrl ?>/payroll/runs">Payroll Runs</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>/orders">Orders (OMS)</a></li>
                        <?php if ($role === 'super_admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>/companies">Companies</a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>/store">Store</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <?php
                        // Display friendly role names
                        $displayRole = match($role) {
                            'super_admin' => 'System Admin',
                            'admin' => 'Vendor',
                            'staff' => 'Staff',
                            'customer' => 'Customer',
                            default => $role,
                        };
                        ?>
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><?= htmlspecialchars($userName) ?> (<?= htmlspecialchars($displayRole) ?>)</a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= $baseUrl ?>/profile"><i class="bi bi-person me-2"></i>My Profile</a></li>
                            <?php if ($role === 'customer'): ?>
                            <li><a class="dropdown-item" href="<?= $baseUrl ?>/store/orders">My Orders</a></li>
                            <?php endif; ?>
                            <?php if ($role === 'admin'): ?>
                            <li><a class="dropdown-item" href="<?= $baseUrl ?>/orders">Manage Orders</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= $baseUrl ?>/auth/logout">Logout</a></li>
                        </ul>
                    </li>
                    <?php if ($role === 'customer'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>/store/cart"><i class="bi bi-cart3"></i> Cart</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>/auth/login">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>/auth/register">Register</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>/store/cart"><i class="bi bi-cart3"></i> Cart</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
