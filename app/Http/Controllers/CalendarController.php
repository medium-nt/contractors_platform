<?php

namespace App\Http\Controllers;

use App\Services\CalendarService;

class CalendarController extends Controller
{
    public function index(CalendarService $calendarService)
    {
        $user = auth()->user();
        $tasks = $calendarService->getTasks($user);
        $orders = $calendarService->getOrders($user);
        $tasksOrders = $calendarService->getTasksOrders($user);

        return view('calendar.index', [
            'title' => 'Календарь',
            'events' => collect($tasks)
                ->merge(collect($tasksOrders))
                ->merge(collect($orders)),
        ]);
    }
}
