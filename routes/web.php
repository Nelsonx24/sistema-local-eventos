<?php

use App\Http\Controllers\Auth\StaffAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\OthersController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [StaffAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [StaffAuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [StaffAuthController::class, 'logout'])->name('logout');

Route::middleware(['auth:staff'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/calendar-pdf', [EventController::class, 'downloadCalendar'])->name('events.calendar-pdf');
    Route::get('/events/report-pdf', [EventController::class, 'downloadReport'])->name('events.report-pdf');
    Route::match(['post', 'delete'], '/events/types', [EventController::class, 'manageTypes'])->name('events.types')->middleware('role:Administrador');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit-data', [EventController::class, 'editData'])->name('events.edit-data');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::post('/events/{event}/pay-balance', [EventController::class, 'payBalance'])->name('events.pay-balance');
    Route::post('/events/{event}/upload-contract', [EventController::class, 'uploadContract'])->name('events.upload-contract');
    Route::get('/events/{event}/download-contract', [EventController::class, 'downloadContract'])->name('events.download-contract');
    Route::post('/events/{event}/close', [EventController::class, 'close'])->name('events.close');

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

    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');

    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::get('/staff/{staff}', [StaffController::class, 'show'])->name('staff.show');
    Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store')->middleware('role:Administrador');
    Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update')->middleware('role:Administrador');
    Route::post('/staff/{staff}/password', [StaffController::class, 'changePassword'])->name('staff.password')->middleware('role:Administrador');
    Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy')->middleware('role:Administrador');

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

    Route::group(['middleware' => 'role:Administrador,Vendedor'], function () {
        Route::get('/store', [StoreController::class, 'index'])->name('store.index');
        Route::get('/store/productos', [StoreController::class, 'products'])->name('store.products');
        Route::get('/store/productos/crear', [StoreController::class, 'productCreate'])->name('store.products.create');
        Route::post('/store/productos', [StoreController::class, 'productStore'])->name('store.products.store');
        Route::get('/store/productos/ventas', [StoreController::class, 'productSales'])->name('store.products.sales');
        Route::post('/store/productos/venta', [StoreController::class, 'productProcessSale'])->name('store.products.sale.process');
        Route::post('/store/productos/add-stock', [StoreController::class, 'productAddStock'])->name('store.products.add-stock');
        Route::get('/store/productos/reporte', [StoreController::class, 'productReport'])->name('store.products.report');
        Route::get('/store/productos/reporte/imagenes', [StoreController::class, 'productImagesReport'])->name('store.products.report.images');
        Route::get('/store/productos/reporte/ventas', [StoreController::class, 'productSalesReport'])->name('store.products.report.sales');
        Route::post('/store/productos/tipos', [StoreController::class, 'productTypeStore'])->name('store.products.types.store');
        Route::post('/store/productos/tipos/{storeProductType}/delete', [StoreController::class, 'productTypeDestroy'])->name('store.products.types.destroy');
        Route::get('/store/productos/{storeProduct}', [StoreController::class, 'productShow'])->name('store.products.show');
        Route::get('/store/productos/{storeProduct}/editar', [StoreController::class, 'productEdit'])->name('store.products.edit');
        Route::put('/store/productos/{storeProduct}', [StoreController::class, 'productUpdate'])->name('store.products.update');
        Route::delete('/store/productos/{storeProduct}', [StoreController::class, 'productDestroy'])->name('store.products.destroy');
        Route::get('/store/regalos', [StoreController::class, 'gifts'])->name('store.gifts');
        Route::get('/store/regalos/crear', [StoreController::class, 'giftCreate'])->name('store.gifts.create');
        Route::post('/store/regalos', [StoreController::class, 'giftStore'])->name('store.gifts.store');
        Route::get('/store/regalos/ventas', [StoreController::class, 'giftSales'])->name('store.gifts.sales');
        Route::post('/store/regalos/venta', [StoreController::class, 'giftProcessSale'])->name('store.gifts.sale.process');
        Route::post('/store/regalos/add-stock', [StoreController::class, 'giftAddStock'])->name('store.gifts.add-stock');
        Route::get('/store/regalos/reporte', [StoreController::class, 'giftReport'])->name('store.gifts.report');
        Route::get('/store/regalos/reporte/imagenes', [StoreController::class, 'giftImagesReport'])->name('store.gifts.report.images');
        Route::get('/store/regalos/reporte/ventas', [StoreController::class, 'giftSalesReport'])->name('store.gifts.report.sales');
        Route::post('/store/regalos/tipos', [StoreController::class, 'giftTypeStore'])->name('store.gifts.types.store');
        Route::post('/store/regalos/tipos/{storeGiftType}/delete', [StoreController::class, 'giftTypeDestroy'])->name('store.gifts.types.destroy');
        Route::get('/store/regalos/{storeGift}', [StoreController::class, 'giftShow'])->name('store.gifts.show');
        Route::get('/store/regalos/{storeGift}/editar', [StoreController::class, 'giftEdit'])->name('store.gifts.edit');
        Route::put('/store/regalos/{storeGift}', [StoreController::class, 'giftUpdate'])->name('store.gifts.update');
        Route::delete('/store/regalos/{storeGift}', [StoreController::class, 'giftDestroy'])->name('store.gifts.destroy');
    });
});

Route::get('/home', function () {
    return redirect('/dashboard');
});
