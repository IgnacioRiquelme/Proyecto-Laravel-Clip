
{{-- filepath: /home/ignaciorici/laravel-docker/resources/views/incidentes/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Incidentes Activos</h1>
            <p class="text-gray-600 mt-1">Gestión de incidentes en curso</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('menu.analista') }}"
                    class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded font-medium shadow transition">
                    Menú
                </a>
            <a href="{{ route('incidentes.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
                ➕ Nuevo Incidente
            </a>
            <a href="{{ route('incidentes.historico') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition">
                📊 Vista Histórica
            </a>
            <form method="POST" action="{{ route('incidentes.exportar') }}" class="inline">
                @csrf
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition">
                    📄 Exportar Excel
                </button>
            </form>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="text-red-600 text-2xl mr-3">🚨</div>
                <div>
                    <p class="text-red-600 text-sm font-medium">Total Activos</p>
                    <p class="text-2xl font-bold text-red-800">{{ $incidentesAbiertos->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="text-yellow-600 text-2xl mr-3">⏳</div>
                <div>
                    <p class="text-yellow-600 text-sm font-medium">Pendientes</p>
                    <p class="text-2xl font-bold text-yellow-800">{{ $incidentesAbiertos->where('estado_nombre', 'Pendiente')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="text-blue-600 text-2xl mr-3">⚙️</div>
                <div>
                    <p class="text-blue-600 text-sm font-medium">En Proceso</p>
                    <p class="text-2xl font-bold text-blue-800">{{ $incidentesAbiertos->where('estado_nombre', 'En Proceso')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="text-purple-600 text-2xl mr-3">📈</div>
                <div>
                    <p class="text-purple-600 text-sm font-medium">Escalados</p>
                    <p class="text-2xl font-bold text-purple-800">{{ $incidentesAbiertos->where('estado_nombre', 'Escalado')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Incidentes -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proceso</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ScheduliX ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Negocio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ambiente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requerimiento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seguimiento</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($incidentesAbiertos as $incidente)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $incidente->id }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="max-w-xs truncate" title="{{ $incidente->trabajo }}">
                                {{ $incidente->trabajo }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $incidente->schedulix_id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($incidente->estado_nombre == 'Pendiente') bg-yellow-100 text-yellow-800
                                @elseif($incidente->estado_nombre == 'En Proceso') bg-blue-100 text-blue-800
                                @elseif($incidente->estado_nombre == 'Escalado') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $incidente->estado_nombre }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $incidente->negocio_nombre }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $incidente->ambiente_nombre }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $incidente->requerimiento ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($incidente->created_at)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            @if($incidente->seguimiento)
                                <div class="max-w-xs max-h-20 overflow-y-auto text-xs bg-gray-50 p-2 rounded border">
                                    {!! nl2br(e($incidente->seguimiento)) !!}
                                </div>
                            @else
                                <span class="text-gray-400">Sin seguimiento</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('incidentes.edit', $incidente->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 transition"
                                   title="Editar incidente">
                                    ✏️
                                </a>
                                <button onclick="openQuickUpdate({{ $incidente->id }}, '{{ $incidente->estado_nombre }}', '{{ $incidente->requerimiento }}', '{{ $incidente->observaciones }}')"
                                        class="text-green-600 hover:text-green-900 transition"
                                        title="Actualización rápida">
                                    ⚡
                                </button>
                                <button onclick="showDetails({{ $incidente->id }})"
                                        class="text-purple-600 hover:text-purple-900 transition"
                                        title="Ver detalles">
                                    👁️
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <div class="text-6xl mb-4">🎉</div>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">¡No hay incidentes activos!</h3>
                                <p class="text-gray-500">Todos los procesos están funcionando correctamente.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para actualización rápida -->
<div id="quickUpdateModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Actualización Rápida</h3>
            <form id="quickUpdateForm">
                <input type="hidden" id="incidenteId" name="incidente_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="estadoSelect" name="estado_incidente_id" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        <option value="">No cambiar</option>
                        <!-- Se llenarán desde JavaScript -->
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Requerimiento</label>
                    <input type="text" id="requerimientoInput" name="requerimiento" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2" 
                           placeholder="Número de requerimiento">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nuevo Seguimiento</label>
                    <textarea id="seguimientoInput" name="seguimiento" rows="3"
                              class="w-full border border-gray-300 rounded-md px-3 py-2" 
                              placeholder="Agregar comentario al seguimiento..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeQuickUpdate()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Estados disponibles (se llenarán desde el servidor)
const estados = @json(DB::table('estado_incidentes')->get());

function openQuickUpdate(id, estadoActual, requerimiento, observaciones) {
    document.getElementById('incidenteId').value = id;
    document.getElementById('requerimientoInput').value = requerimiento || '';
    
    // Llenar select de estados
    const estadoSelect = document.getElementById('estadoSelect');
    estadoSelect.innerHTML = '<option value="">No cambiar</option>';
    estados.forEach(estado => {
        const option = document.createElement('option');
        option.value = estado.id;
        option.textContent = estado.nombre;
        if (estado.nombre === estadoActual) {
            option.selected = true;
        }
        estadoSelect.appendChild(option);
    });
    
    document.getElementById('quickUpdateModal').classList.remove('hidden');
}

function closeQuickUpdate() {
    document.getElementById('quickUpdateModal').classList.add('hidden');
    document.getElementById('quickUpdateForm').reset();
}

// Manejar envío del formulario
document.getElementById('quickUpdateForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const incidenteId = formData.get('incidente_id');
    
    try {
        const response = await fetch(`/incidentes/${incidenteId}/quick-update`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                estado_incidente_id: formData.get('estado_incidente_id') || null,
                requerimiento: formData.get('requerimiento') || null,
                seguimiento: formData.get('seguimiento') || null,
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeQuickUpdate();
            location.reload(); // Recargar para ver los cambios
        } else {
            alert('Error al actualizar el incidente');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error de conexión');
    }
});

function showDetails(id) {
    window.open(`/incidentes/${id}/edit`, '_blank');
}

// Cerrar modal al hacer clic fuera de él
document.getElementById('quickUpdateModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeQuickUpdate();
    }
});
</script>
@endsection