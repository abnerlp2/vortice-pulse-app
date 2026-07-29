<div class="max-w-md mx-auto min-h-screen bg-gray-50 shadow-sm flex flex-col">
    <header class="sticky top-0 z-50 w-full bg-white border-b border-gray-200 shadow-sm py-3 px-4 flex justify-center items-center">
        <img src="{{ asset('images/vortice-logo.svg') }}" alt="Vórtice 2026" class="h-8 w-auto">
    </header>

    <main class="flex-grow p-4 w-full">
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
                {{ session('warning') }}
            </div>
        @endif

        @if($activeBlock)
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-700">Bloque Actual</h2>
                <p class="text-sm text-gray-500">
                    {{ $activeBlock->start_time->format('H:i') }} - {{ $activeBlock->end_time->format('H:i') }}
                </p>
            </div>

            <div class="space-y-4">
                @foreach($talks as $talk)
                    <a href="{{ route('talk.show', $talk->id) }}" 
                       class="block bg-white p-4 rounded-2xl shadow border border-gray-200 active:bg-gray-100 transition-colors"
                       style="min-height: 80px;">
                        <div class="flex justify-between items-center h-full">
                            <div>
                                <h3 class="font-bold text-gray-900">{{ $talk->title }}</h3>
                                <p class="text-sm text-gray-600">{{ $talk->speaker }}</p>
                            </div>
                            <div class="flex items-center justify-center w-11 h-11 bg-indigo-50 rounded-full text-indigo-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center h-64 text-center">
                <div class="bg-gray-100 p-6 rounded-full mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-xl font-medium text-gray-900">No hay charlas activas en este momento</h2>
                <p class="text-gray-500 mt-2">Vuelve a escanear el código cuando inicie la siguiente ponencia.</p>
            </div>
        @endif
    </main>

    <footer class="p-4 text-center text-xs text-gray-400">
        &copy; {{ date('Y') }} Vortice Pulse
    </footer>
</div>
