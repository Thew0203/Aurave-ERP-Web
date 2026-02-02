<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Product';
$p = $product ?? [];
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4"><a href="<?= $baseUrl ?>/store" class="text-decoration-none">Store</a> <span class="text-muted mx-1">/</span> <span><?= htmlspecialchars($p['name']) ?></span></nav>
    <div class="row">
        <div class="col-md-8 animate-slide-up">
            <h2 class="fw-bold mb-2"><?= htmlspecialchars($p['name']) ?></h2>
            <?php if (!empty($p['company_name'])): ?><p class="text-muted small mb-1"><i class="bi bi-shop me-1"></i> Sold by <strong><?= htmlspecialchars($p['company_name']) ?></strong></p><?php endif; ?>
            <?php if (!empty($p['sku'])): ?><p class="text-muted small mb-3"><?= htmlspecialchars($p['sku']) ?></p><?php endif; ?>
            <?php if (!empty($p['image_url'])): ?>
            <img src="<?= $baseUrl . '/' . htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="img-fluid rounded-aruave shadow-sm mb-4" style="max-height:320px;object-fit:contain;background:#f8fafc;">
            <?php else: ?>
            <div class="rounded-aruave bg-light d-flex align-items-center justify-content-center mb-4" style="height:280px"><i class="bi bi-cpu display-1 text-muted"></i></div>
            <?php endif; ?>
            <?php if (!empty($p['description'])): ?><div class="text-muted"><?= nl2br(htmlspecialchars($p['description'])) ?></div><?php endif; ?>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm card-interactive sticky-top animate-slide-up animation-delay-2">
                <div class="card-body p-4">
                    <?php if (!empty($p['company_name'])): ?><p class="text-muted small mb-2">Ordering from <strong><?= htmlspecialchars($p['company_name']) ?></strong></p><?php endif; ?>
                    <p class="text-muted small mb-1">Price</p>
                    <h3 class="mb-4 fw-bold text-aruave-accent"><?= number_format((float)$p['selling_price'], 2) ?></h3>
                    <form method="post" action="<?= $baseUrl ?>/store/cart/add">
                        <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                        <div class="mb-2">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" value="1" min="1" max="999">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
