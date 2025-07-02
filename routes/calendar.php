<?php

use App\Models\Order;

Route::prefix('/calendar')->group(function () {
    Route::get('', [App\Http\Controllers\CalendarController::class, 'index'])
        ->can('viewAny', Order::class)
        ->name('calendar.index');
});
