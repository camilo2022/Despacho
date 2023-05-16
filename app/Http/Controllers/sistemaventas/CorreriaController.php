<?php

namespace App\Http\Controllers\sistemaventas;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CorreriasActualesExport;
use App\Models\sistemaVentas\ProynewBD;
use Illuminate\Support\Facades\DB;

class CorreriaController extends Controller
{
    public function __construct()
    {
        
    }
    
    public function index(){
        $consulta="select * from produccion where fecha BETWEEN '2022-01-01' and '2022-12-31'";
        $correrias = DB::connection('proynew')->select($consulta);
        //return dd($correrias);
        if($correrias != null){
            return Excel::download(new CorreriasActualesExport($correrias), 'CorreriasListadoActualYear2022.xlsx');
        }else {
            return 'No hay despachos para mostrar';
        }
      
    }
}