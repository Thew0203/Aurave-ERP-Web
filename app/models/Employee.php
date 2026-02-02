<?php
namespace App\Models;

use App\Core\Model;

class Employee extends Model
{
    protected string $table = 'employees';

    public function getList(): array
    {
        return $this->fetchAll("SELECT * FROM employees WHERE " . $this->tenantWhere() . " ORDER BY name");
    }

    public function getNextNumber(): string
    {
        $row = $this->fetchOne("SELECT MAX(CAST(SUBSTRING(employee_number, 4) AS UNSIGNED)) AS n FROM employees WHERE " . $this->tenantWhere());
        $n = (int) ($row['n'] ?? 0) + 1;
        return 'EMP' . str_pad((string) $n, 3, '0', STR_PAD_LEFT);
    }
}
