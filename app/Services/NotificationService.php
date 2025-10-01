<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public static function getFiltered($request): Builder
    {
        return Notification::query()
            ->where('receiver_id', auth()->user()->id)
            ->when($request->type, fn($query) => $query->where('type', $request->type));
    }

    public static function create($type, $title, $body, $receiver_id, $sender_id, $order_id): void
    {
        $notifications = new Notification();
        $notifications->receiver_id = $receiver_id;
        $notifications->type = $type;
        $notifications->title = $title;
        $notifications->body = $body;
        $notifications->sender_id = $sender_id ?? null;
        $notifications->order_id = $order_id ?? null;
        $notifications->save();

        Log::channel('notifications')->info('Создано уведомление' . $notifications->id);
    }

}
