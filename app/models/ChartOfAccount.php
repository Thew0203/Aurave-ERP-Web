<?php
namespace App\Models;

use App\Core\Model;

class ChartOfAccount extends Model
{
    protected string $table = 'chart_of_accounts';

    public function getByType(string $type): array
    {
        return $this->fetchAll("SELECT * FROM chart_of_accounts WHERE " . $this->tenantWhere() . " AND type = ? ORDER BY code", [$type]);
    }

    public function getRevenueAccounts(): array
    {
        return $this->getByType('revenue');
    }

    public function getExpenseAccounts(): array
    {
        return $this->getByType('expense');
    }

    public function getAssetAccounts(): array
    {
        return $this->getByType('asset');
    }

    public function getLiabilityAccounts(): array
    {
        return $this->getByType('liability');
    }
}
