<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incidente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use Carbon\Carbon;

class IncidenteController extends Controller
{
    public function create()
    {
        $procesos = DB::table('procesos_mantenimiento')
        ->select('id', 'schedulix_id', 'trabajo', 'ruta_completa', 'responsable_escalamiento', 'requiere_solucion_inmediata')
        ->get();
        $estados = DB::table('estado_incidentes')->get();
        $negocios = DB::table('negocio_incidentes')->get();
        $ambientes = DB::table('ambiente_incidentes')->get();
        $capas = DB::table('capa_incidentes')->get();
        $servidores = DB::table('servidor_incidentes')->get();
        $eventos = DB::table('evento_incidentes')->get();
        $acciones = DB::table('accion_incidentes')->get();
        $escalados = DB::table('escalado_incidentes')->get();

        $incidentesHoy = DB::table('incidentes')
        ->join('estado_incidentes', 'incidentes.estado_incidente_id', '=', 'estado_incidentes.id')
        ->whereDate('incidentes.created_at', now())
        ->where('estado_incidentes.nombre', '!=', 'Cerrado')
        ->select('incidentes.*', 'estado_incidentes.nombre as estado_nombre')
        ->get();

        return view('incidentes.create', compact(
            'procesos', 'estados', 'negocios', 'ambientes', 'capas', 'servidores', 'eventos', 'acciones', 'escalados', 'incidentesHoy'
        ));
    }

    public function store(Request $request)
{
    $request->validate([
        'proceso_id'                => 'required|exists:procesos_mantenimiento,id',
        'negocio_incidente_id'      => 'required|exists:negocio_incidentes,id',
        'ambiente_incidente_id'     => 'required|exists:ambiente_incidentes,id',
        'capa_incidente_id'         => 'required|exists:capa_incidentes,id',
        'servidor_incidente_id'     => 'required|exists:servidor_incidentes,id',
        'evento_incidente_id'       => 'required|exists:evento_incidentes,id',
        'accion_incidente_id'       => 'required|exists:accion_incidentes,id',
        'escalado_incidente_id'     => 'required|exists:escalado_incidentes,id',
        'seguimiento'               => 'nullable|string',
        'estado_incidente_id'       => 'required|exists:estado_incidentes,id',
        'descripcion_evento'        => 'nullable|string',
        'solucion'                  => 'nullable|string',
        'observaciones'             => 'nullable|string',
        'inicio'                    => 'nullable|date',
        'registro'                  => 'nullable|date',
        'requerimiento'             => 'nullable|string|max:255',
    ]);

    // Formatear seguimiento inicial si existe
    $seguimientoInicial = '';
    if ($request->filled('seguimiento')) {
        $fecha = now()->format('d/m/Y');
        $usuario = Auth::user()->name;
        $seguimientoInicial = "{$fecha} - {$usuario}: {$request->seguimiento}";
    }

    $incidente = new Incidente();
    $incidente->proceso_id                = $request->proceso_id;
    $incidente->negocio_incidente_id      = $request->negocio_incidente_id;
    $incidente->ambiente_incidente_id     = $request->ambiente_incidente_id;
    $incidente->capa_incidente_id         = $request->capa_incidente_id;
    $incidente->servidor_incidente_id     = $request->servidor_incidente_id;
    $incidente->evento_incidente_id       = $request->evento_incidente_id;
    $incidente->accion_incidente_id       = $request->accion_incidente_id;
    $incidente->escalado_incidente_id     = $request->escalado_incidente_id;
    $incidente->seguimiento               = $seguimientoInicial;
    $incidente->estado_incidente_id       = $request->estado_incidente_id;
    $incidente->descripcion_evento        = $request->descripcion_evento;
    $incidente->solucion                  = $request->solucion;
    $incidente->observaciones             = $request->observaciones;
    $incidente->inicio                    = $request->filled('inicio') ? $request->inicio : now();
    $incidente->registro                  = $request->filled('registro') ? $request->registro : now();
    $incidente->requerimiento             = $request->requerimiento;
    $incidente->creado_por                = Auth::user()->name;
    $incidente->save();

    return redirect()->route('incidentes.create')->with('success', 'Incidente guardado correctamente');
}
    public function index()
    {
        // Vista de procesos activos (TODOS los que NO estén en estado EXITOSO, sin filtro de fecha)
        $incidentesAbiertos = DB::table('incidentes')
            ->join('procesos_mantenimiento', 'incidentes.proceso_id', '=', 'procesos_mantenimiento.id')
            ->join('estado_incidentes', 'incidentes.estado_incidente_id', '=', 'estado_incidentes.id')
            ->join('negocio_incidentes', 'incidentes.negocio_incidente_id', '=', 'negocio_incidentes.id')
            ->join('ambiente_incidentes', 'incidentes.ambiente_incidente_id', '=', 'ambiente_incidentes.id')
            ->leftJoin('capa_incidentes', 'incidentes.capa_incidente_id', '=', 'capa_incidentes.id')
            ->leftJoin('servidor_incidentes', 'incidentes.servidor_incidente_id', '=', 'servidor_incidentes.id')
            ->where('estado_incidentes.nombre', '!=', 'EXITOSO') // Solo excluir los EXITOSOS
            ->select(
                'incidentes.*',
                'procesos_mantenimiento.trabajo',
                'procesos_mantenimiento.schedulix_id',
                'estado_incidentes.nombre as estado_nombre',
                'negocio_incidentes.nombre as negocio_nombre',
                'ambiente_incidentes.nombre as ambiente_nombre',
                'capa_incidentes.nombre as capa_nombre',
                'servidor_incidentes.nombre as servidor_nombre'
            )
            ->orderBy('incidentes.created_at', 'desc')
            ->get();

        return view('incidentes.index', compact('incidentesAbiertos'));
    }

    public function historico(Request $request)
{
    // Vista histórica con filtros
    $query = DB::table('incidentes')
        ->join('procesos_mantenimiento', 'incidentes.proceso_id', '=', 'procesos_mantenimiento.id')
        ->join('estado_incidentes', 'incidentes.estado_incidente_id', '=', 'estado_incidentes.id')
        ->join('negocio_incidentes', 'incidentes.negocio_incidente_id', '=', 'negocio_incidentes.id')
        ->join('ambiente_incidentes', 'incidentes.ambiente_incidente_id', '=', 'ambiente_incidentes.id')
        ->leftJoin('capa_incidentes', 'incidentes.capa_incidente_id', '=', 'capa_incidentes.id')
        ->leftJoin('servidor_incidentes', 'incidentes.servidor_incidente_id', '=', 'servidor_incidentes.id')
        ->select(
            'incidentes.*',
            'procesos_mantenimiento.trabajo',
            'procesos_mantenimiento.schedulix_id',
            'estado_incidentes.nombre as estado_nombre',
            'negocio_incidentes.nombre as negocio_nombre',
            'ambiente_incidentes.nombre as ambiente_nombre',
            'capa_incidentes.nombre as capa_nombre',
            'servidor_incidentes.nombre as servidor_nombre'
        );

    // Aplicar filtros
    if ($request->filled('fecha_desde')) {
        $query->whereDate('incidentes.created_at', '>=', $request->fecha_desde);
    }
    
    if ($request->filled('fecha_hasta')) {
        $query->whereDate('incidentes.created_at', '<=', $request->fecha_hasta);
    }
    
    if ($request->filled('proceso')) {
        $query->where('procesos_mantenimiento.trabajo', 'like', '%' . $request->proceso . '%');
    }
    
    if ($request->filled('requerimiento')) {
        $query->where('incidentes.requerimiento', 'like', '%' . $request->requerimiento . '%');
    }
    
    if ($request->filled('estado')) {
        $query->where('incidentes.estado_incidente_id', $request->estado);
    }

    $incidentes = $query->orderBy('incidentes.created_at', 'desc')->paginate(50);
    
    // Datos para filtros
    $estados = DB::table('estado_incidentes')->get();
    $negocios = DB::table('negocio_incidentes')->get();
    $ambientes = DB::table('ambiente_incidentes')->get();
    $capas = DB::table('capa_incidentes')->get();
    $servidores = DB::table('servidor_incidentes')->get();

    return view('incidentes.historico', compact('incidentes', 'estados', 'negocios', 'ambientes', 'capas', 'servidores'));
}

    public function edit($id)
    {
        $incidente = Incidente::findOrFail($id);
        
        // Verificar que no esté en estado EXITOSO
        $estado = DB::table('estado_incidentes')->where('id', $incidente->estado_incidente_id)->first();
        if ($estado->nombre === 'EXITOSO') {
            return redirect()->route('incidentes.index')->with('error', 'No se puede editar un incidente exitoso.');
        }
        
        $estados = DB::table('estado_incidentes')->get();
        $negocios = DB::table('negocio_incidentes')->get();
        $ambientes = DB::table('ambiente_incidentes')->get();
        $capas = DB::table('capa_incidentes')->get();
        $servidores = DB::table('servidor_incidentes')->get();
        $eventos = DB::table('evento_incidentes')->get();
        $acciones = DB::table('accion_incidentes')->get();
        $escalados = DB::table('escalado_incidentes')->get();
        
        $proceso = DB::table('procesos_mantenimiento')->where('id', $incidente->proceso_id)->first();
        
        return view('incidentes.edit', compact(
            'incidente', 'proceso', 'estados', 'negocios', 'ambientes', 'capas', 'servidores', 'eventos', 'acciones', 'escalados'
        ));
    }

    public function update(Request $request, $id)
{
    $incidente = Incidente::findOrFail($id);
    
    // Verificar que no esté en estado EXITOSO
    $estado = DB::table('estado_incidentes')->where('id', $incidente->estado_incidente_id)->first();
    if ($estado->nombre === 'EXITOSO') {
        return redirect()->route('incidentes.index')->with('error', 'No se puede actualizar un incidente exitoso.');
    }
    
    $request->validate([
        'negocio_incidente_id'      => 'required|exists:negocio_incidentes,id',
        'ambiente_incidente_id'     => 'required|exists:ambiente_incidentes,id',
        'capa_incidente_id'         => 'required|exists:capa_incidentes,id',
        'servidor_incidente_id'     => 'required|exists:servidor_incidentes,id',
        'evento_incidente_id'       => 'required|exists:evento_incidentes,id',
        'accion_incidente_id'       => 'required|exists:accion_incidentes,id',
        'escalado_incidente_id'     => 'required|exists:escalado_incidentes,id',
        'seguimiento'               => 'nullable|string',
        'estado_incidente_id'       => 'required|exists:estado_incidentes,id',
        'descripcion_evento'        => 'nullable|string',
        'solucion'                  => 'nullable|string',
        'observaciones'             => 'nullable|string',
        'requerimiento'             => 'nullable|string|max:255',
    ]);

    // Acumular seguimiento si se envía nuevo
    $seguimientoActual = $incidente->seguimiento ?? '';
    if ($request->filled('seguimiento')) {
        $fecha = now()->format('d/m/Y');
        $usuario = Auth::user()->name;
        $nuevoSeguimiento = "{$fecha} - {$usuario}: {$request->seguimiento}";
        
        if (!empty($seguimientoActual)) {
            $seguimientoActual .= "\n" . $nuevoSeguimiento;
        } else {
            $seguimientoActual = $nuevoSeguimiento;
        }
    }

    $incidente->update([
        'negocio_incidente_id'     => $request->negocio_incidente_id,
        'ambiente_incidente_id'    => $request->ambiente_incidente_id,
        'capa_incidente_id'        => $request->capa_incidente_id,
        'servidor_incidente_id'    => $request->servidor_incidente_id,
        'evento_incidente_id'      => $request->evento_incidente_id,
        'accion_incidente_id'      => $request->accion_incidente_id,
        'escalado_incidente_id'    => $request->escalado_incidente_id,
        'seguimiento'              => $seguimientoActual,
        'estado_incidente_id'      => $request->estado_incidente_id,
        'descripcion_evento'       => $request->descripcion_evento,
        'solucion'                 => $request->solucion,
        'observaciones'            => $request->observaciones,
        'requerimiento'            => $request->requerimiento,
        'actualizado_por'          => Auth::user()->name,
    ]);

    return redirect()->route('incidentes.index')->with('success', 'Incidente actualizado correctamente');
}

public function quickUpdate(Request $request, $id)
{
    // Actualización rápida desde la grilla
    $incidente = Incidente::findOrFail($id);
    
    $request->validate([
        'estado_incidente_id' => 'nullable|exists:estado_incidentes,id',
        'requerimiento' => 'nullable|string|max:255',
        'observaciones' => 'nullable|string',
        'seguimiento' => 'nullable|string',
    ]);

    $updateData = [];
    
    // Acumular seguimiento si se envía
    if ($request->filled('seguimiento')) {
        $seguimientoActual = $incidente->seguimiento ?? '';
        $fecha = now()->format('d/m/Y');
        $usuario = Auth::user()->name;
        $nuevoSeguimiento = "{$fecha} - {$usuario}: {$request->seguimiento}";
        
        if (!empty($seguimientoActual)) {
            $updateData['seguimiento'] = $seguimientoActual . "\n" . $nuevoSeguimiento;
        } else {
            $updateData['seguimiento'] = $nuevoSeguimiento;
        }
    }

    // Agregar otros campos si están presentes
    if ($request->filled('estado_incidente_id')) {
        $updateData['estado_incidente_id'] = $request->estado_incidente_id;
    }
    if ($request->filled('requerimiento')) {
        $updateData['requerimiento'] = $request->requerimiento;
    }
    if ($request->filled('observaciones')) {
        $updateData['observaciones'] = $request->observaciones;
    }
    
    $updateData['actualizado_por'] = Auth::user()->name;

    $incidente->update($updateData);

    return response()->json(['success' => true, 'message' => 'Incidente actualizado correctamente']);
}

    public function searchProceso(Request $request)
    {
        $q = $request->input('q');
        $procesos = DB::table('procesos_mantenimiento')
            ->select('id', 'schedulix_id', 'trabajo', 'ruta_completa', 'responsable_escalamiento', 'requiere_solucion_inmediata')
            ->where('trabajo', 'like', "%$q%")
            ->orWhere('schedulix_id', 'like', "%$q%")
            ->limit(20)
            ->get();

        return response()->json($procesos);
    }

    public function exportarExcel(Request $request)
    {
        $query = DB::table('incidentes')
            ->join('procesos_mantenimiento', 'incidentes.proceso_id', '=', 'procesos_mantenimiento.id')
            ->join('estado_incidentes', 'incidentes.estado_incidente_id', '=', 'estado_incidentes.id')
            ->join('negocio_incidentes', 'incidentes.negocio_incidente_id', '=', 'negocio_incidentes.id')
            ->join('ambiente_incidentes', 'incidentes.ambiente_incidente_id', '=', 'ambiente_incidentes.id')
            ->join('capa_incidentes', 'incidentes.capa_incidente_id', '=', 'capa_incidentes.id')
            ->join('servidor_incidentes', 'incidentes.servidor_incidente_id', '=', 'servidor_incidentes.id')
            ->join('evento_incidentes', 'incidentes.evento_incidente_id', '=', 'evento_incidentes.id')
            ->join('accion_incidentes', 'incidentes.accion_incidente_id', '=', 'accion_incidentes.id')
            ->join('escalado_incidentes', 'incidentes.escalado_incidente_id', '=', 'escalado_incidentes.id')
            ->select(
                'incidentes.*',
                'procesos_mantenimiento.trabajo',
                'procesos_mantenimiento.schedulix_id',
                'estado_incidentes.nombre as estado_nombre',
                'negocio_incidentes.nombre as negocio_nombre',
                'ambiente_incidentes.nombre as ambiente_nombre',
                'capa_incidentes.nombre as capa_nombre',
                'servidor_incidentes.nombre as servidor_nombre',
                'evento_incidentes.nombre as evento_nombre',
                'accion_incidentes.nombre as accion_nombre',
                'escalado_incidentes.nombre as escalado_nombre'
            );

        // Aplicar filtros
        if ($request->filled('fecha_desde')) {
            $query->whereDate('incidentes.created_at', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('incidentes.created_at', '<=', $request->fecha_hasta);
        }
        
        if ($request->filled('proceso')) {
            $query->where('procesos_mantenimiento.trabajo', 'like', '%' . $request->proceso . '%');
        }
        
        if ($request->filled('requerimiento')) {
            $query->where('incidentes.requerimiento', 'like', '%' . $request->requerimiento . '%');
        }
        
        if ($request->filled('estado')) {
            $query->where('incidentes.estado_incidente_id', $request->estado);
        }

        $incidentes = $query->orderBy('incidentes.id', 'asc')->get();

        $templatePath = storage_path('app/templates/Incidente Base.xlsx');
        $fechaHora = Carbon::now()->format('Ymd_His');
        $outputPath = storage_path("app/exports/Incidentes_{$fechaHora}.xlsx");

        if (!Storage::exists('exports')) {
            Storage::makeDirectory('exports');
        }

        $spreadsheet = IOFactory::load($templatePath);
        
        // Eliminar todos los defined names (named ranges, print areas, etc) para evitar conflictos
        $definedNames = $spreadsheet->getDefinedNames();
        foreach ($definedNames as $definedName) {
            $spreadsheet->removeDefinedName($definedName->getName());
        }
        
        $sheet = $spreadsheet->getActiveSheet();
        $row = 4;

        foreach ($incidentes as $inc) {
            $fillColor = ($row % 2 === 0) ? 'F2F2F2' : 'E6E6E6';

            $sheet->setCellValue("A{$row}", $inc->schedulix_id);                                          // A: ID Proceso
            $sheet->setCellValue("B{$row}", $inc->trabajo);                                               // B: Proceso
            $sheet->setCellValue("C{$row}", $inc->inicio ? Carbon::parse($inc->inicio)->format('d-m-Y H:i') : ''); // C: Inicio
            $sheet->setCellValue("D{$row}", $inc->fin ? Carbon::parse($inc->fin)->format('d-m-Y H:i') : '');       // D: Fin
            $sheet->setCellValue("E{$row}", $inc->requerimiento);                                         // E: Requerimiento
            $sheet->setCellValue("F{$row}", $inc->negocio_nombre);                                        // F: Negocio
            $sheet->setCellValue("G{$row}", $inc->ambiente_nombre);                                       // G: Ambiente
            $sheet->setCellValue("H{$row}", $inc->capa_nombre);                                           // H: Capa
            $sheet->setCellValue("I{$row}", $inc->servidor_nombre);                                       // I: Servidor
            $sheet->setCellValue("J{$row}", $inc->evento_nombre);                                         // J: Evento
            $sheet->setCellValue("K{$row}", $inc->accion_nombre);                                         // K: Acción
            $sheet->setCellValue("L{$row}", $inc->escalado_nombre);                                       // L: Escalado a
            // M: Seguimiento -> mantener formato existente pero resaltar fecha y nombre en negrita
            if (!empty($inc->seguimiento)) {
                $lines = preg_split('/\r?\n/', $inc->seguimiento);
                $rich = new RichText();
                $first = true;
                foreach ($lines as $ln) {
                    if (!$first) {
                        $rich->createTextRun("\n");
                    }
                    $first = false;

                    // Intentar separar fecha, nombre y texto: formato esperado "dd/mm/YYYY - Nombre: comentario"
                    if (preg_match('/^(\d{1,2}\/\d{1,2}\/\d{4})\s*-\s*([^:]+):\s*(.*)$/', $ln, $m)) {
                        $fecha = $m[1] . ' - ';
                        $nombre = $m[2];
                        $texto = $m[3];

                        $runFecha = $rich->createTextRun($fecha);
                        $runFecha->getFont()->setBold(true);

                        $runNombre = $rich->createTextRun($nombre . ': ');
                        $runNombre->getFont()->setBold(true);

                        $rich->createTextRun($texto);
                    } else {
                        // Si no coincide, escribir la línea completa
                        $rich->createTextRun($ln);
                    }
                }
                $sheet->setCellValue("M{$row}", $rich);
            } else {
                $sheet->setCellValue("M{$row}", $inc->seguimiento);
            }

            $sheet->setCellValue("N{$row}", $inc->estado_nombre);                                         // N: Estado
            $sheet->setCellValue("O{$row}", $inc->descripcion_evento);                                    // O: Descripción del evento

            // P: Solución -> incluir fecha y usuario (updated_at/actualizado_por) y poner fecha+nombre en negrita
            if (!empty($inc->solucion)) {
                $fechaSol = Carbon::parse($inc->updated_at ?? $inc->created_at)->format('d/m/Y');
                $usuarioSol = $inc->actualizado_por ?? ($inc->creado_por ?? 'N/A');
                $richSol = new RichText();
                $runFS = $richSol->createTextRun($fechaSol . ' - ');
                $runFS->getFont()->setBold(true);
                $runUser = $richSol->createTextRun($usuarioSol . ': ');
                $runUser->getFont()->setBold(true);
                $richSol->createTextRun($inc->solucion);
                $sheet->setCellValue("P{$row}", $richSol);
            } else {
                $sheet->setCellValue("P{$row}", $inc->solucion);
            }

            // Q: Observaciones -> incluir fecha y usuario (updated_at/actualizado_por) y poner fecha+nombre en negrita
            if (!empty($inc->observaciones)) {
                $fechaObs = Carbon::parse($inc->updated_at ?? $inc->created_at)->format('d/m/Y');
                $usuarioObs = $inc->actualizado_por ?? ($inc->creado_por ?? 'N/A');
                $richObs = new RichText();
                $runFO = $richObs->createTextRun($fechaObs . ' - ');
                $runFO->getFont()->setBold(true);
                $runUserO = $richObs->createTextRun($usuarioObs . ': ');
                $runUserO->getFont()->setBold(true);
                $richObs->createTextRun($inc->observaciones);
                $sheet->setCellValue("Q{$row}", $richObs);
            } else {
                $sheet->setCellValue("Q{$row}", $inc->observaciones);
            }

            // R: ID
            $sheet->setCellValue("R{$row}", $inc->id);                                                    // R: ID

            // S: Registro -> incluir rol si el usuario es operador y poner rol (y nombre) en negrita
            $fechaRegistro = Carbon::parse($inc->registro ?? $inc->created_at)
                ->locale('es')->isoFormat('D MMMM YYYY H:mm');
            $creador = $inc->creado_por ?? 'N/A';
            // Intentar obtener area/rol desde tabla users y normalizar etiquetas
            $userRow = DB::table('users')->where('name', $creador)->first();
            $roleLabel = null;
            if ($userRow && !empty($userRow->area)) {
                $rawArea = strtolower($userRow->area);
                if (str_contains($rawArea, 'analista')) {
                    $roleLabel = 'Analista de Sistemas Integrales';
                } elseif (str_contains($rawArea, 'operador')) {
                    $roleLabel = 'Operador de Sistemas TI';
                } else {
                    // fallback a lo que esté en la base
                    $roleLabel = $userRow->area;
                }
            }

            $richReg = new RichText();
            $richReg->createTextRun($fechaRegistro . ' | Creado por: ');
            if ($creador !== 'N/A') {
                $runName = $richReg->createTextRun($creador);
                $runName->getFont()->setBold(true);
                if ($roleLabel) {
                    $richReg->createTextRun('-');
                    $runRole = $richReg->createTextRun($roleLabel);
                    $runRole->getFont()->setBold(true);
                    $richReg->createTextRun('.');
                }
            }
            $sheet->setCellValue("S{$row}", $richReg);

            // Estilos
            foreach (range('A', 'S') as $col) {
                $cell = "{$col}{$row}";
                $sheet->getStyle($cell)->getFont()->setName('Calibri')->setSize(11);
                $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB($fillColor);
                $sheet->getStyle($cell)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()->setRGB('FFFFFF');
                $sheet->getStyle($cell)->getAlignment()
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            }

            // Alineaciones específicas
            foreach (['A','B','C','D','E','F','G','H','I','J','K','L','N'] as $col) {
                $sheet->getStyle("{$col}{$row}")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            }
            
            $sheet->getStyle("R{$row}")->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Ajustes de texto en columnas largas
            foreach (['M', 'O', 'P', 'Q', 'S'] as $col) {
                $sheet->getStyle("{$col}{$row}")->getAlignment()->setWrapText(true);
            }

            // Altura automática
            $sheet->getRowDimension($row)->setRowHeight(-1);

            $row++;
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($outputPath);

        return response()->download($outputPath)->deleteFileAfterSend(true);
    }
}