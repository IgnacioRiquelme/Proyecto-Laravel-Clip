<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ isset($reporte) ? 'Editar Reporte Veeam' : 'Nuevo Reporte Veeam' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <style>
        .campo-requerido::after {
            content: " *";
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-12">

<div class="flex w-full max-w-7xl bg-white p-8 rounded-lg shadow-lg border border-indigo-200">
    {{-- Menú lateral --}}
    <aside class="w-1/4 bg-gray-100 p-4 border-r border-gray-300">
        <h2 class="text-lg font-semibold mb-4">Acciones</h2>
        <div class="flex flex-col gap-4 mb-6">
            <a href="{{ route('reportes-veeam.index') }}" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-center">
                Seguimiento Reportes
            </a>
        </div>

        @if(isset($reportesHoy) && count($reportesHoy) > 0)
        <h2 class="text-lg font-semibold mb-3 mt-6">Reportes del Día</h2>
        <div class="space-y-2 max-h-96 overflow-y-auto">
            @foreach($reportesHoy as $rep)
            <div class="border border-gray-300 p-3 rounded bg-white hover:bg-gray-50">
                <div class="text-xs text-gray-500">Ticket: {{ $rep->numero_ticket }}</div>
                <div class="text-sm font-semibold mt-1">{{ Str::limit($rep->job_fallido, 40) }}</div>
                <div class="text-xs mt-1">
                    <span class="px-2 py-1 rounded text-white {{ $rep->estado === 'Failed' ? 'bg-red-500' : 'bg-yellow-500' }}">
                        {{ $rep->estado }}
                    </span>
                    <span class="ml-2 px-2 py-1 rounded bg-blue-500 text-white">
                        {{ $rep->estado_ticket }}
                    </span>
                </div>
                <a href="{{ route('reportes-veeam.edit', $rep->id) }}" class="text-blue-600 hover:underline text-xs mt-2 inline-block">
                    Editar
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </aside>

    {{-- Formulario --}}
    <div class="flex-1 pl-8">
        <h1 class="text-3xl font-bold text-indigo-600 text-center mb-10">
            {{ isset($reporte) ? '✏️ Editar Reporte Veeam' : '📝 Nuevo Reporte Veeam' }}
        </h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 border border-green-300 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ isset($reporte) ? route('reportes-veeam.update', $reporte->id) : route('reportes-veeam.store') }}" class="space-y-6">
            @csrf
            @if(isset($reporte))
                @method('PUT')
            @endif

            {{-- Ticket --}}
            <div>
                <label for="numero_ticket" class="block text-sm font-medium text-gray-700">
                    Ticket
                </label>
                <input 
                    type="text" 
                    name="numero_ticket" 
                    id="numero_ticket"
                    value="{{ old('numero_ticket', isset($reporte) ? $reporte->numero_ticket : 'N/A') }}" 
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Número de ticket (opcional)">
                <p class="text-xs text-gray-500 mt-1">Por defecto se asigna "N/A". Acepta números y letras.</p>
            </div>

            {{-- Fecha Inicio --}}
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 campo-requerido">
                    Fecha Inicio
                </label>
                <input 
                    type="text" 
                    name="fecha_inicio" 
                    id="fecha_inicio"
                    value="{{ old('fecha_inicio', isset($reporte) ? $reporte->fecha_inicio->format('d/m/Y') : now()->format('d/m/Y')) }}" 
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required
                    placeholder="dd/mm/aaaa">
                <p class="text-xs text-gray-500 mt-1">Por defecto carga la fecha del sistema. Puedes editar haciendo clic o escribiendo directamente.</p>
            </div>

            {{-- Fecha Fin --}}
            <div>
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700">
                    Fecha Fin
                </label>
                <input 
                    type="text" 
                    name="fecha_fin" 
                    id="fecha_fin"
                    value="{{ old('fecha_fin', isset($reporte) && $reporte->fecha_fin ? $reporte->fecha_fin->format('d/m/Y') : '') }}" 
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="dd/mm/aaaa">
                <p class="text-xs text-gray-500 mt-1">Se completa automáticamente si el Estado del Ticket es "Informativo" o cambia a "Resuelto".</p>
            </div>

            {{-- Estado --}}
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 campo-requerido">
                    Estado
                </label>
                <select 
                    name="estado" 
                    id="estado"
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                    <option value="">Seleccione...</option>
                    <option value="Warning" {{ old('estado', isset($reporte) ? $reporte->estado : '') === 'Warning' ? 'selected' : '' }}>Warning</option>
                    <option value="Failed" {{ old('estado', isset($reporte) ? $reporte->estado : '') === 'Failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            {{-- Job Fallido --}}
            <div>
                <label for="job_fallido" class="block text-sm font-medium text-gray-700 campo-requerido">
                    Job Fallido
                </label>
                <input 
                    type="text" 
                    name="job_fallido" 
                    id="job_fallido"
                    value="{{ old('job_fallido', isset($reporte) ? $reporte->job_fallido : '') }}" 
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required
                    maxlength="500"
                    placeholder="Nombre del job reportado">
                <p class="text-xs text-gray-500 mt-1">Puedes copiar y pegar el nombre del job.</p>
            </div>

            {{-- Descripción del Error --}}
            <div>
                <label for="descripcion_error" class="block text-sm font-medium text-gray-700 campo-requerido">
                    Descripción del Error
                </label>
                <textarea 
                    name="descripcion_error" 
                    id="descripcion_error"
                    rows="6"
                    maxlength="3000"
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required
                    placeholder="Detalle de los errores del reporte Veeam (máx. 3000 caracteres)">{{ old('descripcion_error', isset($reporte) ? $reporte->descripcion_error : '') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Máximo 3000 caracteres. Puedes copiar y pegar.</p>
            </div>

            {{-- Estado del Ticket --}}
            <div>
                <label for="estado_ticket" class="block text-sm font-medium text-gray-700 campo-requerido">
                    Estado del Ticket
                </label>
                <select 
                    name="estado_ticket" 
                    id="estado_ticket"
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                    <option value="">Seleccione...</option>
                    <option value="Informativo" {{ old('estado_ticket', isset($reporte) ? $reporte->estado_ticket : '') === 'Informativo' ? 'selected' : '' }}>Informativo</option>
                    <option value="Pendiente" {{ old('estado_ticket', isset($reporte) ? $reporte->estado_ticket : '') === 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="Resuelto" {{ old('estado_ticket', isset($reporte) ? $reporte->estado_ticket : '') === 'Resuelto' ? 'selected' : '' }}>Resuelto</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Afecta el campo "Fecha Fin" y la posibilidad de agregar seguimiento.</p>
            </div>

            {{-- Seguimiento (solo crear/editar) --}}
            @if(!isset($reporte))
            <div id="seguimiento_inicial_container">
                <label for="seguimiento" class="block text-sm font-medium text-gray-700">
                    Seguimiento Inicial
                </label>
                <textarea 
                    name="seguimiento" 
                    id="seguimiento"
                    rows="4"
                    maxlength="1000"
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Agregar una nota de seguimiento inicial (opcional)">{{ old('seguimiento', 'Se envía correo informativo al cliente') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Texto predeterminado. Puedes editarlo o eliminarlo.</p>
            </div>
            @else
            {{-- Seguimiento existente (solo lectura) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Historial de Seguimiento
                </label>
                <textarea 
                    name="seguimiento" 
                    rows="8"
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-50"
                    readonly>{{ $reporte->seguimiento }}</textarea>
            </div>

            {{-- Nuevo seguimiento (visible en modo edición) --}}
            <div id="seguimiento_nuevo_container" style="display: {{ isset($reporte) ? 'block' : 'none' }};">
                <label for="seguimiento_nuevo" class="block text-sm font-medium text-gray-700">
                    Agregar Nuevo Seguimiento
                </label>
                <textarea 
                    name="seguimiento_nuevo" 
                    id="seguimiento_nuevo"
                    rows="4"
                    maxlength="1000"
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Agregar una nueva nota de seguimiento"></textarea>
                <p class="text-xs text-gray-500 mt-1">Se agregará al historial con tu nombre y la fecha actual.</p>
            </div>
            @endif

            {{-- Botones --}}
            <div class="flex justify-between items-center pt-6">
                    <div class="flex gap-2">
                        <a href="/menu-analista" class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded font-medium shadow transition">
                            Menú
                        </a>
                        <a href="{{ route('reportes-veeam.create') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                            Cancelar
                        </a>
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                        {{ isset($reporte) ? 'Actualizar Reporte' : 'Guardar Reporte' }}
                    </button>
            </div>
        </form>

        @if(isset($reporte))
        <div class="pt-4 border-t">
            <form method="POST" action="{{ route('reportes-veeam.destroy', $reporte->id) }}" onsubmit="return confirm('¿Estás seguro de eliminar este reporte?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700">
                    Eliminar Reporte
                </button>
            </form>
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar Flatpickr para campos de fecha
    const fpConfig = {
        dateFormat: 'd/m/Y',
        locale: 'es',
        allowInput: true,
    };

    flatpickr('#fecha_inicio', fpConfig);
    flatpickr('#fecha_fin', fpConfig);

    // Lógica para mostrar/ocultar campo de seguimiento nuevo
    const estadoTicket = document.getElementById('estado_ticket');
    const fechaFinInput = document.getElementById('fecha_fin');
    const seguimientoNuevoContainer = document.getElementById('seguimiento_nuevo_container');

    function actualizarCampos() {
        const estado = estadoTicket.value;

        // NO ocultar el campo de seguimiento - permitir agregar nota en cualquier estado
        // La validación se hace en el backend, no en el frontend
        
        // Auto-completar fecha_fin si es Informativo o Resuelto
        if (estado === 'Informativo' || estado === 'Resuelto') {
            if (!fechaFinInput.value || fechaFinInput.value === '') {
                const hoy = new Date();
                const dia = String(hoy.getDate()).padStart(2, '0');
                const mes = String(hoy.getMonth() + 1).padStart(2, '0');
                const anio = hoy.getFullYear();
                fechaFinInput.value = `${dia}/${mes}/${anio}`;
            }
        }
    }

    if (estadoTicket) {
        estadoTicket.addEventListener('change', actualizarCampos);
        // Ejecutar al cargar la página en caso de edición
        actualizarCampos();
    }
});
</script>

</body>
</html>
