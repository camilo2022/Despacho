<?php


namespace App\Exports;


use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class FacturacionMedellinExport implements FromArray, Responsable, WithHeadings, WithTitle
{
    use Exportable;
    
    private $FACTURAR;

    public function __construct($FACTURAR)
    {
        $this->FACTURAR = $FACTURAR;
    }
    
    public function headings(): array
    {
        return [
            'ESTADO',
            'CONSECUTIVO',
            'CLIENTE',
            'NIT',
            'DIRECCION',
            'VENDEDOR',
            'PEDIDO',
            'ID',
            'CORRERIA',
            'REFERENCIA',
            'T04',
            'T06',
            'T08',
            'T10',
            'T12',
            'T14',
            'T16',
            'T18',
            'T20',
            'T22',
            'T28',
            'T30',
            'T32',
            'T34',
            'T36',
            'T38',
            'TOTAL',
            'OBSERVACIONES',
            'OBS',
        ];
    }
    
    public function title(): string
    {
        return 'FACTURAR';
    }
    
    public function array(): array
    {
       $array = [];
       $rows = $this->FACTURAR;
            $i=0;
            
            foreach($rows as $row){
                $fila = [
                    'ESTADO' => $row->estado_orden,
                    'CONSECUTIVO' => $row->consecutivo,
                    'CLIENTE' => $row->cliente,
                    'NIT' => $row->nit,
                    'DIRECCION' => $row->direccion,
                    'VENDEDOR' => $row->vendedor,
                    'PEDIDO' => $row->id_pedido,
                    'ID' => $row->id_amarrador,
                    'CORRERIA' => $row->correria,
                    'REFERENCIA' => $row->referencia,
                    'T04' => $row->t04,
                    'T06' => $row->t06,
                    'T08' => $row->t08,
                    'T10' => $row->t10,
                    'T12' => $row->t12,
                    'T14' => $row->t14,
                    'T16' => $row->t16,
                    'T18' => $row->t18,
                    'T20' => $row->t20,
                    'T22' => $row->t22,
                    'T28' => $row->t28,
                    'T30' => $row->t30,
                    'T32' => $row->t32,
                    'T34' => $row->t34,
                    'T36' => $row->t36,
                    'T38' => $row->t38,
                    'TOTAL' => $row->t04+$row->t06+$row->t08+$row->t10+$row->t12+$row->t14+$row->t16+$row->t18+$row->t20+$row->t22+$row->t28+$row->t30+$row->t32+$row->t34+$row->t36+$row->t38,
                    'OBSERVACIONES' => $row->observacion,
                    'OBS' => $row->obs,
                ];
                array_push($array,$fila);
                $i++;
            }
            
       return $array;
    }
    
    
}