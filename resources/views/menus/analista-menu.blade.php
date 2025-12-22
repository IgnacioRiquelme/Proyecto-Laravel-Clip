@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center relative overflow-hidden">
    <!-- Fondo de imagen animado -->
    <img src="/gif/video.gif" alt="Fondo animado" class="fixed inset-0 w-full h-full object-cover z-0" />
    <!-- Recuadro de menú -->
    <div class="relative z-20 flex items-center justify-center w-full min-h-screen px-4">
        <div class="relative bg-white rounded-[48px] shadow-2xl w-full max-w-xl mx-auto flex flex-col items-center pt-20 pb-12 px-8">
            <!-- Icono flotante -->
            <div class="absolute -top-16 left-1/2 -translate-x-1/2 bg-blue-700 rounded-full p-6 flex items-center justify-center shadow-lg">
                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-center mb-8 mt-2">Menú Analista</h1>

            <div class="flex flex-col gap-4 w-full max-w-sm">
                <a href="{{ route('requerimientos.create') }}" class="bg-indigo-500 hover:bg-indigo-600 text-white py-3 px-6 rounded-md text-center font-medium shadow">Nuevo Requerimiento</a>
                <a href="{{ route('incidentes.create') }}" class="bg-indigo-500 hover:bg-indigo-600 text-white py-3 px-6 rounded-md text-center font-medium shadow">Nuevo Incidente</a>
                <a href="{{ route('reportes-veeam.create') }}" class="bg-indigo-500 hover:bg-indigo-600 text-white py-3 px-6 rounded-md text-center font-medium shadow">Reporte Veeam y TCM</a>
                <a href="{{ route('procesos.malla') }}" class="bg-indigo-500 hover:bg-indigo-600 text-white py-3 px-6 rounded-md text-center font-medium shadow">Bitácora de Procesos</a>
                <a href="{{ route('cierre-dia.index') }}" class="bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-md text-center font-medium shadow">📊 Cierre de Día</a>
                <a href="{{ route('carga.requerimientos.form') }}" class="bg-indigo-500 hover:bg-indigo-600 text-white py-3 px-6 rounded-md text-center font-medium shadow">📥 Cargar Requerimientos (Excel)</a>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-md text-center font-medium mt-4 shadow">Salir</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </div>
        </div>
    </div>
</div>
@endsection