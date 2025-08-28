<?php

use App\Models\User;

Route::prefix('/experts')->group(function () {
    Route::get('', [App\Http\Controllers\ExpertsController::class, 'index'])
        ->can('experts', User::class)
        ->name('experts.index');

    Route::get('/show/{user}', [App\Http\Controllers\ExpertsController::class, 'show'])
        ->can('experts', User::class)
        ->name('experts.show');

    Route::put('/update/{user}', [App\Http\Controllers\ExpertsController::class, 'update'])
        ->can('experts', 'user')
        ->name('experts.update');
});
