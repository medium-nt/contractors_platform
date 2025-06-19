<?php

use App\Models\TypeWork;

Route::prefix('/types_work')->group(function () {
    Route::get('', [App\Http\Controllers\TypesWorkController::class, 'index'])
        ->can('viewAny', TypeWork::class)
        ->name('types_work.index');

    Route::get('/create', [App\Http\Controllers\TypesWorkController::class, 'create'])
        ->can('create', TypeWork::class)
        ->name('types_work.create');

    Route::post('/store', [App\Http\Controllers\TypesWorkController::class, 'store'])
        ->can('create', TypeWork::class)
        ->name('types_work.store');

    Route::get('/{type_work}/edit', [App\Http\Controllers\TypesWorkController::class, 'edit'])
        ->can('update', 'type_work')
        ->name('types_work.edit');

    Route::put('/update/{type_work}', [App\Http\Controllers\TypesWorkController::class, 'update'])
        ->can('update', 'type_work')
        ->name('types_work.update');

    Route::delete('/delete/{type_work}', [App\Http\Controllers\TypesWorkController::class, 'destroy'])
        ->can('delete', 'type_work')
        ->name('types_work.destroy');
});
