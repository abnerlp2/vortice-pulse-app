<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Vórtice 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-brand-light flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-md">
        <h1 class="text-2xl font-display mb-6 text-center text-brand-black">Acceso Administrativo</h1>
        <form action="{{ url('/admin/login') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input type="password" name="password" id="password" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-brand-purple focus:border-brand-purple sm:text-sm">
            </div>
            <button type="submit"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-brand-purple hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-purple">
                Entrar
            </button>
        </form>
    </div>
</body>
</html>
