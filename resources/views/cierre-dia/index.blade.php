@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">📊 Cierre de Día</h1>
                <a href="/menu-analista" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg shadow transition">
                    Volver al Menú
                </a>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">ℹ️ Información del Cierre</h3>
                <p class="text-sm text-blue-700">
                    El cierre de día genera un reporte Excel con 4 pestañas:
                </p>
                <ul class="list-disc list-inside text-sm text-blue-700 mt-2 space-y-1">
                    <li><strong>Bitácora de Procesos:</strong> Procesos ejecutados desde 09:00 del día anterior hasta 09:00 de hoy</li>
                    <li><strong>Requerimientos:</strong> Pases a Producción registrados en las últimas 24 horas</li>
                    <li><strong>Incidentes:</strong> Incidentes pendientes + resueltos en el día</li>
                    <li><strong>Respaldos Veeam:</strong> Tickets pendientes + informativos del día</li>
                </ul>
            </div>

            <div class="flex justify-center">
                <form action="{{ route('cierre-dia.generar') }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-8 rounded-lg shadow-lg transition transform hover:scale-105">
                        🚀 Generar Cierre de Día
                    </button>
                </form>
            </div>

            <div class="mt-8 p-4 bg-yellow-50 border border-yellow-300 rounded-lg">
                <p class="text-sm text-yellow-800">
                    <strong>Nota:</strong> El cierre marca automáticamente los registros correspondientes en la base de datos. Los incidentes y reportes resueltos aparecerán por última vez en este cierre.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
