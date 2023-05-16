<?php

namespace App\Http\Controllers\Despachos;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use App\Models\EcommerceModel;
use App\Models\User;

use App\Exports\ListadoEcommerceExport;
use Maatwebsite\Excel\Facades\Excel;

use PDF;

class EcommerceController extends Controller
{
    public function consultaNumeroTelefono(Request $request){
        $numero = $request->telefono;
        $cliente = Cliente::where('telefono','=',$numero)->first();
        if($cliente==null)return response()->json(2);
        $user = User::where('id','=',$cliente->user_id)->first();
        $asesor =  $user->names.' '.$user->apellidos;
        $clienteD = [
            'nombres' => $cliente->nombres,
            'asesor' => $asesor,
        ];
    

        if($cliente !=null) return response()->json($clienteD);
    }

    public function saveContact(Request $request){
        $telefono = $request->telefono;
        $nombres = $request->nombres;
        $ventaConcreta = $request->ventaConcreta;
        $requerimiento = $request->requerimiento;
        $observacion = $request->observacion;
        $clasificacion = $request->clasificacion;
        $talla = $request->talla;

        $cliente = Cliente::where('telefono','=',$telefono)->first();
        if($cliente ==null){
            $cliente = Cliente::create([
                'telefono' => $telefono,
                'nombres' => $nombres,
                'user_id' => Auth::user()->id,
            ]);
        }

        $eticket = EcommerceModel::create([
            'cliente_id' => $cliente->id,
            'ventaconcreta' => $ventaConcreta,
            'tiporequerimiento' => $requerimiento,
            'observacion' => $observacion,
            'clasificacion' => $clasificacion,
            'talla' => $talla,
            'user_id' => Auth::user()->id,
        ]);

        return response()->json(['message' => 'Se ha guardado correctamente. Identificador: '.$eticket->id]);
    }

    public function ListClientes(Request $request){
        $slugrol = Auth::user()->rol->slug;
        $id = Auth::user()->id;
        if($slugrol== 'CEC' || $slugrol== 'AD'){
            $clientes = Cliente::limit(16)->get();
        }else{
            $clientes = Cliente::where('user_id','=',$id)->limit(16)->get();
        }
        
        $clientesList = [];
        foreach ($clientes as $cliente) {
            $e=EcommerceModel::where('cliente_id','=',$cliente->id)->get();

            $wwe = [];
            foreach ($e as $ec) {
                $user = User::where('id','=',$ec->user_id)->first();
                $asesor =  $user->names.' '.$user->apellidos;
                $ecommerce = [
                    'ventaconcreta' => $ec->ventaconcreta,
                    'tiporequerimiento' => $ec->tiporequerimiento,
                    'observacion' => $ec->observacion,
                    'asesor' => $asesor,
                    'fecha' => \Carbon\Carbon::parse($ec->created_at)->format('d/M/Y m:s')
                ];
                array_push($wwe,$ecommerce);
            }
            $arr=[
                'asesor' => $cliente->user->names.' '.$cliente->user->apellidos,
                'cliente' => $cliente,
                'ecommerceSaves' => $wwe,
                'count' => count($e)
            ];
            array_push($clientesList,$arr);
        }
        
        return response()->json($clientesList);
    }


    public function getClienteList(Request $request){
        $tel = $request->telefono;
        $clientesList = [];
        $cliente = Cliente::where('telefono','=',$tel)->first();
        if($cliente == null) response()->json(2);
        $e=EcommerceModel::where('cliente_id','=',$cliente->id)->get();
        $wwe = [];
        foreach ($e as $ec) {
            $user = User::where('id','=',$ec->user_id)->first();
            $asesor =  $user->names.' '.$user->apellidos;
            $ecommerce = [
                'ventaconcreta' => $ec->ventaconcreta,
                'tiporequerimiento' => $ec->tiporequerimiento,
                'observacion' => $ec->observacion,
                'asesor' => $asesor,
                'fecha' => \Carbon\Carbon::parse($ec->created_at)->format('d/M/Y m:s')
            ];
            array_push($wwe,$ecommerce);
        }
        $arr=[
            'asesor' => $cliente->user->names.' '.$cliente->user->apellidos,
            'cliente' => $cliente,
            'ecommerceSaves' => $wwe,
            'count' => count($e),
        ];
        array_push($clientesList,$arr);
        return response()->json($clientesList);
    }

    public function exportarListadoExcelController(Request $request){
        $fecha1 = $request->fecha1;
        $fecha2 = $request->fecha2;
        return Excel::download(new ListadoEcommerceExport($fecha1,$fecha2), 'listadoEcommerceDo.xlsx');
    }

    public function getVendedoresCantidad(Request $request){
        $fecha1 = $request->fecha1;
        $fecha2 = $request->fecha2;
        $users = User::where('rol_id','=','14')->get();
        $arrayDevolvera = [];
        $globalTotal=0;
        $globalConcretas=0;
        $globalFallidas=0;
        
        $slugrol = Auth::user()->rol->slug;
        $id = Auth::user()->id;
        if($slugrol== 'CEC' || $slugrol== 'AD'){
                foreach ($users as $user) {
        
                    $ordenes = EcommerceModel::where('user_id','=',$user->id)->whereBetween('created_at', [$fecha1." 00:00:00",$fecha2." 23:59:59"])->get();
                    
                    $globalTotal += count($ordenes);
                    $concreta = 0;
                    $fallida = 0;
                    foreach($ordenes as $orden){
                        if($orden->ventaconcreta == 1){
                            $concreta++;
                            $globalConcretas ++;
                        }else{
                            $fallida++;
                            $globalFallidas++;
                        }
                    }
                    $aux = [
                        'user' => $user,
                        'total' => count($ordenes),
                        'concretas' => $concreta,
                        'fallida' => $fallida,
                    ];
        
                    array_push($arrayDevolvera,$aux);
                }
        }else{
            $user = User::where('id','=',$id)->first();
            $ordenes = EcommerceModel::where('user_id','=',$id)->whereBetween('created_at', [$fecha1." 00:00:00",$fecha2." 23:59:59"])->get();
                    
                    $globalTotal += count($ordenes);
                    $concreta = 0;
                    $fallida = 0;
                    foreach($ordenes as $orden){
                        if($orden->ventaconcreta == 1){
                            $concreta++;
                            $globalConcretas ++;
                        }else{
                            $fallida++;
                            $globalFallidas++;
                        }
                    }
                    $aux = [
                        'user' => $user,
                        'total' => count($ordenes),
                        'concretas' => $concreta,
                        'fallida' => $fallida,
                    ];
        
                    array_push($arrayDevolvera,$aux);
          
        }
        
        
        return response()->json($arrayDevolvera);
    }

}
