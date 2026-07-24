<div 
    x-data="evaluatorDevice()" 
    x-init="initSignature(); loadQueue(); listenNetwork()" 
    @retry-offline-sync.window="retryPendingEvaluations()"
    class="flex flex-col items-center justify-center min-h-[50vh] p-4 bg-gray-50 rounded-xl shadow-sm max-w-sm mx-auto"
>
    <template x-if="$store.vorticeCache.hasOfflinePending">
        <div class="mb-4 w-full">
            <?php echo $__env->make('livewire.components.offline-status-indicator', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </template>

    <!-- Estado: Voto emitido -->
    <div x-show="$wire.hasSubmitted" x-cloak class="text-center p-6 space-y-4">
        <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-800">¡Gracias por tu evaluación!</h3>
        <p class="text-gray-500">Tu respuesta ha sido registrada exitosamente.</p>
    </div>

    <!-- Estado: Formulario Activo -->
    <div x-show="!$wire.hasSubmitted" class="w-full space-y-8">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-800">¿Qué te pareció esta charla?</h2>
            <p class="text-gray-500 mt-2 text-sm">Toca un corazón para calificar</p>
        </div>

        <!-- Selector Táctil (Min 44x44px por elemento) -->
        <div class="flex justify-between items-center px-4">
            <template x-for="i in 5" :key="i">
                <button 
                    type="button" 
                    @click="$wire.set('rating', i)" 
                    class="w-14 h-14 flex items-center justify-center transition-transform active:scale-95 touch-manipulation focus:outline-none"
                    :class="$wire.rating >= i ? 'text-red-500' : 'text-gray-300'"
                    aria-label="Calificar con corazones"
                >
                    <svg class="w-10 h-10 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </button>
            </template>
        </div>

        <!-- Mensajes de Error de Validación -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['rating'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm block text-center font-medium"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['deviceSignature'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm block text-center font-medium"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- Aspectos Cualitativos (Opcionales) -->
        <div class="space-y-4 px-4 text-left mt-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">¿Qué fue lo que más te gustó? (Opcional)</label>
                <textarea wire:model="likedAspects" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"></textarea>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['likedAspects'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">¿Qué mejorarías? (Opcional)</label>
                <textarea wire:model="improvementAspects" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"></textarea>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['improvementAspects'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <!-- Botón de Envío -->
        <div class="pt-4">
            <button 
                type="button" 
                @click="submitEvaluation()"
                class="w-full h-14 bg-red-600 hover:bg-red-700 disabled:bg-red-300 disabled:cursor-not-allowed text-white font-bold rounded-xl shadow-lg transition-colors flex items-center justify-center text-lg touch-manipulation"
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

            listenNetwork() {
                window.addEventListener('online', () => {
                    if (this.$store.vorticeCache.pendingQueue.length > 0) {
                        this.processQueue();
                    }
                });
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
<?php /**PATH /var/www/html/resources/views/livewire/mobile-evaluator.blade.php ENDPATH**/ ?>