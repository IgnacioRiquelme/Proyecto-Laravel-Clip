<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class CargaRequerimientosController extends Controller
{
    public function form()
    {
        return view('carga.requerimientos');
    }

    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx'
        ]);

        $archivo = $request->file('archivo');

        try {
            $spreadsheet = IOFactory::load($archivo->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $filas = $sheet->toArray(null, true, true, true);
        } catch (\Exception $e) {
            return back()->withErrors(['archivo' => 'No se pudo leer el archivo.']);
        }

        $total = 0;
        $insertados = 0;
        $fallidos = 0;
        $errores = [];

        foreach ($filas as $index => $fila) {
            if ($index < 4) continue; // saltar encabezados (empieza en fila 4)

            $numeroTicket = trim($fila['K'] ?? '');
            if (empty($numeroTicket)) continue;

            $total++;

            $existe = DB::table('requerimientos')->where('numero_ticket', $numeroTicket)->exists();
            if ($existe) {
                $fallidos++;
                $errores[] = "Fila {$index}: Ticket {$numeroTicket} ya existe.";
                continue;
            }

            try {
                [$fechaHora, $creadoPor] = $this->extraerCreadorYFecha($fila['S'] ?? '');

                DB::table('requerimientos')->insert([
                    'turno'            => ucfirst(strtolower(trim($fila['A']))),
                    'fecha_hora'       => $fechaHora,
                    'requerimiento'    => $fila['C'] ?? '',
                    'solicitante'      => $fila['D'] ?? '',
                    'negocio'          => $fila['E'] ?? '',
                    'ambiente'         => $fila['F'] ?? '',
                    'capa'             => $fila['G'] ?? '',
                    'servidor'         => $fila['H'] ?? '',
                    'estado'           => $fila['I'] ?? '',
                    'tipo_solicitud'   => $fila['J'] ?? '',
                    'numero_ticket'    => $numeroTicket,
                    'tipo_pase'        => $fila['L'] ?? '',
                    'ic'               => $fila['M'] ?? '',
                    'observaciones'    => $fila['Q'] ?? '',
                    'creado_por'       => $creadoPor,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);

                $insertados++;
            } catch (\Exception $e) {
                $fallidos++;
                $errores[] = "Fila {$index}: Error al insertar - " . $e->getMessage();
            }
        }

        return redirect()->route('carga.requerimientos.form')->with('resumen', [
            'total' => $total,
            'insertados' => $insertados,
            'fallidos' => $fallidos,
            'errores' => $errores
        ]);
    }

    private function extraerCreadorYFecha($texto)
    {
        $creador = 'Desconocido';
        $fechaHora = now();

        if (preg_match('/^(\d{1,2}) (\w+) (\d{4}) (\d{1,2}:\d{2})/', $texto, $matches)) {
            $dia = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $mesTexto = strtolower($matches[2]);
            $anio = $matches[3];
            $hora = $matches[4];

            $meses = [
                'enero' => '01', 'febrero' => '02', 'marzo' => '03',
                'abril' => '04', 'mayo' => '05', 'junio' => '06',
                'julio' => '07', 'agosto' => '08', 'septiembre' => '09',
                'octubre' => '10', 'noviembre' => '11', 'diciembre' => '12'
            ];

            $mesNumero = $meses[$mesTexto] ?? '01';
            $fechaHora = "$anio-$mesNumero-$dia $hora:00";
        }

        if (preg_match('/Creado por: (.+)$/', $texto, $matches)) {
            $creador = trim($matches[1]);
        }

        return [$fechaHora, $creador];
    }
}
