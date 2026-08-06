<div class="min-h-screen bg-gray-50 flex flex-col">
    <header class="bg-indigo-600 text-white p-4 shadow-md">
        <h1 class="text-xl font-bold">Vortice Pulse - Agenda Activa</h1>
    </header>

    <main class="flex-grow p-4">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('warning')): ?>
            <div class="mb-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
                <?php echo e(session('warning')); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeBlock): ?>
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-700">Bloque Actual</h2>
                <p class="text-sm text-gray-500">
                    <?php echo e($activeBlock->start_time->format('H:i')); ?> - <?php echo e($activeBlock->end_time->format('H:i')); ?>

                </p>
            </div>

            <div class="space-y-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $talks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $talk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <a href="<?php echo e(route('talk.show', $talk->id)); ?>" 
                       class="block bg-white p-4 rounded-lg shadow border border-gray-200 active:bg-gray-100 transition-colors"
                       style="min-height: 80px;">
                        <div class="flex justify-between items-center h-full">
                            <div>
                                <h3 class="font-bold text-gray-900"><?php echo e($talk->title); ?></h3>
                                <p class="text-sm text-gray-600"><?php echo e($talk->speaker); ?></p>
                            </div>
                            <div class="flex items-center justify-center w-11 h-11 bg-indigo-50 rounded-full text-indigo-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        <?php else: ?>
            <div class="flex flex-col items-center justify-center h-64 text-center">
                <div class="bg-gray-100 p-6 rounded-full mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-xl font-medium text-gray-900">No hay charlas activas en este momento</h2>
                <p class="text-gray-500 mt-2">Vuelve a escanear el código cuando inicie la siguiente ponencia.</p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </main>

    <footer class="p-4 text-center text-xs text-gray-400">
        &copy; <?php echo e(date('Y')); ?> Vortice Pulse
    </footer>
</div>
<?php /**PATH /Users/abner.trejos/Developer/vortice-pulse-app/resources/views/livewire/active-agenda-landing.blade.php ENDPATH**/ ?>