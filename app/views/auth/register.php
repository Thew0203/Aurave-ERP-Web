<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = 'Register – ' . $config['name'];
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="auth-page py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card auth-card border-0">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-person-plus text-aruave-accent display-5 mb-2"></i>
                            <h4 class="card-title mb-1 fw-bold">Create account</h4>
                            <p class="text-muted small">Business or customer — one signup for <?= htmlspecialchars($config['name']) ?></p>
                        </div>
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger animate-fade-in"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success animate-fade-in"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>
                        <form method="post" action="<?= $baseUrl ?>/auth/register">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" placeholder="Your name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="you@example.com">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Company / Store Name <span class="text-muted fw-normal">(optional)</span></label>
                                <input type="text" name="company_name" class="form-control" value="<?= htmlspecialchars($_POST['company_name'] ?? '') ?>" placeholder="Leave blank = Customer | Enter name = Vendor/Seller">
                                <small class="text-muted"><strong>Customer:</strong> Leave blank to shop from vendors. <strong>Vendor/Seller:</strong> Enter your company name to sell products.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password <span class="text-muted fw-normal">(min 6 characters)</span></label>
                                <input type="password" name="password" class="form-control" required placeholder="••••••••">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" placeholder="Optional">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="Optional"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">Register</button>
                        </form>
                        <p class="mt-4 mb-0 text-center small text-muted"><a href="<?= $baseUrl ?>/auth/login" class="text-decoration-none">Already have an account? Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
