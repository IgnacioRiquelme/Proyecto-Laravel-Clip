<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Incidente</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <style>
        .ts-control {
            border-radius: 0.375rem !important;
            border-color: rgb(209 213 219) !important;
            padding: 0.5rem !important;
            background-color: white !important;
        }
        .ts-wrapper {
            border-radius: 0.375rem;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-12">

<div class="flex w-full max-w-6xl bg-white p-8 rounded-lg shadow-lg border border-indigo-200">
    {{-- Menú lateral y filtros --}}
    <aside class="w-1/4 bg-gray-100 p-4 border-r border-gray-300">
        <h2 class="text-lg font-semibold mb-4">Acciones</h2>
        <div class="flex flex-col gap-4 mb-6">
            <a href="{{ route('incidentes.index') }}" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-center">
                Seguimiento Incidentes
            </a>
        </div>
        <div class="flex flex-col gap-4 mb-6">
            <a href="{{ route('procesos.conversor.convertir') }}" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-center">
                Consultor de Procesos
            </a>
        </div>
        <div class="flex flex-col gap-4 mb-6">
    <a href="{{ url('/procesos/mantenedor') }}" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-center">
        Mantenedor de Procesos
    </a>
</div>
        <h2 class="text-lg font-semibold mb-3">Filtros</h2>
        <select id="tipo-filtro" class="w-full border border-gray-300 rounded p-2 mt-4">
            <option value="">Agregar filtro...</option>
            <option value="proceso">Proceso</option>
            <option value="estado">Estado</option>
            <option value="servidor">Servidor</option>
            <option value="fecha">Rango de fechas</option>
        </select>
        <form id="filtrosForm" action="{{ route('incidentes.index') }}" method="GET">
            <div id="filtros-dinamicos" class="mb-4"></div>
            <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-center">
                Filtrar
            </button>
        </form>
    </aside>

    {{-- Formulario --}}
    <div class="flex-1 pl-8">
        <h1 class="text-3xl font-bold text-indigo-600 text-center mb-10">
            📝 Nuevo Incidente
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

        <form 
            action="{{ route('incidentes.store') }}" 
            method="POST" 
            class="grid grid-cols-2 gap-6"
        >
            @csrf

            {{-- Sincronización ID Proceso (schedulix_id) y Proceso --}}
            <div class="grid grid-cols-2 gap-4 mb-4 col-span-2">
                <div>
                    <label for="schedulix_id" class="block font-semibold text-sm mb-1">ID Proceso (Schedulix)</label>
                    <select id="schedulix_id" class="w-full">
                        <option value="">Selecciona ID Proceso</option>
                        @foreach($procesos as $proceso)
                            <option value="{{ $proceso->id }}">{{ $proceso->schedulix_id }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="proceso_id" class="block font-semibold text-sm mb-1">Proceso *</label>
                    <select id="proceso_id" name="proceso_id" class="w-full" required>
                        <option value="">Selecciona Proceso</option>
                        @foreach($procesos as $proceso)
                            <option value="{{ $proceso->id }}">{{ $proceso->trabajo }}</option>
                        @endforeach
                    </select>
                    @error('proceso_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Información automática del proceso --}}
            <div class="col-span-2">
                <div id="info-proceso" class="mb-4 p-4 bg-indigo-50 rounded text-indigo-900 hidden"></div>
            </div>

            {{-- Estado --}}
            <div>
                <label for="estado_incidente_id" class="block font-semibold text-sm mb-1">Estado *</label>
                <select name="estado_incidente_id" id="estado_incidente_id" class="searchable w-full" required>
                    <option value="">Seleccione un estado</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->id }}" {{ old('estado_incidente_id') == $estado->id ? 'selected' : '' }}>
                            {{ $estado->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('estado_incidente_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Negocio --}}
            <div>
                <label for="negocio_incidente_id" class="block font-semibold text-sm mb-1">Negocio *</label>
                <select name="negocio_incidente_id" id="negocio_incidente_id" class="searchable w-full" required>
                    <option value="">Seleccione un negocio</option>
                    @foreach($negocios as $negocio)
                        <option value="{{ $negocio->id }}" {{ old('negocio_incidente_id') == $negocio->id ? 'selected' : '' }}>
                            {{ $negocio->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('negocio_incidente_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Ambiente --}}
            <div>
                <label for="ambiente_incidente_id" class="block font-semibold text-sm mb-1">Ambiente *</label>
                <select name="ambiente_incidente_id" id="ambiente_incidente_id" class="searchable w-full" required>
                    <option value="">Seleccione un ambiente</option>
                    @foreach($ambientes as $ambiente)
                        <option value="{{ $ambiente->id }}" {{ old('ambiente_incidente_id') == $ambiente->id ? 'selected' : '' }}>
                            {{ $ambiente->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('ambiente_incidente_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Capa --}}
            <div>
                <label for="capa_incidente_id" class="block font-semibold text-sm mb-1">Capa *</label>
                <select name="capa_incidente_id" id="capa_incidente_id" class="searchable w-full" required>
                    <option value="">Seleccione una capa</option>
                    @foreach($capas as $capa)
                        <option value="{{ $capa->id }}" {{ old('capa_incidente_id') == $capa->id ? 'selected' : '' }}>
                            {{ $capa->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('capa_incidente_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Servidor --}}
            <div>
                <label for="servidor_incidente_id" class="block font-semibold text-sm mb-1">Servidor *</label>
                <select name="servidor_incidente_id" id="servidor_incidente_id" class="searchable w-full" required>
                    <option value="">Seleccione un servidor</option>
                    @foreach($servidores as $servidor)
                        <option value="{{ $servidor->id }}" {{ old('servidor_incidente_id') == $servidor->id ? 'selected' : '' }}>
                            {{ $servidor->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('servidor_incidente_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Evento --}}
            <div>
                <label for="evento_incidente_id" class="block font-semibold text-sm mb-1">Evento *</label>
                <select name="evento_incidente_id" id="evento_incidente_id" class="searchable w-full" required>
                    <option value="">Seleccione un evento</option>
                    @foreach($eventos as $evento)
                        <option value="{{ $evento->id }}" {{ old('evento_incidente_id') == $evento->id ? 'selected' : '' }}>
                            {{ $evento->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('evento_incidente_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Acción --}}
            <div>
                <label for="accion_incidente_id" class="block font-semibold text-sm mb-1">Acción *</label>
                <select name="accion_incidente_id" id="accion_incidente_id" class="searchable w-full" required>
                    <option value="">Seleccione una acción</option>
                    @foreach($acciones as $accion)
                        <option value="{{ $accion->id }}" {{ old('accion_incidente_id') == $accion->id ? 'selected' : '' }}>
                            {{ $accion->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('accion_incidente_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Escalado a --}}
            <div>
                <label for="escalado_incidente_id" class="block font-semibold text-sm mb-1">Escalado a *</label>
                <select name="escalado_incidente_id" id="escalado_incidente_id" class="searchable w-full" required>
                    <option value="">Seleccione una persona/área</option>
                    @foreach($escalados as $escalado)
                        <option value="{{ $escalado->id }}" {{ old('escalado_incidente_id') == $escalado->id ? 'selected' : '' }}>
                            {{ $escalado->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('escalado_incidente_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Requerimiento --}}
            <div class="col-span-2">
                <label for="requerimiento_tipo" class="block font-semibold text-sm mb-1">Tipo de Requerimiento *</label>
                <select id="requerimiento_tipo" class="searchable w-full border border-gray-300 rounded p-2">
                    <option value="correo" {{ old('requerimiento_tipo') == 'correo' ? 'selected' : '' }}>Enviado por Correo</option>
                    <option value="ticket" {{ old('requerimiento_tipo') == 'ticket' ? 'selected' : '' }}>Ticket de Incidente</option>
                </select>
            </div>
            <div class="col-span-2 {{ old('requerimiento_tipo') == 'ticket' ? '' : 'hidden' }}" id="numero_ticket_div">
                <label for="numero_ticket" class="block font-semibold text-sm mb-1">Número de Ticket</label>
                <input type="text" id="numero_ticket" class="w-full border border-gray-300 rounded p-2" value="{{ old('numero_ticket') }}">
            </div>
            <input type="hidden" name="requerimiento" id="requerimiento" value="{{ old('requerimiento', 'Enviado por Correo') }}">
            @error('requerimiento') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

            {{-- Seguimiento --}}
            <div class="col-span-2">
                <label for="seguimiento" class="block font-semibold text-sm mb-1">Seguimiento</label>
                <input type="text" name="seguimiento" id="seguimiento" value="{{ old('seguimiento') }}"
                    class="w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('seguimiento') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Descripción evento --}}
            <div class="col-span-2">
                <label for="descripcion_evento" class="block font-semibold text-sm mb-1">Descripción del evento</label>
                <textarea name="descripcion_evento" id="descripcion_evento" rows="3"
                    class="w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('descripcion_evento') }}</textarea>
                @error('descripcion_evento') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Solución --}}
            <div class="col-span-2">
                <label for="solucion" class="block font-semibold text-sm mb-1">Solución</label>
                <textarea name="solucion" id="solucion" rows="3"
                    class="w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('solucion') }}</textarea>
                @error('solucion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Observaciones --}}
            <div class="col-span-2">
                <label for="observaciones" class="block font-semibold text-sm mb-1">Observaciones</label>
                <textarea name="observaciones" id="observaciones" rows="3"
                    class="w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('observaciones') }}</textarea>
                @error('observaciones') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Fechas --}}
            <input type="hidden" name="inicio" value="{{ old('inicio', now()) }}">
            <input type="hidden" name="registro" value="{{ old('registro', now()) }}">

            <div class="col-span-2 flex justify-between items-center mt-4">
                <a href="{{ route('menu.analista') }}"
                    class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded font-medium shadow transition">
                    Menú
                </a>
                <button type="submit"
                    class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded font-medium shadow transition">
                    Guardar Incidente
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modales para agregar nuevos valores --}}
@php
$combos = [
    ['id' => 'estado_incidente_id', 'label' => 'estado', 'route' => route('estados_incidentes.store')],
    ['id' => 'negocio_incidente_id', 'label' => 'negocio', 'route' => route('negocio_incidentes.store')],
    ['id' => 'ambiente_incidente_id', 'label' => 'ambiente', 'route' => route('ambiente_incidentes.store')],
    ['id' => 'capa_incidente_id', 'label' => 'capa', 'route' => route('capa_incidentes.store')],
    ['id' => 'servidor_incidente_id', 'label' => 'servidor', 'route' => route('servidor_incidentes.store')],
    ['id' => 'evento_incidente_id', 'label' => 'evento', 'route' => route('evento_incidentes.store')],
    ['id' => 'accion_incidente_id', 'label' => 'acción', 'route' => route('accion_incidentes.store')],
    ['id' => 'escalado_incidente_id', 'label' => 'escalado a', 'route' => route('escalado_incidentes.store')],
    ['id' => 'requerimiento_tipo', 'label' => 'tipo de requerimiento', 'route' => route('tipo_requerimiento_incidentes.store')],
];
@endphp

@foreach($combos as $combo)
<div id="modal_{{ $combo['id'] }}" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow-md w-96">
        <h2 class="text-xl font-bold mb-4">Agregar nuevo {{ $combo['label'] }}</h2>
        <input type="text" id="nuevo_{{ $combo['id'] }}" class="w-full p-2 border border-gray-300 rounded mb-4" placeholder="Nombre del nuevo {{ $combo['label'] }}">
        <div class="flex justify-end space-x-2">
            <button type="button" onclick="cerrarModal('{{ $combo['id'] }}')" class="bg-gray-400 px-4 py-2 rounded text-white">Cancelar</button>
            <button type="button" onclick="guardarNuevoValor('{{ $combo['id'] }}', '{{ $combo['route'] }}')" class="bg-indigo-500 hover:bg-indigo-600 px-4 py-2 rounded text-white">Guardar</button>
        </div>
    </div>
</div>
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Carga todos los procesos en JS
    const procesos = @json($procesos);
    const procesoSelect = document.getElementById('proceso_id');
    const schedulixSelect = document.getElementById('schedulix_id');

    // Inicializa TomSelect para Proceso y Schedulix sin create
    new TomSelect(procesoSelect, { create: false, sortField: 'text' });
    new TomSelect(schedulixSelect, { create: false, sortField: 'text' });

    // Sincronización bidireccional
    procesoSelect.addEventListener('change', function () {
        const selectedId = parseInt(this.value);
        const proceso = procesos.find(p => p.id === selectedId);
        if (proceso) {
            schedulixSelect.tomselect.setValue(proceso.id, true);
            mostrarInfoProceso(proceso);
            aplicarRequerimientoYEscalado(proceso);
        } else {
            mostrarInfoProceso(null);
        }
    });

    schedulixSelect.addEventListener('change', function () {
        const selectedId = parseInt(this.value);
        const proceso = procesos.find(p => p.id === selectedId);
        if (proceso) {
            procesoSelect.tomselect.setValue(proceso.id, true);
            mostrarInfoProceso(proceso);
            aplicarRequerimientoYEscalado(proceso);
        } else {
            mostrarInfoProceso(null);
        }
    });

    function mostrarInfoProceso(proceso) {
        const infoDiv = document.getElementById('info-proceso');
        if (!proceso) {
            infoDiv.classList.add('hidden');
            infoDiv.innerHTML = '';
            return;
        }
        let info = `<strong>Responsable escalamiento:</strong> ${proceso.responsable_escalamiento || 'No definido'}<br>`;
        if (proceso.requiere_solucion_inmediata === 'Sí, declarar como incidente') {
            info += `<strong>Requiere solución inmediata:</strong> Sí, declarar como incidente<br>
            <span class="text-red-600">Debe escalar adicionalmente a través de un número de requerimiento (no obligatorio al crear).</span>`;
        } else if (proceso.requiere_solucion_inmediata === 'No') {
            info += `<strong>Requiere solución inmediata:</strong> No<br>
            <span class="text-green-600">El escalamiento es por correo y el responsable es ${proceso.responsable_escalamiento || 'No definido'}.</span>`;
        } else {
            info += `<strong>Requiere solución inmediata:</strong> Sin certeza<br>
            <span class="text-yellow-600">No se tiene certeza de cómo va este proceso.</span>`;
        }
        infoDiv.innerHTML = info;
        infoDiv.classList.remove('hidden');
    }

    // Cambia automáticamente el tipo de requerimiento y escalado
    function aplicarRequerimientoYEscalado(proceso) {
        const tipo = document.getElementById('requerimiento_tipo');
        const ticketDiv = document.getElementById('numero_ticket_div');
        const reqHidden = document.getElementById('requerimiento');
        if (proceso && proceso.requiere_solucion_inmediata === 'Sí, declarar como incidente') {
            tipo.value = 'ticket';
            ticketDiv.classList.remove('hidden');
            reqHidden.value = '';
        } else {
            tipo.value = 'correo';
            ticketDiv.classList.add('hidden');
            reqHidden.value = 'Enviado por Correo';
        }
        // Cambia escalado a
        const escaladoSelect = document.getElementById('escalado_incidente_id');
        if (proceso && proceso.responsable_escalamiento) {
            for (let opt of escaladoSelect.options) {
                if (opt.text.trim() === proceso.responsable_escalamiento.trim()) {
                    escaladoSelect.value = opt.value;
                    if (escaladoSelect.tomselect) escaladoSelect.tomselect.setValue(opt.value);
                    break;
                }
            }
        }
    }

    // TomSelect para todos los combos con popup de creación
    const combos = [
        'estado_incidente_id',
        'negocio_incidente_id',
        'ambiente_incidente_id',
        'capa_incidente_id',
        'servidor_incidente_id',
        'evento_incidente_id',
        'accion_incidente_id',
        'escalado_incidente_id',
        'requerimiento_tipo'
    ];
    combos.forEach(function(id) {
        const select = document.getElementById(id);
        if (!select) return;
        new TomSelect(select, {
            create: function(input, callback) {
                if (!input.trim()) return;
                window['_callback_' + id] = callback;
                document.getElementById('nuevo_' + id).value = input;
                document.getElementById('modal_' + id).classList.remove('hidden');
            },
            persist: false,
            maxOptions: 200,
            sortField: { field: "text", direction: "asc" }
        });
    });

    // Cerrar modal
    window.cerrarModal = function(id) {
        document.getElementById('modal_' + id).classList.add('hidden');
        document.getElementById('nuevo_' + id).value = '';
        window['_callback_' + id] = null;
    }

    // Guardar nuevo valor vía AJAX
    window.guardarNuevoValor = function(id, route) {
        const nombre = document.getElementById('nuevo_' + id).value.trim();
        if (!nombre) return;
        fetch(route, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ nombre })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (typeof window['_callback_' + id] === 'function') {
                    window['_callback_' + id]({ value: data.id ?? nombre, text: nombre });
                }
                cerrarModal(id);
            } else {
                alert("Error al guardar el nuevo valor");
            }
        })
        .catch(() => alert("Error al conectar con el servidor"));
    }

    // Filtros dinámicos
    document.getElementById('tipo-filtro').addEventListener('change', function () {
        const selected = this.value;
        const container = document.getElementById('filtros-dinamicos');
        if (!selected) return;
        let html = '';
        switch (selected) {
            case 'proceso':
                html = `<div class="mb-2"><label>Proceso</label>
                            <input type="text" name="filtro_proceso[]" class="w-full border p-1 rounded" />
                        </div>`;
                break;
            case 'estado':
                html = `<div class="mb-2"><label>Estado</label>
                            <input type="text" name="filtro_estado[]" class="w-full border p-1 rounded" />
                        </div>`;
                break;
            case 'servidor':
                html = `<div class="mb-2"><label>Servidor</label>
                            <input type="text" name="filtro_servidor[]" class="w-full border p-1 rounded" />
                        </div>`;
                break;
            case 'fecha':
                html = `<div class="mb-2">
                            <label>Desde</label><input type="date" name="fecha_desde[]" class="w-full border p-1 rounded" />
                            <label>Hasta</label><input type="date" name="fecha_hasta[]" class="w-full border p-1 rounded" />
                        </div>`;
                break;
        }
        container.insertAdjacentHTML('beforeend', html);
        this.value = '';
    });

    // Requerimiento tipo
    const tipo = document.getElementById('requerimiento_tipo');
    const ticketDiv = document.getElementById('numero_ticket_div');
    const ticketInput = document.getElementById('numero_ticket');
    const reqHidden = document.getElementById('requerimiento');

    function actualizarCampo() {
        if (tipo.value === 'ticket') {
            ticketDiv.classList.remove('hidden');
            reqHidden.value = ticketInput.value;
        } else {
            ticketDiv.classList.add('hidden');
            reqHidden.value = 'Enviado por Correo';
        }
    }

    tipo.addEventListener('change', actualizarCampo);
    ticketInput.addEventListener('input', actualizarCampo);
    actualizarCampo();
});
</script>
</body>
</html>