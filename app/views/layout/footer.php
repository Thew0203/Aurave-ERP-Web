<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$tagline = $config['tagline'] ?? 'Electronics & IT Industry ERP';
?>
<footer class="footer-aruave mt-auto">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <strong class="d-inline-block mb-1"><i class="bi bi-cpu me-1"></i><?= htmlspecialchars($config['name']) ?></strong>
                <p class="footer-tagline mb-0 small"><?= htmlspecialchars($tagline) ?></p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="<?= $baseUrl ?>" class="me-3">Home</a>
                <a href="<?= $baseUrl ?>/store" class="me-3">Store</a>
                <a href="<?= $baseUrl ?>/auth/login" class="me-3">Login</a>
                <a href="<?= $baseUrl ?>/auth/register">Register</a>
            </div>
        </div>
        <hr class="my-3 border-secondary opacity-25">
        <div class="text-center small opacity-75">&copy; <?= date('Y') ?> <?= htmlspecialchars($config['name']) ?>. All rights reserved.</div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $baseUrl ?>/assets/js/app.js"></script>
<?php if (!empty($extraJs)): ?><?= $extraJs ?><?php endif; ?>
</body>
</html>
