<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <!-- Navbar / Header con Logo y Navegación de Retorno -->
    <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-200">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/vortice-logo.svg') }}" alt="Vórtice 2026" class="h-10 w-auto">
            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-brand-light text-slate-700 uppercase tracking-wider">Admin</span>
        </div>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-brand-light focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-purple transition-colors">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver al Dashboard
        </a>
    </div>

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
        <div class="px-6 py-8 border-b border-gray-200">
            <h2 class="text-2xl font-display text-brand-black">Configuración Inicial de Agenda</h2>
            <p class="mt-2 text-sm text-gray-600">Sube el archivo CSV o Excel con la programación del evento. Esto reemplazará la agenda actual.</p>
        </div>

        <div class="p-8">
            @if ($message)
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-700 flex justify-between items-center">
                    <div>
                        <p class="font-medium">{{ $message }}</p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-3 py-1.5 border border-green-600 text-xs font-semibold rounded-xl text-green-800 hover:bg-green-100 transition-colors">
                        Volver al Dashboard &rarr;
                    </a>
                </div>
            @endif

            @if ($error)
                <div class="mb-6 p-4 bg-brand-orange/10 border-l-4 border-brand-orange text-brand-orange">
                    <p class="font-medium">{{ $error }}</p>
                </div>
            @endif

            <form wire:submit.prevent="import" class="space-y-6">
                <div 
                    x-data="{ isDragging: false }"
                    x-on:dragover.prevent="isDragging = true"
                    x-on:dragleave.prevent="isDragging = false"
                    x-on:drop.prevent="isDragging = false"
                    class="relative"
                >
                    <label 
                        for="file-upload" 
                        :class="{ 'border-brand-purple bg-brand-purple/10': isDragging, 'border-gray-300 bg-white': !isDragging }"
                        class="flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-2xl cursor-pointer hover:border-brand-purple transition-colors duration-200"
                    >
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <span class="relative cursor-pointer bg-white rounded-xl font-medium text-brand-purple hover:brightness-110">
                                    Cargar un archivo
                                </span>
                                <p class="pl-1">o arrastra y suelta</p>
                            </div>
                            <p class="text-xs text-gray-500">XLSX, CSV hasta 10MB</p>
                            
                            @if ($file)
                                <p class="mt-2 text-sm font-semibold text-brand-purple">{{ $file->getClientOriginalName() }}</p>
                            @endif
                        </div>
                        <input id="file-upload" wire:model="file" type="file" class="sr-only">
                    </label>
                    
                    @error('file') <span class="mt-2 text-sm text-brand-orange">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-between">
                    <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-600 hover:text-brand-black transition-colors">
                        &larr; Volver al Dashboard
                    </a>
                    <button 
                        type="submit" 
                        wire:loading.attr="disabled"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-brand-purple hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-purple disabled:opacity-50"
                    >
                        <span wire:loading.remove>Iniciar Importación</span>
                        <span wire:loading class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Procesando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
