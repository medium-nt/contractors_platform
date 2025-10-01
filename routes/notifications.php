<?php

use App\Http\Controllers\NotificationController;

Route::prefix('/notifications')->group(function () {
    Route::get('', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::get('/{notification}', [NotificationController::class, 'show'])
        ->name('notifications.show');

});
