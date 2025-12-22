<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ReporteVeeam;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class ReporteVeeamController extends Controller
{
    public function create()
    {
        $reporte = null;
        
        // Obtener reportes del día de hoy que no estén en estado "Resuelto"
        $reportesHoy = DB::table('reportes_veeam')
            ->whereDate('fecha_inicio', today())
            ->where('estado_ticket', '!=', 'Resuelto')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reportes-veeam.create', compact('reporte', 'reportesHoy'));
    }

    public function store(Request $request)
    {
        // Convertir fechas de dd/mm/yyyy a Y-m-d para validación
        if ($request->filled('fecha_inicio')) {
            $request->merge([
                'fecha_inicio' => \Carbon\Carbon::createFromFormat('d/m/Y', $request->fecha_inicio)->format('Y-m-d')
            ]);
        }
        if ($request->filled('fecha_fin')) {
            $request->merge([
                'fecha_fin' => \Carbon\Carbon::createFromFormat('d/m/Y', $request->fecha_fin)->format('Y-m-d')
            ]);
        }

        $validated = $request->validate([
            'numero_ticket'     => 'nullable|string|max:50',
            'fecha_inicio'      => 'required|date',
            'fecha_fin'         => 'nullable|date',
            'estado'            => 'required|in:Warning,Failed',
            'job_fallido'       => 'required|string|max:500',
            'seguimiento'       => 'nullable|string|max:5000',
            'descripcion_error' => 'required|string|max:3000',
            'estado_ticket'     => 'required|in:Informativo,Pendiente,Resuelto',
        ]);

        // Formatear seguimiento inicial
        $seguimientoInicial = '';
        if ($request->filled('seguimiento')) {
            $fecha = now()->format('d-m-Y');
            $usuario = Auth::user()->name;
            $seguimientoInicial = "{$fecha} {$usuario}: {$request->seguimiento}";
        }

        // Determinar fecha_fin automáticamente
        $fechaFin = null;
        if ($request->estado_ticket === 'Informativo') {
            $fechaFin = $request->filled('fecha_fin') ? $request->fecha_fin : now()->format('Y-m-d');
        } elseif ($request->estado_ticket === 'Resuelto') {
            $fechaFin = $request->filled('fecha_fin') ? $request->fecha_fin : now()->format('Y-m-d');
        } else {
            $fechaFin = $request->fecha_fin;
        }

        ReporteVeeam::create([
            'numero_ticket'     => $request->numero_ticket ?? 'N/A',
            'fecha_inicio'      => $request->fecha_inicio,
            'fecha_fin'         => $fechaFin,
            'estado'            => $request->estado,
            'job_fallido'       => $request->job_fallido,
            'seguimiento'       => $seguimientoInicial,
            'descripcion_error' => $request->descripcion_error,
            'estado_ticket'     => $request->estado_ticket,
            'creado_por'        => Auth::user()->name ?? 'Sistema',
        ]);

        return redirect()->route('reportes-veeam.create')
            ->with('success', 'Reporte Veeam guardado correctamente');
    }

    public function edit($id)
    {
        $reporte = ReporteVeeam::findOrFail($id);
        
        $reportesHoy = DB::table('reportes_veeam')
            ->whereDate('fecha_inicio', today())
            ->where('estado_ticket', '!=', 'Resuelto')
            ->where('id', '!=', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reportes-veeam.create', compact('reporte', 'reportesHoy'));
    }

    public function update(Request $request, $id)
    {
        $reporte = ReporteVeeam::findOrFail($id);

        // Convertir fechas de dd/mm/yyyy a Y-m-d para validación
        if ($request->filled('fecha_inicio')) {
            $request->merge([
                'fecha_inicio' => \Carbon\Carbon::createFromFormat('d/m/Y', $request->fecha_inicio)->format('Y-m-d')
            ]);
        }
        if ($request->filled('fecha_fin')) {
            $request->merge([
                'fecha_fin' => \Carbon\Carbon::createFromFormat('d/m/Y', $request->fecha_fin)->format('Y-m-d')
            ]);
        }

        $validated = $request->validate([
            'numero_ticket'      => 'nullable|string|max:50',
            'fecha_inicio'       => 'required|date',
            'fecha_fin'          => 'nullable|date',
            'estado'             => 'required|in:Warning,Failed',
            'job_fallido'        => 'required|string|max:500',
            'seguimiento'        => 'nullable|string|max:5000',
            'seguimiento_nuevo'  => 'nullable|string|max:1000',
            'descripcion_error'  => 'required|string|max:3000',
            'estado_ticket'      => 'required|in:Informativo,Pendiente,Resuelto',
        ]);

        // Lógica de seguimiento
        $seguimientoFinal = $request->seguimiento;

        // Si hay un nuevo seguimiento para agregar (permitir en CUALQUIER estado)
        if ($request->filled('seguimiento_nuevo')) {
            $fecha = now()->format('d-m-Y');
            $usuario = Auth::user()->name;
            $nuevoSeguimiento = "{$fecha} {$usuario}: {$request->seguimiento_nuevo}";
            
            if (!empty($seguimientoFinal)) {
                $seguimientoFinal .= "\n\n" . $nuevoSeguimiento;
            } else {
                $seguimientoFinal = $nuevoSeguimiento;
            }
        }

        // Determinar fecha_fin automáticamente si cambia el estado
        $fechaFin = $request->fecha_fin;
        
        // Si el estado cambió de Pendiente a Resuelto, asignar fecha actual si no tiene
        if ($request->estado_ticket === 'Resuelto' && empty($fechaFin)) {
            $fechaFin = now()->format('Y-m-d');
        }
        
        // Si es Informativo y no tiene fecha fin, asignar fecha actual
        if ($request->estado_ticket === 'Informativo' && empty($fechaFin)) {
            $fechaFin = now()->format('Y-m-d');
        }

        $reporte->update([
            'numero_ticket'     => $request->numero_ticket ?? 'N/A',
            'fecha_inicio'      => $request->fecha_inicio,
            'fecha_fin'         => $fechaFin,
            'estado'            => $request->estado,
            'job_fallido'       => $request->job_fallido,
            'seguimiento'       => $seguimientoFinal,
            'descripcion_error' => $request->descripcion_error,
            'estado_ticket'     => $request->estado_ticket,
        ]);

        return redirect()->route('reportes-veeam.create')
            ->with('success', 'Reporte actualizado correctamente');
    }

    public function index()
    {
        // Vista de reportes pendientes (SOLO estado_ticket = 'Pendiente')
        $reportes = ReporteVeeam::where('estado_ticket', 'Pendiente')
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return view('reportes-veeam.index', compact('reportes'));
    }

    public function historico()
    {
        // Todos los reportes ordenados por fecha
        $reportes = ReporteVeeam::orderBy('fecha_inicio', 'desc')->get();

        return view('reportes-veeam.historico', compact('reportes'));
    }

    public function dia()
    {
        $reportes = ReporteVeeam::whereDate('fecha_inicio', today())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reportes-veeam.lista', compact('reportes'));
    }

    public function pendientes()
    {
        // Mostrar SOLO los reportes con estado_ticket = 'Pendiente'
        $reportes = ReporteVeeam::where('estado_ticket', 'Pendiente')
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return view('reportes-veeam.lista', compact('reportes'));
    }

    public function filtrados(Request $request)
    {
        $query = ReporteVeeam::query();

        if ($request->filled('fecha_desde')) {
            $query->where('fecha_inicio', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_inicio', '<=', $request->fecha_hasta);
        }
        if ($request->filled('filtro_estado')) {
            $query->where('estado', $request->filtro_estado);
        }
        if ($request->filled('filtro_estado_ticket')) {
            $query->where('estado_ticket', $request->filtro_estado_ticket);
        }

        $reportes = $query->orderBy('fecha_inicio', 'desc')->get();

        return view('reportes-veeam.lista', compact('reportes'));
    }

    public function destroy($id)
    {
        $reporte = ReporteVeeam::findOrFail($id);
        $reporte->delete();

        return redirect()->route('reportes-veeam.create')
            ->with('success', 'Reporte eliminado correctamente');
    }

    public function exportarExcel(Request $request)
    {
        $query = DB::table('reportes_veeam');

        // Si viene de la vista del día: Pendientes + Informativos de HOY
        if ($request->filled('exportar_dia')) {
            $query->where(function($q) {
                $q->where('estado_ticket', 'Pendiente')
                  ->orWhere(function($q2) {
                      $q2->where('estado_ticket', 'Informativo')
                         ->whereDate('fecha_inicio', today());
                  });
            });
        } else {
            // Aplicar filtros del histórico
            if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha_inicio', '>=', $request->fecha_desde);
            }
            
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha_inicio', '<=', $request->fecha_hasta);
            }
            
            if ($request->filled('filtro_ticket')) {
                $query->where('numero_ticket', 'like', '%' . $request->filtro_ticket . '%');
            }
            
            if ($request->filled('filtro_estado')) {
                $query->where('estado', $request->filtro_estado);
            }
            
            if ($request->filled('filtro_estado_ticket')) {
                $query->where('estado_ticket', $request->filtro_estado_ticket);
            }
        }

        $reportes = $query->orderBy('id', 'asc')->get();

        $templatePath = storage_path('app/templates/Respaldos Veaam Base.xlsx');
        $fechaHora = Carbon::now()->format('Ymd_His');
        $outputPath = storage_path("app/exports/Reportes_Veeam_{$fechaHora}.xlsx");

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

        foreach ($reportes as $rep) {
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

            // Estilos
            foreach (range('A', 'I') as $col) {
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

        return response()->download($outputPath)->deleteFileAfterSend(true);
    }
}
