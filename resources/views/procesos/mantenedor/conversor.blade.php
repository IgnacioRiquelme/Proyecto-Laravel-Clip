@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center relative overflow-hidden">
    <img src="/gif/video.gif" alt="Fondo animado" class="fixed inset-0 w-full h-full object-cover z-0" />
    <div class="relative z-20 flex items-center justify-center w-full min-h-screen px-4">
        <div class="relative bg-white rounded-[48px] shadow-2xl w-full max-w-xl mx-auto flex flex-col items-center pt-20 pb-12 px-8">
            <h1 class="text-3xl font-bold text-center mb-8 mt-2">Consultor de Procesos</h1>
            
            <!-- Formulario para convertir archivo -->
            <form method="POST" action="{{ route('procesos.conversor.convertir') }}" enctype="multipart/form-data" class="w-full flex flex-col gap-4 mb-6">
                @csrf
                <label class="font-semibold">Selecciona el archivo de rutas (.txt o .csv):</label>
                <input type="file" name="archivo" accept=".txt,.csv" required class="w-full border border-gray-300 rounded p-2" />
                <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white py-2 rounded font-medium">Convertir</button>
            </form>

            <!-- Formulario para descargar informe comparativo -->
            <form method="POST" action="{{ route('procesos.conversor.informe-comparativo') }}" enctype="multipart/form-data" class="w-full flex flex-col gap-4 mb-6">
                @csrf
                <label class="font-semibold">Descargar informe comparativo (.txt o .csv):</label>
                <input type="file" name="archivo" accept=".txt,.csv" required class="w-full border border-gray-300 rounded p-2" />
                <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white py-2 rounded font-medium">Descargar Informe Comparativo</button>
            </form>

            @if(isset($resultado))
                <div class="w-full bg-gray-100 rounded p-4 mt-4">
                    <h2 class="font-semibold mb-2">Archivo procesado:</h2>
                    <pre class="text-xs text-left whitespace-pre-wrap">{{ implode("\n", $resultado) }}</pre>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection