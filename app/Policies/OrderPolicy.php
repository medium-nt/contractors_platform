<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role->name == 'manager';
    }

    public function show(User $user, Order $order): bool
    {
        if($user->role->name == 'admin') {
            return true;
        }

        if ($user->role->name == 'expert' && ($user->id == $order->expert_id || $order->status_id == 1)) {
            return true;
        }

        if ($user->role->name == 'manager' && $user->id == $order->manager_id) {
            return true;
        }

        return false;
    }

    public function update(User $user, Order $order): bool
    {
        return $user->role->name == 'admin' || ($user->role->name == 'manager' && $user->id == $order->manager_id);
    }

    public function delete(User $user): bool
    {
        return false;
    }

    public function restore(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user): bool
    {
        return false;
    }

    public function takeToWork(User $user): bool
    {
        return $user->role->name == 'expert';
    }

    public function complete(User $user, Order $order): bool
    {
        return $user->role->name == 'expert' && $user->id == $order->expert_id;
    }

    public function downloadFile(User $user, Order $order): bool
    {
        return ($user->role->name == 'manager' && $user->id == $order->manager_id)
            || ($user->role->name == 'expert' && $user->id == $order->expert_id)
            || ($user->role->name == 'admin');
    }

    public function deleteFile(User $user, Order $order): bool
    {
        return ($user->role->name == 'manager' && $user->id == $order->manager_id)
            || ($user->role->name == 'admin');
    }
}
