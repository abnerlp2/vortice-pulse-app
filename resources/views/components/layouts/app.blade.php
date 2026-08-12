<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Vórtice Pulse' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Audiowide&family=Montserrat:wght@400..800&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Audiowide&family=Montserrat:wght@400..800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Audiowide&family=Montserrat:wght@400..800&display=swap"></noscript>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-brand-light min-h-screen font-sans antialiased text-brand-black">
    
    <main class="min-h-screen relative">
        {{ $slot }}
    </main>

</body>
</html>