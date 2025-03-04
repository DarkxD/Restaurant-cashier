<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PinkodAuthController;
use App\Http\Controllers\CashierUsersController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ItemsController;

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

Route::get('/admin/cashierusers', [CashierUsersController::class, 'index'])->name('cashierusers');
Route::post('/admin/cashierusers', [CashierUsersController::class, 'store'])->name('cashierusers.store');
Route::get('/admin/fetch-cashierusers', [CashierUsersController::class, 'fetchCashierUsers'])->name('cashierusers.fetch');
Route::delete('/admin/cashieruser-delete/{id}', [CashierUsersController::class, 'deleteCashierUser'])->name('cashieruser.delete');

Route::get('/admin/items', [ItemsController::class, 'index'])->name('admin.items');
Route::post('/admin/items', [ItemsController::class, 'store'])->name('admin.items.store');
Route::get('/admin/fetch-items', [ItemsController::class, 'fetchItems'])->name('admin.items.fetch');
Route::get('/admin/edit-item/{id}', [ItemsController::class, 'edit'])->name('admin.items.edit');
Route::delete('/admin/delete-item/{id}', [ItemsController::class, 'deleteItem'])->name('admin.items.delete');


Route::get('/cashier/{id}', [CashierController::class, 'index'])->name('cashier');
//Route::get('/cashier', [PinkodAuthController::class, 'cashier'])->name('cashier')->middleware('\App\Http\Middleware\PinkodAuthenticated::class');
//Route::get('/cashier/{id}', [CashierController::class, 'edit'])->name('cashieruser');

Route::get('/welcome', function(){
    return view('welcome');
});