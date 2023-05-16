<?php

namespace App\Http\Controllers\Despachos;
use App\Http\Controllers\Controller;
use App\Imports\OrdenImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Despachos\OrdenDespacho;
use App\Models\Despachos\DetallesOrdenDespacho;
use App\Models\Despachos\OrdenAlistamiento;
use App\Models\Despachos\DetallesOrdenAlistamiento;
use App\Models\Despachos\DetallesOrdenEmpacado;
use App\Models\Despachos\OrdenEmpacado;
use App\Models\Despachos\DetallesEmpaque;
use App\Models\Despachos\DetallesPedido;
use App\Models\Despachos\Empaque;
use App\Models\Despachos\PedidosPROYNEW;
use App\Models\User;
use App\Models\Siesa\TercerosModel;
use PDF;

use App\Exports\DespachosExport;
class OrdenDespachoController extends Controller
{
    public function __construct()
    {
      
    }
    public function exportarListadoDespachos(Request $request){
        $fecha1 = $request->fecha1;
        $fecha2 = $request->fecha2;
        
       $listado = DetallesOrdenEmpacado::whereBetween('created_at', [$fecha1." 00:00:00",$fecha2." 23:59:59"])->where('estado','=',2)->get();
        $listado = $this->quitarNulos($listado);
        
        if($listado != null){
            return Excel::download(new DespachosExport($listado), 'listadoDespachos.xlsx');
        }else {
            return 'No hay despachos para mostrar';
        }
        
    }
    
    private function quitarNulos($listado){
        $array=[];
        foreach($listado as $list){
            $list = json_decode(json_encode($list));
            $ordenEmpacado = OrdenEmpacado::where('id','=',$list->empacado_id)->first();
            $ordenDespacho = OrdenDespacho::where('id','=',$ordenEmpacado->ordendespacho_id)->first();
            if($ordenDespacho != null){
                $ordenAlistamiento = OrdenAlistamiento::where('ordendespacho_id','=',$ordenEmpacado->ordendespacho_id)->first();
                if(isset($ordenAlistamiento->user_id_alista)){
                    array_push($array,$list);
                }
            }
        }
        return $array;
    }

    public function marcarDespacho(Request $request){
        $empaque = Empaque::where('id','=',$request->idEmpacado)->first();
        $msg = ''; 
        $udespacha=User::where('id','=',$empaque->user_despacho)->first();
        
        if($empaque->state == 3){
            return response()->json(['codigo'=>1,'message' => 'Esta caja ya fue despachada por '.$udespacha->names.' '.$udespacha->apellidos.' el dia: '.$empaque->fdespacho],200);
        }else{
            
            //state 3 ->Despachado
            $empaque->state = 3;
            $empaque->fdespacho = date("Y-m-d H:i:s");
            $empaque->user_despacho = Auth::user()->id;
            $empaque->save();

            $empaques_orden_empacado = Empaque::where('empacado_id','=',$empaque->empacado_id)->get();
            $totalDespachado = 0;
            foreach ($empaques_orden_empacado as $empaque) {
                if($empaque->state == 3){
                    $totalDespachado++;
                }
            }
            
            $empacado = OrdenEmpacado::where('id','=',$empaque->empacado_id)->first();
            $orden = OrdenDespacho::where('id','=',$empacado->ordendespacho_id)->first();
            if($totalDespachado == count($empaques_orden_empacado)){
                $orden->estado = 8;
                $orden->save();
            }
            
            $detp = DetallesEmpaque::where('empaque_id','=',$empaque->id)->get();
            
            $ad = [];
            foreach($detp as $ert){
                /*$listado = DetallesOrdenEmpacado::where('id_amarrador','=',$ert->id_amarrador)->first();
                $listado->estado = 2;
                $listado->save();*/
               
                $dpp = DetallesPedido::where('id','=',$ert->id_amarrador)->first();
                
                if($dpp != null){
                    $dpp->despacho = 'Despachado';
                    $dpp->fdespacho = date("Y-m-d H:i:s");
                    $dpp->save();
                }
            }
            
            
            
            return response()->json([
                'codigo'=>2,
                'pos'=>$totalDespachado,
                'total' => count($empaques_orden_empacado),
                'ordenDespacho' => $orden,
                'empacado' => $empacado,
                'empaque' => $empaque
            ],200);
        }
    }

    public function viewAlistamiento(Request $request){
        $id=$request->id;
        if(isset($id)){
            $ordenAlista = OrdenAlistamiento::where('id','=',$id)->first();
            $orden = OrdenDespacho::where('id','=',$ordenAlista->ordendespacho_id)->first();
            $detalles = DetallesOrdenAlistamiento::where('alistamiento_id','=',$ordenAlista->id)->get();
            return view('sisdespachos.ordenes.viewAlistamiento')->with('id',$id)->with('detallesOrden',$detalles)->with('orden',$orden);
        }else{
            return view('sisdespachos.ordenes.viewAlistamiento');
        }
    }

    public function viewEmpacado(Request $request){
        $id=$request->id;
        if(isset($id)){
            $ordenAlista = OrdenEmpacado::where('id','=',$id)->first();
            $orden = OrdenDespacho::where('id','=',$ordenAlista->ordendespacho_id)->first();
            $detalles = DetallesOrdenEmpacado::where('empacado_id','=',$ordenAlista->id)->get();
            $empaques = Empaque::where('empacado_id','=',$ordenAlista->id)->get();
            $arrayEmpaques=[];
            foreach ($empaques as $empaque) {
                $detalle = DetallesEmpaque::where('empaque_id')->get();
                $object = [
                    'empaque' => $empaque,
                    'detalles' => $detalle,
                ];
            }


            return view('sisdespachos.ordenes.viewEmpacado')->with('id',$id)->with('detallesOrden',$detalles)->with('orden',$orden);
        }else{
            return view('sisdespachos.ordenes.viewEmpacado');
        }

    }

    public function viewPDFacturacion(Request $request){
        $orden = OrdenDespacho::where('consecutivo','=',$request->consecutivo)->first();
        $detallesOrden = DetallesOrdenDespacho::where('despacho_id','=',$orden->id)->get();
        
        if($orden->ciudad == 'Medellin'){
            $departamento = 'Antioquia';
        }else{
            $tercero = TercerosModel::where('nit','=',$orden->nit)->first();
            if($tercero != null){
                $departamento = $tercero->departamento;
            }
            
        }
        
        $ordenAlistamiento = OrdenAlistamiento::where('ordendespacho_id','=',$orden->id)->first();
        $encargadoRevision = "";
        
        if($ordenAlistamiento->user_id_rechaza != 0){
            $ordenAlistamiento->user_id_rechaza = User::where('id','=',$ordenAlistamiento->user_id_rechaza)->first();
            $ordenAlistamiento->user_id_rechaza = $ordenAlistamiento->user_id_rechaza->names.' '.$ordenAlistamiento->user_id_rechaza->apellidos;
            $encargadoRevision = $ordenAlistamiento->user_id_rechaza->names.' '.$ordenAlistamiento->user_id_rechaza->apellidos;
        }else if($ordenAlistamiento->user_id_aprueba != 0){
            $encargadoRevisionA = User::where('id','=',$ordenAlistamiento->user_id_aprueba)->first();
            $encargadoRevision = $encargadoRevisionA->names.' '.$encargadoRevisionA->apellidos;
        }else{
            $ordenAlistamiento->user_id_rechaza = 'DIGITAL';
            $encargadoRevision = "DIGITAL";
        }
        $ordenAlistamiento->user_id_alista = User::where('id','=',$ordenAlistamiento->user_id_alista)->first();
        $ordenAlistamiento->user_id_alista = $ordenAlistamiento->user_id_alista->names.' '.$ordenAlistamiento->user_id_alista->apellidos;

        if(isset($orden->user_factura)){
            $encargadoFacturacionA = User::where('id','=',$orden->user_factura)->first();
            $encargadoFacturacion = $encargadoFacturacionA->names.' '.$encargadoFacturacionA->apellidos;
        }else{
            $encargadoFacturacion = 'Sin Facturar';
        }
        
        $oEmpacado = OrdenEmpacado::where('ordendespacho_id','=',$orden->id)->first();
        $oEmpacado->user_id_alista = User::where('id','=',$oEmpacado->user_id_alista)->first();
        $oEmpacado->user_id_alista = $oEmpacado->user_id_alista->names.' '.$oEmpacado->user_id_alista->apellidos;

        $empaques = Empaque::where('empacado_id','=',$oEmpacado->id)->get();
        
        return view('sisdespachos.plantillaImprimir.imprimirFacturacion')->with('encargadoFacturacion',$encargadoFacturacion)->with('encargadoRevision',$encargadoRevision)->with('empaques',$empaques)->with('ordenEmpacado',$oEmpacado)->with('ordenAlistamiento',$ordenAlistamiento
        )->with('departamento',$departamento)->with('orden',$orden)->with('detalles',$detallesOrden)->with('consecutivo',$request->consecutivo);
    }

    public function viewCurvaPDF(Request $request){
        $ordena = OrdenAlistamiento::where('id','=',$request->consecutivo)->get();
        $ordena=$ordena[0];
        $orden = OrdenDespacho::where('id','=',$ordena->ordendespacho_id)->first();
        $userAlista = User::where('id','=',$ordena->user_id_alista)->get();
        $userAlista = $userAlista[0];
        $userAlista = $userAlista->names.' '.$userAlista->apellidos;
        $fecha = $ordena->updated_at;
        return view('sisdespachos.plantillaImprimir.imprimirOrden')->with('fecha',$fecha)->with('consecutivo',$request->consecutivo)->with('ordendespachoid',$orden->consecutivo)->with('alista',$userAlista);
        /*$pdf = PDF::loadView('sisdespachos.plantillaImprimir.imprimirOrden');
        return $pdf->stream('Alistamiento.pdf');*/
    }

    public function viewCurvaPDFEmpacado(Request $request){
        $ordena = OrdenEmpacado::where('id','=',$request->consecutivo)->get();
        $ordena=$ordena[0];
        $orden = OrdenDespacho::where('id','=',$ordena->ordendespacho_id)->first();
        $userAlista = User::where('id','=',$ordena->user_id_alista)->get();
        $userAlista = $userAlista[0];
        $userAlista = $userAlista->names.' '.$userAlista->apellidos;
        $fecha = $ordena->updated_at;
        
        return view('sisdespachos.plantillaImprimir.imprimirOrdenEmpacado')->with('fecha',$fecha)->with('consecutivo',$request->consecutivo)->with('ordendespachoid',$orden->consecutivo)->with('alista',$userAlista);
    }

    public function apruebaOrdenDespacho(Request $request){ 
        
        $detailsAprobadaOrden = $request->detalleOrdenAlistamiento;
        $orden = OrdenDespacho::where('consecutivo','=',$request->consecutivo)->first();
        $ordenAlistamiento = OrdenAlistamiento::where('ordendespacho_id','=',$orden->id)->first();
        $detailAlistamiento = DetallesOrdenAlistamiento::where('alistamiento_id','=',$ordenAlistamiento->id)->get();
        $ordenAlistamiento->estado = 2;
        $ordenAlistamiento->razonAprueba = $request->razon;
        $ordenAlistamiento->user_id_aprueba=auth()->user()->id;
        $ordenAlistamiento->save();
        $orden->estado=3;
        $orden->save();
        
        
        for($i=0;$i<count($detailAlistamiento);$i++){
            if($detailAlistamiento[$i]->id == $detailsAprobadaOrden[$i]['id']){
                if($detailsAprobadaOrden[$i]['t4'] == 0 && $detailsAprobadaOrden[$i]['t6'] == 0 && $detailsAprobadaOrden[$i]['t8'] == 0 && $detailsAprobadaOrden[$i]['t10'] == 0
                    && $detailsAprobadaOrden[$i]['t12'] == 0 && $detailsAprobadaOrden[$i]['t14'] == 0 && $detailsAprobadaOrden[$i]['t16'] == 0 && $detailsAprobadaOrden[$i]['t18'] ==0
                    && $detailsAprobadaOrden[$i]['t20'] == 0 && $detailsAprobadaOrden[$i]['t22'] == 0 && $detailsAprobadaOrden[$i]['t24'] == 0 && $detailsAprobadaOrden[$i]['t26'] == 0
                    && $detailsAprobadaOrden[$i]['t28'] == 0 && $detailsAprobadaOrden[$i]['t30'] ==0 && $detailsAprobadaOrden[$i]['t32'] == 0 && $detailsAprobadaOrden[$i]['t34'] == 0 
                    && $detailsAprobadaOrden[$i]['t36'] == 0 && $detailsAprobadaOrden[$i]['s'] == 0 && $detailsAprobadaOrden[$i]['m'] == 0 && 
                    $detailsAprobadaOrden[$i]['l'] == 0 && $detailsAprobadaOrden[$i]['xl'] == 0){
                        
                        $dpp = DetallesPedido::where('id','=',$detailsAprobadaOrden[$i]['id_amarrador'])->first();    
                        $dpp->despacho = 'Aprobado';
                        $dpp->save();
                        
                }else{
                        $detailAlistamiento[$i]->t4 = $detailsAprobadaOrden[$i]['t4'];
                        $detailAlistamiento[$i]->t6 = $detailsAprobadaOrden[$i]['t6'];
                        $detailAlistamiento[$i]->t8 = $detailsAprobadaOrden[$i]['t8'];
                        $detailAlistamiento[$i]->t10 = $detailsAprobadaOrden[$i]['t10'];
                        $detailAlistamiento[$i]->t12 = $detailsAprobadaOrden[$i]['t12'];
                        $detailAlistamiento[$i]->t14 = $detailsAprobadaOrden[$i]['t14'];
                        $detailAlistamiento[$i]->t16 = $detailsAprobadaOrden[$i]['t16'];
                        $detailAlistamiento[$i]->t18 = $detailsAprobadaOrden[$i]['t18'];
                        $detailAlistamiento[$i]->t20 = $detailsAprobadaOrden[$i]['t20'];
                        $detailAlistamiento[$i]->t22 = $detailsAprobadaOrden[$i]['t22'];
                        $detailAlistamiento[$i]->t24 = $detailsAprobadaOrden[$i]['t24'];
                        $detailAlistamiento[$i]->t26 = $detailsAprobadaOrden[$i]['t26'];
                        $detailAlistamiento[$i]->t28 = $detailsAprobadaOrden[$i]['t28'];
                        $detailAlistamiento[$i]->t30 = $detailsAprobadaOrden[$i]['t30'];
                        $detailAlistamiento[$i]->t32 = $detailsAprobadaOrden[$i]['t32'];
                        $detailAlistamiento[$i]->t34 = $detailsAprobadaOrden[$i]['t34'];
                        $detailAlistamiento[$i]->t36 = $detailsAprobadaOrden[$i]['t36'];
                        $detailAlistamiento[$i]->s = $detailsAprobadaOrden[$i]['s'];
                        $detailAlistamiento[$i]->m = $detailsAprobadaOrden[$i]['m'];
                        $detailAlistamiento[$i]->l = $detailsAprobadaOrden[$i]['l'];
                        $detailAlistamiento[$i]->xl = $detailsAprobadaOrden[$i]['xl'];
                        $detailAlistamiento[$i]->total = $detailsAprobadaOrden[$i]['total'];
                        $detailAlistamiento[$i]->save();
                }
            }
        }
        
        
        
        
        /*
        $detailsOrdenDespacho = DetallesOrdenDespacho::where('despacho_id','=',$orden->id)->get();
       
        for($i=0;$i<count($detailsOrdenDespacho);$i++){
            
            if($detailAlistamiento[$i]->t4 == 0 && $detailAlistamiento[$i]->t6 == 0 && $detailAlistamiento[$i]->t8 == 0 && $detailAlistamiento[$i]->t10 == 0
            && $detailAlistamiento[$i]->t12 == 0 && $detailAlistamiento[$i]->t14 == 0 && $detailAlistamiento[$i]->t16 == 0 && $detailAlistamiento[$i]->t18 ==0
            && $detailAlistamiento[$i]->t20 == 0 && $detailAlistamiento[$i]->t22 == 0 && $detailAlistamiento[$i]->t24 == 0 && $detailAlistamiento[$i]->t26 == 0
            && $detailAlistamiento[$i]->t28 == 0 && $detailAlistamiento[$i]->t30 ==0 && $detailAlistamiento[$i]->t32 == 0 && $detailAlistamiento[$i]->t34 == 0 
            && $detailAlistamiento[$i]->t36 == 0 && $detailAlistamiento[$i]->s == 0 && $detailAlistamiento[$i]->m ==0 && $detailAlistamiento[$i]->l==0 && $detailAlistamiento[$i]->xl==0){
                $dpp = DetallesPedido::where('id','=',$detailsOrdenDespacho[$i]->id)->first();    
                $dpp->despacho = 'Aprobado';
                $dpp->save();
                $detailsOrdenDespacho[$i]->delete();
            }else{
                $detailsOrdenDespacho[$i]->t4 = $detailAlistamiento[$i]->t4;
                $detailsOrdenDespacho[$i]->t6 = $detailAlistamiento[$i]->t6;
                $detailsOrdenDespacho[$i]->t8 = $detailAlistamiento[$i]->t8;
                $detailsOrdenDespacho[$i]->t10 = $detailAlistamiento[$i]->t10;
                $detailsOrdenDespacho[$i]->t12 = $detailAlistamiento[$i]->t12;
                $detailsOrdenDespacho[$i]->t14 = $detailAlistamiento[$i]->t14;
                $detailsOrdenDespacho[$i]->t16 = $detailAlistamiento[$i]->t16;
                $detailsOrdenDespacho[$i]->t18 = $detailAlistamiento[$i]->t18;
                $detailsOrdenDespacho[$i]->t20 = $detailAlistamiento[$i]->t20;
                $detailsOrdenDespacho[$i]->t22 = $detailAlistamiento[$i]->t22;
                $detailsOrdenDespacho[$i]->t24 = $detailAlistamiento[$i]->t24;
                $detailsOrdenDespacho[$i]->t26 = $detailAlistamiento[$i]->t26;
                $detailsOrdenDespacho[$i]->t28 = $detailAlistamiento[$i]->t28;
                $detailsOrdenDespacho[$i]->t30 = $detailAlistamiento[$i]->t30;
                $detailsOrdenDespacho[$i]->t32 = $detailAlistamiento[$i]->t32;
                $detailsOrdenDespacho[$i]->t34 = $detailAlistamiento[$i]->t34;
                $detailsOrdenDespacho[$i]->t36 = $detailAlistamiento[$i]->t36;
                $detailsOrdenDespacho[$i]->s = $detailAlistamiento[$i]->s;
                $detailsOrdenDespacho[$i]->m = $detailAlistamiento[$i]->m;
                $detailsOrdenDespacho[$i]->l = $detailAlistamiento[$i]->l;
                $detailsOrdenDespacho[$i]->xl = $detailAlistamiento[$i]->xl;
                $detailsOrdenDespacho[$i]->total = $detailAlistamiento[$i]->total;
                $detailsOrdenDespacho[$i]->save();
            }
            
        }
        */
        /*foreach ($detailsOrdenDespacho as $detalle) {
            foreach ($detailAlistamiento as $deA) {
                $detalle->t4 = $deA->t4;
                $detalle->t6 = $deA->t6;
                $detalle->t8 = $deA->t8;
                $detalle->t10 = $deA->t10;
                $detalle->t12 = $deA->t12;
                $detalle->t14 = $deA->t14;
                $detalle->t16 = $deA->t16;
                $detalle->t18 = $deA->t18;
                $detalle->t20 = $deA->t20;
                $detalle->t22 = $deA->t22;
                $detalle->t24 = $deA->t24;
                $detalle->t26 = $deA->t26;
                $detalle->t28 = $deA->t28;
                $detalle->t30 = $deA->t30;
                $detalle->t32 = $deA->t32;
                $detalle->t34 = $deA->t34;
                $detalle->t36 = $deA->t36;
                $detalle->t38 = $deA->t38;
                $detalle->s = $deA->s;
                $detalle->m = $deA->m;
                $detalle->l = $deA->l;
                $detalle->xl = $deA->xl;
                $detalle->total = $deA->total;
                $detalle->save();
            }
        }*/
        return response()->json(['message' => 'Se ha aprobado correctamente, por lo que se ha modificado la curva de la orden de despacho.'],200);
    }

    public function rechazaOrdenDespacho(Request $request){
        $orden = OrdenDespacho::where('consecutivo','=',$request->consecutivo)->first();
        $ordenAlistamiento = OrdenAlistamiento::where('ordendespacho_id','=',$orden->id)->first();
        $detailAlistamiento = DetallesOrdenAlistamiento::where('alistamiento_id','=',$ordenAlistamiento->id)->get();
        $ordenAlistamiento->estado = 4;
        $ordenAlistamiento->razonRechazo = $request->razon;
        $ordenAlistamiento->user_id_rechaza=auth()->user()->id;
        $ordenAlistamiento->save();
        $orden->estado=7;
        $orden->save();
        foreach($detailAlistamiento as $detalle){
             $dpp = DetallesPedido::where('id','=',$detalle->id_amarrador)->first();
            if($dpp != null){
                $dpp->despacho = 'Aprobado';
                $dpp->save();
            }
        }
        return response()->json(['message' => 'Se ha rechazado correctamente la orden con consecutivo: '.$request->consecutivo],200);
    }

    public function getOrdenesPendienteRevision(Request $request){
        $ordenes = OrdenDespacho::where('estado','=',6)->get();
        $arraDevolvera = [];
        foreach ($ordenes as $orden) {
          $detalleOrdenDespacho = DetallesOrdenDespacho::where('despacho_id','=',$orden->id)->get();
          $ordenAlistamiento = OrdenAlistamiento::where('ordendespacho_id','=',$orden->id)->first();
          $detallesAlistamiento = DetallesOrdenAlistamiento::where('alistamiento_id','=',$ordenAlistamiento->id)->get();
          $ctodp=0; $ctdali=0;
          foreach($detalleOrdenDespacho as $detailOrdenDespacho) {
                $ctodp+=$detailOrdenDespacho->total;
          }

          foreach ($detallesAlistamiento as $deAlistamiento) {
                $ctdali+=$deAlistamiento->total;
          }
          $operario = User::where('id','=',$ordenAlistamiento->user_id_alista)->first();
            $object = [
                'ordenDespacho' => $orden,
                'cantidadDespacho' => $ctodp,
                'cantidadAlistada' => $ctdali,
                'operario' => $operario->names.' '.$operario->apellidos,
            ];

            array_push($arraDevolvera,$object);
        }
        return response()->json(['datica'=>$arraDevolvera],200);
    }
    
    public function getOrdenDespacho(Request $request){
        $orden = OrdenDespacho::where('consecutivo','=',$request->consecutivo)->first();
        return response()->json($orden);
    }

    public function alistarDeNuevo(Request $request){
        $orden = OrdenDespacho::where('consecutivo','=',$request->consecutivo)->first();
        $ordenAlistamiento = OrdenAlistamiento::where('ordendespacho_id','=',$orden->id)->first();
        $detailAlistamiento = DetallesOrdenAlistamiento::where('alistamiento_id','=',$ordenAlistamiento->id)->get();
        foreach ($detailAlistamiento as $detalle) {
            $detalle->delete();            
        }
        $ordenAlistamiento->delete();
        $orden->estado=1;
        $orden->save();
        return response()->json(['message' => 'Se ha mandado a alistar la orden nuevamente satisfactoriamente.']);
    }

    public function getDetailAlistamiento(Request $request){
        $orden = OrdenDespacho::where('consecutivo','=',$request->consecutivo)->first();
        $ordenAlistamiento = OrdenAlistamiento::where('ordendespacho_id','=',$orden->id)->first();
        $detailAlistamiento = DetallesOrdenAlistamiento::where('alistamiento_id','=',$ordenAlistamiento->id)->get();
        return response()->json($detailAlistamiento);
    }
    
    public function getDetailAlistamientoRevisar(Request $request){
        $orden = OrdenDespacho::where('consecutivo','=',$request->consecutivo)->first();
        $ordenAlistamiento = OrdenAlistamiento::where('ordendespacho_id','=',$orden->id)->first();
        $detailAlistamiento = DetallesOrdenAlistamiento::where('alistamiento_id','=',$ordenAlistamiento->id)->get();
        return dd($detailAlistamiento);
        return response()->json($detailAlistamiento);
    }
    
    public function getDetailsOrdenDespacho(Request $request){
        $orden = OrdenDespacho::where('consecutivo','=',$request->consecutivo)->first();
        $detallesOrden = DetallesOrdenDespacho::where('despacho_id','=',$orden->id)->get();
        return response()->json($detallesOrden);
    }

    public function revisarOrdenes(){
       if(auth()->user()->rol->slug=='COORD' || auth()->user()->rol->slug=='AD' || auth()->user()->rol->slug=='DP'){
            return view('sisdespachos.ordenes.listPendientesRevisar');
       }else{
           return redirect(env('APP_URL'));
       }
    }

    public function saveNumFacturas(Request $request){
        $consecutivo = $request->consecutivo;
        $numFacturas = $request->numFacturas;
        $orden = OrdenDespacho::where('consecutivo','=',$consecutivo)->first();
        $orden->user_factura = auth()->user()->id;
        $orden->save();
        if($orden->estado == 11){
            $orden->facturas = $numFacturas;
            $orden->save();
            $msg = "Se han ACTUALIZADO correctamente los numeros de factura ".$numFacturas." a  la orden de despacho con consecutivo: ".$consecutivo." del cliente: ".$orden->cliente;
        }else{
            $orden->facturas = $numFacturas;
            $orden->estado = 11;
            $orden->save();
            $msg = "Se han agregado correctamente los numeros de factura ".$numFacturas." a  la orden de despacho con consecutivo: ".$consecutivo." del cliente: ".$orden->cliente;
        }
        return  response()->json(['message' => $msg],200);
    }

    public function viewProcesoConsecutivo(Request $request){
        $consecutivo = $request->consecutivo;
        $ordenDespacho = OrdenDespacho::where('consecutivo','=',$consecutivo)->first();
        if(isset($ordenDespacho)){
            
        
        $detallesOrdenDespacho = DetallesOrdenDespacho::where('despacho_id','=',$ordenDespacho->id)->get();
        $ordenAlistamiento = OrdenAlistamiento::where('ordendespacho_id','=',$ordenDespacho->id)->first();
        if(isset($ordenAlistamiento)){
            $detallesOrdenAlistamiento = DetallesOrdenAlistamiento::where('alistamiento_id','=',$ordenAlistamiento->id)->get();
            $ordenEmpacado = OrdenEmpacado::where('ordendespacho_id','=',$ordenDespacho->id)->first();
            $userOperario = User::where('id','=',$ordenAlistamiento->user_id_alista)->first();
            $userCoordinadorAprueba = User::where('id','=',$ordenAlistamiento->user_id_aprueba)->first();
            $userCoordinadorRechaza = User::where('id','=',$ordenAlistamiento->user_id_rechaza)->first();
            $userOperario = $userOperario->names.' '.$userOperario->apellidos;
            
            if(isset($userCoordinadorRechaza)){
             $userCoordinadorRechaza = $userCoordinadorRechaza->names.' '.$userCoordinadorRechaza->apellidos;
           }else if(isset($userCoordinadorAprueba)){
               $userCoordinadorRechaza = $userCoordinadorAprueba->names.' '.$userCoordinadorAprueba->apellidos;
            }else{
                $userCoordinadorRechaza = 'No necesito revision';
            }
            $razon = $ordenAlistamiento->razonAprueba ?? $ordenAlistamiento->razonRechazo;
           
            if(isset($ordenEmpacado)){
                $operarioEmpaca = User::where('id','=',$ordenEmpacado->user_id_alista)->first();
                $operarioEmpaca = $operarioEmpaca->names.' '.$operarioEmpaca->apellidos;
                $detallesOrdenEmpacado = DetallesOrdenEmpacado::where('empacado_id','=',$ordenEmpacado->id)->get();
                $cajas = Empaque::where('empacado_id','=',$ordenEmpacado->id)->get();
                $CajasJSON = [];
                foreach ($cajas as $caja) {
                    $detallesCaja = DetallesEmpaque::where('empaque_id','=',$caja->id)->get();
                    $object = [
                        'empaqueid' => $caja->id,
                        'cantidad' => $caja->cantidad,
                        'peso' => $caja->peso,
                        'tipo' => $caja->tipo_empaque,
                        'details' => $detallesCaja,
                    ];
                    array_push($CajasJSON,$object);
                }
                return view('sisdespachos.orden.procesoList')->with('razonrevisa',$razon)->with('opempaca',$operarioEmpaca )->with('rechaza',$userCoordinadorRechaza)->with('operario',$userOperario)->with('cajas',$CajasJSON)->with('ordenEmpacado',$ordenEmpacado)->with('detalleOrdenEmpacado',$detallesOrdenEmpacado)->with('orden', $ordenDespacho)->with('detallesOrden',$detallesOrdenDespacho)->with('ordenAlistamiento',$ordenAlistamiento)->with('detailOrdenAlistamiento',$detallesOrdenAlistamiento);
            }else return view('sisdespachos.orden.procesoList')->with('razonrevisa',$razon)->with('rechaza',$userCoordinadorRechaza)->with('operario',$userOperario)->with('orden', $ordenDespacho)->with('detallesOrden',$detallesOrdenDespacho)->with('ordenAlistamiento',$ordenAlistamiento)->with('detailOrdenAlistamiento',$detallesOrdenAlistamiento);
        }else return view('sisdespachos.orden.procesoList')->with('razonrevisa',$razon)->with('orden', $ordenDespacho)->with('detallesOrden',$detallesOrdenDespacho);
    
        }else{
           return redirect()->back()->withErrors(['msg' => 'No hay orden de despacho con ese consecutivo en el sistema.']);
        }
    }

    public function viewProcesoConsecutivoDespacho(Request $request){
        $consecutivo = $request->consecutivo;
        $ordenDespacho = OrdenDespacho::where('consecutivo','=',$consecutivo)->first();
        if(isset($ordenDespacho)){
            
        $detallesOrdenDespacho = DetallesOrdenDespacho::where('despacho_id','=',$ordenDespacho->id)->get();
        $ordenAlistamiento = OrdenAlistamiento::where('ordendespacho_id','=',$ordenDespacho->id)->first();
        if(isset($ordenAlistamiento)){
            $detallesOrdenAlistamiento = DetallesOrdenAlistamiento::where('alistamiento_id','=',$ordenAlistamiento->id)->get();
            $ordenEmpacado = OrdenEmpacado::where('ordendespacho_id','=',$ordenDespacho->id)->first();
            $userOperario = User::where('id','=',$ordenAlistamiento->user_id_alista)->first();
            $userCoordinadorAprueba = User::where('id','=',$ordenAlistamiento->user_id_aprueba)->first();
            $userCoordinadorRechaza = User::where('id','=',$ordenAlistamiento->user_id_rechaza)->first();
            $userOperario = $userOperario->names.' '.$userOperario->apellidos;
            
            if(isset($userCoordinadorRechaza)){
             $userCoordinadorRechaza = $userCoordinadorRechaza->names.' '.$userCoordinadorRechaza->apellidos;
           }else if(isset($userCoordinadorAprueba)){
               $userCoordinadorRechaza = $userCoordinadorAprueba->names.' '.$userCoordinadorAprueba->apellidos;
            }else{
                $userCoordinadorRechaza = 'No necesito revision';
            }
           
           $razon = "";
           if(isset($ordenAlistamiento->razonAprueba)){
               $razon = "APROBACION: ".$ordenAlistamiento->razonAprueba;
           }else{
               $razon = "RECHAZO: ".$ordenAlistamiento->razonRechazo;
           } 
           
            if(isset($ordenEmpacado)){
                $operarioEmpaca = User::where('id','=',$ordenEmpacado->user_id_alista)->first();
                $operarioEmpaca = $operarioEmpaca->names.' '.$operarioEmpaca->apellidos;
                $detallesOrdenEmpacado = DetallesOrdenEmpacado::where('empacado_id','=',$ordenEmpacado->id)->get();
                $cajas = Empaque::where('empacado_id','=',$ordenEmpacado->id)->get();
                $CajasJSON = [];
                foreach ($cajas as $caja) {
                    $detallesCaja = DetallesEmpaque::where('empaque_id','=',$caja->id)->get();
                    $object = [
                        'empaqueid' => $caja->id,
                        'cantidad' => $caja->cantidad,
                        'peso' => $caja->peso,
                        'tipo' => $caja->tipo_empaque,
                        'details' => $detallesCaja,
                    ];
                    array_push($CajasJSON,$object);
                }
                return view('sisdespachos.orden.procesoListDespachos')->with('razonrevisa',$razon)->with('opempaca',$operarioEmpaca )->with('rechaza',$userCoordinadorRechaza)->with('operario',$userOperario)->with('cajas',$CajasJSON)->with('ordenEmpacado',$ordenEmpacado)->with('detalleOrdenEmpacado',$detallesOrdenEmpacado)->with('orden', $ordenDespacho)->with('detallesOrden',$detallesOrdenDespacho)->with('ordenAlistamiento',$ordenAlistamiento)->with('detailOrdenAlistamiento',$detallesOrdenAlistamiento);
            }else return view('sisdespachos.orden.procesoListDespachos')->with('razonrevisa',$razon)->with('rechaza',$userCoordinadorRechaza)->with('operario',$userOperario)->with('orden', $ordenDespacho)->with('detallesOrden',$detallesOrdenDespacho)->with('ordenAlistamiento',$ordenAlistamiento)->with('detailOrdenAlistamiento',$detallesOrdenAlistamiento);
        }else return view('sisdespachos.orden.procesoListDespachos')->with('razonrevisa','n/a')->with('orden', $ordenDespacho)->with('detallesOrden',$detallesOrdenDespacho);
    
        }else{
            return redirect()->back()->withErrors(['msg' => 'No hay orden de despacho con ese consecutivo en el sistema.']);
        }
    }

    public function getordenesParaFacturar(Request $request){
        $ldate = date('Y-m-d');
        $orden = OrdenDespacho::Where('estado','=',5)->orWhere('estado','=',4)->orWhere('estado','=',11)->orWhere('estado','=',3)->get();
        return response()->json($orden);
    }

    public function viewFacturar(Request $request){
        return view('sisdespachos.facturar.list');
    }

    public function marcarCierreEmpaque(Request $request){
        $empaqueActual = $request->empaqueActual;
        $curva = $request->curva;

        $empaqueBD = Empaque::where('id','=',$empaqueActual['id'])->first();
        $empaqueBD->cantidad = $empaqueActual['cantidad'];
        $empaqueBD->peso = $empaqueActual['peso'].' Kg';
        $empaqueBD->state=2;
        $empaqueBD->save();

        $orden = OrdenEmpacado::where('id','=',$empaqueBD->empacado_id)->first();


        foreach ($curva as $curve) {
            $detalleOrdenEmpacado = DetallesOrdenEmpacado::where('id_amarrador','=',$curve['id_amarrador'])->first();

             $detalle = DetallesEmpaque::where('empaque_id','=',$empaqueBD->id)->where('referencia','=',$curve['referencia'])->first();
            if(isset($detalle)){
                if($curve['talla'] == '04'){
                    $detalle->t4 += 1;
                    $detalleOrdenEmpacado->t4 +=1;
                }else if($curve['talla'] == '06'){
                    $detalle->t6 += 1;
                    $detalleOrdenEmpacado->t6 +=1;
                }else if($curve['talla'] == '08'){
                    $detalle->t8 += 1;
                    $detalleOrdenEmpacado->t8 +=1;
                }else if($curve['talla'] == '10'){
                    $detalle->t10 += 1;
                    $detalleOrdenEmpacado->t10 +=1;
                }else if($curve['talla'] == '12'){
                    $detalle->t12 += 1;
                    $detalleOrdenEmpacado->t12 +=1;
                }else if($curve['talla'] == '14'){
                    $detalle->t14 += 1;
                    $detalleOrdenEmpacado->t14 +=1;
                }else if($curve['talla'] == '16'){
                    $detalle->t16+= 1;
                    $detalleOrdenEmpacado->t16 +=1;
                }else if($curve['talla'] == '18'){
                    $detalle->t18 += 1;
                    $detalleOrdenEmpacado->t18 +=1;
                }else if($curve['talla'] == '20'){
                    $detalle->t20 += 1;
                    $detalleOrdenEmpacado->t20 +=1;
                }else if($curve['talla'] == '22'){
                    $detalle->t22 += 1;
                    $detalleOrdenEmpacado->t22 +=1;
                }else if($curve['talla'] == '24'){
                    $detalle->t24 += 1;
                    $detalleOrdenEmpacado->t24 +=1;
                }else if($curve['talla'] == '26'){
                    $detalle->t26 += 1;
                    $detalleOrdenEmpacado->t26 +=1;
                }else if($curve['talla'] == '28'){
                    $detalle->t28 += 1;
                    $detalleOrdenEmpacado->t28 +=1;
                }else if($curve['talla'] == '30'){
                    $detalle->t30 += 1;
                    $detalleOrdenEmpacado->t30 +=1;
                }else if($curve['talla'] == '32'){
                    $detalle->t32 += 1;
                    $detalleOrdenEmpacado->t32 +=1;
                }else if($curve['talla'] == '34'){
                    $detalle->t34 += 1;
                    $detalleOrdenEmpacado->t34 +=1;
                }else if($curve['talla'] == '36'){
                    $detalle->t36 += 1;
                    $detalleOrdenEmpacado->t36 +=1;
                }else if($curve['talla'] == 's'){
                    $detalle->s += 1;
                    $detalleOrdenEmpacado->s +=1;
                }else if($curve['talla'] == 'm'){
                    $detalle->m += 1;
                    $detalleOrdenEmpacado->m +=1;
                }else if($curve['talla'] == 'l'){
                    $detalle->l += 1;
                    $detalleOrdenEmpacado->l +=1;
                }else if($curve['talla'] == 'xl'){
                    $detalle->xl += 1;
                    $detalleOrdenEmpacado->xl +=1;
                }
                $detalleOrdenEmpacado->total++;
                $detalle->total++;
                $detalle->save();
                $detalleOrdenEmpacado->save();
            }else{
                $detalle = DetallesEmpaque::create([
                    'empaque_id' => $curve['idEmpaque'],
                    'referencia' => $curve['referencia'],
                    'id_amarrador' => $curve['id_amarrador'],
                    't4' => 0,
                    't6' => 0,
                    't8' => 0,
                    't10' => 0,
                    't12' => 0,
                    't14' => 0,
                    't16' => 0,
                    't16' => 0,
                    't16' => 0,
                    't18' => 0,
                    't20' => 0,
                    't22' => 0,
                    't24' => 0,
                    't26' => 0,
                    't28' => 0,
                    't30' => 0,
                    't32' => 0,
                    't34' => 0,
                    't36' => 0,
                    't38' => 0,
                    's' => 0,
                    'm' => 0,
                    'l' => 0,
                    'xl' => 0,
                    'total' => 0,
                ]);

                if($curve['talla'] == '04'){
                    $detalle->t4 += 1;
                    $detalleOrdenEmpacado->t4 +=1;
                }else if($curve['talla'] == '06'){
                    $detalle->t6 += 1;
                    $detalleOrdenEmpacado->t6 +=1;
                }else if($curve['talla'] == '08'){
                    $detalle->t8 += 1;
                    $detalleOrdenEmpacado->t8 +=1;
                }else if($curve['talla'] == '10'){
                    $detalle->t10 += 1;
                    $detalleOrdenEmpacado->t10 +=1;
                }else if($curve['talla'] == '12'){
                    $detalle->t12 += 1;
                    $detalleOrdenEmpacado->t12 +=1;
                }else if($curve['talla'] == '14'){
                    $detalle->t14 += 1;
                    $detalleOrdenEmpacado->t14 +=1;
                }else if($curve['talla'] == '16'){
                    $detalle->t16+= 1;
                    $detalleOrdenEmpacado->t16 +=1;
                }else if($curve['talla'] == '18'){
                    $detalle->t18 += 1;
                    $detalleOrdenEmpacado->t18 +=1;
                }else if($curve['talla'] == '20'){
                    $detalle->t20 += 1;
                    $detalleOrdenEmpacado->t20 +=1;
                }else if($curve['talla'] == '22'){
                    $detalle->t22 += 1;
                    $detalleOrdenEmpacado->t22 +=1;
                }else if($curve['talla'] == '24'){
                    $detalle->t24 += 1;
                    $detalleOrdenEmpacado->t24 +=1;
                }else if($curve['talla'] == '26'){
                    $detalle->t26 += 1;
                    $detalleOrdenEmpacado->t26 +=1;
                }else if($curve['talla'] == '28'){
                    $detalle->t28 += 1;
                    $detalleOrdenEmpacado->t28 +=1;
                }else if($curve['talla'] == '30'){
                    $detalle->t30 += 1;
                    $detalleOrdenEmpacado->t30 +=1;
                }else if($curve['talla'] == '32'){
                    $detalle->t32 += 1;
                    $detalleOrdenEmpacado->t32 +=1;
                }else if($curve['talla'] == '34'){
                    $detalle->t34 += 1;
                    $detalleOrdenEmpacado->t34 +=1;
                }else if($curve['talla'] == '36'){
                    $detalle->t36 += 1;
                    $detalleOrdenEmpacado->t36 +=1;
                }else if($curve['talla'] == 's'){
                    $detalle->s += 1;
                    $detalleOrdenEmpacado->s +=1;
                }else if($curve['talla'] == 'm'){
                    $detalle->m += 1;
                    $detalleOrdenEmpacado->m +=1;
                }else if($curve['talla'] == 'l'){
                    $detalle->l += 1;
                    $detalleOrdenEmpacado->l +=1;
                }else if($curve['talla'] == 'xl'){
                    $detalle->xl += 1;
                    $detalleOrdenEmpacado->xl +=1;
                }
                $detalleOrdenEmpacado->total++;
                $detalle->total++;
                $detalle->save();
                $detalleOrdenEmpacado->save();
            }
        }
        return response()->json(['message','El empaque se ha marcado como cerrada, correctamente.'],200);
    }

    public function getEmpaques(Request $request){
        $empacados = Empaque::where('empacado_id','=',$request->idEmpacado)->get();
        return response()->json($empacados);
    }

    public function deleteEmpaque(Request $request){
        $empaqueid = $request->empaque_id;
        $empaque = Empaque::where('id','=',$empaqueid)->first();
        $detalles = DetallesEmpaque::where('empaque_id','=',$empaque->id)->get();
        foreach($detalles as $detalle){
            $detail=DetallesOrdenEmpacado::where('id_amarrador','=',$detalle->id_amarrador)->first();
            $detail->t4 -= $detalle->t4;
            $detail->t6 -= $detalle->t6;
            $detail->t8 -= $detalle->t8;
            $detail->t10 -= $detalle->t10;
            $detail->t12 -= $detalle->t12;
            $detail->t14 -= $detalle->t14;
            $detail->t16 -= $detalle->t16;
            $detail->t18 -= $detalle->t18;
            $detail->t20 -= $detalle->t20;
            $detail->t22 -= $detalle->t22;
            $detail->t24 -= $detalle->t24;
            $detail->t26 -= $detalle->t26;
            $detail->t28 -= $detalle->t28;
            $detail->t30 -= $detalle->t30;
            $detail->t32 -= $detalle->t32;
            $detail->t34 -= $detalle->t34;
            $detail->t36 -= $detalle->t36;
            $detail->t38 -= $detalle->t38;
            $detail->s -= $detalle->s;
            $detail->m -= $detalle->m;
            $detail->l -= $detalle->l;
            $detail->xl -= $detalle->xl;
            $total = $detalle->t4+$detalle->t6+$detalle->t8+$detalle->t10+$detalle->t12+$detalle->t14+$detalle->t16+$detalle->t18+$detalle->t20+$detalle->t22+$detalle->t24+$detalle->t26+$detalle->t28+$detalle->t30+$detalle->t32+$detalle->t34+$detalle->t36+$detalle->s+$detalle->m+$detalle->l+$detalle->xl;
            $detail->total -= $total;
            $detail->save();
            $detalle->delete();
        }

        $empaque->delete();

        return response()->json(['message', 'Se ha eliminado correctamente.'],200);
    }

    public function createEmpaque(Request $request){
        $empacado_id = $request->empacado_id;
        $tipoempaque = $request->tipoempaque;

        $empaque = Empaque::create([
            'empacado_id' => $empacado_id,
            'cantidad' => 0,
            'peso' => 'Por Definir',
            'tipo_empaque' => $tipoempaque,
            'state' => 1
        ]);
        return response()->json(['message','Se ha creado correctamente el empaque.'],200);
    }

    public function getEmpaque(Request $request){
        $empaque_id = $request->idEmpaque;
        $empaque = Empaque::where('id','=',$empaque_id)->first();
        $empaque = json_decode(json_encode($empaque));
        return response()->json($empaque);
    }

    public function getDetallesEmpaque(Request $request){
        $empaqueid = $request->empaqueid;
        $empques = DetallesEmpaque::where('empaque_id','=',$empaqueid)->get();
        return response()->json($empques);
    }

    public function subirDetallesEmpaque(Request $request){
        $empaqueid = $request->empaqueid;
        $arrayAlistaOperario = $request->arrayOperario;
        $arrayAlistaOperario = json_decode(json_encode($arrayAlistaOperario));
        DetallesEmpaque::create([
            'empaque_id' => $empaqueid,
            'referencia' => $arrayAlistaOperario->referencia,
            't4' => $arrayAlistaOperario->t4,
            't6' => $arrayAlistaOperario->t6,
            't8' => $arrayAlistaOperario->t8,
            't10' => $arrayAlistaOperario->t10,
            't12' => $arrayAlistaOperario->t12,
            't14' => $arrayAlistaOperario->t14,
            't16' => $arrayAlistaOperario->t16,
            't18' => $arrayAlistaOperario->t18,
            't20' => $arrayAlistaOperario->t20,
            't22' => $arrayAlistaOperario->t22,
            't24' => $arrayAlistaOperario->t24,
            't26' => $arrayAlistaOperario->t26,
            't28' => $arrayAlistaOperario->t28,
            't30' => $arrayAlistaOperario->t30,
            't32' => $arrayAlistaOperario->t32,
            't34' => $arrayAlistaOperario->t34,
            't36' => $arrayAlistaOperario->t36,
            't38' => $arrayAlistaOperario->t38,
            's' => $arrayAlistaOperario->s,
            'm' => $arrayAlistaOperario->m,
            'l' => $arrayAlistaOperario->l,
            'xl' => $arrayAlistaOperario->xl,
            'total' => $arrayAlistaOperario->total,
        ]);
        return response()->json(['message','Detalles de empaque subidos correctamente.'],200);
    }

    public function getOrdenes(Request $request){
        $ldate = date('Y-m-d');
        $orden = OrdenDespacho::where('created_at','LIKE','%'.$ldate.'%')->get();
        return response()->json($orden);
    }
    
    public function getOrdenesPorFecha(Request $request){
        $fecha = $request->date;
        $orden = OrdenDespacho::where('created_at','LIKE','%'.$fecha.'%')->get();
        return response()->json($orden); 
    }
    
    public function marcarCompletadaEmpacado(Request $request){
        $id_alistamiento = $request->iorden;
        $arrayAlistaOperario = $request->arrayOperario;
        $arrayAlistaOperario = json_decode(json_encode($arrayAlistaOperario));
        $ordena = OrdenEmpacado::where('id','=',$id_alistamiento)->first();
        $detallesAlistamientos = DetallesOrdenEmpacado::where('empacado_id','=',$id_alistamiento)->get();
        $ordendespacho = OrdenDespacho::where('id','=',$ordena->ordendespacho_id)->first();
        $ordendespacho->estado=5;
        $ordendespacho->save();

        $ordena->estado = 2;
        $ordena->save();

        foreach ($detallesAlistamientos as $curva) {
            foreach ($arrayAlistaOperario as $curve) {
                if($curva->id_amarrador == $curve->id_amarrador){
                    $curva->t4 = $curve->t4;
                    $curva->t6 = $curve->t6;
                    $curva->t8 = $curve->t8;
                    $curva->t10 = $curve->t10;
                    $curva->t12 = $curve->t12;
                    $curva->t14 = $curve->t14;
                    $curva->t16 = $curve->t16;
                    $curva->t18 = $curve->t18;
                    $curva->t20 = $curve->t20;
                    $curva->t22 = $curve->t22;
                    $curva->t24 = $curve->t24;
                    $curva->t26 = $curve->t26;
                    $curva->t28 = $curve->t28;
                    $curva->t30 = $curve->t30;
                    $curva->t32 = $curve->t32;
                    $curva->t34 = $curve->t34;
                    $curva->t36 = $curve->t36;
                    $curva->t38 = $curve->t38;
                    $curva->s = $curve->s;
                    $curva->m = $curve->m;
                    $curva->l = $curve->l;
                    $curva->xl = $curve->xl;
                    $curva->estado = $curve->estado;
                    $curva->save();
                }
                
            }
        }
        return response()->json(['message' => 'Se ha marcado la orden como empacada correctamente.'],200);
    }
    
    public function marcarCompletada(Request $request){
        $id_alistamiento = $request->iorden;
        $arrayAlistaOperario = $request->arrayOperario;
        $arrayAlistaOperario = json_decode(json_encode($arrayAlistaOperario));
        $ordena = OrdenAlistamiento::where('id','=',$id_alistamiento)->first();
        $detallesAlistamientos = DetallesOrdenAlistamiento::where('alistamiento_id','=',$id_alistamiento)->get();
        $ordendespacho = OrdenDespacho::where('id','=',$ordena->ordendespacho_id)->first();
        $ordendespacho->estado=3;
        $ordendespacho->save();

        $ordena->estado = 2;
        $ordena->save();

        foreach ($detallesAlistamientos as $curva) {
            foreach ($arrayAlistaOperario as $curve) {
                if($curva->id_amarrador == $curve->id_amarrador){
                    $curva->t4 = $curve->t4;
                    $curva->t6 = $curve->t6;
                    $curva->t8 = $curve->t8;
                    $curva->t10 = $curve->t10;
                    $curva->t12 = $curve->t12;
                    $curva->t14 = $curve->t14;
                    $curva->t16 = $curve->t16;
                    $curva->t18 = $curve->t18;
                    $curva->t20 = $curve->t20;
                    $curva->t22 = $curve->t22;
                    $curva->t24 = $curve->t24;
                    $curva->t26 = $curve->t26;
                    $curva->t28 = $curve->t28;
                    $curva->t30 = $curve->t30;
                    $curva->t32 = $curve->t32;
                    $curva->t34 = $curve->t34;
                    $curva->t36 = $curve->t36;
                    $curva->t38 = $curve->t38;
                    $curva->s = $curve->s;
                    $curva->m = $curve->m;
                    $curva->l = $curve->l;
                    $curva->xl = $curve->xl;
                    $curva->total = $curve->total;
                    $curva->estado = $curve->estado;
                    $curva->save();
                }
            }
        }
        return response()->json(['message' => 'Se ha marcado la orden como alistada correctamente.'],200);

    }
    
    public function mandarARevisar(Request $request){ 
        $id_alistamiento = $request->iorden;
        $arrayAlistaOperario = $request->arrayOperario;
        $razonMandaRevision = $request->razonMandaRevision;
        
        $arrayAlistaOperario = json_decode(json_encode($arrayAlistaOperario));
        $ordena = OrdenAlistamiento::where('id','=',$id_alistamiento)->first();
        $detallesAlistamientos = DetallesOrdenAlistamiento::where('alistamiento_id','=',$id_alistamiento)->get();
        $ordendespacho = OrdenDespacho::where('id','=',$ordena->ordendespacho_id)->first();
        $ordendespacho->estado=6;
        $ordendespacho->save();

        $ordena->razonRevisionOp = $razonMandaRevision;
        $ordena->estado = 3;
        $ordena->save();
        
        for($i=0;$i<count($detallesAlistamientos);$i++){
            $detallesAlistamientos[$i]->t4 = $arrayAlistaOperario[$i]->t4;
            $detallesAlistamientos[$i]->t6 = $arrayAlistaOperario[$i]->t6;
            $detallesAlistamientos[$i]->t8 = $arrayAlistaOperario[$i]->t8;
            $detallesAlistamientos[$i]->t10 = $arrayAlistaOperario[$i]->t10;
            $detallesAlistamientos[$i]->t12 = $arrayAlistaOperario[$i]->t12;
            $detallesAlistamientos[$i]->t14 = $arrayAlistaOperario[$i]->t14;
            $detallesAlistamientos[$i]->t16 = $arrayAlistaOperario[$i]->t16;
            $detallesAlistamientos[$i]->t18 = $arrayAlistaOperario[$i]->t18;
            $detallesAlistamientos[$i]->t20 = $arrayAlistaOperario[$i]->t20;
            $detallesAlistamientos[$i]->t22 = $arrayAlistaOperario[$i]->t22;
            $detallesAlistamientos[$i]->t24 = $arrayAlistaOperario[$i]->t24;
            $detallesAlistamientos[$i]->t26 = $arrayAlistaOperario[$i]->t26;
            $detallesAlistamientos[$i]->t28 = $arrayAlistaOperario[$i]->t28;
            $detallesAlistamientos[$i]->t30 = $arrayAlistaOperario[$i]->t30;
            $detallesAlistamientos[$i]->t32 = $arrayAlistaOperario[$i]->t32;
            $detallesAlistamientos[$i]->t34 = $arrayAlistaOperario[$i]->t34;
            $detallesAlistamientos[$i]->t36 = $arrayAlistaOperario[$i]->t36;
            $detallesAlistamientos[$i]->t38 = $arrayAlistaOperario[$i]->t38;
            $detallesAlistamientos[$i]->s = $arrayAlistaOperario[$i]->s;
            $detallesAlistamientos[$i]->m = $arrayAlistaOperario[$i]->m;
            $detallesAlistamientos[$i]->l = $arrayAlistaOperario[$i]->l;
            $detallesAlistamientos[$i]->xl = $arrayAlistaOperario[$i]->xl;
            $detallesAlistamientos[$i]->estado = $arrayAlistaOperario[$i]->estado;
            $detallesAlistamientos[$i]->total = $arrayAlistaOperario[$i]->total;
            $detallesAlistamientos[$i]->save();
        }
        
        return response()->json(['message' => 'Se ha mandado a revision al coordinador de despacho la orden para su revision de cantidades de la curva.'],200);
}

    public function infoOrdenAlistamiento(Request $request){
        $ordenDespacho = OrdenDespacho::where('consecutivo','=',$request->consecutivo)->first();
        $orden=OrdenAlistamiento::where('ordendespacho_id','=',$ordenDespacho->id)->first();
        $userOpAlista = User::where('id','=',$orden->user_id_alista)->first();
        $orden->user_id_alista = $userOpAlista->names.' '.$userOpAlista->apellidos;
        return response()->json($orden);
    }
    
    public function quitarOperarioEmpacado(Request $request){
        
    }

    public function pendienteEmpacar(Request $request){
        return view('sisdespachos.ordenes.porempacar');
    }
    
    public function viewCurva(Request $request){
       $ordenDespacho = OrdenDespacho::where('consecutivo','=',$request->id)->first();
        $detalleorden = DetallesOrdenDespacho::where('despacho_id','=',$ordenDespacho->id)->get();
        //return response()->json($detalleorden);
        return view('sisdespachos.ordenes.viewcurva')->with('orden',$ordenDespacho)->with('detallesOrden',$detalleorden);
    }
    
    public function getPendientesPorAlistar(Request $request){
        return view('sisdespachos.ordenes.poralistar');
    }
    
    public function getDataPendienteAlistar(Request $request){
        $orden = OrdenDespacho::where('estado','=',1)->orWhere('estado','=',9)->orderBy('created_at', 'asc')->get();
        return response()->json($orden); 
    }
    
    public function getDataPendienteEmpacar(Request $request){
        $orden = OrdenDespacho::where('estado','=',3)->orWhere('estado','=',10)->orderBy('created_at', 'asc')->get();
        return response()->json($orden); 
    }
    
    public function getInformacionDespacho(Request $request){
        $ordenDespacho = OrdenDespacho::where('consecutivo','=',$request->consecutivo)->first();
        return response()->json($ordenDespacho);
    } 
    
    public function getCurvaDespacho(Request $request){
        $ordenDespacho = OrdenDespacho::where('consecutivo','=',$request->consecutivo)->first();
        $detallesorden = DetallesOrdenDespacho::where('despacho_id','=',$ordenDespacho->id)->get();
        return response()->json($detallesorden);
    }
    
    public function getCurvaAlistamientoConsecutivo(Request $request){
        $ordenDespacho = OrdenDespacho::where('consecutivo','=',$request->consecutivo)->first();
        $ordenAlistamiento = OrdenAlistamiento::where('ordendespacho_id',$ordenDespacho->id)->first();
        $detallesorden = DetallesOrdenAlistamiento::where('alistamiento_id','=',$ordenAlistamiento->id)->where('total','>',0)->get();
        return response()->json($detallesorden);
    }
    
    public function getOrdenAlistamiento(Request $request){
        $ordenDespacho = OrdenDespacho::where('consecutivo','=',$request->consecutivo)->first();
        $orden=OrdenAlistamiento::where('ordendespacho_id','=',$ordenDespacho->id)->first();
        if(isset($orden)){
            return response()->json(['Ya existe un operario alistando esta orden'],200);
        }
        return response()->json(['No hay operario en la orden'],200);
    }
    
    public function getOrdenEmpacado(Request $request){
        $ordenDespacho = OrdenDespacho::where('consecutivo','=',$request->consecutivo)->first();
        $orden=OrdenEmpacado::where('ordendespacho_id','=',$ordenDespacho->id)->first();
        if(isset($orden)){
            return response()->json(['Ya existe un operario empacando esta orden'],200);
        }
        return response()->json(['No hay operario en la orden'],200);
    }
    
    public function getCurvaAlistamiento(Request $request){
        $curva = DetallesOrdenAlistamiento::where('alistamiento_id','=',$request->alistamientoid)->get();
        return response()->json($curva);
    }
    
    public function getCurvaEmpacado(Request $request){
        $curva = DetallesOrdenEmpacado::where('empacado_id','=',$request->alistamientoid)->get();
        return response()->json($curva);
    }
    
    public function validaSiTieneOrden(){
        $userid = auth()->user()->id;
        $ordenAlistamiento = OrdenAlistamiento::where('user_id_alista','=',$userid)->where('estado','=','1')->first();
        if($ordenAlistamiento != null){
            $ordenDespacho = OrdenDespacho::where('id','=',$ordenAlistamiento->ordendespacho_id)->first();
            return response()->json([$ordenAlistamiento->id,$ordenDespacho->consecutivo]);
        } 
        return null;
    }
    
    public function validaSiTieneOrdenEmpacado(){
        $userid = auth()->user()->id;
        $ordenEmpacado = OrdenEmpacado::where('user_id_alista','=',$userid)->where('estado','=','1')->first();
        if($ordenEmpacado != null){
            $ordenDespacho = OrdenDespacho::where('id','=',$ordenEmpacado->ordendespacho_id)->first();
            //$array = DetallesOrdenDespacho::where('despacho_id','=',$ordenEmpacado->ordendespacho_id)->get();
            
            $ordenAlistamiento = OrdenAlistamiento::where('ordendespacho_id','=',$ordenEmpacado->ordendespacho_id)->first();
            $array = DetallesOrdenAlistamiento::where('alistamiento_id','=',$ordenAlistamiento->id)->get();
            
            $total=0;
            foreach ($array as $detalle) {
               $total += $detalle->total;
            }
            $totalEmpacado=0;
            $arraYEmpacado = DetallesOrdenEmpacado::where('empacado_id','=',$ordenEmpacado->id)->get();
            foreach ($arraYEmpacado as $arra) {
                $totalEmpacado += $arra->total;
            }
            
            return response()->json([$ordenEmpacado->id,$ordenDespacho->consecutivo,$total,$totalEmpacado,$ordenDespacho->cliente]);
        } 
        return null;
    }
    
    public function deleteAlistamiento(Request $request){
        $id_alistamiento = $request->ordenalistamiento;
        $ordena = OrdenAlistamiento::where('id','=',$id_alistamiento)->first();
        $detallesa = DetallesOrdenAlistamiento::where('alistamiento_id','=',$id_alistamiento)->get();
        $ordendespacho = OrdenDespacho::where('id','=',$ordena->ordendespacho_id)->first();
        $ordendespacho->estado=9;
        $ordendespacho->save();

        $ordena->delete();
        foreach ($detallesa as $detalle) {
            $detalle->delete();
        }
        return response()->json(['message' => 'Se ha eliminado la orden de alistamiento.'],200);
    }
    
    public function deleteEmpacado(Request $request){
        $id_empacado = $request->idordenEmpa;
        $this->desbloquearEmpacadoOperario($id_empacado);
    }
    
    public function deleteOperarioEmpacado(Request $request){
        $id_empacado = $request->idordenEmpa;
        $this->desbloquearEmpacadoOperario($id_empacado);
    }
    
    public function quitarOperarioDelaOrden(Request $request){
        $consecutivo = $request->consecutivoOrden;
        $ordendespacho = OrdenDespacho::where('consecutivo','=',$consecutivo)->first();
        $ordenae = OrdenEmpacado::where('ordendespacho_id','=',$ordendespacho->id)->first();
        if($ordenae != null){
            $idordene = $ordenae->id;
            $this->desbloquearEmpacadoOperario($idordene);
            return true;
        }
        return false;
    }
    
    
    private function desbloquearEmpacadoOperario($idOrdenEmpacado){
        $ordena = OrdenEmpacado::where('id','=',$idOrdenEmpacado)->first();
        $detallesa = DetallesOrdenEmpacado::where('empacado_id','=',$idOrdenEmpacado)->get();
        $ordendespacho = OrdenDespacho::where('id','=',$ordena->ordendespacho_id)->first();
        $ordendespacho->estado=10;
        $ordendespacho->save();

        $empaques = Empaque::where('empacado_id','=',$ordena->id)->get();
        foreach($empaques as $empaque){
            $detalles = DetallesEmpaque::where('empaque_id','=',$empaque->id)->get();
                foreach($detalles as $detalle){
                    $detalle->delete();
                }
            $empaque->delete();
        }
        
        foreach ($detallesa as $detalle) {
            $detalle->delete();
        }
        $ordena->delete();
        return response()->json(['message' => 'Se ha eliminado la orden de empacado.'],200);
    }
    
    public function createEmpacado(Request $request){
        $ordenDespacho = OrdenDespacho::where('consecutivo','=',$request->consecutivo)->first();
        if($ordenDespacho->estado == 4 ){
            $ordenempacado = OrdenEmpacado::where('ordendespacho_id','=',$ordenDespacho->id)->first();
            $user = User::where('id','=',$ordenempacado->user_id_alista)->first();
            return response()->json(['message' => 'La orden ya esta siendo empacada por: '.$user->names.' '.$user->apellidos],500);
        }else{
            $ordenAlistamiento = OrdenAlistamiento::where('ordendespacho_id','=',$ordenDespacho->id)->first();
            $detallesorden = DetallesOrdenAlistamiento::where('alistamiento_id','=',$ordenAlistamiento->id)->get();
            //$detallesorden = DetallesOrdenDespacho::where('despacho_id','=',$ordenDespacho->id)->get();
            $ordenDespacho->estado = 4;
            $ordenDespacho->save();
            $ordenempacado = OrdenEmpacado::create([
                'ordendespacho_id' => $ordenDespacho->id,
                'user_id_alista' => auth()->user()->id,
                'estado' => 1,
                'user_id_rechaza' => 0
            ]);
            foreach($detallesorden as $detalleorden){
                DetallesOrdenEmpacado::create([
                    'empacado_id' => $ordenempacado->id,
                    'id_amarrador' => $detalleorden->id_amarrador,
                    'pedido_id' => $detalleorden->pedido_id,
                    'referencia' => $detalleorden->referencia,
                    't4' => 0,
                    't6' => 0,
                    't8' => 0,
                    't10' => 0,
                    't12' => 0,
                    't14' => 0,
                    't16' => 0,
                    't18' => 0,
                    't20' => 0,
                    't22' => 0,
                    't24' => 0,
                    't26' => 0,
                    't28' => 0,
                    't30' => 0,
                    't32' => 0,
                    't34' => 0,
                    't36' => 0,
                    't38' => 0,
                    's' => 0,
                    'm' => 0,
                    'l' => 0,
                    'xl' => 0,
                    'total' => 0,
                    'estado' => 1
                ]);
            }
            return response()->json($ordenempacado);
        }
        return dump($ordenDespacho);
    }
    
    public function createAlistamiento(Request $request){
        $ordenDespacho = OrdenDespacho::where('consecutivo','=',$request->consecutivo)->first();
        if($ordenDespacho->estado == 2 ){
            $ordenalistamiento = OrdenAlistamiento::where('ordendespacho_id','=',$ordenDespacho->id)->first();
            $user = User::where('id','=',$ordenalistamiento->user_id_alista)->first();
            return response()->json(['message' => 'La orden ya esta siendo alistada por: '.$user->names.' '.$user->apellidos],500);
        }else{
            $detallesorden = DetallesOrdenDespacho::where('despacho_id','=',$ordenDespacho->id)->get();
        
            $ordenDespacho->estado = 2;
            $ordenDespacho->save();
            $ordenalistamiento = OrdenAlistamiento::create([
                'ordendespacho_id' => $ordenDespacho->id,
                'user_id_alista' => auth()->user()->id,
                'estado' => 1,
                'user_id_rechaza' => 0
            ]);
            foreach($detallesorden as $detalleorden){
                DetallesOrdenAlistamiento::create([
                    'alistamiento_id' => $ordenalistamiento->id,
                    'id_amarrador' => $detalleorden->id,
                    'pedido_id' => $detalleorden->pedido_id,
                    'referencia' => $detalleorden->referencia,
                    't4' => 0,
                    't6' => 0,
                    't8' => 0,
                    't10' => 0,
                    't12' => 0,
                    't14' => 0,
                    't16' => 0,
                    't18' => 0,
                    't20' => 0,
                    't22' => 0,
                    't24' => 0,
                    't26' => 0,
                    't28' => 0,
                    't30' => 0,
                    't32' => 0,
                    't34' => 0,
                    't36' => 0,
                    't38' => 0,
                    's' => 0,
                    'm' => 0,
                    'l' => 0,
                    'xl' => 0,
                    'total' => 0,
                    'estado' => 1
                ]);
            }
            return response()->json($ordenalistamiento);
        }
    }
    
    public function cargaMasiva(Request $request){
        $file = $request->file;
        $orden = new OrdenImport();
        $ts=Excel::import($orden,$file);
        $items=json_decode(json_encode($orden->sheetData[0]));
        $cargados = 0;
        $detalles = 0;
        $au = 0;
        for($i=0;$i<count($items);$i++){
            $orden = OrdenDespacho::where('consecutivo','=',$items[$i]->Consecutivo)->first();
            if($orden == null){
                $ordenDespacho = OrdenDespacho::create([
                    'consecutivo' => $items[$i]->Consecutivo,
                    'cliente' => $items[$i]->Cliente,
                    'nit' => $items[$i]->Nit,
                    'prioridad' => $items[$i]->Prioridad,
                    'observacion' => $items[$i]->Observacion,
                    'ciudad' => $items[$i]->Ciudad, 
                    'direccion' => $items[$i]->Direccion,
                    'identificador' => 'NA',
                    'estado' => 1
                ]);

                $vendedorExcel = $items[$i]->Vendedor;
                $arrayVendedor = explode(" ",$vendedorExcel);
                $vendedor = $vendedorExcel;
                if(count($arrayVendedor) == 4){
                    $vendedor = $arrayVendedor[0].' '.$arrayVendedor[2];
                }



                DetallesOrdenDespacho::create([
                    'id' => $items[$i]->ID,
                    'vendedor' => $vendedor,
                    'despacho_id' => $ordenDespacho->id,
                    'pedido_id' => $items[$i]->Ped,
                    'referencia' => $items[$i]->Referencia,
                    't4' => str_replace('-','',$items[$i]->T4),
                    't6' => str_replace('-','',$items[$i]->T6),
                    't8' => str_replace('-','',$items[$i]->T8),
                    't10' => str_replace('-','',$items[$i]->T10),
                    't12' => str_replace('-','',$items[$i]->T12),
                    't14' => str_replace('-','',$items[$i]->T14),
                    't16' => str_replace('-','',$items[$i]->T16),
                    't18' => str_replace('-','',$items[$i]->T18),
                    't20' => str_replace('-','',$items[$i]->T20),
                    't22' => str_replace('-','',$items[$i]->T22),
                    't24' => str_replace('-','',$items[$i]->T24),
                    't26' => str_replace('-','',$items[$i]->T26),
                    't28' => str_replace('-','',$items[$i]->T28),
                    't30' => str_replace('-','',$items[$i]->T30),
                    't32' => str_replace('-','',$items[$i]->T32),
                    't34' => str_replace('-','',$items[$i]->T34),
                    't36' => str_replace('-','',$items[$i]->T36),
                    't38' => str_replace('-','',$items[$i]->T38),
                    's' => str_replace('-','',$items[$i]->S),
                    'm' => str_replace('-','',$items[$i]->M),
                    'l' => str_replace('-','',$items[$i]->L),
                    'xl' => str_replace('-','',$items[$i]->XL),
                    'total' => str_replace('-','',$items[$i]->Total)
                ]);
                $cargados++;
                $detalles++;
            }else{
                $vendedorExcel = $items[$i]->Vendedor;
                $arrayVendedor = explode(" ",$vendedorExcel);
                $vendedor = $vendedorExcel;
                if(count($arrayVendedor) == 4){
                    $vendedor = $arrayVendedor[0].' '.$arrayVendedor[2];
                }
                DetallesOrdenDespacho::create([
                    'id' => $items[$i]->ID,
                    'despacho_id' => $orden->id,
                    'vendedor' => $vendedor,
                    'pedido_id' => $items[$i]->Ped,
                    'referencia' => $items[$i]->Referencia,
                    't4' => str_replace('-','',$items[$i]->T4),
                    't6' => str_replace('-','',$items[$i]->T6),
                    't8' => str_replace('-','',$items[$i]->T8),
                    't10' => str_replace('-','',$items[$i]->T10),
                    't12' => str_replace('-','',$items[$i]->T12),
                    't14' => str_replace('-','',$items[$i]->T14),
                    't16' => str_replace('-','',$items[$i]->T16),
                    't18' => str_replace('-','',$items[$i]->T18),
                    't20' => str_replace('-','',$items[$i]->T20),
                    't22' => str_replace('-','',$items[$i]->T22),
                    't24' => str_replace('-','',$items[$i]->T24),
                    't26' => str_replace('-','',$items[$i]->T26),
                    't28' => str_replace('-','',$items[$i]->T28),
                    't30' => str_replace('-','',$items[$i]->T30),
                    't32' => str_replace('-','',$items[$i]->T32),
                    't34' => str_replace('-','',$items[$i]->T34),
                    't36' => str_replace('-','',$items[$i]->T36),
                    't38' => str_replace('-','',$items[$i]->T38),
                    's' => str_replace('-','',$items[$i]->S),
                    'm' => str_replace('-','',$items[$i]->M),
                    'l' => str_replace('-','',$items[$i]->L),
                    'xl' => str_replace('-','',$items[$i]->XL),
                    'total' => str_replace('-','',$items[$i]->Total)
                ]);
                $detalles++;
            }

            $dpp = DetallesPedido::where('id','=',$items[$i]->ID)->first();
            if($dpp != null){
                $dpp->despacho = 'Comprometido';
                $dpp->filtro = date("Y-m-d H:i:s");
                $dpp->save();
                $au++;
            }
           
        }           
        return response()->json(['message' => 'Se han cargado'.$cargados.' Ordenes de despacho con un total de '.$detalles.' detalles de ordenes y se han marcado '.$au.' detalles de pedidos en BLESS VENTAS.'],200);
    }
 
}