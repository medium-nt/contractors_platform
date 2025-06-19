<?php

use App\Models\Subject;

Route::prefix('/subjects')->group(function () {
    Route::get('', [App\Http\Controllers\SubjectController::class, 'index'])
        ->can('viewAny', Subject::class)
        ->name('subjects.index');

    Route::get('/create', [App\Http\Controllers\SubjectController::class, 'create'])
        ->can('create', Subject::class)
        ->name('subjects.create');

    Route::post('/store', [App\Http\Controllers\SubjectController::class, 'store'])
        ->can('create', Subject::class)
        ->name('subjects.store');

    Route::get('/{subject}/edit', [App\Http\Controllers\SubjectController::class, 'edit'])
        ->can('update', 'subject')
        ->name('subjects.edit');

    Route::put('/update/{subject}', [App\Http\Controllers\SubjectController::class, 'update'])
        ->can('update', 'subject')
        ->name('subjects.update');

    Route::delete('/delete/{subject}', [App\Http\Controllers\SubjectController::class, 'destroy'])
        ->can('delete', 'subject')
        ->name('subjects.destroy');
});
