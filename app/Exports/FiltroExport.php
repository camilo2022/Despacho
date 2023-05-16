<?php


namespace App\Exports;


use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class FiltroExport implements FromArray, Responsable, WithHeadings, WithTitle
{
    use Exportable;
    
    private $REFERENCIAS;

    public function __construct($REFERENCIAS)
    {
        $this->REFERENCIAS = $REFERENCIAS;
    }
    
    public function headings(): array
    {
        return [
            'REFERENCIA',
            'MARCA',
            'BODEGA',
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
        ];
    }
    
    public function title(): string
    {
        return 'REFERENCIAS';
    }
    
    public function array(): array
    {
       $array = [];
       $rows = $this->REFERENCIAS;
            $i=0;
            
            foreach($rows as $row){
                foreach($row as $r){
                    $fila = [
                        'REFERENCIA' => $r['referencia'],
                        'MARCA' => $r['marca'],
                        'BODEGA' => $r['bodega'],
                        'T04' => $r['t04'],
                        'T06' => $r['t06'],
                        'T08' => $r['t08'],
                        'T10' => $r['t10'],
                        'T12' => $r['t12'],
                        'T14' => $r['t14'],
                        'T16' => $r['t16'],
                        'T18' => $r['t18'],
                        'T20' => $r['t20'],
                        'T22' => $r['t22'],
                        'T28' => $r['t28'],
                        'T30' => $r['t30'],
                        'T32' => $r['t32'],
                        'T34' => $r['t34'],
                        'T36' => $r['t36'],
                        'T38' => $r['t38'],
                        'TOTAL' => $r['total'],
                    ];
                    array_push($array,$fila);
                    $i++;
                }
            }
            
       return $array;
    }
    
    
}