<?php

namespace App\Http\Controllers;

use App\Services\CalendarService;

class CalendarController extends Controller
{
    public function index()
    {
        $tasks = CalendarService::getTasks();
        $orders = CalendarService::getOrders();
        $tasksOrders = CalendarService::getTasksOrders();

        return view('calendar.index', [
            'title' => 'Календарь',
            'events' => $tasks->merge($tasksOrders)->merge($orders)
        ]);
    }
}
