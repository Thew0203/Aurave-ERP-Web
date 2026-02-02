<?php
namespace App\Models;

use App\Core\Model;

class Payslip extends Model
{
    protected string $table = 'payslips';

    public function __construct()
    {
        parent::__construct();
        $this->tenantId = null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM payslips WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getWithEmployee(int $id): ?array
    {
        $slip = $this->find($id);
        if (!$slip) return null;
        $slip['employee'] = (new Employee())->find($slip['employee_id']);
        $slip['run'] = (new PayrollRun())->find($slip['payroll_run_id']);
        return $slip;
    }
}
