<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PinkodAuthController;
use App\Http\Controllers\CashierUsersController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\CheckboxController;

/* Route::get('/', function () {
    return view('cashier')->middleware('pinkod');
}); */


Route::get('/', [PinkodAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [PinkodAuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [PinkodAuthController::class, 'logout'])->name('logout');
Route::get('/home', [PinkodAuthController::class, 'home'])->name('home');

//Route::post('/home', [ClientController::class, ' store'])->name('client.store');
Route::post('/clients/store', [ClientController::class, 'store'])->name('clients.store');
Route::get('/clients/fetch-clients', [ClientController::class, 'fetchClients'])->name('clients.fetch');
Route::post('/create-new-client', [ClientController::class, 'createNewClient']);
Route::post('/create-new-invoice', [InvoiceController::class, 'createNewInvoice']);
Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');

Route::get('/admin/cashierusers', [CashierUsersController::class, 'index'])->name('cashierusers');
Route::post('/admin/cashierusers', [CashierUsersController::class, 'store'])->name('cashierusers.store');
Route::get('/admin/fetch-cashierusers', [CashierUsersController::class, 'fetchCashierUsers'])->name('cashierusers.fetch');
Route::delete('/admin/cashieruser-delete/{id}', [CashierUsersController::class, 'deleteCashierUser'])->name('cashieruser.delete');

Route::get('/admin/items', [ItemsController::class, 'index'])->name('admin.items');
Route::post('/admin/items', [ItemsController::class, 'store'])->name('admin.items.store');
Route::get('/admin/fetch-items', [ItemsController::class, 'fetchItems'])->name('admin.items.fetch');
Route::get('/admin/edit-item/{id}', [ItemsController::class, 'edit'])->name('admin.items.edit');
Route::delete('/admin/delete-item/{id}', [ItemsController::class, 'deleteItem'])->name('admin.items.delete');
Route::put('/admin/update-item/{id}', [ItemsController::class, 'update']);

Route::get('/cashier/{id}', [CashierController::class, 'index'])->name('cashier');
//Route::get('/cashier', [PinkodAuthController::class, 'cashier'])->name('cashier')->middleware('\App\Http\Middleware\PinkodAuthenticated::class');
//Route::get('/cashier/{id}', [CashierController::class, 'edit'])->name('cashieruser');

Route::post('/add-item-to-invoice', [InvoiceController::class, 'addItemToInvoice'])->name('add.item.to.invoice');
Route::delete('/delete-invoice-item/{id}', [InvoiceController::class, 'deleteInvoiceItem'])->name('delete.invoice.item');
Route::post('/move-items-to-new-invoice', [InvoiceController::class, 'moveItemsToNewInvoice']);
Route::post('/close-invoice', [InvoiceController::class, 'closeInvoice']);
Route::post('/delete-invoice', [InvoiceController::class, 'deleteInvoice']);
Route::get('/get-data-for-receipt/{id}', [CashierController::class, 'getDataForReceipt']);
Route::get('/get-data-for-receipt-by-client/{clientId}', [InvoiceController::class, 'getDataForReceiptByClient']);
Route::get('/get-receipt-data-by-client/{clientId}', [ReceiptController::class, 'getReceiptDataByClient']);
Route::post('/save-checkbox-state', [CheckboxController::class, 'saveState']);
Route::get('/load-checkbox-state', [CheckboxController::class, 'loadState']);

Route::prefix('admin')->group(function () {
    Route::resource('tags', TagController::class)->names([
        'index' => 'admin.tags',
        'store' => 'admin.tags.store',
        'update' => 'admin.tags.update',
        'destroy' => 'admin.tags.destroy',
    ]);

    Route::resource('categories', CategoryController::class)->names([
        'index' => 'admin.categories',
        'store' => 'admin.categories.store',
        'update' => 'admin.categories.update',
        'destroy' => 'admin.categories.destroy',
    ]);
});

Route::get('/admin/compare-images', [ImageController::class, 'compareImages'])->name('admin.images');
Route::delete('/admin/delete-image/{filename}', [ImageController::class, 'deleteImage'])->name('delete.image');


/* Route::post('/create-invoice', [InvoiceController::class, 'createInvoice']); */
Route::get('/print-invoice/{id}', [InvoiceController::class, 'printInvoice']);



Route::get('/welcome', function(){
    return view('welcome');
});