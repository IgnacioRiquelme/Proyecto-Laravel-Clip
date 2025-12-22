<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use setasign\Fpdi\Tcpdf\Fpdi;

class CierreDiaController extends Controller
{
    public function index()
    {
        return view('cierre-dia.index');
    }

    public function generarCierre(Request $request)
    {
        \Log::info('Iniciando generación de Cierre de Día');
        
        // Fecha del cierre: día ANTERIOR (si hoy es 15/12, cierre del 14/12)
        $fechaCierre = Carbon::yesterday()->toDateString();
        \Log::info('Fecha cierre: ' . $fechaCierre);
        $inicioRango = Carbon::yesterday()->setTime(9, 0, 0);
        $finRango = Carbon::today()->setTime(9, 0, 0);

        // === MARCAR REGISTROS EN BD (IMPORTANTE: mantener lógica de marcado) ===

        // REQUERIMIENTOS: Marcar los del día anterior
        DB::table('requerimientos')
            ->whereNull('fecha_cierre_dia')
            ->whereDate('created_at', $fechaCierre)
            ->update(['fecha_cierre_dia' => $fechaCierre]);

        // INCIDENTES: Marcar los que pasaron a Exitoso
        $estadoExitoso = DB::table('estado_incidentes')->where('nombre', 'EXITOSO')->value('id');
        DB::table('incidentes')
            ->where('estado_incidente_id', $estadoExitoso)
            ->whereNull('fecha_cambio_a_exitoso')
            ->update(['fecha_cambio_a_exitoso' => now()]);

        // VEEAM: Marcar informativos
        DB::table('reportes_veeam')
            ->where('estado', 'Warning')
            ->where('es_informativo', false)
            ->whereNull('fecha_cierre_informativo')
            ->whereDate('fecha_inicio', $fechaCierre)
            ->update([
                'es_informativo' => true,
                'fecha_cierre_informativo' => $fechaCierre
            ]);

        // VEEAM: Marcar resueltos
        DB::table('reportes_veeam')
            ->where('estado_ticket', 'Resuelto')
            ->whereNull('fecha_cambio_a_resuelto')
            ->update(['fecha_cambio_a_resuelto' => now()]);

        \Log::info('Marcado de registros completado');

        // === GENERAR 4 EXCEL TEMPORALES CON LÓGICA DE FILTRADO ===

        $tempDir = storage_path('app/temp_cierre');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        \Log::info('Directorio temporal creado: ' . $tempDir);

        $pdfFiles = [];

        try {
            // 1. BITÁCORA: Exportar procesos entre 09:00 ayer y 09:00 hoy
            \Log::info('Generando Excel Bitácora...');
            $bitacoraPath = $this->generarBitacoraExcel($inicioRango, $finRango, $tempDir);
            \Log::info('Excel Bitácora generado: ' . $bitacoraPath);

            // 2. REQUERIMIENTOS: Con filtro de fecha_cierre_dia y pase a producción
            \Log::info('Generando Excel Requerimientos...');
            $reqPath = $this->generarRequerimientosExcel($fechaCierre, $tempDir);
            \Log::info('Excel Requerimientos generado: ' . $reqPath);

            // 3. INCIDENTES: Pendientes + Exitosos del día
            \Log::info('Generando Excel Incidentes...');
            $incPath = $this->generarIncidentesExcel($fechaCierre, $tempDir);
            \Log::info('Excel Incidentes generado: ' . $incPath);

            // 4. VEEAM: Informativos + Pendientes + Resueltos del día
            \Log::info('Generando Excel Veeam...');
            $veeamPath = $this->generarVeeamExcel($fechaCierre, $tempDir);
            \Log::info('Excel Veeam generado: ' . $veeamPath);

            // === CONVERTIR CADA EXCEL A PDF (preserva 100% formato LibreOffice) ===
            \Log::info('Convirtiendo cada Excel a PDF...');
            $pdfBitacora = $this->convertirExcelAPdfConLibreOffice($bitacoraPath, $tempDir);
            \Log::info('PDF Bitácora generado: ' . $pdfBitacora);
            
            $pdfReq = $this->convertirExcelAPdfConLibreOffice($reqPath, $tempDir);
            \Log::info('PDF Requerimientos generado: ' . $pdfReq);
            
            $pdfInc = $this->convertirExcelAPdfConLibreOffice($incPath, $tempDir);
            \Log::info('PDF Incidentes generado: ' . $pdfInc);
            
            $pdfVeeam = $this->convertirExcelAPdfConLibreOffice($veeamPath, $tempDir);
            \Log::info('PDF Veeam generado: ' . $pdfVeeam);

            // === COMBINAR LOS 4 PDFs EN UNO SOLO CON GHOSTSCRIPT ===
            \Log::info('Combinando los 4 PDFs en uno solo...');
            $pdfFinal = $this->combinarPdfs([$pdfBitacora, $pdfReq, $pdfInc, $pdfVeeam], $fechaCierre);
            \Log::info('PDF final generado: ' . $pdfFinal);

            // Limpiar archivos temporales (excepto el PDF final que ya está en exports)
            foreach (glob($tempDir . '/*') as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            if (file_exists($tempDir)) {
                rmdir($tempDir);
            }

            // Verificar que el PDF existe
            if (!file_exists($pdfFinal)) {
                throw new \Exception('PDF final no existe: ' . $pdfFinal);
            }

            \Log::info('Iniciando descarga del PDF: ' . $pdfFinal);
            \Log::info('Tamaño del archivo: ' . filesize($pdfFinal) . ' bytes');
            
            $nombreArchivo = basename($pdfFinal);
            
            return response()->download($pdfFinal, $nombreArchivo, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
            ]);

        } catch (\Exception $e) {
            // Limpiar en caso de error
            \Log::error('Error en generarCierre: ' . $e->getMessage());
            \Log::error('Archivo: ' . $e->getFile() . ':' . $e->getLine());
            \Log::error('Stack: ' . $e->getTraceAsString());
            
            if (file_exists($tempDir)) {
                foreach (glob($tempDir . '/*') as $file) {
                    unlink($file);
                }
                rmdir($tempDir);
            }
            return back()->with('error', 'Error al generar cierre: ' . $e->getMessage());
        }
    }

    /**
     * Genera Excel de Bitácora usando método existente de MallaController
     */
    private function generarBitacoraExcel($inicioRango, $finRango, $tempDir)
    {
        \Log::info('generarBitacoraExcel - Inicio');
        
        // Consultar procesos en el rango horario
        $procesos = DB::table('procesos')
            ->join('nombres_procesos', 'procesos.id_proceso', '=', 'nombres_procesos.id_proceso')
            ->leftJoin('estados_procesos', 'procesos.estado_id', '=', 'estados_procesos.id')
            ->whereBetween('procesos.inicio', [$inicioRango, $finRango])
            ->select(
                'procesos.*',
                'nombres_procesos.proceso',
                'nombres_procesos.descripcion',
                'estados_procesos.nombre as estado_nombre',
                'estados_procesos.color_fondo',
                'estados_procesos.color_texto'
            )
            ->orderBy('procesos.id_proceso')
            ->get();

        \Log::info('Procesos encontrados: ' . $procesos->count());

        // Cargar plantilla y generar Excel usando la misma lógica que MallaController
        $templatePath = storage_path('app/templates/Bitacora de Procesos Base.xlsx');
        \Log::info('Cargando plantilla: ' . $templatePath);
        
        if (!file_exists($templatePath)) {
            throw new \Exception('No se encontró la plantilla: ' . $templatePath);
        }
        
        $outputPath = $tempDir . '/bitacora_temp.xlsx';

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();
        
        \Log::info('Plantilla cargada, llenando datos...');
        
        $row = 4;
        foreach ($procesos as $proc) {
            $sheet->setCellValue("A{$row}", $proc->id_proceso);
            $sheet->setCellValue("B{$row}", $proc->proceso ?? '');
            $sheet->setCellValue("C{$row}", $proc->descripcion ?? '');
            $sheet->setCellValue("D{$row}", $proc->inicio ? Carbon::parse($proc->inicio)->format('d/m/Y H:i') : '');
            $sheet->setCellValue("E{$row}", $proc->fin ? Carbon::parse($proc->fin)->format('d/m/Y H:i') : '');
            $sheet->setCellValue("F{$row}", $proc->total ?? '');
            $sheet->setCellValue("G{$row}", $proc->estado_nombre ?? 'Pendiente');
            $row++;
        }

        // Auto-ajustar ancho de columnas para que todo quepa
        foreach(range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Calcular anchos automáticos
        foreach($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
        
        \Log::info('Guardando Excel en: ' . $outputPath);
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($outputPath);

        \Log::info('generarBitacoraExcel - Completado');
        return $outputPath;
    }

    /**
     * Genera Excel de Requerimientos con filtros de cierre
     */
    /**
     * Genera Excel de Requerimientos con filtros de cierre
     * LÓGICA COPIADA EXACTAMENTE DE RequerimientoController->exportarExcel
     */
    private function generarRequerimientosExcel($fechaCierre, $tempDir)
    {
        // Consultar requerimientos con filtro de cierre y pase a producción
        $requerimientos = DB::table('requerimientos')
            ->where('fecha_cierre_dia', $fechaCierre)
            ->where('requerimiento', 'like', '%pase a produccion%')
            ->orderBy('id')
            ->get();

        $templatePath = storage_path('app/templates/Requerimientos Base.xlsx');
        $outputPath = $tempDir . '/requerimientos_temp.xlsx';

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();
        $row = 4;

        foreach ($requerimientos as $req) {
            $fillColor = ($row % 2 === 0) ? 'F2F2F2' : 'E6E6E6';

            $sheet->setCellValue("A{$row}", ucfirst(strtolower($req->turno)));                  // A: Turno
            $sheet->setCellValue("B{$row}", Carbon::parse($req->fecha_hora)->format('d-m-Y')); // B: Fecha
            $sheet->setCellValue("C{$row}", $req->requerimiento);                              // C: Requerimiento
            $sheet->setCellValue("D{$row}", $req->solicitante);                                // D: Solicitante
            $sheet->setCellValue("E{$row}", $req->negocio);                                    // E: Negocio
            $sheet->setCellValue("F{$row}", $req->ambiente);                                   // F: Ambiente
            $sheet->setCellValue("G{$row}", $req->capa);                                       // G: Capa
            $sheet->setCellValue("H{$row}", $req->servidor);                                   // H: Servidor
            $sheet->setCellValue("I{$row}", $req->estado);                                     // I: Estado
            $sheet->setCellValue("J{$row}", $req->tipo_solicitud);                             // J: Tipo Solicitud
            
            // Columnas K-S: no se imprimen (área de impresión A:J)
            $sheet->setCellValue("K{$row}", $req->numero_ticket);                              // K: Número Ticket
            $sheet->setCellValue("L{$row}", $req->tipo_pase);                                  // L: Tipo Pase
            $sheet->setCellValue("M{$row}", $req->ic);                                         // M: IC
            $sheet->setCellValue("N{$row}", '1');                                              // N: 1 fijo
            $sheet->setCellValue("O{$row}", '');                                               // O: Vacío
            $sheet->setCellValue("P{$row}", '');                                               // P: Vacío
            $sheet->setCellValue("Q{$row}", $req->observaciones);                              // Q: Observaciones
            $sheet->setCellValue("R{$row}", $req->id);                                         // R: ID
            $sheet->setCellValue("S{$row}", Carbon::parse($req->fecha_hora)
                ->locale('es')->isoFormat('D MMMM YYYY H:mm') . " | Creado por: " . $req->creado_por); // S: Registro

            // Estilos (EXACTAMENTE como RequerimientoController)
            foreach (range('A', 'S') as $col) {
                $cell = "{$col}{$row}";
                $sheet->getStyle($cell)->getFont()->setName('Calibri')->setSize(11);
                $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB($fillColor);
                $sheet->getStyle($cell)->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->getColor()->setRGB('FFFFFF');
                $sheet->getStyle($cell)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            }

            // Alineaciones específicas
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            foreach (['C','D','E','F','G','H','I','J','K','L'] as $col)
                $sheet->getStyle("{$col}{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            $sheet->getStyle("M{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("N{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("R{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Ajustes de texto en Q y S
            foreach (['Q', 'S'] as $col) {
                $sheet->getStyle("{$col}{$row}")->getAlignment()->setWrapText(true);
            }

            // Altura automática
            $sheet->getRowDimension($row)->setRowHeight(-1);

            $row++;
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($outputPath);

        return $outputPath;
    }

    /**
     * Genera Excel de Incidentes con filtros de cierre (mantiene formato RichText)
     * LÓGICA COPIADA EXACTAMENTE DE IncidenteController->exportarExcel
     */
    private function generarIncidentesExcel($fechaCierre, $tempDir)
    {
        // Consultar incidentes con la misma lógica del marcado
        $estadoPendiente = DB::table('estado_incidentes')->where('nombre', 'Pendiente')->value('id');
        
        $incidentes = DB::table('incidentes')
            ->join('procesos_mantenimiento', 'incidentes.proceso_id', '=', 'procesos_mantenimiento.id')
            ->join('estado_incidentes', 'incidentes.estado_incidente_id', '=', 'estado_incidentes.id')
            ->join('negocio_incidentes', 'incidentes.negocio_incidente_id', '=', 'negocio_incidentes.id')
            ->join('ambiente_incidentes', 'incidentes.ambiente_incidente_id', '=', 'ambiente_incidentes.id')
            ->join('capa_incidentes', 'incidentes.capa_incidente_id', '=', 'capa_incidentes.id')
            ->join('servidor_incidentes', 'incidentes.servidor_incidente_id', '=', 'servidor_incidentes.id')
            ->join('evento_incidentes', 'incidentes.evento_incidente_id', '=', 'evento_incidentes.id')
            ->join('accion_incidentes', 'incidentes.accion_incidente_id', '=', 'accion_incidentes.id')
            ->join('escalado_incidentes', 'incidentes.escalado_incidente_id', '=', 'escalado_incidentes.id')
            ->where(function ($query) use ($estadoPendiente, $fechaCierre) {
                $query->where('incidentes.estado_incidente_id', $estadoPendiente)
                    ->orWhere(function ($q) use ($fechaCierre) {
                        $q->where('estado_incidentes.nombre', 'EXITOSO')
                          ->whereDate('incidentes.fecha_cambio_a_exitoso', $fechaCierre);
                    });
            })
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
            )
            ->orderBy('incidentes.id')
            ->get();

        $templatePath = storage_path('app/templates/Incidente Base.xlsx');
        $outputPath = $tempDir . '/incidentes_temp.xlsx';

        $spreadsheet = IOFactory::load($templatePath);
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
            
            // COLUMNAS K-S: no se imprimen (área de impresión A:J ya configurada)
            // Pero las lleno por si cambia el área de impresión
            $sheet->setCellValue("K{$row}", $inc->accion_nombre);                                         // K: Acción
            $sheet->setCellValue("L{$row}", $inc->escalado_nombre);                                       // L: Escalado a
            
            // M: Seguimiento -> RichText con fecha y nombre en negrita
            if (!empty($inc->seguimiento)) {
                $lines = preg_split('/\r?\n/', $inc->seguimiento);
                $rich = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $first = true;
                foreach ($lines as $ln) {
                    if (!$first) {
                        $rich->createTextRun("\n");
                    }
                    $first = false;

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
                        $rich->createTextRun($ln);
                    }
                }
                $sheet->setCellValue("M{$row}", $rich);
            } else {
                $sheet->setCellValue("M{$row}", $inc->seguimiento);
            }

            $sheet->setCellValue("N{$row}", $inc->estado_nombre);                                         // N: Estado
            $sheet->setCellValue("O{$row}", $inc->descripcion_evento);                                    // O: Descripción del evento

            // P: Solución -> RichText con fecha y usuario en negrita
            if (!empty($inc->solucion)) {
                $fechaSol = Carbon::parse($inc->updated_at ?? $inc->created_at)->format('d/m/Y');
                $usuarioSol = $inc->actualizado_por ?? ($inc->creado_por ?? 'N/A');
                $richSol = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $runFS = $richSol->createTextRun($fechaSol . ' - ');
                $runFS->getFont()->setBold(true);
                $runUser = $richSol->createTextRun($usuarioSol . ': ');
                $runUser->getFont()->setBold(true);
                $richSol->createTextRun($inc->solucion);
                $sheet->setCellValue("P{$row}", $richSol);
            } else {
                $sheet->setCellValue("P{$row}", $inc->solucion);
            }

            // Q: Observaciones -> RichText con fecha y usuario en negrita
            if (!empty($inc->observaciones)) {
                $fechaObs = Carbon::parse($inc->updated_at ?? $inc->created_at)->format('d/m/Y');
                $usuarioObs = $inc->actualizado_por ?? ($inc->creado_por ?? 'N/A');
                $richObs = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $runFO = $richObs->createTextRun($fechaObs . ' - ');
                $runFO->getFont()->setBold(true);
                $runUserO = $richObs->createTextRun($usuarioObs . ': ');
                $runUserO->getFont()->setBold(true);
                $richObs->createTextRun($inc->observaciones);
                $sheet->setCellValue("Q{$row}", $richObs);
            } else {
                $sheet->setCellValue("Q{$row}", $inc->observaciones);
            }

            $sheet->setCellValue("R{$row}", $inc->id);                                                    // R: ID

            // S: Registro -> RichText con rol y nombre en negrita
            $fechaRegistro = Carbon::parse($inc->created_at)->locale('es')->isoFormat('D MMMM YYYY H:mm');
            $creador = $inc->creado_por ?? 'N/A';
            $userRow = DB::table('users')->where('name', $creador)->first();
            $roleLabel = null;
            if ($userRow && !empty($userRow->area)) {
                $rawArea = strtolower($userRow->area);
                if (str_contains($rawArea, 'analista')) {
                    $roleLabel = 'Analista de Sistemas Integrales';
                } elseif (str_contains($rawArea, 'operador')) {
                    $roleLabel = 'Operador de Sistemas TI';
                } else {
                    $roleLabel = $userRow->area;
                }
            }

            $richReg = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
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

            // Estilos (EXACTAMENTE como IncidenteController)
            foreach (range('A', 'S') as $col) {
                $cell = "{$col}{$row}";
                $sheet->getStyle($cell)->getFont()->setName('Calibri')->setSize(11);
                $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB($fillColor);
                $sheet->getStyle($cell)->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
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

        return $outputPath;
    }

    /**
     * Genera Excel de Veeam con filtros de cierre
     */
    /**
     * Genera Excel de Veeam con filtros de cierre
     * LÓGICA COPIADA EXACTAMENTE DE ReporteVeeamController->exportarExcel
     */
    private function generarVeeamExcel($fechaCierre, $tempDir)
    {
        // Consultar con la misma lógica del marcado
        $veeam = DB::table('reportes_veeam')
            ->where(function ($query) use ($fechaCierre) {
                $query->where('estado_ticket', 'Pendiente')
                    ->orWhere(function ($q) use ($fechaCierre) {
                        $q->where('es_informativo', true)
                          ->where('fecha_cierre_informativo', $fechaCierre);
                    })
                    ->orWhere(function ($q) use ($fechaCierre) {
                        $q->where('estado_ticket', 'Resuelto')
                          ->whereDate('fecha_cambio_a_resuelto', $fechaCierre);
                    });
            })
            ->orderBy('id')
            ->get();

        $templatePath = storage_path('app/templates/Respaldos Veaam Base.xlsx');
        $outputPath = $tempDir . '/veeam_temp.xlsx';

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();
        $row = 4;

        foreach ($veeam as $rep) {
            $fillColor = ($row % 2 === 0) ? 'F2F2F2' : 'E6E6E6';

            $sheet->setCellValue("A{$row}", $rep->numero_ticket);                                         // A: # Ticket
            $sheet->setCellValue("B{$row}", $rep->fecha_inicio ? Carbon::parse($rep->fecha_inicio)->format('d-m-Y') : ''); // B: Fecha Inicio
            $sheet->setCellValue("C{$row}", $rep->fecha_fin ? Carbon::parse($rep->fecha_fin)->format('d-m-Y') : '');       // C: Fecha Fin
            $sheet->setCellValue("D{$row}", $rep->estado);                                                // D: Estado
            $sheet->setCellValue("E{$row}", $rep->job_fallido);                                           // E: Job Fallido
            $sheet->setCellValue("F{$row}", $rep->seguimiento);                                           // F: Seguimiento
            $sheet->setCellValue("G{$row}", $rep->descripcion_error);                                     // G: Descripción del error
            $sheet->setCellValue("H{$row}", $rep->estado_ticket);                                         // H: Estado Ticket
            $sheet->setCellValue("I{$row}", $rep->id);                                                    // I: ID
            
            // Columna J ya no se imprime (área de impresión A:J incluye hasta J)

            // Estilos (EXACTAMENTE como ReporteVeeamController)
            foreach (range('A', 'I') as $col) {
                $cell = "{$col}{$row}";
                $sheet->getStyle($cell)->getFont()->setName('Calibri')->setSize(11);
                $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB($fillColor);
                $sheet->getStyle($cell)->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                    ->getColor()->setRGB('FFFFFF');
                $sheet->getStyle($cell)->getAlignment()
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            }

            // Alineaciones específicas
            foreach (['A','B','C','D','E','H'] as $col) {
                $sheet->getStyle("{$col}{$row}")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            }
            
            $sheet->getStyle("I{$row}")->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Ajustes de texto en columnas largas
            foreach (['F', 'G'] as $col) {
                $sheet->getStyle("{$col}{$row}")->getAlignment()->setWrapText(true);
            }

            // Altura automática
            $sheet->getRowDimension($row)->setRowHeight(-1);

            $row++;
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($outputPath);

        return $outputPath;
    }

    
    /**
     * Convierte Excel a PDF usando LibreOffice (respeta TODO el formato)
     * Retorna la ruta del PDF generado en tempDir
     */
    private function convertirExcelAPdfConLibreOffice($excelPath, $tempDir)
    {
        // Comando LibreOffice - usar solo calc_pdf_Export sin parámetros extra
        // El área de impresión ya está configurada en el Excel
        $command = sprintf(
            'export HOME=/tmp && /usr/bin/libreoffice --headless --nofirststartwizard --nologo --norestore --convert-to pdf:calc_pdf_Export --outdir %s %s 2>&1',
            escapeshellarg($tempDir),
            escapeshellarg($excelPath)
        );
        
        \Log::info('Ejecutando: ' . $command);
        exec($command, $output, $returnCode);
        \Log::info('Output: ' . implode("\n", $output));
        
        if ($returnCode !== 0) {
            throw new \Exception('Error LibreOffice: ' . implode("\n", $output));
        }
        
        // LibreOffice genera el PDF con el mismo nombre
        $pdfPath = $tempDir . '/' . pathinfo($excelPath, PATHINFO_FILENAME) . '.pdf';
        
        if (!file_exists($pdfPath)) {
            throw new \Exception('PDF no generado: ' . $pdfPath);
        }
        
        return $pdfPath;
    }

    /**
     * Combina múltiples PDFs en uno solo usando ghostscript
     * Preserva 100% el formato de cada PDF individual
     */
    private function combinarPdfs($pdfPaths, $fechaCierre)
    {
        $fechaHora = Carbon::now()->format('Ymd_His');
        $finalPath = storage_path("app/exports/Cierre_Dia_{$fechaCierre}_{$fechaHora}.pdf");
        
        // Comando ghostscript para combinar PDFs (usar ruta completa)
        $inputFiles = implode(' ', array_map('escapeshellarg', $pdfPaths));
        $command = sprintf(
            '/usr/bin/gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile=%s %s 2>&1',
            escapeshellarg($finalPath),
            $inputFiles
        );
        
        \Log::info('Combinando PDFs con ghostscript: ' . $command);
        exec($command, $output, $returnCode);
        \Log::info('Output: ' . implode("\n", $output));
        
        if ($returnCode !== 0) {
            throw new \Exception('Error combinando PDFs: ' . implode("\n", $output));
        }
        
        if (!file_exists($finalPath)) {
            throw new \Exception('PDF final no generado: ' . $finalPath);
        }
        
        return $finalPath;
    }

}
