<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Customer;
use App\Models\Employee;

class DashboardController extends Controller
{
    public function index(): void
    {
        $companyId = (int) ($_SESSION['company_id'] ?? 0);
        $saleModel = new Sale();
        $saleModel->setTenantId($companyId);
        $purchaseModel = new Purchase();
        $purchaseModel->setTenantId($companyId);
        $productModel = new Product();
        $productModel->setTenantId($companyId);
        $orderModel = new Order();
        $orderModel->setTenantId($companyId);

        $salesThisMonth = $saleModel->getTotalByMonth((int) date('Y'), (int) date('n'));
        $salesList = $saleModel->getList();
        $recentSales = array_slice($salesList, 0, 5);
        $inventoryValue = $productModel->getValuation();
        $lowStock = $productModel->getLowStock();
        $ordersList = $orderModel->getList();
        $pendingOrders = array_filter($ordersList, fn($o) => in_array($o['current_status'], ['pending', 'confirmed', 'processing'], true));
        $ordersCount = count($ordersList);
        $pendingCount = count($pendingOrders);

        $companyUsers = [];
        $customersCount = 0;
        $employeesCount = 0;
        if ($companyId > 0) {
            $userModel = new User();
            $companyUsers = $userModel->getByCompany($companyId);
            $customerModel = new Customer();
            $customerModel->setTenantId($companyId);
            $customersCount = count($customerModel->getByCompany());
            $employeeModel = new Employee();
            $employeeModel->setTenantId($companyId);
            $employeesCount = count($employeeModel->getList());
        }

        $this->view('dashboard.index', [
            'pageTitle' => 'Dashboard',
            'salesThisMonth' => $salesThisMonth,
            'recentSales' => $recentSales,
            'inventoryValue' => $inventoryValue,
            'lowStock' => $lowStock,
            'ordersCount' => $ordersCount,
            'pendingCount' => $pendingCount,
            'companyUsers' => $companyUsers,
            'customersCount' => $customersCount,
            'employeesCount' => $employeesCount,
        ]);
    }

    public function admin(): void
    {
        $this->index();
    }

    public function super(): void
    {
        $companyModel = new \App\Models\Company();
        $companies = $companyModel->all();
        $userModel = new \App\Models\User();
        $this->view('dashboard.super', [
            'pageTitle' => 'Super Admin Dashboard',
            'companies' => $companies,
        ]);
    }
}
