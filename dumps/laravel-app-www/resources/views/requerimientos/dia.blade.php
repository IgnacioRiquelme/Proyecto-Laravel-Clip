<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Requerimientos del Día</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-indigo-600 mb-6 text-center">📅 Requerimientos del Día</h1>

        <a href="{{ route('requerimientos.create') }}" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded shadow mb-6 inline-block">← Volver</a>

        @if ($requerimientos->isEmpty())
            <div class="bg-yellow-100 text-yellow-700 p-4 rounded shadow text-center">
                No hay requerimientos registrados para hoy.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-[1200px] table-auto text-sm border border-gray-300 bg-white shadow rounded">
                    <thead class="bg-indigo-100 text-gray-700">
                        <tr>
                            <th class="p-2 border">Fecha</th>
                            <th class="p-2 border">Turno</th>
                            <th class="p-2 border">N° Ticket</th>
                            <th class="p-2 border">Requerimiento</th>
                            <th class="p-2 border">Solicitante</th>
                            <th class="p-2 border">Negocio</th>
                            <th class="p-2 border">Ambiente</th>
                            <th class="p-2 border">Capa</th>
                            <th class="p-2 border">Servidor</th>
                            <th class="p-2 border">Estado</th>
                            <th class="p-2 border">Tipo Solicitud</th>
                            <th class="p-2 border">Tipo Pase</th>
                            <th class="p-2 border">IC</th>
                            <th class="p-2 border">Observaciones</th>
                            <th class="p-2 border">Creado por</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requerimientos as $req)
                            <tr class="hover:bg-gray-100">
                                <td class="p-2 border">{{ $req->fecha_hora }}</td>
                                <td class="p-2 border">{{ $req->turno }}</td>
                                <td class="p-2 border">{{ $req->numero_ticket }}</td>
                                <td class="p-2 border">{{ $req->requerimiento }}</td>
                                <td class="p-2 border">{{ $req->solicitante }}</td>
                                <td class="p-2 border">{{ $req->negocio }}</td>
                                <td class="p-2 border">{{ $req->ambiente }}</td>
                                <td class="p-2 border">{{ $req->capa }}</td>
                                <td class="p-2 border">{{ $req->servidor }}</td>
                                <td class="p-2 border">{{ $req->estado }}</td>
                                <td class="p-2 border">{{ $req->tipo_solicitud }}</td>
                                <td class="p-2 border">{{ $req->tipo_pase }}</td>
                                <td class="p-2 border">{{ $req->ic }}</td>
                                <td class="p-2 border">{{ $req->observaciones }}</td>
                                <td class="p-2 border">{{ $req->creado_por }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</body>
</html>
