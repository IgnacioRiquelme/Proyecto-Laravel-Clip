<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes Veeam - Listado</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-12">

<div class="max-w-7xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-red-600">📋 Reportes Veeam</h1>
            <div class="flex gap-4">
                <a href="{{ route('reportes-veeam.create') }}" 
                   class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded font-medium shadow transition">
                    ➕ Nuevo Reporte
                </a>
                <a href="{{ route('menu.analista') }}" 
                   class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded font-medium shadow transition">
                    🏠 Menú
                </a>
            </div>
        </div>

        @if($reportes->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 text-sm">
                    <thead>
                        <tr class="bg-red-500 text-white">
                            <th class="border border-gray-300 px-3 py-2">ID</th>
                            <th class="border border-gray-300 px-3 py-2">Ticket</th>
                            <th class="border border-gray-300 px-3 py-2">Fecha Inicio</th>
                            <th class="border border-gray-300 px-3 py-2">Fecha Fin</th>
                            <th class="border border-gray-300 px-3 py-2">Estado</th>
                            <th class="border border-gray-300 px-3 py-2">Job Fallido</th>
                            <th class="border border-gray-300 px-3 py-2 min-w-[200px]">Seguimiento</th>
                            <th class="border border-gray-300 px-3 py-2 min-w-[200px]">Descripción Error</th>
                            <th class="border border-gray-300 px-3 py-2">Estado Ticket</th>
                            <th class="border border-gray-300 px-3 py-2">Creado Por</th>
                            <th class="border border-gray-300 px-3 py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportes as $reporte)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 px-3 py-2 text-center font-semibold">{{ $reporte->id }}</td>
                            <td class="border border-gray-300 px-3 py-2">{{ $reporte->numero_ticket }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-center">
                                {{ $reporte->fecha_inicio ? $reporte->fecha_inicio->format('d-m-Y') : '-' }}
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-center">
                                {{ $reporte->fecha_fin ? $reporte->fecha_fin->format('d-m-Y') : '-' }}
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-center">
                                <span class="px-2 py-1 rounded text-xs font-semibold
                                    {{ $reporte->estado == 'Failed' ? 'bg-red-200 text-red-800' : '' }}
                                    {{ $reporte->estado == 'Warning' ? 'bg-yellow-200 text-yellow-800' : '' }}
                                    {{ $reporte->estado == 'Success' ? 'bg-green-200 text-green-800' : '' }}">
                                    {{ $reporte->estado }}
                                </span>
                            </td>
                            <td class="border border-gray-300 px-3 py-2">{{ $reporte->job_fallido }}</td>
                            <td class="border border-gray-300 px-3 py-2">
                                <div class="max-h-24 overflow-y-auto text-xs whitespace-pre-wrap">
                                    {{ $reporte->seguimiento ?? 'Sin seguimiento' }}
                                </div>
                            </td>
                            <td class="border border-gray-300 px-3 py-2">
                                <div class="max-h-24 overflow-y-auto text-xs">
                                    {{ $reporte->descripcion_error }}
                                </div>
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-center">
                                <span class="px-2 py-1 rounded text-xs font-semibold
                                    {{ $reporte->estado_ticket == 'Pendiente' ? 'bg-orange-200 text-orange-800' : '' }}
                                    {{ $reporte->estado_ticket == 'Resuelto' ? 'bg-green-200 text-green-800' : '' }}
                                    {{ $reporte->estado_ticket == 'Informativo' ? 'bg-blue-200 text-blue-800' : '' }}">
                                    {{ $reporte->estado_ticket }}
                                </span>
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-xs">{{ $reporte->creado_por ?? '-' }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-center">
                                <div class="flex flex-col gap-2">
                                    <a href="{{ route('reportes-veeam.edit', $reporte->id) }}" 
                                       class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs">
                                        ✏️ Editar
                                    </a>
                                    <form action="{{ route('reportes-veeam.destroy', $reporte->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('¿Estás seguro de eliminar este reporte?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-full bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">
                                            🗑️ Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 p-4 rounded text-center">
                ⚠️ No se encontraron reportes con los criterios seleccionados.
            </div>
        @endif

        <div class="mt-6 text-center">
            <a href="{{ route('reportes-veeam.create') }}" 
               class="text-red-500 hover:underline font-medium">
                ← Volver al formulario
            </a>
        </div>
    </div>
</div>

</body>
</html>