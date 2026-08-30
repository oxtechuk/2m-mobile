<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PayrollController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'check.branch', 'audit.log'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/switch-branch', [DashboardController::class, 'switchBranch'])->name('switch.branch');

    // Profile (Auth default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Branches & Users (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('branches', BranchController::class);
        Route::resource('users', UserController::class);
        
        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // Payroll & HR Adjustments
    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('index');
        Route::post('/generate', [PayrollController::class, 'generate'])->name('generate');
        Route::post('/{payroll}/approve', [PayrollController::class, 'approve'])->name('approve');
        Route::post('/{payroll}/pay', [PayrollController::class, 'pay'])->name('pay');
        Route::post('/bulk-pay', [PayrollController::class, 'bulkPay'])->name('bulk-pay');
        Route::get('/{payroll}/payslip', [PayrollController::class, 'payslip'])->name('payslip');

        // Adjustments (Advances, Deductions, Bonuses)
        Route::get('/adjustments', [PayrollController::class, 'adjustments'])->name('adjustments');
        Route::post('/adjustments', [PayrollController::class, 'storeAdjustment'])->name('adjustments.store');
        Route::delete('/adjustments/{adjustment}', [PayrollController::class, 'destroyAdjustment'])->name('adjustments.destroy');
    });

    // Customers
    Route::resource('customers', CustomerController::class);

    // Categories
    Route::resource('categories', CategoryController::class);

    // Products Export & Import & Barcode
    Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
    Route::get('/products/import-template', [ProductController::class, 'importTemplate'])->name('products.import-template');
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::get('/products/barcode-studio', [ProductController::class, 'barcodeStudio'])->name('products.barcode-studio');
    Route::post('/products/barcode-studio/test-printer', [ProductController::class, 'testPrinter'])->name('products.test-printer');
    Route::post('/products/barcode-studio/direct-print', [ProductController::class, 'directPrint'])->name('products.direct-print');
    Route::post('/products/{product}/generate-barcode', [ProductController::class, 'generateBarcode'])->name('products.generate-barcode');
    Route::resource('products', ProductController::class);
    Route::get('products/{product}/barcode', [ProductController::class, 'barcode'])->name('products.barcode');

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/restock', [InventoryController::class, 'restock'])->name('inventory.restock');
    Route::post('/inventory/transfer', [InventoryController::class, 'transfer'])->name('inventory.transfer');

    // POS & Shifts
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('/pos/sale', [POSController::class, 'processSale'])->name('pos.sale');
    Route::get('/pos/customer-search', [POSController::class, 'customerSearch'])->name('pos.customer-search');
    Route::get('/pos/product-search', [POSController::class, 'productSearch'])->name('pos.product-search');
    
    // Shift Management
    Route::get('/pos/shift/status', [POSController::class, 'shiftStatus'])->name('pos.shift.status');
    Route::post('/pos/shift/open', [POSController::class, 'openShift'])->name('pos.shift.open');
    Route::post('/pos/shift/close', [POSController::class, 'closeShift'])->name('pos.shift.close');
    Route::post('/pos/shift/handover', [POSController::class, 'handoverShift'])->name('pos.shift.handover');
    Route::get('/pos/shift/{shift}/print', [POSController::class, 'printShiftSummary'])->name('pos.shift.print');

    // Sales
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/invoice', [SaleController::class, 'invoice'])->name('sales.invoice');
    Route::post('/sales/{sale}/direct-print', [SaleController::class, 'directPrint'])->name('sales.direct-print');
    Route::post('/sales/{sale}/void', [SaleController::class, 'void'])->name('sales.void')->middleware('can:process-return');

    // Returns (Protected by process-return permission)
    Route::middleware('can:process-return')->group(function () {
        Route::get('/returns', [\App\Http\Controllers\ReturnController::class, 'index'])->name('returns.index');
        Route::get('/returns/create', [\App\Http\Controllers\ReturnController::class, 'create'])->name('returns.create');
        Route::post('/returns', [\App\Http\Controllers\ReturnController::class, 'store'])->name('returns.store');
    });

    // Maintenance
    Route::resource('maintenance', MaintenanceController::class);
    Route::patch('/maintenance/{maintenanceRequest}/status', [MaintenanceController::class, 'updateStatus'])->name('maintenance.status');
    Route::patch('/maintenance/{maintenanceRequest}/assign', [MaintenanceController::class, 'assign'])->name('maintenance.assign');

    // Wallets & All Transactions
    Route::resource('wallets', WalletController::class)->except(['destroy']);
    Route::get('/wallets/{wallet}/transactions', [WalletController::class, 'transactions'])->name('wallets.transactions');
    Route::get('/transactions', [\App\Http\Controllers\TransactionController::class, 'index'])->name('transactions.index');

    // Transfers
    Route::get('/transfers', [TransferController::class, 'index'])->name('transfers.index');
    Route::post('/transfers', [TransferController::class, 'store'])->name('transfers.store');
    Route::patch('/transfers/{transfer}/approve', [TransferController::class, 'approve'])->name('transfers.approve');

    // Expenses
    Route::resource('expenses', ExpenseController::class);

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/maintenance', [ReportController::class, 'maintenance'])->name('reports.maintenance');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('/financial', [ReportController::class, 'financial'])->name('reports.financial');
        Route::get('/employees', [ReportController::class, 'employees'])->name('reports.employees');
        Route::get('/export/{type}', [ReportController::class, 'export'])->name('reports.export');
    });

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    // AJAX API endpoints
    Route::get('/api/dashboard-stats', [DashboardController::class, 'stats'])->name('api.dashboard-stats');
    Route::get('/api/chart-data/{type}', [DashboardController::class, 'chartData'])->name('api.chart-data');
});

require __DIR__.'/auth.php';
