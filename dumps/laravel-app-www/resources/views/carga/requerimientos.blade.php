@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-100 min-h-screen">
    <h1 class="text-2xl font-bold text-indigo-700 mb-6 text-center">Carga Masiva de Requerimientos</h1>

    @if (session('resumen'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <h2 class="font-semibold text-lg">✅ Resultado de la Carga</h2>
            <p><strong>Total de filas procesadas:</strong> {{ session('resumen.total') }}</p>
            <p><strong>Insertados correctamente:</strong> {{ session('resumen.insertados') }}</p>
            <p><strong>Fallidos:</strong> {{ session('resumen.fallidos') }}</p>

            @if (!empty(session('resumen.errores')))
                <div class="mt-4">
                    <h3 class="font-semibold">Errores encontrados:</h3>
                    <ul class="list-disc list-inside text-sm text-red-600">
                        @foreach (session('resumen.errores') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    <form action="{{ route('carga.requerimientos.importar') }}" method="POST" enctype="multipart/form-data"
      class="bg-white p-6 rounded shadow-md max-w-xl mx-auto">
    @csrf

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Seleccionar archivo Excel</label>
        <input type="file" name="archivo" accept=".xlsx" required
               class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 text-sm">
    </div>

    <div class="flex flex-col items-center space-y-3 mt-6">
        <button type="submit"
            class="bg-indigo-500 hover:bg-indigo-600 text-white py-2 px-4 rounded text-center font-medium shadow-md border border-indigo-700">
            ⬆️ Cargar Archivo
        </button>

        <a href="{{ route('menu.analista') }}"
            class="bg-indigo-500 hover:bg-indigo-600 text-white py-2 px-4 rounded text-center font-medium shadow-md border border-indigo-700">
            🔙 Volver al Menú Principal
        </a>
    </div>
</form>
</div>
@endsection
