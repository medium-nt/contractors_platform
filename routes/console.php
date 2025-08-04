<?php

use App\Services\OrderService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    OrderService::sendExpertMessageIfDeadlineNowByOrders();
    OrderService::sendManagerMessageIfDeadlineNowByOrders();
    OrderService::sendManagerMessageIfDeadlineNowByTasks();
})->dailyAt('07:00');

Schedule::call(function () {
    OrderService::sendMessageIfHalfwayPassedByOrders();
})->hourly();

Schedule::call(function () {
    Log::info('тестируем запуск крона');
})->everyMinute();
