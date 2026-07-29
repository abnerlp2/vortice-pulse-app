<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ open: @entangle('showSlideOver') }">
    <!-- Header Desktop -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8 pb-6 border-b border-slate-200">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/vortice-logo.svg') }}" alt="Vórtice 2026" class="h-10 w-auto">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard de Organización</h1>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.setup') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-xl shadow-sm text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Importar Agenda
            </a>
            <div class="rounded-2xl bg-white px-4 py-2 shadow-sm border border-slate-200 flex items-center gap-3">
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Estado del bloque</p>
                    <p class="text-lg font-semibold {{ $isBlockActive ? 'text-emerald-600' : 'text-amber-600' }}">{{ $blockStatus }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Layout Desktop (12 columnas) -->
    <div class="grid grid-cols-12 gap-6">
        @if($hasOfflineAlert)
            <div class="col-span-12 rounded-2xl border border-amber-300 bg-amber-50 p-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div>
                        <p class="font-semibold text-amber-800">Alerta: ranking alterado por datos offline tardíos</p>
                        <p class="text-sm text-amber-700 mt-0.5">{{ $offlineAlert['message'] ?? 'Se ha detectado una permutación en el podio tras procesar votos fuera de línea.' }}</p>
                        <div class="mt-2 text-sm text-slate-700">
                            <span class="font-medium">Ponencias afectadas:</span>
                            <ul class="list-disc list-inside ml-2 mt-1 space-y-0.5">
                                @foreach($offlineAlert['affected_talks'] ?? [] as $talkId)
                                    <li>{{ $talkTitles[$talkId] ?? $talkId }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Area Principal: Tarjetas de Ponencias -->
        <div class="col-span-12 lg:col-span-8 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-800">Ponencias en Evaluación</h2>
                <span class="text-xs text-slate-500">Haz clic en una tarjeta para explorar feedback cualitativo</span>
            </div>

            <div class="grid gap-6 grid-cols-1 md:grid-cols-2">
                @foreach($talks as $talk)
                    <div 
                        wire:click="loadQualitativeData('{{ $talk['id'] }}')"
                        class="rounded-2xl bg-white p-5 shadow-sm border border-slate-200 hover:border-sky-300 hover:shadow-md transition-all cursor-pointer group"
                    >
                        <div class="flex items-start justify-between gap-2 mb-4">
                            <div>
                                <p class="text-slate-400 uppercase tracking-widest text-xs font-semibold">{{ $talk['speaker'] }}</p>
                                <h3 class="text-lg font-bold text-slate-900 group-hover:text-sky-600 transition-colors mt-0.5">{{ $talk['title'] }}</h3>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-3 py-1 text-xs font-bold">#{{ $loop->iteration }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-slate-50 p-3.5 border border-slate-100">
                                <p class="text-xs text-slate-400 uppercase tracking-wider font-medium">Promedio Actual</p>
                                <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ $talkStats[$talk['id']]['average'] ?? '--' }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-3.5 border border-slate-100">
                                <p class="text-xs text-slate-400 uppercase tracking-wider font-medium">Votos Totales</p>
                                <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ $talkStats[$talk['id']]['total_votes'] ?? '--' }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Panel Lateral Desktop: Podio / Resumen -->
        <div class="col-span-12 lg:col-span-4 space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sticky top-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Orden actual del Podio</h3>
                    <span class="flex h-2.5 w-2.5 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                </div>

                @if(count($podiumOrder) > 0)
                    <ol class="space-y-3">
                        @foreach($podiumOrder as $position => $talkId)
                            <li class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-100 text-slate-800">
                                <span class="flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold {{ $loop->first ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $loop->iteration }}
                                </span>
                                <span class="text-sm font-semibold truncate">{{ $talkTitles[$talkId] ?? $talkId }}</span>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-medium mb-3">Preview Estático de Ponencias</p>
                    <div class="space-y-2">
                        @foreach($talks as $talk)
                            <div class="rounded-2xl border border-slate-100 p-3 bg-slate-50 text-slate-700">
                                <p class="text-xs font-medium text-slate-400">{{ $talk['speaker'] }}</p>
                                <p class="text-sm font-semibold text-slate-800 mt-0.5">{{ $talk['title'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Qualitative Slide-over (T012) -->
    <div 
        x-show="open" 
        class="fixed inset-0 overflow-hidden z-50" 
        aria-labelledby="slide-over-title" 
        role="dialog" 
        aria-modal="true"
        x-cloak
    >
        <div class="absolute inset-0 overflow-hidden">
            <div 
                x-show="open"
                x-transition:enter="ease-in-out duration-500"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in-out duration-500"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-slate-500 bg-opacity-75 transition-opacity" 
                @click="open = false" 
                aria-hidden="true"
            ></div>

            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div 
                    x-show="open"
                    x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="pointer-events-auto w-screen max-w-2xl"
                >
                    <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl">
                        <div class="px-4 py-6 sm:px-6 border-b border-slate-100">
                            <div class="flex items-start justify-between">
                                <h2 class="text-xl font-bold text-slate-900" id="slide-over-title">
                                    Feedback Cualitativo: {{ $talkTitles[$selectedTalkId] ?? '' }}
                                </h2>
                                <div class="ml-3 flex h-7 items-center">
                                    <button @click="open = false" class="rounded-xl bg-white text-slate-400 hover:text-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                                        <span class="sr-only">Cerrar panel</span>
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="relative mt-6 flex-1 px-4 sm:px-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Columna de Aspectos Positivos -->
                                <div class="space-y-4">
                                    <h3 class="text-sm font-bold text-emerald-700 uppercase tracking-wider flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.757c1.246 0 2.256 1.01 2.256 2.256 0 1.246-1.01 2.256-2.256 2.256H19.5m-5.5 0V8.25M6.75 19.5h6.75M6.75 4.5h6.75M6.75 4.5v15m0-15H4.5A2.25 2.25 0 002.25 6.75v10.5A2.25 2.25 0 004.5 19.5h2.25m0-15v15"></path></svg>
                                        Puntos Fuertes
                                    </h3>
                                    @forelse($qualitativeComments['liked'] as $comment)
                                        <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-100 text-sm text-emerald-900 italic shadow-sm">
                                            "{{ $comment }}"
                                        </div>
                                    @empty
                                        <p class="text-slate-400 text-sm">No se han registrado comentarios positivos aún.</p>
                                    @endforelse
                                </div>

                                <!-- Columna de Puntos de Mejora -->
                                <div class="space-y-4">
                                    <h3 class="text-sm font-bold text-amber-700 uppercase tracking-wider flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Oportunidades de Mejora
                                    </h3>
                                    @forelse($qualitativeComments['improvement'] as $comment)
                                        <div class="p-3 bg-amber-50 rounded-xl border border-amber-100 text-sm text-amber-900 italic shadow-sm">
                                            "{{ $comment }}"
                                        </div>
                                    @empty
                                        <p class="text-slate-400 text-sm">No se han registrado puntos de mejora aún.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', function () {
        if (!window.Echo || typeof window.Echo.channel !== 'function') {
            return;
        }

        window.Echo.channel('modules.dashboard')
            .listen('.evaluation.received', function (event) {
                @this.call('onEvaluationReceived', event);
            })
            .listen('.ranking.order.altered', function (event) {
                @this.call('onRankingOrderAltered', event);
            });
    });
</script>
