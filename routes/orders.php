<?php

use App\Models\Order;

Route::prefix('/orders')->group(function () {
    Route::get('', [App\Http\Controllers\OrdersController::class, 'index'])
        ->can('viewAny', Order::class)
        ->name('users.index');
});
