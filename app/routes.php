<?php
use App\Core\Router;

$router = new Router();

$router->get('/', 'HomeController@index');
$router->get('/auth/login', 'AuthController@loginForm', ['GuestMiddleware']);
$router->post('/auth/login', 'AuthController@login', ['GuestMiddleware']);
$router->get('/auth/verify-login', 'AuthController@verifyLogin');
$router->get('/auth/register', 'AuthController@registerForm', ['GuestMiddleware']);
$router->post('/auth/register', 'AuthController@register', ['GuestMiddleware']);
$router->get('/auth/logout', 'AuthController@logout');

$router->get('/profile', 'ProfileController@index', ['AuthMiddleware']);
$router->post('/profile', 'ProfileController@update', ['AuthMiddleware']);
$router->post('/profile/password', 'ProfileController@changePassword', ['AuthMiddleware']);

$router->group(function (Router $r) {
    $r->get('/dashboard', 'DashboardController@index', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/dashboard/admin', 'DashboardController@admin', ['AuthMiddleware', 'AdminMiddleware']);
    $r->get('/dashboard/super', 'DashboardController@super', ['AuthMiddleware', 'SuperAdminMiddleware']);

    $r->get('/inventory/products', 'InventoryController@products', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/inventory/products/create', 'InventoryController@productForm', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/inventory/products/create', 'InventoryController@productCreate', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/inventory/products/edit/{id}', 'InventoryController@productEdit', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/inventory/products/edit/{id}', 'InventoryController@productUpdate', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/inventory/products/delete/{id}', 'InventoryController@productDelete', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/inventory/stock', 'InventoryController@stock', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/inventory/stock-movement', 'InventoryController@stockMovement', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/inventory/categories', 'InventoryController@categories', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/inventory/categories', 'InventoryController@categorySave', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/inventory/suppliers', 'InventoryController@suppliers', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/inventory/suppliers/create', 'InventoryController@supplierForm', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/inventory/suppliers/create', 'InventoryController@supplierCreate', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/inventory/suppliers/edit/{id}', 'InventoryController@supplierEdit', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/inventory/suppliers/edit/{id}', 'InventoryController@supplierUpdate', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/inventory/alerts', 'InventoryController@alerts', ['AuthMiddleware', 'StaffMiddleware']);

    $r->get('/production/bom', 'ProductionController@bomList', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/production/bom/create', 'ProductionController@bomForm', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/production/bom/create', 'ProductionController@bomCreate', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/production/bom/edit/{id}', 'ProductionController@bomEdit', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/production/bom/edit/{id}', 'ProductionController@bomUpdate', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/production/orders', 'ProductionController@orders', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/production/orders/create', 'ProductionController@orderForm', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/production/orders/create', 'ProductionController@orderCreate', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/production/orders/view/{id}', 'ProductionController@orderView', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/production/orders/status/{id}', 'ProductionController@orderStatus', ['AuthMiddleware', 'StaffMiddleware']);

    $r->get('/sales', 'SalesController@index', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/sales/create', 'SalesController@create', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/sales/create', 'SalesController@store', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/sales/view/{id}', 'SalesController@viewSale', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/sales/invoice/{id}', 'SalesController@invoice', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/sales/customers', 'SalesController@customers', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/sales/customers', 'SalesController@customerSave', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/sales/customers/edit/{id}', 'SalesController@customerEdit', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/sales/customers/edit/{id}', 'SalesController@customerUpdate', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/sales/customers/delete/{id}', 'SalesController@customerDelete', ['AuthMiddleware', 'StaffMiddleware']);

    $r->get('/purchasing', 'PurchasingController@index', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/purchasing/create', 'PurchasingController@create', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/purchasing/create', 'PurchasingController@store', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/purchasing/view/{id}', 'PurchasingController@viewPurchase', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/purchasing/suppliers', 'PurchasingController@suppliers', ['AuthMiddleware', 'StaffMiddleware']);

    $r->get('/accounting/chart', 'AccountingController@chart', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/accounting/journal', 'AccountingController@journal', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/accounting/journal', 'AccountingController@journalSave', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/accounting/pl', 'AccountingController@profitLoss', ['AuthMiddleware', 'StaffMiddleware']);

    $r->get('/payroll/employees', 'PayrollController@employees', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/payroll/employees/create', 'PayrollController@employeeForm', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/payroll/employees/create', 'PayrollController@employeeCreate', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/payroll/employees/edit/{id}', 'PayrollController@employeeEdit', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/payroll/employees/edit/{id}', 'PayrollController@employeeUpdate', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/payroll/runs', 'PayrollController@runs', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/payroll/runs/create', 'PayrollController@runForm', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/payroll/runs/create', 'PayrollController@runCreate', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/payroll/payslip/{id}', 'PayrollController@payslip', ['AuthMiddleware', 'StaffMiddleware']);

    $r->get('/orders', 'OrderController@index', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/orders/view/{id}', 'OrderController@viewOrder', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/orders/status/{id}', 'OrderController@updateStatus', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/orders/payment/{id}', 'OrderController@updatePaymentStatus', ['AuthMiddleware', 'StaffMiddleware']);
    $r->post('/orders/delete/{id}', 'OrderController@orderDelete', ['AuthMiddleware', 'StaffMiddleware']);
    $r->get('/orders/invoice/{id}', 'OrderController@invoice', ['AuthMiddleware', 'StaffMiddleware']);

    $r->get('/companies', 'CompanyController@index', ['AuthMiddleware', 'SuperAdminMiddleware']);
    $r->get('/companies/create', 'CompanyController@create', ['AuthMiddleware', 'SuperAdminMiddleware']);
    $r->post('/companies/create', 'CompanyController@store', ['AuthMiddleware', 'SuperAdminMiddleware']);
    $r->get('/companies/edit/{id}', 'CompanyController@edit', ['AuthMiddleware', 'SuperAdminMiddleware']);
    $r->post('/companies/edit/{id}', 'CompanyController@update', ['AuthMiddleware', 'SuperAdminMiddleware']);
}, ['AuthMiddleware']);

$router->get('/store', 'StoreController@index');
$router->get('/store/product/{id}', 'StoreController@product');
$router->get('/store/cart', 'StoreController@cart');
$router->post('/store/cart/add', 'StoreController@cartAdd');
$router->post('/store/cart/update', 'StoreController@cartUpdate');
$router->post('/store/cart/remove', 'StoreController@cartRemove');
$router->get('/store/checkout', 'StoreController@checkout', ['AuthMiddleware']);
$router->post('/store/checkout', 'StoreController@checkoutSubmit', ['AuthMiddleware']);
$router->get('/store/orders', 'StoreController@myOrders', ['AuthMiddleware']);
$router->get('/store/orders/track/{id}', 'StoreController@orderTrack', ['AuthMiddleware']);
$router->post('/store/orders/cancel/{id}', 'StoreController@orderCancel', ['AuthMiddleware']);

return $router;
