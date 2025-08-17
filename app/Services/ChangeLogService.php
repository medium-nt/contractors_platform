<?php

namespace App\Services;

use App\Models\ChangeLog;
use App\Models\Order;
use Illuminate\Support\Collection;

class ChangeLogService
{
    public function getChangeLog(Order $order): Collection
    {
        return ChangeLog::query()
            ->where('order_id', $order->id)
            ->get();
    }

    public static function setChangeLog(Order $order, $text): void
    {
        ChangeLog::query()->create([
            'order_id' => $order->id,
            'user_id' => auth()->user()->id,
            'message' => $text,
        ]);
    }
}
