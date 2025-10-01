<?php

namespace App\Http\Controllers;

use App\Services\CalendarService;

class CalendarController extends Controller
{
    public function index(CalendarService $calendarService)
    {
        $tasks = $calendarService->getTasks();
        $orders = $calendarService->getOrders();
        $tasksOrders = $calendarService->getTasksOrders();

        return view('calendar.index', [
            'title' => 'Календарь',
            'events' => collect($tasks)
                ->merge(collect($tasksOrders))
                ->merge(collect($orders)),
        ]);
    }
}
