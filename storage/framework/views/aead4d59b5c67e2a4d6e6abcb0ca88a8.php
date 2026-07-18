<div class="p-6 bg-slate-50 rounded-3xl shadow-xl max-w-6xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-4xl font-bold text-slate-900">Dashboard de Organización</h1>
            <p class="text-slate-500 mt-2">Métricas en vivo y alertas de ranking por sincronización offline.</p>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-sm border border-slate-200">
            <p class="text-sm text-slate-400">Estado del bloque</p>
            <p class="text-2xl font-semibold <?php echo e($isBlockActive ? 'text-emerald-600' : 'text-amber-600'); ?>"><?php echo e($blockStatus); ?></p>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasOfflineAlert): ?>
        <div class="mb-6 rounded-2xl border border-amber-300 bg-amber-50 p-4 shadow-sm">
            <p class="font-semibold text-amber-800">Alerta: ranking alterado por datos offline tardíos</p>
            <p class="text-sm text-amber-700"><?php echo e($offlineAlert['message'] ?? 'Se ha detectado una permutación en el podio tras procesar votos fuera de línea.'); ?></p>
            <div class="mt-3 text-sm text-slate-700">
                Ponencias afectadas:
                <ul class="list-disc list-inside ml-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $offlineAlert['affected_talks'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $talkId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <li><?php echo e($talkTitles[$talkId] ?? $talkId); ?></li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="grid gap-6 md:grid-cols-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $talks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $talk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between gap-2 mb-4">
                    <div>
                        <p class="text-slate-500 uppercase tracking-[0.2em] text-xs"><?php echo e($talk['speaker']); ?></p>
                        <h2 class="text-xl font-semibold text-slate-900"><?php echo e($talk['title']); ?></h2>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-3 py-1 text-sm">#<?php echo e($loop->iteration); ?></span>
                </div>
                <div class="space-y-3">
                    <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100">
                        <p class="text-xs text-slate-500 uppercase tracking-[0.2em]">Promedio Actual</p>
                        <p class="text-3xl font-bold text-slate-900"><?php echo e($talkStats[$talk['id']]['average'] ?? '--'); ?></p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100">
                        <p class="text-xs text-slate-500 uppercase tracking-[0.2em]">Votos Totales</p>
                        <p class="text-3xl font-bold text-slate-900"><?php echo e($talkStats[$talk['id']]['total_votes'] ?? '--'); ?></p>
                    </div>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($podiumOrder) > 0): ?>
        <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-800 mb-3">Orden actual del Podio</h3>
            <ol class="space-y-2 list-decimal list-inside text-slate-700">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $podiumOrder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position => $talkId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <li><?php echo e($talkTitles[$talkId] ?? $talkId); ?></li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ol>
        </div>
    <?php else: ?>
        <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-800 mb-3">Orden del Podio (Preview Estático)</h3>
            <div class="grid gap-3 md:grid-cols-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $talks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $talk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="rounded-2xl border border-slate-100 p-4 bg-slate-50 text-slate-700">
                        <p class="text-sm font-medium text-slate-500"><?php echo e($talk['speaker']); ?></p>
                        <p class="mt-2 text-base font-semibold"><?php echo e($talk['title']); ?></p>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<script>
    document.addEventListener('livewire:initialized', function () {
        if (!window.Echo || typeof window.Echo.channel !== 'function') {
            return;
        }

        window.Echo.channel('modules.dashboard')
            .listen('.evaluation.received', function (event) {
                window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('onEvaluationReceived', event);
            })
            .listen('.ranking.order.altered', function (event) {
                window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('onRankingOrderAltered', event);
            });
    });
</script>
<?php /**PATH /var/www/html/resources/views/livewire/admin-dashboard.blade.php ENDPATH**/ ?>