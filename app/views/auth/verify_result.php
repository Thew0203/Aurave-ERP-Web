<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$action = $action ?? 'yes';
$message = $message ?? 'Your response has been recorded.';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-5">
                    <?php if ($action === 'yes'): ?>
                    <div class="mb-3">
                        <span class="display-4 text-success">✓</span>
                    </div>
                    <h4 class="mb-3">Thank you</h4>
                    <p class="text-muted mb-0"><?= htmlspecialchars($message) ?></p>
                    <?php else: ?>
                    <div class="mb-3">
                        <span class="display-4 text-warning">!</span>
                    </div>
                    <h4 class="mb-3">Response recorded</h4>
                    <p class="text-muted mb-0"><?= htmlspecialchars($message) ?></p>
                    <p class="small text-muted mt-2">If this wasn't you, we recommend changing your password in your profile.</p>
                    <?php endif; ?>
                    <hr class="my-4">
                    <a href="<?= $baseUrl ?>/auth/login" class="btn btn-primary">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
