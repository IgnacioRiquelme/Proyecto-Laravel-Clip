@extends('layouts.app')

@section('content')
<div class="p-4 bg-gray-100 min-h-screen">
<div class="p-4">
    <div class="flex items-center justify-between mb-6">
        <!-- Botón Volver -->
        <a href="{{ route('menu.analista') }}"
                    class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded font-medium shadow transition">
                    Menú
                </a>
    </div>
    <div class="mb-4 relative">
        <h1 class="text-3xl font-bold text-indigo-700 text-center">Malla de Procesos</h1>

        <div class="absolute right-0 top-0 flex gap-2 items-start">
            @if (empty($vista_historica))
                <form method="POST" action="{{ route('procesos.cerrar-dia') }}"
                    onsubmit="return confirm('¿Estás seguro que deseas cerrar el día?')">
                    @csrf
                    <button type="submit"
                            style="background: #991b1b; color: #fff; font-weight: bold; padding: 12px 24px; border-radius: 6px;">
                        🔒 Cerrar Día
                    </button>
                </form>
            @endif

            <form action="{{ route('procesos.exportar', $fecha_malla) }}" method="GET" target="_blank">
                <button type="submit"
                        style="background: #166534; color: #fff; font-weight: bold; padding: 12px 24px; border-radius: 6px;">
                    📤 Exportar a Excel
                </button>
            </form>
        </div>
    </div>

    <div class="text-center mt-4 mb-6">
        <label for="historico-selector" class="text-sm text-gray-700 mr-2">Ver malla de días anteriores:</label>
        <select id="historico-selector" class="text-sm border rounded px-2 py-1"
                onchange="if (this.value) window.location.href = this.value;">
            <option value="">-- Seleccionar fecha --</option>
            @php
                $actual = \Carbon\Carbon::parse($fecha_malla ?? now('America/Santiago'));
            @endphp
            @for ($i = 1; $i <= 7; $i++)
                @php
                    $fecha = $actual->copy()->subDays($i);
                    $ruta = route('procesos.historico', $fecha->toDateString());
                @endphp
                <option value="{{ $ruta }}">{{ $fecha->format('d-m-Y') }}</option>
            @endfor
        </select>
    </div>

    @if (!empty($vista_historica))
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-2 rounded mb-4 text-sm text-center">
            🕰️ Estás viendo la malla del <strong>{{ \Carbon\Carbon::parse($fecha_malla)->format('d-m-Y') }}</strong>.
            <a href="{{ route('procesos.malla') }}" class="underline text-blue-700 hover:text-blue-900 ml-2">
                ← Volver a malla actual
            </a>
        </div>
    @else
        <p class="text-sm text-gray-700 mb-4 text-center">
            Fecha en ejecución: <strong>{{ $fecha_malla ?? \Carbon\Carbon::now('America/Santiago')->format('d-m-Y') }}</strong>
        </p>

        <div class="text-center mb-6">
            <a href="{{ route('procesos.historico', \Carbon\Carbon::parse($fecha_malla)->subDay()->format('Y-m-d')) }}"
               class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900 text-sm font-semibold shadow">
                🗓️ Ver Día Anterior
            </a>
        </div>
    @endif

    @php
        $mostrarZenit = false;
    @endphp

 {{-- Título BCI SEGUROS --}}
<h2 class="text-2xl font-bold text-center text-gray-800 mb-6 border-b-2 pb-2">🏢 COMPAÑÍA BCI SEGUROS</h2>

@php
    $mostrarZenit = false;
@endphp

@foreach ($grupos as $grupo)
    {{-- Antes de mostrar el grupo Zenit, mostramos el título centrado --}}
    @if (!$mostrarZenit && $grupo['nombre'] === 'Procesos Zenit')
        <div style="height:40px;"></div>
<h2 class="text-2xl font-bold text-center text-gray-800 mb-6 border-b-2 pb-2">🏢 COMPAÑÍA ZENIT SEGUROS</h2>
        @php $mostrarZenit = true; @endphp
    @endif

    @if (!empty($grupo['procesos']))
        <div class="mb-10">
            <h2 class="mt-6 text-2xl font-bold text-{{ $grupo['color'] }}-600 mb-4 flex items-center gap-2">
                @php
                    $iconos = [
                        'red' => '🔥', 'indigo' => '📦', 'blue' => '💾', 'green' => '🛡️',
                        'purple' => '🌐', 'yellow' => '🧬', 'gray' => '📁', 'orange' => '📊', 'slate' => '📄',
                    ];
                    $icono = $iconos[$grupo['color']] ?? '📄';
                @endphp
                <span>{{ $icono }}</span>
                <span>{{ $grupo['nombre'] }}</span>
            </h2>

            <div class="grid gap-4" style="grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));">
                @foreach ($grupo['procesos'] as $proceso)
                        @php
                            $bgColor = $proceso->corre_hoy
                                ? match($grupo['color']) {
                                    'red' => '#ef4444', 'indigo' => '#6366f1', 'blue' => '#3b82f6', 'green' => '#10b981',
                                    'purple' => '#8b5cf6', 'yellow' => '#facc15', 'gray' => '#6b7280', 'orange' => '#f97316',
                                    default => '#64748b'
                                }
                                : '#E5E7EB';

                            $textColor = $proceso->corre_hoy ? 'text-white drop-shadow-sm' : 'text-gray-700';
                            $estado = $proceso->estado_nombre ?? $proceso->estado ?? 'Pendiente';
                        @endphp

                        <div id="proceso-{{ $proceso->id_proceso }}"
                             class="rounded-xl shadow-md p-3 transition duration-300 relative flex flex-col justify-between {{ $textColor }}"
                             style="background-color: {{ $bgColor }}; height: 190px;"
                             title="{{ $proceso->descripcion }}">

                            <div class="font-bold text-sm">{{ $proceso->id_proceso }}</div>
                            <div class="text-xs mb-1">{{ $proceso->proceso }}</div>

                            <div class="text-xs">
                                <strong>Estado:</strong>
                                <span
                                    style="background-color: {{ $proceso->color_fondo ?? '#ffffff' }};
                                            color: {{ $proceso->color_texto ?? '#000000' }};
                                            border: 1px solid {{ $proceso->borde_color ?? '#000000' }};"
                                    class="px-2 py-0.5 rounded text-xs inline-block mt-1"
                                >
                                    {{ $proceso->emoji ?? '' }} {{ $estado }}
                                </span>
                            </div>

                            @if ($proceso->inicio && $proceso->fin)
                                @php
                                    $duracion = $proceso->inicio->diff($proceso->fin)->format('%H:%I:%S');
                                @endphp
                                <div class="text-xs mt-1">⏱️ <strong>Duración:</strong> {{ $duracion }}</div>
                            @elseif ($proceso->inicio && !$proceso->fin)
                                <div class="text-xs mt-1 text-yellow-800">⏳ <strong>En ejecución desde:</strong> {{ $proceso->inicio->format('H:i') }}</div>
                            @endif

                            @if ($proceso->adm_inicio)
                                <div class="text-xs mt-1">👤 <strong>Inicio:</strong> {{ $proceso->adm_inicio }}</div>
                            @endif

                            @if ($proceso->adm_fin)
                                <div class="text-xs">👤 <strong>Fin:</strong> {{ $proceso->adm_fin }}</div>
                            @endif

                            @if ($proceso->corre_hoy || ($proceso->inicio && !$proceso->fin))
                                <div class="mt-2">
                                    <button
                                        class="text-white text-xs underline hover:text-gray-200"
                                        data-id="{{ $proceso->id_proceso }}"
                                        data-nombre="{{ $proceso->proceso }}"
                                        data-inicio="{{ optional($proceso->inicio)->format('Y-m-d\TH:i') }}"
                                        data-fin="{{ optional($proceso->fin)->format('Y-m-d\TH:i') }}"
                                        data-estado="{{ $proceso->estado_nombre ?? 'Pendiente' }}"
                                        onclick="handleClick(this)">
                                        Actualizar
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
</div>

{{-- ✅ MODAL --}}
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-sm p-6 relative">
        <form id="modalForm" method="POST" action="">
            @csrf
            <input type="hidden" id="modal-fecha" name="fecha" value="{{ $fecha_malla ?? now()->toDateString() }}">
            <h2 class="text-lg font-semibold mb-4 text-gray-800">
                Actualizar Proceso <span id="modal-proceso-id" class="text-indigo-600"></span>
            </h2>

            <label class="block text-sm text-gray-700 mb-1">Inicio</label>
            <input type="datetime-local" id="modal-inicio" name="inicio"
                   class="w-full border rounded px-2 py-1 mb-3 text-sm text-gray-900">

            <label class="block text-sm text-gray-700 mb-1">Fin</label>
            <input type="datetime-local" id="modal-fin" name="fin"
                   class="w-full border rounded px-2 py-1 mb-3 text-sm text-gray-900">

            <label class="block text-sm text-gray-700 mb-1">Estado</label>
            <select id="modal-estado" name="estado"
                    class="w-full border rounded px-2 py-1 mb-4 text-sm text-gray-900">
                @foreach (['Pendiente', 'En ejecución', 'Ok', 'Anómalo', 'No corre', 'Undurraga', 'OK con observaciones', 'Sin Registro'] as $estado)
                    <option value="{{ $estado }}">{{ $estado }}</option>
                @endforeach
            </select>

            <div class="flex justify-between">
                <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 text-sm">
                    Guardar
                </button>
                <button type="button" onclick="closeModal()" class="text-red-600 text-sm hover:underline">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let procesoActivo = null;

function handleClick(button) {
    const id = button.dataset.id;
    const nombre = button.dataset.nombre || '';
    const inicio = button.dataset.inicio || '';
    const fin = button.dataset.fin || '';
    const estado = button.dataset.estado || 'Pendiente';

    const form = document.getElementById('modalForm');
    document.getElementById('modal-inicio').value = inicio;
    document.getElementById('modal-fin').value = fin;
    document.getElementById('modal-estado').value = estado;
    document.getElementById('modal-fecha').value = "{{ $fecha_malla ?? now()->toDateString() }}";
    form.action = `/procesos/actualizar/${id}`;

    document.getElementById('modal-proceso-id').textContent = `(ID: ${id}) - ${nombre}`;

    if (procesoActivo) procesoActivo.classList.remove('card-activa');

    const tarjeta = document.getElementById(`proceso-${id}`);
    if (tarjeta) {
        tarjeta.classList.add('card-activa');
        procesoActivo = tarjeta;
    }

    document.getElementById('modal').classList.add('hidden'); // Ocultar antes de mostrar para asegurar el estado
    document.getElementById('modal').classList.remove('hidden');
}

function closeModal() {
    if (procesoActivo) {
        procesoActivo.classList.remove('card-activa');
        procesoActivo = null;
    }
    document.getElementById('modal').classList.add('hidden');
}
</script>

<style>
.card-activa {
    outline: 3px solid #2563eb;
    outline-offset: 2px;
}
</style>
@endsection