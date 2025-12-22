<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProcesosConversorController extends Controller
{
    public function showForm()
    {
        return view('procesos.mantenedor.conversor');
    }

    public function convertir(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:txt,csv',
        ]);

        // Leer el archivo con mejor manejo de codificación
        $contenido = file_get_contents($request->file('archivo')->getRealPath());
        $contenido = str_replace(["\r\n", "\r"], "\n", $contenido);
        $contenido = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $contenido);
        $encoding = mb_detect_encoding($contenido, ['UTF-8', 'Windows-1252', 'ISO-8859-1', 'ASCII'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $contenido = mb_convert_encoding($contenido, 'UTF-8', $encoding);
        }
        $lineas = explode("\n", $contenido);

        try {
            $resultado = $this->procesarArchivoProcesos($lineas);
            return $this->generarCSV($resultado);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function descargarInformeComparativo(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:txt,csv',
        ]);

        // Leer archivo y extraer trabajos con categoría y ruta
        $contenido = file_get_contents($request->file('archivo')->getRealPath());
        $lineas = explode("\n", str_replace(["\r\n", "\r"], "\n", $contenido));
        $procesosArchivo = [];
        $stack = [];
        $categoria1 = '';
        $categoria2 = '';

        $gruposCategoria1 = [
            '00-PROCESOS GENERALES', '00-PROCESOS VIDA', 'APP-SERVERS', 'AS400', 'BCIPROD', 'CGPRD11G',
            'P01','P02','P03','P04','P05','P06','P07','P08','P09','P10','P11','P12','P13','P14','P15',
            'SCHEDULIX','ZP02'
        ];

        foreach ($lineas as $linea) {
            $linea = trim($linea);
            if (empty($linea)) continue;

            // Detectar grupo raíz (categoria1)
            $esCategoria1 = false;
            foreach ($gruposCategoria1 as $grupo) {
                if (strpos($linea, "pinCollapseFolder$grupo") === 0) {
                    $stack = [$grupo];
                    $categoria1 = $grupo;
                    $esCategoria1 = true;
                    break;
                }
            }
            if ($esCategoria1) continue;

            // Subcarpetas (pueden ser anidadas)
            if (preg_match('/^pinCollapseFolder(\([^)]+\)|[A-Z0-9 _\-]+)/', $linea, $matches)) {
                $nombre = trim(substr($linea, 16));
                $stack[] = $nombre;
                $categoria2 = isset($stack[1]) ? $stack[1] : '';
                continue;
            }

            // pinJob o pinBatch
            if (strpos($linea, 'pinJob') === 0 || strpos($linea, 'pinBatch') === 0) {
                $trabajo = strpos($linea, 'pinJob') === 0 ? substr($linea, 6) : substr($linea, 8);
                $trabajo = trim($trabajo);
                $rutaCompleta = 'SYSTEM / ' . implode(' / ', $stack);
                $procesosArchivo[] = [
                    'categoria1' => $categoria1,
                    'categoria2' => $categoria2,
                    'ruta_completa' => $rutaCompleta,
                    'trabajo' => $trabajo
                ];
                continue;
            }
        }

        // Procesos históricos en BD
        $procesosBD = \DB::table('procesos_mantenimiento')->get()->map(function($item) {
            return [
                'categoria1' => $item->categoria_1,
                'categoria2' => $item->categoria_2,
                'ruta_completa' => $item->ruta_completa,
                'trabajo' => trim($item->trabajo)
            ];
        })->toArray();

        // Indexar por trabajo para comparar
        $trabajosArchivo = array_column($procesosArchivo, 'trabajo');
        $trabajosBD = array_column($procesosBD, 'trabajo');

        // Nuevos: en archivo pero no en BD
        $nuevos = array_filter($procesosArchivo, function($p) use ($trabajosBD) {
            return !in_array($p['trabajo'], $trabajosBD);
        });

        // No vigentes: en BD pero no en archivo
        $noVigentes = array_filter($procesosBD, function($p) use ($trabajosArchivo) {
            return !in_array($p['trabajo'], $trabajosArchivo);
        });

        // Generar CSV
        $filename = 'informe_comparativo_procesos_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($nuevos, $noVigentes) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            // Encabezados
            fputcsv($file, ['Estado', 'Categoria 1', 'Categoria 2', 'Ruta completa', 'Trabajo'], ';');
            // Nuevos
            foreach ($nuevos as $p) {
                fputcsv($file, ['Nuevo', $p['categoria1'], $p['categoria2'], $p['ruta_completa'], $p['trabajo']], ';');
            }
            // No vigentes
            foreach ($noVigentes as $p) {
                fputcsv($file, ['No Vigente', $p['categoria1'], $p['categoria2'], $p['ruta_completa'], $p['trabajo']], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function generarCSV($datos)
    {
        $filename = 'procesos_convertidos_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($datos) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Categoria 1', 'Categoria 2', 'Ruta completa', 'Trabajo'], ';');
            foreach ($datos as $fila) {
                fputcsv($file, [
                    $fila['categoria1'],
                    $fila['categoria2'], 
                    $fila['ruta_completa'],
                    $fila['trabajo']
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function procesarArchivoProcesos($lineas)
    {
        $resultado = [];
        $stack = [];
        $categoria1 = '';
        $gruposCategoria1 = [
            '00-PROCESOS GENERALES', '00-PROCESOS VIDA', 'APP-SERVERS', 'AS400', 'BCIPROD', 'CGPRD11G',
            'P01','P02','P03','P04','P05','P06','P07','P08','P09','P10','P11','P12','P13','P14','P15',
            'SCHEDULIX','ZP02'
        ];

        foreach ($lineas as $linea) {
            $linea = trim($linea);
            if (empty($linea)) continue;

            $esCategoria1 = false;
            foreach ($gruposCategoria1 as $grupo) {
                if (strpos($linea, "pinCollapseFolder$grupo") === 0) {
                    $stack = [$grupo];
                    $categoria1 = $grupo;
                    $esCategoria1 = true;
                    break;
                }
            }
            if ($esCategoria1) continue;

            if (preg_match('/^pinCollapseFolder(\([^)]+\)|[A-Z0-9 _\-]+)/', $linea, $matches)) {
                $nombre = trim(substr($linea, 16));
                $stack[] = $nombre;
                continue;
            }

            if (strpos($linea, 'pinJob') === 0 || strpos($linea, 'pinBatch') === 0) {
                $trabajo = strpos($linea, 'pinJob') === 0 ? substr($linea, 6) : substr($linea, 8);
                $trabajo = trim($trabajo);
                $categoria2 = isset($stack[1]) ? $stack[1] : '';
                $rutaCompleta = 'SYSTEM / ' . implode(' / ', $stack);
                $resultado[] = [
                    'categoria1' => str_replace('r', '', $categoria1),
                    'categoria2' => str_replace('r', '', $categoria2),
                    'ruta_completa' => str_replace('r', '', $rutaCompleta),
                    'trabajo' => str_replace('r', '', $trabajo)
                ];
                continue;
            }
        }

        return $resultado;
    }
    
    private function dividirEnGrupos($lineas)
    {
        $grupos = [];
        $grupoActual = '';
        $lineasGrupo = [];
        
        foreach ($lineas as $linea) {
            $linea = trim($linea);
            if (empty($linea)) continue;
            
            // LIMPIEZA DE LÍNEA
            $linea = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $linea);
            $linea = preg_replace('/[^\x20-\x7E\xA0-\xFF]/', '', $linea);
            $linea = preg_replace('/\s+/', ' ', $linea);
            $linea = trim($linea);
            if (empty($linea)) continue;
            
            // Detectar inicio de nuevo grupo (categoria1) EN ORDEN ESPECÍFICO
            if (strpos($linea, 'pinCollapseFolder00-PROCESOS GENERALES') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = '00-PROCESOS GENERALES';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolder00-PROCESOS VIDA') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = '00-PROCESOS VIDA';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderAPP-SERVERS') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'APP-SERVERS';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderAS400') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'AS400';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderBCIPROD') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'BCIPROD';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderCGPRD11G') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'CGPRD11G';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderP01') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'P01';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderP02') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'P02';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderP03') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'P03';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderP04') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'P04';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderP05') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'P05';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderP06') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'P06';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderP07') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'P07';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderP08') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'P08';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderP09') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'P09';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderP10') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'P10';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderP11') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'P11';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderP12') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'P12';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderP13') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'P13';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderP14') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'P14';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderP15') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'P15';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderSCHEDULIX') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'SCHEDULIX';
                $lineasGrupo = [$linea];
                
            } elseif (strpos($linea, 'pinCollapseFolderZP02') === 0) {
                // Guardar grupo anterior
                if (!empty($grupoActual) && !empty($lineasGrupo)) {
                    $grupos[$grupoActual] = $lineasGrupo;
                }
                $grupoActual = 'ZP02';
                $lineasGrupo = [$linea];
                
            } else {
                // Agregar línea al grupo actual
                if (!empty($grupoActual)) {
                    $lineasGrupo[] = $linea;
                }
            }
        }
        
        // Guardar último grupo
        if (!empty($grupoActual) && !empty($lineasGrupo)) {
            $grupos[$grupoActual] = $lineasGrupo;
        }
        
        return $grupos;
    }
    
    private function contarJobsEnGrupo($lineasGrupo)
    {
        $count = 0;
        foreach ($lineasGrupo as $linea) {
            $linea = trim($linea);
            if (str_starts_with($linea, 'pinJob') || str_starts_with($linea, 'pinBatch')) {
                $count++;
            }
        }
        return $count;
    }
    
    private function procesarGrupo($nombreGrupo, $lineasGrupo)
{
    if ($nombreGrupo !== 'ZP02') {
        return [];
    }

    $resultado = [];
    $stack = [];
    $rutaZP02Batch = '';
    $dentroDeBatch = false;
    $dentroDeBatch2 = false;

    foreach ($lineasGrupo as $linea) {
        $linea = trim($linea);
        if (empty($linea)) continue;

        // JOBS
        if (strpos($linea, 'pinCollapseFolderJOBS') === 0) {
            $stack = [$nombreGrupo, 'JOBS'];
            continue;
        }
        // DBA
        if (strpos($linea, 'pinCollapseFolderDBA') === 0) {
            $stack = [$nombreGrupo, 'JOBS', 'DBA'];
            continue;
        }
        // PROCESOS: forzar estructura correcta
        if (strpos($linea, 'pinCollapseFolderPROCESOS') === 0) {
            $stack = [$nombreGrupo, 'JOBS', 'PROCESOS'];
            continue;
        }
        // BATCH COPIA AS400 BRETON
        if (strpos($linea, 'pinCollapseFolderBATCH COPIA AS400 BRETON') === 0) {
            $stack = [$nombreGrupo, 'JOBS', 'PROCESOS', 'BATCH COPIA AS400 BRETON'];
            $rutaZP02Batch = 'SYSTEM / ' . implode(' / ', $stack);
            continue;
        }

        // Trabajos
        if (strpos($linea, 'pinJob') === 0 || strpos($linea, 'pinBatch') === 0) {
            $trabajo = strpos($linea, 'pinJob') === 0 ? substr($linea, 6) : substr($linea, 8);
            $trabajo = trim($trabajo);

            if ($trabajo === '(ZP02) PRC CARGA XEXT MSI') {
                $dentroDeBatch = true;
                $rutaFinal = $rutaZP02Batch;
            } elseif ($trabajo === '(ZP02) PROC CARGA XEXT MSI - REFRESH 14 H') {
                $dentroDeBatch = false;
                $dentroDeBatch2 = true;
                $rutaFinal = $rutaZP02Batch;
            } elseif ($dentroDeBatch) {
                if (
                    str_contains($trabajo, 'ADMMAP_PROCESO_MSI') ||
                    str_contains($trabajo, 'MERGE - PROC_MERGE_MSI')
                ) {
                    $rutaFinal = $rutaZP02Batch;
                } else {
                    $rutaFinal = 'SYSTEM / ' . implode(' / ', $stack);
                }
            } elseif ($dentroDeBatch2) {
                if (
                    str_contains($trabajo, 'SP_ADMKPIS') ||
                    str_contains($trabajo, 'SP_ADMOPER') ||
                    str_contains($trabajo, 'SP_ADMPACO') ||
                    str_contains($trabajo, 'SP_ADMRELE')
                ) {
                    $rutaFinal = $rutaZP02Batch;
                } else {
                    $dentroDeBatch2 = false;
                    $rutaFinal = 'SYSTEM / ' . implode(' / ', $stack);
                }
            } else {
                $rutaFinal = 'SYSTEM / ' . implode(' / ', $stack);
            }

            $resultado[] = [
                'categoria1' => $nombreGrupo,
                'categoria2' => 'JOBS',
                'ruta_completa' => $rutaFinal,
                'trabajo' => $trabajo
            ];
        }
    }

    return $resultado;
}
}