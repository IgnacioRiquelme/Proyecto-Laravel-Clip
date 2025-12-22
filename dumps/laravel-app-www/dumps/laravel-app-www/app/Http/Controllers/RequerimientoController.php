<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RequerimientoController extends Controller
{
    // Mostrar formulario de ingreso
    public function create()
    {
        $fechaHora = Carbon::now('America/Santiago');
        $hora = $fechaHora->format('H:i');

        $turno = match (true) {
            $hora >= '08:00' && $hora <= '11:59' => 'mañana',
            $hora >= '12:00' && $hora <= '17:59' => 'tarde',
            default => 'noche',
        };

        return view('requerimientos.create', [
            'requerimiento' => null,
            'fechaHora' => $fechaHora->format('Y-m-d H:i:s'),
            'turno' => $turno,
            'tiposRequerimientos' => DB::table('tipos_requerimientos')->get(),
            'tiposSolicitantes' => DB::table('tipos_solicitantes')->get(),
            'tiposNegocios' => DB::table('tipos_negocios')->get(),
            'tiposAmbientes' => DB::table('tipos_ambientes')->get(),
            'tiposCapas' => DB::table('tipos_capas')->get(),
            'tiposServidores' => DB::table('tipos_servidores')->get(),
            'tiposEstados' => DB::table('tipos_estados')->get(),
            'tiposSolicitudes' => DB::table('tipos_solicitudes')->get(),
            'tiposPases' => DB::table('tipos_pases')->get(),
            'tiposICs' => DB::table('tipos_ics')->get(),
        ]);
    }
    // Exportar requerimientos filtrados a Excel
    public function exportarExcel(Request $request)
{
    $query = DB::table('requerimientos');

    if ($request->has('fecha_desde') && $request->has('fecha_hasta') &&
        !empty($request->input('fecha_desde')[0]) && !empty($request->input('fecha_hasta')[0])) {
        $desde = min($request->input('fecha_desde'));
        $hasta = max($request->input('fecha_hasta'));
        $query->whereBetween('fecha_hora', [$desde . ' 00:00:00', $hasta . ' 23:59:59']);
    }

    $filtros = [
        'filtro_numero' => 'numero_ticket',
        'filtro_tipo' => 'requerimiento',
        'filtro_negocio' => 'negocio',
        'filtro_ambiente' => 'ambiente',
        'filtro_capa' => 'capa',
        'filtro_servidor' => 'servidor',
        'filtro_estado' => 'estado',
        'filtro_tipo_solicitud' => 'tipo_solicitud',
        'filtro_tipo_pase' => 'tipo_pase',
        'filtro_ic' => 'ic',
    ];

    foreach ($filtros as $campoRequest => $campoDB) {
        $valores = $request->input($campoRequest);
        if (is_array($valores) && count(array_filter($valores)) > 0) {
            $query->whereIn($campoDB, array_filter($valores));
        }
    }

    $requerimientos = $query->orderBy('id', 'asc')->get();

    $templatePath = storage_path('app/templates/Requerimientos Base.xlsx');
    $outputPath = storage_path('app/exports/Requerimientos-Filtrados.xlsx');

    if (!Storage::exists('exports')) {
        Storage::makeDirectory('exports');
    }

    $spreadsheet = IOFactory::load($templatePath);
    $sheet = $spreadsheet->getActiveSheet();
    $row = 4;

    foreach ($requerimientos as $req) {
        $fillColor = ($row % 2 === 0) ? 'F2F2F2' : 'E6E6E6';

        $sheet->setCellValue("A{$row}", ucfirst(strtolower($req->turno)));                  // A: Turno
        $sheet->setCellValue("B{$row}", \Carbon\Carbon::parse($req->fecha_hora)->format('d-m-Y')); // B: Fecha
        $sheet->setCellValue("C{$row}", $req->requerimiento);                              // C: Requerimiento
        $sheet->setCellValue("D{$row}", $req->solicitante);                                // D: Solicitante
        $sheet->setCellValue("E{$row}", $req->negocio);                                    // E: Negocio
        $sheet->setCellValue("F{$row}", $req->ambiente);                                   // F: Ambiente
        $sheet->setCellValue("G{$row}", $req->capa);                                       // G: Capa
        $sheet->setCellValue("H{$row}", $req->servidor);                                   // H: Servidor
        $sheet->setCellValue("I{$row}", $req->estado);                                     // I: Estado
        $sheet->setCellValue("J{$row}", $req->tipo_solicitud);                             // J: Tipo Solicitud
        $sheet->setCellValue("K{$row}", $req->numero_ticket);                              // K: Número Ticket
        $sheet->setCellValue("L{$row}", $req->tipo_pase);                                  // L: Tipo Pase
        $sheet->setCellValue("M{$row}", $req->ic);                                         // M: IC
        $sheet->setCellValue("N{$row}", '1');                                              // N: 1 fijo
        $sheet->setCellValue("O{$row}", '');                                               // O: Vacío
        $sheet->setCellValue("P{$row}", '');                                               // P: Vacío
        $sheet->setCellValue("Q{$row}", $req->observaciones);                              // Q: Observaciones
        $sheet->setCellValue("R{$row}", $req->id);                                         // R: ID
        $sheet->setCellValue("S{$row}", \Carbon\Carbon::parse($req->fecha_hora)
            ->locale('es')->isoFormat('D MMMM YYYY H:mm') . " | Creado por: " . $req->creado_por); // S: Registro

        // 🎨 Estilos
        foreach (range('A', 'S') as $col) {
            $cell = "{$col}{$row}";
            $sheet->getStyle($cell)->getFont()->setName('Calibri')->setSize(11);
            $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($fillColor);
            $sheet->getStyle($cell)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($cell)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }

        // 🧭 Alineaciones específicas
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

    return response()->download($outputPath)->deleteFileAfterSend(true);
}


    // Guardar nuevo requerimiento
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_ticket' => 'required|unique:requerimientos,numero_ticket',
            'requerimiento' => 'required|string',
            'solicitante' => 'required|string',
            'negocio' => 'required|string',
            'ambiente' => 'required|string',
            'capa' => 'required|string',
            'servidor' => 'required|string',
            'estado' => 'required|string',
            'tipo_solicitud' => 'required|string',
            'tipo_pase' => 'required|string',
            'ic' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        $fechaHora = Carbon::now('America/Santiago');
        $hora = $fechaHora->format('H:i');

        $turno = match (true) {
            $hora >= '08:00' && $hora <= '11:59' => 'mañana',
            $hora >= '12:00' && $hora <= '17:59' => 'tarde',
            default => 'noche',
        };

        DB::table('requerimientos')->insert([
            'fecha_hora' => $fechaHora->format('Y-m-d H:i:s'),
            'turno' => $turno,
            'numero_ticket' => $validated['numero_ticket'],
            'requerimiento' => $validated['requerimiento'],
            'solicitante' => $validated['solicitante'],
            'negocio' => $validated['negocio'],
            'ambiente' => $validated['ambiente'],
            'capa' => $validated['capa'],
            'servidor' => $validated['servidor'],
            'estado' => $validated['estado'],
            'tipo_solicitud' => $validated['tipo_solicitud'],
            'tipo_pase' => $validated['tipo_pase'],
            'ic' => $validated['ic'],
            'observaciones' => $validated['observaciones'],
            'creado_por' => Auth::user()->name,
        ]);

        return redirect()->back()->with('success', 'Requerimiento ingresado correctamente.');
    }

    // Mostrar formulario de edición
    public function edit($ticket)
    {
        $requerimiento = DB::table('requerimientos')->where('numero_ticket', $ticket)->first();
        if (!$requerimiento) abort(404);

        return view('requerimientos.create', [
            'requerimiento' => $requerimiento,
            'fechaHora' => $requerimiento->fecha_hora,
            'turno' => $requerimiento->turno,
            'tiposRequerimientos' => DB::table('tipos_requerimientos')->get(),
            'tiposSolicitantes' => DB::table('tipos_solicitantes')->get(),
            'tiposNegocios' => DB::table('tipos_negocios')->get(),
            'tiposAmbientes' => DB::table('tipos_ambientes')->get(),
            'tiposCapas' => DB::table('tipos_capas')->get(),
            'tiposServidores' => DB::table('tipos_servidores')->get(),
            'tiposEstados' => DB::table('tipos_estados')->get(),
            'tiposSolicitudes' => DB::table('tipos_solicitudes')->get(),
            'tiposPases' => DB::table('tipos_pases')->get(),
            'tiposICs' => DB::table('tipos_ics')->get(),
        ]);
    }

    // Actualizar requerimiento existente
    public function update(Request $request, $ticket)
{
    $req = DB::table('requerimientos')->where('numero_ticket', $ticket)->first();

    if (!$req) {
        return redirect()->route('requerimientos.create')->with('error', 'Requerimiento no encontrado.');
    }

        // Validar sin duplicar ticket (no permitimos cambiar el número)
        $validated = $request->validate([
            'requerimiento' => 'required|string',
            'solicitante' => 'required|string',
            'negocio' => 'required|string',
            'ambiente' => 'required|string',
            'capa' => 'required|string',
            'servidor' => 'required|string',
            'estado' => 'required|string',
            'tipo_solicitud' => 'required|string',
            'tipo_pase' => 'required|string',
            'ic' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        DB::table('requerimientos')
            ->where('numero_ticket', $ticket)
            ->update(array_merge($validated, ['updated_at' => now()]));

        return redirect()->route('requerimientos.create')->with('success', '¡Requerimiento actualizado correctamente!');
    }

    // Mostrar requerimientos del día
    public function vistaRequerimientosDelDia()
    {
        $hoy = Carbon::now('America/Santiago')->toDateString();

        $requerimientos = DB::table('requerimientos')
            ->whereDate('fecha_hora', $hoy)
            ->orderBy('fecha_hora', 'desc')
            ->get();

        return view('requerimientos.dia', compact('requerimientos'));
    }

    // Guardar nuevo solicitante vía AJAX
    public function storeSolicitante(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:tipos_solicitantes,nombre',
        ]);

        DB::table('tipos_solicitantes')->insert([
            'nombre' => $request->input('nombre'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Solicitante agregado con éxito.']);
    }

    // Mostrar requerimientos filtrados
public function filtrados(Request $request)
{
    $query = DB::table('requerimientos');

    // Fecha (solo si ambos están presentes y no vacíos)
    if ($request->has('fecha_desde') && $request->has('fecha_hasta') &&
        !empty($request->input('fecha_desde')[0]) && !empty($request->input('fecha_hasta')[0])) {
        $desde = min($request->input('fecha_desde'));
        $hasta = max($request->input('fecha_hasta'));
        $query->whereBetween('fecha_hora', [$desde . ' 00:00:00', $hasta . ' 23:59:59']);
    }

    // Filtros seguros
    $filtros = [
        'filtro_numero' => 'numero_ticket',
        'filtro_tipo' => 'requerimiento',
        'filtro_negocio' => 'negocio',
        'filtro_ambiente' => 'ambiente',
        'filtro_capa' => 'capa',
        'filtro_servidor' => 'servidor',
        'filtro_estado' => 'estado',
        'filtro_tipo_solicitud' => 'tipo_solicitud',
        'filtro_tipo_pase' => 'tipo_pase',
        'filtro_ic' => 'ic',
    ];

    foreach ($filtros as $campoRequest => $campoDB) {
        $valores = $request->input($campoRequest);
        if (is_array($valores) && count(array_filter($valores)) > 0) {
            $query->whereIn($campoDB, array_filter($valores));
        }
    }

    $requerimientos = $query->orderBy('fecha_hora', 'desc')->get();

    return view('requerimientos.filtrados', [
        'requerimientos' => $requerimientos,
        'filtros' => $request->all(),
    ]);
}

public function importarRequerimientos(Request $request)
{
    $request->validate([
        'archivo' => 'required|mimes:xlsx,xls',
    ]);

    $archivo = $request->file('archivo');

    try {
        $spreadsheet = IOFactory::load($archivo);
        $hoja = $spreadsheet->getActiveSheet();
        $datos = $hoja->toArray();
    } catch (\Exception $e) {
        return back()->with('error', 'No se pudo leer el archivo. Verifica el formato.');
    }

    $total = 0;
    $insertados = 0;
    $repetidos = 0;
    $errores = [];

    foreach ($datos as $index => $fila) {
        // Saltamos encabezado
        if ($index < 3) continue;

        $total++;

        try {
            $fecha = isset($fila[1]) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fila[1]) : null;
            $fechaFormateada = $fecha ? $fecha->format('Y-m-d H:i:s') : null;

            $ticket = trim($fila[10]); // Columna K = Nº Ticket

            // Evitar duplicados
            if (DB::table('requerimientos')->where('numero_ticket', $ticket)->exists()) {
                $repetidos++;
                continue;
            }

            DB::table('requerimientos')->insert([
                'fecha_hora'       => $fechaFormateada,
                'turno'            => ucfirst(strtolower(trim($fila[0]))), // Columna A
                'requerimiento'    => trim($fila[2]), // C
                'solicitante'      => trim($fila[3]),
                'negocio'          => trim($fila[4]),
                'ambiente'         => trim($fila[5]),
                'capa'             => trim($fila[6]),
                'servidor'         => trim($fila[7]),
                'estado'           => trim($fila[8]),
                'tipo_solicitud'   => trim($fila[9]),
                'numero_ticket'    => $ticket,
                'tipo_pase'        => trim($fila[11]),
                'ic'               => trim($fila[12]),
                'observaciones'    => trim($fila[16]),
                'creado_por'       => auth()->user()->name,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            $insertados++;
        } catch (\Exception $e) {
            $errores[] = "Fila " . ($index + 1) . ": " . $e->getMessage();
        }
    }

    return back()->with('resultado', [
        'total' => $total,
        'insertados' => $insertados,
        'repetidos' => $repetidos,
        'errores' => $errores,
    ]);
}

}