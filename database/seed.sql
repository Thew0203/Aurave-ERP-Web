-- Aurave ERP - SaaS-Safe Seed Data
-- Safe to commit to GitHub. No real personal data. No employees/customers.
-- Default system password: password (CHANGE ON FIRST LOGIN)

-- Super Admin only (global; company_id NULL). System email for localhost/demo.
-- INSERT IGNORE = safe to re-run; existing rows are skipped (no duplicate errors).
INSERT IGNORE INTO `companies` (`id`, `name`, `slug`, `email`, `phone`, `address`, `tax_id`, `is_active`) VALUES
(1, 'Aruave Demo', 'aruave-demo', NULL, NULL, NULL, NULL, 1);

INSERT IGNORE INTO `users` (`id`, `company_id`, `email`, `password`, `name`, `role`, `is_active`) VALUES
(1, NULL, 'superadmin@system.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Admin', 'super_admin', 1);

-- Electronics & IT categories for demo company. New tenants create their own via UI.
INSERT IGNORE INTO `categories` (`id`, `company_id`, `name`, `slug`, `description`) VALUES
(1, 1, 'Arduino & Microcontrollers', 'arduino-microcontrollers', 'Development boards and microcontrollers'),
(2, 1, 'Networking', 'networking', 'Routers, switches, cables, and network equipment'),
(3, 1, 'Computers & Components', 'computers-components', 'PCs, laptops, CPUs, RAM, storage'),
(4, 1, 'Electronics Components', 'electronics-components', 'Resistors, capacitors, sensors, ICs'),
(5, 1, 'Cables & Peripherals', 'cables-peripherals', 'Cables, adapters, keyboards, monitors'),
(6, 1, 'Software & Licenses', 'software-licenses', 'Software and digital licenses');

-- System chart of accounts for demo company. New tenants get default COA on registration.
INSERT IGNORE INTO `chart_of_accounts` (`id`, `company_id`, `code`, `name`, `type`, `is_system`) VALUES
(1, 1, '1000', 'Cash', 'asset', 1),
(2, 1, '1100', 'Accounts Receivable', 'asset', 1),
(3, 1, '1200', 'Inventory', 'asset', 1),
(4, 1, '2000', 'Accounts Payable', 'liability', 1),
(5, 1, '3000', 'Equity', 'equity', 1),
(6, 1, '4000', 'Sales Revenue', 'revenue', 1),
(7, 1, '5000', 'Cost of Goods Sold', 'expense', 1),
(8, 1, '6000', 'Operating Expenses', 'expense', 1);

-- Demo supplier and product for store/OMS (company 1). IGNORE = safe to re-run, no duplicate errors.
INSERT IGNORE INTO `suppliers` (`id`, `company_id`, `name`, `email`, `phone`, `is_active`) VALUES
(1, 1, 'Demo Supplier', 'demo@supplier.local', NULL, 1);

INSERT IGNORE INTO `products` (`id`, `company_id`, `category_id`, `supplier_id`, `sku`, `name`, `description`, `unit`, `cost_price`, `selling_price`, `quantity`, `low_stock_threshold`, `is_active`) VALUES
(1, 1, 1, 1, 'ARDUINO-001', 'Arduino Uno R3 (Demo)', 'Demo product for store and OMS', 'pcs', 15.00, 24.99, 50.000, 5.000, 1);

-- Demo e-commerce orders (OMS) so admin can see and test order status.
INSERT IGNORE INTO `orders` (`id`, `company_id`, `customer_id`, `user_id`, `order_number`, `subtotal`, `tax_amount`, `shipping_amount`, `discount_amount`, `total`, `payment_method`, `payment_status`, `current_status`, `shipping_name`, `shipping_address`) VALUES
(1, 1, NULL, NULL, 'ORD000001', 49.98, 0.00, 5.00, 0.00, 54.98, 'cod', 'pending', 'pending', 'Demo Customer', '123 Demo St'),
(2, 1, NULL, NULL, 'ORD000002', 24.99, 0.00, 5.00, 0.00, 29.99, 'cod', 'pending', 'confirmed', 'Guest Buyer', '456 Sample Ave');

INSERT IGNORE INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `quantity`, `unit_price`, `total`) VALUES
(1, 1, 1, 'Arduino Uno R3 (Demo)', 2.000, 24.99, 49.98),
(2, 2, 1, 'Arduino Uno R3 (Demo)', 1.000, 24.99, 24.99);

INSERT IGNORE INTO `order_status_history` (`id`, `order_id`, `status`, `notes`, `created_by`) VALUES
(1, 1, 'pending', 'Order placed', NULL),
(2, 2, 'pending', 'Order placed', NULL),
(3, 2, 'confirmed', 'Confirmed by admin', NULL);
