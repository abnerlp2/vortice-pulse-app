<div class="leaderboard-screen min-h-screen bg-brand-black text-white p-6 lg:p-10">
    <div class="max-w-6xl 2xl:max-w-[1600px] mx-auto">
        <header class="flex flex-wrap gap-6 justify-between items-center mb-10 lg:mb-14 border-b border-white/15 pb-6">
            <div class="flex items-center gap-6">
                <div class="bg-white/95 px-4 py-2 rounded-2xl shadow-md inline-block">
                    <img src="{{ asset('images/vortice-logo.svg') }}" alt="Vórtice 2026" width="140" height="32" class="h-8 lg:h-10 w-auto">
                </div>
                <h1 class="text-2xl lg:text-3xl 2xl:text-4xl font-display tracking-tight text-brand-cyan">Ranking de charlas en tiempo real</h1>
            </div>
            <div class="text-right">
                <p class="text-sm font-mono text-brand-mute uppercase tracking-widest">Estado del Evento</p>
                <p class="flex items-center justify-end text-brand-cyan font-bold">
                    <span class="relative flex h-3 w-3 mr-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-cyan opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-brand-cyan"></span>
                    </span>
                    Sincronizado
                </p>
            </div>
        </header>

        <div class="grid grid-cols-1 gap-6">
            @foreach($podiumOrder as $index => $talkId)
                @php
                    $talk = collect($talks)->firstWhere('id', $talkId);
                    $stats = $talkStats[$talkId];
                    $rank = $index + 1;
                @endphp

                <div wire:key="podium-{{ $talkId }}" class="relative bg-white/[0.06] rounded-2xl p-6 lg:p-8 flex flex-col md:flex-row md:items-center gap-6 transition-all duration-500 border border-white/10 shadow-2xl">

                    <div class="flex-shrink-0 w-14 h-14 lg:w-16 lg:h-16 bg-brand-purple/25 rounded-full flex items-center justify-center text-3xl font-black text-white">
                        {{ $rank }}
                    </div>

                    <div class="flex-grow min-w-0">
                        <h2 class="text-2xl lg:text-3xl 2xl:text-4xl font-bold text-balance">{{ $talk['title'] }}</h2>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                            <p class="text-lg lg:text-xl text-brand-mute">{{ $talk['speaker'] }}</p>
                            <p class="text-xs lg:text-sm text-brand-mute">{{ $talk['formatted_start_time'] }} - {{ $talk['formatted_end_time'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-10 lg:gap-12 md:flex-shrink-0">
                        <div class="text-center">
                            <p class="text-sm text-brand-mute uppercase tracking-wider mb-1">Promedio</p>
                            <p class="text-4xl lg:text-5xl 2xl:text-6xl font-black text-brand-cyan tabular-nums">
                                {{ number_format($stats['average'], 1) }}
                            </p>
                        </div>
                        <div class="text-center w-24">
                            <p class="text-sm text-brand-mute uppercase tracking-wider mb-1">Votos</p>
                            <p class="text-2xl lg:text-3xl font-bold text-white tabular-nums">
                                {{ $stats['total_votes'] }}
                            </p>
                        </div>
                    </div>

                    @if($rank <= 3)
                        <div class="absolute -top-2 -right-2 bg-brand-orange text-brand-black text-xs font-black px-3 py-1 rounded-full uppercase">
                            Top {{ $rank }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <footer class="mt-16 flex flex-wrap gap-4 justify-between items-center text-brand-mute border-t border-white/15 pt-8">
            <p class="text-sm">
                Actualizado automáticamente vía Reverb WebSockets
            </p>
            <p class="text-sm font-mono">
                {{ now()->format('H:i:s') }}
            </p>
        </footer>
    </div>
</div>

@assets
    <style>
        /* Esta pantalla se proyecta: el marco claro del resto de la app no aplica. */
        body:has(.leaderboard-screen) { background-color: var(--color-brand-black); }
        body:has(.leaderboard-screen) main { max-width: none; background-color: transparent; box-shadow: none; }
    </style>
@endassets
