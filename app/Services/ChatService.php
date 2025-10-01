<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ChatService
{
    public static function hasUnreadChatNotifications(Order $order, User $user): bool
    {
        return Notification::query()
            ->where('type', Notification::TYPE_CHAT)
            ->where('order_id', $order->id)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->exists();
    }

    public static function getNotificationRecipients(Order $order): array
    {
        $manager = $order->manager;
        $expert = $order->expert;
        $authId = auth()->id();

        if ($authId === $manager->id) {
            return [$expert];
        }

        if ($authId === $expert->id) {
            return [$manager];
        }

        return [$manager, $expert];
    }

    public static function getFioSummary(Order $order): string
    {
        $manager = $order->manager;
        $expert = $order->expert;
        $authId = auth()->id();

        if ($authId === $manager->id) {
            return "ФИО эксперта: {$expert->name} {$expert->last_name}";
        }

        if ($authId === $expert->id) {
            return "ФИО менеджера: {$manager->name} {$manager->last_name}";
        }

        return "ФИО менеджера: {$manager->name} {$manager->last_name} " .
            "ФИО эксперта: {$expert->name} {$expert->last_name}";
    }

    public static function getNewMessages($orderId, $lastMessageId): Collection
    {
        return Chat::with('sender')
            ->where('order_id', $orderId)
            ->where('id', '>', $lastMessageId)
            ->orderBy('id')
            ->get();
    }

}
