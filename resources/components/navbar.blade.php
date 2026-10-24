<header class="h-[82px] border-b border-[rgba(28,48,43,0.08)] bg-[rgba(245,247,241,0.94)] max-[760px]:h-[70px]">
    <nav class="mx-auto flex h-full w-[calc(100%-80px)] max-w-[1240px] items-center justify-between max-[1050px]:w-[calc(100%-52px)] max-[1050px]:max-w-[860px] max-[760px]:w-[calc(100%-40px)] max-[760px]:max-w-none max-[520px]:w-[calc(100%-32px)]" aria-label="Navegación principal">
        <x-brand />
        <div class="flex items-center gap-[38px] text-[13px] font-semibold text-[#52615b] max-[760px]:gap-[14px] max-[760px]:text-[11px] max-[520px]:gap-[10px]">
            <a class="transition-colors duration-200 hover:text-[var(--coral)]" href="#funciones">Funciones</a>
            <a class="transition-colors duration-200 hover:text-[var(--coral)] max-[760px]:hidden" href="#beneficios">Para tu clínica</a>
            <a class="transition-colors duration-200 hover:text-[var(--coral)] max-[520px]:hidden" href="#contacto">Contacto</a>
        </div>
        <div class="flex items-center gap-2 max-[520px]:gap-1">
            @auth
                <a href="{{ Auth::user()->redirectToPanel() }}" class="inline-flex min-h-10 items-center justify-center gap-2 border border-[#b9c7bc] px-3 text-[11px] font-bold transition-colors duration-200 hover:bg-[#e8eee6] max-[760px]:min-h-9 max-[760px]:px-2 max-[760px]:text-[10px]">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-[var(--green)] text-white text-[10px] font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                    <span>{{ Auth::user()->name }}</span>
                </a>
            @else
                <a class="inline-flex min-h-10 items-center justify-center border border-[#b9c7bc] px-3 text-[11px] font-bold transition-colors duration-200 hover:bg-[#e8eee6] max-[760px]:min-h-9 max-[760px]:px-2 max-[760px]:text-[10px] max-[520px]:min-h-8 max-[520px]:px-[6px] max-[520px]:text-[9px]" href="{{ route('login') }}">Iniciar sesión</a>
                <a class="inline-flex min-h-10 items-center justify-center gap-2 border border-[var(--green)] bg-[var(--green)] px-3 text-[11px] font-bold text-white transition-colors duration-200 hover:bg-[var(--green-dark)] max-[760px]:min-h-9 max-[760px]:gap-1 max-[760px]:px-2 max-[760px]:text-[10px] max-[520px]:min-h-8 max-[520px]:gap-0 max-[520px]:px-[6px] max-[520px]:text-[9px]" href="{{ route('register') }}">Crear cuenta <span aria-hidden="true">↗</span></a>
            @endauth
        </div>
    </nav>
</header>