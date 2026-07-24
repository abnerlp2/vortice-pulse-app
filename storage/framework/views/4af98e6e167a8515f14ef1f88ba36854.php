<div class="min-h-screen bg-gray-900 text-white p-8 overflow-hidden">
    <div class="max-w-6xl mx-auto">
        <header class="flex justify-between items-end mb-12 border-b border-gray-800 pb-6">
            <div>
                <h1 class="text-5xl font-extrabold tracking-tight text-indigo-400">VORTICE PULSE</h1>
                <p class="text-xl text-gray-400 mt-2">Ranking de Ponencias en Tiempo Real</p>
            </div>
            <div class="text-right">
                <div class="text-sm font-mono text-gray-500 uppercase tracking-widest">Estado del Evento</div>
                <div class="flex items-center justify-end text-green-400 font-bold">
                    <span class="relative flex h-3 w-3 mr-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    Sincronizado
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 gap-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $podiumOrder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $talkId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php 
                    $talk = collect($talks)->firstWhere('id', $talkId);
                    $stats = $talkStats[$talkId];
                    $rank = $index + 1;
                ?>

                <div class="relative bg-gray-800 rounded-2xl p-6 flex items-center transition-all duration-500 transform border border-gray-700 shadow-2xl"
                     style="order: <?php echo e($index); ?>;">
                    
                    <div class="flex-shrink-0 w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center text-3xl font-black text-indigo-300 mr-8">
                        <?php echo e($rank); ?>

                    </div>

                    <div class="flex-grow">
                        <h2 class="text-3xl font-bold"><?php echo e($talk['title']); ?></h2>
                        <p class="text-xl text-gray-400"><?php echo e($talk['speaker']); ?></p>
                    </div>

                    <div class="flex items-center space-x-12">
                        <div class="text-center">
                            <div class="text-sm text-gray-500 uppercase tracking-wider mb-1">Puntaje</div>
                            <div class="text-5xl font-black text-indigo-400">
                                <?php echo e(number_format($stats['average'], 1)); ?>

                            </div>
                        </div>
                        <div class="text-center w-24">
                            <div class="text-sm text-gray-500 uppercase tracking-wider mb-1">Votos</div>
                            <div class="text-2xl font-bold text-gray-300">
                                <?php echo e($stats['total_votes']); ?>

                            </div>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rank <= 3): ?>
                        <div class="absolute -top-2 -right-2 bg-yellow-500 text-gray-900 text-xs font-black px-3 py-1 rounded-full uppercase">
                            Top <?php echo e($rank); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        <footer class="mt-16 flex justify-between items-center text-gray-600 border-t border-gray-800 pt-8">
            <div class="text-sm">
                Actualizado automáticamente vía Reverb WebSockets
            </div>
            <div class="text-sm font-mono">
                <?php echo e(now()->format('H:i:s')); ?>

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
<?php /**PATH /Users/abner.trejos/Developer/vortice-pulse-app/resources/views/livewire/public-leaderboard.blade.php ENDPATH**/ ?>