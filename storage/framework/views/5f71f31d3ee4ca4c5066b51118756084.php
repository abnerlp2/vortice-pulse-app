<div class="rounded-2xl border border-orange-200 bg-orange-50 p-4 flex items-center justify-between gap-4 shadow-sm">
    <div>
        <p class="text-sm font-semibold text-orange-800">Envío pendiente por conexión</p>
        <p class="text-xs text-orange-700">Tu evaluación está segura y se enviará cuando la red se restablezca.</p>
    </div>
    <button
        type="button"
        @click="$dispatch('retry-offline-sync')"
        class="min-h-[44px] min-w-[44px] rounded-full bg-orange-600 text-white flex items-center justify-center hover:bg-orange-700 transition-colors"
        aria-label="Reintentar sincronización offline"
    >
        ↻
    </button>
</div>
<?php /**PATH /Users/abner.trejos/Developer/vortice-pulse-app/resources/views/livewire/components/offline-status-indicator.blade.php ENDPATH**/ ?>