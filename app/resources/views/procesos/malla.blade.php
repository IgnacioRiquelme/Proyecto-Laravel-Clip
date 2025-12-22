{{-- resources/views/procesos/malla.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-8 rounded-lg shadow-lg mt-8">
    <h1 class="text-2xl font-bold text-indigo-600 mb-6">Malla de Procesos</h1>
    <form method="POST" action="{{ route('procesos.malla.actualizar') }}" class="space-y-6">
        @csrf
        <div>
            <label class="block font-semibold mb-1">Fecha y hora de inicio</label>
            <input type="datetime-local" name="inicio" class="w-full border border-gray-300 rounded p-2" value="{{ old('inicio', now()->format('Y-m-d\TH:i')) }}">
        </div>
        <!-- Aquí irían los demás campos de la malla -->
        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">Guardar</button>
        </div>
    </form>
</div>
@endsection
