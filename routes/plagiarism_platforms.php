<?php

use App\Models\PlagiarismPlatform;

Route::prefix('/plagiarism_platforms')->group(function () {
    Route::get('', [App\Http\Controllers\PlagiarismPlatformController::class, 'index'])
        ->can('viewAny', PlagiarismPlatform::class)
        ->name('plagiarism_platforms.index');

    Route::get('/create', [App\Http\Controllers\PlagiarismPlatformController::class, 'create'])
        ->can('create', PlagiarismPlatform::class)
        ->name('plagiarism_platforms.create');

    Route::post('/store', [App\Http\Controllers\PlagiarismPlatformController::class, 'store'])
        ->can('create', PlagiarismPlatform::class)
        ->name('plagiarism_platforms.store');

    Route::get('/{plagiarism_platform}/edit', [App\Http\Controllers\PlagiarismPlatformController::class, 'edit'])
        ->can('update', 'plagiarism_platform')
        ->name('plagiarism_platforms.edit');

    Route::put('/update/{plagiarism_platform}', [App\Http\Controllers\PlagiarismPlatformController::class, 'update'])
        ->can('update', 'plagiarism_platform')
        ->name('plagiarism_platforms.update');

    Route::delete('/delete/{plagiarism_platform}', [App\Http\Controllers\PlagiarismPlatformController::class, 'destroy'])
        ->can('delete', 'plagiarism_platform')
        ->name('plagiarism_platforms.destroy');
});
