<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MallaController extends Controller
{
    public function index()
{
    $fechaMalla = $this->obtenerFechaMalla();
    $dia = strtolower(Carbon::parse($fechaMalla)->locale('es')->englishDayOfWeek);

    $gruposBD = DB::table('grupos')->orderBy('id')->get();
    $procesosHoy = DB::table('procesos')
        ->whereDate('fecha_malla', $fechaMalla)
        ->get()
        ->keyBy('id_proceso');
    $estados = DB::table('estados_procesos')->get();
    $procesosDefinidos = DB::table('nombres_procesos')->get();

    $grupos = $gruposBD->map(function ($grupo) use ($procesosDefinidos, $procesosHoy, $estados, $dia) {
        $procesos = $procesosDefinidos
            ->filter(fn($p) => $p->grupo === $grupo->nombre)
            ->map(function ($p) use ($procesosHoy, $estados, $dia) {
                $ejecucion = $procesosHoy[$p->id_proceso] ?? null;
                $dias = json_decode($p->dias ?? '[]', true);
                $correHoy = in_array($dia, $dias);

                $estadoInfo = $estados->firstWhere('id', $ejecucion->estado_id ?? null) ?? (object)[
                    'nombre' => 'Pendiente',
                    'color_fondo' => '#ffffff',
                    'color_texto' => '#000000',
                    'borde_color' => '#000000',
                    'emoji' => '❔',
                ];

                return (object)[
                    'id_proceso' => $p->id_proceso,
                    'proceso' => $p->proceso,
                    'descripcion' => $p->descripcion,
                    'hora_programada' => $p->hora_programada,
                    'estado_nombre' => $estadoInfo->nombre,
                    'color_fondo' => $estadoInfo->color_fondo,
                    'color_texto' => $estadoInfo->color_texto,
                    'borde_color' => $estadoInfo->borde_color,
                    'emoji' => $estadoInfo->emoji,
                    'inicio' => $ejecucion && $ejecucion->inicio ? Carbon::parse($ejecucion->inicio) : null,
                    'fin' => $ejecucion && $ejecucion->fin ? Carbon::parse($ejecucion->fin) : null,
                    'adm_inicio' => $ejecucion->adm_inicio ?? null,
                    'adm_fin' => $ejecucion->adm_fin ?? null,
                    'correo_inicio' => $ejecucion->correo_inicio ?? null,
                    'correo_fin' => $ejecucion->correo_fin ?? null,
                    'registro_id' => $ejecucion->id ?? null,
                    'corre_hoy' => $correHoy,
                    'mostrar_boton' => $correHoy || ($ejecucion && $ejecucion->inicio && !$ejecucion->fin),
                ];
            });

        return [
            'nombre' => $grupo->nombre,
            'color' => $grupo->color ?? 'slate',
            'procesos' => $procesos,
        ];
    });

    return view('procesos.malla', [
        'grupos' => $grupos,
        'fecha_malla' => $fechaMalla,
        'vista_historica' => false,
    ]);
}



    public function actualizar(Request $request, $idProceso)
    {
        $correo = Auth::user()->email;
        $sigla = DB::table('operadores')->where('correo', $correo)->value('sigla') ?? 'ND';

        $fechaMalla = $request->input('fecha') ?? $this->obtenerFechaMalla();

        $registro = DB::table('procesos')
            ->where('id_proceso', $idProceso)
            ->whereDate('fecha_malla', $fechaMalla)
            ->first();

        $estadoNombre = $request->input('estado');
        $estadoID = DB::table('estados_procesos')->where('nombre', $estadoNombre)->value('id');
        $grupoNombre = DB::table('nombres_procesos')->where('id_proceso', $idProceso)->value('grupo');

        $data = [
            'estado_id' => $estadoID,
            'updated_at' => now(),
        ];

        if ($request->filled('inicio')) {
            $data['inicio'] = $request->input('inicio');
            $data['adm_inicio'] = $sigla;
            $data['correo_inicio'] = $correo;

            if (!$request->filled('fin')) {
                $data['estado_id'] = DB::table('estados_procesos')->where('nombre', 'En ejecución')->value('id');
            }
        }

        if ($request->filled('fin')) {
            $data['fin'] = $request->input('fin');
            $data['adm_fin'] = $sigla;
            $data['correo_fin'] = $correo;

            if ($request->filled('inicio')) {
                $inicio = Carbon::parse($request->input('inicio'));
                $fin = Carbon::parse($request->input('fin'));
                $data['total'] = $inicio->diff($fin)->format('%H:%I:%S');
            }
        }

        if ($registro) {
            DB::table('procesos')->where('id', $registro->id)->update($data);
        } else {
            DB::table('procesos')->insert(array_merge($data, [
                'id_proceso' => $idProceso,
                'grupo' => $grupoNombre,
                'fecha_malla' => $fechaMalla,
                'created_at' => now(),
            ]));
        }

        return redirect()->back()->with('success', "✅ Proceso actualizado para el día $fechaMalla.");
    }

    public function cerrarDia(Request $request)
{
    $ultimaFecha = Carbon::parse($this->obtenerFechaMalla());
    $nuevaFecha = $ultimaFecha->copy()->addDay()->toDateString();
    $diaSemana = strtolower(Carbon::parse($nuevaFecha)->englishDayOfWeek);

    $estadoNoCorreID = DB::table('estados_procesos')->where('nombre', 'No corre')->value('id');

    $todosLosProcesos = DB::table('nombres_procesos')->get();
    $insertados = 0;

    foreach ($todosLosProcesos as $proceso) {
        $correEseDia = in_array($diaSemana, json_decode($proceso->dias ?? '[]', true));

        $existe = DB::table('procesos')->where([
            ['id_proceso', '=', $proceso->id_proceso],
            ['fecha_malla', '=', $nuevaFecha],
        ])->exists();

        if (!$existe) {
            DB::table('procesos')->insert([
                'id_proceso' => $proceso->id_proceso,
                'grupo' => $proceso->grupo,
                'fecha_malla' => $nuevaFecha,
                'estado_id' => $correEseDia ? null : $estadoNoCorreID,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $insertados++;
        }
    }

    Storage::put('malla_fecha.txt', $nuevaFecha);

    return redirect()->back()->with('success', "✅ Cierre de día exitoso. Se registraron $insertados procesos para la fecha $nuevaFecha.");
}


    private function obtenerFechaMalla(): string
    {
        if (Storage::exists('malla_fecha.txt')) {
            return trim(Storage::get('malla_fecha.txt'));
        } else {
            $hoy = Carbon::now('America/Santiago');
            $fecha = $hoy->hour < 9 ? $hoy->copy()->subDay()->toDateString() : $hoy->toDateString();
            Storage::put('malla_fecha.txt', $fecha);
            return $fecha;
        }
    }

    public function historico($fecha)
{
    try {
        $fecha = Carbon::parse($fecha)->toDateString();
    } catch (\Exception $e) {
        abort(404);
    }

    $dia = strtolower(Carbon::parse($fecha)->locale('es')->englishDayOfWeek);

    $gruposBD = DB::table('grupos')->orderBy('id')->get();
    $procesosDelDia = DB::table('procesos')->whereDate('fecha_malla', $fecha)->get()->keyBy('id_proceso');
    $estados = DB::table('estados_procesos')->get();
    $procesosDefinidos = DB::table('nombres_procesos')->get();

    $grupos = $gruposBD->map(function ($grupo) use ($procesosDefinidos, $procesosDelDia, $estados, $dia) {
        $procesos = $procesosDefinidos
            ->filter(fn($p) => $p->grupo === $grupo->nombre)
            ->map(function ($p) use ($procesosDelDia, $estados, $dia) {
                $ejecucion = $procesosDelDia[$p->id_proceso] ?? null;
                $dias = json_decode($p->dias ?? '[]', true);
                $correHoy = in_array($dia, $dias);

                $estadoInfo = $estados->firstWhere('id', $ejecucion->estado_id ?? null) ?? (object)[
                    'nombre' => 'Pendiente',
                    'color_fondo' => '#ffffff',
                    'color_texto' => '#000000',
                    'borde_color' => '#000000',
                    'emoji' => '❔',
                ];

                return (object)[
                    'id_proceso' => $p->id_proceso,
                    'proceso' => $p->proceso,
                    'descripcion' => $p->descripcion,
                    'hora_programada' => $p->hora_programada,
                    'estado_nombre' => $estadoInfo->nombre,
                    'color_fondo' => $estadoInfo->color_fondo,
                    'color_texto' => $estadoInfo->color_texto,
                    'borde_color' => $estadoInfo->borde_color,
                    'emoji' => $estadoInfo->emoji,
                    'inicio' => $ejecucion && $ejecucion->inicio ? Carbon::parse($ejecucion->inicio) : null,
                    'fin' => $ejecucion && $ejecucion->fin ? Carbon::parse($ejecucion->fin) : null,
                    'adm_inicio' => $ejecucion->adm_inicio ?? null,
                    'adm_fin' => $ejecucion->adm_fin ?? null,
                    'correo_inicio' => $ejecucion->correo_inicio ?? null,
                    'correo_fin' => $ejecucion->correo_fin ?? null,
                    'registro_id' => $ejecucion->id ?? null,
                    'corre_hoy' => $correHoy,
                    'mostrar_boton' => true,
                ];
            });

        return [
            'nombre' => $grupo->nombre,
            'color' => $grupo->color ?? 'slate',
            'procesos' => $procesos,
        ];
    });

    return view('procesos.malla', [
        'grupos' => $grupos,
        'fecha_malla' => $fecha,
        'vista_historica' => true,
    ]);
}



    public function exportar($fecha)
{
    $fechaFormateada = \Carbon\Carbon::parse($fecha)->toDateString();

    $templatePath = storage_path('app/templates/Bitacora de Procesos Base.xlsx');
    $outputPath = storage_path("app/exports/Bitacora-Procesos-{$fechaFormateada}.xlsx");

    if (!Storage::exists('exports')) {
        Storage::makeDirectory('exports');
    }

    try {
        $spreadsheet = IOFactory::load($templatePath);
    } catch (\Exception $e) {
        return response()->json(['error' => 'No se pudo cargar la plantilla Excel. Asegúrate de que existe en: ' . $templatePath . ' Error: ' . $e->getMessage()], 500);
    }

    $sheet = $spreadsheet->getActiveSheet();

    $titleString = "Bitácora diaria de procesos y jobs " . \Carbon\Carbon::yesterday()->format('d-m-Y');
    $sheet->setCellValue('A1', $titleString);

    // 📌 Mapeo de ID_Proceso → fila en Excel (CORREGIDO)
    $mapeo = [
        'P159' => 7, 'P160' => 8, 'P162' => 9,
        'P206' => 13, 'P205' => 14, 'P029' => 15, 'P126' => 16, 'P124' => 17, 'P125' => 18, 'P130' => 19,
        'P158' => 20, 'P163' => 21, 'P215' => 22, 'P166' => 23, 'P183' => 24, 'P119' => 25, 'P120' => 26,
        'P140' => 27, 'P216' => 31, 'P217' => 32, 'P218' => 33, 'P219' => 34, 'P220' => 35, 'P221' => 36,
        'P104' => 37, 'P105' => 38, 'P106' => 39, 'P101' => 40, 'P102' => 41, 'P103' => 42, 'P108' => 47,
        'P109' => 48, 'P110' => 49, 'P111' => 50, 'P112' => 51, 'P113' => 52, 'P049' => 56, 'P059' => 57,
        'P087' => 58, 'P097' => 59, 'P134' => 60, 'P161' => 61, 'P209' => 67, 'P107' => 68, 'P114' => 69,
        'P115' => 70, 'P116' => 71, 'P171' => 72, 'P129' => 73, 'P121' => 74, 'P122' => 75, 'P123' => 76,
        'P167' => 77, 'P127' => 78, 'P128' => 79, 'P172' => 83, 'P173' => 84, 'P174' => 85, 'P177' => 86,
        'P178' => 87, 'P179' => 88, 'P154' => 92, 'P003' => 96, 'P131' => 97, 'P139' => 98, 'P180' => 102
    ];

    $datos = DB::table('procesos')
        ->whereDate('fecha_malla', $fechaFormateada)
        ->join('nombres_procesos', 'procesos.id_proceso', '=', 'nombres_procesos.id_proceso')
        ->leftJoin('estados_procesos', 'procesos.estado_id', '=', 'estados_procesos.id')
        ->select(
            'procesos.id_proceso',
            'procesos.inicio',
            'procesos.fin',
            'procesos.total',
            'procesos.adm_inicio',
            'procesos.adm_fin',
            'procesos.correo_inicio',
            'procesos.correo_fin',
            'procesos.estado_id',
            'nombres_procesos.dias'
        )
        ->get()
        ->keyBy('id_proceso');

    $dia = strtolower(\Carbon\Carbon::parse($fechaFormateada)->locale('es')->englishDayOfWeek);

    // Mapeo de estados a colores HEX
    $statusColors = [
        'Undurraga' => 'f78583',
        'En ejecución' => 'ffc000',
        'Anómalo' => 'ff0000',
        'Pendiente' => 'ffff00',
        'No corre' => '00b0f0',
        'OK con observaciones' => 'c65911',
        'Ok' => '70ad47',
        'Sin Estado' => null, // No aplicar color salvo columna G
    ];
    $mediaColor = 'c6e0b4';

    foreach ($mapeo as $id => $fila) {
        $registro = $datos[$id] ?? null;
        $dias = $registro ? json_decode($registro->dias ?? '[]', true) : [];
        $correHoy = in_array($dia, $dias);

        $inicio = $registro && $registro->inicio ? \Carbon\Carbon::parse($registro->inicio)->format('d-m-Y H:i') : '';
        $fin = $registro && $registro->fin ? \Carbon\Carbon::parse($registro->fin)->format('d-m-Y H:i') : '';
        $total = $registro->total ?? '';
        $admInicio = $registro->adm_inicio ?? '';
        $admFin = $registro->adm_fin ?? '';
        $correoInicio = $registro->correo_inicio ?? '';
        $correoFin = $registro->correo_fin ?? '';

        $estado = $registro && $registro->estado_id
            ? DB::table('estados_procesos')->where('id', $registro->estado_id)->value('nombre')
            : ($correHoy ? 'Pendiente' : 'No corre');

        //$estado = ucfirst(strtolower($estado));

        // Fuente Calibri 11 y NO NEGRITA en columnas B y C
        $sheet->getStyle("B{$fila}")->getFont()->setName('Calibri')->setSize(11)->setBold(false);
        $sheet->getStyle("C{$fila}")->getFont()->setName('Calibri')->setSize(11)->setBold(false);

        $cellsToFill = [
            "D" => $correHoy ? $inicio : '',
            "E" => $correHoy ? $fin : '',
            "F" => $correHoy ? $total : '',
            "G" => '', // Media
            "H" => $correHoy ? $admInicio : '',
            "I" => $correHoy ? $admFin : '',
            "J" => $estado,
            "K" => $correHoy ? $correoInicio : '',
            "L" => $correHoy ? $correoFin : '',
        ];

        foreach ($cellsToFill as $colLetter => $value) {
            $sheet->setCellValue("{$colLetter}{$fila}", $value);
            $sheet->getStyle("{$colLetter}{$fila}")->getFont()->setName('Calibri')->setSize(11)->setBold(false);
        }

        // Colorear según el estado
        if ($estado === 'Ok') {
            // Solo la celda J
            $sheet->getStyle("J{$fila}")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB($statusColors['Ok']);
        } elseif (array_key_exists($estado, $statusColors) && $statusColors[$estado]) {
            // Todo el rango B:L
            $sheet->getStyle("B{$fila}:L{$fila}")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB($statusColors[$estado]);
        }

        // Columna G ("Media") siempre verde claro si estado es Sin Estado, Ok o En ejecución
        if (in_array($estado, ['Sin estado', 'Sin Estado', 'Ok'])) {
            $sheet->getStyle("G{$fila}")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB($mediaColor);
        }
    }

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save($outputPath);

    return response()->download($outputPath)->deleteFileAfterSend(true);
}
}
