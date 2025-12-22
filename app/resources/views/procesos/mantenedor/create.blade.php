{{-- filepath: resources/views/procesos/mantenedor/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow-lg mt-8">
    <h1 class="text-2xl font-bold text-indigo-600 mb-6">Crear Nuevo Proceso</h1>
    <form action="{{ route('procesos.mantenedor.store') }}" method="POST" class="space-y-6">
        @csrf
        <div>
            <label class="block font-semibold mb-1">Trabajo *</label>
            <input type="text" name="trabajo" class="w-full border border-gray-300 rounded p-2" required>
        </div>
        <div>
            <label class="block font-semibold mb-1">Schedulix ID (opcional)</label>
            <input type="text" name="schedulix_id" class="w-full border border-gray-300 rounded p-2">
        </div>
        <div class="flex justify-between">
            <a href="{{ route('procesos.mantenedor.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Volver</a>
            <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">Guardar</button>
        </div>
    </form>
</div>
@endsection