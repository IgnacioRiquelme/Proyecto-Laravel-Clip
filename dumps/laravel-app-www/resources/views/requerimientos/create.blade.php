<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $requerimiento ? 'Editar' : 'Ingresar' }} Requerimiento</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Tom Select -->
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
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center py-12">

<div class="flex w-full max-w-6xl bg-white p-8 rounded-lg shadow-lg border border-indigo-200">
    {{-- Menú lateral --}}
    <aside class="w-1/4 bg-gray-100 p-4 border-r border-gray-300">
        <h2 class="text-lg font-semibold mb-4">Acciones</h2>
        <div class="flex flex-col gap-4 mb-6">
            <a href="{{ route('requerimientos.dia') }}" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-center">
                Requerimientos del día
            </a>
            <form id="filtrosForm" action="{{ route('requerimientos.filtrados') }}" method="POST">
                @csrf
                <div id="filtros-dinamicos" class="mb-4"></div>
                <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-center">
                    Filtrar
                </button>
            </form>
        </div>

        <h2 class="text-lg font-semibold mb-3">Filtros</h2>
        <select id="tipo-filtro" class="w-full border border-gray-300 rounded p-2 mt-4">
            <option value="">Agregar filtro...</option>
            <option value="numero">N° de requerimiento</option>
            <option value="fecha">Rango de fechas</option>
            <option value="tipo">Tipo de requerimiento</option>
            <option value="negocio">Tipo de negocio</option>
            <option value="ambiente">Tipo de ambiente</option>
            <option value="capa">Capa</option>
            <option value="servidor">Servidor</option>
            <option value="estado">Estado</option>
            <option value="tipo_solicitud">Tipo de solicitud</option>
            <option value="tipo_pase">Tipo de pase</option>
            <option value="ic">IC</option>
        </select>
    </aside>

    {{-- Formulario --}}
    <div class="flex-1 pl-8">
        <h1 class="text-3xl font-bold text-indigo-600 text-center mb-10">
            {{ $requerimiento ? '✏️ Editar Requerimiento' : '📝 Nuevo Requerimiento' }}
        </h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-300 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form 
            action="{{ $requerimiento ? route('requerimientos.update', $requerimiento->numero_ticket) : route('requerimientos.store') }}" 
            method="POST" 
            class="grid grid-cols-2 gap-6"
        >
            @csrf
            @if($requerimiento)
                @method('PUT')
            @endif

            <label class="uppercase text-base font-semibold text-gray-700">Número de Ticket</label>
            <div>
                <input type="text" name="numero_ticket"
                    value="{{ old('numero_ticket', $requerimiento->numero_ticket ?? '') }}"
                    class="p-2 border border-gray-300 rounded w-full shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    {{ $requerimiento ? 'readonly' : '' }} required>
                @error('numero_ticket')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <label class="uppercase text-base font-semibold text-gray-700">Solicitante</label>
            <div>
                <select name="solicitante" class="searchable w-full" required>
                    <option value="" disabled {{ !$requerimiento ? 'selected' : '' }}>Seleccione un solicitante</option>
                    @foreach ($tiposSolicitantes as $item)
                        <option value="{{ $item->nombre }}" 
                            @selected(old('solicitante', $requerimiento->solicitante ?? '') == $item->nombre)>
                            {{ $item->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('solicitante')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <label class="uppercase text-base font-semibold text-gray-700">Requerimiento</label>
            <div>
                <select name="requerimiento" class="searchable w-full" required>
                    <option value="" disabled {{ !$requerimiento ? 'selected' : '' }}>Seleccione un requerimiento</option>
                    @foreach ($tiposRequerimientos as $item)
                        <option value="{{ $item->nombre }}" 
                            @selected(old('requerimiento', $requerimiento->requerimiento ?? '') == $item->nombre)>
                            {{ $item->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('requerimiento')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <label class="uppercase text-base font-semibold text-gray-700">Negocio</label>
            <div>
                <select name="negocio" class="searchable w-full" required>
                    <option value="" disabled {{ !$requerimiento ? 'selected' : '' }}>Seleccione un negocio</option>
                    @foreach ($tiposNegocios as $item)
                        <option value="{{ $item->nombre }}" 
                            @selected(old('negocio', $requerimiento->negocio ?? '') == $item->nombre)>
                            {{ $item->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('negocio')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <label class="uppercase text-base font-semibold text-gray-700">Ambiente</label>
            <div>
                <select name="ambiente" class="searchable w-full" required>
                    <option value="" disabled {{ !$requerimiento ? 'selected' : '' }}>Seleccione un ambiente</option>
                    @foreach ($tiposAmbientes as $item)
                        <option value="{{ $item->nombre }}" 
                            @selected(old('ambiente', $requerimiento->ambiente ?? '') == $item->nombre)>
                            {{ $item->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('ambiente')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <label class="uppercase text-base font-semibold text-gray-700">Capa</label>
            <div>
                <select name="capa" class="searchable w-full" required>
                    <option value="" disabled {{ !$requerimiento ? 'selected' : '' }}>Seleccione una capa</option>
                    @foreach ($tiposCapas as $item)
                        <option value="{{ $item->nombre }}" 
                            @selected(old('capa', $requerimiento->capa ?? '') == $item->nombre)>
                            {{ $item->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('capa')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <label class="uppercase text-base font-semibold text-gray-700">Servidor</label>
            <div>
                <select name="servidor" class="searchable w-full" required>
                    <option value="" disabled {{ !$requerimiento ? 'selected' : '' }}>Seleccione un servidor</option>
                    @foreach ($tiposServidores as $item)
                        <option value="{{ $item->nombre }}" 
                            @selected(old('servidor', $requerimiento->servidor ?? '') == $item->nombre)>
                            {{ $item->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('servidor')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <label class="uppercase text-base font-semibold text-gray-700">Estado</label>
            <div>
                <select name="estado" class="searchable w-full" required>
                    <option value="" disabled {{ !$requerimiento ? 'selected' : '' }}>Seleccione un estado</option>
                    @foreach ($tiposEstados as $item)
                        <option value="{{ $item->nombre }}" 
                            @selected(old('estado', $requerimiento->estado ?? '') == $item->nombre)>
                            {{ $item->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('estado')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <label class="uppercase text-base font-semibold text-gray-700">Tipo de Solicitud</label>
            <div>
                <select name="tipo_solicitud" class="searchable w-full" required>
                    <option value="" disabled {{ !$requerimiento ? 'selected' : '' }}>Seleccione tipo de solicitud</option>
                    @foreach ($tiposSolicitudes as $item)
                        <option value="{{ $item->nombre }}" 
                            @selected(old('tipo_solicitud', $requerimiento->tipo_solicitud ?? '') == $item->nombre)>
                            {{ $item->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('tipo_solicitud')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <label class="uppercase text-base font-semibold text-gray-700">Tipo de Pase</label>
            <div>
                <select name="tipo_pase" class="searchable w-full" required>
                    <option value="" disabled {{ !$requerimiento ? 'selected' : '' }}>Seleccione tipo de pase</option>
                    @foreach ($tiposPases as $item)
                        <option value="{{ $item->nombre }}" 
                            @selected(old('tipo_pase', $requerimiento->tipo_pase ?? '') == $item->nombre)>
                            {{ $item->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('tipo_pase')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <label class="uppercase text-base font-semibold text-gray-700">IC</label>
            <div>
                <select name="ic" class="searchable w-full">
                    <option value="" disabled {{ !$requerimiento ? 'selected' : '' }}>Seleccione un IC (opcional)</option>
                    @foreach ($tiposICs as $item)
                        <option value="{{ $item->nombre }}" 
                            @selected(old('ic', $requerimiento->ic ?? '') == $item->nombre)>
                            {{ $item->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('ic')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <label class="uppercase text-base font-semibold text-gray-700 col-span-2">Observaciones</label>
            <textarea name="observaciones" placeholder="Observaciones"
                class="p-2 border border-gray-300 rounded w-full col-span-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('observaciones', $requerimiento->observaciones ?? '') }}</textarea>
            @error('observaciones')
                <span class="text-red-500 text-sm col-span-2">{{ $message }}</span>
            @enderror

            <div class="col-span-2 flex justify-between items-center mt-4">
    <a href="{{ route('menu.analista') }}"
        class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded font-medium shadow transition">
        Menú
    </a>
    @if(!$requerimiento)
    <button type="button"
        onclick="pedirTicketEditar()"
        class="bg-yellow-400 hover:bg-yellow-500 text-white px-6 py-2 rounded font-medium shadow transition mx-2">
        Editar Requerimiento
    </button>
@endif
    <button type="submit"
        class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded font-medium shadow transition">
        {{ $requerimiento ? 'Actualizar' : 'Guardar' }}
    </button>
</div>
        </form>

        <!-- Modal -->
        <div id="modalSolicitante" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow-md w-96">
                <h2 class="text-xl font-bold mb-4">Agregar nuevo solicitante</h2>
                <input type="text" id="nuevoSolicitante" class="w-full p-2 border border-gray-300 rounded mb-4" placeholder="Nombre del nuevo solicitante">
                <div class="flex justify-end space-x-2">
                    <button onclick="cerrarModal()" class="bg-gray-400 px-4 py-2 rounded text-white">Cancelar</button>
                    <button onclick="guardarSolicitante()" class="bg-indigo-500 hover:bg-indigo-600 px-4 py-2 rounded text-white">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Opciones para los filtros (desde PHP a JS)
    const opcionesTipo = @json($tiposRequerimientos->pluck('nombre'));
    const opcionesNegocio = @json($tiposNegocios->pluck('nombre'));
    const opcionesAmbiente = @json($tiposAmbientes->pluck('nombre'));
    const opcionesCapa = @json($tiposCapas->pluck('nombre'));
    const opcionesServidor = @json($tiposServidores->pluck('nombre'));
    const opcionesEstado = @json($tiposEstados->pluck('nombre'));
    const opcionesTipoSolicitud = @json($tiposSolicitudes->pluck('nombre'));
    const opcionesTipoPase = @json($tiposPases->pluck('nombre'));
    const opcionesIC = @json($tiposICs->pluck('nombre'));

    document.addEventListener('DOMContentLoaded', function () {
        // Inicializar TomSelect en todos los selects
        document.querySelectorAll('.searchable').forEach(function (select) {
            const isSolicitante = select.name === 'solicitante';
            new TomSelect(select, {
                create: isSolicitante ? function (input, callback) {
                    if (!input.trim()) return;
                    window._callbackSolicitante = callback;
                    document.getElementById('nuevoSolicitante').value = input;
                    document.getElementById('modalSolicitante').classList.remove('hidden');
                } : false,
                persist: false,
                maxOptions: 200,
                sortField: { field: "text", direction: "asc" }
            });
        });

        // Auto-llenado de campos según combinación
        const requerimientoSelect = document.querySelector('select[name="requerimiento"]');
        const negocioSelect = document.querySelector('select[name="negocio"]');
        const ambienteSelect = document.querySelector('select[name="ambiente"]');

        function verificarYAutocompletar() {
            const req = requerimientoSelect.value.trim();
            const neg = negocioSelect.value.trim();
            const amb = ambienteSelect.value.trim();

            let valores = null;

            if (req === 'Pase a QA' && neg === 'BCI Seguros' && amb === 'As400') {
                valores = {
                    capa: 'Aplicativo',
                    servidor: 'Ascerbci',
                    estado: 'Exitoso',
                    tipo_solicitud: 'Proactivanet',
                    tipo_pase: 'Normal',
                    ic: 'N/A'
                };
            } else if (req === 'Pase a QA' && neg === 'ZENIT Seguros' && amb === 'As400') {
                valores = {
                    capa: 'Aplicativo',
                    servidor: 'Ascerzen',
                    estado: 'Exitoso',
                    tipo_solicitud: 'Proactivanet',
                    tipo_pase: 'Normal',
                    ic: 'N/A'
                };
            } else if (req === 'Pase a Producción' && neg === 'BCI Seguros' && amb === 'As400') {
                valores = {
                    capa: 'Aplicativo',
                    servidor: 'Concorde',
                    estado: 'Exitoso',
                    tipo_solicitud: 'Proactivanet',
                    tipo_pase: 'Normal',
                    ic: 'N/A'
                };
            } else if (req === 'Pase a Producción' && neg === 'ZENIT Seguros' && amb === 'As400') {
                valores = {
                    capa: 'Aplicativo',
                    servidor: 'Breton',
                    estado: 'Exitoso',
                    tipo_solicitud: 'Proactivanet',
                    tipo_pase: 'Normal',
                    ic: 'N/A'
                };
            }

            if (valores) {
                Object.entries(valores).forEach(([name, value]) => {
                    const select = document.querySelector(`select[name="${name}"]`);
                    if (select && select.tomselect) {
                        select.tomselect.setValue(value);
                    }
                });
            }
        }

        [requerimientoSelect, negocioSelect, ambienteSelect].forEach(function (select) {
            select.addEventListener('change', verificarYAutocompletar);
        });

        // Script para los filtros del menú lateral con combobox
        document.getElementById('tipo-filtro').addEventListener('change', function () {
            const selected = this.value;
            const container = document.getElementById('filtros-dinamicos');
            if (!selected) return;
            let html = '';
            switch (selected) {
                case 'numero':
                    html = `<div class="mb-2"><label>N° Requerimiento</label>
                                <input type="text" name="filtro_numero[]" class="w-full border p-1 rounded" />
                            </div>`;
                    break;
                case 'fecha':
                    html = `<div class="mb-2">
                                <label>Desde</label><input type="date" name="fecha_desde[]" class="w-full border p-1 rounded" />
                                <label>Hasta</label><input type="date" name="fecha_hasta[]" class="w-full border p-1 rounded" />
                            </div>`;
                    break;
                case 'tipo':
                    html = `<div class="mb-2">
                                <label>Tipo de requerimiento</label>
                                <select class="filtro-select w-full border p-1 rounded" name="filtro_tipo[]">
                                    <option value="">Seleccione...</option>
                                    ${opcionesTipo.map(o => `<option value="${o}">${o}</option>`).join('')}
                                </select>
                            </div>`;
                    break;
                case 'negocio':
                    html = `<div class="mb-2">
                                <label>Tipo de negocio</label>
                                <select class="filtro-select w-full border p-1 rounded" name="filtro_negocio[]">
                                    <option value="">Seleccione...</option>
                                    ${opcionesNegocio.map(o => `<option value="${o}">${o}</option>`).join('')}
                                </select>
                            </div>`;
                    break;
                case 'ambiente':
                    html = `<div class="mb-2">
                                <label>Tipo de ambiente</label>
                                <select class="filtro-select w-full border p-1 rounded" name="filtro_ambiente[]">
                                    <option value="">Seleccione...</option>
                                    ${opcionesAmbiente.map(o => `<option value="${o}">${o}</option>`).join('')}
                                </select>
                            </div>`;
                    break;
                case 'capa':
                    html = `<div class="mb-2">
                                <label>Capa</label>
                                <select class="filtro-select w-full border p-1 rounded" name="filtro_capa[]">
                                    <option value="">Seleccione...</option>
                                    ${opcionesCapa.map(o => `<option value="${o}">${o}</option>`).join('')}
                                </select>
                            </div>`;
                    break;
                case 'servidor':
                    html = `<div class="mb-2">
                                <label>Servidor</label>
                                <select class="filtro-select w-full border p-1 rounded" name="filtro_servidor[]">
                                    <option value="">Seleccione...</option>
                                    ${opcionesServidor.map(o => `<option value="${o}">${o}</option>`).join('')}
                                </select>
                            </div>`;
                    break;
                case 'estado':
                    html = `<div class="mb-2">
                                <label>Estado</label>
                                <select class="filtro-select w-full border p-1 rounded" name="filtro_estado[]">
                                    <option value="">Seleccione...</option>
                                    ${opcionesEstado.map(o => `<option value="${o}">${o}</option>`).join('')}
                                </select>
                            </div>`;
                    break;
                case 'tipo_solicitud':
                    html = `<div class="mb-2">
                                <label>Tipo de solicitud</label>
                                <select class="filtro-select w-full border p-1 rounded" name="filtro_tipo_solicitud[]">
                                    <option value="">Seleccione...</option>
                                    ${opcionesTipoSolicitud.map(o => `<option value="${o}">${o}</option>`).join('')}
                                </select>
                            </div>`;
                    break;
                case 'tipo_pase':
                    html = `<div class="mb-2">
                                <label>Tipo de pase</label>
                                <select class="filtro-select w-full border p-1 rounded" name="filtro_tipo_pase[]">
                                    <option value="">Seleccione...</option>
                                    ${opcionesTipoPase.map(o => `<option value="${o}">${o}</option>`).join('')}
                                </select>
                            </div>`;
                    break;
                case 'ic':
                    html = `<div class="mb-2">
                                <label>IC</label>
                                <select class="filtro-select w-full border p-1 rounded" name="filtro_ic[]">
                                    <option value="">Seleccione...</option>
                                    ${opcionesIC.map(o => `<option value="${o}">${o}</option>`).join('')}
                                </select>
                            </div>`;
                    break;
            }
            container.insertAdjacentHTML('beforeend', html);

            // Inicializa TomSelect en los nuevos selects de filtro
            setTimeout(() => {
                container.querySelectorAll('.filtro-select').forEach(sel => {
                    if (!sel.tomselect) new TomSelect(sel, { create: false, maxOptions: 200 });
                });
            }, 10);

            this.value = '';
        });

        // Intercepta el submit en modo edición y usa AJAX para actualizar
        document.querySelector('form').addEventListener('submit', function(e) {
            if (document.getElementById('modo_edicion').value === "1") {
                e.preventDefault();

                const form = this;
                const ticket = document.querySelector('input[name="numero_ticket"]').value.trim();
                const url = `/requerimientos/${encodeURIComponent(ticket)}`;
                const formData = new FormData(form);
                formData.append('_method', 'PUT');

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message || 'Actualización exitosa');
                        window.location.href = "{{ route('requerimientos.create') }}";
                    } else {
                        alert(data.message || 'Error al actualizar');
                    }
                })
                .catch(() => alert('Error al actualizar el requerimiento.'));
            }
        });
    });

    // Cierra el modal correctamente al hacer clic en "Cancelar"
    function cerrarModal() {
        document.getElementById('modalSolicitante').classList.add('hidden');
        document.getElementById('nuevoSolicitante').value = '';
        window._callbackSolicitante = null;
    }

    // Guarda el nuevo solicitante vía fetch AJAX
    function guardarSolicitante() {
        const nombre = document.getElementById('nuevoSolicitante').value.trim();
        if (!nombre) return;

        fetch("{{ route('solicitantes.store') }}", {
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
                if (typeof window._callbackSolicitante === 'function') {
                    window._callbackSolicitante({ value: nombre, text: nombre });
                }
                cerrarModal();
            } else {
                alert("Error al guardar el nuevo solicitante");
            }
        })
        .catch(() => alert("Error al conectar con el servidor"));
    }

    let modoEdicion = false;

    document.getElementById('btn-editar').addEventListener('click', function () {
        modoEdicion = true;
        document.querySelectorAll('form input, form select, form textarea').forEach(el => {
            if (el.name !== 'numero_ticket') {
                el.disabled = true;
                if (el.tomselect) el.tomselect.disable();
            } else {
                el.disabled = false;
            }
        });
        document.querySelector('input[name="numero_ticket"]').focus();
    });

    // Al presionar Enter en número de ticket, busca el requerimiento
    document.querySelector('input[name="numero_ticket"]').addEventListener('keydown', function (e) {
        if (modoEdicion && e.key === 'Enter') {
            e.preventDefault();
            const ticket = this.value.trim();
            if (!ticket) return;

            fetch(`/api/requerimientos/${encodeURIComponent(ticket)}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.existe) {
                        Object.entries(data.requerimiento).forEach(([name, value]) => {
                            const el = document.querySelector(`[name="${name}"]`);
                            if (el) {
                                if (el.tagName === 'SELECT' && el.tomselect) {
                                    el.tomselect.setValue(value);
                                } else {
                                    el.value = value;
                                }
                            }
                        });
                        document.querySelectorAll('form input, form select, form textarea').forEach(el => {
                            if (el.name !== 'numero_ticket') {
                                el.disabled = false;
                                if (el.tomselect) el.tomselect.enable();
                            }
                        });

                        document.getElementById('modo_edicion').value = "1";
                        const form = document.querySelector('form');
                        form.action = `/requerimientos/${ticket}`;
                        form.method = "POST";
                        if (!document.querySelector('input[name="_method"]')) {
                            const methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            methodInput.value = 'PUT';
                            form.appendChild(methodInput);
                        } else {
                            document.querySelector('input[name="_method"]').value = 'PUT';
                        }

                    } else {
                        alert('No existe el requerimiento con ese número de ticket.');
                    }
                })
                .catch(() => alert('Error al buscar el requerimiento.'));
        }
    });
</script>
<script>
function pedirTicketEditar() {
    const ticket = prompt('Ingrese el número de ticket a editar:');
    if (ticket && ticket.trim() !== '') {
        window.location.href = '/requerimientos/' + encodeURIComponent(ticket.trim()) + '/edit';
    }
}
</script>
</body>
</html>
