<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationToastController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $sessionKey = 'sigc.toast.seen.'.$user->getAuthIdentifier();
        $seenNotificationIds = session()->get($sessionKey, []);
        $notifications = $user->unreadNotifications()
            ->whereNotIn('id', $seenNotificationIds)
            ->latest()
            ->limit(3)
            ->get();

        session()->put(
            $sessionKey,
            collect($seenNotificationIds)
                ->merge($notifications->pluck('id'))
                ->unique()
                ->take(-100)
                ->values()
                ->all(),
        );

        return response()
            ->json([
                'notifications' => $notifications->map(fn (DatabaseNotification $notification): array => [
                    'id' => $notification->id,
                    'title' => $notification->data['message'] ?? 'Tienes una nueva notificación.',
                    'data' => $notification->data,
                ])->values(),
            ])
            ->header('Cache-Control', 'no-store');
    }
}
