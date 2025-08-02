<?php

namespace App\Services;

use App\Http\Requests\FileRequest;
use App\Models\Order;
use App\Models\Status;
use Illuminate\Database\Eloquent\Builder;
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
        $content = YandexDiskService::read('alexstud/' . $path);
        $filename = basename('alexstud/' . $path);

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
                $path = 'orders/' . $order->id . '/' . $folder . '/' . $filename;

                YandexDiskService::write($path, file_get_contents($file));
            }
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
        $tgId = $order->expert->tg_id;

        if (empty($tgId)) {
            return;
        }

        $text = 'Вы выбраны исполнителем по заказу: ' . $order->id . ' ('. $order->title . "). \n" .
            'Ссылка на заказ ' . route('orders.show', $order->id);

        Log::info('Отправлено сообщение в телеграм (tg_id: ' . $tgId . "): \n" . $text );

        TgService::sendMessage(
            $tgId,
            $text
        );
    }
}
