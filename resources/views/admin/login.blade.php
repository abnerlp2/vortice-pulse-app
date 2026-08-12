<x-layouts.app title="Acceso administrativo — Vórtice 2026">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="bg-brand-card p-8 rounded-2xl shadow-md w-full max-w-md">
            <img src="{{ asset('images/vortice-logo.svg') }}" alt="Vórtice 2026" width="140" height="32" class="h-8 w-auto mx-auto mb-8">

            <h1 class="text-2xl font-display mb-6 text-center text-brand-black">Acceso administrativo</h1>

            @if (session('error'))
                <p role="alert" class="mb-4 text-sm text-brand-orange-ink text-center font-medium">{{ session('error') }}</p>
            @endif

            <form action="{{ url('/admin/login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="password" class="block text-sm font-medium text-brand-black">Contraseña</label>
                    <input type="password" name="password" id="password" required autofocus autocomplete="current-password"
                        class="mt-1 block w-full min-h-[44px] px-3 py-2 bg-white text-brand-black border border-gray-300 rounded-xl shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-cyan focus:border-brand-purple sm:text-sm">
                </div>
                <button type="submit" class="btn btn-purple btn-block min-h-[44px] rounded-xl">
                    Entrar
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>
