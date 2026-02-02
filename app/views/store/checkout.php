<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Checkout';
$customer = $customer ?? null;
$userName = $customer['name'] ?? $_SESSION['user_name'] ?? '';
$userPhone = $customer['phone'] ?? '';
$userAddress = $customer['address'] ?? '';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container py-4">
    <h2 class="mb-4">Checkout</h2>
    <p class="text-muted small mb-3">Your order will be linked to your customer account. Admin will see your name in Orders (OMS) and control status (pending → confirmed → shipped → delivered).</p>
    <form method="post" action="<?= $baseUrl ?>/store/checkout">
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white"><h5 class="mb-0">Shipping</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" name="shipping_name" class="form-control" required value="<?= htmlspecialchars($userName) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone *</label>
                            <input type="text" name="shipping_phone" class="form-control" required value="<?= htmlspecialchars($userPhone) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address *</label>
                            <textarea name="shipping_address" class="form-control" rows="2" required><?= htmlspecialchars($userAddress) ?></textarea>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6"><label class="form-label">City</label><input type="text" name="shipping_city" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label">ZIP</label><input type="text" name="shipping_zip" class="form-control"></div>
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white"><h5 class="mb-0">Payment</h5></div>
                    <div class="card-body">
                        <div class="form-check"><input type="radio" name="payment_method" value="cod" class="form-check-input" id="pm_cod" checked><label class="form-check-label" for="pm_cod">Cash on Delivery (COD)</label></div>
                        <div class="form-check"><input type="radio" name="payment_method" value="gateway" class="form-check-input" id="pm_gw"><label class="form-check-label" for="pm_gw">Online Payment (placeholder)</label></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Order notes</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top">
                    <div class="card-header bg-white"><h5 class="mb-0">Order Summary</h5></div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php foreach ($products ?? [] as $p):
                                $qty = (float)($p['cart_qty'] ?? 0);
                                $unitPrice = (float)($p['selling_price'] ?? 0);
                                $lineTotal = (float)($p['line_total'] ?? 0);
                                $imgUrl = !empty($p['image_url']) ? $baseUrl . '/' . $p['image_url'] : null;
                            ?>
                            <div class="list-group-item d-flex align-items-start gap-3 px-3 py-3 border-0 border-bottom">
                                <?php if ($imgUrl): ?>
                                <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="rounded flex-shrink-0" style="width:56px;height:56px;object-fit:cover;">
                                <?php else: ?>
                                <div class="rounded bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width:56px;height:56px;"><i class="bi bi-cpu text-muted"></i></div>
                                <?php endif; ?>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold"><?= htmlspecialchars($p['name']) ?></div>
                                    <div class="small text-muted"><?= number_format($qty, 0) ?> x <?= number_format($unitPrice, 2) ?> = <?= number_format($lineTotal, 2) ?></div>
                                    <?php if (!empty($p['sku'])): ?><div class="small text-muted"><?= htmlspecialchars($p['sku']) ?></div><?php endif; ?>
                                </div>
                                <div class="text-end fw-semibold"><?= number_format($lineTotal, 2) ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="card-body border-top">
                        <p class="d-flex justify-content-between mb-2"><span class="text-muted">Subtotal</span><span><?= number_format($subtotal ?? 0, 2) ?></span></p>
                        <?php $shippingAmount = (float)($shipping ?? 0); ?>
                        <p class="d-flex justify-content-between mb-2"><span class="text-muted">Shipping</span><span><?= $shippingAmount > 0 ? number_format($shippingAmount, 2) : 'Free' ?></span></p>
                        <p class="d-flex justify-content-between mb-3 fw-bold fs-5"><span>Total</span><span class="text-aruave-accent"><?= number_format($total ?? 0, 2) ?></span></p>
                        <button type="submit" class="btn btn-primary w-100 py-2">Place Order</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
