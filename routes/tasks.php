<?php

use App\Models\Task;

Route::prefix('/tasks')->group(function () {
    Route::get('/create', [App\Http\Controllers\TaskController::class, 'create'])
        ->can('create', Task::class)
        ->name('tasks.create');

    Route::post('/store', [App\Http\Controllers\TaskController::class, 'store'])
        ->can('create', Task::class)
        ->name('tasks.store');

    Route::get('/{task}/edit', [App\Http\Controllers\TaskController::class, 'edit'])
        ->can('update', 'task')
        ->name('tasks.edit');

    Route::put('/update/{task}', [App\Http\Controllers\TaskController::class, 'update'])
        ->can('update', 'task')
        ->name('tasks.update');

    Route::delete('/delete/{task}', [App\Http\Controllers\TaskController::class, 'destroy'])
        ->can('delete', 'task')
        ->name('tasks.destroy');

    Route::get('/{task}/complete', [App\Http\Controllers\TaskController::class, 'complete'])
        ->can('complete', 'task')
        ->name('tasks.complete');
});
