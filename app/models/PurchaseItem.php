<?php
namespace App\Models;

use App\Core\Model;

class PurchaseItem extends Model
{
    protected string $table = 'purchase_items';

    public function __construct()
    {
        parent::__construct();
        $this->tenantId = null;
    }
}
