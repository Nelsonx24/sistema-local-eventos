<?php

use App\Http\Controllers\Auth\StaffAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OthersController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [StaffAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [StaffAuthController::class, 'login']);
Route::post('/logout', [StaffAuthController::class, 'logout'])->name('logout');

Route::middleware(['auth:staff'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/calendar-pdf', [EventController::class, 'downloadCalendar'])->name('events.calendar-pdf');
    Route::get('/events/report-pdf', [EventController::class, 'downloadReport'])->name('events.report-pdf');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit-data', [EventController::class, 'editData'])->name('events.edit-data');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::post('/events/{event}/pay-balance', [EventController::class, 'payBalance'])->name('events.pay-balance');
    Route::post('/events/{event}/upload-contract', [EventController::class, 'uploadContract'])->name('events.upload-contract');
    Route::get('/events/{event}/download-contract', [EventController::class, 'downloadContract'])->name('events.download-contract');
    Route::post('/events/{event}/close', [EventController::class, 'close'])->name('events.close');
    Route::post('/events/types', [EventController::class, 'manageTypes'])->name('events.types');

    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/pdf', [InventoryController::class, 'downloadPdf'])->name('inventory.pdf');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::put('/inventory/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::post('/inventory/restock', [InventoryController::class, 'restock'])->name('inventory.restock');
    Route::post('/inventory/audit', [InventoryController::class, 'audit'])->name('inventory.audit');
    Route::post('/inventory/prices', [InventoryController::class, 'updatePrices'])->name('inventory.prices');

    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/event/{event}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/direct', [SaleController::class, 'directSale'])->name('sales.direct');
    Route::post('/sales', [SaleController::class, 'processSale'])->name('sales.process');
    Route::post('/sales/{sale}/print', [SaleController::class, 'printTicket'])->name('sales.print');
    Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');
    Route::post('/sales/event/{event}/close', [SaleController::class, 'closeEvent'])->name('sales.event.close');

    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/direct/{date}', [ReportController::class, 'directSales'])->name('reports.direct');
    Route::get('/reports/direct/{date}/pdf', [ReportController::class, 'directSalesPdf'])->name('reports.direct.pdf');
    Route::get('/reports/{event}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{event}/pdf', [ReportController::class, 'downloadPdf'])->name('reports.pdf');
    Route::delete('/reports/{event}', [ReportController::class, 'destroy'])->name('reports.destroy');

    Route::get('/others', [OthersController::class, 'index'])->name('others.index');
    Route::get('/others/qr', [OthersController::class, 'qr'])->name('others.qr');
    Route::post('/others/qr', [OthersController::class, 'updateQr'])->name('others.qr.update');
    Route::get('/others/assets', [OthersController::class, 'assets'])->name('others.assets');
    Route::get('/others/assets/pdf', [OthersController::class, 'downloadAssetsPdf'])->name('others.assets.pdf');
    Route::post('/others/assets', [OthersController::class, 'storeAsset'])->name('others.assets.store');
    Route::delete('/others/assets/{asset}', [OthersController::class, 'destroyAsset'])->name('others.assets.destroy');
    Route::get('/others/contract-settings', [OthersController::class, 'contractSettings'])->name('others.contract-settings');
    Route::post('/others/contract-settings', [OthersController::class, 'updateContractSettings'])->name('others.contract-settings.update');
    Route::get('/others/notifications', [OthersController::class, 'notifications'])->name('others.notifications');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

Route::get('/home', function () {
    return redirect('/dashboard');
});
