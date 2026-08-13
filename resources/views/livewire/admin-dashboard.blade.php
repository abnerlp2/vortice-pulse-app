<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ open: @entangle('showSlideOver') }">
    <!-- Header Desktop -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8 pb-6 border-b border-gray-200">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/vortice-logo.svg') }}" alt="Vórtice 2026" width="175" height="40" class="h-10 w-auto">
            <h1 class="text-3xl font-display text-brand-black tracking-tight">Dashboard de organización</h1>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.setup') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl shadow-sm text-sm font-medium text-brand-black bg-white hover:bg-brand-light transition-colors">
                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Importar agenda
            </a>
            <div class="rounded-2xl bg-white px-4 py-2 shadow-sm border border-gray-200 flex items-center gap-3">
                <div>
                    <p class="text-xs text-gray-600 font-medium uppercase tracking-wider">Estado del bloque</p>
                    <p class="text-lg font-semibold {{ $isBlockActive ? 'text-brand-cyan-ink' : 'text-brand-orange-ink' }}">{{ $blockStatus }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Layout Desktop (12 columnas) -->
    <div class="grid grid-cols-12 gap-6">
        @if($hasOfflineAlert)
            <div class="col-span-12 rounded-2xl border border-brand-orange/40 bg-brand-orange/10 p-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-brand-orange-ink mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div>
                        <p class="font-semibold text-brand-orange-ink">Alerta: ranking alterado por datos offline tardíos</p>
                        <p class="text-sm text-brand-orange-ink mt-0.5">{{ $offlineAlert['message'] ?? 'Se ha detectado una permutación en el podio tras procesar votos fuera de línea.' }}</p>
                        <div class="mt-2 text-sm text-brand-black">
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
                <h2 class="text-xl font-bold text-brand-black">Lista de charlas</h2>
                <span class="text-xs text-gray-600">Haz clic en una tarjeta para explorar feedback cualitativo</span>
            </div>

            <div class="grid gap-6 grid-cols-1 md:grid-cols-2">
                @foreach($talks as $talk)
                    <div
                        wire:key="talk-card-{{ $talk['id'] }}"
                        role="button"
                        tabindex="0"
                        aria-label="Ver feedback cualitativo de {{ $talk['title'] }}"
                        wire:click="loadQualitativeData('{{ $talk['id'] }}')"
                        wire:keydown.enter="loadQualitativeData('{{ $talk['id'] }}')"
                        wire:keydown.space.prevent="loadQualitativeData('{{ $talk['id'] }}')"
                        class="rounded-2xl bg-white p-5 shadow-sm border border-gray-200 hover:border-brand-cyan/30 hover:shadow-md transition-all cursor-pointer group focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-cyan focus-visible:ring-offset-2"
                    >
                        <div class="flex items-start justify-between gap-2 mb-4">
                            <div>
                                <p class="text-gray-600 uppercase tracking-widest text-xs font-semibold">
                                    {{ $talk['speaker'] }}
                                    <span class="inline-block ml-2 px-2 py-0.5 rounded text-[10px] font-bold bg-brand-light text-gray-700 border border-gray-200">
                                        📍 {{ !empty($talk['room']) ? $talk['room'] : 'Por confirmar' }}
                                    </span>
                                </p>
                                <p class="text-xs text-gray-600 mt-1">{{ $talk['formatted_start_time'] }} - {{ $talk['formatted_end_time'] }}</p>
                                <h3 class="text-lg font-bold text-brand-black group-hover:text-brand-cyan-ink transition-colors mt-0.5">{{ $talk['title'] }}</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button"
                                    wire:click.stop="editTalk('{{ $talk['id'] }}')" 
                                    class="inline-flex items-center min-h-[44px] px-3 py-2 rounded-xl text-xs font-semibold bg-brand-light hover:bg-brand-cyan/10 text-brand-black hover:text-brand-cyan-ink border border-gray-200 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-cyan"
                                    title="Editar Charla"
                                >
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Editar
                                </button>
                                <span class="inline-flex items-center rounded-full bg-brand-cyan/10 text-brand-cyan-ink px-3 py-1 text-xs font-bold">#{{ $loop->iteration }}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-brand-light p-3.5 border border-gray-100">
                                <p class="text-xs text-gray-600 uppercase tracking-wider font-medium">Promedio actual</p>
                                <p class="text-2xl font-extrabold text-brand-black mt-1">{{ $talkStats[$talk['id']]['average'] ?? '--' }}</p>
                            </div>
                            <div class="rounded-2xl bg-brand-light p-3.5 border border-gray-100">
                                <p class="text-xs text-gray-600 uppercase tracking-wider font-medium">Votos totales</p>
                                <p class="text-2xl font-extrabold text-brand-black mt-1">{{ $talkStats[$talk['id']]['total_votes'] ?? '--' }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Panel Lateral Desktop: Podio / Resumen -->
        <div class="col-span-12 lg:col-span-4 space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sticky top-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-brand-black">Orden actual del podio</h3>
                    <span class="flex h-2.5 w-2.5 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-cyan opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-brand-cyan/100"></span>
                    </span>
                </div>

                @if(count($podiumOrder) > 0)
                    <ol class="space-y-3">
                        @foreach($podiumOrder as $position => $talkId)
                            <li wire:key="podium-{{ $talkId }}" class="flex items-center gap-3 p-3 rounded-2xl bg-brand-light border border-gray-100 text-brand-black">
                                <span class="flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold {{ $loop->first ? 'bg-brand-orange/10 text-brand-orange-ink' : 'bg-gray-200 text-brand-black' }}">
                                    {{ $loop->iteration }}
                                </span>
                                <span class="text-sm font-semibold truncate">{{ $talkTitles[$talkId] ?? $talkId }}</span>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <p class="text-xs text-gray-600 uppercase tracking-wider font-medium mb-3">Preview estático de charlas</p>
                    <div class="space-y-2">
                        @foreach($talks as $talk)
                            <div wire:key="preview-{{ $talk['id'] }}" class="rounded-2xl border border-gray-100 p-3 bg-brand-light text-brand-black">
                                <p class="text-xs font-medium text-gray-600">{{ $talk['speaker'] }}</p>
                                <p class="text-sm font-semibold text-brand-black mt-0.5">{{ $talk['title'] }}</p>
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
        x-trap.noscroll="open"
        @keydown.escape.window="open = false"
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
                class="absolute inset-0 bg-brand-black/70 transition-opacity" 
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
                    <div class="flex h-full flex-col overflow-y-auto bg-white shadow-xl">
                        <div class="px-4 py-6 sm:px-6 border-b border-gray-100">
                            <div class="flex items-start justify-between">
                                <h2 class="text-xl font-bold text-brand-black" id="slide-over-title">
                                    Feedback cualitativo: {{ $talkTitles[$selectedTalkId] ?? '' }}
                                </h2>
                                <div class="ml-3 flex h-7 items-center">
                                    <button @click="open = false" class="rounded-xl bg-white text-gray-600 hover:text-brand-black focus:outline-none focus:ring-2 focus:ring-brand-cyan focus:ring-offset-2">
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
                                    <h3 class="text-sm font-bold text-brand-cyan-ink uppercase tracking-wider flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.757c1.246 0 2.256 1.01 2.256 2.256 0 1.246-1.01 2.256-2.256 2.256H19.5m-5.5 0V8.25M6.75 19.5h6.75M6.75 4.5h6.75M6.75 4.5v15m0-15H4.5A2.25 2.25 0 002.25 6.75v10.5A2.25 2.25 0 004.5 19.5h2.25m0-15v15"></path></svg>
                                        Puntos fuertes
                                    </h3>
                                    @forelse($qualitativeComments['liked'] as $comment)
                                        <div class="p-3 bg-brand-cyan/10 rounded-xl border border-brand-cyan/30 text-sm text-brand-cyan-ink italic shadow-sm">
                                            "{{ $comment }}"
                                        </div>
                                    @empty
                                        <p class="text-gray-600 text-sm">No se han registrado comentarios positivos aún.</p>
                                    @endforelse
                                </div>

                                <!-- Columna de Puntos de Mejora -->
                                <div class="space-y-4">
                                    <h3 class="text-sm font-bold text-brand-orange-ink uppercase tracking-wider flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Oportunidades de mejora
                                    </h3>
                                    @forelse($qualitativeComments['improvement'] as $comment)
                                        <div class="p-3 bg-brand-orange/10 rounded-xl border border-brand-orange/40 text-sm text-brand-orange-ink italic shadow-sm">
                                            "{{ $comment }}"
                                        </div>
                                    @empty
                                        <p class="text-gray-600 text-sm">No se han registrado puntos de mejora aún.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Formulario de Edición de Charla (T019) -->
    @if($showEditModal)
        <div 
            x-data="{ show: @entangle('showEditModal') }"
            x-show="show" 
            x-trap.noscroll="show"
            @keydown.escape.window="$wire.cancelEdit()"
            role="dialog"
            aria-modal="true"
            aria-labelledby="edit-modal-title"
            class="fixed inset-0 z-50 overflow-y-auto"
            x-cloak
        >
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div 
                    x-show="show"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-brand-black/70 transition-opacity" 
                    wire:click="cancelEdit"
                ></div>

                <div 
                    x-show="show"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200"
                >
                    <form wire:submit.prevent="updateTalk">
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                                <h3 id="edit-modal-title" class="text-xl font-bold text-brand-black">Editar charla</h3>
                                <button type="button" wire:click="cancelEdit" class="text-gray-600 hover:text-brand-black rounded-xl p-2 min-h-[44px] min-w-[44px] flex items-center justify-center focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-cyan">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="editTitle" class="block text-sm font-semibold text-brand-black">Título de la charla</label>
                                    <input 
                                        type="text" 
                                        id="editTitle" 
                                        wire:model="editTitle" 
                                        class="mt-1 block w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-brand-black shadow-sm focus:border-brand-cyan focus:ring-2 focus:ring-brand-cyan text-sm @error('editTitle') border-brand-orange @enderror"
                                        placeholder="Ingrese el título"
                                    >
                                    @error('editTitle')
                                        <p class="mt-1 text-xs text-brand-orange-ink font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="editSpeaker" class="block text-sm font-semibold text-brand-black">Conferencista(s)</label>
                                    <input 
                                        type="text" 
                                        id="editSpeaker" 
                                        wire:model="editSpeaker" 
                                        class="mt-1 block w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-brand-black shadow-sm focus:border-brand-cyan focus:ring-2 focus:ring-brand-cyan text-sm @error('editSpeaker') border-brand-orange @enderror"
                                        placeholder="Nombre del conferencista"
                                    >
                                    @error('editSpeaker')
                                        <p class="mt-1 text-xs text-brand-orange-ink font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="editRoom" class="block text-sm font-semibold text-brand-black">Sala / Auditorio</label>
                                    <input 
                                        type="text" 
                                        id="editRoom" 
                                        wire:model="editRoom" 
                                        class="mt-1 block w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-brand-black shadow-sm focus:border-brand-cyan focus:ring-2 focus:ring-brand-cyan text-sm @error('editRoom') border-brand-orange @enderror"
                                        placeholder="Ej. Auditorio Principal, Sala A"
                                    >
                                    @error('editRoom')
                                        <p class="mt-1 text-xs text-brand-orange-ink font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="editTimeBlockId" class="block text-sm font-semibold text-brand-black">Bloque horario</label>
                                    <select 
                                        id="editTimeBlockId" 
                                        wire:model="editTimeBlockId" 
                                        class="mt-1 block w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-brand-black shadow-sm focus:border-brand-cyan focus:ring-2 focus:ring-brand-cyan text-sm @error('editTimeBlockId') border-brand-orange @enderror"
                                    >
                                        <option value="">Seleccione un bloque</option>
                                        @foreach($availableTimeBlocks as $block)
                                            <option value="{{ $block['id'] }}">
                                                Bloque {{ $block['id'] }} ({{ \Carbon\Carbon::parse($block['start_time'])->format('H:i') }} - {{ \Carbon\Carbon::parse($block['end_time'])->format('H:i') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('editTimeBlockId')
                                        <p class="mt-1 text-xs text-brand-orange-ink font-medium">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="bg-brand-light px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-100 rounded-b-2xl">
                            <button 
                                type="button" 
                                wire:click="cancelEdit" 
                                class="inline-flex justify-center rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-brand-black shadow-sm hover:bg-brand-light transition-colors"
                            >
                                Cancelar
                            </button>
                            <button 
                                type="submit" 
                                wire:loading.attr="disabled"
                                class="inline-flex items-center justify-center rounded-xl border border-transparent bg-brand-cyan px-4 py-2 text-sm font-bold text-brand-black shadow-sm hover:brightness-110 transition-colors disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="updateTalk">Guardar cambios</span>
                                <span wire:loading wire:target="updateTalk" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Guardando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @script
        <script>
            if (window.Echo && typeof window.Echo.channel === 'function') {
                const channel = window.Echo.channel('modules.dashboard');

                channel
                    .listen('.evaluation.received', (event) => $wire.call('onEvaluationReceived', event))
                    .listen('.ranking.order.altered', (event) => $wire.call('onRankingOrderAltered', event));

                // Cada navegación con wire:navigate volvía a suscribirse sobre el
                // mismo canal, multiplicando los renders por evento recibido.
                document.addEventListener('livewire:navigating', () => {
                    channel.stopListening('.evaluation.received')
                           .stopListening('.ranking.order.altered');
                }, { once: true });
            }
        </script>
    @endscript
</div>
