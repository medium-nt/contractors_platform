<?php

use App\Models\Task;

Route::prefix('/disk')->group(function () {
    Route::get('', [App\Http\Controllers\DiskController::class, 'index'])
        ->can('viewAny', Task::class)
        ->name('disk.index');

    Route::post('/create_folder', [App\Http\Controllers\DiskController::class, 'createFolder'])
        ->name('disk.create_folder');

    Route::post('/upload', [App\Http\Controllers\DiskController::class, 'uploadFile'])
        ->name('disk.upload');

    Route::get('/delete', [App\Http\Controllers\DiskController::class, 'deleteFile'])
        ->name('disk.delete');
});
