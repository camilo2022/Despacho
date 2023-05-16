<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Exports\FiltroExport;
use App\Exports\FacturacionMedellinExport;
use App\Exports\OrdenesExport;
use App\Exports\ArchivoPrimarioExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\PayUService\Exception;
use App\Imports\ImportExcel;
use App\Http\Requests\JsonRequest;
use App;
use PDF;

class FiltroMedellinController extends Controller
{
    public function indexOrdenesClientes()
    {
        $ordenesClientes = DB::select('select * from zarethpr_produccion.orden_despacho');
        $fecha = Carbon::now()->format('Y-m-d');
        return view('filtro_medellin.indexOrdenesDespachoClientes',compact('ordenesClientes','fecha'));
    }

    public function viewOrdenesClientes($consecutivo)
    {
        $cliente = DB::select('select zarethpr_produccion.despacho.nit, zarethpr_produccion.despacho.cliente, zarethpr_produccion.despacho.sucursal, 
        zarethpr_produccion.despacho.zona, zarethpr_produccion.despacho.ciudad, zarethpr_produccion.despacho.direccion, zarethpr_produccion.despacho.estado_orden
        from zarethpr_produccion.despacho where zarethpr_produccion.despacho.consecutivo = '.$consecutivo.'
        GROUP BY zarethpr_produccion.despacho.nit, zarethpr_produccion.despacho.cliente, zarethpr_produccion.despacho.sucursal 
        HAVING COUNT(*)>0 ORDER BY zarethpr_produccion.despacho.cliente ASC');
        if(count($cliente) > 0){
            $cliente = $cliente[0];
        }else{
        $cliente = DB::select('select * from zarethpr_produccion.orden_despacho where zarethpr_produccion.orden_despacho.consecutivo = '.$consecutivo);
        $cliente = $cliente[0];
        }
        $detallesOrdenCliente = DB::select('select * from zarethpr_produccion.despacho where zarethpr_produccion.despacho.consecutivo = '.$consecutivo);
        return view('filtro_medellin.viewOrdenDespachoCliente',compact('cliente','detallesOrdenCliente','consecutivo'));
    }

    public function printOrdenesDespacho()
    {
        $ordenes = [];
        $ordenesDespacho = DB::select('SELECT * FROM zarethpr_produccion.orden_despacho WHERE (zarethpr_produccion.orden_despacho.estado = "FACTURANDO" OR zarethpr_produccion.orden_despacho.estado = "ALISTANDO") AND zarethpr_produccion.orden_despacho.fecha = "'.Carbon::now()->format('Y-m-d').'"');
        for ($i=0; $i <count($ordenesDespacho) ; $i++) { 
            $detalles = [];
            $detallesOrdenDespacho = DB::select('SELECT * FROM zarethpr_produccion.detalles_orden_despacho WHERE zarethpr_produccion.detalles_orden_despacho.id_orden_despacho = '.$ordenesDespacho[$i]->id.'
            AND (zarethpr_produccion.detalles_orden_despacho.estado = "FACTURAR" OR zarethpr_produccion.detalles_orden_despacho.estado = "ALISTAR")');
            for ($j=0; $j <count($detallesOrdenDespacho) ; $j++) { 
                $detalles[$j] = $detallesOrdenDespacho[$j];
            }
            if(count($detallesOrdenDespacho)>0){
                $observaciones = DB::select('select * from zarethpr_produccion.ordens where zarethpr_produccion.ordens.id = '.$detallesOrdenDespacho[0]->id_pedido);
                $ordenesDespacho[$i]->obs = $observaciones[0]->observacion;
                $ordenesDespacho[$i]->obscartera = "";
            }else{
                $ordenesDespacho[$i]->obs = "";
                $ordenesDespacho[$i]->obscartera = "";
            }
            $ordenes[$i][0] = $ordenesDespacho[$i];
            $ordenes[$i][1] = $detalles;
        }
        //$pdf = PDF::loadView('filtro.printOrdenesDespacho',compact('ordenes'))->setOptions(['defaultFont' => 'sans-serif']);;
        //return $pdf->download('Ordenes Despacho.pdf');
        return view('filtro_medellin.printOrdenesDespacho',compact('ordenes'));
    }

    public function printOrdenesDespachoActualizadas()
    {
        $ordenes = [];
        $fecha = Carbon::now()->format('Y-m-d');
        $ordenesDespacho = DB::select('SELECT * FROM zarethpr_produccion.orden_despacho WHERE (zarethpr_produccion.orden_despacho.estado = "PREPARANDO" OR zarethpr_produccion.orden_despacho.estado = "ALISTANDO") AND
        DATE_FORMAT(zarethpr_produccion.orden_despacho.updated_at, "%Y-%m-%d") = "'.$fecha.'" AND zarethpr_produccion.orden_despacho.updated_at != zarethpr_produccion.orden_despacho.created_at');
        for ($i=0; $i <count($ordenesDespacho) ; $i++) { 
            $detalles = [];
            $detallesOrdenDespacho = DB::select('SELECT * FROM zarethpr_produccion.detalles_orden_despacho WHERE zarethpr_produccion.detalles_orden_despacho.id_orden_despacho = '.$ordenesDespacho[$i]->id.'
            AND (zarethpr_produccion.detalles_orden_despacho.estado = "APROBADO" OR zarethpr_produccion.detalles_orden_despacho.estado = "ALISTAR")');
            for ($j=0; $j <count($detallesOrdenDespacho) ; $j++) { 
                $detalles[$j] = $detallesOrdenDespacho[$j];
            }
            if(count($detallesOrdenDespacho)>0){
                $observaciones = DB::select('select * from zarethpr_produccion.ordens where zarethpr_produccion.ordens.id = '.$detallesOrdenDespacho[0]->id_pedido);
                $ordenesDespacho[$i]->obs = $observaciones[0]->observacion;
                $ordenesDespacho[$i]->obscartera = "";
            }else{
                $ordenesDespacho[$i]->obs = "";
                $ordenesDespacho[$i]->obscartera = "";
            }
            $ordenes[$i][0] = $ordenesDespacho[$i];
            $ordenes[$i][1] = $detalles;
        }
        //$pdf = PDF::loadView('filtro.printOrdenesDespacho',compact('ordenes'))->setOptions(['defaultFont' => 'sans-serif']);;
        //return $pdf->download('Ordenes Despacho.pdf');
        return view('filtro_medellin.printOrdenesDespachoActualizadas',compact('ordenes','fecha'));
    }

    public function excelDownloadOrdenesFacturar()
    {
        $ordenes = DB::select('SELECT * from zarethpr_produccion.despacho where (zarethpr_produccion.despacho.estado_orden = "FACTURANDO" AND 
        zarethpr_produccion.despacho.estado_detalle_orden = "FACTURAR") OR (zarethpr_produccion.despacho.estado_orden = "ALISTANDO" AND 
        zarethpr_produccion.despacho.estado_detalle_orden = "ALISTAR")');
        return Excel::download(new FacturacionMedellinExport($ordenes),"ORDENES FACTURAR.xlsx");
    }

    public function printOrdenesClientes($consecutivo)
    {
        $cliente = DB::select('select * from zarethpr_produccion.orden_despacho where zarethpr_produccion.orden_despacho.consecutivo = '.$consecutivo);
        $cliente = $cliente[0];
        $detallesOrdenCliente = DB::select('select * from zarethpr_produccion.despacho where (zarethpr_produccion.despacho.estado_detalle_orden = "APROBADO" OR zarethpr_produccion.despacho.estado_detalle_orden = "DESPACHADO"
        OR zarethpr_produccion.despacho.estado_detalle_orden = "FACTURAR" OR zarethpr_produccion.despacho.estado_detalle_orden = "ALISTAR") and zarethpr_produccion.despacho.consecutivo = '.$consecutivo);
        if(count($detallesOrdenCliente)>0){
            $observaciones = DB::select('select * from zarethpr_produccion.ordens where zarethpr_produccion.ordens.id = '.$detallesOrdenCliente[0]->id_pedido);
            $cliente->obs = $observaciones[0]->observacion;
            $cliente->obscartera = "";
        }else{
            $cliente->obs = "";
            $cliente->obscartera = "";
        }
        return view('filtro_medellin.printOrdenDespachoCliente',compact('cliente','detallesOrdenCliente','consecutivo'));
    }

    public function facturarOrdenesClientes($id)
    {
        $consulta = DB::select('SELECT * FROM zarethpr_produccion.detalles_orden_despacho WHERE zarethpr_produccion.detalles_orden_despacho.id_orden_despacho = '.$id.' 
        AND zarethpr_produccion.detalles_orden_despacho.estado = "ALISTAR"');
        DB::table('zarethpr_produccion.orden_despacho')->where('id','=',$id)->update(['estado'=>'FACTURANDO']);
        for ($i=0;$i<count($consulta);$i++) { 
            DB::table('zarethpr_produccion.detalles_orden_despacho')->where('id','=',$consulta[$i]->id)->update(['estado'=>'FACTURAR']);
        }
        return back();
    }

    public function alistarOrdenesClientes($id)
    {
        $consulta = DB::select('SELECT * FROM zarethpr_produccion.detalles_orden_despacho WHERE zarethpr_produccion.detalles_orden_despacho.id_orden_despacho = '.$id.' 
        AND zarethpr_produccion.detalles_orden_despacho.estado = "APROBADO"');
        DB::table('zarethpr_produccion.orden_despacho')->where('id','=',$id)->update(['estado'=>'ALISTANDO']);
        for ($i=0;$i<count($consulta);$i++) { 
            DB::table('zarethpr_produccion.detalles_orden_despacho')->where('id','=',$consulta[$i]->id)->update(['estado'=>'ALISTAR']);
        }
        return back();
    }

    public function reversarOrdenesClientes($id)
    {
        $consulta = DB::select('SELECT * FROM zarethpr_produccion.detalles_orden_despacho WHERE zarethpr_produccion.detalles_orden_despacho.id_orden_despacho = '.$id.' 
        AND zarethpr_produccion.detalles_orden_despacho.estado = "ALISTAR"');
        DB::table('zarethpr_produccion.orden_despacho')->where('id','=',$id)->update(['estado'=>'PREPARANDO']);
        for ($i=0;$i<count($consulta);$i++) { 
            DB::table('zarethpr_produccion.detalles_orden_despacho')->where('id','=',$consulta[$i]->id)->update(['estado'=>'APROBADO']);
        }
        return back();
    }

    public function despacharOrdenesClientes($id)
    {
        $fechades = Carbon::now();
        $consulta = DB::select('SELECT * FROM zarethpr_produccion.detalles_orden_despacho WHERE zarethpr_produccion.detalles_orden_despacho.id_orden_despacho = '.$id.'
        AND zarethpr_produccion.detalles_orden_despacho.estado = "FACTURAR"');
        DB::table('zarethpr_produccion.orden_despacho')->where('id','=',$id)->update(['estado'=>'DESPACHADO']);
        for ($i=0;$i<count($consulta);$i++) { 
            DB::table('zarethpr_produccion.ordenrefs')->where('id','=',$consulta[$i]->id_amarrador)->update(['despachor'=>1, 'fechades'=>$fechades]);
            DB::table('zarethpr_produccion.detalles_orden_despacho')->where('id','=',$consulta[$i]->id)->update(['estado'=>'DESPACHADO']);
        }
        return back();
    }

    public function cancelarOrdenesClientes($id)
    {
        $consulta = DB::select('SELECT * FROM zarethpr_produccion.detalles_orden_despacho WHERE zarethpr_produccion.detalles_orden_despacho.id_orden_despacho = '.$id);
        DB::table('zarethpr_produccion.orden_despacho')->where('id','=',$id)->update(['estado'=>'CANCELADO']);
        for ($i=0;$i<count($consulta);$i++) { 
            DB::table('zarethpr_produccion.ordenrefs')->where('id','=',$consulta[$i]->id_amarrador)->update(['despachor'=>4]);
            DB::table('zarethpr_produccion.detalles_orden_despacho')->where('id','=',$consulta[$i]->id)->update(['estado'=>'CANCELADO']);
        }
        return back();
    }

    public function aprobarDetalleOrdenCliente($id, $amarrador)
    {
        DB::table('zarethpr_produccion.detalles_orden_despacho')->where('id','=',$id)->update(['estado'=>'APROBADO']);
        DB::table('zarethpr_produccion.ordenrefs')->where('id','=',$amarrador)->update(['despachor'=>12]);
        return back();
    }

    public function cancelarDetalleOrdenCliente($id, $amarrador)
    {
        DB::table('zarethpr_produccion.detalles_orden_despacho')->where('id','=',$id)->update(['estado'=>'CANCELADO']);
        DB::table('zarethpr_produccion.ordenrefs')->where('id','=',$amarrador)->update(['despachor'=>4]);
        return back();
    }

    public function pendienteDetalleOrdenCliente($id, $amarrador)
    {
        DB::table('zarethpr_produccion.detalles_orden_despacho')->where('id','=',$id)->update(['estado'=>'PENDIENTE']);
        DB::table('zarethpr_produccion.ordenrefs')->where('id','=',$amarrador)->update(['despachor'=>0]);
        return back();
    }

    public function editDetalleOrdenCliente($consecutivo, $amarrador)
    {
        $cliente = DB::select('select zarethpr_produccion.despacho.nit, zarethpr_produccion.despacho.cliente, zarethpr_produccion.despacho.sucursal, 
        zarethpr_produccion.despacho.zona, zarethpr_produccion.despacho.ciudad, zarethpr_produccion.despacho.direccion, zarethpr_produccion.despacho.estado_orden
        from zarethpr_produccion.despacho where zarethpr_produccion.despacho.consecutivo = '.$consecutivo.'
        GROUP BY zarethpr_produccion.despacho.nit, zarethpr_produccion.despacho.cliente, zarethpr_produccion.despacho.sucursal 
        HAVING COUNT(*)>0 ORDER BY zarethpr_produccion.despacho.cliente ASC');
        $cliente = $cliente[0];
        $detalleOrdenCliente = DB::select('select * from zarethpr_produccion.despacho where zarethpr_produccion.despacho.consecutivo = "'.$consecutivo.'" 
        and zarethpr_produccion.despacho.id_amarrador = '.$amarrador);
        $detalleOrdenCliente = $detalleOrdenCliente[0];
        return view('filtro_medellin.editDetalleOrdenDespachoCliente',compact('cliente','detalleOrdenCliente','consecutivo','amarrador'));
    }

    public function updateDetalleOrdenCliente(Request $request, $consecutivo, $amarrador, $id)
    {
        $updated_at = Carbon::now();
        DB::table('zarethpr_produccion.detalles_orden_despacho')->where('id','=',$id)->update(['t04'=>$request->t04, 't06'=>$request->t06, 
            't08'=>$request->t08, 't10'=>$request->t10, 't12'=>$request->t12, 't14'=>$request->t14, 't16'=>$request->t16, 
            't18'=>$request->t18, 't20'=>$request->t20, 't22'=>$request->t22, 't24'=>$request->t24, 't28'=>$request->t28, 
            't30'=>$request->t30, 't32'=>$request->t32, 't34'=>$request->t34, 't36'=>$request->t36, 't38'=>$request->t38, 
            'updated_at'=>$updated_at]);
        return redirect()->route('filtro.medellin.listado.ordenes.clientes.view', $consecutivo);
    }

    private function consultaReferenciasFiltrarPedidos()
    {
        $consulta = DB::select('select UPPER(zarethpr_produccion.filtrado.referencia) AS referencia, zarethpr_produccion.filtrado.marca, 
        sum(zarethpr_produccion.filtrado.t04 + zarethpr_produccion.filtrado.t06 + zarethpr_produccion.filtrado.t08 + zarethpr_produccion.filtrado.t10
        + zarethpr_produccion.filtrado.t12 + zarethpr_produccion.filtrado.t14 + zarethpr_produccion.filtrado.t16 + zarethpr_produccion.filtrado.t18
        + zarethpr_produccion.filtrado.t20 + zarethpr_produccion.filtrado.t22 + zarethpr_produccion.filtrado.t24 + zarethpr_produccion.filtrado.t28 
        + zarethpr_produccion.filtrado.t30 + zarethpr_produccion.filtrado.t32 + zarethpr_produccion.filtrado.t34 + zarethpr_produccion.filtrado.t36 
        + zarethpr_produccion.filtrado.t38) AS sum_tallas from zarethpr_produccion.filtrado 
        where zarethpr_produccion.filtrado.despachor = 0 and zarethpr_produccion.filtrado.aprobado = 1 and zarethpr_produccion.filtrado.estado = "Pedido"
        GROUP BY zarethpr_produccion.filtrado.referencia HAVING COUNT(*)>0 ORDER BY sum_tallas DESC');
        return $consulta;
    }

    public function referencias()
    {
        $fecha = Carbon::now()->format('Y-m-d');
        $referencias = $this->consultaReferenciasFiltrarPedidos();
        $ref_filtradas = DB::select('select zarethpr_produccion.control_referencia_filtrada.referencia FROM zarethpr_produccion.control_referencia_filtrada WHERE fecha = DATE_FORMAT(NOW(), "%Y/%m/%d")');
        if(count($referencias) > 0){
            /*for ($i=0; $i<count($referencias); $i++) {  
                for ($j=0; $j<count($ref_filtradas); $j++) { 
                    if($ref_filtradas[$j]->referencia == $referencias[$i]->referencia){
                        unset($referencias[$i]);
                        break;
                    }
                }
            }
            $referencias = array_values($referencias);*/
            $ult_ref_fil = DB::select('select id, referencia FROM zarethpr_produccion.control_referencia_filtrada where fecha = "'.$fecha.'"');
            /*
            $refes = DB::select('select * from zarethpr_produccion.inventario_sistemas where fecha = (SELECT MAX(fecha) FROM zarethpr_produccion.inventario_sistemas) 
            AND zarethpr_produccion.inventario_sistemas.bodega = "BMEC" AND zarethpr_produccion.inventario_sistemas.existencia != 0');
            foreach ($refes as $r){
                $array = explode("-", $r->referencia);
                if(count($array) == 2){
                    $r->referencia = $array[0];
                    $r->talla = $array[1];
                    $r->color = "00";
                }
                if(count($array) == 3){
                    $r->referencia = $array[0];
                    $r->talla = $array[1];
                    $r->color = $array[2];
                }elseif(count($array) == 4){
                    $r->referencia = $array[0]."-".$array[1];
                    $r->talla = $array[2];
                    $r->color = $array[3];
                }
            }
            
            for ($i=0; $i<count($referencias); $i++) {
                $bolean = false;
                for ($j=0; $j<count($refes); $j++) { 
                    if($referencias[$i]->referencia == $refes[$j]->referencia){
                        unset($refes[$j]);
                        $refes = array_values($refes);
                        $bolean = true;
                        break;
                    }
                }
                if($bolean == false){
                    unset($referencias[$i]);  
                }
            }*/
            $referencias = array_values($referencias);
            return view('filtro_medellin.all', compact('referencias','ult_ref_fil'));
        }
        else{
            return "NO HAY REFERENCIAS A FILTRAR EN LA CORRERIA ACTUAL";
        }
    }

    private function deleteDuplicateObjects($arrayObjects){
        $newArray = [];
        for ($i=0; $i < count($arrayObjects); $i++) { 
            $existe = false;     
            for ($j=0; $j < count($newArray); $j++) {
                if($newArray[$j]->IdBodega == $arrayObjects[$i]->IdBodega && $newArray[$j]->CodBarras == $arrayObjects[$i]->CodBarras){
                    $existe = true;
                    break;
                }
            }
            if(!$existe){
                $newArray[] = $arrayObjects[$i];
            }
        }
        return $newArray;
    }

    public function getPedidosAndInventario(Request $request)
    {
        //$request->referencia = strtoupper($request->referencia);
        //$correria = DB::select('select zarethpr_produccion.correrias.codigo from zarethpr_produccion.correrias where estado = 1');
        $pedidos = DB::select('select * from zarethpr_produccion.filtrado where zarethpr_produccion.filtrado.despachor = 0 and 
        zarethpr_produccion.filtrado.aprobado = 1 and zarethpr_produccion.filtrado.estado = "Pedido"
        and zarethpr_produccion.filtrado.referencia = "'.$request->referencia.'"');
        $alertSIESA = ""; $alertTNS = ""; $alertBMI = "";
        $bodegas = [
            "t04" => 0, "t06" => 0, "t08" => 0, "t10" => 0, 
            "t12" => 0, "t14" => 0, "t16" => 0, "t18" => 0, 
            "t20" => 0, "t22" => 0, "t28" => 0, "t30" => 0, 
            "t32" => 0, "t34" => 0, "t36" => 0, "t38" => 0,
            "total" => 0
        ];
        $referencia = [
            "t04" => 0, "t06" => 0, "t08" => 0, "t10" => 0, 
            "t12" => 0, "t14" => 0, "t16" => 0, "t18" => 0, 
            "t20" => 0, "t22" => 0, "t28" => 0, "t30" => 0, 
            "t32" => 0, "t34" => 0, "t36" => 0, "t38" => 0,
            "total" => 0
        ];
        
        //CONSULTA DISPONIBLE A SIESA
        $alertSIESA = "danger";
        //CONSULTA DISPONIBLE A TNS
        try{

            $refTnsBodMED = DB::connection('firebird')->select("SELECT B.CODIGO CODBOD , B.NOMBRE NOMBOD , M.CODIGO , M.DESCRIP , SM.EXISTENC
            FROM MATERIAL M INNER JOIN SALMATERIAL SM ON (SM.MATID = M.MATID) INNER JOIN BODEGA B ON (B.BODID = SM.BODID)
            WHERE M.CODIGO LIKE '".$request->referencia."%' AND B.CODIGO = 'BMEC'");

            foreach($refTnsBodMED as $rtns){
                $array = explode("-", $rtns->CODIGO);
                if(count($array) == 3 || count($array) == 2){
                    if($array[0] == $request->referencia){
                        switch ($array[1]) {
                            case "04":
                                $referencia["t04"]+= intval($rtns->EXISTENC);
                                break;
                            case "06":
                                $referencia["t06"]+= intval($rtns->EXISTENC);
                                break;
                            case "08":
                                $referencia["t08"]+= intval($rtns->EXISTENC);
                                break;
                            case "10":
                                $referencia["t10"]+= intval($rtns->EXISTENC);
                                break;
                            case "12":
                                $referencia["t12"]+= intval($rtns->EXISTENC);
                                break;
                            case "14":
                                $referencia["t14"]+= intval($rtns->EXISTENC);
                                break;
                            case "16":
                                $referencia["t16"]+= intval($rtns->EXISTENC);
                                break;
                            case "18":
                                $referencia["t18"]+= intval($rtns->EXISTENC);
                                break;
                            case "20":
                                $referencia["t20"]+= intval($rtns->EXISTENC);
                                break;
                            case "22":
                                $referencia["t22"]+= intval($rtns->EXISTENC);
                                break;
                            case "28":
                                $referencia["t28"]+= intval($rtns->EXISTENC);
                                break;
                            case "30":
                                $referencia["t30"]+= intval($rtns->EXISTENC);
                                break;
                            case "32":
                                $referencia["t32"]+= intval($rtns->EXISTENC);
                                break;
                            case "34":
                                $referencia["t34"]+= intval($rtns->EXISTENC);
                                break;
                            case "36":
                                $referencia["t36"]+= intval($rtns->EXISTENC);
                                break;
                            case "38":
                                $referencia["t38"]+= intval($rtns->EXISTENC);
                                break;
                        }
                        $referencia["total"]+= intval($rtns->EXISTENC);
                    }
                }elseif(count($array) == 4){
                    if($array[0]."-".$array[1] == $request->referencia){
                        switch ($array[2]) {
                            case "04":
                                $referencia["t04"]+= intval($rtns->EXISTENC);
                                break;
                            case "06":
                                $referencia["t06"]+= intval($rtns->EXISTENC);
                                break;
                            case "08":
                                $referencia["t08"]+= intval($rtns->EXISTENC);
                                break;
                            case "10":
                                $referencia["t10"]+= intval($rtns->EXISTENC);
                                break;
                            case "12":
                                $referencia["t12"]+= intval($rtns->EXISTENC);
                                break;
                            case "14":
                                $referencia["t14"]+= intval($rtns->EXISTENC);
                                break;
                            case "16":
                                $referencia["t16"]+= intval($rtns->EXISTENC);
                                break;
                            case "18":
                                $referencia["t18"]+= intval($rtns->EXISTENC);
                                break;
                            case "20":
                                $referencia["t20"]+= intval($rtns->EXISTENC);
                                break;
                            case "22":
                                $referencia["t22"]+= intval($rtns->EXISTENC);
                                break;
                            case "28":
                                $referencia["t28"]+= intval($rtns->EXISTENC);
                                break;
                            case "30":
                                $referencia["t30"]+= intval($rtns->EXISTENC);
                                break;
                            case "32":
                                $referencia["t32"]+= intval($rtns->EXISTENC);
                                break;
                            case "34":
                                $referencia["t34"]+= intval($rtns->EXISTENC);
                                break;
                            case "36":
                                $referencia["t36"]+= intval($rtns->EXISTENC);
                                break;
                            case "38":
                                $referencia["t38"]+= intval($rtns->EXISTENC);
                                break;
                        }
                        $referencia["total"]+= intval($rtns->EXISTENC);
                    }
                }
            }
            $alertTNS = "success";
            
        } catch (\Exception $e) {
            $alertTNS = "warning";
            $refTns = DB::select('select * from zarethpr_produccion.inventario_sistemas where zarethpr_produccion.inventario_sistemas.sistema = 
            "VISUAL TNS" and fecha = "'.Carbon::now()->format('Y-m-d').'" and referencia LIKE "%'.$request->referencia.'%"');
            if(count($refTns) == 0){
                $alertTNS = "danger";
                $refTns = DB::select('select * from zarethpr_produccion.inventario_sistemas where zarethpr_produccion.inventario_sistemas.sistema = 
                "VISUAL TNS" and fecha = (SELECT MAX(fecha) FROM zarethpr_produccion.inventario_sistemas) and referencia LIKE "%'.$request->referencia.'%"');
            }

            foreach($refTns as $rtns){
                $array = explode("-", $rtns->referencia);
                if($rtns->bodega == "BMEC"){
                    if(count($array) == 3 || count($array) == 2){
                        if($array[0] == $request->referencia){
                            switch ($array[1]) {
                                case "04":
                                    $referencia["t04"]+= $rtns->existencia;
                                    break;
                                case "06":
                                    $referencia["t06"]+= $rtns->existencia;
                                    break;
                                case "08":
                                    $referencia["t08"]+= $rtns->existencia;
                                    break;
                                case "10":
                                    $referencia["t10"]+= $rtns->existencia;
                                    break;
                                case "12":
                                    $referencia["t12"]+= $rtns->existencia;
                                    break;
                                case "14":
                                    $referencia["t14"]+= $rtns->existencia;
                                    break;
                                case "16":
                                    $referencia["t16"]+= $rtns->existencia;
                                    break;
                                case "18":
                                    $referencia["t18"]+= $rtns->existencia;
                                    break;
                                case "20":
                                    $referencia["t20"]+= $rtns->existencia;
                                    break;
                                case "22":
                                    $referencia["t22"]+= $rtns->existencia;
                                    break;
                                case "28":
                                    $referencia["t28"]+= $rtns->existencia;
                                    break;
                                case "30":
                                    $referencia["t30"]+= $rtns->existencia;
                                    break;
                                case "32":
                                    $referencia["t32"]+= $rtns->existencia;
                                    break;
                                case "34":
                                    $referencia["t34"]+= $rtns->existencia;
                                    break;
                                case "36":
                                    $referencia["t36"]+= $rtns->existencia;
                                    break;
                                case "38":
                                    $referencia["t38"]+= $rtns->existencia;
                                    break;
                            }
                            $referencia["total"]+= $rtns->existencia;
                        }
                    }elseif(count($array) == 4){
                        if($array[0]."-".$array[1] == $request->referencia){
                            switch ($array[2]) {
                                case "04":
                                    $referencia["t04"]+= $rtns->existencia;
                                    break;
                                case "06":
                                    $referencia["t06"]+= $rtns->existencia;
                                    break;
                                case "08":
                                    $referencia["t08"]+= $rtns->existencia;
                                    break;
                                case "10":
                                    $referencia["t10"]+= $rtns->existencia;
                                    break;
                                case "12":
                                    $referencia["t12"]+= $rtns->existencia;
                                    break;
                                case "14":
                                    $referencia["t14"]+= $rtns->existencia;
                                    break;
                                case "16":
                                    $referencia["t16"]+= $rtns->existencia;
                                    break;
                                case "18":
                                    $referencia["t18"]+= $rtns->existencia;
                                    break;
                                case "20":
                                    $referencia["t20"]+= $rtns->existencia;
                                    break;
                                case "22":
                                    $referencia["t22"]+= $rtns->existencia;
                                    break;
                                case "28":
                                    $referencia["t28"]+= $rtns->existencia;
                                    break;
                                case "30":
                                    $referencia["t30"]+= $rtns->existencia;
                                    break;
                                case "32":
                                    $referencia["t32"]+= $rtns->existencia;
                                    break;
                                case "34":
                                    $referencia["t34"]+= $rtns->existencia;
                                    break;
                                case "36":
                                    $referencia["t36"]+= $rtns->existencia;
                                    break;
                                case "38":
                                    $referencia["t38"]+= $rtns->existencia;
                                    break;
                            }
                            $referencia["total"]+= $rtns->existencia;
                        }
                    }
                }
            }         
        }
        $alertBMI = "danger";

        $saldo = DB::select('select zarethpr_produccion.detalles_orden_despacho.referencia, sum(zarethpr_produccion.detalles_orden_despacho.t04) as t04,
        sum(zarethpr_produccion.detalles_orden_despacho.t06 * -1) as t06, sum(zarethpr_produccion.detalles_orden_despacho.t08 * -1) as t08, sum(zarethpr_produccion.detalles_orden_despacho.t10 * -1) as t10,
        sum(zarethpr_produccion.detalles_orden_despacho.t12 * -1) as t12, sum(zarethpr_produccion.detalles_orden_despacho.t14 * -1) as t14, sum(zarethpr_produccion.detalles_orden_despacho.t16 * -1) as t16,
        sum(zarethpr_produccion.detalles_orden_despacho.t18 * -1) as t18, sum(zarethpr_produccion.detalles_orden_despacho.t20 * -1) as t20, sum(zarethpr_produccion.detalles_orden_despacho.t22 * -1) as t22,
        sum(zarethpr_produccion.detalles_orden_despacho.t24 * -1) as t24, sum(zarethpr_produccion.detalles_orden_despacho.t28 * -1) as t28, sum(zarethpr_produccion.detalles_orden_despacho.t30 * -1) as t30, 
        sum(zarethpr_produccion.detalles_orden_despacho.t32 * -1) as t32, sum(zarethpr_produccion.detalles_orden_despacho.t34 * -1) as t34, sum(zarethpr_produccion.detalles_orden_despacho.t36 * -1) as t36, 
        sum(zarethpr_produccion.detalles_orden_despacho.t38 * -1) as t38, sum((t04 + t06 + t08 + t10 + t12 + t14 + t16 + t18 + t20 + t22 + t24 + t28 + t30 + t32 + t34 + t36 + t38) * -1) AS sum_tallas from zarethpr_produccion.detalles_orden_despacho 
        where zarethpr_produccion.detalles_orden_despacho.referencia = "'.$request->referencia.'" and (zarethpr_produccion.detalles_orden_despacho.estado = "APROBADO" or zarethpr_produccion.detalles_orden_despacho.estado = "ALISTAR") 
        GROUP BY zarethpr_produccion.detalles_orden_despacho.referencia HAVING COUNT(*)>0 ORDER BY sum_tallas DESC');
        if(count($saldo) > 0){
            $saldo = $saldo[0];
            $referencia["t04"]+=intVal($saldo->t04);
            $referencia["t06"]+=intVal($saldo->t06);
            $referencia["t08"]+=intVal($saldo->t08);
            $referencia["t10"]+=intVal($saldo->t10);
            $referencia["t12"]+=intVal($saldo->t12);
            $referencia["t14"]+=intVal($saldo->t14);
            $referencia["t16"]+=intVal($saldo->t16);
            $referencia["t18"]+=intVal($saldo->t18);
            $referencia["t20"]+=intVal($saldo->t20);
            $referencia["t22"]+=intVal($saldo->t22);
            $referencia["t28"]+=intVal($saldo->t28);
            $referencia["t30"]+=intVal($saldo->t30);
            $referencia["t32"]+=intVal($saldo->t32);
            $referencia["t34"]+=intVal($saldo->t34);
            $referencia["t36"]+=intVal($saldo->t36);
            $referencia["t38"]+=intVal($saldo->t38);
            $referencia["total"]+=intVal($saldo->sum_tallas);
        }
        
        $alerts = '<div class="col-4 text-center">
                        <div class="alert alert-'.$alertSIESA.'" role="alert">
                            Siesa
                        </div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="alert alert-'.$alertTNS.'" role="alert">
                            Visual TNS
                        </div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="alert alert-'.$alertBMI.'" role="alert">
                            Bless Manufacturing  
                        </div>
                    </div>';
        return response()->json([$pedidos, $referencia, $bodegas, $alerts]);
    }

    public function cargaBlessManufacturing()
    {
        return view('filtro_medellin.cargaBlessManufacturing');
    }

    public function storeCargaBlessManufacturing(Request $request)
    {   
        ini_set('max_execution_time', 3600);
        $created_at = Carbon::now();
        $fecha = Carbon::now()->format('Y-m-d');
        $countSincBMI = 0;
        $bmi = "";
        $idControlInv = DB::select('select * from zarethpr_produccion.inventario_sistemas where fecha = "'.$fecha.'"');
        if(count($idControlInv) == 0){
            $idControlInv = DB::select('select MAX(id_control_inventario) as id_control_inventario from zarethpr_produccion.inventario_sistemas');
            $idControlInv = $idControlInv[0]->id_control_inventario + 1;
        }else{
            $idControlInv = $idControlInv[0]->id_control_inventario;
        }
        $file = $request->file('archivo');
        $up = new ImportExcel();
        Excel::import($up,$file);
        $items=json_decode(json_encode($up->sheetData[0]));
        $consultaBMI = DB::select('select * from zarethpr_produccion.inventario_sistemas where fecha = "'.$fecha.'" and sistema = "BMI"');
        if(count($consultaBMI) == 0){
            foreach ($items as $item) {
                if($item->CodBodega == "PT001"){
                    DB::insert('insert into zarethpr_produccion.inventario_sistemas (id_control_inventario, sistema, bodega, fecha, referencia, existencia, created_at) values (?, ?, ?, ?, ?, ?, ?)', 
                    [$idControlInv, 'BMI', $item->CodBodega, $fecha, $item->Item, intval($item->Disponible), $created_at]);
                    $countSincBMI++;
                }
            }
            $bmi = "Se ha cargado a las ".$created_at." un total de ".$countSincBMI." registros de Bless Manufacturing para el filtro.";
            return redirect(route('filtro.medellin.cargar'))->with('msg', $bmi)->with('alert', 'success');
        }else{
            $created_at = $consultaBMI[0]->created_at;
            $bmi = "Ya fueron cargados los registros de Bless Manufacturing a las ".$created_at." para el filtro.";
            return redirect(route('filtro.medellin.cargar'))->with('msg', $bmi)->with('alert', 'danger');
        }
    }

    public function storeDataFiltrada(Request $request)
    {
        $pedidos = $request->data;
        $referencia = $request->referencia;
        $created_at = Carbon::now();
        $updated_at = Carbon::now();
        $fecha = $created_at->format('Y-m-d');

        DB::insert('insert into zarethpr_produccion.control_referencia_filtrada (referencia, fecha, created_at) values (?, ?, ?)', [$request->referencia, $fecha, $created_at]);
        
        for ($i=0; $i<count($pedidos); $i++) {
            $consultaCliente = DB::select('SELECT zarethpr_produccion.filtrado.nit, zarethpr_produccion.filtrado.nombre,
            zarethpr_produccion.filtrado.direccion, zarethpr_produccion.filtrado.zona, zarethpr_produccion.filtrado.ciudad
            FROM zarethpr_produccion.filtrado WHERE idamarrador = '.$pedidos[$i]["idamarrador"].' GROUP BY zarethpr_produccion.filtrado.nit,
            zarethpr_produccion.filtrado.direccion HAVING COUNT(*)>0');
            $consultaCliente = $consultaCliente[0];
            
            $consultaOrdenDespacho = DB::select('SELECT * FROM zarethpr_produccion.orden_despacho WHERE zarethpr_produccion.orden_despacho.nit = "'.$consultaCliente->nit.'" AND
            (zarethpr_produccion.orden_despacho.direccion = "'.$consultaCliente->direccion.'" OR zarethpr_produccion.orden_despacho.direccion IS null)
            AND zarethpr_produccion.orden_despacho.cliente =  "'.$consultaCliente->nombre.'" AND zarethpr_produccion.orden_despacho.estado = "PREPARANDO"');
            
            $vendedor = DB::select('SELECT zarethpr_produccion.filtrado.vendedor FROM zarethpr_produccion.filtrado where idamarrador = '.$pedidos[$i]["idamarrador"]);
            $vendedor = $vendedor[0]->vendedor;
            
            if( count($consultaOrdenDespacho) == 0 ){
                $idConsecutivo = DB::select('SELECT MAX(id_diario) as id_diario FROM zarethpr_produccion.orden_despacho WHERE fecha = "'.$fecha.'"');
                $idConsecutivo = $idConsecutivo[0]->id_diario + 1;
                $idDiario = $idConsecutivo;

                if( $idConsecutivo < 10 ){ $idConsecutivo = "00".$idConsecutivo; }elseif( $idConsecutivo >= 10 && $idConsecutivo < 100 ){ $idConsecutivo = "0".$idConsecutivo; }else{ $idConsecutivo = "".$idConsecutivo; }
                $consecutivo = str_split($fecha);
                $consecutivo = $consecutivo[2].$consecutivo[3].$consecutivo[5].$consecutivo[6].$consecutivo[8].$consecutivo[9].$idConsecutivo;
                
                DB::insert('insert into zarethpr_produccion.orden_despacho (id_diario, consecutivo, nit, cliente, zona, ciudad, direccion, fecha, estado, 
                user_filtra, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?)', [$idConsecutivo, $consecutivo, $consultaCliente->nit, $consultaCliente->nombre, 
                $consultaCliente->zona, $consultaCliente->ciudad, $consultaCliente->direccion, $fecha, "PREPARANDO", Auth::user()->id, $created_at]);

                $idOrdenDespacho = DB::select('SELECT id FROM zarethpr_produccion.orden_despacho WHERE consecutivo = "'.$consecutivo.'"');
                $idOrdenDespacho = $idOrdenDespacho[0]->id;

                DB::insert('insert into zarethpr_produccion.detalles_orden_despacho (id_orden_despacho, id_pedido, id_amarrador, vendedor, 
                referencia, t04, t06, t08, t10, t12, t14, t16, t18, t20, t22, t28, t30, t32, t34, t36, t38, estado, created_at) 
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', [$idOrdenDespacho, $pedidos[$i]["ped"], $pedidos[$i]["idamarrador"],
                $vendedor, $referencia, $pedidos[$i]["t04"], $pedidos[$i]["t06"], $pedidos[$i]["t08"], $pedidos[$i]["t10"], $pedidos[$i]["t12"], $pedidos[$i]["t14"],
                $pedidos[$i]["t16"], $pedidos[$i]["t18"], $pedidos[$i]["t20"], $pedidos[$i]["t22"], $pedidos[$i]["t28"], $pedidos[$i]["t30"], 
                $pedidos[$i]["t32"], $pedidos[$i]["t34"], $pedidos[$i]["t36"], $pedidos[$i]["t38"], "APROBADO", $created_at]);
            }else{
                DB::table('zarethpr_produccion.orden_despacho')->where('id','=',$consultaOrdenDespacho[0]->id)->update(['updated_at'=>$updated_at]);
                DB::insert('insert into zarethpr_produccion.detalles_orden_despacho (id_orden_despacho, id_pedido, id_amarrador, vendedor, 
                referencia, t04, t06, t08, t10, t12, t14, t16, t18, t20, t22, t28, t30, t32, t34, t36, t38, estado, created_at) 
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', [$consultaOrdenDespacho[0]->id, $pedidos[$i]["ped"], $pedidos[$i]["idamarrador"],
                $vendedor, $referencia, $pedidos[$i]["t04"], $pedidos[$i]["t06"], $pedidos[$i]["t08"], $pedidos[$i]["t10"], $pedidos[$i]["t12"], $pedidos[$i]["t14"],
                $pedidos[$i]["t16"], $pedidos[$i]["t18"], $pedidos[$i]["t20"], $pedidos[$i]["t22"], $pedidos[$i]["t28"], $pedidos[$i]["t30"], 
                $pedidos[$i]["t32"], $pedidos[$i]["t34"], $pedidos[$i]["t36"], $pedidos[$i]["t38"], "APROBADO", $created_at]);
            }
            DB::table('zarethpr_produccion.ordenrefs')->where('id','=',$pedidos[$i]["idamarrador"])->update(['despachor'=>12]);
        }
        return response()->json("Respuesta Controller");
    }

    public function reporteCliente()
    {   
        $clientes = DB::select('select zarethpr_produccion.despacho.nit, zarethpr_produccion.despacho.cliente, zarethpr_produccion.despacho.direccion 
        from zarethpr_produccion.despacho GROUP BY zarethpr_produccion.despacho.nit, zarethpr_produccion.despacho.cliente, zarethpr_produccion.despacho.direccion 
        HAVING COUNT(*)>0 ORDER BY zarethpr_produccion.despacho.cliente ASC');
        return view('filtro_medellin.reporteClienteOrdenDespacho')->with('clientes',$clientes);
    }

    public function generarReporteCliente(Request $request)
    {
        $consulta = DB::select('select * from zarethpr_produccion.despacho where zarethpr_produccion.despacho.nit = "'.$request->documento.'" 
        and zarethpr_produccion.despacho.direccion = "'.$request->direccion.'"');
        if(count($consulta) == 0){
            return response()->json([
                0, "No hay registros"
            ], 200);
        }else{
            return response()->json([
                1, "Registros encontrados", view('filtro_medellin.fragmentoClienteOrdenDespacho',compact('consulta'))->render()
            ], 200);
        }
    }

    public function reporteReferencia()
    {
        $referencias = DB::select('select zarethpr_produccion.despacho.referencia from zarethpr_produccion.despacho 
        GROUP BY zarethpr_produccion.despacho.referencia HAVING COUNT(*)>0 ORDER BY zarethpr_produccion.despacho.referencia ASC');
        return view('filtro_medellin.reporteReferenciaOrdenDespacho')->with('referencias',$referencias);
    }

    public function generarReporteReferencia(Request $request)
    {
        $consulta = DB::select('select * from zarethpr_produccion.despacho where zarethpr_produccion.despacho.referencia = "'.$request->referencia.'"');
        if(count($consulta) == 0){
            return response()->json([
                0, "No hay registros"
            ], 200);
        }else{
            return response()->json([
                1, "Registros encontrados", view('filtro_medellin.fragmentoReferenciaOrdenDespacho',compact('consulta'))->render()
            ], 200);
        }
    }

    public function reporteProduccion()
    {
        $correrias = DB::select('select * from zarethpr_produccion.correrias');
        return view('filtro_medellin.reporteProduccionOrdenDespacho')->with('correrias',$correrias);
    }

    public function generarReporteProduccion(Request $request)
    {
        ini_set('max_execution_time', 3600);
        $array = explode(" - ", $request->correria);
        $produccion = DB::select('SELECT * from zarethpr_produccion.filtrado WHERE fpedido BETWEEN "'.$array[1].'" AND "'.$array[2].'"');
        foreach($produccion as $p){
            $p->correria = $array[0];
            $marca = $this->getMarcaReferencia($p->referencia);
            $p->marca = $marca;
            $p->tipo = "PEDIDO";
            $p->consecutivo = "S/C";
            $p->filtrador = "S/F";
            if(isset($p->zona)){
                $p->zona = "Sin Zona";
            }
            if($p->despachor == 1 || $p->despachor == 12){
                $p->tipo = "ORDEN DE DESPACHO";
                $od = DB::select('SELECT * from zarethpr_produccion.despacho WHERE id_amarrador = '.$p->idamarrador.' AND (estado_detalle_orden != "CANCELADO"
                OR estado_detalle_orden != "PENDIENTE")');
                if(count($od) > 0){
                    $p->t04 = $od[0]->t04;
                    $p->t06 = $od[0]->t06;
                    $p->t08 = $od[0]->t08;
                    $p->t10 = $od[0]->t10;
                    $p->t12 = $od[0]->t12;
                    $p->t14 = $od[0]->t14;
                    $p->t16 = $od[0]->t16;
                    $p->t18 = $od[0]->t18;
                    $p->t20 = $od[0]->t20;
                    $p->t22 = $od[0]->t22;
                    $p->t24 = $od[0]->t24;
                    $p->t28 = $od[0]->t28;
                    $p->t30 = $od[0]->t30;
                    $p->t32 = $od[0]->t32;
                    $p->t34 = $od[0]->t34;
                    $p->t36 = $od[0]->t36;
                    $p->t38 = $od[0]->t38;
                    $p->consecutivo = $od[0]->consecutivo;
                    $p->filtrador = $od[0]->filtrador;
                }
            }
            $p->total = $p->t04+$p->t06+$p->t08+$p->t10+$p->t12+$p->t14+$p->t16+$p->t18+$p->t20+$p->t22+$p->t24+$p->t28+$p->t30+$p->t32+$p->t34+$p->t36+$p->t38;
        }
        if(count($produccion) == 0){
            return response()->json([
                0, "No hay registros"
            ], 200);
        }else{
            return response()->json([
                1, "Registros encontrados", view('filtro_medellin.fragmentoProduccionOrdenDespacho',compact('produccion'))->render()
            ], 200);
        }
    }

    public function reporteCorreria()
    {
        $correrias = DB::select('select * from zarethpr_produccion.correrias');
        return view('filtro_medellin.reporteCorreriaOrdenDespacho')->with('correrias',$correrias);
    }

    public function generarReporteCorreria(Request $request)
    {
        $array = explode(" - ", $request->correria);
        $ordenes = DB::select('SELECT * from zarethpr_produccion.despacho WHERE fpedido BETWEEN "'.$array[1].'" AND "'.$array[2].'"');
        foreach($ordenes as $orden){
            $orden->correria = $array[0];
            $marca = $this->getMarcaReferencia($orden->referencia);
            $orden->marca = $marca;
        }
        //return Excel::download(new OrdenesExport($ordenes),"ORDENES DE DESPACHO.xlsx");
        //$consulta = DB::select('select * from zarethpr_produccion.despacho where zarethpr_produccion.despacho.referencia = "'.$request->referencia.'"');
        if(count($ordenes) == 0){
            return response()->json([
                0, "No hay registros"
            ], 200);
        }else{
            return response()->json([
                1, "Registros encontrados", view('filtro_medellin.fragmentoCorreriaOrdenDespacho',compact('ordenes'))->render()
            ], 200);
        }
    }

    private function getMarcaReferencia($referencia)
    {   
        $marca = "";
        $refe = str_split($referencia);

        if(strtoupper($refe[0]) == "1"){
            $marca = "ZARETH PREMIUM";
        }elseif(strtoupper($refe[0]) == "2"){
            $marca = "STARA GIRLS";
        }elseif(strtoupper($refe[0]) == "3"){
            $marca = "STARA";
        }elseif(strtoupper($refe[0]) == "4"){
            $marca = "ZARETH TEENS";
        }elseif(strtoupper($refe[0]) == "5"){
            $marca = "BLESS";
        }elseif(strtoupper($refe[0]) == "6"){
            $marca = "BLESS 23 JUNIOR";
        }elseif(strtoupper($refe[0]) == "7"){
            $marca = "ZARETH";
        }elseif(strtoupper($refe[0]) == "8"){
            $marca = "BLESS 23";
        }elseif(strtoupper($refe[0]) == "9"){
            $marca = "SHIREL";
        }elseif(strtoupper($refe[0]) == "H"){
            $marca = "STARA MEN";
        }elseif(strtoupper($refe[0]) == "E"){
            if(strtoupper($refe[0].$refe[1]) == "EL"){
                $marca = "ELOHE";
            }else{
                $marca = "ELOHE";
            }
        }elseif(strtoupper($refe[0]) == "S"){
            if(strtoupper($refe[0].$refe[1].$refe[2]) == "STG"){
                $marca = "STARA BLUSAS";
            }elseif(strtoupper($refe[0].$refe[1].$refe[2]) == "STD"){
                $marca = "STARA";
            }else{
                $marca = "SIN DEFINIR";
            }
        }elseif(strtoupper($refe[0].$refe[1]) == "MV"){
            $marca = "MICHELL V";
        }elseif(strtoupper($refe[0]) == "F"){
            if(strtoupper($refe[0].$refe[1]) == "FV"){
                $marca = "FIANCHI VIP";
            }elseif(strtoupper($refe[0].$refe[1]) == "FR"){
                $marca = "FARFALLA";
            }else{
                $marca = "FLOW";
            }
        }elseif(strtoupper($refe[0]) == "B"){
            if(strtoupper($refe[0].$refe[1]) == "BZ"){
                $marca = "ESTILOS BZ";
            }else{
                $marca = "ZARETH CURVI PLUS";
            }
        }elseif(strtoupper($refe[0]) == "D"){
            $marca = "DHARA";
        }elseif(strtoupper($refe[0]) == "Z"){
            $marca = "STORE";
        }elseif(strtoupper($refe[0]) == "K"){
            $marca = "STARA KIDS";
        }elseif(strtoupper($refe[0]) == "C"){
            if(strtoupper($refe[0].$refe[1].$refe[2]) == "CRP"){
                $marca = "CALIFORNIA PLUS";
            }elseif(strtoupper($refe[0].$refe[1].$refe[2]) == "CPP"){
                $marca = "CALIFORNIA PLUS";
            }elseif(strtoupper($refe[0].$refe[1]) == "CR"){
                $marca = "CALIFORNIA";
            }elseif(strtoupper($refe[0].$refe[1]) == "CM"){
                $marca = "CALIFORNIA MEN";
            }elseif(strtoupper($refe[0].$refe[1]) == "CK"){
                $marca = "CALIFORNIA KIDS";
            }elseif(strtoupper($refe[0].$refe[1]) == "CT"){
                $marca = "CALIFORNIA TEENS";
            }elseif(strtoupper($refe[0].$refe[1]) == "CP"){
                $marca = "CALIFORNIA PREMIUM";
            }elseif(strtoupper($refe[0].$refe[1]) == "CV"){
                $marca = "CURVE LOS ANGELES";
            }elseif(strtoupper($refe[0].$refe[1]) == "C9"){
                $marca = "CLASIC SHIREL";
            }
        }elseif(strtoupper($refe[0]) == "L"){
            if(strtoupper($refe[0].$refe[1]) == "LR"){
                $marca = "LOA RIGIDO";
            }elseif(strtoupper($refe[0].$refe[1]) == "LS"){
                $marca = "LOA STRECH";
            }else{
                $marca = "LOA";
            }
        }elseif(strtoupper($refe[0]) == "O"){
            $marca = "BLESS ORIGINAL";
        }elseif(strtoupper($refe[0]) == "P"){
            $marca = "ZARETH PREMIUM";
        }elseif(strtoupper($refe[0]) == "A"){
            $marca = "ALPHA LEGACY";
        }elseif(strtoupper($refe[0]) == "N"){
            if(strtoupper($refe[0].$refe[1]) == "NY"){
                $marca = "NEW YORK";
            }elseif(strtoupper($refe[0].$refe[1]) == "NE"){
                $marca = "NEON CAMISA";
            }elseif(strtoupper($refe[0].$refe[1]) == "NK"){
                $marca = "NEON KIDS";
            }elseif(strtoupper($refe[0].$refe[1]) == "NB"){
                $marca = "NEON CAMISA NIÑO";
            }else{
                $marca = "NEON";
            }
        }elseif(strtoupper($refe[0]) == "Y"){
            if(strtoupper($refe[0].$refe[1]) == "YD"){
                $marca = "NEW YORK";
            }elseif(strtoupper($refe[0].$refe[1]) == "YB"){
                $marca = "NEW YORK PLUS";
            }elseif(strtoupper($refe[0].$refe[1]) == "YG"){
                $marca = "NEW YORK PLUS";
            }elseif(strtoupper($refe[0].$refe[1]) == "YM"){
                $marca = "NEW YORK MEN";
            }elseif(strtoupper($refe[0].$refe[1]) == "YK"){
                $marca = "NEW YORK KIDS";
            }elseif(strtoupper($refe[0].$refe[1]) == "YT"){
                $marca = "NEW YORK TEENS";
            }else{
                $marca = "NEW YORK";
            }
        }     
        return $marca;
    }

    public function excelDownload()
    {
        ini_set('max_execution_time', 10800);
        $consulta = DB::select('select zarethpr_produccion.filtrado.referencia, zarethpr_produccion.filtrado.marca, sum(zarethpr_produccion.filtrado.t04) as t04,
        sum(zarethpr_produccion.filtrado.t06 * -1) as t06, sum(zarethpr_produccion.filtrado.t08 * -1) as t08, sum(zarethpr_produccion.filtrado.t10 * -1) as t10,
        sum(zarethpr_produccion.filtrado.t12 * -1) as t12, sum(zarethpr_produccion.filtrado.t14 * -1) as t14, sum(zarethpr_produccion.filtrado.t16 * -1) as t16,
        sum(zarethpr_produccion.filtrado.t18 * -1) as t18, sum(zarethpr_produccion.filtrado.t20 * -1) as t20, sum(zarethpr_produccion.filtrado.t22 * -1) as t22,
        sum(zarethpr_produccion.filtrado.t24 * -1) as t24, sum(zarethpr_produccion.filtrado.t28 * -1) as t28, sum(zarethpr_produccion.filtrado.t30 * -1) as t30, 
        sum(zarethpr_produccion.filtrado.t32 * -1) as t32, sum(zarethpr_produccion.filtrado.t34 * -1) as t34, sum(zarethpr_produccion.filtrado.t36 * -1) as t36, 
        sum(zarethpr_produccion.filtrado.t38 * -1) as t38, sum((t04 + t06 + t08 + t10 + t12 + t14 + t16 + t18 + t20 + t22 + t24 + t28 + t30 + t32 + t34 + t36 + t38) * -1) 
        AS sum from zarethpr_produccion.filtrado where zarethpr_produccion.filtrado.despachor = 0 and zarethpr_produccion.filtrado.aprobado = 1 and zarethpr_produccion.filtrado.estado = "Pedido"
        GROUP BY zarethpr_produccion.filtrado.referencia HAVING COUNT(*)>0 ORDER BY zarethpr_produccion.filtrado.referencia ASC');
        
        $arrayRef = [];
        for ($i=0; $i<count($consulta) ; $i++) { 
            $marca = $this->getMarcaReferencia($consulta[$i]->referencia);
            $consulta[$i]->referencia = strtoupper($consulta[$i]->referencia);
            $arrayRef[$i][0] = [
                                    "referencia" => $consulta[$i]->referencia, "bodega" => "VENTAS", "marca" => $marca,
                                    "t04" => intval($consulta[$i]->t04), "t06" => intval($consulta[$i]->t06), 
                                    "t08" => intval($consulta[$i]->t08), "t10" => intval($consulta[$i]->t10), "t12" => intval($consulta[$i]->t12), 
                                    "t14" => intval($consulta[$i]->t14), "t16" => intval($consulta[$i]->t16), "t18" => intval($consulta[$i]->t18), 
                                    "t20" => intval($consulta[$i]->t20), "t22" => intval($consulta[$i]->t22), "t28" => intval($consulta[$i]->t28), 
                                    "t30" => intval($consulta[$i]->t30), "t32" => intval($consulta[$i]->t32), "t34" => intval($consulta[$i]->t34), 
                                    "t36" => intval($consulta[$i]->t36), "t38" => intval($consulta[$i]->t38), "total" => intval($consulta[$i]->sum)
                                ];   
            $arrayRef[$i][2] = [
                                    "referencia" => $consulta[$i]->referencia, "bodega" => "COMPROMETIDO", "marca" => $marca,
                                    "t04" => 0, "t06" => 0, "t08" => 0, "t10" => 0, 
                                    "t12" => 0, "t14" => 0, "t16" => 0, "t18" => 0, "t20" => 0, "t22" => 0, 
                                    "t28" => 0, "t30" => 0, "t32" => 0, "t34" => 0, "t36" => 0, "t38" => 0, "total" => 0
                                ];               
            $arrayRef[$i][1] = [
                                    "referencia" => $consulta[$i]->referencia, "bodega" => "BODEGA MEDELLIN", "marca" => $marca,
                                    "t04" => 0, "t06" => 0, "t08" => 0, "t10" => 0, 
                                    "t12" => 0, "t14" => 0, "t16" => 0, "t18" => 0, "t20" => 0, "t22" => 0, 
                                    "t28" => 0, "t30" => 0, "t32" => 0, "t34" => 0, "t36" => 0, "t38" => 0, "total" => 0
                                ];   


            try{
                $refTns = DB::connection('firebird')->select("SELECT B.CODIGO CODBOD , B.NOMBRE NOMBOD , M.CODIGO , M.DESCRIP , SM.EXISTENC
                FROM MATERIAL M INNER JOIN SALMATERIAL SM ON (SM.MATID = M.MATID) INNER JOIN BODEGA B ON (B.BODID = SM.BODID)
                WHERE M.CODIGO LIKE '%".strtoupper($consulta[$i]->referencia)."%' AND B.CODIGO = 'BMEC'");
                foreach($refTns as $rtns){
                    $talla = "";
                    $array = explode("-", $rtns->CODIGO);
                    if(count($array) == 3){
                        if($array[0] == $consulta[$i]->referencia){
                            $rtns->CODIGO = $array[0];
                            $talla = $array[1];
                        }
                    }elseif(count($array) == 4){
                        if($array[0]."-".$array[1] == $consulta[$i]->referencia){
                            $rtns->CODIGO = $array[0]."-".$array[1];
                            $talla = $array[2];
                        }elseif($array[1] == $consulta[$i]->referencia){
                            $rtns->CODIGO = $array[1];
                            $talla = $array[2];
                        }
                    }elseif(count($array) == 5){
                        if($array[1]."-".$array[2] == $consulta[$i]->referencia){
                            $rtns->CODIGO = $array[1]."-".$array[2];
                            $talla = $array[3];
                        }
                    }
                    if($rtns->CODBOD == "BMEC"){
                        if($rtns->CODIGO == $consulta[$i]->referencia){
                            switch ($talla){
                                    case "04":
                                        $arrayRef[$i][1]["t04"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "06":
                                        $arrayRef[$i][1]["t06"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "08":
                                        $arrayRef[$i][1]["t08"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "10":
                                        $arrayRef[$i][1]["t10"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "12":
                                        $arrayRef[$i][1]["t12"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "14":
                                        $arrayRef[$i][1]["t14"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "16":
                                        $arrayRef[$i][1]["t16"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "18":
                                        $arrayRef[$i][1]["t18"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "20":
                                        $arrayRef[$i][1]["t20"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "22":
                                        $arrayRef[$i][1]["t22"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "28":
                                        $arrayRef[$i][1]["t28"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "30":
                                        $arrayRef[$i][1]["t30"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "32":
                                        $arrayRef[$i][1]["t32"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "34":
                                        $arrayRef[$i][1]["t34"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "36":
                                        $arrayRef[$i][1]["t36"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "38":
                                        $arrayRef[$i][1]["t38"]+= intval($rtns->EXISTENC);
                                        break;
                            }
                                $arrayRef[$i][1]["total"]+= intval($rtns->EXISTENC);
                        }
                    }
                }          
            } catch (\Exception $e) {
                $refTns = DB::select('select * from zarethpr_produccion.inventario_sistemas where zarethpr_produccion.inventario_sistemas.sistema = 
                "VISUAL TNS" and fecha = "'.Carbon::now()->format('Y-m-d').'"');
                if(count($refTns) == 0){
                    $refTns = DB::select('select * from zarethpr_produccion.inventario_sistemas where zarethpr_produccion.inventario_sistemas.sistema = 
                    "VISUAL TNS" and fecha = (SELECT MAX(fecha) FROM zarethpr_produccion.inventario_sistemas)');
                }
    
                foreach($refTns as $rtns){
                    $talla = "";
                    $array = explode("-", $rtns->referencia);
                    if(count($array) == 3){
                        if($array[0] == $consulta[$i]->referencia){
                            $rtns->referencia = $array[0];
                            $talla = $array[1];
                        }
                    }elseif(count($array) == 4){
                        if($array[0]."-".$array[1] == $consulta[$i]->referencia){
                            $rtns->referencia = $array[0]."-".$array[1];
                            $talla = $array[2];
                        }elseif($array[1] == $consulta[$i]->referencia){
                            $rtns->referencia = $array[1];
                            $talla = $array[2];
                        }
                    }elseif(count($array) == 5){
                        if($array[1]."-".$array[2] == $consulta[$i]->referencia){
                            $rtns->referencia = $array[1]."-".$array[2];
                            $talla = $array[3];
                        }
                    }

                    if($rtns->bodega == "BMEC"){
                        if($rtns->referencia == $consulta[$i]->referencia){
                            switch ($talla){
                                    case "04":
                                        $arrayRef[$i][1]["t04"]+= intval($rtns->existencia);
                                        break;
                                    case "06":
                                        $arrayRef[$i][1]["t06"]+= intval($rtns->existencia);
                                        break;
                                    case "08":
                                        $arrayRef[$i][1]["t08"]+= intval($rtns->existencia);
                                        break;
                                    case "10":
                                        $arrayRef[$i][1]["t10"]+= intval($rtns->existencia);
                                        break;
                                    case "12":
                                        $arrayRef[$i][1]["t12"]+= intval($rtns->existencia);
                                        break;
                                    case "14":
                                        $arrayRef[$i][1]["t14"]+= intval($rtns->existencia);
                                        break;
                                    case "16":
                                        $arrayRef[$i][1]["t16"]+= intval($rtns->existencia);
                                        break;
                                    case "18":
                                        $arrayRef[$i][1]["t18"]+= intval($rtns->existencia);
                                        break;
                                    case "20":
                                        $arrayRef[$i][1]["t20"]+= intval($rtns->existencia);
                                        break;
                                    case "22":
                                        $arrayRef[$i][1]["t22"]+= intval($rtns->existencia);
                                        break;
                                    case "28":
                                        $arrayRef[$i][1]["t28"]+= intval($rtns->existencia);
                                        break;
                                    case "30":
                                        $arrayRef[$i][1]["t30"]+= intval($rtns->existencia);
                                        break;
                                    case "32":
                                        $arrayRef[$i][1]["t32"]+= intval($rtns->existencia);
                                        break;
                                    case "34":
                                        $arrayRef[$i][1]["t34"]+= intval($rtns->existencia);
                                        break;
                                    case "36":
                                        $arrayRef[$i][1]["t36"]+= intval($rtns->existencia);
                                        break;
                                    case "38":
                                        $arrayRef[$i][1]["t38"]+= intval($rtns->existencia);
                                        break;
                            }
                                $arrayRef[$i][1]["total"]+= intval($rtns->existencia);
                        }
                    }
                }       
            }

            $saldo = DB::select('select zarethpr_produccion.detalles_orden_despacho.referencia, sum(zarethpr_produccion.detalles_orden_despacho.t04) as t04,
            sum(zarethpr_produccion.detalles_orden_despacho.t06 * -1) as t06, sum(zarethpr_produccion.detalles_orden_despacho.t08 * -1) as t08, sum(zarethpr_produccion.detalles_orden_despacho.t10 * -1) as t10,
            sum(zarethpr_produccion.detalles_orden_despacho.t12 * -1) as t12, sum(zarethpr_produccion.detalles_orden_despacho.t14 * -1) as t14, sum(zarethpr_produccion.detalles_orden_despacho.t16 * -1) as t16,
            sum(zarethpr_produccion.detalles_orden_despacho.t18 * -1) as t18, sum(zarethpr_produccion.detalles_orden_despacho.t20 * -1) as t20, sum(zarethpr_produccion.detalles_orden_despacho.t22 * -1) as t22,
            sum(zarethpr_produccion.detalles_orden_despacho.t24 * -1) as t24, sum(zarethpr_produccion.detalles_orden_despacho.t28 * -1) as t28, sum(zarethpr_produccion.detalles_orden_despacho.t30 * -1) as t30, 
            sum(zarethpr_produccion.detalles_orden_despacho.t32 * -1) as t32, sum(zarethpr_produccion.detalles_orden_despacho.t34 * -1) as t34, sum(zarethpr_produccion.detalles_orden_despacho.t36 * -1) as t36, 
            sum(zarethpr_produccion.detalles_orden_despacho.t38 * -1) as t38, sum((t04 + t06 + t08 + t10 + t12 + t14 + t16 + t18 + t20 + t22 + t24 + t28 + t30 + t32 + t34 + t36 + t38) * -1) AS sum_tallas from zarethpr_produccion.detalles_orden_despacho 
            where zarethpr_produccion.detalles_orden_despacho.referencia = "'.$consulta[$i]->referencia.'" and (zarethpr_produccion.detalles_orden_despacho.estado = "APROBADO" or zarethpr_produccion.detalles_orden_despacho.estado = "ALISTAR") 
            GROUP BY zarethpr_produccion.detalles_orden_despacho.referencia HAVING COUNT(*)>0 ORDER BY sum_tallas DESC');
            if(count($saldo) > 0){
                $saldo = $saldo[0];
                $arrayRef[$i][2]["t04"]+=intVal($saldo->t04);
                $arrayRef[$i][2]["t06"]+=intVal($saldo->t06);
                $arrayRef[$i][2]["t08"]+=intVal($saldo->t08);
                $arrayRef[$i][2]["t10"]+=intVal($saldo->t10);
                $arrayRef[$i][2]["t12"]+=intVal($saldo->t12);
                $arrayRef[$i][2]["t14"]+=intVal($saldo->t14);
                $arrayRef[$i][2]["t16"]+=intVal($saldo->t16);
                $arrayRef[$i][2]["t18"]+=intVal($saldo->t18);
                $arrayRef[$i][2]["t20"]+=intVal($saldo->t20);
                $arrayRef[$i][2]["t22"]+=intVal($saldo->t22);
                $arrayRef[$i][2]["t28"]+=intVal($saldo->t28);
                $arrayRef[$i][2]["t30"]+=intVal($saldo->t30);
                $arrayRef[$i][2]["t32"]+=intVal($saldo->t32);
                $arrayRef[$i][2]["t34"]+=intVal($saldo->t34);
                $arrayRef[$i][2]["t36"]+=intVal($saldo->t36);
                $arrayRef[$i][2]["t38"]+=intVal($saldo->t38);
                $arrayRef[$i][2]["total"]+=intVal($saldo->sum_tallas);
            }
        }
        return Excel::download(new FiltroExport($arrayRef),"INFORME VENTAS VS INV MEDELLIN.xlsx");
    }

    public function reporteTotalDownload(){
        ini_set('max_execution_time', 10800);
        $bmec = DB::connection('firebird')->select("SELECT B.CODIGO CODBOD , B.NOMBRE NOMBOD , M.CODIGO , M.DESCRIP , SM.EXISTENC
        FROM MATERIAL M INNER JOIN SALMATERIAL SM ON (SM.MATID = M.MATID) INNER JOIN BODEGA B ON (B.BODID = SM.BODID)
        WHERE B.CODIGO = 'BMEC'");
        $referencias = [];
        foreach ($bmec as $b) {
            $array = explode("-", $b->CODIGO);
            switch (count($array)){
                case 2:
                    $referencias[] = $array[0];
                    break;
                case 3:
                    $referencias[] = $array[0];
                    break;
                case 4:
                    $referencias[] = $array[0]."-".$array[1];
                    break;
            }
            
        }
        $referencias = array_values(array_unique($referencias));

        for ($i=0; $i < count($referencias); $i++) {
            $marca = $this->getMarcaReferencia($referencias[$i]);
            $venta = DB::select('select zarethpr_produccion.filtrado.referencia, zarethpr_produccion.filtrado.marca, sum(zarethpr_produccion.filtrado.t04) as t04,
            sum(zarethpr_produccion.filtrado.t06 * -1) as t06, sum(zarethpr_produccion.filtrado.t08 * -1) as t08, sum(zarethpr_produccion.filtrado.t10 * -1) as t10,
            sum(zarethpr_produccion.filtrado.t12 * -1) as t12, sum(zarethpr_produccion.filtrado.t14 * -1) as t14, sum(zarethpr_produccion.filtrado.t16 * -1) as t16,
            sum(zarethpr_produccion.filtrado.t18 * -1) as t18, sum(zarethpr_produccion.filtrado.t20 * -1) as t20, sum(zarethpr_produccion.filtrado.t22 * -1) as t22,
            sum(zarethpr_produccion.filtrado.t24 * -1) as t24, sum(zarethpr_produccion.filtrado.t28 * -1) as t28, sum(zarethpr_produccion.filtrado.t30 * -1) as t30, 
            sum(zarethpr_produccion.filtrado.t32 * -1) as t32, sum(zarethpr_produccion.filtrado.t34 * -1) as t34, sum(zarethpr_produccion.filtrado.t36 * -1) as t36, 
            sum(zarethpr_produccion.filtrado.t38 * -1) as t38, sum((t04 + t06 + t08 + t10 + t12 + t14 + t16 + t18 + t20 + t22 + t24 + t28 + t30 + t32 + t34 + t36 + t38) * -1) 
            AS sum from zarethpr_produccion.filtrado where zarethpr_produccion.filtrado.despachor = 0 and zarethpr_produccion.filtrado.aprobado = 1 and zarethpr_produccion.filtrado.estado = "Pedido"
            AND zarethpr_produccion.filtrado.referencia = "'.$referencias[$i].'" GROUP BY zarethpr_produccion.filtrado.referencia HAVING COUNT(*)>0');
            
            $comprometido = DB::select('select zarethpr_produccion.detalles_orden_despacho.referencia, sum(zarethpr_produccion.detalles_orden_despacho.t04) as t04,
            sum(zarethpr_produccion.detalles_orden_despacho.t06 * -1) as t06, sum(zarethpr_produccion.detalles_orden_despacho.t08 * -1) as t08, sum(zarethpr_produccion.detalles_orden_despacho.t10 * -1) as t10,
            sum(zarethpr_produccion.detalles_orden_despacho.t12 * -1) as t12, sum(zarethpr_produccion.detalles_orden_despacho.t14 * -1) as t14, sum(zarethpr_produccion.detalles_orden_despacho.t16 * -1) as t16,
            sum(zarethpr_produccion.detalles_orden_despacho.t18 * -1) as t18, sum(zarethpr_produccion.detalles_orden_despacho.t20 * -1) as t20, sum(zarethpr_produccion.detalles_orden_despacho.t22 * -1) as t22,
            sum(zarethpr_produccion.detalles_orden_despacho.t24 * -1) as t24, sum(zarethpr_produccion.detalles_orden_despacho.t28 * -1) as t28, sum(zarethpr_produccion.detalles_orden_despacho.t30 * -1) as t30, 
            sum(zarethpr_produccion.detalles_orden_despacho.t32 * -1) as t32, sum(zarethpr_produccion.detalles_orden_despacho.t34 * -1) as t34, sum(zarethpr_produccion.detalles_orden_despacho.t36 * -1) as t36, 
            sum(zarethpr_produccion.detalles_orden_despacho.t38 * -1) as t38, sum((t04 + t06 + t08 + t10 + t12 + t14 + t16 + t18 + t20 + t22 + t24 + t28 + t30 + t32 + t34 + t36 + t38) * -1) AS sum_tallas from zarethpr_produccion.detalles_orden_despacho 
            where zarethpr_produccion.detalles_orden_despacho.referencia = "'.$referencias[$i].'" and (zarethpr_produccion.detalles_orden_despacho.estado = "APROBADO" or zarethpr_produccion.detalles_orden_despacho.estado = "ALISTAR") 
            GROUP BY zarethpr_produccion.detalles_orden_despacho.referencia HAVING COUNT(*)>0 ORDER BY sum_tallas DESC');
            $arrayRef[$i][0] = [
                                    "referencia" => $referencias[$i], "bodega" => "VENTAS", "marca" => $marca,
                                    "t04" => !empty($venta) ? intval($venta[0]->t04) : 0, "t06" => !empty($venta) ? intval($venta[0]->t06) : 0, 
                                    "t08" => !empty($venta) ? intval($venta[0]->t08) : 0, "t10" => !empty($venta) ? intval($venta[0]->t10) : 0, "t12" => !empty($venta) ? intval($venta[0]->t12) : 0, 
                                    "t14" => !empty($venta) ? intval($venta[0]->t14) : 0, "t16" => !empty($venta) ? intval($venta[0]->t16) : 0, "t18" => !empty($venta) ? intval($venta[0]->t18) : 0, 
                                    "t20" => !empty($venta) ? intval($venta[0]->t20) : 0, "t22" => !empty($venta) ? intval($venta[0]->t22) : 0, "t28" => !empty($venta) ? intval($venta[0]->t28) : 0, 
                                    "t30" => !empty($venta) ? intval($venta[0]->t30) : 0, "t32" => !empty($venta) ? intval($venta[0]->t32) : 0, "t34" => !empty($venta) ? intval($venta[0]->t34) : 0, 
                                    "t36" => !empty($venta) ? intval($venta[0]->t36) : 0, "t38" => !empty($venta) ? intval($venta[0]->t38) : 0, "total" => !empty($venta) ? intval($venta[0]->sum) : 0
                                ];   
            $arrayRef[$i][2] = [
                                    "referencia" => $referencias[$i], "bodega" => "COMPROMETIDO", "marca" => $marca,
                                    "t04" => !empty($comprometido) ? intval($comprometido[0]->t04) : 0, "t06" => !empty($comprometido) ? intval($comprometido[0]->t06) : 0, 
                                    "t08" => !empty($comprometido) ? intval($comprometido[0]->t08) : 0, "t10" => !empty($comprometido) ? intval($comprometido[0]->t10) : 0, "t12" => !empty($comprometido) ? intval($comprometido[0]->t12) : 0, 
                                    "t14" => !empty($comprometido) ? intval($comprometido[0]->t14) : 0, "t16" => !empty($comprometido) ? intval($comprometido[0]->t16) : 0, "t18" => !empty($comprometido) ? intval($comprometido[0]->t18) : 0, 
                                    "t20" => !empty($comprometido) ? intval($comprometido[0]->t20) : 0, "t22" => !empty($comprometido) ? intval($comprometido[0]->t22) : 0, "t28" => !empty($comprometido) ? intval($comprometido[0]->t28) : 0, 
                                    "t30" => !empty($comprometido) ? intval($comprometido[0]->t30) : 0, "t32" => !empty($comprometido) ? intval($comprometido[0]->t32) : 0, "t34" => !empty($comprometido) ? intval($comprometido[0]->t34) : 0, 
                                    "t36" => !empty($comprometido) ? intval($comprometido[0]->t36) : 0, "t38" => !empty($comprometido) ? intval($comprometido[0]->t38) : 0, "total" => !empty($comprometido) ? intval($comprometido[0]->sum_tallas) : 0
                                ];               
            $arrayRef[$i][1] = [
                                    "referencia" => $referencias[$i], "bodega" => "BODEGA MEDELLIN", "marca" => $marca,
                                    "t04" => 0, "t06" => 0, "t08" => 0, "t10" => 0, 
                                    "t12" => 0, "t14" => 0, "t16" => 0, "t18" => 0, "t20" => 0, "t22" => 0, 
                                    "t28" => 0, "t30" => 0, "t32" => 0, "t34" => 0, "t36" => 0, "t38" => 0, "total" => 0
                                ];   

            $refTns = DB::connection('firebird')->select("SELECT B.CODIGO CODBOD , B.NOMBRE NOMBOD , M.CODIGO , M.DESCRIP , SM.EXISTENC
            FROM MATERIAL M INNER JOIN SALMATERIAL SM ON (SM.MATID = M.MATID) INNER JOIN BODEGA B ON (B.BODID = SM.BODID)
            WHERE M.CODIGO LIKE '%".$referencias[$i]."%' AND B.CODIGO = 'BMEC'");
            foreach($refTns as $rtns){
                $talla = "";
                $array = explode("-", $rtns->CODIGO);
                if(count($array) == 3){
                    if($array[0] == $referencias[$i]){
                        $rtns->CODIGO = $array[0];
                        $talla = $array[1];
                    }
                }elseif(count($array) == 4){
                    if($array[0]."-".$array[1] == $referencias[$i]){
                        $rtns->CODIGO = $array[0]."-".$array[1];
                        $talla = $array[2];
                    }elseif($array[1] == $referencias[$i]){
                        $rtns->CODIGO = $array[1];
                        $talla = $array[2];
                    }
                }elseif(count($array) == 5){
                    if($array[1]."-".$array[2] == $$referencias[$i]){
                        $rtns->CODIGO = $array[1]."-".$array[2];
                        $talla = $array[3];
                    }
                }
                if($rtns->CODBOD == "BMEC"){
                    if($rtns->CODIGO == $referencias[$i]){
                        switch ($talla){
                                case "04":
                                    $arrayRef[$i][1]["t04"]+= intval($rtns->EXISTENC);
                                    break;
                                case "06":
                                    $arrayRef[$i][1]["t06"]+= intval($rtns->EXISTENC);
                                    break;
                                case "08":
                                    $arrayRef[$i][1]["t08"]+= intval($rtns->EXISTENC);
                                    break;
                                case "10":
                                    $arrayRef[$i][1]["t10"]+= intval($rtns->EXISTENC);
                                    break;
                                case "12":
                                    $arrayRef[$i][1]["t12"]+= intval($rtns->EXISTENC);
                                    break;
                                case "14":
                                    $arrayRef[$i][1]["t14"]+= intval($rtns->EXISTENC);
                                    break;
                                case "16":
                                    $arrayRef[$i][1]["t16"]+= intval($rtns->EXISTENC);
                                    break;
                                case "18":
                                    $arrayRef[$i][1]["t18"]+= intval($rtns->EXISTENC);
                                    break;
                                case "20":
                                    $arrayRef[$i][1]["t20"]+= intval($rtns->EXISTENC);
                                    break;
                                case "22":
                                    $arrayRef[$i][1]["t22"]+= intval($rtns->EXISTENC);
                                    break;
                                case "28":
                                    $arrayRef[$i][1]["t28"]+= intval($rtns->EXISTENC);
                                    break;
                                case "30":
                                    $arrayRef[$i][1]["t30"]+= intval($rtns->EXISTENC);
                                    break;
                                case "32":
                                    $arrayRef[$i][1]["t32"]+= intval($rtns->EXISTENC);
                                    break;
                                case "34":
                                    $arrayRef[$i][1]["t34"]+= intval($rtns->EXISTENC);
                                    break;
                                case "36":
                                    $arrayRef[$i][1]["t36"]+= intval($rtns->EXISTENC);
                                    break;
                                case "38":
                                    $arrayRef[$i][1]["t38"]+= intval($rtns->EXISTENC);
                                    break;
                        }
                            $arrayRef[$i][1]["total"]+= intval($rtns->EXISTENC);
                    }
                }
            } 
        }
        return Excel::download(new FiltroExport($arrayRef),"REPORTE TOTAL MEDELLIN.xlsx");
    }

    public function backupDailyDataSystem()
    {
        ini_set('max_execution_time', 10800);
        $created_at = Carbon::now();
        $fecha = Carbon::now()->format('Y-m-d');
        $idControlInv = DB::select('select * from zarethpr_produccion.inventario_sistemas where fecha = "'.$fecha.'"');
        if(count($idControlInv) == 0){
            $idControlInv = DB::select('select MAX(id_control_inventario) as id_control_inventario from zarethpr_produccion.inventario_sistemas');
            $idControlInv = $idControlInv[0]->id_control_inventario + 1;
        }else{
            $idControlInv = $idControlInv[0]->id_control_inventario;
        }
        $tns = "";

        try{
            $consultaTNS = DB::select('select * from zarethpr_produccion.inventario_sistemas where fecha = "'.$fecha.'" and sistema = "VISUAL TNS"');
            if(count($consultaTNS) == 0){
                $countSincTNS = 0;
                $refTns = DB::connection('firebird')->select("SELECT B.CODIGO CODBOD , B.NOMBRE NOMBOD , M.CODIGO , M.DESCRIP , SM.EXISTENC
                FROM MATERIAL M INNER JOIN SALMATERIAL SM ON (SM.MATID = M.MATID) INNER JOIN BODEGA B ON (B.BODID = SM.BODID)
                WHERE B.CODIGO = 'BMEC'");
                foreach($refTns as $rtns){
                    DB::insert('insert into zarethpr_produccion.inventario_sistemas (id_control_inventario, sistema, bodega, fecha, referencia, existencia, created_at) values (?, ?, ?, ?, ?, ?, ?)', 
                    [$idControlInv, 'VISUAL TNS', $rtns->CODBOD, $fecha, $rtns->CODIGO, intval($rtns->EXISTENC), $created_at]);
                    $countSincTNS++;
                }
                $tns = "Se ha hecho respaldo local a las ".$created_at." con un total de ".$countSincTNS." registros de Visual TNS para el filtro.";
                echo $tns;
            }else{
                $tns = "Ya se hizo un respaldo local a las ".$consultaTNS[0]->created_at." de los registros de Visual TNS para el filtro.";
                echo $tns;
            }
        } catch (\Exception $e) {
            $tns = "No se ha podido respaldar los registros de Visual TNS para el filtro.";
            echo $tns;
        }
    }
}
