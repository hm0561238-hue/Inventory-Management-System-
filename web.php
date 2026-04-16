<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockLedgerController;
use App\Http\Controllers\StockAlertController;
use App\Http\Controllers\BarcodeController;
use Illuminate\Support\Facades\Route;

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
Route::get('register', [AuthController::class, 'showRegister'])->name('register');
Route::post('register', [AuthController::class, 'register'])->name('register.post');

Route::middleware(['auth'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [SaleController::class, 'pos'])->name('dashboard');
    Route::get('sales/pos', [SaleController::class, 'pos'])->name('sales.pos');
    Route::post('sales/checkout', [SaleController::class, 'checkout'])->name('sales.checkout');
    Route::get('sales/history', [SaleController::class, 'history'])->name('sales.history');

    // Barcode scanning routes
    Route::post('barcode-search', [SaleController::class, 'barcodeSearch'])->name('barcode.search');
    Route::get('barcode', [BarcodeController::class, 'index'])->name('barcode.index');
    Route::post('api/barcode/search', [BarcodeController::class, 'search'])->name('api.barcode.search');
    Route::get('api/barcode/statistics', [BarcodeController::class, 'statistics'])->name('api.barcode.statistics');
    Route::get('api/barcode/recent', [BarcodeController::class, 'recent'])->name('api.barcode.recent');

    // Stock alert routes
    Route::get('alerts', [StockAlertController::class, 'index'])->name('alerts.index');
    Route::get('api/alerts', [StockAlertController::class, 'getAlerts'])->name('api.alerts');
    Route::post('api/alerts/{alert}/resolve', [StockAlertController::class, 'resolve'])->name('api.alerts.resolve');
    Route::post('api/alerts/resolve-bulk', [StockAlertController::class, 'resolveBulk'])->name('api.alerts.resolve-bulk');

    Route::middleware(['role:admin'])->group(function () {
        Route::resource('products', ProductController::class)->except(['show']);
        Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store']);
        Route::get('stock-ledger', [StockLedgerController::class, 'index'])->name('stock.ledger.index');
        Route::get('stock-ledger/product/{product}', [StockLedgerController::class, 'productLedger'])->name('stock.ledger.product');
        Route::get('stock-ledger/filter', [StockLedgerController::class, 'filter'])->name('stock.ledger.filter');

        // Enhanced reporting routes
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/analytics', [ReportController::class, 'analytics'])->name('reports.analytics');
        Route::get('api/reports/chart-data', [ReportController::class, 'chartData'])->name('api.reports.chart-data');

        // Export routes
        Route::get('reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
        Route::get('reports/export-detailed-csv', [ReportController::class, 'exportDetailedCsv'])->name('reports.export.detailed.csv');
        Route::get('reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::get('reports/export-inventory-pdf', [ReportController::class, 'exportInventoryPdf'])->name('reports.export.inventory.pdf');
        Route::get('reports/export-alerts-csv', [ReportController::class, 'exportAlertsCsv'])->name('reports.export.alerts.csv');
    });
});
