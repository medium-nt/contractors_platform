<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

if (App::environment(['local'])) {
    Route::prefix('autologin')->group(function () {
        Route::get('/{email}', [App\Http\Controllers\UsersController::class, 'autologin'])
            ->name('users.autologin');
    });
}

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
    ->middleware(['auth', 'updateLastActive'])
    ->name('home');

Route::prefix('admin')->middleware(['auth', 'approve', 'updateLastActive'])->group(function () {

    require base_path('routes/users.php');
    require base_path('routes/profile.php');
    require base_path('routes/setting.php');
    require base_path('routes/orders.php');
    require base_path('routes/types_work.php');
    require base_path('routes/subjects.php');
    require base_path('routes/plagiarism_platforms.php');
    require base_path('routes/calendar.php');
    require base_path('routes/tasks.php');
    require base_path('routes/chat.php');
    require base_path('routes/disk.php');
    require base_path('routes/experts.php');
    require base_path('routes/notifications.php');

});
