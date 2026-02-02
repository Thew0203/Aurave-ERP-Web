<?php
namespace App\Models;

use App\Core\Model;

class SaleItem extends Model
{
    protected string $table = 'sale_items';

    public function __construct()
    {
        parent::__construct();
        $this->tenantId = null;
    }
}
