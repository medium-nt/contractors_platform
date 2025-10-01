<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function index(Request $request, NotificationService $notificationService)
    {
        return view('notifications.index',
            [
                'title' => 'Уведомления',
                'notificationsUser' => $notificationService->getFiltered($request)
                    ->paginate(10),
            ]);
    }

    public function show(Notification $notification)
    {
        $notification->update(['read_at' => now()]);

        return view('notifications.show',
            [
                'title' => $notification->title,
                'notification' => $notification,
            ]);
    }
}
