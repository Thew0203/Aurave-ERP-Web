<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Chart of Accounts';
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <h2 class="mb-4">Chart of Accounts</h2>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Code</th><th>Name</th><th>Type</th></tr></thead>
                <tbody>
                    <?php foreach ($accounts ?? [] as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['code']) ?></td>
                        <td><?= htmlspecialchars($a['name']) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($a['type']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($accounts)): ?><tr><td colspan="3" class="text-muted">No accounts. Run seed.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
