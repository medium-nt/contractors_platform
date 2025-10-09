<?php

use App\Services\CalendarService;
use App\Services\OrderService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

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
    OrderService::finalizeWarrantyLifecycle();
})->dailyAt('01:00');

Schedule::call(function () {
    CalendarService::createNotificationsForDeadlineToday();
})->dailyAt('08:45');
