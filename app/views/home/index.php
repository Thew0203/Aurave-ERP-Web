<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$tagline = $config['tagline'] ?? 'Electronics & IT Industry ERP';
$pageTitle = $pageTitle ?? $config['name'];
include dirname(__DIR__) . '/layout/header.php';
?>
<section class="hero-aruave">
    <div class="container position-relative">
        <div class="row align-items-center py-4">
            <div class="col-lg-7 animate-slide-up">
                <h1 class="display-4 fw-bold mb-3">Electronics & IT ERP, Built for Business</h1>
                <p class="lead mb-4"><?= htmlspecialchars($tagline) ?> — Inventory, sales, production, accounting, payroll, and e‑commerce in one platform.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?= $baseUrl ?>/store" class="btn btn-light btn-lg px-4 rounded-aruave shadow"><i class="bi bi-bag me-2"></i>Shop Store</a>
                    <a href="<?= $baseUrl ?>/auth/register" class="btn btn-outline-light btn-lg px-4 rounded-aruave">Get Started</a>
                </div>
            </div>
            <div class="col-lg-5 text-center mt-4 mt-lg-0 animate-slide-up animation-delay-2">
                <div class="bg-dark bg-opacity-25 rounded-3 p-4 d-inline-block">
                    <i class="bi bi-cpu display-1 text-aruave-accent"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="features" class="py-5 bg-white">
    <div class="container py-4">
        <h2 class="text-center fw-bold mb-5">Everything You Need to Run Your Tech Business</h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card card-interactive h-100 border-0 shadow-sm animate-slide-up animation-delay-1">
                    <div class="card-body p-4">
                        <div class="text-aruave-accent mb-3"><i class="bi bi-box-seam display-6"></i></div>
                        <h5 class="card-title fw-bold">Inventory & Products</h5>
                        <p class="text-muted mb-0">Manage Arduino, networking gear, computers, components — SKUs, stock levels, low-stock alerts, and categories.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card card-interactive h-100 border-0 shadow-sm animate-slide-up animation-delay-2">
                    <div class="card-body p-4">
                        <div class="text-aruave-accent mb-3"><i class="bi bi-gear-wide-connected display-6"></i></div>
                        <h5 class="card-title fw-bold">Production & BOM</h5>
                        <p class="text-muted mb-0">Bills of materials and production orders for assembly and manufacturing workflows.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card card-interactive h-100 border-0 shadow-sm animate-slide-up animation-delay-3">
                    <div class="card-body p-4">
                        <div class="text-aruave-accent mb-3"><i class="bi bi-cart-check display-6"></i></div>
                        <h5 class="card-title fw-bold">Sales & E‑commerce</h5>
                        <p class="text-muted mb-0">Invoicing, orders, and a customer-facing store for your electronics and IT products.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card card-interactive h-100 border-0 shadow-sm animate-slide-up animation-delay-2">
                    <div class="card-body p-4">
                        <div class="text-aruave-accent mb-3"><i class="bi bi-journal-bookmark display-6"></i></div>
                        <h5 class="card-title fw-bold">Accounting</h5>
                        <p class="text-muted mb-0">Chart of accounts, journal entries, and profit & loss reporting.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card card-interactive h-100 border-0 shadow-sm animate-slide-up animation-delay-3">
                    <div class="card-body p-4">
                        <div class="text-aruave-accent mb-3"><i class="bi bi-people display-6"></i></div>
                        <h5 class="card-title fw-bold">Payroll</h5>
                        <p class="text-muted mb-0">Employees, payroll runs, and payslips for your team.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card card-interactive h-100 border-0 shadow-sm animate-slide-up animation-delay-4">
                    <div class="card-body p-4">
                        <div class="text-aruave-accent mb-3"><i class="bi bi-building display-6"></i></div>
                        <h5 class="card-title fw-bold">Multi-tenant</h5>
                        <p class="text-muted mb-0">SaaS-ready: multiple companies, role-based access (Admin, Staff, Customer).</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <a href="<?= $baseUrl ?>/auth/register" class="btn btn-primary btn-lg px-5">Start Free — Register Your Business</a>
        </div>
    </div>
</section>

<section class="py-5 bg-aruave-light">
    <div class="container text-center py-4">
        <h2 class="fw-bold mb-3">Ready to Run Your Tech Business?</h2>
        <p class="lead text-muted mb-4">Join Aruave — one platform for electronics and IT.</p>
        <a href="<?= $baseUrl ?>/store" class="btn btn-primary btn-lg me-2"><i class="bi bi-bag me-2"></i>Browse Store</a>
        <a href="<?= $baseUrl ?>/auth/login" class="btn btn-outline-primary btn-lg">Login</a>
    </div>
</section>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
