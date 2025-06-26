<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Status;
use Illuminate\Database\Eloquent\Builder;

class OrderService
{
    public static function getFiltered($request): Builder
    {
        $statusId = $request->status ?? 1;

        $orders = Order::query();

        $orders->where('status_id', $statusId);

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

}
