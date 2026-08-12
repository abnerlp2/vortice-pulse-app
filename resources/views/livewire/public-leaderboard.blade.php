<div class="min-h-screen bg-gray-900 text-white p-8 overflow-hidden">
    <div class="max-w-6xl mx-auto">
        <header class="flex justify-between items-center mb-12 border-b border-gray-800 pb-6">
            <div class="flex items-center gap-6">
                <div class="bg-white/95 px-4 py-2 rounded-2xl shadow-md inline-block">
                    <img src="{{ asset('images/vortice-logo.svg') }}" class="h-8 w-auto">
                </div>
                <div>
                    <p class="text-2xl font-display tracking-tight text-brand-cyan">Ranking de Ponencias en Tiempo Real</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm font-mono text-gray-500 uppercase tracking-widest">Estado del Evento</div>
                <div class="flex items-center justify-end text-brand-cyan font-bold">
                    <span class="relative flex h-3 w-3 mr-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    Sincronizado
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 gap-6">
            @foreach($podiumOrder as $index => $talkId)
                @php 
                    $talk = collect($talks)->firstWhere('id', $talkId);
                    $stats = $talkStats[$talkId];
                    $rank = $index + 1;
                @endphp

                <div class="relative bg-gray-800 rounded-2xl p-6 flex items-center transition-all duration-500 transform border border-gray-700 shadow-2xl"
                     style="order: {{ $index }};">
                    
                    <div class="flex-shrink-0 w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center text-3xl font-black text-brand-purple mr-8">
                        {{ $rank }}
                    </div>

                    <div class="flex-grow">
                        <h2 class="text-3xl font-bold">{{ $talk['title'] }}</h2>
                            <div class="flex flex-wrap items-center gap-3 mt-1">
                                <p class="text-xl text-gray-400">{{ $talk['speaker'] }}</p>
                                <p class="text-xs text-gray-500">{{ $talk['formatted_start_time'] }} - {{ $talk['formatted_end_time'] }}</p>
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-12">
                        <div class="text-center">
                            <div class="text-sm text-gray-500 uppercase tracking-wider mb-1">Puntaje</div>
                            <div class="text-5xl font-black text-brand-purple">
                                {{ number_format($stats['average'], 1) }}
                            </div>
                        </div>
                        <div class="text-center w-24">
                            <div class="text-sm text-gray-500 uppercase tracking-wider mb-1">Votos</div>
                            <div class="text-2xl font-bold text-gray-300">
                                {{ $stats['total_votes'] }}
                            </div>
                        </div>
                    </div>

                    @if($rank <= 3)
                        <div class="absolute -top-2 -right-2 bg-yellow-500 text-brand-black text-xs font-black px-3 py-1 rounded-full uppercase">
                            Top {{ $rank }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <footer class="mt-16 flex justify-between items-center text-gray-600 border-t border-gray-800 pt-8">
            <div class="text-sm">
                Actualizado automáticamente vía Reverb WebSockets
            </div>
            <div class="text-sm font-mono">
                {{ now()->format('H:i:s') }}
            </div>
        </footer>
    </div>
</div>

<style>
    body {
        background-color: #111827 !important;
    }
    main {
        max-width: none !important;
        background-color: transparent !important;
        box-shadow: none !important;
    }
</style>
