<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankingController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', fn () => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


// Protected routes
// Route::middleware(\App\Http\Middleware\CheckAuthenticated::class)->group(function () {

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/accounts', [BankingController::class, 'accList'])->name('accounts');//اظهار الحسابات 
Route::get('/internal-fund', [BankingController::class, 'intFund'])->name('internal-fund'); // تحويل داخلي
Route::post('/internal-fund', [BankingController::class, 'submitIntFund'])->name('internal-fund.submit');
Route::get('/transfer-bban', [BankingController::class, 'tranToBBAN'])->name('transfer-bban');
Route::post('/transfer-bban/verify', [BankingController::class, 'verifyBban'])->name('transfer-bban.verify');




// Route::get('/generate-token', [BankingController::class, 'generateToken'])->name('generate-token');
// Route::get('/change-pin', [BankingController::class, 'changePin'])->name('change-pin');
// Route::post('/change-pin', [BankingController::class, 'submitChangePin'])->name('change-pin.submit');
// Route::get('/subscription', [BankingController::class, 'subscription'])->name('subscription');
// Route::get('/transactions', [BankingController::class, 'accTrans'])->name('transactions');
// Route::get('/beneficiaries', [BankingController::class, 'mngBenf'])->name('beneficiaries');
// Route::get('/transfer-bban', [BankingController::class, 'tranToBBAN'])->name('transfer-bban');
// Route::post('/transfer-bban', [BankingController::class, 'submitTransferBBAN'])->name('transfer-bban.submit');
// Route::get('/external-fund', [BankingController::class, 'extFund'])->name('external-fund');
// Route::post('/external-fund', [BankingController::class, 'submitExtFund'])->name('external-fund.submit');
// Route::post('/internal-fund', [BankingController::class, 'submitIntFund'])->name('internal-fund.submit');
// Route::get('/payment', [BankingController::class, 'payment'])->name('payment');
// Route::post('/payment', [BankingController::class, 'submitPayment'])->name('payment.submit');

// });
