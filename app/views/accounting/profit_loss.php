<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'P&L';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <h2 class="mb-4">Profit &amp; Loss Summary</h2>
    <form method="get" class="mb-3">
        <label>Year</label>
        <input type="number" name="year" value="<?= (int)($year ?? date('Y')) ?>" class="form-control d-inline-block w-auto">
        <button type="submit" class="btn btn-primary">Apply</button>
    </form>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table class="table">
                <tr><td><strong>Revenue (Sales)</strong></td><td class="text-end"><?= number_format($revenue ?? 0, 2) ?></td></tr>
                <tr><td><strong>Cost of Goods Sold</strong></td><td class="text-end"><?= number_format($cogs ?? 0, 2) ?></td></tr>
                <tr><td><strong>Gross Profit</strong></td><td class="text-end"><?= number_format($grossProfit ?? 0, 2) ?></td></tr>
            </table>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
