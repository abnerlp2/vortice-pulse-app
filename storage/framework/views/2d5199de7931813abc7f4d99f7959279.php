<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo e($title ?? 'Vórtice Pulse'); ?></title>
    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="bg-gray-100 min-h-screen font-sans antialiased text-gray-900">
    
    <main class="max-w-md mx-auto min-h-screen bg-white shadow-xl relative">
        <?php echo e($slot); ?>

    </main>

</body>
</html><?php /**PATH /Users/abner.trejos/Developer/vortice-pulse-app/resources/views/components/layouts/app.blade.php ENDPATH**/ ?>