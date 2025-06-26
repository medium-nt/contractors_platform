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

    Route::get('/{order}/show', [App\Http\Controllers\OrdersController::class, 'show'])
        ->can('show', 'order')
        ->name('orders.show');

    Route::put('/update/{order}', [App\Http\Controllers\OrdersController::class, 'update'])
        ->can('update', 'order')
        ->name('orders.update');

    Route::delete('/delete/{order}', [App\Http\Controllers\OrdersController::class, 'destroy'])
        ->can('delete', 'order')
        ->name('orders.destroy');

    Route::get('/{order}/take_to_work', [App\Http\Controllers\OrdersController::class, 'takeToWork'])
        ->can('takeToWork', 'order')
        ->name('orders.take_to_work');

    Route::put('/{order}/complete', [App\Http\Controllers\OrdersController::class, 'complete'])
        ->can('complete', 'order')
        ->name('orders.complete');

    Route::get('/{order}/{name}/download', [App\Http\Controllers\OrdersController::class, 'downloadFile'])
        ->can('downloadFile', 'order')
        ->name('orders.download');

    Route::get('/{order}/{name}/download_result', [App\Http\Controllers\OrdersController::class, 'downloadResultFile'])
        ->can('downloadFile', 'order')
        ->name('orders.download_result');

    Route::post('/{order}/{name}/delete', [App\Http\Controllers\OrdersController::class, 'deleteFile'])
        ->can('deleteFile', 'order')
        ->name('orders.delete');

    Route::get('/{order}/change_status/{status}', [App\Http\Controllers\OrdersController::class, 'changeStatus'])
        ->name('orders.change_status');
});
