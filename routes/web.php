<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductModelController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Livewire\Customers\Index;
use App\Livewire\Dashboard;
use App\Livewire\Locations\Index as LocationsIndex;
use App\Livewire\Products\Index as ProductsIndex;
use App\Livewire\Products\Model\Index as ModelIndex;
use App\Livewire\Purchases\Index as PurchasesIndex;
use App\Livewire\Roles\Index as RolesIndex;
use App\Livewire\Users\Index as UsersIndex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// routes/web.php
Route::get('/locations', LocationsIndex::class)
    ->name('locations.index');


// Print Report
Route::get('/print-report/{id}', [PrintController::class, 'report'])->name('report.print')->middleware('role:admin');


// // Users Route:
// Route::resource('users', UserController::class);


Route::get('/users', UsersIndex::class)
->name('users.index');

Route::get('/dashboard', Dashboard::class)->name('dashboard')->middleware('auth','role:admin');


// Route::get('my-customer', Customer::class);

// Route::get('/customers/{id}/emi-plans', [CustomerController::class, 'customerEmiPlans'])->name('customers.emi_plans')->middleware('auth');
// Route::get('/locations/{id}/customers', [CustomerController::class, 'showByLocation'])->name('location.customers')->middleware('auth');

Route::get('/customers', Index::class)->name('customers.index')->middleware('auth');
Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create')->middleware('auth');
Route::post('/customers/create', [CustomerController::class, 'store'])->name('customers.store')->middleware('auth');
Route::get('/customers/{id}/emi-plans', [Index::class, 'customerEmiPlans'])->name('customers.emi_plans')->middleware('auth');

Route::resource('purchases', PurchaseController::class);

// Route::get('/purchases', PurchasesIndex::class)->name('purchases.index');

Route::get('pdf', [PurchaseController::class, 'getpdf'])->name('pdf');
Route::get('/purchases/models/{productId}', [PurchaseController::class, 'getModels']);
Route::get('/autocomplete', [PurchaseController::class, 'autocomplete'])->name('autocomplete');

Route::resource('installments', InstallmentController::class);

Route::get('/products', ProductsIndex::class)->name('products.index');

Route::get('/products/models', ModelIndex::class)->name('products.model');

Route::get('/roles', RolesIndex::class)->name('roles.index');

Route::post('/installments/pay-multiple', [InstallmentController::class, 'payMultiple'])->name('installments.pay-multiple');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/payments/{payment}/edit', [InstallmentController::class, 'edit'])->name('payments.edit');
    Route::put('/payments/{payment}', [InstallmentController::class, 'update'])->name('payments.update');
    Route::delete('/payments/{payment}', [InstallmentController::class, 'destroy'])->name('payments.destroy');
});

Route::get('/reports/monthly', [ReportController::class, 'monthlyReport'])
    ->name('report')
    ->middleware('role:admin');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('role:admin');


Route::middleware(['auth', 'role:admin'])->group(function () {
    // Notice Controller 
    Route::resource('notices', NoticeController::class)->middleware('role:admin');
});
