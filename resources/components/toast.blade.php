@props([
    'position' => 'top-right',
])

<div
    id="app-toast-container"
    class="sigc-toast-container sigc-toast-{{ $position }}"
    aria-live="polite"
    aria-label="Notificaciones"
></div>

<script>
    (function() {
        const container = document.getElementById('app-toast-container');
        if (!container) return;

        function createToast(message, type = 'success', timeout = 4000) {
            const toast = document.createElement('div');
            toast.className = `sigc-toast sigc-toast--${type}`;
            toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
            toast.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');

            const content = document.createElement('div');
            content.className = 'sigc-toast__content';
            content.textContent = message;

            const close = document.createElement('button');
            close.className = 'sigc-toast__close';
            close.type = 'button';
            close.setAttribute('aria-label', 'Cerrar notificación');
            close.innerHTML = '&times;';

            close.addEventListener('click', function() {
                toast.classList.add('sigc-toast--hide');
                setTimeout(function() { toast.remove(); }, 250);
            });

            toast.appendChild(content);
            toast.appendChild(close);
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

        window.SigcToast = {
            success: function(message, timeout) { createToast(message, 'success', timeout || 4000); },
            error: function(message, timeout) { createToast(message, 'error', timeout || 5000); },
            info: function(message, timeout) { createToast(message, 'info', timeout || 4000); },
        };

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
