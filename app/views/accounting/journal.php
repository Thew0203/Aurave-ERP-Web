<?php
$config = require dirname(__DIR__, 2) . '/config/app.php';
$baseUrl = rtrim($config['url'], '/');
$pageTitle = $pageTitle ?? 'Journal';
$accounts = (new \App\Models\ChartOfAccount())->all('code');
$accounts = array_filter($accounts, fn($a) => ($a['company_id'] ?? 0) == ($_SESSION['company_id'] ?? 0));
include dirname(__DIR__) . '/layout/header.php';
?>
<div class="container-fluid py-4">
    <h2 class="mb-4">Journal Entries</h2>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white"><h5 class="mb-0">New Entry</h5></div>
        <div class="card-body">
            <form method="post" action="<?= $baseUrl ?>/accounting/journal">
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Date</label>
                        <input type="date" name="entry_date" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" placeholder="Description">
                    </div>
                </div>
                <table class="table table-bordered" id="journalLines">
                    <thead><tr><th>Account</th><th>Debit</th><th>Credit</th><th>Line Description</th><th></th></tr></thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="lines[][account_id]" class="form-select form-select-sm">
                                    <option value="">--</option>
                                    <?php foreach ($accounts as $a): ?>
                                    <option value="<?= (int)$a['id'] ?>"><?= htmlspecialchars($a['code']) ?> - <?= htmlspecialchars($a['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" name="lines[][debit]" class="form-control form-control-sm" min="0"></td>
                            <td><input type="number" step="0.01" name="lines[][credit]" class="form-control form-control-sm" min="0"></td>
                            <td><input type="text" name="lines[][line_description]" class="form-control form-control-sm"></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger remove-journal-row">Remove</button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-outline-secondary btn-sm mb-2" id="addJournalRow">+ Add Line</button>
                <div><button type="submit" class="btn btn-primary">Save Entry</button></div>
            </form>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white"><h5 class="mb-0">Recent Entries</h5></div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Entry #</th><th>Date</th><th>Description</th></tr></thead>
                <tbody>
                    <?php foreach ($entries ?? [] as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['entry_number']) ?></td>
                        <td><?= htmlspecialchars($e['entry_date']) ?></td>
                        <td><?= htmlspecialchars($e['description'] ?? '') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($entries)): ?><tr><td colspan="3" class="text-muted">No entries.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var accountOptions = <?= json_encode(array_map(function($a) { return ['id' => $a['id'], 'label' => $a['code'] . ' - ' . $a['name']]; }, $accounts)) ?>;
document.getElementById('addJournalRow').addEventListener('click', function() {
    var tbody = document.querySelector('#journalLines tbody');
    var opt = accountOptions.map(function(a) { return '<option value="' + a.id + '">' + a.label + '</option>'; }).join('');
    var row = '<tr><td><select name="lines[][account_id]" class="form-select form-select-sm"><option value="">--</option>' + opt + '</select></td><td><input type="number" step="0.01" name="lines[][debit]" class="form-control form-control-sm" min="0"></td><td><input type="number" step="0.01" name="lines[][credit]" class="form-control form-control-sm" min="0"></td><td><input type="text" name="lines[][line_description]" class="form-control form-control-sm"></td><td><button type="button" class="btn btn-sm btn-outline-danger remove-journal-row">Remove</button></td></tr>';
    tbody.insertAdjacentHTML('beforeend', row);
});
document.getElementById('journalLines').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-journal-row')) e.target.closest('tr').remove();
});
</script>
<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
