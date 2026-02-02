<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Employee;
use App\Models\PayrollRun;
use App\Models\Payslip;

class PayrollController extends Controller
{
    private function companyId(): int { return (int) $_SESSION['company_id']; }
    private function userId(): ?int { return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null; }

    private function employee(): Employee { $m = new Employee(); $m->setTenantId($this->companyId()); return $m; }
    private function payrollRun(): PayrollRun { $m = new PayrollRun(); $m->setTenantId($this->companyId()); return $m; }
    private function payslipModel(): Payslip { $m = new Payslip(); $m->setTenantId($this->companyId()); return $m; }

    public function employees(): void
    {
        $list = $this->employee()->getList();
        $this->view('payroll.employees', ['pageTitle' => 'Employees', 'employees' => $list]);
    }

    public function employeeForm(): void
    {
        $this->view('payroll.employee_form', ['pageTitle' => 'Add Employee', 'employee' => null]);
    }

    public function employeeCreate(): void
    {
        $num = $this->employee()->getNextNumber();
        $this->employee()->create([
            'company_id' => $this->companyId(),
            'employee_number' => $num,
            'name' => trim((string) $this->input('name')),
            'email' => trim((string) $this->input('email')),
            'phone' => trim((string) $this->input('phone')),
            'department' => trim((string) $this->input('department')),
            'designation' => trim((string) $this->input('designation')),
            'join_date' => $this->input('join_date') ?: date('Y-m-d'),
            'basic_salary' => (float) $this->input('basic_salary'),
            'allowances' => (float) $this->input('allowances'),
            'deductions' => (float) $this->input('deductions'),
            'is_active' => 1,
        ]);
        $this->redirect($this->baseUrl() . '/payroll/employees');
    }

    public function employeeEdit(string $id): void
    {
        $emp = $this->employee()->find((int) $id);
        if (!$emp) { $this->redirect($this->baseUrl() . '/payroll/employees'); return; }
        $this->view('payroll.employee_form', ['pageTitle' => 'Edit Employee', 'employee' => $emp]);
    }

    public function employeeUpdate(string $id): void
    {
        $this->employee()->update((int) $id, [
            'name' => trim((string) $this->input('name')),
            'email' => trim((string) $this->input('email')),
            'phone' => trim((string) $this->input('phone')),
            'department' => trim((string) $this->input('department')),
            'designation' => trim((string) $this->input('designation')),
            'join_date' => $this->input('join_date'),
            'basic_salary' => (float) $this->input('basic_salary'),
            'allowances' => (float) $this->input('allowances'),
            'deductions' => (float) $this->input('deductions'),
        ]);
        $this->redirect($this->baseUrl() . '/payroll/employees');
    }

    public function runs(): void
    {
        $list = $this->payrollRun()->getList();
        $this->view('payroll.runs', ['pageTitle' => 'Payroll Runs', 'runs' => $list]);
    }

    public function runForm(): void
    {
        $employees = $this->employee()->getList();
        $this->view('payroll.run_form', ['pageTitle' => 'New Payroll Run', 'employees' => $employees]);
    }

    public function runCreate(): void
    {
        $periodFrom = $this->input('period_from');
        $periodTo = $this->input('period_to');
        if (!$periodFrom || !$periodTo) {
            $this->redirect($this->baseUrl() . '/payroll/runs/create');
            return;
        }
        $employees = $this->employee()->getList();
        $totalGross = 0;
        $totalNet = 0;
        $runId = $this->payrollRun()->create([
            'company_id' => $this->companyId(),
            'period_from' => $periodFrom,
            'period_to' => $periodTo,
            'run_date' => date('Y-m-d'),
            'status' => 'processed',
            'created_by' => $this->userId(),
        ]);
        foreach ($employees as $emp) {
            if (empty($emp['is_active'])) continue;
            $gross = (float) ($emp['basic_salary'] ?? 0) + (float) ($emp['allowances'] ?? 0);
            $deductions = (float) ($emp['deductions'] ?? 0);
            $net = $gross - $deductions;
            $totalGross += $gross;
            $totalNet += $net;
            $this->payslipModel()->create([
                'payroll_run_id' => $runId,
                'employee_id' => (int) $emp['id'],
                'gross_salary' => $gross,
                'deductions' => $deductions,
                'net_salary' => $net,
            ]);
        }
        $this->payrollRun()->update($runId, ['total_gross' => $totalGross, 'total_net' => $totalNet]);
        $this->redirect($this->baseUrl() . '/payroll/runs');
    }

    public function payslip(string $id): void
    {
        $slip = $this->payslipModel()->getWithEmployee((int) $id);
        if (!$slip) { $this->redirect($this->baseUrl() . '/payroll/runs'); return; }
        $this->view('payroll.payslip', ['pageTitle' => 'Payslip', 'payslip' => $slip]);
    }
}
