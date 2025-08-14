<?php

namespace App\Services;

use App\Http\Requests\FileRequest;
use App\Models\Order;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class OrderService
{
    public static function getFiltered($request): Builder
    {
        $statusId = $request->status ?? 1;

        $orders = Order::query();

        if ($statusId == 10) {
            $orders = $orders->whereIn('status_id', [5, 6]);
        } else {
            $orders = $orders->where('status_id', $statusId);
        }

        if ($request->has('type_work_id') && $request->type_work_id !== 'all') {
            $orders = $orders->where('type_work_id', $request->type_work_id);
        }

        if ($request->has('subject_id') && $request->subject_id !== 'all') {
            $orders = $orders->where('subject_id', $request->subject_id);
        }

        $user = auth()->user();

        if($user->role->name == 'manager') {
            $orders = $orders->where('manager_id', $user->id);
        }

        if($user->role->name == 'expert' && $statusId != 1) {
            $orders = $orders->where('expert_id', $user->id);
        }

        if ($request->has('search') && $request->search !== null && $request->search !== '') {
            $orders = $orders->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhere('hidden_field', 'like', '%' . $request->search . '%');
            });
        }

        return $orders;
    }

    public static function changeStatus(Order $order, Status $newStatus): bool
    {
        $roleName = auth()->user()->role->name;
        $accept = false;

        switch ($newStatus->id) {
            case 4:
                if ($roleName == 'manager' && ($order->status_id == 3 || $order->status_id == 5)) {
                    $accept = true;

                    self::sendExpertMessageAboutReturnToWork($order);
                }
                break;
            case 5:
            case 6:
                if ($roleName == 'manager' && $order->status_id == 3) {
                    $accept = true;
                    $order->completed_at = now();
                }
                break;
            case 7:
            case 8:
                if (($roleName == 'manager' || $roleName == 'admin') && $order->status_id == 1) {
                    $accept = true;
                }
                break;
            default:
                break;
        }

        if($accept) {
            $order->status_id = $newStatus->id;
            $order->save();
            return true;
        }

        return false;
    }

    public static function downloadFile($path)
    {
        $content = YandexDiskService::read($path);
        $filename = basename($path);

        return Response::make($content, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    public static function addFile(FileRequest $request, Order $order, $folder): RedirectResponse
    {
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = $file->getClientOriginalName();
                $path = 'alexstud/orders/' . $order->id . '/' . $folder . '/' . $filename;

                YandexDiskService::write($path, file_get_contents($file));
            }
            self::sendMessageAddFile($order, $folder);
        }

        return redirect()
            ->route('orders.show', ['order' => $order->id])
            ->with('success', 'Файл добавлен');
    }

    public static function deleteFile(Order $order, $fileName, $folder): bool
    {
        $result = YandexDiskService::deleteFile('alexstud/orders/' . $order->id . '/' . $folder . '/' . $fileName);

        if (!$result) {
            return false;
        }

        return true;
    }

    public static function sendExpertSelectionMessage(Order $order): void
    {
        TgService::sendMessage($order->expert->tg_id,
            'Вы выбраны исполнителем по заказу: ' . $order->id . ' ('. $order->title . "). \n" .
            'Ссылка на заказ ' . route('orders.show', $order->id)
        );
    }

    public static function sendExpertMessageAboutReturnToWork(Order $order): void
    {
        TgService::sendMessage($order->expert->tg_id,
            'Заказ: ' . $order->id . ' ('. $order->title . ") возвращен вам на доработку. \n" .
            'Ссылка на заказ ' . route('orders.show', $order->id)
        );
    }

    public static function sendManagerMessageAboutOrderInspection(Order $order): void
    {
        TgService::sendMessage(
            $order->manager->tg_id,
            'Исполнитель сдал заказ ' . $order->id . ' ('. $order->title . ") на проверку. \n" .
                'Ссылка на заказ ' . route('orders.show', $order->id)
        );
    }

    public static function sendExpertMessageIfDeadlineNowByOrders(): void
    {
        $orders = Order::query()
            ->whereDate('deadline_at', now())
            ->get();

        $groupedOrders = $orders->groupBy('expert_id');

        self::sendMessageListOrders($groupedOrders);
    }

    public static function sendManagerMessageIfDeadlineNowByOrders(): void
    {
        $orders = Order::query()
            ->whereDate('deadline_at', now())
            ->get();

        $groupedOrders = $orders->groupBy('manager_id');

        self::sendMessageListOrders($groupedOrders);
    }

    public static function sendManagerMessageIfDeadlineNowByTasks(): void
    {
        $tasks = Task::query()
            ->whereDate('deadline_at', now())
            ->get();

        $groupedTasks = $tasks->groupBy('manager_id');

        foreach ($groupedTasks as $userId => $userTasks) {
            $text = "Ваши задачи у которых сегодня дедлайн:\n";

            foreach ($userTasks as $task) {
                $text .= "- #{$task->id} {$task->title} (до {$task->deadline_at->format('H:i')})."
                    . " Ссылка на задачу: " . route('tasks.edit', $task->id) . "\n";
            }

            TgService::sendMessage(
                User::query()->find($userId)->tg_id,
                $text
            );
        }
    }

    public static function sendMessageIfHalfwayPassedByOrders(): void
    {
        $orders = Order::query()
            ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, NOW()) BETWEEN FLOOR(TIMESTAMPDIFF(HOUR, created_at, deadline_at) / 2) AND FLOOR(TIMESTAMPDIFF(HOUR, created_at, deadline_at) / 2) + 1')
            ->get();

        foreach ($orders as $order) {
            $text = "Обратите внимание, что до сдачи заказа #{$order->id} \"{$order->title}\" осталось менее 50% срока:\n"
                . " Ссылка на заказ: " . route('orders.edit', $order->id) . "\n";

            TgService::sendMessage(
                $order->manager->tg_id,
                $text
            );

            TgService::sendMessage(
                $order->expert->tg_id,
                $text
            );
        }
    }

    private static function sendMessageListOrders(Collection $groupedOrders): void
    {
        foreach ($groupedOrders as $userId => $userTasks) {
            $text = "Ваши заказы у которых сегодня дедлайн:\n";

            foreach ($userTasks as $task) {
                $text .= "- #{$task->id} {$task->title} (до {$task->deadline_at->format('H:i')})."
                    . " Ссылка на заказ: " . route('orders.edit', $task->id) . "\n";
            }

            TgService::sendMessage(
                User::query()->find($userId)->tg_id,
                $text
            );
        }
    }

    private static function sendMessageAddFile(Order $order, $folder): void
    {
        $TgId = match ($folder) {
            'manager_files' => $order->expert->tg_id,
            'expert_files' => $order->manager->tg_id,
        };

        TgService::sendMessage(
            $TgId,
            'В заказ #' . $order->id . ' ('. $order->title . ") добавлены новые дополнительные файлы. \n" .
                'Ссылка на заказ ' . route('orders.show', $order->id)
        );
    }
}
