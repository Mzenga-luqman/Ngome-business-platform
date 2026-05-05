<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\CreditorController;
use App\Http\Controllers\SubscriptionController;
use App\Livewire\Dashboard;
use App\Livewire\Creditors;
use App\Livewire\Finance;
use App\Livewire\Pos;
use App\Livewire\Predictions;
use App\Livewire\Products;
use App\Livewire\Reports;
use App\Livewire\Sales;
use App\Livewire\Staff;
use App\Livewire\StockInsights;
use Illuminate\Support\Facades\Route;

Route::middleware('set.locale')->group(function () {
    Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

    Route::get('/', function () {
	if (! auth()->check()) {
		return redirect()->route('login');
	}

	$user = auth()->user();

	if (! $user->is_admin && ! $user->hasActiveSubscription()) {
		return redirect()->route('subscription.plans');
	}

	return redirect()->route('dashboard');
    });

    Route::middleware('guest')->group(function () {
	Route::get('/login', [AuthController::class, 'create'])->name('login');
	Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:login')->name('login.store');
	Route::get('/forgot-password', [AuthController::class, 'forgotPasswordForm'])->name('password.forgot');
	Route::post('/forgot-password', [AuthController::class, 'requestPasswordReset'])
		->middleware('throttle:login')
		->name('password.request-reset');
	Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register')->name('register.store');
    });

    Route::middleware('auth')->group(function () {
	Route::get('/ptofile', fn () => redirect()->route('profile.edit'));
	Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
	Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
	Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
	Route::post('/profile/change-password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

	Route::get('/subscription/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');
	Route::get('/subscription/{subscription}/payment', [InvoiceController::class, 'show'])->name('invoice.show');
	Route::post('/subscription/{subscription}/payment', [InvoiceController::class, 'store'])->name('payment.store');
	Route::get('/subscription/status', [SubscriptionController::class, 'status'])->name('subscription.status');
	Route::get('/subscription/pending', [SubscriptionController::class, 'pending'])->name('subscription.pending');

	Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
		Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
		Route::post('/payments/{payment}/approve', [AdminController::class, 'approve'])->name('payments.approve');
		Route::post('/payments/{payment}/reject', [AdminController::class, 'reject'])->name('payments.reject');
		Route::post('/password-resets/{passwordResetRequest}/reset', [AdminController::class, 'resetUserPassword'])
			->name('password-resets.reset');
	});

	Route::middleware('check.subscription')->group(function () {
		Route::get('/dashboard',      Dashboard::class)->name('dashboard');
		Route::get('/products',       Products::class)->name('products');
		Route::get('/pos',            Pos::class)->name('pos');
		Route::get('/sales',          Sales::class)->name('sales');
		Route::get('/reports',        Reports::class)->name('reports');
		Route::get('/stock-insights', StockInsights::class)->name('stock-insights');
		Route::get('/receipt/{saleId}', [ReceiptController::class, 'show'])->name('receipt');
		Route::get('/predictions',    Predictions::class)->name('predictions');
		Route::get('/staff',          Staff::class)->name('staff');
		Route::get('/finance',        Finance::class)->name('finance');
				Route::get('/creditors',      Creditors::class)->name('creditors');
				Route::post('/creditors/{creditor}/mark-paid', [CreditorController::class, 'markPaid'])->name('creditors.mark-paid');
	});
    });
});
