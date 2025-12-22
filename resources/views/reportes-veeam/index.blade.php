<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguimiento Reportes Veeam</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8">

<div class="container mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-indigo-600">
                📊 Seguimiento Reportes Veeam
            </h1>
            <div class="flex gap-4">
                <a href="{{ route('reportes-veeam.create') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Nuevo Reporte
                </a>
                <a href="{{ route('reportes-veeam.historico') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Ver Histórico
                </a>
                <form method="POST" action="{{ route('reportes-veeam.exportar') }}" class="inline">
                    @csrf
                    <input type="hidden" name="exportar_dia" value="1">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        📄 Exportar Excel
                    </button>
                </form>
                <a href="/menu-analista" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Volver al Menú
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(count($reportes) === 0)
            <div class="bg-blue-100 border border-blue-300 text-blue-700 p-4 rounded text-center">
                <p class="text-lg">🎉 No hay reportes pendientes</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead class="bg-indigo-600 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Ticket</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Fecha Inicio</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Fecha Fin</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Estado</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Job Fallido</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Estado Ticket</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Creado Por</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportes as $reporte)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">{{ $reporte->numero_ticket }}</td>
                            <td class="px-4 py-3 text-sm">{{ $reporte->fecha_inicio ? $reporte->fecha_inicio->format('d/m/Y') : 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $reporte->fecha_fin ? $reporte->fecha_fin->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded text-white text-xs {{ $reporte->estado === 'Failed' ? 'bg-red-500' : 'bg-yellow-500' }}">
                                    {{ $reporte->estado }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ Str::limit($reporte->job_fallido, 50) }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded text-white text-xs 
                                    {{ $reporte->estado_ticket === 'Informativo' ? 'bg-green-500' : 
                                       ($reporte->estado_ticket === 'Pendiente' ? 'bg-orange-500' : 'bg-blue-500') }}">
                                    {{ $reporte->estado_ticket }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $reporte->creado_por ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('reportes-veeam.edit', $reporte->id) }}" 
                                   class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-xs">
                                    Editar
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-sm text-gray-600">
                Total de reportes pendientes: <strong>{{ count($reportes) }}</strong>
            </div>
        @endif
    </div>
</div>

</body>
</html>
