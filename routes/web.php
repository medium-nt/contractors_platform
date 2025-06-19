<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('admin')->middleware(['auth', 'approve'])->group(function () {

    require base_path('routes/users.php');
    require base_path('routes/profile.php');
    require base_path('routes/setting.php');
    require base_path('routes/orders.php');
    require base_path('routes/types_work.php');
    require base_path('routes/subjects.php');
    require base_path('routes/plagiarism_platforms.php');

});
