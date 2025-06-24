<?php

namespace App\Services;

use App\Models\Order;
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

        if($user->role_id == 1) {
            $orders = $orders->where('manager_id', $user->id);
        }

        if($user->role_id == 2 && $statusId != 1) {
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

}
