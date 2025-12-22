{{-- filepath: /home/ignaciorici/laravel-docker/resources/views/incidentes/historico.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Vista Histórica de Incidentes</h1>
            <p class="text-gray-600 mt-1">Consulta y edición de todos los incidentes registrados</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('menu.analista') }}"
                    class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded font-medium shadow transition">
                    Menú
                </a>
            <a href="{{ route('incidentes.index') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
                🔄 Procesos Activos
            </a>
            <a href="{{ route('incidentes.create') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition">
                ➕ Nuevo Incidente
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">🔍 Filtros de Búsqueda</h3>
        <form method="GET" action="{{ route('incidentes.historico') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Proceso</label>
                <input type="text" name="proceso" value="{{ request('proceso') }}" 
                       placeholder="Nombre del proceso..."
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Requerimiento</label>
                <input type="text" name="requerimiento" value="{{ request('requerimiento') }}" 
                       placeholder="Número de ticket..."
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select name="estado" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los estados</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->id }}" {{ request('estado') == $estado->id ? 'selected' : '' }}>
                            {{ $estado->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium transition">
                    🔍 Filtrar
                </button>
                <a href="{{ route('incidentes.historico') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md font-medium transition">
                    🗑️ Limpiar
                </a>
            </div>
        </form>
        <form method="POST" action="{{ route('incidentes.exportar') }}" class="mt-4">
            @csrf
            @if(request('fecha_desde')) <input type="hidden" name="fecha_desde" value="{{ request('fecha_desde') }}"> @endif
            @if(request('fecha_hasta')) <input type="hidden" name="fecha_hasta" value="{{ request('fecha_hasta') }}"> @endif
            @if(request('proceso')) <input type="hidden" name="proceso" value="{{ request('proceso') }}"> @endif
            @if(request('requerimiento')) <input type="hidden" name="requerimiento" value="{{ request('requerimiento') }}"> @endif
            @if(request('estado')) <input type="hidden" name="estado" value="{{ request('estado') }}"> @endif
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium transition">
                📄 Exportar Resultados a Excel
            </button>
        </form>
    </div>

    <!-- Estadísticas de resultados -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="text-blue-600 text-xl mr-3">📊</div>
                <div>
                    <p class="text-blue-800 font-medium">Resultados encontrados: {{ $incidentes->total() }}</p>
                    <p class="text-blue-600 text-sm">Página {{ $incidentes->currentPage() }} de {{ $incidentes->lastPage() }}</p>
                </div>
            </div>
            <div class="text-sm text-blue-600">
                Mostrando {{ $incidentes->firstItem() ?? 0 }} - {{ $incidentes->lastItem() ?? 0 }} de {{ $incidentes->total() }} registros
            </div>
        </div>
    </div>

    <!-- Tabla con edición inline -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proceso</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Negocio</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ambiente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requerimiento</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seguimiento</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($incidentes as $incidente)
                    <tr class="hover:bg-gray-50" data-incidente-id="{{ $incidente->id }}">
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $incidente->id }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            <div class="max-w-xs truncate" title="{{ $incidente->trabajo }}">
                                <strong>{{ $incidente->schedulix_id }}</strong><br>
                                {{ Str::limit($incidente->trabajo, 40) }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <select class="inline-edit-estado border-0 bg-transparent text-xs font-semibold rounded-full px-2 py-1
                                @if($incidente->estado_nombre == 'Pendiente') bg-yellow-100 text-yellow-800
                                @elseif($incidente->estado_nombre == 'En Proceso') bg-blue-100 text-blue-800
                                @elseif($incidente->estado_nombre == 'Escalado') bg-red-100 text-red-800
                                @elseif($incidente->estado_nombre == 'EXITOSO') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif"
                                data-field="estado_incidente_id" data-value="{{ $incidente->estado_incidente_id }}">
                                @foreach($estados as $estado)
                                    <option value="{{ $estado->id }}" {{ $estado->id == $incidente->estado_incidente_id ? 'selected' : '' }}>
                                        {{ $estado->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $incidente->negocio_nombre }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $incidente->ambiente_nombre }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="text" 
                                   class="inline-edit-input w-full border-0 bg-transparent text-sm focus:bg-white focus:border focus:border-blue-300 rounded px-2 py-1" 
                                   data-field="requerimiento" 
                                   value="{{ $incidente->requerimiento }}" 
                                   placeholder="Sin requerimiento">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($incidente->created_at)->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            <div class="relative">
                                @if($incidente->seguimiento)
                                    <div class="max-w-xs max-h-16 overflow-y-auto text-xs bg-gray-50 p-2 rounded border cursor-pointer"
                                         onclick="openSeguimientoModal({{ $incidente->id }}, '{{ addslashes($incidente->seguimiento) }}')">
                                        {!! nl2br(e(Str::limit($incidente->seguimiento, 100))) !!}
                                        @if(strlen($incidente->seguimiento) > 100)
                                            <span class="text-blue-600 font-medium">... ver más</span>
                                        @endif
                                    </div>
                                @else
                                    <button onclick="openSeguimientoModal({{ $incidente->id }}, '')" 
                                            class="text-blue-600 hover:text-blue-800 text-xs">
                                        + Agregar seguimiento
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <div class="flex justify-center space-x-2">
                                <button onclick="saveRow({{ $incidente->id }})" 
                                        class="text-green-600 hover:text-green-800 transition" title="Guardar cambios">
                                    💾
                                </button>
                                <a href="{{ route('incidentes.edit', $incidente->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition" title="Editar completo">
                                    ✏️
                                </a>
                                <button onclick="resetRow({{ $incidente->id }})" 
                                        class="text-gray-600 hover:text-gray-800 transition" title="Deshacer cambios">
                                    ↩️
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <div class="text-6xl mb-4">🔍</div>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">No se encontraron incidentes</h3>
                                <p class="text-gray-500">Intenta ajustar los filtros de búsqueda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    @if($incidentes->hasPages())
    <div class="mt-6">
        {{ $incidentes->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Modal para editar seguimiento -->
<div id="seguimientoModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-2/3 max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">📝 Gestionar Seguimiento</h3>
            
            <!-- Seguimiento actual -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Historial de Seguimiento</label>
                <div id="seguimientoActual" class="bg-gray-50 border rounded-md p-3 max-h-40 overflow-y-auto text-sm whitespace-pre-line"></div>
            </div>
            
            <!-- Nuevo seguimiento -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Agregar Nuevo Comentario</label>
                <textarea id="nuevoSeguimiento" rows="4" 
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                          placeholder="Escribir nuevo comentario..."></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeSeguimientoModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                    Cancelar
                </button>
                <button type="button" onclick="saveSeguimiento()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentIncidenteId = null;
const originalValues = new Map();

// Guardar valores originales para poder resetear
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('tr[data-incidente-id]').forEach(row => {
        const id = row.dataset.incidenteId;
        const values = {};
        
        row.querySelectorAll('.inline-edit-input, .inline-edit-estado').forEach(input => {
            values[input.dataset.field] = input.value;
        });
        
        originalValues.set(id, values);
    });
});

// Guardar cambios de una fila
async function saveRow(incidenteId) {
    const row = document.querySelector(`tr[data-incidente-id="${incidenteId}"]`);
    const updateData = {};
    
    // Recopilar datos de los campos editables
    row.querySelectorAll('.inline-edit-input, .inline-edit-estado').forEach(input => {
        if (input.value !== originalValues.get(incidenteId)[input.dataset.field]) {
            updateData[input.dataset.field] = input.value;
        }
    });
    
    if (Object.keys(updateData).length === 0) {
        alert('No hay cambios para guardar');
        return;
    }
    
    try {
        const response = await fetch(`/incidentes/${incidenteId}/quick-update`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(updateData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Actualizar valores originales
            const newValues = originalValues.get(incidenteId);
            Object.assign(newValues, updateData);
            originalValues.set(incidenteId, newValues);
            
            // Feedback visual
            row.style.backgroundColor = '#dcfce7';
            setTimeout(() => {
                row.style.backgroundColor = '';
            }, 1500);
            
            alert('✅ Cambios guardados correctamente');
        } else {
            alert('❌ Error al guardar los cambios');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Error de conexión');
    }
}

// Resetear fila a valores originales
function resetRow(incidenteId) {
    const row = document.querySelector(`tr[data-incidente-id="${incidenteId}"]`);
    const originalData = originalValues.get(incidenteId);
    
    row.querySelectorAll('.inline-edit-input, .inline-edit-estado').forEach(input => {
        input.value = originalData[input.dataset.field] || '';
    });
}

// Modal de seguimiento
function openSeguimientoModal(incidenteId, seguimientoActual) {
    currentIncidenteId = incidenteId;
    document.getElementById('seguimientoActual').textContent = seguimientoActual || 'Sin seguimiento previo';
    document.getElementById('nuevoSeguimiento').value = '';
    document.getElementById('seguimientoModal').classList.remove('hidden');
}

function closeSeguimientoModal() {
    document.getElementById('seguimientoModal').classList.add('hidden');
    currentIncidenteId = null;
}

async function saveSeguimiento() {
    const nuevoSeguimiento = document.getElementById('nuevoSeguimiento').value.trim();
    
    if (!nuevoSeguimiento) {
        alert('Debe escribir un comentario');
        return;
    }
    
    try {
        const response = await fetch(`/incidentes/${currentIncidenteId}/quick-update`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                seguimiento: nuevoSeguimiento
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeSeguimientoModal();
            location.reload(); // Recargar para ver el seguimiento actualizado
        } else {
            alert('❌ Error al guardar el seguimiento');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Error de conexión');
    }
}

// Cerrar modal al hacer clic fuera
document.getElementById('seguimientoModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSeguimientoModal();
    }
});
</script>

<style>
.inline-edit-input:focus {
    background-color: #fff !important;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

.inline-edit-estado {
    appearance: none;
    cursor: pointer;
}

.inline-edit-estado:focus {
    outline: 2px solid rgba(59, 130, 246, 0.5);
}
</style>
@endsection