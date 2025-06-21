<?php

use App\Models\Order;

Route::prefix('/orders')->group(function () {
    Route::get('', [App\Http\Controllers\OrdersController::class, 'index'])
        ->can('viewAny', Order::class)
        ->name('orders.index');

    Route::get('/create', [App\Http\Controllers\OrdersController::class, 'create'])
        ->can('create', Order::class)
        ->name('orders.create');

    Route::post('/store', [App\Http\Controllers\OrdersController::class, 'store'])
        ->can('create', Order::class)
        ->name('orders.store');

    Route::get('/{order}/edit', [App\Http\Controllers\OrdersController::class, 'edit'])
        ->can('update', 'order')
        ->name('orders.edit');

    Route::put('/update/{order}', [App\Http\Controllers\OrdersController::class, 'update'])
        ->can('update', 'order')
        ->name('orders.update');

    Route::delete('/delete/{order}', [App\Http\Controllers\OrdersController::class, 'destroy'])
        ->can('delete', 'order')
        ->name('orders.destroy');
});
