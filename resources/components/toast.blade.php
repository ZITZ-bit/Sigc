@props([
    'position' => 'top-right',
    'notifications' => null,
    'pollUrl' => null,
])

<div
    id="app-toast-container"
    class="sigc-toast-container sigc-toast-{{ $position }}"
    aria-live="polite"
    aria-label="Notificaciones"
    @if ($notifications?->isNotEmpty()) data-notification-toast @endif
    @if ($pollUrl) data-poll-url="{{ $pollUrl }}" @endif
></div>

<script>
    (function() {
        const container = document.getElementById('app-toast-container');
        if (!container) return;

        function createToast(message, type = 'success', timeout = 4000, details = '', variant = '') {
            const toast = document.createElement('div');
            toast.className = `sigc-toast sigc-toast--${type}${variant ? ` sigc-toast--${variant}` : ''}`;
            toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
            toast.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
            toast.style.setProperty('--toast-duration', `${timeout}ms`);

            const icon = document.createElement('span');
            icon.className = 'sigc-toast__icon';
            icon.setAttribute('aria-hidden', 'true');
            icon.textContent = variant === 'notification' ? '✦' : type === 'error' ? '!' : type === 'success' ? '✓' : 'i';

            const content = document.createElement('div');
            content.className = 'sigc-toast__content';

            if (variant === 'notification') {
                const eyebrow = document.createElement('span');
                eyebrow.className = 'sigc-toast__eyebrow';
                eyebrow.textContent = 'Nueva notificación';
                content.appendChild(eyebrow);
            }

            const title = document.createElement('strong');
            title.className = 'sigc-toast__title';
            title.textContent = message;
            content.appendChild(title);

            if (details) {
                const description = document.createElement('span');
                description.className = 'sigc-toast__details';
                description.textContent = details;
                content.appendChild(description);
            }

            const close = document.createElement('button');
            close.className = 'sigc-toast__close';
            close.type = 'button';
            close.setAttribute('aria-label', 'Cerrar notificación');
            close.innerHTML = '&times;';

            close.addEventListener('click', function() {
                toast.classList.add('sigc-toast--hide');
                setTimeout(function() { toast.remove(); }, 250);
            });

            toast.appendChild(icon);
            toast.appendChild(content);
            toast.appendChild(close);
            if (timeout > 0) {
                const progress = document.createElement('span');
                progress.className = 'sigc-toast__progress';
                progress.setAttribute('aria-hidden', 'true');
                toast.appendChild(progress);
            }
            container.appendChild(toast);

            // trigger animation
            requestAnimationFrame(function() {
                toast.classList.add('sigc-toast--show');
            });

            if (timeout > 0) {
                setTimeout(function() {
                    if (toast.parentElement) {
                        toast.classList.add('sigc-toast--hide');
                        setTimeout(function() { toast.remove(); }, 250);
                    }
                }, timeout);
            }
        }

        function formatNotificationDetails(data) {
            const details = [data.paciente, data.medico, data.especialidad].filter(Boolean);

            if (data.fecha) {
                const date = new Date(`${data.fecha}T12:00:00Z`);
                details.push(new Intl.DateTimeFormat('es', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                    timeZone: 'UTC',
                }).format(date));
            }

            if (data.hora) {
                details.push(String(data.hora).slice(0, 5));
            }

            return details.join(' · ');
        }

        function showNotification(notification) {
            createToast(notification.title, 'info', 6500, formatNotificationDetails(notification.data || {}), 'notification');
        }

        window.SigcToast = {
            success: function(message, timeout) { createToast(message, 'success', timeout || 4000); },
            error: function(message, timeout) { createToast(message, 'error', timeout || 5000); },
            info: function(message, timeout) { createToast(message, 'info', timeout || 4000); },
        };

        @foreach ($notifications ?? [] as $notification)
            showNotification(@json($notification));
        @endforeach

        const pollUrl = container.dataset.pollUrl;
        if (pollUrl) {
            window.setInterval(function() {
                fetch(pollUrl, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                })
                    .then(function(response) {
                        if (!response.ok) {
                            throw new Error(`Notification polling failed with status ${response.status}`);
                        }

                        return response.json();
                    })
                    .then(function(payload) {
                        payload.notifications.forEach(showNotification);
                    })
                    .catch(function(error) {
                        console.error('Unable to check for new notifications.', error);
                    });
            }, 10000);
        }

        @if(session('success'))
            window.SigcToast.success(@json(session('success')));
        @endif
        @if(session('error'))
            window.SigcToast.error(@json(session('error')));
        @endif
        @if($errors->any())
            window.SigcToast.error(@json($errors->first()));
        @endif
    })();
</script>
