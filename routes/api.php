<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    // Payment routes
    Route::post('/purchase', [PaymentController::class, 'purchase']);
    Route::post('/payment-callback', [PaymentController::class, 'handleCallback']);
    Route::get('/transaction/{orderId}/status', [PaymentController::class, 'checkStatus']);
});

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Marketplace routes
    Route::get('/products', [MarketplaceController::class, 'index']);
    Route::get('/products/{product}', [MarketplaceController::class, 'show']);
    Route::get('/products/search', [MarketplaceController::class, 'search']);
    Route::get('/my-transactions', [MarketplaceController::class, 'myTransactions']);

    // Payment routes
    Route::post('/purchase', [PaymentController::class, 'purchase']);
    Route::get('/transaction/{orderId}/status', [PaymentController::class, 'checkStatus']);

    // Admin routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Product management
        Route::get('/products', [ProductController::class, 'index']);
        Route::post('/products/sync', [ProductController::class, 'syncFromDigiflazz']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
    });
});
