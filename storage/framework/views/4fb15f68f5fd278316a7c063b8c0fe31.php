<div wire:poll.5s class="max-w-4xl mx-auto p-6 bg-white shadow-xl rounded-2xl">
    <div class="border-b pb-4 mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-bold text-gray-800"><?php echo e($talk->title); ?></h2>
            <p class="text-gray-500 mt-1">Speaker: <span class="font-medium"><?php echo e($talk->speaker); ?></span></p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-400">Total Votos</p>
            <p class="text-2xl font-bold text-blue-600"><?php echo e($total_votes); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Tarjeta de Promedio -->
        <div class="bg-gray-50 rounded-xl p-6 flex flex-col items-center justify-center border border-gray-100 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-500 uppercase tracking-wider mb-2">Latido Promedio</h3>
            <div class="flex items-baseline space-x-2">
                <span class="text-6xl font-black text-gray-800"><?php echo e(number_format($average, 1)); ?></span>
                <svg class="w-10 h-10 text-red-500 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
            </div>
        </div>

        <!-- Tarjeta de Desviación Estándar / Alerta de Polarización -->
        <div class="rounded-xl p-6 flex flex-col items-center justify-center border shadow-sm transition-colors duration-500
            <?php if($std_dev <= 0.6): ?>
                bg-green-50 border-green-200 text-green-800
            <?php elseif($std_dev > 1.2): ?>
                bg-red-50 border-red-200 text-red-800 animate-pulse
            <?php else: ?>
                bg-gray-50 border-gray-200 text-gray-800
            <?php endif; ?>
        ">
            <h3 class="text-lg font-semibold uppercase tracking-wider mb-2 opacity-80">Desviación (Polarización)</h3>
            <div class="text-5xl font-black">
                <?php echo e(number_format($std_dev, 2)); ?>

            </div>
            
            <div class="mt-4 text-sm font-medium px-4 py-1 rounded-full 
                <?php if($std_dev <= 0.6): ?> bg-green-200 text-green-900 
                <?php elseif($std_dev > 1.2): ?> bg-red-200 text-red-900 
                <?php else: ?> bg-gray-200 text-gray-700 <?php endif; ?>
            ">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($std_dev <= 0.6): ?>
                    Opinión Unificada
                <?php elseif($std_dev > 1.2): ?>
                    ⚠️ Debate Activo / Polarizado
                <?php else: ?>
                    Opinión Dividida Moderada
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="mt-6 text-center text-xs text-gray-400">
        Actualizado en tiempo real (Polling: 5s)
    </div>
</div>
<?php /**PATH /var/www/html/resources/views/livewire/vortice-console.blade.php ENDPATH**/ ?>