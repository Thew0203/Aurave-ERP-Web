<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Store';
include dirname(__DIR__) . '/layout/header.php';
?>
<section class="hero-aruave py-4">
    <div class="container position-relative">
        <h1 class="mb-2 fw-bold">Electronics & IT Store</h1>
        <p class="lead mb-0 opacity-90">Arduino, networking, computers, components & more</p>
    </div>
</section>

<div class="container py-5">
    <div class="row g-4">
        <?php foreach ($products ?? [] as $i => $p): ?>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="card store-product-card border-0 shadow-sm h-100 animate-slide-up" style="animation-delay: <?= ($i % 6) * 0.05 ?>s">
                <?php if (!empty($p['image_url'])): ?>
                <img src="<?= $baseUrl . '/' . htmlspecialchars($p['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['name']) ?>">
                <?php else: ?>
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:180px"><i class="bi bi-cpu display-4 text-muted"></i></div>
                <?php endif; ?>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold"><?= htmlspecialchars($p['name']) ?></h5>
                    <?php if (!empty($p['company_name'])): ?><p class="text-muted small mb-0"><i class="bi bi-shop me-1"></i> <?= htmlspecialchars($p['company_name']) ?></p><?php endif; ?>
                    <?php if (!empty($p['sku'])): ?><p class="text-muted small mb-1"><?= htmlspecialchars($p['sku']) ?></p><?php endif; ?>
                    <p class="mb-3 mt-auto"><span class="fw-bold text-aruave-accent"><?= number_format((float)$p['selling_price'], 2) ?></span></p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= $baseUrl ?>/store/product/<?= (int)$p['id'] ?>" class="btn btn-primary btn-sm">View</a>
                        <form method="post" action="<?= $baseUrl ?>/store/cart/add" class="d-inline">
                            <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-outline-primary btn-sm">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php if (empty($products)): ?>
    <div class="text-center py-5 animate-fade-in">
        <i class="bi bi-inbox display-4 text-muted mb-3"></i>
        <h5 class="text-muted">No products available yet</h5>
        <p class="text-muted small mb-2">Only <strong>active</strong>, <strong>finished</strong> products appear here (raw materials are hidden).</p>
        <p class="text-muted small">As admin: Inventory → Products → Edit → uncheck <strong>Raw Material</strong> and ensure <strong>Active</strong> is checked, then Save.</p>
    </div>
    <?php endif; ?>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
