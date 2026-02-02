<?php
namespace App\Models;

use App\Core\Model;

class PayrollRun extends Model
{
    protected string $table = 'payroll_runs';

    public function getList(): array
    {
        return $this->fetchAll("SELECT * FROM payroll_runs WHERE " . $this->tenantWhere() . " ORDER BY period_from DESC");
    }
}
