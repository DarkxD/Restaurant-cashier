<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PinkodAuthController;
use App\Http\Controllers\CashierUsersController;

/* Route::get('/', function () {
    return view('cashier')->middleware('pinkod');
}); */


Route::get('/', [PinkodAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [PinkodAuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [PinkodAuthController::class, 'logout'])->name('logout');
Route::get('/home', [PinkodAuthController::class, 'home'])->name('home')->middleware('\App\Http\Middleware\PinkodAuthenticated::class');
//Route::get('/cashierscontroller', [CashierConroller::class, 'index']);
Route::get('/admin/cashierusers', [CashierUsersController::class, 'index'])->name('cashierusers');
Route::post('/admin/cashierusers', [CashierUsersController::class, 'store'])->name('cashierusers.store');
Route::get('/admin/fetch-cashierusers', [CashierUsersController::class, 'fetchCashierUsers'])->name('cashierusers.fetch');
Route::delete('/admin/cashieruser-delete/{id}', [CashierUsersController::class, 'deleteCashierUser'])->name('cashieruser.delete');

Route::get('/cashier', [PinkodAuthController::class, 'cashier'])->name('cashier')->middleware('\App\Http\Middleware\PinkodAuthenticated::class');