<!-- resources/views/requerimientos/edit.blade.php -->

@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-10 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-6 text-indigo-600">Editar Requerimiento</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('requerimientos.update', $requerimiento->numero_ticket) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Número de Ticket</label>
                    <input type="text" name="numero_ticket" value="{{ old('numero_ticket', $requerimiento->numero_ticket) }}"
                           class="w-full border rounded px-3 py-2" readonly>
                </div>

                <div>
                    <label class="block font-medium">Solicitante</label>
                    <input type="text" name="solicitante" value="{{ old('solicitante', $requerimiento->solicitante) }}"
                           class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-medium">Requerimiento</label>
                    <input type="text" name="requerimiento" value="{{ old('requerimiento', $requerimiento->requerimiento) }}"
                           class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-medium">Negocio</label>
                    <input type="text" name="negocio" value="{{ old('negocio', $requerimiento->negocio) }}"
                           class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-medium">Ambiente</label>
                    <input type="text" name="ambiente" value="{{ old('ambiente', $requerimiento->ambiente) }}"
                           class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-medium">Capa</label>
                    <input type="text" name="capa" value="{{ old('capa', $requerimiento->capa) }}"
                           class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-medium">Servidor</label>
                    <input type="text" name="servidor" value="{{ old('servidor', $requerimiento->servidor) }}"
                           class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-medium">Estado</label>
                    <input type="text" name="estado" value="{{ old('estado', $requerimiento->estado) }}"
                           class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-medium">Tipo de Solicitud</label>
                    <input type="text" name="tipo_solicitud" value="{{ old('tipo_solicitud', $requerimiento->tipo_solicitud) }}"
                           class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-medium">Tipo de Pase</label>
                    <input type="text" name="tipo_pase" value="{{ old('tipo_pase', $requerimiento->tipo_pase) }}"
                           class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-medium">IC</label>
                    <input type="text" name="ic" value="{{ old('ic', $requerimiento->ic) }}"
                           class="w-full border rounded px-3 py-2">
                </div>

                <div class="col-span-2">
                    <label class="block font-medium">Observaciones</label>
                    <textarea name="observaciones" rows="3"
                              class="w-full border rounded px-3 py-2">{{ old('observaciones', $requerimiento->observaciones) }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <a href="{{ route('requerimientos.create') }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Cancelar
                </a>
                <button type="submit"
                        class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
