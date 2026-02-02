<?php
namespace App\Models;

use App\Core\Model;

class JournalEntryLine extends Model
{
    protected string $table = 'journal_entry_lines';

    public function __construct()
    {
        parent::__construct();
        $this->tenantId = null;
    }
}
