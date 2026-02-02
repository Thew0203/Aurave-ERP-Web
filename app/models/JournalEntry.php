<?php
namespace App\Models;

use App\Core\Model;

class JournalEntry extends Model
{
    protected string $table = 'journal_entries';

    public function getList(): array
    {
        return $this->fetchAll("SELECT * FROM journal_entries WHERE " . $this->tenantWhere() . " ORDER BY entry_date DESC, id DESC");
    }

    public function getNextEntryNumber(): string
    {
        $year = date('Y');
        $row = $this->fetchOne("SELECT MAX(CAST(SUBSTRING(entry_number, 6) AS UNSIGNED)) AS n FROM journal_entries WHERE " . $this->tenantWhere() . " AND entry_number LIKE ?", ['JE-' . $year . '-%']);
        $n = (int) ($row['n'] ?? 0) + 1;
        return 'JE-' . $year . '-' . str_pad((string) $n, 5, '0', STR_PAD_LEFT);
    }

    public function getWithLines(int $id): ?array
    {
        $entry = $this->find($id);
        if (!$entry) return null;
        $entry['lines'] = $this->fetchAll("SELECT jel.*, coa.code, coa.name AS account_name FROM journal_entry_lines jel JOIN chart_of_accounts coa ON jel.account_id = coa.id WHERE jel.journal_entry_id = ?", [$id]);
        return $entry;
    }
}
