<div 
    x-data="evaluatorDevice()" 
    x-init="initSignature(); loadQueue(); listenNetwork()" 
    @retry-offline-sync.window="retryPendingEvaluations()"
    class="max-w-md mx-auto min-h-screen bg-brand-light shadow-sm flex flex-col items-center justify-start w-full"
>
    <x-header />

    <div class="p-4 w-full flex-grow flex flex-col items-center justify-start">
        <template x-if="$store.vorticeCache.hasOfflinePending">
        <div class="mb-4 w-full">
            @include('livewire.components.offline-status-indicator')
        </div>
    </template>

    <!-- Estado: Voto emitido -->
    <div 
        x-show="$wire.hasSubmitted" 
        x-cloak 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-90"
        x-transition:enter-end="opacity-100 transform scale-100"
        class="text-center p-6 space-y-4 w-full my-auto"
    >
        <div class="w-16 h-16 mx-auto bg-brand-cyan/15 rounded-full flex items-center justify-center">
            <svg aria-hidden="true" class="w-8 h-8 text-brand-cyan-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <h2 class="text-xl font-semibold text-brand-black">¡Gracias por tu evaluación!</h2>
        <p class="text-gray-600">Tu respuesta ha sido registrada exitosamente.</p>
        <div class="pt-4">
            <a href="/" class="inline-flex items-center justify-center min-h-[44px] min-w-[44px] px-6 py-3 bg-brand-black hover:brightness-125 text-white font-semibold rounded-xl transition shadow-md text-base focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-cyan focus-visible:ring-offset-2">
                ← Volver a la Agenda
            </a>
        </div>
    </div>

    <!-- Estado: Formulario Activo -->
    <div x-show="!$wire.hasSubmitted" x-cloak x-transition.opacity.duration.300ms class="w-full space-y-6">
        <div class="flex justify-start px-2">
            <a href="/" class="inline-flex items-center min-h-[44px] min-w-[44px] text-sm font-medium text-gray-600 hover:text-brand-black transition-colors">
                ← Volver a la Agenda
            </a>
        </div>

        <div class="text-center">
            <h1 class="text-xl font-bold text-brand-black">{{ $talk?->title ?? 'Evaluar ponencia' }}</h1>
            @if(isset($talk) && $talk)
                <p class="text-sm font-medium text-gray-600 mt-1">{{ $talk->speaker }}</p>
                <p class="text-xs text-gray-600 mt-1">{{ $talk->formatted_start_time }} - {{ $talk->formatted_end_time }}</p>
                <div class="mt-2">
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-brand-purple bg-brand-purple/10 px-2.5 py-1 rounded-md">
                        <svg aria-hidden="true" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11z"/>
                            <circle cx="12" cy="10" r="2.5"/>
                        </svg>
                        {{ $talk->room ?: 'Por confirmar' }}
                    </span>
                </div>
            @endif
            <h2 class="text-2xl font-display text-brand-black mt-4">¿Qué te pareció esta charla?</h2>
            <p class="text-gray-600 mt-1 text-sm">Toca un corazón para calificar</p>
        </div>

        <!-- Selector Táctil (Min 44x44px por elemento) -->
        <div
            role="radiogroup"
            aria-label="Calificación de la charla, de 1 a 5 corazones"
            class="flex justify-between items-center px-4"
            x-data="{
                hover: 0,
                popUntil: 0,
                // Sin red: la calificación se guarda en el cliente y viaja con el
                // envío, así una ráfaga de flechas no dispara una petición por tecla.
                pick(value, group) {
                    this.$wire.rating = value;
                    this.popUntil = 0;
                    this.$nextTick(() => this.popUntil = value);
                    group?.querySelector(`[aria-label^='${value} ']`)?.focus();
                },
            }"
            @mouseleave="hover = 0"
        >
            <template x-for="i in 5" :key="i">
                <button
                    type="button"
                    role="radio"
                    :aria-checked="$wire.rating === i"
                    :aria-label="`${i} de 5 corazones`"
                    :tabindex="($wire.rating || 1) === i ? 0 : -1"
                    @click="pick(i)"
                    @keydown.arrow-right.prevent="pick(Math.min(($wire.rating || 0) + 1, 5), $el.parentElement)"
                    @keydown.arrow-down.prevent="pick(Math.min(($wire.rating || 0) + 1, 5), $el.parentElement)"
                    @keydown.arrow-left.prevent="pick(Math.max(($wire.rating || 1) - 1, 1), $el.parentElement)"
                    @keydown.arrow-up.prevent="pick(Math.max(($wire.rating || 1) - 1, 1), $el.parentElement)"
                    @keydown.home.prevent="pick(1, $el.parentElement)"
                    @keydown.end.prevent="pick(5, $el.parentElement)"
                    @mouseenter="hover = i"
                    class="w-14 h-14 rounded-full flex items-center justify-center transition-colors touch-manipulation focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-cyan focus-visible:ring-offset-2"
                    :class="(hover || $wire.rating) >= i ? 'text-brand-orange' : 'text-gray-500'"
                >
                    <svg
                        aria-hidden="true"
                        class="w-10 h-10 fill-current"
                        :class="popUntil >= i ? 'heart-pop' : ''"
                        :style="popUntil >= i ? `animation-delay: ${(i - 1) * 70}ms` : ''"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </button>
            </template>
        </div>

        <p class="sr-only" aria-live="polite" x-text="$wire.rating ? `Calificación seleccionada: ${$wire.rating} de 5` : ''"></p>

        <!-- Mensajes de Error de Validación -->
        <div class="min-h-[1.5rem]">
            @error('rating')
                <span role="alert" x-transition.fade class="text-brand-orange-ink text-sm block text-center font-medium">{{ $message }}</span>
            @enderror
            @error('deviceSignature')
                <span role="alert" x-transition.fade class="text-brand-orange-ink text-sm block text-center font-medium">{{ $message }}</span>
            @enderror
        </div>

        <!-- Aspectos Cualitativos (Opcionales) -->
        <div class="space-y-4 px-4 text-left mt-6">
            <div>
                <label for="liked-aspects" class="block text-sm font-medium text-gray-700">¿Qué fue lo que más te gustó? (Opcional)</label>
                <textarea id="liked-aspects" wire:model="likedAspects" rows="2"
                    @error('likedAspects') aria-invalid="true" aria-describedby="liked-aspects-error" @enderror
                    class="mt-1 block w-full bg-white text-brand-black border border-gray-300 rounded-xl p-3 shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-cyan text-sm"></textarea>
                @error('likedAspects') <span id="liked-aspects-error" class="text-brand-orange-ink text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="improvement-aspects" class="block text-sm font-medium text-gray-700">¿Qué mejorarías? (Opcional)</label>
                <textarea id="improvement-aspects" wire:model="improvementAspects" rows="2"
                    @error('improvementAspects') aria-invalid="true" aria-describedby="improvement-aspects-error" @enderror
                    class="mt-1 block w-full bg-white text-brand-black border border-gray-300 rounded-xl p-3 shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-cyan text-sm"></textarea>
                @error('improvementAspects') <span id="improvement-aspects-error" class="text-brand-orange-ink text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Botón de Envío -->
        <div class="pt-4">
            <button
                type="button"
                @click="submitEvaluation()"
                class="w-full h-14 bg-brand-orange hover:brightness-110 disabled:opacity-60 disabled:cursor-not-allowed text-white font-bold rounded-xl shadow-lg transition focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-black focus-visible:ring-offset-2 flex items-center justify-center text-lg touch-manipulation"
                wire:loading.attr="disabled"
                :disabled="isSubmitting"
            >
                <span wire:loading.remove x-text="isSubmitting ? 'Enviando...' : 'Enviar Valoración'"></span>
                <span wire:loading>Enviando...</span>
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('vorticeCache', {
            pendingQueue: [],
            hasOfflinePending: false,
            
            get queueKey() {
                return 'vortice:evaluator:pending';
            },
            
            loadQueue() {
                const stored = localStorage.getItem(this.queueKey);
                this.pendingQueue = stored ? JSON.parse(stored) : [];
                this.hasOfflinePending = this.pendingQueue.length > 0;
            },
            
            saveQueue() {
                localStorage.setItem(this.queueKey, JSON.stringify(this.pendingQueue));
                this.hasOfflinePending = this.pendingQueue.length > 0;
            },
            
            add(item) {
                this.pendingQueue.push(item);
                this.saveQueue();
            },
            
            shift() {
                const item = this.pendingQueue.shift();
                this.saveQueue();
                return item;
            }
        });

        Alpine.data('evaluatorDevice', () => ({
            isSubmitting: false,

            initSignature() {
                if (this.$wire.deviceSignature) {
                    return;
                }

                let uuid = localStorage.getItem('vortice_device_uuid');
                if (!uuid) {
                    uuid = crypto.randomUUID ? crypto.randomUUID() : 'uuid-' + Date.now() + '-' + Math.random();
                    localStorage.setItem('vortice_device_uuid', uuid);
                }

                const rawString = uuid + '-' + navigator.userAgent;
                const encoder = new TextEncoder();
                const data = encoder.encode(rawString);

                crypto.subtle.digest('SHA-256', data).then((hashBuffer) => {
                    const hashArray = Array.from(new Uint8Array(hashBuffer));
                    const hashHex = hashArray.map((b) => b.toString(16).padStart(2, '0')).join('');
                    this.$wire.set('deviceSignature', hashHex);
                });
            },

            loadQueue() {
                this.$store.vorticeCache.loadQueue();
            },

            onOnline: null,

            listenNetwork() {
                this.onOnline = () => {
                    if (this.$store.vorticeCache.pendingQueue.length > 0) {
                        this.processQueue();
                    }
                };

                window.addEventListener('online', this.onOnline);
            },

            // Sin esto, cada navegación con wire:navigate deja un listener vivo
            // y la cola se procesa varias veces en paralelo.
            destroy() {
                if (this.onOnline) {
                    window.removeEventListener('online', this.onOnline);
                }
            },

            submitEvaluation() {
                if (!this.$wire.rating || !this.$wire.deviceSignature) {
                    return;
                }

                if (!navigator.onLine) {
                    this.storeOffline();
                    return;
                }

                this.isSubmitting = true;

                this.$wire.call('submitEvaluation')
                    .then(() => {
                        this.isSubmitting = false;
                    })
                    .catch(() => {
                        this.isSubmitting = false;
                        this.storeOffline();
                    });
            },

            storeOffline() {
                const pendingItem = {
                    talk_id: this.$wire.talkId,
                    rating: this.$wire.rating,
                    device_signature: this.$wire.deviceSignature,
                    liked_aspects: this.$wire.likedAspects,
                    improvement_aspects: this.$wire.improvementAspects,
                    created_at: new Date().toISOString(),
                };

                this.$store.vorticeCache.add(pendingItem);
            },

            retryPendingEvaluations() {
                if (!navigator.onLine || this.$store.vorticeCache.pendingQueue.length === 0) {
                    return;
                }

                this.processQueue();
            },

            processQueue() {
                if (this.$store.vorticeCache.pendingQueue.length === 0) {
                    return;
                }

                const evaluation = this.$store.vorticeCache.shift();

                this.$wire.set('rating', evaluation.rating);
                this.$wire.set('deviceSignature', evaluation.device_signature);
                this.$wire.set('likedAspects', evaluation.liked_aspects);
                this.$wire.set('improvementAspects', evaluation.improvement_aspects);

                this.$wire.call('submitEvaluation')
                    .then(() => {
                        if (this.$store.vorticeCache.pendingQueue.length > 0) {
                            this.processQueue();
                        }
                    })
                    .catch(() => {
                        this.$store.vorticeCache.pendingQueue.unshift(evaluation);
                        this.$store.vorticeCache.saveQueue();
                    });
            }
        }));
    });
</script>
