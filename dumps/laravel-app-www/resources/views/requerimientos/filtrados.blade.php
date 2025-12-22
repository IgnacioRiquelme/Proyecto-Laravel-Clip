@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center bg-gray-100 dark:bg-gray-900 pt-12 min-h-screen">
    <h1 class="text-3xl font-bold text-center text-indigo-700 mb-6">Resultados de Requerimientos Filtrados</h1>

    <a href="{{ route('requerimientos.create') }}"
       class="mb-6 bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded shadow">
        Volver al formulario
    </a>

    @if (count($filtros) > 0)
        <div class="mb-4">
            <h4 class="font-semibold">Filtros aplicados:</h4>
            <ul class="text-sm text-gray-600 list-disc list-inside">
                {{-- ... tus filtros ... --}}
                @if (!empty($filtros['filtro_numero']))
                    <li><strong>Número de ticket:</strong> {{ implode(', ', $filtros['filtro_numero']) }}</li>
                @endif
                @if (!empty($filtros['fecha_desde']) && !empty($filtros['fecha_hasta']))
                    <li><strong>Fecha:</strong> {{ implode(', ', $filtros['fecha_desde']) }} - {{ implode(', ', $filtros['fecha_hasta']) }}</li>
                @endif
                @if (!empty($filtros['filtro_tipo']))
                    <li><strong>Tipo de requerimiento:</strong> {{ implode(', ', $filtros['filtro_tipo']) }}</li>
                @endif
                @if (!empty($filtros['filtro_negocio']))
                    <li><strong>Negocio:</strong> {{ implode(', ', $filtros['filtro_negocio']) }}</li>
                @endif
                @if (!empty($filtros['filtro_ambiente']))
                    <li><strong>Ambiente:</strong> {{ implode(', ', $filtros['filtro_ambiente']) }}</li>
                @endif
                @if (!empty($filtros['filtro_capa']))
                    <li><strong>Capa:</strong> {{ implode(', ', $filtros['filtro_capa']) }}</li>
                @endif
                @if (!empty($filtros['filtro_servidor']))
                    <li><strong>Servidor:</strong> {{ implode(', ', $filtros['filtro_servidor']) }}</li>
                @endif
                @if (!empty($filtros['filtro_estado']))
                    <li><strong>Estado:</strong> {{ implode(', ', $filtros['filtro_estado']) }}</li>
                @endif
                @if (!empty($filtros['filtro_tipo_solicitud']))
                    <li><strong>Tipo de solicitud:</strong> {{ implode(', ', $filtros['filtro_tipo_solicitud']) }}</li>
                @endif
                @if (!empty($filtros['filtro_tipo_pase']))
                    <li><strong>Tipo de pase:</strong> {{ implode(', ', $filtros['filtro_tipo_pase']) }}</li>
                @endif
                @if (!empty($filtros['filtro_ic']))
                    <li><strong>IC:</strong> {{ implode(', ', $filtros['filtro_ic']) }}</li>
                @endif
            </ul>
        </div>
    @endif

    {{-- FORMULARIO EXPORTAR A EXCEL --}}
@if(count($requerimientos) > 0)
<form action="{{ route('requerimientos.exportar') }}" method="POST" target="_blank" class="mb-4">
    @csrf
    @foreach(request()->all() as $key => $values)
        @if(is_array($values))
            @foreach($values as $value)
                <input type="hidden" name="{{ $key }}[]" value="{{ $value }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $key }}" value="{{ $values }}">
        @endif
    @endforeach
    <button type="submit" style="background: #166534; color: #fff; font-weight: bold; padding: 12px 24px; border-radius: 6px; margin-bottom: 16px;">
    Exportar a Excel
</button>
</form>
@endif

@if(count($requerimientos) === 0)
    <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 p-4 rounded">
        No se encontraron requerimientos para los filtros aplicados.
    </div>
@else
        <div class="overflow-x-auto w-full max-w-7xl block">
            <table class="table-auto w-full border border-gray-300 text-sm shadow">
                <thead class="bg-indigo-100 text-gray-800">
                    <tr>
                        <th class="border px-3 py-2">Fecha</th>
                        <th class="border px-3 py-2">Turno</th>
                        <th class="border px-3 py-2">Ticket</th>
                        <th class="border px-3 py-2">Solicitante</th>
                        <th class="border px-3 py-2">Requerimiento</th>
                        <th class="border px-3 py-2">Negocio</th>
                        <th class="border px-3 py-2">Ambiente</th>
                        <th class="border px-3 py-2">Capa</th>
                        <th class="border px-3 py-2">Servidor</th>
                        <th class="border px-3 py-2">Estado</th>
                        <th class="border px-3 py-2">Tipo Solicitud</th>
                        <th class="border px-3 py-2">Tipo Pase</th>
                        <th class="border px-3 py-2">IC</th>
                        <th class="border px-3 py-2">Observaciones</th>
                        <th class="border px-3 py-2">Creado por</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requerimientos as $req)
                        <tr class="odd:bg-white even:bg-gray-50">
                            <td class="border px-3 py-2">{{ $req->fecha_hora }}</td>
                            <td class="border px-3 py-2 capitalize">{{ $req->turno }}</td>
                            <td class="border px-3 py-2 font-bold">{{ $req->numero_ticket }}</td>
                            <td class="border px-3 py-2">{{ $req->solicitante }}</td>
                            <td class="border px-3 py-2">{{ $req->requerimiento }}</td>
                            <td class="border px-3 py-2">{{ $req->negocio }}</td>
                            <td class="border px-3 py-2">{{ $req->ambiente }}</td>
                            <td class="border px-3 py-2">{{ $req->capa }}</td>
                            <td class="border px-3 py-2">{{ $req->servidor }}</td>
                            <td class="border px-3 py-2">{{ $req->estado }}</td>
                            <td class="border px-3 py-2">{{ $req->tipo_solicitud }}</td>
                            <td class="border px-3 py-2">{{ $req->tipo_pase }}</td>
                            <td class="border px-3 py-2">{{ $req->ic }}</td>
                            <td class="border px-3 py-2">{{ $req->observaciones }}</td>
                            <td class="border px-3 py-2">{{ $req->creado_por }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection