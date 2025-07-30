<?php

Route::prefix('/profile')->withoutMiddleware('approve')->group(function () {
    Route::get('', [App\Http\Controllers\UsersController::class, 'profile'])
        ->name('profile');
    Route::put('', [App\Http\Controllers\UsersController::class, 'profileUpdate'])
        ->name('profile.update');
    Route::get('disconnectTg', [App\Http\Controllers\UsersController::class, 'disconnectTg'])
        ->name('profile.disconnectTg');
});
