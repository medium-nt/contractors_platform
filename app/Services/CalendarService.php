<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Task;
use Illuminate\Support\Collection;

class CalendarService
{
    public static function getTasks(): Collection
    {
        $tasks = Task::all();

        if (auth()->user()->role->name == 'manager') {
            $tasks = $tasks->where('manager_id', auth()->user()->id);
        }

        return $tasks->map(function ($task) {
            return [
                'title' => 'Задача "' . $task->title . '"',
                'start' => $task->deadline_at,
                'url' => ($task->order_id)
                    ? route('orders.show', $task->order_id)
                    : route('tasks.edit', $task->id),
                'allDay' => true,
                'extendedProps' => [
                    'description' => $task->description ?? '',
                    'completed_at' => $task->completed_at
                ]
            ];
        });
    }

    public static function getTasksOrders(): Collection
    {
        $tasksOrders = Task::all();
        $orders = Order::query()->where('manager_id', auth()->user()->id);
        $tasksOrders = $tasksOrders->whereIn('order_id', $orders->pluck('id'));

        return $tasksOrders->map(function ($task) {
            return [
                'title' => 'Задача к заказу "' . $task->title . '"',
                'start' => $task->deadline_at,
                'url' => ($task->order_id)
                    ? route('orders.show', $task->order_id)
                    : route('tasks.edit', $task->id),
                'allDay' => true,
                'extendedProps' => [
                    'description' => $task->description ?? '',
                    'completed_at' => $task->completed_at
                ]
            ];
        });
    }

    public static function getOrders(): Collection
    {
        $orders = Order::all();
        if (auth()->user()->role->name == 'manager') {
            $orders = $orders->where('manager_id', auth()->user()->id);
        }

        return $orders->map(function ($order) {
            return [
                'title' => 'Заказ: "' . $order->title . '"',
                'start' => $order->deadline_at,
                'url' => route('orders.show', $order->id),
                'allDay' => true,
                'extendedProps' => [
                    'description' => $order->description ?? '',
                    'completed_at' => $order->completed_at
                ]
            ];
        });
    }

}
