<div
    wire:poll.30s
    x-data
    x-init="
        window.Echo?.channel('modules.dashboard')
            .listen('.evaluation.received', () => $wire.$refresh())
            .listen('.ranking.order.altered', () => $wire.$refresh());
    "
    class="max-w-4xl mx-auto p-6 bg-brand-card shadow-xl rounded-2xl"
>
    <div class="border-b border-gray-200 pb-4 mb-6 flex flex-wrap gap-4 justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-brand-black">{{ $talk->title }}</h1>
            <p class="text-gray-700 mt-1">Conferencista: <span class="font-medium">{{ $talk->speaker }}</span></p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-700">Total votos</p>
            <p class="text-2xl font-bold text-brand-blue tabular-nums">{{ $total_votes }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Tarjeta de Promedio -->
        <div class="bg-brand-light rounded-xl p-6 flex flex-col items-center justify-center border border-gray-200 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-700 uppercase tracking-wider mb-2">Latido promedio</h2>
            <div class="flex items-baseline space-x-2">
                <span class="text-6xl font-black text-brand-black tabular-nums">{{ number_format($average, 1) }}</span>
                <svg aria-hidden="true" class="w-10 h-10 text-brand-orange fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
            </div>
        </div>

        <!-- Tarjeta de Desviación Estándar / Alerta de Polarización -->
        <div class="rounded-xl p-6 flex flex-col items-center justify-center border shadow-sm transition-colors duration-500
            @if($std_dev <= 0.6)
                bg-brand-cyan/10 border-brand-cyan/30 text-brand-cyan-ink
            @elseif($std_dev > 1.2)
                bg-brand-orange/10 border-brand-orange text-brand-orange-ink
            @else
                bg-brand-light border-gray-200 text-brand-black
            @endif
        ">
            <h2 class="text-lg font-semibold uppercase tracking-wider mb-2">Desviación (polarización)</h2>
            <p class="text-5xl font-black tabular-nums">
                {{ number_format($std_dev, 2) }}
            </p>

            <p class="mt-4 text-sm font-medium px-4 py-1 rounded-full
                @if($std_dev <= 0.6) bg-brand-cyan/20 text-brand-cyan-ink
                @elseif($std_dev > 1.2) bg-brand-orange/20 text-brand-orange-ink
                @else bg-gray-200 text-gray-700 @endif
            ">
                @if($std_dev <= 0.6)
                    Opinión unificada
                @elseif($std_dev > 1.2)
                    Debate activo / polarizado
                @else
                    Opinión dividida moderada
                @endif
            </p>
        </div>
    </div>

    <p class="mt-6 text-center text-xs text-gray-700">
        Actualizado en tiempo real por WebSocket
    </p>
</div>
