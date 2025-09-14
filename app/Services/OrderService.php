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
use Illuminate\Support\Facades\Response;

class OrderService
{
    public static function getFiltered($request): Builder
    {
        $orders = Order::query();

        if ($request->has('search') && $request->search !== null && $request->search !== '') {
            $orders = $orders->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhere('hidden_field', 'like', '%' . $request->search . '%');
            });
        } else {
            $statusId = $request->status ?? 1;
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
        }

        $user = auth()->user();

        if($user->role->name == 'manager') {
            $orders = $orders->where('manager_id', $user->id);
        }

        if($user->role->name == 'expert' && $statusId != 1) {
            $orders = $orders->where('expert_id', $user->id);
        }

        return $orders->orderBy('created_at', 'desc');
    }

    public static function changeStatus(Order $order, Status $newStatus): bool
    {
        $roleName = auth()->user()->role->name;
        $accept = false;

        $oldStatus = Status::find($order->status_id);

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

            $text = 'Статус заказа изменился с "' . $oldStatus->title . '" на "' . $newStatus->title . '"';
            ChangeLogService::setChangeLog($order, $text);

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

                $folderName = match ($folder) {
                    'manager_files' => 'файлы менеджера',
                    'expert_files' => 'файлы эксперта',
                };

                $text = 'В раздел "' . $folderName . '" загружен файл "' . $filename . '"';
                ChangeLogService::setChangeLog($order, $text);

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

        $folderName = match ($folder) {
            'order_files' => 'файлы заказа',
            'manager_files' => 'файлы менеджера',
            'expert_files' => 'файлы эксперта',
        };

        $text = 'Из раздела "' . $folderName . '" удален файл "' . $fileName . '"';
        ChangeLogService::setChangeLog($order, $text);

        return true;
    }

    public static function sendExpertSelectionMessage(Order $order): void
    {
        TgService::sendMessage($order->expert->tg_id,
            'Вы выбраны исполнителем по заказу: ' . $order->id . ' ('. $order->title . "). \n" .
            'Ссылка на заказ ' . route('orders.show', $order->id) . "\n" .
            'Менеджер ' . $order->manager->name . ' ' . $order->manager->last_name
        );
    }

    public static function sendExpertMessageAboutReturnToWork(Order $order): void
    {
        TgService::sendMessage($order->expert->tg_id,
            'Заказ: ' . $order->id . ' ('. $order->title . ") возвращен вам на доработку. \n" .
            'Ссылка на заказ ' . route('orders.show', $order->id) . "\n" .
            'Менеджер ' . $order->manager->name . ' ' . $order->manager->last_name
        );
    }

    public static function sendManagerMessageAboutOrderInspection(Order $order): void
    {
        TgService::sendMessage(
            $order->manager->tg_id,
            'Исполнитель сдал заказ ' . $order->id . ' ('. $order->title . ") на проверку. \n" .
                'Ссылка на заказ ' . route('orders.show', $order->id) . "\n" .
                'Эксперт ' . $order->expert->name . ' ' . $order->expert->last_name
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
            $text = "Обратите внимание, что до сдачи заказа #{$order->id} \"{$order->title}\" (Эксперт: {$order->expert->name} {$order->expert->last_name}) осталось менее 50% срока:\n"
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
        switch ($folder) {
            case 'manager_files':
                $TgId = $order->expert->tg_id;
                $fio = 'ФИО менеджера: ' . $order->manager->name . ' ' . $order->manager->last_name;
                break;
            case 'expert_files':
                $TgId = $order->manager->tg_id;
                $fio = 'ФИО эксперта: ' . $order->expert->name . ' ' . $order->expert->last_name;
                break;
            default:
                $TgId = null;
                $fio = '';
        }

        TgService::sendMessage(
            $TgId,
            'В заказ #' . $order->id . ' ('. $order->title . ") добавлены новые дополнительные файлы. \n" .
                'Ссылка на заказ ' . route('orders.show', $order->id) . "\n" .
                $fio
        );
    }

    public static function finalizeWarrantyLifecycle(): void
    {
        Order::query()
            ->where('status_id', 5)
            ->whereDate('warranty_up_to', '<=', now())
            ->update([
                'status_id' => 6,
                'completed_at' => now(),
            ]);
    }

    public static function getCountOrders($role): array
    {
        switch ($role) {
            case 'manager':
                $users = User::query()->where('role_id', 1);
                $field = 'manager_id';
                break;
            case 'expert':
                $users = User::query()->where('role_id', 2);
                $field = 'expert_id';
                break;
            default:
                return [];
        }

        if(auth()->user()->role->name == $role) {
            $users = $users->where('id', auth()->id());
        }

        $allOrders = Order::query()->get();
        foreach ($users->get() as $user) {
            $managerOrders = $allOrders->where($field, $user->id);

            $return[$user->id] = [
                'name' => $user->name . ' ' . $user->last_name,
                'all' => $managerOrders->count(),
                'inWork' => $managerOrders->where('status_id', 2)->count(),
                'inFixing' => $managerOrders->where('status_id', 4)->count(),
                'warranty' => $managerOrders->where('status_id', 5)->count(),
                'done' => $managerOrders->where('status_id', 6)->count(),
            ];
        }

        return $return ?? [];
    }

}
