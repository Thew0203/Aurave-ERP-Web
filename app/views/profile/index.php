<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'My Profile';
$user = $user ?? [];
$customer = $customer ?? null;
$company = $company ?? null;
$role = $user['role'] ?? '';
$profileError = $_SESSION['profile_error'] ?? '';
$profileSuccess = $_SESSION['profile_success'] ?? '';
if ($profileError) { unset($_SESSION['profile_error']); }
if ($profileSuccess) { unset($_SESSION['profile_success']); }

$displayRole = match($role) {
    'super_admin' => 'System Admin',
    'admin' => 'Vendor',
    'staff' => 'Staff',
    'customer' => 'Customer',
    default => $role,
};

$backUrl = $role === 'customer' ? $baseUrl . '/store' : $baseUrl . '/dashboard';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">My Profile</h2>
                <a href="<?= htmlspecialchars($backUrl) ?>" class="btn btn-outline-secondary">Back</a>
            </div>
            <p class="text-muted small mb-3">User ID <code><?= (int)($user['id'] ?? 0) ?></code> · <?= htmlspecialchars($displayRole) ?></p>

            <?php if ($profileError): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($profileError) ?></div>
            <?php endif; ?>
            <?php if ($profileSuccess): ?>
            <div class="alert alert-success"><?= htmlspecialchars($profileSuccess) ?></div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Profile Information</h5></div>
                <div class="card-body">
                    <form method="post" action="<?= $baseUrl ?>/profile">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name *</label>
                                <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($user['name'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? ($customer['phone'] ?? '')) ?>">
                            </div>
                            <?php if ($role === 'customer'): ?>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($customer['address'] ?? '') ?></textarea>
                            </div>
                            <?php endif; ?>
                            <?php if (in_array($role, ['admin', 'staff'], true) && $company): ?>
                            <div class="col-12">
                                <label class="form-label">Company</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($company['name'] ?? '') ?>" readonly disabled>
                                <small class="text-muted">Company ID <?= (int)($company['id'] ?? 0) ?> · Linked to your account</small>
                            </div>
                            <?php endif; ?>
                            <?php if ($role === 'super_admin'): ?>
                            <div class="col-12">
                                <p class="text-muted small mb-0"><i class="bi bi-shield-lock me-1"></i> System Admin — no company linked</p>
                            </div>
                            <?php endif; ?>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Save Profile</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><h5 class="mb-0">Change Password</h5></div>
                <div class="card-body">
                    <form method="post" action="<?= $baseUrl ?>/profile/password">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required placeholder="••••••••">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" required minlength="6" placeholder="Min 6 characters">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" required minlength="6" placeholder="••••••••">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-outline-primary">Change Password</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
