<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequerimientoController;
use App\Http\Controllers\MallaController;
use App\Http\Middleware\EnsureDatabaseConnection;
use App\Http\Controllers\CargaRequerimientosController;
use App\Http\Controllers\IncidenteController;
use App\Http\Controllers\EstadoIncidenteController;
use App\Http\Controllers\NegocioIncidenteController;
use App\Http\Controllers\AmbienteIncidenteController;
use App\Http\Controllers\CapaIncidenteController;
use App\Http\Controllers\ServidorIncidenteController;
use App\Http\Controllers\EventoIncidenteController;
use App\Http\Controllers\AccionIncidenteController;
use App\Http\Controllers\EscaladoIncidenteController;
use App\Http\Controllers\TipoRequerimientoIncidenteController;
use App\Http\Controllers\ProcesoMantenedorController;
use App\Http\Controllers\ProcesosConversorController;
use App\Http\Controllers\ReporteVeeamController;

// Ruta para test de conexión (ping DB)
Route::get('/ping-db', function () {
    try {
        \Illuminate\Support\Facades\DB::select('SELECT 1');
        return response()->json(['status' => 'ok']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});

Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas protegidas por autenticación
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Menús por rol
    Route::get('/menu-analista', fn () => view('menus.analista-menu'))->name('menu.analista');
    Route::get('/menu-operador', fn () => view('menus.operador-menu'))->name('menu.operador');

    // === REQUERIMIENTOS ===
    Route::get('/requerimientos/create', [RequerimientoController::class, 'create'])->name('requerimientos.create');
    Route::get('/requerimientos/{ticket}/edit', [RequerimientoController::class, 'edit'])->name('requerimientos.edit');
    Route::post('/requerimientos', [RequerimientoController::class, 'store'])->name('requerimientos.store');
    Route::put('/requerimientos/{ticket}', [RequerimientoController::class, 'update'])->name('requerimientos.update');
    Route::post('/requerimientos/filtrados', [RequerimientoController::class, 'filtrados'])->name('requerimientos.filtrados');
    Route::get('/requerimientos/filtrados', [RequerimientoController::class, 'filtrados'])->name('requerimientos.filtrados');
    Route::get('/requerimientos/dia', [RequerimientoController::class, 'vistaRequerimientosDelDia'])->name('requerimientos.dia');
    Route::post('/requerimientos/exportar', [RequerimientoController::class, 'exportarExcel'])->name('requerimientos.exportar');
    Route::post('/solicitantes', [RequerimientoController::class, 'storeSolicitante'])->name('solicitantes.store');
    Route::get('/carga/requerimientos', [CargaRequerimientosController::class, 'form'])->name('carga.requerimientos.form');
    Route::post('/carga/requerimientos', [CargaRequerimientosController::class, 'importar'])->name('carga.requerimientos.importar');

    // === INCIDENTES ===
    Route::prefix('incidentes')->name('incidentes.')->group(function () {
        Route::get('/', [IncidenteController::class, 'index'])->name('index');
        Route::get('/create', [IncidenteController::class, 'create'])->name('create');
        Route::post('/', [IncidenteController::class, 'store'])->name('store');
        Route::get('/historico', [IncidenteController::class, 'historico'])->name('historico');
        Route::get('/{id}/edit', [IncidenteController::class, 'edit'])->name('edit');
        Route::put('/{id}', [IncidenteController::class, 'update'])->name('update');
        Route::patch('/{id}/quick-update', [IncidenteController::class, 'quickUpdate'])->name('quickUpdate');
        Route::get('/search-proceso', [IncidenteController::class, 'searchProceso'])->name('searchProceso');
        Route::post('/exportar', [IncidenteController::class, 'exportarExcel'])->name('exportar');
    });
    
    // === ACTUALIZACIONES INCIDENTES COMBOBOX ===
    Route::post('/estados_incidentes', [EstadoIncidenteController::class, 'store'])->name('estados_incidentes.store');
    Route::post('/negocio_incidentes', [NegocioIncidenteController::class, 'store'])->name('negocio_incidentes.store');
    Route::post('/ambiente_incidentes', [AmbienteIncidenteController::class, 'store'])->name('ambiente_incidentes.store');
    Route::post('/capa_incidentes', [CapaIncidenteController::class, 'store'])->name('capa_incidentes.store');
    Route::post('/servidor_incidentes', [ServidorIncidenteController::class, 'store'])->name('servidor_incidentes.store');
    Route::post('/evento_incidentes', [EventoIncidenteController::class, 'store'])->name('evento_incidentes.store');
    Route::post('/accion_incidentes', [AccionIncidenteController::class, 'store'])->name('accion_incidentes.store');
    Route::post('/escalado_incidentes', [EscaladoIncidenteController::class, 'store'])->name('escalado_incidentes.store');
    Route::post('/tipo_requerimiento_incidentes', [TipoRequerimientoIncidenteController::class, 'store'])->name('tipo_requerimiento_incidentes.store');

    // === CONVERSOR DE PROCESOS ===
    Route::get('/procesos/conversor', [ProcesosConversorController::class, 'showForm'])->name('procesos.conversor.form');
    Route::post('/procesos/conversor', [ProcesosConversorController::class, 'convertir'])->name('procesos.conversor.convertir');
    Route::post('/procesos/conversor/informe-comparativo', [ProcesosConversorController::class, 'descargarInformeComparativo'])->name('procesos.conversor.informe-comparativo');

    // === MANTENEDOR PROCESOS ===
    Route::prefix('procesos/mantenedor')->name('procesos.mantenedor.')->group(function () {
        Route::get('/', [ProcesoMantenedorController::class, 'index'])->name('index');
        Route::get('/crear', [ProcesoMantenedorController::class, 'create'])->name('create');
        Route::post('/', [ProcesoMantenedorController::class, 'store'])->name('store');
        Route::get('/{id}/editar', [ProcesoMantenedorController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ProcesoMantenedorController::class, 'update'])->name('update');
    });

    // === MALLA DE PROCESOS ===
    Route::prefix('procesos')->group(function () {
        Route::get('/malla', [MallaController::class, 'index'])->name('procesos.malla');
        Route::post('/actualizar/{id}', [MallaController::class, 'actualizar'])->name('procesos.actualizar');
        Route::post('/cerrar-dia', [MallaController::class, 'cerrarDia'])->name('procesos.cerrar-dia');
        Route::get('/exportar/{fecha}', [MallaController::class, 'exportar'])->name('procesos.exportar');
        Route::get('/historico/{fecha}', [MallaController::class, 'historico'])->name('procesos.historico');
        Route::get('/configurar-fecha-inicial', [MallaController::class, 'configurarFechaInicial'])->name('procesos.configurar-fecha-inicial');
        Route::post('/guardar-fecha-inicial', [MallaController::class, 'guardarFechaInicial'])->name('procesos.guardar-fecha-inicial');
        Route::post('/resetear-fecha', [MallaController::class, 'resetearFecha'])->name('procesos.resetear-fecha');
    });

    // Reportes Veeam
    // Reportes Veeam
    Route::prefix('reportes-veeam')->name('reportes-veeam.')->group(function () {
        Route::get('/', [ReporteVeeamController::class, 'index'])->name('index');
        Route::get('/historico', [ReporteVeeamController::class, 'historico'])->name('historico');
        Route::get('/create', [ReporteVeeamController::class, 'create'])->name('create');
        Route::post('/', [ReporteVeeamController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ReporteVeeamController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ReporteVeeamController::class, 'update'])->name('update');
        Route::delete('/{id}', [ReporteVeeamController::class, 'destroy'])->name('destroy');
        Route::get('/dia', [ReporteVeeamController::class, 'dia'])->name('dia');
        Route::get('/pendientes', [ReporteVeeamController::class, 'pendientes'])->name('pendientes');
        Route::post('/filtrados', [ReporteVeeamController::class, 'filtrados'])->name('filtrados');
        Route::post('/exportar', [ReporteVeeamController::class, 'exportarExcel'])->name('exportar');
    });

    // Endpoints para guardar nuevos valores desde los combobox
    Route::post('/veeam/guardar-estado', [ReporteVeeamController::class, 'guardarEstado'])->name('veeam.guardar-estado');
    Route::post('/veeam/guardar-job', [ReporteVeeamController::class, 'guardarJob'])->name('veeam.guardar-job');
    Route::post('/veeam/guardar-estado-ticket', [ReporteVeeamController::class, 'guardarEstadoTicket'])->name('veeam.guardar-estado-ticket');

    // API para edición de requerimientos
    Route::get('/api/requerimientos/{ticket}', function ($ticket) {
        $req = \App\Models\Requerimiento::where('numero_ticket', $ticket)->first();
        return $req
            ? ['existe' => true, 'requerimiento' => collect($req)->only([
                'solicitante', 'requerimiento', 'negocio', 'ambiente',
                'capa', 'servidor', 'estado', 'tipo_solicitud',
                'tipo_pase', 'ic', 'observaciones'
            ])]
            : ['existe' => false];
    })->name('requerimientos.api.buscar');

    // === CIERRE DE DÍA ===
    Route::prefix('cierre-dia')->name('cierre-dia.')->group(function () {
        Route::get('/', [\App\Http\Controllers\CierreDiaController::class, 'index'])->name('index');
        Route::post('/generar', [\App\Http\Controllers\CierreDiaController::class, 'generarCierre'])->name('generar');
    });
});

require __DIR__.'/auth.php';