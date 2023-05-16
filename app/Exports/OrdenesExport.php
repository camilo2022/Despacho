<?php


namespace App\Exports;


use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class OrdenesExport implements FromArray, Responsable, WithHeadings, WithTitle
{
    use Exportable;
    
    private $ORDENES;

    public function __construct($ORDENES)
    {
        $this->ORDENES = $ORDENES;
    }
    
    public function headings(): array
    {
        return [
            
            'CONSECUTIVO',
            'CLIENTE',
            'NIT',
            'DIRECCION',
            'VENDEDOR',
            'PEDIDO',
            'ID',
            'CORRERIA',
            'DESPACHO',
            'MARCA',
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
            'ESTADO ORDEN',
            'ESTADO DETALLE ORDEN',
            'FECHA',
            'DESPACHAR',
            'FILTRADOR',
        ];
    }
    
    public function title(): string
    {
        return 'FACTURAR';
    }
    
    public function array(): array
    {
       $array = [];
       $rows = $this->ORDENES;
            $i=0;
            
            foreach($rows as $row){
                $fila = [
                    'CONSECUTIVO' => $row->consecutivo,
                    'CLIENTE' => $row->cliente,
                    'NIT' => $row->nit,
                    'DIRECCION' => $row->direccion,
                    'VENDEDOR' => $row->vendedor,
                    'PEDIDO' => $row->id_pedido,
                    'ID' => $row->id_amarrador,
                    'CORRERIA' => $row->correria,
                    'DESPACHO' => $row->despacho,
                    'MARCA' => $row->marca,
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
                    'ESTADO ORDEN' => $row->estado_orden,
                    'ESTADO DETALLE ORDEN' => $row->estado_detalle_orden,
                    'FECHA' => $row->fecha,
                    'DESPACHAR' => $row->despachar,
                    'FILTRADOR' => $row->filtrador,
                ];
                array_push($array,$fila);
                $i++;
            }
            
       return $array;
    }
    
    
}