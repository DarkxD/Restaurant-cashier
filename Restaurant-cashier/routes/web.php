<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PinkodAuthController;

/* Route::get('/', function () {
    return view('cashier')->middleware('pinkod');
}); */


Route::get('/', [PinkodAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [PinkodAuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [PinkodAuthController::class, 'logout'])->name('logout');
Route::get('/home', [PinkodAuthController::class, 'home'])->name('home')->middleware('\App\Http\Middleware\PinkodAuthenticated::class');

//Route::get('/cashier', [PinkodAuthController::class, 'cashier'])->name('cashier')->middleware('\App\Http\Middleware\PinkodAuthenticated::class');