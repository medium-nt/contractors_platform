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

});
