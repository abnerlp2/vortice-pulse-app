<div class="max-w-md mx-auto min-h-screen bg-brand-light shadow-sm flex flex-col">
    <x-header />

    <div class="flex-grow p-4 w-full">
        @if(session('error'))
            <div role="alert" class="mb-4 p-4 bg-brand-orange/10 border-l-4 border-brand-orange text-brand-orange-ink">
                {{ session('error') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-4 p-4 bg-brand-orange/10 border-l-4 border-brand-orange text-brand-orange-ink">
                {{ session('warning') }}
            </div>
        @endif

        @if($activeBlock)
            <div class="mb-6">
                <h1 class="sr-only">Agenda en curso — Vórtice Pulse</h1>
                <h2 class="text-lg font-semibold text-gray-700">Bloque actual</h2>
                <p class="text-sm text-gray-600">
                    {{ $activeBlock->start_time->format('H:i') }} - {{ $activeBlock->end_time->format('H:i') }}
                </p>
            </div>

            <div class="space-y-4">
                @foreach($talks as $talk)
                    <a wire:key="talk-{{ $talk->id }}" href="{{ route('talk.show', $talk->id) }}" 
                       class="block bg-white p-4 rounded-2xl shadow border border-gray-200 active:bg-brand-light transition-colors"
                       >
                        <div class="flex justify-between items-center h-full">
                            <div>
                                <h3 class="font-bold text-brand-black">{{ $talk->title }}</h3>
                                <p class="text-sm text-gray-600">{{ $talk->speaker }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ $talk->formatted_start_time }} - {{ $talk->formatted_end_time }}</p>
                                <span class="inline-block mt-2 text-xs font-medium text-brand-purple bg-brand-purple/10 px-2 py-0.5 rounded-md">
                                    {{ $talk->room ?: 'Por confirmar' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-center w-11 h-11 bg-brand-purple/10 rounded-full text-brand-purple">
                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center h-64 text-center">
                <div class="bg-brand-light p-6 rounded-full mb-4">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-xl font-medium text-brand-black">No hay charlas activas en este momento</h1>
                <p class="text-gray-600 mt-2">Vuelve a escanear el código cuando inicie la siguiente ponencia.</p>
            </div>
        @endif
    </div>

    <footer class="p-4 text-center text-xs text-gray-600">
        &copy; {{ date('Y') }} Vortice Pulse
    </footer>
</div>
