<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CalendarService
{
    public static function getTasks($user): Collection
    {
        $tasks = self::getTasksQuery($user);

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

    public static function getTasksOrders($user): Collection
    {
        $tasksOrders = self::getTasksOrdersQuery($user);

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

    public static function getOrders($user): Collection
    {
        $orders = self::getOrdersQuery($user);

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

    private static function getTasksQuery($user): Collection
    {
        $tasks = Task::query()
            ->where('order_id', null)->get();

        if ($user->role->name == 'manager' || $user->role->name == 'expert') {
            $tasks = $tasks->where('manager_id', $user->id);
        }

        return $tasks;
    }

    private static function getTasksOrdersQuery($user): Collection
    {
        $tasksOrders = Task::all();

        $orders = Order::query();
        if ($user->role->name == 'manager') {
            $orders = $orders->where('manager_id', $user->id);
        } elseif ($user->role->name == 'expert') {
            $orders = $orders->where('expert_id', $user->id);
        }

        return $tasksOrders->whereIn('order_id', $orders->pluck('id'));
    }

    private static function getOrdersQuery($user): Collection
    {
        $orders = Order::all();
        if ($user->role->name == 'manager') {
            $orders = $orders->where('manager_id', $user->id);
        } elseif ($user->role->name == 'expert') {
            $orders = $orders->where('expert_id', $user->id);
        }

        return $orders;
    }

    public static function getCollectByDeadlineToday($user): Collection
    {
        $collection = collect(self::getTasksQuery($user))
            ->merge(collect(self::getTasksOrdersQuery($user)))
            ->merge(collect(self::getOrdersQuery($user)))
            ->filter(function ($item) {
                // у которых сегодня дедлайн и они еще не выполнены.
                return Carbon::parse($item->deadline_at)->isSameDay(Carbon::today())
                    && $item->completed_at === null;
             });

        $collection->map(function ($item) {
            if (isset($item['order_id'])) {
                $item['type'] = 'task_order'; // Задача к заказу
            } elseif ($item['price']) {
                $item['type'] = 'order'; // Заказ
            } else {
                $item['type'] = 'task'; // Задача без заказа
            }
            return $item;
        });

        return $collection;
    }

    public static function createNotificationsForDeadlineToday(): void
    {
        $users = User::where('role_id', '!=', 3)->get();

        foreach ($users as $user) {
            $collection = self::getCollectByDeadlineToday($user);
            foreach ($collection as $item) {

                $orderId = match ($item['type']) {
                    'order' => $item['id'],
                    'task_order' => $item['order_id'],
                    default => null,
                };

                NotificationService::create(
                    Notification::TYPE_CALENDAR,
                    'Сегодня дедлайн!',
                    'Дедлайн ' . (
                        ($item['type'] == 'order')
                            ? 'по заказу '
                            : 'по задаче '
                    ) . '"' .$item['title'] . '"',
                    $user->id,
                    null,
                    $orderId,
                );
            }
        }

        Log::channel('notifications')->info('Все уведомления о дедлайне созданы.');
    }

}
