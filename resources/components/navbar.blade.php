<nav class="bg-white shadow-md px-6 py-4">
    <div class="max-w-7xl mx-auto flex items-center justify-between">

        {{-- Logo --}}
        <a href="{{ route('dashboard') }}"
            class="text-xl font-bold text-blue-600">
            ClínicaApp
        </a>

        {{-- Navegación --}}
        <div class="flex items-center gap-6">

            <a href="{{ route('dashboard') }}"
                class="text-gray-700 hover:text-blue-600">
                Inicio
            </a>

            <a href="{{ route('pacientes.index') }}"
                class="text-gray-700 hover:text-blue-600">
                Pacientes
            </a>

            <a href="{{ route('citas.index') }}"
                class="text-gray-700 hover:text-blue-600">
                Citas
            </a>

            <a href="{{ route('doctores.index') }}"
                class="text-gray-700 hover:text-blue-600">
                Doctores
            </a>

        </div>

        {{-- Usuario --}}
        @auth
            <div class="flex items-center gap-4">
                <span class="text-gray-600">
                    {{ auth()->user()->name }}
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit"
                            class="text-red-500 hover:text-red-700">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        @endauth

    </div>
</nav>