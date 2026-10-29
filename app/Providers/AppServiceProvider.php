<?php

namespace App\Providers;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $appUrl = (string) config('app.url');

        URL::useOrigin($appUrl);
        URL::forceScheme(parse_url($appUrl, PHP_URL_SCHEME) ?: null);

        $this->loadViewsFrom(resource_path('components'), 'portal');

        View::composer([
            'portal::paciente.sidebar',
            'portal::medico.sidebar',
            'portal::coordinacion.sidebar',
        ], function ($view): void {
            $user = auth()->user();
            $toastNotifications = collect();

            if ($user) {
                $sessionKey = 'sigc.toast.seen.'.$user->getAuthIdentifier();
                $seenNotificationIds = session()->get($sessionKey, []);
                $notifications = $user->unreadNotifications()
                    ->latest()
                    ->limit(20)
                    ->get();

                $toastNotifications = $notifications
                    ->reject(fn (DatabaseNotification $notification): bool => in_array($notification->id, $seenNotificationIds, true))
                    ->take(3)
                    ->map(function (DatabaseNotification $notification): array {
                        $data = $notification->data;

                        return [
                            'id' => $notification->id,
                            'title' => $data['message'] ?? 'Tienes una nueva notificación.',
                            'data' => $data,
                        ];
                    })
                    ->values();

                session()->put(
                    $sessionKey,
                    collect($seenNotificationIds)
                        ->merge($toastNotifications->pluck('id'))
                        ->unique()
                        ->take(-100)
                        ->values()
                        ->all(),
                );
            }

            $view->with('toastNotifications', $toastNotifications);
        });
    }
}
