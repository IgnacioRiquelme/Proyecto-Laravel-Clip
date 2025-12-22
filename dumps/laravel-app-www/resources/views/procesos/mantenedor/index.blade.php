{{-- filepath: resources/views/procesos/mantenedor/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center relative overflow-hidden">
    <!-- Fondo de imagen animado -->
    <img src="/gif/video.gif" alt="Fondo animado" class="fixed inset-0 w-full h-full object-cover z-0" />
    <!-- Recuadro de mantenedor -->
    <div class="relative z-20 flex items-center justify-center w-full min-h-screen px-4">
        <div class="relative bg-white rounded-[48px] shadow-2xl w-full max-w-xl mx-auto flex flex-col items-center pt-20 pb-12 px-8">
            <!-- Icono flotante -->
            <div class="absolute -top-16 left-1/2 -translate-x-1/2 bg-blue-700 rounded-full p-6 flex items-center justify-center shadow-lg">
                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-indigo-700 mb-8 text-center mt-2">Buscar Proceso</h1>
            {{-- Buscador --}}
            <form method="GET" action="{{ route('procesos.mantenedor.index') }}" class="mb-8 flex gap-2 w-full max-w-md mx-auto">
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre o Schedulix ID..." class="flex-1 border border-gray-300 rounded p-2" autofocus />
                <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">Buscar</button>
            </form>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded text-center w-full">
                    {{ session('success') }}
                </div>
            @endif

            @if(request('buscar') && $proceso)
                <div class="bg-gray-50 p-6 rounded-lg shadow text-center w-full max-w-md mx-auto">
                    <form method="POST" action="{{ route('procesos.mantenedor.update', $proceso->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="block font-semibold mb-1">Trabajo *</label>
                            <input type="text" name="trabajo" value="{{ old('trabajo', $proceso->trabajo) }}" class="w-full border border-gray-300 rounded p-2" required>
                        </div>
                        <div class="mb-4">
                            <label class="block font-semibold mb-1">Schedulix ID</label>
                            <input type="text" name="schedulix_id" value="{{ old('schedulix_id', $proceso->schedulix_id) }}" class="w-full border border-gray-300 rounded p-2">
                        </div>
                        <div class="mb-4">
                            <label class="block font-semibold mb-1">Categoría 1</label>
                            <input type="text" name="categoria_1" value="{{ old('categoria_1', $proceso->categoria_1) }}" class="w-full border border-gray-300 rounded p-2">
                        </div>
                        <div class="mb-4">
                            <label class="block font-semibold mb-1">Categoría 2</label>
                            <input type="text" name="categoria_2" value="{{ old('categoria_2', $proceso->categoria_2) }}" class="w-full border border-gray-300 rounded p-2">
                        </div>
                        <div class="mb-4">
                            <label class="block font-semibold mb-1">Ruta Completa</label>
                            <textarea name="ruta_completa" class="w-full border border-gray-300 rounded p-2">{{ old('ruta_completa', $proceso->ruta_completa) }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block font-semibold mb-1">Responsable Escalamiento</label>
                            <input type="text" name="responsable_escalamiento" value="{{ old('responsable_escalamiento', $proceso->responsable_escalamiento) }}" class="w-full border border-gray-300 rounded p-2">
                        </div>
                        <div class="mb-4">
                            <label class="block font-semibold mb-1">Observación</label>
                            <input type="text" name="observacion" value="{{ old('observacion', $proceso->observacion) }}" class="w-full border border-gray-300 rounded p-2">
                        </div>
                        <div class="mb-4">
                            <label class="block font-semibold mb-1">¿Requiere solución inmediata?</label>
                            <select name="requiere_solucion_inmediata" class="w-full border border-gray-300 rounded p-2">
                                <option value="" {{ old('requiere_solucion_inmediata', $proceso->requiere_solucion_inmediata) === null ? 'selected' : '' }}>Vacío</option>
                                <option value="1" {{ old('requiere_solucion_inmediata', $proceso->requiere_solucion_inmediata) == 1 ? 'selected' : '' }}>Sí, declarar como incidente</option>
                                <option value="0" {{ old('requiere_solucion_inmediata', $proceso->requiere_solucion_inmediata) === 0 ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="flex justify-between mt-6">
                            <a href="{{ route('procesos.mantenedor.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Volver</a>
                            <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">Actualizar</button>
                        </div>
                    </form>
                </div>
            @elseif(request('buscar'))
                <div class="text-center text-red-500 mt-8">No se encontró ningún proceso con ese criterio.</div>
            @endif
        </div>
    </div>
</div>
@endsection