<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Task;
use Illuminate\Support\Collection;

class CalendarService
{
    public static function getTasks(): Collection
    {
        $tasks = Task::query()
            ->where('order_id', null)->get();

        if (auth()->user()->role->name == 'manager' || auth()->user()->role->name == 'expert') {
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
                    'id' => $task->id,
                    'description' => $task->description ?? '',
                    'completed_at' => $task->completed_at,
                    'owner_name' => ($task->manager->name  ?? '') .' '. ($task->manager->last_name  ?? ''),
                    'type' => 2,
                    'color' => '#a8cbfe',
                    'status' => 2
                ]
            ];
        });
    }

    public static function getTasksOrders(): Collection
    {
        $tasksOrders = Task::all();

        $orders = Order::query();
        if (auth()->user()->role->name == 'manager') {
            $orders = $orders->where('manager_id', auth()->user()->id);
        } elseif (auth()->user()->role->name == 'expert') {
            $orders = $orders->where('expert_id', auth()->user()->id);
        }

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
                    'id' => $task->id,
                    'description' => $task->description ?? '',
                    'completed_at' => $task->completed_at,
                    'manager_name' => ($task->order->manager->name ?? '') .' '. ($task->order->manager->last_name ?? ''),
                    'expert_name' => ($task->order->expert->name ?? '') .' '. ($task->order->expert->last_name ?? ''),
                    'type' => 1,
                    'color' => $task->order->status->color,
                    'status' => $task->order->status->id
                ]
            ];
        });
    }

    public static function getOrders(): Collection
    {
        $orders = Order::all();
        if (auth()->user()->role->name == 'manager') {
            $orders = $orders->where('manager_id', auth()->user()->id);
        } elseif (auth()->user()->role->name == 'expert') {
            $orders = $orders->where('expert_id', auth()->user()->id);
        }

        return $orders->map(function ($order) {
            return [
                'title' => 'Заказ: "' . $order->title . '"',
                'start' => $order->deadline_at,
                'url' => route('orders.show', $order->id),
                'allDay' => true,
                'extendedProps' => [
                    'id' => $order->id,
                    'description' => $order->description ?? '',
                    'completed_at' => $order->completed_at,
                    'manager_name' => ($order->manager->name ?? '') .' '. ($order->manager->last_name ?? ''),
                    'expert_name' => ($order->expert->name ?? '') .' '. ($order->expert->name ?? ''),
                    'hidden_field' => $order->hidden_field ?? '',
                    'type' => 0,
                    'color' => $order->status->color,
                    'status' => $order->status->id
                ]
            ];
        });
    }

}
