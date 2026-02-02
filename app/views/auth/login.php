<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = 'Login – ' . $config['name'];
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="auth-page py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card auth-card border-0">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-cpu text-aruave-accent display-5 mb-2"></i>
                            <h4 class="card-title mb-1 fw-bold">Welcome back</h4>
                            <p class="text-muted small">Sign in to <?= htmlspecialchars($config['name']) ?></p>
                        </div>
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger animate-fade-in"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <form method="post" action="<?= $baseUrl ?>/auth/login">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="you@example.com">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required placeholder="••••••••">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
                        </form>
                        <p class="mt-4 mb-0 text-center small text-muted"><a href="<?= $baseUrl ?>/auth/register" class="text-decoration-none">Register an account</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
