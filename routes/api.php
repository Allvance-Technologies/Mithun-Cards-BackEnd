<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\InventoryController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\ExpenseController;
use App\Http\Controllers\Api\V1\CardTypeController;
use App\Http\Controllers\Api\V1\SubcategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/test', function () {
        return response()->json(['status' => 'ok']);
    });
    // Public routes
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        // Orders
        Route::apiResource('orders', OrderController::class);
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']);

        // Customers
        Route::apiResource('customers', \App\Http\Controllers\Api\V1\CustomerController::class);

        // Inventory
        Route::get('inventory', [InventoryController::class, 'index']);
        Route::post('inventory', [InventoryController::class, 'store']);
        Route::put('inventory/{inventory}', [InventoryController::class, 'update']);
        Route::delete('inventory/{inventory}', [InventoryController::class, 'destroy']);
        Route::get('inventory/low-stock', [InventoryController::class, 'lowStock']);

        // Card Types (Level 1)
        Route::apiResource('card-types', CardTypeController::class);

        // Subcategories (Level 2) - nested under card types
        Route::apiResource('card-types.subcategories', SubcategoryController::class);

        // Expenses
        Route::apiResource('expenses', ExpenseController::class);

        // Reports
        Route::get('reports/summary', [ReportController::class, 'summary']);
    });
});
