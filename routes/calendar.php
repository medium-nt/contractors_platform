<?php

use App\Models\Task;

Route::prefix('/calendar')->group(function () {
    Route::get('', [App\Http\Controllers\CalendarController::class, 'index'])
        ->can('viewAny', Task::class)
        ->name('calendar.index');
});
