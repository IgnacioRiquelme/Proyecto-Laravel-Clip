<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MallaProcesosExport implements FromView, WithEvents
{
    protected $procesos;
    protected $fecha;

    public function __construct($procesos, $fecha)
    {
        $this->procesos = $procesos;
        $this->fecha = $fecha;
    }

    public function view(): View
    {
        // Agrupar por grupo
        $procesosAgrupados = $this->procesos->groupBy(function ($item) {
            return (string) $item->grupo; // 👈 Aseguramos string aquí también
        });

        return view('procesos.exports.bitacora', [
            'fecha' => Carbon::parse($this->fecha)->format('d-m-Y'),
            'procesosPorGrupo' => $procesosAgrupados
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:Z1000')->applyFromArray([
                    'font' => [
                        'name' => 'Calibri',
                        'size' => 10,
                    ],
                ]);
                $event->sheet->getDelegate()->getDefaultColumnDimension()->setAutoSize(true);
            }
        ];
    }
}
