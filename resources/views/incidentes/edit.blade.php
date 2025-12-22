{{-- filepath: /home/ignaciorici/laravel-docker/resources/views/incidentes/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Editar Incidente #{{ $incidente->id }}</h1>
            <p class="text-gray-600 mt-1">Modificar datos del incidente registrado</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('menu.analista') }}"
                    class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded font-medium shadow transition">
                    Menú
                </a>
            <a href="{{ route('incidentes.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition">
                ← Volver a Activos
            </a>
            <a href="{{ route('incidentes.historico') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
                📊 Vista Histórica
            </a>
        </div>
    </div>

    <!-- Información del proceso -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="text-blue-600 text-2xl mr-3">⚙️</div>
            <div>
                <h3 class="text-lg font-semibold text-blue-800">{{ $proceso->trabajo }}</h3>
                <p class="text-blue-600">ScheduliX ID: <strong>{{ $proceso->schedulix_id }}</strong></p>
                <p class="text-sm text-blue-600 mt-1">{{ $proceso->ruta_completa }}</p>
            </div>
        </div>
    </div>

    <!-- Formulario de edición -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">📝 Datos del Incidente</h2>
        </div>

        <form action="{{ route('incidentes.update', $incidente->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Columna izquierda -->
                <div class="space-y-6">
                    <!-- Información básica -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">🏢 Información Básica</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Negocio *</label>
                                <select name="negocio_incidente_id" required 
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @foreach($negocios as $negocio)
                                        <option value="{{ $negocio->id }}" {{ $negocio->id == $incidente->negocio_incidente_id ? 'selected' : '' }}>
                                            {{ $negocio->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ambiente *</label>
                                <select name="ambiente_incidente_id" required 
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @foreach($ambientes as $ambiente)
                                        <option value="{{ $ambiente->id }}" {{ $ambiente->id == $incidente->ambiente_incidente_id ? 'selected' : '' }}>
                                            {{ $ambiente->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Capa *</label>
                                <select name="capa_incidente_id" required 
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @foreach($capas as $capa)
                                        <option value="{{ $capa->id }}" {{ $capa->id == $incidente->capa_incidente_id ? 'selected' : '' }}>
                                            {{ $capa->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Servidor *</label>
                                <select name="servidor_incidente_id" required 
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @foreach($servidores as $servidor)
                                        <option value="{{ $servidor->id }}" {{ $servidor->id == $incidente->servidor_incidente_id ? 'selected' : '' }}>
                                            {{ $servidor->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Clasificación del incidente -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">🔍 Clasificación</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Evento *</label>
                                <select name="evento_incidente_id" required 
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @foreach($eventos as $evento)
                                        <option value="{{ $evento->id }}" {{ $evento->id == $incidente->evento_incidente_id ? 'selected' : '' }}>
                                            {{ $evento->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Acción *</label>
                                <select name="accion_incidente_id" required 
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @foreach($acciones as $accion)
                                        <option value="{{ $accion->id }}" {{ $accion->id == $incidente->accion_incidente_id ? 'selected' : '' }}>
                                            {{ $accion->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Escalado *</label>
                                <select name="escalado_incidente_id" required 
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @foreach($escalados as $escalado)
                                        <option value="{{ $escalado->id }}" {{ $escalado->id == $incidente->escalado_incidente_id ? 'selected' : '' }}>
                                            {{ $escalado->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                                <select name="estado_incidente_id" required 
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @foreach($estados as $estado)
                                        <option value="{{ $estado->id }}" {{ $estado->id == $incidente->estado_incidente_id ? 'selected' : '' }}>
                                            {{ $estado->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Requerimiento -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">🎫 Información Adicional</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Requerimiento</label>
                            <input type="text" name="requerimiento" value="{{ $incidente->requerimiento }}" 
                                   placeholder="Número de ticket o requerimiento..." 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Columna derecha -->
                <div class="space-y-6">
                    <!-- Seguimiento -->
                    <div class="bg-yellow-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">📝 Seguimiento Acumulativo</h3>
                        
                        <!-- Historial actual -->
                        @if($incidente->seguimiento)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Historial Existente</label>
                                <div class="bg-white border rounded-md p-3 max-h-40 overflow-y-auto text-sm whitespace-pre-line">{{ $incidente->seguimiento }}</div>
                            </div>
                        @endif
                        
                        <!-- Nuevo seguimiento -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Agregar Nuevo Comentario</label>
                            <textarea name="seguimiento" rows="4" 
                                      placeholder="Escribir nuevo comentario que se agregará al historial..."
                                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            <p class="text-xs text-gray-600 mt-1">
                                ℹ️ Se agregará automáticamente: <strong>{{ now()->format('d/m/Y') }} - {{ Auth::user()->name }}:</strong> [su comentario]
                            </p>
                        </div>
                    </div>

                    <!-- Descripción del evento -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">📋 Descripción del Evento</h3>
                        
                        <div>
                            <textarea name="descripcion_evento" rows="4" 
                                      placeholder="Descripción detallada del evento o incidente..."
                                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $incidente->descripcion_evento }}</textarea>
                        </div>
                    </div>

                    <!-- Solución -->
                    <div class="bg-green-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">🔧 Solución</h3>
                        
                        <div>
                            <textarea name="solucion" rows="4" 
                                      placeholder="Descripción de la solución implementada..."
                                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $incidente->solucion }}</textarea>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="bg-purple-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">💭 Observaciones</h3>
                        
                        <div>
                            <textarea name="observaciones" rows="3" 
                                      placeholder="Observaciones adicionales o notas importantes..."
                                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $incidente->observaciones }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de auditoría -->
            <div class="mt-6 bg-gray-100 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-700 mb-2">📊 Información de Auditoría</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                    <div>
                        <strong>Creado por:</strong> {{ $incidente->creado_por ?? 'Sistema' }}<br>
                        <strong>Fecha creación:</strong> {{ \Carbon\Carbon::parse($incidente->created_at)->format('d/m/Y H:i') }}
                    </div>
                    <div>
                        <strong>Última actualización:</strong> {{ \Carbon\Carbon::parse($incidente->updated_at)->format('d/m/Y H:i') }}<br>
                        @if($incidente->actualizado_por)
                            <strong>Actualizado por:</strong> {{ $incidente->actualizado_por }}
                        @endif
                    </div>
                    <div>
                        <strong>Fecha inicio:</strong> {{ $incidente->inicio ? \Carbon\Carbon::parse($incidente->inicio)->format('d/m/Y H:i') : 'No definida' }}<br>
                        <strong>Fecha registro:</strong> {{ $incidente->registro ? \Carbon\Carbon::parse($incidente->registro)->format('d/m/Y H:i') : 'No definida' }}
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-8 flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    <span class="text-red-500">*</span> Campos obligatorios
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('incidentes.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium transition">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                        💾 Actualizar Incidente
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal de confirmación para estados críticos -->
<div id="confirmModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="text-4xl mb-4">⚠️</div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmar cambio de estado</h3>
            <p class="text-sm text-gray-600 mb-6" id="confirmMessage"></p>
            <div class="flex justify-center space-x-3">
                <button id="cancelConfirm" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                    Cancelar
                </button>
                <button id="proceedConfirm" 
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Confirmación para estados críticos
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const estadoSelect = document.querySelector('select[name="estado_incidente_id"]');
    const originalEstado = estadoSelect.value;
    
    form.addEventListener('submit', function(e) {
        const nuevoEstado = estadoSelect.value;
        const nuevoEstadoTexto = estadoSelect.options[estadoSelect.selectedIndex].text;
        
        // Verificar si el cambio es a estado EXITOSO
        if (nuevoEstadoTexto === 'EXITOSO' && nuevoEstado !== originalEstado) {
            e.preventDefault();
            showConfirmModal('¿Está seguro de marcar este incidente como EXITOSO? Una vez marcado, no podrá editarse nuevamente.');
        }
    });
    
    function showConfirmModal(message) {
        document.getElementById('confirmMessage').textContent = message;
        document.getElementById('confirmModal').classList.remove('hidden');
        
        document.getElementById('proceedConfirm').onclick = function() {
            document.getElementById('confirmModal').classList.add('hidden');
            form.submit();
        };
        
        document.getElementById('cancelConfirm').onclick = function() {
            document.getElementById('confirmModal').classList.add('hidden');
            estadoSelect.value = originalEstado;
        };
    }
});

// Validación de campos requeridos
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('[required]');
    let hasErrors = false;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('border-red-500');
            hasErrors = true;
        } else {
            field.classList.remove('border-red-500');
        }
    });
    
    if (hasErrors) {
        e.preventDefault();
        alert('⚠️ Por favor complete todos los campos obligatorios marcados en rojo.');
    }
});
</script>
@endsection