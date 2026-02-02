<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Sale;
use App\Models\Purchase;

class AccountingController extends Controller
{
    private function companyId(): int { return (int) $_SESSION['company_id']; }
    private function userId(): ?int { return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null; }

    private function coa(): ChartOfAccount { $m = new ChartOfAccount(); $m->setTenantId($this->companyId()); return $m; }
    private function journalModel(): JournalEntry { $m = new JournalEntry(); $m->setTenantId($this->companyId()); return $m; }
    private function journalLine(): JournalEntryLine { $m = new JournalEntryLine(); $m->setTenantId($this->companyId()); return $m; }

    public function chart(): void
    {
        $accounts = $this->coa()->all('code');
        $this->view('accounting.chart', ['pageTitle' => 'Chart of Accounts', 'accounts' => $accounts]);
    }

    public function journal(): void
    {
        $entries = $this->journalModel()->getList();
        $this->view('accounting.journal', ['pageTitle' => 'Journal Entries', 'entries' => $entries]);
    }

    public function journalSave(): void
    {
        $entryDate = $this->input('entry_date') ?: date('Y-m-d');
        $description = trim((string) $this->input('description'));
        $lines = $this->input('lines');
        if (!is_array($lines) || empty($lines)) {
            $this->redirect($this->baseUrl() . '/accounting/journal');
            return;
        }
        $totalDebit = 0;
        $totalCredit = 0;
        $validLines = [];
        foreach ($lines as $row) {
            if (empty($row['account_id'])) continue;
            $debit = (float) ($row['debit'] ?? 0);
            $credit = (float) ($row['credit'] ?? 0);
            if ($debit <= 0 && $credit <= 0) continue;
            $validLines[] = [
                'account_id' => (int) $row['account_id'],
                'debit' => $debit,
                'credit' => $credit,
                'description' => trim((string) ($row['line_description'] ?? '')),
            ];
            $totalDebit += $debit;
            $totalCredit += $credit;
        }
        if (empty($validLines) || abs($totalDebit - $totalCredit) > 0.01) {
            $this->redirect($this->baseUrl() . '/accounting/journal');
            return;
        }
        $entryNumber = $this->journalModel()->getNextEntryNumber();
        $entryId = $this->journalModel()->create([
            'company_id' => $this->companyId(),
            'entry_number' => $entryNumber,
            'entry_date' => $entryDate,
            'description' => $description,
            'created_by' => $this->userId(),
        ]);
        foreach ($validLines as $row) {
            $this->journalLine()->create([
                'journal_entry_id' => $entryId,
                'account_id' => $row['account_id'],
                'debit' => $row['debit'],
                'credit' => $row['credit'],
                'description' => $row['description'],
            ]);
        }
        $this->redirect($this->baseUrl() . '/accounting/journal');
    }

    public function profitLoss(): void
    {
        $companyId = $this->companyId();
        $saleModel = new Sale();
        $saleModel->setTenantId($companyId);
        $purchaseModel = new Purchase();
        $purchaseModel->setTenantId($companyId);
        $year = (int) ($_GET['year'] ?? date('Y'));
        $revenue = 0;
        for ($m = 1; $m <= 12; $m++) {
            $revenue += $saleModel->getTotalByMonth($year, $m);
        }
        $salesList = $saleModel->getList();
        $cogs = 0;
        foreach ($salesList as $s) {
            $saleWithItems = $saleModel->getWithItems((int) $s['id']);
            foreach ($saleWithItems['items'] ?? [] as $item) {
                $cogs += (float) ($item['quantity'] ?? 0) * 0;
            }
        }
        $this->view('accounting.profit_loss', [
            'pageTitle' => 'Profit & Loss',
            'year' => $year,
            'revenue' => $revenue,
            'cogs' => $cogs,
            'grossProfit' => $revenue - $cogs,
        ]);
    }
}
