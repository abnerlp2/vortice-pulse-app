<div class="rounded-2xl border border-brand-orange/30 bg-brand-orange/10 p-4 flex items-center justify-between gap-4 shadow-sm">
    <div>
        <p class="text-sm font-semibold text-brand-orange-ink">Envío pendiente por conexión</p>
        <p class="text-xs text-brand-orange-ink">Tu evaluación está segura y se enviará cuando la red se restablezca.</p>
    </div>
    <button
        type="button"
        @click="$dispatch('retry-offline-sync')"
        class="min-h-[44px] min-w-[44px] rounded-full bg-brand-orange text-white flex items-center justify-center hover:brightness-110 transition-colors"
        aria-label="Reintentar sincronización offline"
    >
        ↻
    </button>
</div>
