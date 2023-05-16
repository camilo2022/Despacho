<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Exports\FiltroExport;
use App\Exports\FacturacionExport;
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

class FiltroController extends Controller
{
    public function iniciarMessageWhatsAppReports()
    {
        $phones=["573222759176"];
        $client = new \GuzzleHttp\Client(['base_uri' => 'https://graph.facebook.com']);
        $url="/v16.0/104628379278603/messages";      
        $token="EAAhjIbGZBBmUBABLvYc02QqEZBmZBINacQK3H1kRGI8qRP3lBWMfQYGZBQO5PwI5Hw0ZCk3pRyNGZB7BM2ZBiTDypRVJ6CrjPThlgZBRJxsHnUAn1ncOibK08ZC3g8oUsUuVF6RMig4CNXWv2xQJRgLxKf8k7dNuwwZC1jwon6ZAp1bG4ZAqC41S1tkg";   
        foreach($phones as $phone){
            $client->request('POST',$url, [
                'headers' => [ 'Authorization' => 'Bearer '.$token ],
                'form_params' => [
                    "messaging_product"=> "whatsapp", 
                    "to"=> $phone, 
                    "type"=> "template", 
                    "template"=> [ 
                        "name"=> "hello_world", 
                        "language"=> [ 
                            "code"=> "en_US" 
                        ]
                    ] 
                ]
            ]);  
        }      
    }

    public function enviarMessageWhatsAppReports()
    {
        $messages = $this->messageWhatsAppReports();
        $phones=["573222759176","573208312491","573203940388","573006601557"];
        $client = new \GuzzleHttp\Client(['base_uri' => 'https://graph.facebook.com']);
        $url="/v16.0/104628379278603/messages";      
        $token="EAAhjIbGZBBmUBABLvYc02QqEZBmZBINacQK3H1kRGI8qRP3lBWMfQYGZBQO5PwI5Hw0ZCk3pRyNGZB7BM2ZBiTDypRVJ6CrjPThlgZBRJxsHnUAn1ncOibK08ZC3g8oUsUuVF6RMig4CNXWv2xQJRgLxKf8k7dNuwwZC1jwon6ZAp1bG4ZAqC41S1tkg";   
        foreach($messages as $message){
            foreach($phones as $phone){
                $client->request('POST',$url, [
                    'headers' => [ 'Authorization' => 'Bearer '.$token ],
                    'form_params' => [
                        "messaging_product"=> "whatsapp",
                        "recipient_type"=> "individual",
                        "to"=> $phone,
                        "type"=> "text",
                        "text"=> [
                            "preview_url"=> false,
                            "body"=> $message
                        ]
                    ]
                ]);  
            }     
        } 
    }

    private function messageWhatsAppReports()
    {
        $correria = DB::select('select zarethpr_proynew.correrias.codigo from zarethpr_proynew.correrias where estado = 1');
        $reporteVendedorVentas1 = DB::select('SELECT sum(t04+t06+t08+t10+t12+t14+t16+t18+t20+t22+t24+t26+t28+t30+t32+t34+t36+t38+xs+s+m+l+xl) as total,
        vendedor, despacho, correria FROM zarethpr_proynew.filtrado WHERE zarethpr_proynew.filtrado.correria = "'.$correria[0]->codigo.'" AND 
        (zarethpr_proynew.filtrado.despacho != "DESPACHADO" AND zarethpr_proynew.filtrado.despacho != "COMPROMETIDO")
        GROUP BY zarethpr_proynew.filtrado.vendedor, zarethpr_proynew.filtrado.despacho
        HAVING COUNT(*)>0 ORDER BY zarethpr_proynew.filtrado.vendedor ASC');
        $reporteVendedorVentas2 = DB::select('SELECT sum(t04+t06+t08+t10+t12+t14+t16+t18+t20+t22+t24+t26+t28+t30+t32+t34+t36+t38+xs+s+m+l+xl) as total,
        vendedor, despacho, correria FROM zarethpr_proynew.despacho WHERE zarethpr_proynew.despacho.correria = "'.$correria[0]->codigo.'" AND 
        (zarethpr_proynew.despacho.despacho = "DESPACHADO" OR zarethpr_proynew.despacho.despacho = "COMPROMETIDO")
        GROUP BY zarethpr_proynew.despacho.vendedor, zarethpr_proynew.despacho.despacho
        HAVING COUNT(*)>0 ORDER BY zarethpr_proynew.despacho.vendedor ASC');
        $reporteVendedorVentas = collect(array_merge($reporteVendedorVentas1, $reporteVendedorVentas2))->sortBy('vendedor')->values()->all();
        $reporteVendedorVentas = json_decode(json_encode($reporteVendedorVentas),true);
        $levels = array_unique(array_column($reporteVendedorVentas, 'vendedor'));
        $reportePorcentaje = array();
        foreach($reporteVendedorVentas as $key => $value){ 
        $reportePorcentaje[$levels[array_search($value['vendedor'],$levels )]][] = $value ; 
        };
        $reportePorcentaje = array_values($reportePorcentaje);
        
        
        $canceladas = 0; $despachadas = 0; $comprometidas = 0; $aprobadas = 0;
        $messageVentas = "*REPORTE DE VENTAS Y DESPACHOS NACIONAL A FECHA: ".Carbon::now()."* \n \n \n";
        $totalGlobal = 0;
        for($i=0; $i<count($reportePorcentaje); $i++){
            $totalGlobal+= collect($reportePorcentaje[$i])->groupBy('vendedor')->map(function ($item) {return $item->sum('total');})->values()[0];
        }
        for($i=0; $i<count($reportePorcentaje); $i++){
            $total = collect($reportePorcentaje[$i])->groupBy('vendedor')->map(function ($item) {return $item->sum('total');})->values()[0];
            $messageVentas = $messageVentas."VENDEDOR: ".$reportePorcentaje[$i][0]['vendedor']." TIENE";
            for($j=0; $j<count($reportePorcentaje[$i]); $j++){
                switch ($reportePorcentaje[$i][$j]['despacho']){
                    case "APROBADO":
                        $reportePorcentaje[$i][$j]['despacho'] = "APROBADO CARTERA";
                        $aprobadas+=$reportePorcentaje[$i][$j]['total'];
                        break;
                    case "COMPROMETIDO":
                        $reportePorcentaje[$i][$j]['despacho'] = "ALISTANDO";
                        $comprometidas+=$reportePorcentaje[$i][$j]['total'];
                        break;
                    case "CANCELADO":
                        $reportePorcentaje[$i][$j]['despacho'] = "CANCELADO CARTERA";
                        $canceladas+=$reportePorcentaje[$i][$j]['total'];
                        break;
                    case "DESPACHADO":
                        $reportePorcentaje[$i][$j]['despacho'] = "DESPACHADAS";
                        $despachadas+=$reportePorcentaje[$i][$j]['total'];
                        break;
                }
                $messageVentas = $messageVentas." | ".$reportePorcentaje[$i][$j]['total']." UND ".$reportePorcentaje[$i][$j]['despacho']." -> (".number_format((($reportePorcentaje[$i][$j]['total']/$total)*100),2)."%)";
            }
            $messageVentas = $messageVentas." | TOTAL VENTAS: ".$total." -> (".number_format((($total/$totalGlobal)*100),2)."%) | \n \n";
        }
        $totalDespacho = $despachadas+$aprobadas+$comprometidas+$canceladas;
        $messageTotal = "*REPORTE TOTAL DE VENTAS Y DESPACHOS NACIONAL A FECHA: ".Carbon::now()."* \n \n \n ".
        "DESPACHADAS: ".$despachadas." UND -> (".number_format((($despachadas/$totalDespacho)*100),2)."%) | ".
        "APROBADAS: ".$aprobadas." UND -> (".number_format((($aprobadas/$totalDespacho)*100),2)."%) | ".
        "ALISTANDO: ".$comprometidas." UND -> (".number_format((($comprometidas/$totalDespacho)*100),2)."%) | ".
        "CANCELADAS: ".$canceladas." UND -> (".number_format((($canceladas/$totalDespacho)*100),2)."%) | ";

        $consultaReferencia1 = DB::select('select zarethpr_proynew.filtrado.referencia, sum(t04 + t06 + 
        t08 + t10 + t12 + t14 + t16 + t18 + t20 + t22 + t24 + t26 + t28 + t30 + t32 + t34 + t36 + t38 + xs + s + m + l + xl) AS sum,
        zarethpr_proynew.filtrado.despacho from zarethpr_proynew.filtrado where (zarethpr_proynew.filtrado.despacho = "CANCELADO" OR 
        zarethpr_proynew.filtrado.despacho = "APROBADO") GROUP BY zarethpr_proynew.filtrado.referencia HAVING COUNT(*)>0 ORDER BY zarethpr_proynew.filtrado.referencia ASC');

        $consultaReferencia2 = DB::select('select zarethpr_proynew.despacho.referencia, sum(t04 + t06 + 
        t08 + t10 + t12 + t14 + t16 + t18 + t20 + t22 + t24 + t26 + t28 + t30 + t32 + t34 + t36 + t38 + xs + s + m + l + xl) AS sum,
        zarethpr_proynew.despacho.despacho from zarethpr_proynew.despacho where (zarethpr_proynew.despacho.despacho = "DESPACHADO" OR 
        zarethpr_proynew.despacho.despacho = "COMPROMETIDO") GROUP BY zarethpr_proynew.despacho.referencia HAVING COUNT(*)>0 ORDER BY zarethpr_proynew.despacho.referencia ASC');
        
        $consultaReferencia = collect(array_merge($consultaReferencia1, $consultaReferencia2))->sortBy('referencia')->values()->all();

        foreach($consultaReferencia as $r){
            $r->marca = $this->getMarcaReferencia($r->referencia);
        }
        
        $consultaReferencia = json_decode(json_encode($consultaReferencia),true);
        $levels = array_unique(array_column($consultaReferencia, 'marca'));
        $reporteMarca = array();
        foreach($consultaReferencia as $key => $value){ 
        $reporteMarca[$levels[array_search($value['marca'],$levels )]][] = $value ; 
        };
        $reporteMarca = array_values($reporteMarca);
        
        //$reporteMarca = array_values(collect(json_decode(json_encode($consultaReferencia),true))->groupBy('marca')->map(function ($item) {return [ 'marca'=>$item[0]['marca'],'total'=>$item->sum('sum'),];})->values()->sortBy('marca')->toArray());
        $menssageMarca = "*REPORTE DE UNIDADES POR MARCA A FECHA: ".Carbon::now()."* \n \n \n";
        $totalGlobal = 0;
        for($i=0; $i<count($reporteMarca); $i++){
            $totalGlobal+= collect($reporteMarca[$i])->groupBy('marca')->map(function ($item) {return $item->sum('sum');})->values()[0];
        }
        for($i=0; $i<count($reporteMarca); $i++){
            $menssageMarca = $menssageMarca."MARCA: ".$reporteMarca[$i][0]['marca']." TIENE";
            $total = collect($reporteMarca[$i])->groupBy('marca')->map(function ($item) {return $item->sum('sum');})->values()[0];
            $reporteMarca[$i] = collect($reporteMarca[$i])->groupBy('despacho')->map(function ($item) {return ['marca'=>$item[0]['marca'],'despacho'=>$item[0]['despacho'] ,'sum'=>$item->sum('sum'),];})->values()->toArray();
            for($j=0; $j<count($reporteMarca[$i]); $j++){
                switch ($reporteMarca[$i][$j]['despacho']){
                    case "APROBADO":
                        $reporteMarca[$i][$j]['despacho'] = "APROBADO CARTERA";
                        break;
                    case "COMPROMETIDO":
                        $reporteMarca[$i][$j]['despacho'] = "ALISTANDO";
                        break;
                    case "CANCELADO":
                        $reporteMarca[$i][$j]['despacho'] = "CANCELADO CARTERA";
                        break;
                    case "DESPACHADO":
                        $reporteMarca[$i][$j]['despacho'] = "DESPACHADAS";
                        break;
                }
                $menssageMarca = $menssageMarca." | ".$reporteMarca[$i][$j]['sum']." UND ".$reporteMarca[$i][$j]['despacho']." -> (".number_format((($reporteMarca[$i][$j]['sum']/$total)*100),2)."%)";
            }
            $menssageMarca = $menssageMarca." | TOTAL VENTAS: ".$total." -> (".number_format((($total/$totalGlobal)*100),2)."%) | \n \n";
        }
        return [$messageTotal,$messageVentas,$menssageMarca];
    }

    public function indexOrdenesClientes()
    {
        $consulta = DB::select('SELECT * FROM zarethpr_proynew.orden_alistamiento WHERE zarethpr_proynew.orden_alistamiento.user_alista = '.Auth::user()->id.' and zarethpr_proynew.orden_alistamiento.estado = "INICIADO"');
        $consulta2 = DB::select('SELECT * FROM zarethpr_proynew.orden_empacado WHERE zarethpr_proynew.orden_empacado.user_empaca = '.Auth::user()->id.' and zarethpr_proynew.orden_empacado.estado = "INICIADO"');
        if(!empty($consulta)){
            return redirect()->route('filtro.listado.ordenes.clientes.alistar.picking', $consulta[0]->id_orden_despacho);
        }elseif(!empty($consulta2)){
            return redirect()->route('filtro.listado.ordenes.clientes.empacar.picking', $consulta2[0]->id_orden_despacho);
        }else{
            if(Auth::user()->rol->slug == 'FCNAC'){
                $ordenesClientes = DB::select('select *, (select zarethpr_proynew.orden_alistamiento.estado from zarethpr_proynew.orden_alistamiento where zarethpr_proynew.orden_alistamiento.id_orden_despacho = zarethpr_proynew.orden_despacho.id) as alistamiento, (select zarethpr_proynew.orden_empacado.estado from zarethpr_proynew.orden_empacado where zarethpr_proynew.orden_empacado.id_orden_despacho = zarethpr_proynew.orden_despacho.id) as empacado from zarethpr_proynew.orden_despacho where estado = "FACTURANDO"');
            }elseif(Auth::user()->rol->slug == 'OANAC'){
                $ordenesClientes = DB::select('select *, (select zarethpr_proynew.orden_alistamiento.estado from zarethpr_proynew.orden_alistamiento where zarethpr_proynew.orden_alistamiento.id_orden_despacho = zarethpr_proynew.orden_despacho.id) as alistamiento, (select zarethpr_proynew.orden_empacado.estado from zarethpr_proynew.orden_empacado where zarethpr_proynew.orden_empacado.id_orden_despacho = zarethpr_proynew.orden_despacho.id) as empacado from zarethpr_proynew.orden_despacho where estado = "ALISTANDO"');
            }else{
                $ordenesClientes = DB::select('select *, (select zarethpr_proynew.orden_alistamiento.estado from zarethpr_proynew.orden_alistamiento where zarethpr_proynew.orden_alistamiento.id_orden_despacho = zarethpr_proynew.orden_despacho.id) as alistamiento, (select zarethpr_proynew.orden_empacado.estado from zarethpr_proynew.orden_empacado where zarethpr_proynew.orden_empacado.id_orden_despacho = zarethpr_proynew.orden_despacho.id) as empacado from zarethpr_proynew.orden_despacho');
            }
            $fecha = Carbon::now()->format('Y-m-d');
            return view('filtro.indexOrdenesDespachoClientes',compact('ordenesClientes','fecha'));
        }
    }
    
    public function viewOrdenesClientes($consecutivo)
    {
        $cliente = DB::select('select zarethpr_proynew.despacho.nit, zarethpr_proynew.despacho.cliente, zarethpr_proynew.despacho.sucursal, 
        zarethpr_proynew.despacho.departamento, zarethpr_proynew.despacho.ciudad, zarethpr_proynew.despacho.direccion, zarethpr_proynew.despacho.estado_orden
        from zarethpr_proynew.despacho where zarethpr_proynew.despacho.consecutivo = '.$consecutivo.'
        GROUP BY zarethpr_proynew.despacho.nit, zarethpr_proynew.despacho.cliente, zarethpr_proynew.despacho.sucursal 
        HAVING COUNT(*)>0 ORDER BY zarethpr_proynew.despacho.cliente ASC');
        if(count($cliente) > 0){
            $cliente = $cliente[0];
        }else{
            $cliente = DB::select('select * from zarethpr_proynew.orden_despacho where zarethpr_proynew.orden_despacho.consecutivo = '.$consecutivo);
            $cliente = $cliente[0];
        }
        $detallesOrdenCliente = DB::select('select *, (select zarethpr_proynew.orden_alistamiento.estado from zarethpr_proynew.orden_alistamiento where zarethpr_proynew.orden_alistamiento.id_orden_despacho = zarethpr_proynew.despacho.id_od) as alistamiento from zarethpr_proynew.despacho where zarethpr_proynew.despacho.consecutivo = '.$consecutivo);
        return view('filtro.viewOrdenDespachoCliente',compact('cliente','detallesOrdenCliente','consecutivo'));
    }

    public function printOrdenesDespacho()
    {
        $ordenes = [];
        $ordenesDespacho = DB::select('SELECT * FROM zarethpr_proynew.orden_despacho WHERE zarethpr_proynew.orden_despacho.estado = "FACTURANDO"');
        for ($i=0; $i <count($ordenesDespacho) ; $i++) { 
            $detalles = [];
            $detallesOrdenDespacho = DB::select('SELECT * FROM zarethpr_proynew.detalles_orden_despacho WHERE zarethpr_proynew.detalles_orden_despacho.id_orden_despacho = '.$ordenesDespacho[$i]->id.'
            AND zarethpr_proynew.detalles_orden_despacho.estado = "FACTURAR"');
            for ($j=0; $j <count($detallesOrdenDespacho) ; $j++) { 
                $detalles[$j] = $detallesOrdenDespacho[$j];
            }
            if(count($detallesOrdenDespacho)>0){
                $observaciones = DB::select('select * from zarethpr_proynew.pedidos where zarethpr_proynew.pedidos.id = '.$detallesOrdenDespacho[0]->id_pedido);
                $ordenesDespacho[$i]->obs = $observaciones[0]->observaciones;
                $ordenesDespacho[$i]->obscartera = $observaciones[0]->obs_cartera;
            }else{
                $ordenesDespacho[$i]->obs = "";
                $ordenesDespacho[$i]->obscartera = "";
            }
            $ordenes[$i][0] = $ordenesDespacho[$i];
            $ordenes[$i][1] = $detalles;
        }
        //$pdf = PDF::loadView('filtro.printOrdenesDespacho',compact('ordenes'))->setOptions(['defaultFont' => 'sans-serif']);;
        //return $pdf->download('Ordenes Despacho.pdf');
        return view('filtro.printOrdenesDespacho',compact('ordenes'));
    }

    public function printOrdenesDespachoActualizadas()
    {
        $ordenes = [];
        $fecha = Carbon::now()->format('Y-m-d');
        $ordenesDespacho = DB::select('SELECT * FROM zarethpr_proynew.orden_despacho WHERE (zarethpr_proynew.orden_despacho.estado = "PREPARANDO" OR zarethpr_proynew.orden_despacho.estado = "ALISTANDO") AND
        DATE_FORMAT(zarethpr_proynew.orden_despacho.updated_at, "%Y-%m-%d") = "'.$fecha.'" AND zarethpr_proynew.orden_despacho.updated_at != zarethpr_proynew.orden_despacho.created_at');
        for ($i=0; $i <count($ordenesDespacho) ; $i++) { 
            $detalles = [];
            $detallesOrdenDespacho = DB::select('SELECT * FROM zarethpr_proynew.detalles_orden_despacho WHERE zarethpr_proynew.detalles_orden_despacho.id_orden_despacho = '.$ordenesDespacho[$i]->id.'
            AND (zarethpr_proynew.detalles_orden_despacho.estado = "APROBADO" OR zarethpr_proynew.detalles_orden_despacho.estado = "ALISTAR")');
            for ($j=0; $j <count($detallesOrdenDespacho) ; $j++) { 
                $detalles[$j] = $detallesOrdenDespacho[$j];
            }
            if(count($detallesOrdenDespacho)>0){
                $observaciones = DB::select('select * from zarethpr_proynew.pedidos where zarethpr_proynew.pedidos.id = '.$detallesOrdenDespacho[0]->id_pedido);
                $ordenesDespacho[$i]->obs = $observaciones[0]->observaciones;
                $ordenesDespacho[$i]->obscartera = $observaciones[0]->obs_cartera;
            }else{
                $ordenesDespacho[$i]->obs = "";
                $ordenesDespacho[$i]->obscartera = "";
            }
            $ordenes[$i][0] = $ordenesDespacho[$i];
            $ordenes[$i][1] = $detalles;
        }
        //$pdf = PDF::loadView('filtro.printOrdenesDespacho',compact('ordenes'))->setOptions(['defaultFont' => 'sans-serif']);;
        //return $pdf->download('Ordenes Despacho.pdf');
        return view('filtro.printOrdenesDespachoActualizadas',compact('ordenes','fecha'));
    }

    public function excelDownloadOrdenesFacturar()
    {
        $ordenes = DB::select('SELECT * from zarethpr_proynew.despacho where (zarethpr_proynew.despacho.estado_orden = "FACTURANDO" AND 
        zarethpr_proynew.despacho.estado_detalle_orden = "FACTURAR") OR (zarethpr_proynew.despacho.estado_orden = "ALISTANDO" AND 
        zarethpr_proynew.despacho.estado_detalle_orden = "ALISTAR")');
        return Excel::download(new FacturacionExport($ordenes),"ORDENES FACTURAR.xlsx");
    }

    public function printOrdenesClientes($consecutivo)
    {
        $cliente = DB::select('select * from zarethpr_proynew.orden_despacho where zarethpr_proynew.orden_despacho.consecutivo = '.$consecutivo);
        $cliente = $cliente[0];
        $detallesOrdenCliente = DB::select('select * from zarethpr_proynew.despacho where (zarethpr_proynew.despacho.estado_detalle_orden = "APROBADO" OR zarethpr_proynew.despacho.estado_detalle_orden = "DESPACHADO"
        OR zarethpr_proynew.despacho.estado_detalle_orden = "FACTURAR" OR zarethpr_proynew.despacho.estado_detalle_orden = "ALISTAR") and zarethpr_proynew.despacho.consecutivo = '.$consecutivo);
        if(count($detallesOrdenCliente)>0){
            $observaciones = DB::select('select * from zarethpr_proynew.pedidos where zarethpr_proynew.pedidos.id = '.$detallesOrdenCliente[0]->id_pedido);
            $cliente->obs = $observaciones[0]->observaciones;
            $cliente->obscartera = $observaciones[0]->obs_cartera;
        }else{
            $cliente->obs = "";
            $cliente->obscartera = "";
        }
        return view('filtro.printOrdenDespachoCliente',compact('cliente','detallesOrdenCliente','consecutivo'));
    }

    public function reversarOrdenesClientes($id)
    {
        $consulta = DB::select('SELECT * FROM zarethpr_proynew.detalles_orden_despacho WHERE zarethpr_proynew.detalles_orden_despacho.id_orden_despacho = '.$id.' 
        AND zarethpr_proynew.detalles_orden_despacho.estado = "ALISTAR"');
        DB::table('zarethpr_proynew.orden_despacho')->where('id','=',$id)->update(['estado'=>'PREPARANDO']);
        for ($i=0;$i<count($consulta);$i++) { 
            DB::table('zarethpr_proynew.detalles_orden_despacho')->where('id','=',$consulta[$i]->id)->update(['estado'=>'APROBADO']);
        }
        return back();
    }

    public function facturarOrdenesClientes($id)
    {
        $consulta = DB::select('SELECT * FROM zarethpr_proynew.detalles_orden_despacho WHERE zarethpr_proynew.detalles_orden_despacho.id_orden_despacho = '.$id.' 
        AND (zarethpr_proynew.detalles_orden_despacho.estado = "EMPACAR" OR zarethpr_proynew.detalles_orden_despacho.estado = "ALISTAR")');
        DB::table('zarethpr_proynew.orden_despacho')->where('id','=',$id)->update(['estado'=>'FACTURANDO']);
        for ($i=0;$i<count($consulta);$i++) { 
            DB::table('zarethpr_proynew.detalles_orden_despacho')->where('id','=',$consulta[$i]->id)->update(['estado'=>'FACTURAR']);
        }
        return back();
    }

    public function addFacturasOrdenesClientes($id, Request $request)
    {
        $facturas="";
        for ($i=0; $i < count($request->facturas); $i++) { 
            $facturas = $facturas.($i+1)." - ".$request->facturas[$i]."\n";
        }
        DB::table('zarethpr_proynew.orden_despacho')->where('id','=',$id)->update(['facturas'=>$facturas, 'user_factura'=>Auth::user()->id]);
        return back();
    }

    public function alistarOrdenesClientes($id)
    {
        $consulta = DB::select('SELECT * FROM zarethpr_proynew.detalles_orden_despacho WHERE zarethpr_proynew.detalles_orden_despacho.id_orden_despacho = '.$id.' 
        AND zarethpr_proynew.detalles_orden_despacho.estado = "APROBADO"');
        DB::table('zarethpr_proynew.orden_despacho')->where('id','=',$id)->update(['estado'=>'ALISTANDO']);
        for ($i=0;$i<count($consulta);$i++) { 
            DB::table('zarethpr_proynew.detalles_orden_despacho')->where('id','=',$consulta[$i]->id)->update(['estado'=>'ALISTAR']);
        }
        return back();
    }

    private function consultaDespachoVsAlistamiento($id)
    {
        $consulta = DB::select('SELECT detalles_orden_alistamiento.id_orden_alistamiento as orden_alistamiento,detalles_orden_alistamiento.id as id_detalle_orden_alistamiento, detalles_orden_alistamiento.id_detalle_orden_despacho as id_detalle_orden_despacho, 
        detalles_orden_alistamiento.t04 as t04_a, detalles_orden_alistamiento.t06 as t06_a, detalles_orden_alistamiento.t08 as t08_a, detalles_orden_alistamiento.t10 as t10_a, detalles_orden_alistamiento.t12 as t12_a,
        detalles_orden_alistamiento.t14 as t14_a, detalles_orden_alistamiento.t16 as t16_a, detalles_orden_alistamiento.t18 as t18_a, detalles_orden_alistamiento.t20 as t20_a, detalles_orden_alistamiento.t22 as t22_a, 
        detalles_orden_alistamiento.t24 as t24_a, detalles_orden_alistamiento.t26 as t26_a, detalles_orden_alistamiento.t28 as t28_a, detalles_orden_alistamiento.t30 as t30_a, detalles_orden_alistamiento.t32 as t32_a, 
        detalles_orden_alistamiento.t34 as t34_a, detalles_orden_alistamiento.t36 as t36_a, detalles_orden_alistamiento.t38 as t38_a, detalles_orden_alistamiento.xs as xs_a, detalles_orden_alistamiento.s as s_a, 
        detalles_orden_alistamiento.m as m_a, detalles_orden_alistamiento.l as l_a, detalles_orden_alistamiento.xl as xl_a, (detalles_orden_alistamiento.t04+detalles_orden_alistamiento.t06+detalles_orden_alistamiento.t08+
        detalles_orden_alistamiento.t10+detalles_orden_alistamiento.t12+detalles_orden_alistamiento.t14+detalles_orden_alistamiento.t16+detalles_orden_alistamiento.t18+detalles_orden_alistamiento.t20+detalles_orden_alistamiento.t22+
        detalles_orden_alistamiento.t24+detalles_orden_alistamiento.t26+detalles_orden_alistamiento.t28+detalles_orden_alistamiento.t30+detalles_orden_alistamiento.t32+detalles_orden_alistamiento.t34+detalles_orden_alistamiento.t36+
        detalles_orden_alistamiento.t38+detalles_orden_alistamiento.xs+detalles_orden_alistamiento.s+detalles_orden_alistamiento.m+detalles_orden_alistamiento.l+detalles_orden_alistamiento.xl) as sum_a,
        detalles_orden_alistamiento.estado as estado_a, detalles_orden_despacho.vendedor as vendedor, detalles_orden_despacho.id_pedido as id_pedido, detalles_orden_despacho.id_amarrador as id_amarrador, detalles_orden_despacho.referencia as referencia, zarethpr_proynew.detalles_orden_despacho.id_orden_despacho as orden_despacho,
        detalles_orden_despacho.t04 as t04_d, detalles_orden_despacho.t06 as t06_d, detalles_orden_despacho.t08 as t08_d, detalles_orden_despacho.t10 as t10_d, 
        detalles_orden_despacho.t12 as t12_d, detalles_orden_despacho.t14 as t14_d, detalles_orden_despacho.t16 as t16_d, detalles_orden_despacho.t18 as t18_d, detalles_orden_despacho.t20 as t20_d, detalles_orden_despacho.t22 as t22_d, 
        detalles_orden_despacho.t24 as t24_d, detalles_orden_despacho.t26 as t26_d, detalles_orden_despacho.t28 as t28_d, detalles_orden_despacho.t30 as t30_d, detalles_orden_despacho.t32 as t32_d, detalles_orden_despacho.t34 as t34_d, 
        detalles_orden_despacho.t36 as t36_d, detalles_orden_despacho.t38 as t38_d, detalles_orden_despacho.xs as xs_d, detalles_orden_despacho.s as s_d, detalles_orden_despacho.m as m_d, detalles_orden_despacho.l as l_d, 
        detalles_orden_despacho.xl as xl_d, (detalles_orden_despacho.t04+detalles_orden_despacho.t06+detalles_orden_despacho.t08+detalles_orden_despacho.t10+detalles_orden_despacho.t12+detalles_orden_despacho.t14+detalles_orden_despacho.t16+
        detalles_orden_despacho.t18+detalles_orden_despacho.t20+detalles_orden_despacho.t22+detalles_orden_despacho.t24+detalles_orden_despacho.t26+detalles_orden_despacho.t28+detalles_orden_despacho.t30+detalles_orden_despacho.t32+
        detalles_orden_despacho.t34+detalles_orden_despacho.t36+detalles_orden_despacho.t38+detalles_orden_despacho.xs+detalles_orden_despacho.s+detalles_orden_despacho.m+detalles_orden_despacho.l+detalles_orden_despacho.xl) as sum_d, 
        detalles_orden_despacho.estado as estado_d FROM (zarethpr_proynew.detalles_orden_despacho join zarethpr_proynew.detalles_orden_alistamiento 
        on(zarethpr_proynew.detalles_orden_alistamiento.id_detalle_orden_despacho = zarethpr_proynew.detalles_orden_despacho.id)) WHERE zarethpr_proynew.detalles_orden_despacho.id_orden_despacho = '.$id.' AND zarethpr_proynew.detalles_orden_despacho.estado = "ALISTAR"');

        return $consulta;
    }

    public function revisarOrdenesClientes($consecutivo)
    {
        $consulta = DB::select('SELECT * FROM zarethpr_proynew.despacho WHERE zarethpr_proynew.despacho.consecutivo = "'.$consecutivo.'"');
        $consulta = $consulta[0];
        $consulta_detalles = $this->consultaDespachoVsAlistamiento($consulta->id_od);
        $orden_despacho = $consulta->id_od;
        return view('filtro.revisarDetalleOrdenDespachoCliente',compact('consecutivo','consulta','consulta_detalles','orden_despacho'));
    }

    public function aprobarRevisionOrdenesClientes($consecutivo, $id)
    {
        $consulta = DB::select('SELECT * FROM zarethpr_proynew.orden_alistamiento WHERE zarethpr_proynew.orden_alistamiento.id_orden_despacho = '.$id);
        $detalles = DB::select('SELECT * FROM zarethpr_proynew.detalles_orden_alistamiento WHERE zarethpr_proynew.detalles_orden_alistamiento.id_orden_alistamiento = '.$consulta[0]->id);
        DB::table('zarethpr_proynew.orden_alistamiento')->where('id', '=', $consulta[0]->id)->update(['estado'=>"FINALIZADO",'updated_at'=>Carbon::now()]);
        foreach ($detalles as $d) {
            DB::table('zarethpr_proynew.detalles_orden_despacho')->where('id', '=', $d->id_detalle_orden_despacho)->update(['t04'=>$d->t04,'t06'=>$d->t06,'t08'=>$d->t08,'t10'=>$d->t10,'t12'=>$d->t12,'t14'=>$d->t14,'t16'=>$d->t16,'t18'=>$d->t18,'t20'=>$d->t20,'t22'=>$d->t22,'t24'=>$d->t24,'t30'=>$d->t30,'t32'=>$d->t32,'t34'=>$d->t34,'t36'=>$d->t36,'t38'=>$d->t38,'updated_at'=>Carbon::now()]);
        }
        return redirect()->route('filtro.listado.ordenes.clientes');
    }

    public function cancelarRevisionOrdenesClientes($consecutivo, $id)
    {
        $consulta = DB::select('SELECT * FROM zarethpr_proynew.orden_alistamiento WHERE zarethpr_proynew.orden_alistamiento.id_orden_despacho = '.$id);
        DB::table('zarethpr_proynew.detalles_orden_alistamiento')->where('id_orden_alistamiento', '=', $consulta[0]->id)->delete();
        DB::table('zarethpr_proynew.orden_despacho')->where('id','=',$id)->update(['user_alista'=>null]);
        DB::table('zarethpr_proynew.orden_alistamiento')->where('id', '=', $consulta[0]->id)->delete();
        return redirect()->route('filtro.listado.ordenes.clientes');
    }

    public function alistarPickingOrdenesClientes($id)
    {
        $consulta = DB::select('SELECT * FROM zarethpr_proynew.orden_alistamiento WHERE zarethpr_proynew.orden_alistamiento.id_orden_despacho = '.$id);
        if(empty($consulta)){
            $consulta_orden_despacho = DB::select('SELECT * FROM zarethpr_proynew.orden_despacho WHERE zarethpr_proynew.orden_despacho.id = '.$id);
            $consulta_detalles_despacho = DB::select('SELECT * FROM zarethpr_proynew.detalles_orden_despacho WHERE zarethpr_proynew.detalles_orden_despacho.id_orden_despacho = '.$id.' 
            AND zarethpr_proynew.detalles_orden_despacho.estado = "ALISTAR"');
            DB::insert('insert into zarethpr_proynew.orden_alistamiento (id_orden_despacho, user_alista, estado, fecha, created_at) values (?, ?, ?, ?, ?)', [$id, Auth::user()->id, "INICIADO", Carbon::now()->format('Y-m-d'), Carbon::now()]);
            DB::table('zarethpr_proynew.orden_despacho')->where('id','=',$id)->update(['user_alista'=>Auth::user()->id]);
            $consulta = DB::select('select * from zarethpr_proynew.orden_alistamiento where zarethpr_proynew.orden_alistamiento.id_orden_despacho = '.$id);
            for ($i=0;$i<count($consulta_detalles_despacho);$i++) { 
                DB::insert('insert into zarethpr_proynew.detalles_orden_alistamiento (id_orden_alistamiento, id_detalle_orden_despacho, estado, created_at) values (?, ?, ?, ?)', [$consulta[0]->id, $consulta_detalles_despacho[$i]->id, "FALTAN", Carbon::now()]);
            }   
            $consulta_detalles = $this->consultaDespachoVsAlistamiento($id);
            $od = DB::select('SELECT * FROM zarethpr_proynew.detalles_orden_alistamiento WHERE zarethpr_proynew.detalles_orden_alistamiento.id_orden_alistamiento = '.$consulta_detalles[0]->orden_alistamiento.' and zarethpr_proynew.detalles_orden_alistamiento.estado = "FALTAN"');
            return view('filtro.alistarDetalleOrdenDespachoCliente',compact('consulta_orden_despacho','consulta_detalles','id','od'));
        }else{
            if($consulta[0]->user_alista == Auth::user()->id && $consulta[0]->estado == "INICIADO"){
                $consulta_orden_despacho = DB::select('SELECT * FROM zarethpr_proynew.orden_despacho WHERE zarethpr_proynew.orden_despacho.id = '.$id);
                $consulta_detalles = $this->consultaDespachoVsAlistamiento($id);
                $od = DB::select('SELECT * FROM zarethpr_proynew.detalles_orden_alistamiento WHERE zarethpr_proynew.detalles_orden_alistamiento.id_orden_alistamiento = '.$consulta_detalles[0]->orden_alistamiento.' and zarethpr_proynew.detalles_orden_alistamiento.estado = "FALTAN"');
                return view('filtro.alistarDetalleOrdenDespachoCliente',compact('consulta_orden_despacho','consulta_detalles','id','od'));
            }else{
                return redirect()->route('filtro.listado.ordenes.clientes');
            }
        }
    }

    public function addAlistarPickingOrdenesClientes($id, Request $request)
    {   
        DB::table('zarethpr_proynew.detalles_orden_alistamiento')->where('id','=',$request->id_detalle_orden_alistamiento)->update(['estado'=>$request->estado,$request->talla=>DB::raw($request->talla." + 1"),'updated_at'=>Carbon::now()]);
        $consulta = DB::select('SELECT * FROM zarethpr_proynew.detalles_orden_alistamiento WHERE zarethpr_proynew.detalles_orden_alistamiento.id_orden_alistamiento = '.$request->id_orden_alistamiento.' and zarethpr_proynew.detalles_orden_alistamiento.estado = "FALTAN"');
        if(empty($consulta)){
            /*DB::table('zarethpr_proynew.orden_alistamiento')->where('id','=',$request->id_orden_alistamiento)->update(['estado'=>"FINALIZADO",'updated_at'=>Carbon::now()]);
            $consulta = DB::select('SELECT * FROM zarethpr_proynew.detalles_orden_despacho WHERE zarethpr_proynew.detalles_orden_despacho.id_orden_despacho = '.$id.' 
            AND zarethpr_proynew.detalles_orden_despacho.estado = "ALISTAR"');
            DB::table('zarethpr_proynew.orden_despacho')->where('id','=',$id)->update(['estado'=>'EMPACANDO']);
            for ($i=0;$i<count($consulta);$i++) { 
                DB::table('zarethpr_proynew.detalles_orden_despacho')->where('id','=',$consulta[$i]->id)->update(['estado'=>'EMPACAR']);
            }*/
            return response()->json([
                "msg" => "Orden Finalizada",
                "estado" => 1
            ], 200);
        }else{
            return response()->json([
                "msg" => "¡Hecho!",
                "estado" => 2
            ], 200);
        }
    }

    public function aceptarAlistarPickingOrdenesClientes($id)
    {   
        DB::table('zarethpr_proynew.orden_alistamiento')->where('id_orden_despacho','=',$id)->update(['estado'=>'FINALIZADO','updated_at'=>Carbon::now()]);
        return back();
    }

    public function revisarAlistarPickingOrdenesClientes($id, Request $request)
    {   
        $consulta = DB::select('SELECT * FROM zarethpr_proynew.orden_alistamiento WHERE zarethpr_proynew.orden_alistamiento.id_orden_despacho = '.$id);
        if($request->revisar == 1){
            DB::table('zarethpr_proynew.orden_alistamiento')->where('id', '=', $consulta[0]->id)->update(['estado'=>"REVISION",'updated_at'=>Carbon::now()]);
            return redirect()->route('filtro.listado.ordenes.clientes');
        }else{
            $detalles = DB::select('SELECT * FROM zarethpr_proynew.detalles_orden_alistamiento WHERE zarethpr_proynew.detalles_orden_alistamiento.id_orden_alistamiento = '.$consulta[0]->id);
            DB::table('zarethpr_proynew.orden_alistamiento')->where('id', '=', $consulta[0]->id)->update(['estado'=>"FINALIZADO",'updated_at'=>Carbon::now()]);
            foreach ($detalles as $d) {
                DB::table('zarethpr_proynew.detalles_orden_despacho')->where('id', '=', $d->id_detalle_orden_despacho)->update(['t04'=>$d->t04,'t06'=>$d->t06,'t08'=>$d->t08,'t10'=>$d->t10,'t12'=>$d->t12,'t14'=>$d->t14,'t16'=>$d->t16,'t18'=>$d->t18,'t20'=>$d->t20,'t22'=>$d->t22,'t24'=>$d->t24,'t30'=>$d->t30,'t32'=>$d->t32,'t34'=>$d->t34,'t36'=>$d->t36,'t38'=>$d->t38,'updated_at'=>Carbon::now()]);
            }
            return redirect()->route("filtro.listado.ordenes.clientes.empacar.picking",$id);
        }
    }

    public function cancelarAlistarPickingOrdenesClientes($id)
    {   
        $consulta = DB::select('SELECT * FROM zarethpr_proynew.orden_alistamiento WHERE zarethpr_proynew.orden_alistamiento.id_orden_despacho = '.$id);
        DB::table('zarethpr_proynew.detalles_orden_alistamiento')->where('id_orden_alistamiento', '=', $consulta[0]->id)->delete();
        DB::table('zarethpr_proynew.orden_despacho')->where('id','=',$id)->update(['user_alista'=>null]);
        DB::table('zarethpr_proynew.orden_alistamiento')->where('id', '=', $consulta[0]->id)->delete();
        return redirect()->route('filtro.listado.ordenes.clientes');
    }

    public function empacarOrdenesClientes($id)
    {
        $consulta = DB::select('SELECT * FROM zarethpr_proynew.detalles_orden_despacho WHERE zarethpr_proynew.detalles_orden_despacho.id_orden_despacho = '.$id.' 
        AND zarethpr_proynew.detalles_orden_despacho.estado = "ALISTAR"');
        DB::table('zarethpr_proynew.orden_despacho')->where('id','=',$id)->update(['estado'=>'EMPACANDO']);
        for ($i=0;$i<count($consulta);$i++) { 
            DB::table('zarethpr_proynew.detalles_orden_despacho')->where('id','=',$consulta[$i]->id)->update(['estado'=>'EMPACAR']);
        }
        return back();
    }

    private function consultaDespachoVsEmpacado($id)
    {
        $consulta = DB::select('SELECT id, id_orden_despacho as orden_despacho, id_pedido, id_amarrador, vendedor, referencia, t04 as t04_d, t06 as t06_d, t08 as t08_d, t10 as t10_d, t12 as t12_d, t14 as t14_d, t16 as t16_d, t18 as t18_d, t20 as t20_d, t22 as t22_d, t24 as t24_d, t26 as t26_d, t28 as t28_d, t30 as t30_d, t32 as t32_d, t34 as t34_d, t36 as t36_d, t38 as t38_d, xs as xs_d, s as s_d, m as m_d, l as l_d, xl as xl_d FROM zarethpr_proynew.detalles_orden_despacho WHERE id_orden_despacho = '.$id);
        $empacar = 0;
        $despachar = 0;
        foreach ($consulta as $c) {
            $cons = DB::select('SELECT id_orden_empacado AS orden_empacado, id_empaque as empaque, sum(t04) as t04, sum(t06) as t06, sum(t08) as t08, sum(t10) as t10, sum(t12) as t12, sum(t14) as t14, sum(t16) as t16, sum(t18) as t18, sum(t20) as t20, sum(t22) as t22, sum(t24) as t24, sum(t26) as t26, sum(t28) as t28, sum(t30) as t30, sum(t32) as t32, sum(t34) as t34, sum(t36) as t36, sum(t38) as t38, sum(xs) as xs, sum(s) as s, sum(m) as m, sum(l) as l, sum(xl) as xl FROM zarethpr_proynew.detalles_orden_empacado WHERE id_detalle_orden_despacho = '.$c->id.' GROUP BY id_detalle_orden_despacho HAVING COUNT(*)>0');
            $c->sum_d = $c->t04_d+$c->t06_d+$c->t08_d+$c->t10_d+$c->t12_d+$c->t14_d+$c->t16_d+$c->t18_d+$c->t20_d+$c->t22_d+$c->t24_d+$c->t26_d+$c->t28_d+$c->t30_d+$c->t32_d+$c->t34_d+$c->t36_d+$c->t38_d+$c->xs_d +$c->s_d+$c->m_d+$c->l_d+$c->xl_d;
            $c->t04_e = empty($cons[0]->t04) ? 0 : intVal($cons[0]->t04);
            $c->t06_e = empty($cons[0]->t06) ? 0 : intVal($cons[0]->t06);
            $c->t08_e = empty($cons[0]->t08) ? 0 : intVal($cons[0]->t08);
            $c->t10_e = empty($cons[0]->t10) ? 0 : intVal($cons[0]->t10);
            $c->t12_e = empty($cons[0]->t12) ? 0 : intVal($cons[0]->t12);
            $c->t14_e = empty($cons[0]->t14) ? 0 : intVal($cons[0]->t14);
            $c->t16_e = empty($cons[0]->t16) ? 0 : intVal($cons[0]->t16);
            $c->t18_e = empty($cons[0]->t18) ? 0 : intVal($cons[0]->t18);
            $c->t20_e = empty($cons[0]->t20) ? 0 : intVal($cons[0]->t20);
            $c->t22_e = empty($cons[0]->t22) ? 0 : intVal($cons[0]->t22);
            $c->t24_e = empty($cons[0]->t24) ? 0 : intVal($cons[0]->t24);
            $c->t26_e = empty($cons[0]->t26) ? 0 : intVal($cons[0]->t26);
            $c->t28_e = empty($cons[0]->t28) ? 0 : intVal($cons[0]->t28);
            $c->t30_e = empty($cons[0]->t30) ? 0 : intVal($cons[0]->t30);
            $c->t32_e = empty($cons[0]->t32) ? 0 : intVal($cons[0]->t32);
            $c->t34_e = empty($cons[0]->t34) ? 0 : intVal($cons[0]->t34);
            $c->t36_e = empty($cons[0]->t36) ? 0 : intVal($cons[0]->t36);
            $c->t38_e = empty($cons[0]->t38) ? 0 : intVal($cons[0]->t38);
            $c->xs_e = empty($cons[0]->xs) ? 0 : intVal($cons[0]->xs);
            $c->s_e = empty($cons[0]->s) ? 0 : intVal($cons[0]->s);
            $c->m_e = empty($cons[0]->m) ? 0 : intVal($cons[0]->m);
            $c->l_e = empty($cons[0]->l) ? 0 : intVal($cons[0]->l);
            $c->xl_e = empty($cons[0]->xl) ? 0 : intVal($cons[0]->xl);
            $c->sum_e = $c->t04_e+$c->t06_e+$c->t08_e+$c->t10_e+$c->t12_e+$c->t14_e+$c->t16_e+$c->t18_e+$c->t20_e+$c->t22_e+$c->t24_e+$c->t26_e+$c->t28_e+$c->t30_e+$c->t32_e+$c->t34_e+$c->t36_e+$c->t38_e+$c->xs_e +$c->s_e+$c->m_e+$c->l_e+$c->xl_e;
            $empacar+=$c->sum_e;
            $despachar+=$c->sum_d;
        }
        return [$consulta,$empacar,$despachar];
    }

    private function empaques($id)
    {
        $empaques = DB::select('SELECT * FROM zarethpr_proynew.empaque WHERE zarethpr_proynew.empaque.id_orden_empacado = '.$id);
        foreach ($empaques as $e) {
            $e->detalles = DB::select('SELECT *,(t04+t06+t08+t10+t12+t14+t16+t18+t20+t22+t24+t26+t28+t30+t32+t34+t36+t38+xs+s+m+l+xl) as sum , (SELECT referencia FROM zarethpr_proynew.detalles_orden_despacho WHERE detalles_orden_despacho.id = detalles_orden_empacado.id_detalle_orden_despacho) as referencia FROM zarethpr_proynew.detalles_orden_empacado WHERE zarethpr_proynew.detalles_orden_empacado.id_empaque = '.$e->id);
        }
        return $empaques;
    }

    public function empacarPickingOrdenesClientes($id)
    {
        $consulta = DB::select('SELECT * FROM zarethpr_proynew.orden_empacado WHERE zarethpr_proynew.orden_empacado.id_orden_despacho = '.$id);
        if(empty($consulta)){
            DB::table('zarethpr_proynew.orden_alistamiento')->where('id_orden_despacho','=',$id)->update(['estado'=>'FINALIZADO']);
            $alis = DB::select('SELECT * FROM zarethpr_proynew.detalles_orden_despacho WHERE zarethpr_proynew.detalles_orden_despacho.id_orden_despacho = '.$id.' 
            AND zarethpr_proynew.detalles_orden_despacho.estado = "ALISTAR"');
            DB::table('zarethpr_proynew.orden_despacho')->where('id','=',$id)->update(['estado'=>'EMPACANDO']);
            for ($i=0;$i<count($alis);$i++) { 
                DB::table('zarethpr_proynew.detalles_orden_despacho')->where('id','=',$alis[$i]->id)->update(['estado'=>'EMPACAR']);
            }

            $consulta_orden_despacho = DB::select('SELECT * FROM zarethpr_proynew.orden_despacho WHERE zarethpr_proynew.orden_despacho.id = '.$id);
            DB::insert('insert into zarethpr_proynew.orden_empacado (id_orden_despacho, user_empaca, estado, fecha, created_at) values (?, ?, ?, ?, ?)', [$id, Auth::user()->id, "INICIADO", Carbon::now()->format('Y-m-d'), Carbon::now()]);
            DB::table('zarethpr_proynew.orden_despacho')->where('id','=',$id)->update(['user_empaca'=>Auth::user()->id]);
            $consulta = DB::select('SELECT * FROM zarethpr_proynew.orden_empacado WHERE zarethpr_proynew.orden_empacado.id_orden_despacho = '.$id);
            $empaque = DB::select('SELECT * FROM zarethpr_proynew.empaque WHERE estado = "ABIERTO" AND id_orden_empacado ='.$consulta[0]->id);
            $consulta_detalles = $this->consultaDespachoVsEmpacado($id);
            $empacar = $consulta_detalles[1];
            $despachar = $consulta_detalles[2];
            $consulta_detalles = $consulta_detalles[0];
            $empaques = $this->empaques($consulta[0]->id);
            return view('filtro.empacarDetalleOrdenDespachoCliente',compact('consulta_orden_despacho','consulta_detalles','consulta','empaque','empaques','empacar','despachar'));
        }else{
            if($consulta[0]->user_empaca == Auth::user()->id && $consulta[0]->estado == "INICIADO"){
                $consulta_orden_despacho = DB::select('SELECT * FROM zarethpr_proynew.orden_despacho WHERE zarethpr_proynew.orden_despacho.id = '.$id);
                $consulta_detalles = $this->consultaDespachoVsEmpacado($id);
                $empacar = $consulta_detalles[1];
                $despachar = $consulta_detalles[2];
                $consulta_detalles = $consulta_detalles[0];
                $empaques = $this->empaques($consulta[0]->id);
                $empaque = DB::select('SELECT * FROM zarethpr_proynew.empaque WHERE estado = "ABIERTO" AND id_orden_empacado ='.$consulta[0]->id);
                return view('filtro.empacarDetalleOrdenDespachoCliente',compact('consulta_orden_despacho','consulta_detalles','consulta','empaque','empaques','empacar','despachar'));
            }else{
                return redirect()->route('filtro.listado.ordenes.clientes');
            }
        }
    }

    public function crearEmpacarPickingOrdenesClientes($id, Request $request)
    {
        $consulta = DB::select('SELECT * FROM zarethpr_proynew.orden_empacado WHERE zarethpr_proynew.orden_empacado.id_orden_despacho = '.$id);
        DB::insert('insert into zarethpr_proynew.empaque (id_orden_empacado, tipo, estado, created_at) values (?, ?, ?, ?)', [$consulta[0]->id, $request->tipo, "ABIERTO", Carbon::now()]);
        return back();
    }

    public function addEmpacarPickingOrdenesClientes($id, Request $request)
    {   
        $consulta = DB::select('SELECT * FROM zarethpr_proynew.detalles_orden_empacado WHERE zarethpr_proynew.detalles_orden_empacado.id_detalle_orden_despacho = '.$request->id_detalle_orden_despacho.' and zarethpr_proynew.detalles_orden_empacado.id_empaque = '.$request->id_empaque);
        if(empty($consulta)){
            DB::insert('insert into zarethpr_proynew.detalles_orden_empacado (id_orden_empacado, id_empaque, id_detalle_orden_despacho, created_at) values (?, ?, ?, ?)', [$request->id_orden_empacado, $request->id_empaque, $request->id_detalle_orden_despacho, Carbon::now()]);
        }
        DB::table('zarethpr_proynew.detalles_orden_empacado')->where('id_orden_empacado','=',$request->id_orden_empacado)->where('id_empaque','=',$request->id_empaque)->where('id_detalle_orden_despacho','=',$request->id_detalle_orden_despacho)->update([$request->talla=>DB::raw($request->talla." + 1"),'updated_at'=>Carbon::now()]);
        return response()->json([
            "msg" => "¡Hecho!",
        ], 200);
        
    }

    public function cancelarEmpacarPickingOrdenesClientes($id)
    {
        DB::table('zarethpr_proynew.detalles_orden_empacado')->where('id_empaque', '=', $id)->delete();
        DB::table('zarethpr_proynew.empaque')->where('id', '=', $id)->delete();
        return back();
    }

    public function cerrarEmpacarPickingOrdenesClientes($id, Request $request)
    {
        if(empty($request->peso)){
            return back();
        }
        DB::table('zarethpr_proynew.empaque')->where('id', '=', $id)->update(['peso'=>$request->peso.' KG','estado'=>'CERRADO','updated_at'=>Carbon::now()]);
        return back();
    }

    public function modificarEmpacarPickingOrdenesClientes($id)
    {
        DB::table('zarethpr_proynew.empaque')->where('id', '=', $id)->update(['estado'=>'ABIERTO','updated_at'=>Carbon::now()]);
        return back();
    }

    public function finalizarEmpacarPickingOrdenesClientes($id)
    {
        DB::table('zarethpr_proynew.orden_empacado')->where('id', '=', $id)->update(['estado'=>'FINALIZADO','updated_at'=>Carbon::now()]);
        $consulta = DB::select('SELECT * FROM zarethpr_proynew.orden_empacado WHERE zarethpr_proynew.orden_empacado.id = '.$id);
        DB::table('zarethpr_proynew.orden_despacho')->where('id','=',$consulta[0]->id_orden_despacho)->update(['estado'=>'FACTURANDO']);
        $consulta = DB::select('SELECT * FROM zarethpr_proynew.detalles_orden_despacho WHERE zarethpr_proynew.detalles_orden_despacho.id_orden_despacho = '.$consulta[0]->id_orden_despacho.' 
        AND zarethpr_proynew.detalles_orden_despacho.estado = "EMPACAR"');
        for ($i=0;$i<count($consulta);$i++) { 
            DB::table('zarethpr_proynew.detalles_orden_despacho')->where('id','=',$consulta[$i]->id)->update(['estado'=>'FACTURAR']);
        }
        return back();
    }

    private function rotulosGenerate($id){
        $orden_despacho = DB::select('select * from zarethpr_proynew.orden_despacho where id = '.$id)[0]; 
        $orden_empaque = DB::select('select * from zarethpr_proynew.orden_empacado where id_orden_despacho = '.$id);
        if(!empty($orden_empaque)) {
            $orden_empaque = $orden_empaque[0];
        }else{
            $orden_empaque = (object) [];
            $orden_empaque->id = 0;
        }
        $empaques = DB::select('select * from zarethpr_proynew.empaque where id_orden_empacado = '.$orden_empaque->id);
        foreach($empaques as $empaque) {
            $empaque->unidades = DB::select('SELECT sum(t04+t06+t08+t10+t12+t14+t16+t18+t20+t22+t24+t26+t28+t30+t32+t34+t36+t38+xs+s+m+l+xl) as sum from zarethpr_proynew.detalles_orden_empacado where id_empaque = '.$empaque->id)[0]->sum;
            $empaque->fecha = Carbon::now()->format('d/m/Y h:i A');
            $empaque->consecutivo = $orden_despacho->consecutivo;
            $empaque->cliente = $orden_despacho->cliente;
            $empaque->nit = $orden_despacho->nit;
            $empaque->departamento = $orden_despacho->departamento;
            $empaque->ciudad = $orden_despacho->ciudad;
            $empaque->direccion = $orden_despacho->direccion;
            empty($orden_despacho->user_filtra) ? $orden_despacho->user_filtra = 0 : $orden_despacho->user_filtra;
            $filtrador = DB::select('SELECT concat(names," ",apellidos) as filtrador from zarethpr_siver.users where id = '.$orden_despacho->user_filtra);
            $empaque->filtrador = empty($filtrador) ? "GENERICO" : strtoupper($filtrador[0]->filtrador);
            empty($orden_despacho->user_alista) ? $orden_despacho->user_alista = 0 : $orden_despacho->user_alista;
            $alistador = DB::select('SELECT concat(names," ",apellidos) as alistador from zarethpr_siver.users where id = '.$orden_despacho->user_alista);
            $empaque->alistador = empty($alistador) ? "GENERICO" : strtoupper($alistador[0]->alistador);
            empty($orden_despacho->user_empaca) ? $orden_despacho->user_empaca = 0 : $orden_despacho->user_empaca;
            $empacador = DB::select('SELECT concat(names," ",apellidos) as empacador from zarethpr_siver.users where id = '.$orden_despacho->user_empaca);
            $empaque->empacador = empty($empacador) ? "GENERICO" : strtoupper($empacador[0]->empacador);
            empty($orden_despacho->user_factura) ? $orden_despacho->user_factura = 0 : $orden_despacho->user_factura;
            $facturador = DB::select('SELECT concat(names," ",apellidos) as facturador from zarethpr_siver.users where id = '.$orden_despacho->user_factura);
            $empaque->facturador = empty($facturador) ? "GENERICO" : strtoupper($facturador[0]->facturador);
            $empaque->facturas = $orden_despacho->facturas;
        }
        return $empaques;
    }

    public function rotulosOrdenesClientes($id)
    {
        $empaques = $this->rotulosGenerate($id);
        return view('filtro.viewRotulosOrdenDespachoCliente',compact('empaques'));
    }

    public function rotulosMultiplesOrdenesClientes(Request $request)
    {
        $clientes = DB::select('select zarethpr_proynew.despacho.nit, zarethpr_proynew.despacho.cliente, zarethpr_proynew.despacho.sucursal, zarethpr_proynew.despacho.ciudad, zarethpr_proynew.despacho.departamento, zarethpr_proynew.despacho.direccion 
        from zarethpr_proynew.despacho GROUP BY zarethpr_proynew.despacho.nit, zarethpr_proynew.despacho.cliente, zarethpr_proynew.despacho.sucursal 
        HAVING COUNT(*)>0 ORDER BY zarethpr_proynew.despacho.cliente ASC');
        $ordenes = DB::select('SELECT * FROM zarethpr_proynew.orden_despacho WHERE facturas IS NOT NULL');
        return view('filtro.generarRotuloMultiplesOrdenDespachoClientes',compact('clientes','ordenes'));
    }

    public function rotulosViewMultiplesOrdenesClientes(Request $request)
    {
        $empaques = [];
        $cliente = explode(" / ", $request->cliente);
        $datos = DB::select('SELECT * FROM zarethpr_proynew.orden_despacho WHERE consecutivo = "'.$request->ordenes[0].'"')[0];
        for ($i=0; $i < $request->range ; $i++) { 
            $empaque = (object) [];
            $empaque->tipo = $request->input("tipo_".$i);
            $empaque->peso = $request->input("peso_".$i)." KG";
            $empaque->unidades = $request->input("unds_".$i);
            $empaque->fecha = Carbon::now()->format('d/m/Y h:i A');
            $empaque->consecutivo = str_replace(['[', ']', '"'], '', json_encode($request->ordenes));
            $empaque->cliente = $cliente[5];
            $empaque->nit = $cliente[0];
            $empaque->departamento = $cliente[3];
            $empaque->ciudad = $cliente[2];
            $empaque->direccion = $cliente[4];
            empty($datos->user_filtra) ? $datos->user_filtra = 0 : $datos->user_filtra;
            $filtrador = DB::select('SELECT concat(names," ",apellidos) as filtrador from zarethpr_siver.users where id = '.$datos->user_filtra);
            $empaque->filtrador = empty($filtrador) ? "GENERICO" : strtoupper($filtrador[0]->filtrador);
            empty($datos->user_alista) ? $datos->user_alista = 0 : $datos->user_alista;
            $alistador = DB::select('SELECT concat(names," ",apellidos) as alistador from zarethpr_siver.users where id = '.$datos->user_alista);
            $empaque->alistador = empty($alistador) ? "GENERICO" : $alistador[0]->alistador;
            empty($datos->user_empaca) ? $datos->user_empaca = 0 : strtoupper($datos->user_empaca);
            $empacador = DB::select('SELECT concat(names," ",apellidos) as empacador from zarethpr_siver.users where id = '.$datos->user_empaca);
            $empaque->empacador = empty($empacador) ? "GENERICO" : strtoupper($empacador[0]->empacador);
            empty($datos->user_factura) ? $datos->user_factura = 0 : $datos->user_factura;
            $facturador = DB::select('SELECT concat(names," ",apellidos) as facturador from zarethpr_siver.users where id = '.$datos->user_factura);
            $empaque->facturador = empty($facturador) ? "GENERICO" : strtoupper($facturador[0]->facturador);
            $empaque->facturas = $datos->facturas;  
            $empaques [$i] = $empaque;
        }
        return view('filtro.viewRotulosOrdenDespachoCliente',compact('empaques'));
    }

    public function despacharOrdenesClientes($id)
    {
        $fdespacho = Carbon::now();
        $consulta = DB::select('SELECT * FROM zarethpr_proynew.detalles_orden_despacho WHERE zarethpr_proynew.detalles_orden_despacho.id_orden_despacho = '.$id.'
        AND zarethpr_proynew.detalles_orden_despacho.estado = "FACTURAR"');
        DB::table('zarethpr_proynew.orden_despacho')->where('id','=',$id)->update(['estado'=>'DESPACHADO']);
        for ($i=0;$i<count($consulta);$i++) { 
            DB::table('zarethpr_proynew.detalles')->where('id','=',$consulta[$i]->id_amarrador)->update(['despacho'=>'DESPACHADO', 'fdespacho'=>$fdespacho]);
            DB::table('zarethpr_proynew.detalles_orden_despacho')->where('id','=',$consulta[$i]->id)->update(['estado'=>'DESPACHADO']);
        }
        return back();
    }

    public function cancelarOrdenesClientes($id)
    {
        $consulta = DB::select('SELECT * FROM zarethpr_proynew.detalles_orden_despacho WHERE zarethpr_proynew.detalles_orden_despacho.id_orden_despacho = '.$id);
        DB::table('zarethpr_proynew.orden_despacho')->where('id','=',$id)->update(['estado'=>'CANCELADO']);
        for ($i=0;$i<count($consulta);$i++) { 
            if($consulta[$i]->estado != "PENDIENTE"){
                DB::table('zarethpr_proynew.detalles')->where('id','=',$consulta[$i]->id_amarrador)->update(['despacho'=>'CANCELADO']);
                DB::table('zarethpr_proynew.detalles_orden_despacho')->where('id','=',$consulta[$i]->id)->update(['estado'=>'CANCELADO']);
            }
        }
        return back();
    }

    public function aprobarDetalleOrdenCliente($id, $amarrador)
    {
        DB::table('zarethpr_proynew.detalles_orden_despacho')->where('id','=',$id)->update(['estado'=>'APROBADO']);
        DB::table('zarethpr_proynew.detalles')->where('id','=',$amarrador)->update(['despacho'=>'COMPROMETIDO']);
        return back();
    }

    public function cancelarDetalleOrdenCliente($id, $amarrador)
    {
        DB::table('zarethpr_proynew.detalles_orden_despacho')->where('id','=',$id)->update(['estado'=>'CANCELADO']);
        DB::table('zarethpr_proynew.detalles')->where('id','=',$amarrador)->update(['despacho'=>'CANCELADO']);
        return back();
    }

    public function pendienteDetalleOrdenCliente($id, $amarrador)
    {
        DB::table('zarethpr_proynew.detalles_orden_despacho')->where('id','=',$id)->update(['estado'=>'PENDIENTE']);
        DB::table('zarethpr_proynew.detalles')->where('id','=',$amarrador)->update(['despacho'=>'APROBADO']);
        return back();
    }

    public function editDetalleOrdenCliente($consecutivo, $amarrador)
    {
        $cliente = DB::select('select zarethpr_proynew.despacho.nit, zarethpr_proynew.despacho.cliente, zarethpr_proynew.despacho.sucursal, 
        zarethpr_proynew.despacho.departamento, zarethpr_proynew.despacho.ciudad, zarethpr_proynew.despacho.direccion, zarethpr_proynew.despacho.estado_orden
        from zarethpr_proynew.despacho where zarethpr_proynew.despacho.consecutivo = '.$consecutivo.'
        GROUP BY zarethpr_proynew.despacho.nit, zarethpr_proynew.despacho.cliente, zarethpr_proynew.despacho.sucursal 
        HAVING COUNT(*)>0 ORDER BY zarethpr_proynew.despacho.cliente ASC');
        $cliente = $cliente[0];
        $detalleOrdenCliente = DB::select('select * from zarethpr_proynew.despacho where zarethpr_proynew.despacho.consecutivo = "'.$consecutivo.'" 
        and zarethpr_proynew.despacho.id_amarrador = '.$amarrador);
        $detalleOrdenCliente = $detalleOrdenCliente[0];
        return view('filtro.editDetalleOrdenDespachoCliente',compact('cliente','detalleOrdenCliente','consecutivo','amarrador'));
    }

    public function updateDetalleOrdenCliente(Request $request, $consecutivo, $amarrador, $id)
    {
        $updated_at = Carbon::now();
        DB::table('zarethpr_proynew.detalles_orden_despacho')->where('id','=',$id)->update(['t04'=>$request->t04, 't06'=>$request->t06, 
            't08'=>$request->t08, 't10'=>$request->t10, 't12'=>$request->t12, 't14'=>$request->t14, 't16'=>$request->t16, 
            't18'=>$request->t18, 't20'=>$request->t20, 't22'=>$request->t22, 't24'=>$request->t24, 't26'=>$request->t26, 
            't28'=>$request->t28, 't30'=>$request->t30, 't32'=>$request->t32, 't34'=>$request->t34, 't36'=>$request->t36, 
            't38'=>$request->t38, 'updated_at'=>$updated_at]);
        return redirect()->route('filtro.listado.ordenes.clientes.view', $consecutivo);
    }

    private function consultaReferenciasFiltrarPedidos($correria)
    {
        $consulta = DB::select('select zarethpr_proynew.filtrado.referencia, zarethpr_proynew.filtrado.marca, 
        sum(zarethpr_proynew.filtrado.t04 + zarethpr_proynew.filtrado.t06 + zarethpr_proynew.filtrado.t08 + zarethpr_proynew.filtrado.t10
        + zarethpr_proynew.filtrado.t12 + zarethpr_proynew.filtrado.t14 + zarethpr_proynew.filtrado.t16 + zarethpr_proynew.filtrado.t18
        + zarethpr_proynew.filtrado.t20 + zarethpr_proynew.filtrado.t22 + zarethpr_proynew.filtrado.t24 + zarethpr_proynew.filtrado.t26
        + zarethpr_proynew.filtrado.t28 + zarethpr_proynew.filtrado.t30 + zarethpr_proynew.filtrado.t32 + zarethpr_proynew.filtrado.t34
        + zarethpr_proynew.filtrado.t36 + zarethpr_proynew.filtrado.t38 + zarethpr_proynew.filtrado.xs + zarethpr_proynew.filtrado.s
        + zarethpr_proynew.filtrado.m + zarethpr_proynew.filtrado.l + zarethpr_proynew.filtrado.xl) AS sum_tallas from zarethpr_proynew.filtrado 
        where (zarethpr_proynew.filtrado.despacho = "APROBADO" OR zarethpr_proynew.filtrado.despacho = "Aprobado")
        GROUP BY zarethpr_proynew.filtrado.referencia HAVING COUNT(*)>0 ORDER BY sum_tallas DESC');
        return $consulta;
    }

    public function referencias()
    {
        $correria = DB::select('select zarethpr_proynew.correrias.codigo from zarethpr_proynew.correrias where estado = 1');
        $fecha = Carbon::now()->format('Y-m-d');
        $referencias = $this->consultaReferenciasFiltrarPedidos($correria);
        $ref_filtradas = DB::select('select zarethpr_proynew.control_referencia_filtrada.referencia FROM zarethpr_proynew.control_referencia_filtrada WHERE fecha = DATE_FORMAT(NOW(), "%Y/%m/%d")');
        if(count($referencias) > 0){
            for ($i=0; $i<count($referencias); $i++) {     
                for ($j=0; $j<count($ref_filtradas); $j++) { 
                    if($ref_filtradas[$j]->referencia == $referencias[$i]->referencia){
                        unset($referencias[$i]);
                        break;
                    }
                }
            }
            $referencias = array_values($referencias);
            $ult_ref_fil = DB::select('select id, referencia FROM zarethpr_proynew.control_referencia_filtrada where fecha = "'.$fecha.'"');
            //$refes = DB::select('select * from zarethpr_proynew.inventario_sistemas where fecha = (SELECT MAX(fecha) FROM zarethpr_proynew.inventario_sistemas) 
            //AND (zarethpr_proynew.inventario_sistemas.bodega = "PT" OR zarethpr_proynew.inventario_sistemas.bodega = "PT001") AND zarethpr_proynew.inventario_sistemas.existencia != 0');
            //foreach ($refes as $r){
            //    $array = explode("-", $r->referencia);
            //    if(count($array) == 3){
            //        $r->referencia = $array[0];
            //        $r->talla = $array[1];
            //        $r->color = $array[2];
            //    }elseif(count($array) == 4){
            //        $r->referencia = $array[0]."-".$array[1];
            //        $r->talla = $array[2];
            //        $r->color = $array[3];
            //    }
            //}
            //
            //for ($i=0; $i<count($referencias); $i++) {
            //    $bolean = false;
            //    for ($j=0; $j<count($refes); $j++) { 
            //        if($referencias[$i]->referencia == $refes[$j]->referencia){
            //            unset($refes[$j]);
            //            $refes = array_values($refes);
            //            $bolean = true;
            //            break;
            //        }
            //    }
            //    if($bolean == false){
            //        unset($referencias[$i]);  
            //    }
            //}
            //$referencias = array_values($referencias);
            return view('filtro.all', compact('referencias','ult_ref_fil'));
        }
        else{
            return "NO HAY REFERENCIAS A FILTRAR EN LA CORRERIA ACTUAL";
        }
    }

    private function deleteDuplicateObjects($arrayObjects)
    {
        $newArray = [];
        for ($i=0; $i < count($arrayObjects); $i++) { 
            if($arrayObjects[$i]->CodBarras == "" || $arrayObjects[$i]->CodBarras == null || empty($arrayObjects[$i]->CodBarras)){
                $arrayObjects[$i]->CodBarras = $arrayObjects[$i]->CodBarras."-".$arrayObjects[$i]->IdExtension1."-".$arrayObjects[$i]->IdExtension2;
            }
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
        $descripcionReferencia = "";
        $correria = DB::select('select zarethpr_proynew.correrias.codigo from zarethpr_proynew.correrias where estado = 1');
        $pedidos = DB::select('select * from zarethpr_proynew.filtrado where zarethpr_proynew.filtrado.despacho = "APROBADO" 
        and zarethpr_proynew.filtrado.referencia = "'.$request->referencia.'"');
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
        try {

            $Username='bless';
            $Password='orgblessRe$t';
            $client = new \GuzzleHttp\Client(['base_uri' => 'http://45.76.251.153']);
            $response = $client->request('post','/API_GT/api/login/authenticate', [
                'form_params' => [
                    'Username' => $Username,
                    'Password' => $Password,
                ]
            ]);
            $url="/API_GT/api/orgBless/getInvPorBodega?Referencia=".$request->referencia."&CentroOperacion=001";      
            $token=$response->getBody()->getContents();
            $token=str_replace('"','',$token);   
            $response2 = $client->request('get',$url, [
                'headers' => [ 'Authorization' => 'Bearer '.$token ],
            ]);        
            $refSiesa = json_decode($response2->getBody()->getContents());
            $refSiesa->detail == null ? $refSiesa = [] : $refSiesa = $refSiesa->detail;
            $refSiesa = $this->deleteDuplicateObjects($refSiesa);
            foreach($refSiesa as $rsie){
                if($rsie->IdBodega == "PT001"){       
                    if($rsie->Referencia == $request->referencia){
                        switch ($rsie->IdExtension1){
                        case "04":
                            $referencia["t04"]+= intval($rsie->Disponible);
                            break;
                        case "06":
                            $referencia["t06"]+= intval($rsie->Disponible);
                            break;
                        case "08": 
                            $referencia["t08"]+= intval($rsie->Disponible);
                            break;
                        case "10":
                            $referencia["t10"]+= intval($rsie->Disponible);
                            break;
                        case "12":
                            $referencia["t12"]+= intval($rsie->Disponible);
                            break;
                        case "14":
                            $referencia["t14"]+= intval($rsie->Disponible);
                            break;
                        case "16":
                            $referencia["t16"]+= intval($rsie->Disponible);
                            break;
                        case "18":
                            $referencia["t18"]+= intval($rsie->Disponible);
                            break;
                        case "20":
                            $referencia["t20"]+= intval($rsie->Disponible);
                            break;
                        case "22":
                            $referencia["t22"]+= intval($rsie->Disponible);
                            break;
                        case "28":
                            $referencia["t28"]+= intval($rsie->Disponible);
                            break;
                        case "30":
                            $referencia["t30"]+= intval($rsie->Disponible);
                            break;
                        case "32":
                            $referencia["t32"]+= intval($rsie->Disponible);
                            break;
                        case "34":
                            $referencia["t34"]+= intval($rsie->Disponible);
                            break;
                        case "36":
                            $referencia["t36"]+= intval($rsie->Disponible);
                            break;
                        case "38":
                            $referencia["t38"]+= intval($rsie->Disponible);
                            break;
                        }
                        $referencia["total"]+= intval($rsie->Disponible);
                    }
                }elseif($rsie->IdBodega == "PPCNI" || $rsie->IdBodega == "PTCOR" || $rsie->IdBodega == "PPLV" || $rsie->IdBodega == "PPTER" || $rsie->IdBodega == "PTN"){
                    if($rsie->Referencia == $request->referencia){
                        switch ($rsie->IdExtension1) {
                        case "04":
                            $bodegas["t04"]+= intval($rsie->Disponible);
                            break;
                        case "06":
                            $bodegas["t06"]+= intval($rsie->Disponible);
                            break;
                        case "08": 
                            $bodegas["t08"]+= intval($rsie->Disponible);
                            break;
                        case "10":
                            $bodegas["t10"]+= intval($rsie->Disponible);
                            break;
                        case "12":
                            $bodegas["t12"]+= intval($rsie->Disponible);
                            break;
                        case "14":
                            $bodegas["t14"]+= intval($rsie->Disponible);
                            break;
                        case "16":
                            $bodegas["t16"]+= intval($rsie->Disponible);
                            break;
                        case "18":
                            $bodegas["t18"]+= intval($rsie->Disponible);
                            break;
                        case "20":
                            $bodegas["t20"]+= intval($rsie->Disponible);
                            break;
                        case "22":
                            $bodegas["t22"]+= intval($rsie->Disponible);
                            break;
                        case "28":
                            $bodegas["t28"]+= intval($rsie->Disponible);
                            break;
                        case "30":
                            $bodegas["t30"]+= intval($rsie->Disponible);
                            break;
                        case "32":
                            $bodegas["t32"]+= intval($rsie->Disponible);
                            break;
                        case "34":
                            $bodegas["t34"]+= intval($rsie->Disponible);
                            break;
                        case "36":
                            $bodegas["t36"]+= intval($rsie->Disponible);
                            break;
                        case "38":
                            $bodegas["t38"]+= intval($rsie->Disponible);
                            break;
                        }
                        $bodegas["total"]+= intval($rsie->Disponible);
                    }
                }
            }  
            
            $alertSIESA = "success";

        } catch (\Exception $e) {

            $alertSIESA = "warning";
            $refSiesa = DB::select('select * from zarethpr_proynew.inventario_sistemas where zarethpr_proynew.inventario_sistemas.sistema = 
            "SIESA" and fecha = "'.Carbon::now()->format('Y-m-d').'" and referencia LIKE "%'.$request->referencia.'%"');
            if(count($refSiesa) == 0){
                $alertSIESA = "danger";
                $refSiesa = DB::select('select * from zarethpr_proynew.inventario_sistemas where zarethpr_proynew.inventario_sistemas.sistema = 
                "SIESA" and fecha = (SELECT MAX(fecha) FROM zarethpr_proynew.inventario_sistemas) and referencia LIKE "%'.$request->referencia.'%"');
            }
            foreach($refSiesa as $rsie){
                $array = explode("-", $rsie->referencia);
                if($rsie->bodega == "PT001"){
                    if(count($array) == 3){
                        if($array[0] == $request->referencia){
                            switch ($array[1]) {
                                case "04":
                                    $referencia["t04"]+= $rsie->existencia;
                                    break;
                                case "06":
                                    $referencia["t06"]+= $rsie->existencia;
                                    break;
                                case "08":
                                    $referencia["t08"]+= $rsie->existencia;
                                    break;
                                case "10":
                                    $referencia["t10"]+= $rsie->existencia;
                                    break;
                                case "12":
                                    $referencia["t12"]+= $rsie->existencia;
                                    break;
                                case "14":
                                    $referencia["t14"]+= $rsie->existencia;
                                    break;
                                case "16":
                                    $referencia["t16"]+= $rsie->existencia;
                                    break;
                                case "18":
                                    $referencia["t18"]+= $rsie->existencia;
                                    break;
                                case "20":
                                    $referencia["t20"]+= $rsie->existencia;
                                    break;
                                case "22":
                                    $referencia["t22"]+= $rsie->existencia;
                                    break;
                                case "28":
                                    $referencia["t28"]+= $rsie->existencia;
                                    break;
                                case "30":
                                    $referencia["t30"]+= $rsie->existencia;
                                    break;
                                case "32":
                                    $referencia["t32"]+= $rsie->existencia;
                                    break;
                                case "34":
                                    $referencia["t34"]+= $rsie->existencia;
                                    break;
                                case "36":
                                    $referencia["t36"]+= $rsie->existencia;
                                    break;
                                case "38":
                                    $referencia["t38"]+= $rsie->existencia;
                                    break;
                            }
                            $referencia["total"]+= $rsie->existencia;
                        }
                    }elseif(count($array) == 4){
                        if($array[0]."-".$array[1] == $request->referencia){
                            switch ($array[2]) {
                                case "04":
                                    $referencia["t04"]+= $rsie->existencia;
                                    break;
                                case "06":
                                    $referencia["t06"]+= $rsie->existencia;
                                    break;
                                case "08":
                                    $referencia["t08"]+= $rsie->existencia;
                                    break;
                                case "10":
                                    $referencia["t10"]+= $rsie->existencia;
                                    break;
                                case "12":
                                    $referencia["t12"]+= $rsie->existencia;
                                    break;
                                case "14":
                                    $referencia["t14"]+= $rsie->existencia;
                                    break;
                                case "16":
                                    $referencia["t16"]+= $rsie->existencia;
                                    break;
                                case "18":
                                    $referencia["t18"]+= $rsie->existencia;
                                    break;
                                case "20":
                                    $referencia["t20"]+= $rsie->existencia;
                                    break;
                                case "22":
                                    $referencia["t22"]+= $rsie->existencia;
                                    break;
                                case "28":
                                    $referencia["t28"]+= $rsie->existencia;
                                    break;
                                case "30":
                                    $referencia["t30"]+= $rsie->existencia;
                                    break;
                                case "32":
                                    $referencia["t32"]+= $rsie->existencia;
                                    break;
                                case "34":
                                    $referencia["t34"]+= $rsie->existencia;
                                    break;
                                case "36":
                                    $referencia["t36"]+= $rsie->existencia;
                                    break;
                                case "38":
                                    $referencia["t38"]+= $rsie->existencia;
                                    break;
                            }
                            $referencia["total"]+= $rsie->existencia;
                        }
                    }
                }elseif($rsie->bodega == "PPCNI" || $rsie->bodega == "PTCOR" || $rsie->bodega == "PPLV" || $rsie->bodega == "PPTER" || $rsie->bodega == "PTN"){
                    if(count($array) == 3){
                        if($array[0] == $request->referencia){
                            switch ($array[1]) {
                                case "04":
                                    $bodegas["t04"]+= $rsie->existencia;
                                    break;
                                case "06":
                                    $bodegas["t06"]+= $rsie->existencia;
                                    break;
                                case "08":
                                    $bodegas["t08"]+= $rsie->existencia;
                                    break;
                                case "10":
                                    $bodegas["t10"]+= $rsie->existencia;
                                    break;
                                case "12":
                                    $bodegas["t12"]+= $rsie->existencia;
                                    break;
                                case "14":
                                    $bodegas["t14"]+= $rsie->existencia;
                                    break;
                                case "16":
                                    $bodegas["t16"]+= $rsie->existencia;
                                    break;
                                case "18":
                                    $bodegas["t18"]+= $rsie->existencia;
                                    break;
                                case "20":
                                    $bodegas["t20"]+= $rsie->existencia;
                                    break;
                                case "22":
                                    $bodegas["t22"]+= $rsie->existencia;
                                    break;
                                case "28":
                                    $bodegas["t28"]+= $rsie->existencia;
                                    break;
                                case "30":
                                    $bodegas["t30"]+= $rsie->existencia;
                                    break;
                                case "32":
                                    $bodegas["t32"]+= $rsie->existencia;
                                    break;
                                case "34":
                                    $bodegas["t34"]+= $rsie->existencia;
                                    break;
                                case "36":
                                    $bodegas["t36"]+= $rsie->existencia;
                                    break;
                                case "38":
                                    $bodegas["t38"]+= $rsie->existencia;
                                    break;
                            }
                            $bodegas["total"]+= $rsie->existencia;
                        }
                    }elseif(count($array) == 4){
                        if($array[0]."-".$array[1] == $request->referencia){
                            switch ($array[2]) {
                                case "04":
                                    $bodegas["t04"]+= $rsie->existencia;
                                    break;
                                case "06":
                                    $bodegas["t06"]+= $rsie->existencia;
                                    break;
                                case "08":
                                    $bodegas["t08"]+= $rsie->existencia;
                                    break;
                                case "10":
                                    $bodegas["t10"]+= $rsie->existencia;
                                    break;
                                case "12":
                                    $bodegas["t12"]+= $rsie->existencia;
                                    break;
                                case "14":
                                    $bodegas["t14"]+= $rsie->existencia;
                                    break;
                                case "16":
                                    $bodegas["t16"]+= $rsie->existencia;
                                    break;
                                case "18":
                                    $bodegas["t18"]+= $rsie->existencia;
                                    break;
                                case "20":
                                    $bodegas["t20"]+= $rsie->existencia;
                                    break;
                                case "22":
                                    $bodegas["t22"]+= $rsie->existencia;
                                    break;
                                case "28":
                                    $bodegas["t28"]+= $rsie->existencia;
                                    break;
                                case "30":
                                    $bodegas["t30"]+= $rsie->existencia;
                                    break;
                                case "32":
                                    $bodegas["t32"]+= $rsie->existencia;
                                    break;
                                case "34":
                                    $bodegas["t34"]+= $rsie->existencia;
                                    break;
                                case "36":
                                    $bodegas["t36"]+= $rsie->existencia;
                                    break;
                                case "38":
                                    $bodegas["t38"]+= $rsie->existencia;
                                    break;
                            }
                            $bodegas["total"]+= $rsie->existencia;
                        }
                    }
                }
            }              
        }

        //CONSULTA DISPONIBLE A TNS
        try{

            $refTnsBodPT001 = DB::connection('firebird')->select("SELECT B.CODIGO CODBOD , B.NOMBRE NOMBOD , M.CODIGO , M.DESCRIP , SM.EXISTENC
            FROM MATERIAL M INNER JOIN SALMATERIAL SM ON (SM.MATID = M.MATID) INNER JOIN BODEGA B ON (B.BODID = SM.BODID)
            WHERE M.CODIGO LIKE '".$request->referencia."%' AND (B.CODIGO = 'PT001' OR B.CODIGO = 'PT')");
            
            $refTnsOtherBod = DB::connection('firebird')->select("SELECT B.CODIGO CODBOD , B.NOMBRE NOMBOD , M.CODIGO , M.DESCRIP , SM.EXISTENC
            FROM MATERIAL M INNER JOIN SALMATERIAL SM ON (SM.MATID = M.MATID) INNER JOIN BODEGA B ON (B.BODID = SM.BODID)
            WHERE M.CODIGO LIKE '%".$request->referencia."%' AND (B.CODIGO = 'PPCNI' OR B.CODIGO = 'PPCOR' OR B.CODIGO = 'PPLV'
            OR B.CODIGO = 'PPTER' OR B.CODIGO = 'PTN' OR B.CODIGO = 'TER')");
            foreach($refTnsBodPT001 as $rtns){
                $array = explode("-", $rtns->CODIGO);
                if(count($array) == 3){
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
            foreach($refTnsOtherBod as $rtns){
                $array = explode("-", $rtns->CODIGO);
                if(count($array) == 3){
                    if($array[0] == $request->referencia){
                        switch ($array[1]) {
                            case "04":
                                $bodegas["t04"]+= intval($rtns->EXISTENC);
                                break;
                            case "06":
                                $bodegas["t06"]+= intval($rtns->EXISTENC);
                                break;
                            case "08":
                                $bodegas["t08"]+= intval($rtns->EXISTENC);
                                break;
                            case "10":
                                $bodegas["t10"]+= intval($rtns->EXISTENC);
                                break;
                            case "12":
                                $bodegas["t12"]+= intval($rtns->EXISTENC);
                                break;
                            case "14":
                                $bodegas["t14"]+= intval($rtns->EXISTENC);
                                break;
                            case "16":
                                $bodegas["t16"]+= intval($rtns->EXISTENC);
                                break;
                            case "18":
                                $bodegas["t18"]+= intval($rtns->EXISTENC);
                                break;
                            case "20":
                                $bodegas["t20"]+= intval($rtns->EXISTENC);
                                break;
                            case "22":
                                $bodegas["t22"]+= intval($rtns->EXISTENC);
                                break;
                            case "28":
                                $bodegas["t28"]+= intval($rtns->EXISTENC);
                                break;
                            case "30":
                                $bodegas["t30"]+= intval($rtns->EXISTENC);
                                break;
                            case "32":
                                $bodegas["t32"]+= intval($rtns->EXISTENC);
                                break;
                            case "34":
                                $bodegas["t34"]+= intval($rtns->EXISTENC);
                                break;
                            case "36":
                                $bodegas["t36"]+= intval($rtns->EXISTENC);
                                break;
                            case "38":
                                $bodegas["t38"]+= intval($rtns->EXISTENC);
                                break;
                        }
                        $bodegas["total"]+= intval($rtns->EXISTENC);
                    }
                }if(count($array) == 4){
                    if($array[1] == $request->referencia){
                        switch ($array[2]) {
                            case "04":
                                $bodegas["t04"]+= intval($rtns->EXISTENC);
                                break;
                            case "06":
                                $bodegas["t06"]+= intval($rtns->EXISTENC);
                                break;
                            case "08":
                                $bodegas["t08"]+= intval($rtns->EXISTENC);
                                break;
                            case "10":
                                $bodegas["t10"]+= intval($rtns->EXISTENC);
                                break;
                            case "12":
                                $bodegas["t12"]+= intval($rtns->EXISTENC);
                                break;
                            case "14":
                                $bodegas["t14"]+= intval($rtns->EXISTENC);
                                break;
                            case "16":
                                $bodegas["t16"]+= intval($rtns->EXISTENC);
                                break;
                            case "18":
                                $bodegas["t18"]+= intval($rtns->EXISTENC);
                                break;
                            case "20":
                                $bodegas["t20"]+= intval($rtns->EXISTENC);
                                break;
                            case "22":
                                $bodegas["t22"]+= intval($rtns->EXISTENC);
                                break;
                            case "28":
                                $bodegas["t28"]+= intval($rtns->EXISTENC);
                                break;
                            case "30":
                                $bodegas["t30"]+= intval($rtns->EXISTENC);
                                break;
                            case "32":
                                $bodegas["t32"]+= intval($rtns->EXISTENC);
                                break;
                            case "34":
                                $bodegas["t34"]+= intval($rtns->EXISTENC);
                                break;
                            case "36":
                                $bodegas["t36"]+= intval($rtns->EXISTENC);
                                break;
                            case "38":
                                $bodegas["t38"]+= intval($rtns->EXISTENC);
                                break;
                        }
                        $bodegas["total"]+= intval($rtns->EXISTENC);
                    }elseif($array[0]."-".$array[1] == $request->referencia){
                        switch ($array[2]) {
                            case "04":
                                $bodegas["t04"]+= intval($rtns->EXISTENC);
                                break;
                            case "06":
                                $bodegas["t06"]+= intval($rtns->EXISTENC);
                                break;
                            case "08":
                                $bodegas["t08"]+= intval($rtns->EXISTENC);
                                break;
                            case "10":
                                $bodegas["t10"]+= intval($rtns->EXISTENC);
                                break;
                            case "12":
                                $bodegas["t12"]+= intval($rtns->EXISTENC);
                                break;
                            case "14":
                                $bodegas["t14"]+= intval($rtns->EXISTENC);
                                break;
                            case "16":
                                $bodegas["t16"]+= intval($rtns->EXISTENC);
                                break;
                            case "18":
                                $bodegas["t18"]+= intval($rtns->EXISTENC);
                                break;
                            case "20":
                                $bodegas["t20"]+= intval($rtns->EXISTENC);
                                break;
                            case "22":
                                $bodegas["t22"]+= intval($rtns->EXISTENC);
                                break;
                            case "28":
                                $bodegas["t28"]+= intval($rtns->EXISTENC);
                                break;
                            case "30":
                                $bodegas["t30"]+= intval($rtns->EXISTENC);
                                break;
                            case "32":
                                $bodegas["t32"]+= intval($rtns->EXISTENC);
                                break;
                            case "34":
                                $bodegas["t34"]+= intval($rtns->EXISTENC);
                                break;
                            case "36":
                                $bodegas["t36"]+= intval($rtns->EXISTENC);
                                break;
                            case "38":
                                $bodegas["t38"]+= intval($rtns->EXISTENC);
                                break;
                        }
                        $bodegas["total"]+= intval($rtns->EXISTENC);
                    }
                }elseif(count($array) == 5){
                    if($array[1]."-".$array[2] == $request->referencia){
                        switch ($array[3]) {
                            case "04":
                                $bodegas["t04"]+= intval($rtns->EXISTENC);
                                break;
                            case "06":
                                $bodegas["t06"]+= intval($rtns->EXISTENC);
                                break;
                            case "08":
                                $bodegas["t08"]+= intval($rtns->EXISTENC);
                                break;
                            case "10":
                                $bodegas["t10"]+= intval($rtns->EXISTENC);
                                break;
                            case "12":
                                $bodegas["t12"]+= intval($rtns->EXISTENC);
                                break;
                            case "14":
                                $bodegas["t14"]+= intval($rtns->EXISTENC);
                                break;
                            case "16":
                                $bodegas["t16"]+= intval($rtns->EXISTENC);
                                break;
                            case "18":
                                $bodegas["t18"]+= intval($rtns->EXISTENC);
                                break;
                            case "20":
                                $bodegas["t20"]+= intval($rtns->EXISTENC);
                                break;
                            case "22":
                                $bodegas["t22"]+= intval($rtns->EXISTENC);
                                break;
                            case "28":
                                $bodegas["t28"]+= intval($rtns->EXISTENC);
                                break;
                            case "30":
                                $bodegas["t30"]+= intval($rtns->EXISTENC);
                                break;
                            case "32":
                                $bodegas["t32"]+= intval($rtns->EXISTENC);
                                break;
                            case "34":
                                $bodegas["t34"]+= intval($rtns->EXISTENC);
                                break;
                            case "36":
                                $bodegas["t36"]+= intval($rtns->EXISTENC);
                                break;
                            case "38":
                                $bodegas["t38"]+= intval($rtns->EXISTENC);
                                break;
                        }
                        $bodegas["total"]+= intval($rtns->EXISTENC);
                    }
                }
            }  
            $alertTNS = "success";
            
        } catch (\Exception $e) {
            $alertTNS = "warning";
            $refTns = DB::select('select * from zarethpr_proynew.inventario_sistemas where zarethpr_proynew.inventario_sistemas.sistema = 
            "VISUAL TNS" and fecha = "'.Carbon::now()->format('Y-m-d').'" and referencia LIKE "%'.$request->referencia.'%"');
            if(count($refTns) == 0){
                $alertTNS = "danger";
                $refTns = DB::select('select * from zarethpr_proynew.inventario_sistemas where zarethpr_proynew.inventario_sistemas.sistema = 
                "VISUAL TNS" and fecha = (SELECT MAX(fecha) FROM zarethpr_proynew.inventario_sistemas) and referencia LIKE "%'.$request->referencia.'%"');
            }

            foreach($refTns as $rtns){
                $array = explode("-", $rtns->referencia);
                if($rtns->bodega == "PT001"){
                    if(count($array) == 3){
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
                }elseif($rtns->bodega == "PPCNI" || $rtns->bodega == "PTCOR" || $rtns->bodega == "PPLV" || $rtns->bodega == "PPTER" || $rtns->bodega == "PTN"){
                    if(count($array) == 4){
                        if($array[0] == $request->referencia){
                            switch ($array[1]) {
                                case "04":
                                    $bodegas["t04"]+= $rtns->existencia;
                                    break;
                                case "06":
                                    $bodegas["t06"]+= $rtns->existencia;
                                    break;
                                case "08":
                                    $bodegas["t08"]+= $rtns->existencia;
                                    break;
                                case "10":
                                    $bodegas["t10"]+= $rtns->existencia;
                                    break;
                                case "12":
                                    $bodegas["t12"]+= $rtns->existencia;
                                    break;
                                case "14":
                                    $bodegas["t14"]+= $rtns->existencia;
                                    break;
                                case "16":
                                    $bodegas["t16"]+= $rtns->existencia;
                                    break;
                                case "18":
                                    $bodegas["t18"]+= $rtns->existencia;
                                    break;
                                case "20":
                                    $bodegas["t20"]+= $rtns->existencia;
                                    break;
                                case "22":
                                    $bodegas["t22"]+= $rtns->existencia;
                                    break;
                                case "28":
                                    $bodegas["t28"]+= $rtns->existencia;
                                    break;
                                case "30":
                                    $bodegas["t30"]+= $rtns->existencia;
                                    break;
                                case "32":
                                    $bodegas["t32"]+= $rtns->existencia;
                                    break;
                                case "34":
                                    $bodegas["t34"]+= $rtns->existencia;
                                    break;
                                case "36":
                                    $bodegas["t36"]+= $rtns->existencia;
                                    break;
                                case "38":
                                    $bodegas["t38"]+= $rtns->existencia;
                                    break;
                            }
                            $bodegas["total"]+= $rtns->existencia;
                        }
                    }elseif(count($array) == 4){
                        if($array[1] == $request->referencia){
                            switch ($array[2]) {
                                case "04":
                                    $bodegas["t04"]+= $rtns->existencia;
                                    break;
                                case "06":
                                    $bodegas["t06"]+= $rtns->existencia;
                                    break;
                                case "08":
                                    $bodegas["t08"]+= $rtns->existencia;
                                    break;
                                case "10":
                                    $bodegas["t10"]+= $rtns->existencia;
                                    break;
                                case "12":
                                    $bodegas["t12"]+= $rtns->existencia;
                                    break;
                                case "14":
                                    $bodegas["t14"]+= $rtns->existencia;
                                    break;
                                case "16":
                                    $bodegas["t16"]+= $rtns->existencia;
                                    break;
                                case "18":
                                    $bodegas["t18"]+= $rtns->existencia;
                                    break;
                                case "20":
                                    $bodegas["t20"]+= $rtns->existencia;
                                    break;
                                case "22":
                                    $bodegas["t22"]+= $rtns->existencia;
                                    break;
                                case "28":
                                    $bodegas["t28"]+= $rtns->existencia;
                                    break;
                                case "30":
                                    $bodegas["t30"]+= $rtns->existencia;
                                    break;
                                case "32":
                                    $bodegas["t32"]+= $rtns->existencia;
                                    break;
                                case "34":
                                    $bodegas["t34"]+= $rtns->existencia;
                                    break;
                                case "36":
                                    $bodegas["t36"]+= $rtns->existencia;
                                    break;
                                case "38":
                                    $bodegas["t38"]+= $rtns->existencia;
                                    break;
                            }
                            $bodegas["total"]+= $rtns->existencia;
                        }elseif($array[0]."-".$array[1] == $request->referencia){
                            switch ($array[2]) {
                                case "04":
                                    $bodegas["t04"]+= $rtns->existencia;
                                    break;
                                case "06":
                                    $bodegas["t06"]+= $rtns->existencia;
                                    break;
                                case "08":
                                    $bodegas["t08"]+= $rtns->existencia;
                                    break;
                                case "10":
                                    $bodegas["t10"]+= $rtns->existencia;
                                    break;
                                case "12":
                                    $bodegas["t12"]+= $rtns->existencia;
                                    break;
                                case "14":
                                    $bodegas["t14"]+= $rtns->existencia;
                                    break;
                                case "16":
                                    $bodegas["t16"]+= $rtns->existencia;
                                    break;
                                case "18":
                                    $bodegas["t18"]+= $rtns->existencia;
                                    break;
                                case "20":
                                    $bodegas["t20"]+= $rtns->existencia;
                                    break;
                                case "22":
                                    $bodegas["t22"]+= $rtns->existencia;
                                    break;
                                case "28":
                                    $bodegas["t28"]+= $rtns->existencia;
                                    break;
                                case "30":
                                    $bodegas["t30"]+= $rtns->existencia;
                                    break;
                                case "32":
                                    $bodegas["t32"]+= $rtns->existencia;
                                    break;
                                case "34":
                                    $bodegas["t34"]+= $rtns->existencia;
                                    break;
                                case "36":
                                    $bodegas["t36"]+= $rtns->existencia;
                                    break;
                                case "38":
                                    $bodegas["t38"]+= $rtns->existencia;
                                    break;
                            }
                            $bodegas["total"]+= $rtns->existencia;
                        }
                    }elseif(count($array) == 5){
                        if($array[1]."-".$array[2] == $request->referencia){
                            switch ($array[3]) {
                                case "04":
                                    $bodegas["t04"]+= $rtns->existencia;
                                    break;
                                case "06":
                                    $bodegas["t06"]+= $rtns->existencia;
                                    break;
                                case "08":
                                    $bodegas["t08"]+= $rtns->existencia;
                                    break;
                                case "10":
                                    $bodegas["t10"]+= $rtns->existencia;
                                    break;
                                case "12":
                                    $bodegas["t12"]+= $rtns->existencia;
                                    break;
                                case "14":
                                    $bodegas["t14"]+= $rtns->existencia;
                                    break;
                                case "16":
                                    $bodegas["t16"]+= $rtns->existencia;
                                    break;
                                case "18":
                                    $bodegas["t18"]+= $rtns->existencia;
                                    break;
                                case "20":
                                    $bodegas["t20"]+= $rtns->existencia;
                                    break;
                                case "22":
                                    $bodegas["t22"]+= $rtns->existencia;
                                    break;
                                case "28":
                                    $bodegas["t28"]+= $rtns->existencia;
                                    break;
                                case "30":
                                    $bodegas["t30"]+= $rtns->existencia;
                                    break;
                                case "32":
                                    $bodegas["t32"]+= $rtns->existencia;
                                    break;
                                case "34":
                                    $bodegas["t34"]+= $rtns->existencia;
                                    break;
                                case "36":
                                    $bodegas["t36"]+= $rtns->existencia;
                                    break;
                                case "38":
                                    $bodegas["t38"]+= $rtns->existencia;
                                    break;
                            }
                            $bodegas["total"]+= $rtns->existencia;
                        }
                    }
                }
            }             
        }

        $alertBMI = "warning";
        $refBmi = DB::select('select * from zarethpr_proynew.inventario_sistemas where zarethpr_proynew.inventario_sistemas.sistema = 
        "BMI" and fecha = "'.Carbon::now()->format('Y-m-d').'"');
        if(count($refBmi) == 0){
            $alertBMI = "danger";
            $refBmi = DB::select('select * from zarethpr_proynew.inventario_sistemas where zarethpr_proynew.inventario_sistemas.sistema = 
            "BMI" and fecha = (SELECT MAX(fecha) FROM zarethpr_proynew.inventario_sistemas)');
        }
        foreach($refBmi as $rbmi){
            $array = explode("-", $rbmi->referencia);
            if($rbmi->bodega == "PT001"){
                if(count($array) == 3){
                    if($array[0] == $request->referencia){
                        switch ($array[1]) {
                                case "04":
                                    $referencia["t04"]+= $rbmi->existencia;
                                    break;
                                case "06":
                                    $referencia["t06"]+= $rbmi->existencia;
                                    break;
                                case "08":
                                    $referencia["t08"]+= $rbmi->existencia;
                                    break;
                                case "10":
                                    $referencia["t10"]+= $rbmi->existencia;
                                    break;
                                case "12":
                                    $referencia["t12"]+= $rbmi->existencia;
                                    break;
                                case "14":
                                    $referencia["t14"]+= $rbmi->existencia;
                                    break;
                                case "16":
                                    $referencia["t16"]+= $rbmi->existencia;
                                    break;
                                case "18":
                                    $referencia["t18"]+= $rbmi->existencia;
                                    break;
                                case "20":
                                    $referencia["t20"]+= $rbmi->existencia;
                                    break;
                                case "22":
                                    $referencia["t22"]+= $rbmi->existencia;
                                    break;
                                case "28":
                                    $referencia["t28"]+= $rbmi->existencia;
                                    break;
                                case "30":
                                    $referencia["t30"]+= $rbmi->existencia;
                                    break;
                                case "32":
                                    $referencia["t32"]+= $rbmi->existencia;
                                    break;
                                case "34":
                                    $referencia["t34"]+= $rbmi->existencia;
                                    break;
                                case "36":
                                    $referencia["t36"]+= $rbmi->existencia;
                                    break;
                                case "38":
                                    $referencia["t38"]+= $rbmi->existencia;
                                    break;
                        }
                        $referencia["total"]+= $rbmi->existencia;
                    }
                }elseif(count($array) == 4){
                    if($array[0]."-".$array[1] == $request->referencia){
                        switch ($array[2]) {
                                case "04":
                                    $referencia["t04"]+= $rbmi->existencia;
                                    break;
                                case "06":
                                    $referencia["t06"]+= $rbmi->existencia;
                                    break;
                                case "08":
                                    $referencia["t08"]+= $rbmi->existencia;
                                    break;
                                case "10":
                                    $referencia["t10"]+= $rbmi->existencia;
                                    break;
                                case "12":
                                    $referencia["t12"]+= $rbmi->existencia;
                                    break;
                                case "14":
                                    $referencia["t14"]+= $rbmi->existencia;
                                    break;
                                case "16":
                                    $referencia["t16"]+= $rbmi->existencia;
                                    break;
                                case "18":
                                    $referencia["t18"]+= $rbmi->existencia;
                                    break;
                                case "20":
                                    $referencia["t20"]+= $rbmi->existencia;
                                    break;
                                case "22":
                                    $referencia["t22"]+= $rbmi->existencia;
                                    break;
                                case "28":
                                    $referencia["t28"]+= $rbmi->existencia;
                                    break;
                                case "30":
                                    $referencia["t30"]+= $rbmi->existencia;
                                    break;
                                case "32":
                                    $referencia["t32"]+= $rbmi->existencia;
                                    break;
                                case "34":
                                    $referencia["t34"]+= $rbmi->existencia;
                                    break;
                                case "36":
                                    $referencia["t36"]+= $rbmi->existencia;
                                    break;
                                case "38":
                                    $referencia["t38"]+= $rbmi->existencia;
                                    break;
                        }
                        $referencia["total"]+= $rbmi->existencia;
                    }
                }
            }
        } 
        $saldo = DB::select('select zarethpr_proynew.detalles_orden_despacho.referencia, sum(zarethpr_proynew.detalles_orden_despacho.t04) as t04,
        sum(zarethpr_proynew.detalles_orden_despacho.t06 * -1) as t06, sum(zarethpr_proynew.detalles_orden_despacho.t08 * -1) as t08, sum(zarethpr_proynew.detalles_orden_despacho.t10 * -1) as t10,
        sum(zarethpr_proynew.detalles_orden_despacho.t12 * -1) as t12, sum(zarethpr_proynew.detalles_orden_despacho.t14 * -1) as t14, sum(zarethpr_proynew.detalles_orden_despacho.t16 * -1) as t16,
        sum(zarethpr_proynew.detalles_orden_despacho.t18 * -1) as t18, sum(zarethpr_proynew.detalles_orden_despacho.t20 * -1) as t20, sum(zarethpr_proynew.detalles_orden_despacho.t22 * -1) as t22,
        sum(zarethpr_proynew.detalles_orden_despacho.t24 * -1) as t24, sum(zarethpr_proynew.detalles_orden_despacho.t26 * -1) as t26, sum(zarethpr_proynew.detalles_orden_despacho.t28 * -1) as t28,
        sum(zarethpr_proynew.detalles_orden_despacho.t30 * -1) as t30, sum(zarethpr_proynew.detalles_orden_despacho.t32 * -1) as t32, sum(zarethpr_proynew.detalles_orden_despacho.t34 * -1) as t34,
        sum(zarethpr_proynew.detalles_orden_despacho.t36 * -1) as t36, sum(zarethpr_proynew.detalles_orden_despacho.t38 * -1) as t38, sum(zarethpr_proynew.detalles_orden_despacho.xs * -1) as xs,
        sum(zarethpr_proynew.detalles_orden_despacho.s * -1) as s, sum(zarethpr_proynew.detalles_orden_despacho.m * -1) as m, sum(zarethpr_proynew.detalles_orden_despacho.l * -1) as l,
        sum(zarethpr_proynew.detalles_orden_despacho.xl * -1) as xl, sum((t04 + t06 + t08 + t10 + t12 + t14 + t16 + t18 + t20 + t22 + t24 + t26 + t28 + t30 + t32 + t34 + t36 + t38 + xs + s + m + l + xl) * -1) AS sum_tallas from zarethpr_proynew.detalles_orden_despacho 
        where zarethpr_proynew.detalles_orden_despacho.referencia = "'.$request->referencia.'" and (zarethpr_proynew.detalles_orden_despacho.estado = "APROBADO" or zarethpr_proynew.detalles_orden_despacho.estado = "ALISTAR"
        or zarethpr_proynew.detalles_orden_despacho.estado = "FACTURAR" or zarethpr_proynew.detalles_orden_despacho.estado = "EMPACAR") 
        GROUP BY zarethpr_proynew.detalles_orden_despacho.referencia HAVING COUNT(*)>0 ORDER BY sum_tallas DESC');
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

        try {

            $Username='bless';
            $Password='orgblessRe$t';
            $client = new \GuzzleHttp\Client(['base_uri' => 'http://45.76.251.153']);
            $response = $client->request('post','/API_GT/api/login/authenticate', [
                'form_params' => [
                    'Username' => $Username,
                    'Password' => $Password,
                ]
            ]);
            $url="/API_GT/api/orgBless/getInfoReferencia?Referencia=".$request->referencia;      
            $token=$response->getBody()->getContents();
            $token=str_replace('"','',$token);   
            $response2 = $client->request('get',$url, [
                'headers' => [ 'Authorization' => 'Bearer '.$token ],
            ]);        
            $refSiesa = json_decode($response2->getBody()->getContents());
            $refSiesa->detail == null ? $refSiesa = [] : $refSiesa = $refSiesa->detail;
            
            
            $descripcionReferencia = $refSiesa[0]->DescItem;

        } catch (\Exception $e) {
            $descripcionReferencia = "NO SE PUEDE TOMAR LA DESCRIPCION DEL ITEM";
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
        return response()->json([$pedidos, $referencia, $bodegas, $alerts, $descripcionReferencia]);
    }

    public function cargaBlessManufacturing()
    {
        return view('filtro.cargaBlessManufacturing');
    }

    public function storeCargaBlessManufacturing(Request $request)
    {
        ini_set('max_execution_time', 3600);
        $created_at = Carbon::now();
        $fecha = Carbon::now()->format('Y-m-d');
        $countSincBMI = 0;
        $bmi = "";
        $idControlInv = DB::select('select * from zarethpr_proynew.inventario_sistemas where fecha = "'.$fecha.'"');
        if(count($idControlInv) == 0){
            $idControlInv = DB::select('select MAX(id_control_inventario) as id_control_inventario from zarethpr_proynew.inventario_sistemas');
            $idControlInv = $idControlInv[0]->id_control_inventario + 1;
        }else{
            $idControlInv = $idControlInv[0]->id_control_inventario;
        }
        $file = $request->file('archivo');
        $up = new ImportExcel();
        Excel::import($up,$file);
        $items=json_decode(json_encode($up->sheetData[0]));
        $consultaBMI = DB::select('select * from zarethpr_proynew.inventario_sistemas where fecha = "'.$fecha.'" and sistema = "BMI"');
        if(count($consultaBMI) == 0){
            foreach ($items as $item) {
                if($item->CodBodega == "PT001"){
                    DB::insert('insert into zarethpr_proynew.inventario_sistemas (id_control_inventario, sistema, bodega, fecha, referencia, existencia, created_at) values (?, ?, ?, ?, ?, ?, ?)', 
                    [$idControlInv, 'BMI', $item->CodBodega, $fecha, $item->Item, intval($item->Disponible), $created_at]);
                    $countSincBMI++;
                }
            }
            $bmi = "Se ha cargado a las ".$created_at." un total de ".$countSincBMI." registros de Bless Manufacturing para el filtro.";
            return redirect(route('filtro.cargar'))->with('msg', $bmi)->with('alert', 'success');
        }else{
            $created_at = $consultaBMI[0]->created_at;
            $bmi = "Ya fueron cargados los registros de Bless Manufacturing a las ".$created_at." para el filtro.";
            return redirect(route('filtro.cargar'))->with('msg', $bmi)->with('alert', 'danger');
        }
    }

    public function storeDataFiltrada(Request $request)
    {
        $pedidos = $request->data;
        $referencia = $request->referencia;
        $created_at = Carbon::now();
        $updated_at = Carbon::now();
        $fecha = $created_at->format('Y-m-d');
        $correria = DB::select('select zarethpr_proynew.correrias.codigo from zarethpr_proynew.correrias where estado = 1');

        DB::insert('insert into zarethpr_proynew.control_referencia_filtrada (referencia, fecha, created_at) values (?, ?, ?)', [$request->referencia, $fecha, $created_at]);

        for ($i=0; $i<count($pedidos); $i++) {
            $consultaCliente = DB::select('SELECT zarethpr_proynew.filtrado.nit, zarethpr_proynew.filtrado.nombre, zarethpr_proynew.filtrado.suc,
            zarethpr_proynew.filtrado.direccion, zarethpr_proynew.filtrado.departamento, zarethpr_proynew.filtrado.ciudad, zarethpr_proynew.filtrado.correria 
            FROM zarethpr_proynew.filtrado WHERE idamarrador = '.$pedidos[$i]["idamarrador"].' GROUP BY zarethpr_proynew.filtrado.nit,
            zarethpr_proynew.filtrado.suc HAVING COUNT(*)>0');
            $consultaCliente = $consultaCliente[0];
            
            $consultaOrdenDespacho = DB::select('SELECT * FROM zarethpr_proynew.orden_despacho WHERE zarethpr_proynew.orden_despacho.nit = '.$consultaCliente->nit.' AND
            zarethpr_proynew.orden_despacho.sucursal = "'.$consultaCliente->suc.'" AND zarethpr_proynew.orden_despacho.estado = "PREPARANDO" AND
            zarethpr_proynew.orden_despacho.correria = "'.$consultaCliente->correria.'"');
            
            $vendedor = DB::select('SELECT zarethpr_proynew.filtrado.vendedor FROM zarethpr_proynew.filtrado where idamarrador = '.$pedidos[$i]["idamarrador"]);
            $vendedor = $vendedor[0]->vendedor;
            
            if( count($consultaOrdenDespacho) == 0 ){
                $idConsecutivo = DB::select('SELECT MAX(id_diario) as id_diario FROM zarethpr_proynew.orden_despacho WHERE fecha = "'.$fecha.'"');
                $idConsecutivo = $idConsecutivo[0]->id_diario + 1;
                $idDiario = $idConsecutivo;

                if( $idConsecutivo < 10 ){ $idConsecutivo = "00".$idConsecutivo; }elseif( $idConsecutivo >= 10 && $idConsecutivo < 100 ){ $idConsecutivo = "0".$idConsecutivo; }else{ $idConsecutivo = "".$idConsecutivo; }
                $consecutivo = str_split($fecha);
                $consecutivo = $consecutivo[2].$consecutivo[3].$consecutivo[5].$consecutivo[6].$consecutivo[8].$consecutivo[9].$idConsecutivo;
                
                DB::insert('insert into zarethpr_proynew.orden_despacho (id_diario, consecutivo, nit, cliente, departamento, ciudad, direccion, sucursal, fecha, estado, 
                correria, user_filtra, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)', [$idConsecutivo, $consecutivo, $consultaCliente->nit, $consultaCliente->nombre, 
                $consultaCliente->departamento, $consultaCliente->ciudad, $consultaCliente->direccion, $consultaCliente->suc, $fecha, "PREPARANDO",
                $consultaCliente->correria, Auth::user()->id, $created_at]);

                $idOrdenDespacho = DB::select('SELECT id FROM zarethpr_proynew.orden_despacho WHERE consecutivo = "'.$consecutivo.'"');
                $idOrdenDespacho = $idOrdenDespacho[0]->id;

                DB::insert('insert into zarethpr_proynew.detalles_orden_despacho (id_orden_despacho, id_pedido, id_amarrador, vendedor, 
                referencia, t04, t06, t08, t10, t12, t14, t16, t18, t20, t22, t28, t30, t32, t34, t36, t38, estado, created_at) 
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', [$idOrdenDespacho, $pedidos[$i]["ped"], $pedidos[$i]["idamarrador"],
                $vendedor, $referencia, $pedidos[$i]["t04"], $pedidos[$i]["t06"], $pedidos[$i]["t08"], $pedidos[$i]["t10"], $pedidos[$i]["t12"], $pedidos[$i]["t14"],
                $pedidos[$i]["t16"], $pedidos[$i]["t18"], $pedidos[$i]["t20"], $pedidos[$i]["t22"], $pedidos[$i]["t28"], $pedidos[$i]["t30"], 
                $pedidos[$i]["t32"], $pedidos[$i]["t34"], $pedidos[$i]["t36"], $pedidos[$i]["t38"], "APROBADO", $created_at]);
            }else{
                DB::table('zarethpr_proynew.orden_despacho')->where('id','=',$consultaOrdenDespacho[0]->id)->update(['updated_at'=>$updated_at]);
                DB::insert('insert into zarethpr_proynew.detalles_orden_despacho (id_orden_despacho, id_pedido, id_amarrador, vendedor, 
                referencia, t04, t06, t08, t10, t12, t14, t16, t18, t20, t22, t28, t30, t32, t34, t36, t38, estado, created_at) 
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', [$consultaOrdenDespacho[0]->id, $pedidos[$i]["ped"], $pedidos[$i]["idamarrador"],
                $vendedor, $referencia, $pedidos[$i]["t04"], $pedidos[$i]["t06"], $pedidos[$i]["t08"], $pedidos[$i]["t10"], $pedidos[$i]["t12"], $pedidos[$i]["t14"],
                $pedidos[$i]["t16"], $pedidos[$i]["t18"], $pedidos[$i]["t20"], $pedidos[$i]["t22"], $pedidos[$i]["t28"], $pedidos[$i]["t30"], 
                $pedidos[$i]["t32"], $pedidos[$i]["t34"], $pedidos[$i]["t36"], $pedidos[$i]["t38"], "APROBADO", $created_at]);
            }
            DB::table('zarethpr_proynew.detalles')->where('id','=',$pedidos[$i]["idamarrador"])->update(['despacho'=>'COMPROMETIDO']);
        }
        return response()->json("Respuesta Controller");
    }

    public function reporteCliente()
    {   
        $clientes = DB::select('select zarethpr_proynew.despacho.nit, zarethpr_proynew.despacho.cliente, zarethpr_proynew.despacho.sucursal 
        from zarethpr_proynew.despacho GROUP BY zarethpr_proynew.despacho.nit, zarethpr_proynew.despacho.cliente, zarethpr_proynew.despacho.sucursal 
        HAVING COUNT(*)>0 ORDER BY zarethpr_proynew.despacho.cliente ASC');
        return view('filtro.reporteClienteOrdenDespacho')->with('clientes',$clientes);
    }

    public function generarReporteCliente(Request $request)
    {
        $consulta = DB::select('select * from zarethpr_proynew.despacho where zarethpr_proynew.despacho.nit = '.$request->documento.' 
        and sucursal = "'.$request->sucursal.'"');
        if(count($consulta) == 0){
            return response()->json([
                0, "No hay registros"
            ], 200);
        }else{
            return response()->json([
                1, "Registros encontrados", view('filtro.fragmentoClienteOrdenDespacho',compact('consulta'))->render()
            ], 200);
        }
    }

    public function reporteReferencia()
    {
        $referencias = DB::select('select zarethpr_proynew.despacho.referencia from zarethpr_proynew.despacho 
        GROUP BY zarethpr_proynew.despacho.referencia HAVING COUNT(*)>0 ORDER BY zarethpr_proynew.despacho.referencia ASC');
        return view('filtro.reporteReferenciaOrdenDespacho')->with('referencias',$referencias);
    }

    public function generarReporteReferencia(Request $request)
    {
        $consulta = DB::select('select * from zarethpr_proynew.despacho where zarethpr_proynew.despacho.referencia = "'.$request->referencia.'"');
        if(count($consulta) == 0){
            return response()->json([
                0, "No hay registros"
            ], 200);
        }else{
            return response()->json([
                1, "Registros encontrados", view('filtro.fragmentoReferenciaOrdenDespacho',compact('consulta'))->render()
            ], 200);
        }
    }

    public function reporteProduccion()
    {
        $correrias = DB::select('select * from zarethpr_proynew.correrias');
        return view('filtro.reporteProduccionOrdenDespacho')->with('correrias',$correrias);
    }

    public function generarReporteProduccion(Request $request)
    {
        ini_set('max_execution_time', 3600);
        $produccion = DB::select('SELECT * from zarethpr_proynew.filtrado WHERE correria = "'.$request->correria.'"');
        foreach($produccion as $p){
            $marca = $this->getMarcaReferencia($p->referencia);
            $p->marca = $marca;
            $p->tipo = "PEDIDO";
            $p->consecutivo = "S/C";
            $p->filtrador = "S/F";
            if(isset($p->departamento)){
                $p->departamento = "Sin Departamento";
            }
            if($p->despacho == "DESPACHADO" || $p->despacho == "Despachado" || $p->despacho == "COMPROMETIDO" || $p->despacho == "Comprometido"){
                $p->tipo = "ORDEN DE DESPACHO";
                $od = DB::select('SELECT * from zarethpr_proynew.despacho WHERE id_amarrador = '.$p->idamarrador.' AND (estado_detalle_orden != "CANCELADO"
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
                    $p->t26 = $od[0]->t26;
                    $p->t28 = $od[0]->t28;
                    $p->t30 = $od[0]->t30;
                    $p->t32 = $od[0]->t32;
                    $p->t34 = $od[0]->t34;
                    $p->t36 = $od[0]->t36;
                    $p->t38 = $od[0]->t38;
                    $p->xs = $od[0]->xs;
                    $p->s = $od[0]->s;
                    $p->m = $od[0]->m;
                    $p->l = $od[0]->l;
                    $p->xl = $od[0]->xl;
                    $p->consecutivo = $od[0]->consecutivo;
                    $p->filtrador = $od[0]->filtrador;
                }
            }
            $p->total = $p->t04+$p->t06+$p->t08+$p->t10+$p->t12+$p->t14+$p->t16+$p->t18+$p->t20+$p->t22+$p->t24+$p->t26+$p->t28+$p->t30+$p->t32+$p->t34+$p->t36+$p->t38+$p->xs+$p->s+$p->m+$p->l+$p->xl;
        }
        if(count($produccion) == 0){
            return response()->json([
                0, "No hay registros"
            ], 200);
        }else{
            return response()->json([
                1, "Registros encontrados", view('filtro.fragmentoProduccionOrdenDespacho',compact('produccion'))->render()
            ], 200);
        }
    }

    public function reporteCorreria()
    {
        $correrias = DB::select('select * from zarethpr_proynew.correrias');
        return view('filtro.reporteCorreriaOrdenDespacho')->with('correrias',$correrias);
    }

    public function generarReporteCorreria(Request $request)
    {
        $ordenes = DB::select('SELECT * from zarethpr_proynew.despacho WHERE correria = "'.$request->correria.'"');
        foreach($ordenes as $orden){
            $marca = $this->getMarcaReferencia($orden->referencia);
            $orden->marca = $marca;
        }
        //return Excel::download(new OrdenesExport($ordenes),"ORDENES DE DESPACHO.xlsx");
        //$consulta = DB::select('select * from zarethpr_proynew.despacho where zarethpr_proynew.despacho.referencia = "'.$request->referencia.'"');
        if(count($ordenes) == 0){
            return response()->json([
                0, "No hay registros"
            ], 200);
        }else{
            return response()->json([
                1, "Registros encontrados", view('filtro.fragmentoCorreriaOrdenDespacho',compact('ordenes'))->render()
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
        $consulta = DB::select('select zarethpr_proynew.filtrado.referencia, zarethpr_proynew.filtrado.marca, sum(zarethpr_proynew.filtrado.t04) as t04,
        sum(zarethpr_proynew.filtrado.t06 * -1) as t06, sum(zarethpr_proynew.filtrado.t08 * -1) as t08, sum(zarethpr_proynew.filtrado.t10 * -1) as t10,
        sum(zarethpr_proynew.filtrado.t12 * -1) as t12, sum(zarethpr_proynew.filtrado.t14 * -1) as t14, sum(zarethpr_proynew.filtrado.t16 * -1) as t16,
        sum(zarethpr_proynew.filtrado.t18 * -1) as t18, sum(zarethpr_proynew.filtrado.t20 * -1) as t20, sum(zarethpr_proynew.filtrado.t22 * -1) as t22,
        sum(zarethpr_proynew.filtrado.t24 * -1) as t24, sum(zarethpr_proynew.filtrado.t26 * -1) as t26, sum(zarethpr_proynew.filtrado.t28 * -1) as t28,
        sum(zarethpr_proynew.filtrado.t30 * -1) as t30, sum(zarethpr_proynew.filtrado.t32 * -1) as t32, sum(zarethpr_proynew.filtrado.t34 * -1) as t34,
        sum(zarethpr_proynew.filtrado.t36 * -1) as t36, sum(zarethpr_proynew.filtrado.t38 * -1) as t38, sum(zarethpr_proynew.filtrado.xs * -1) as xs,
        sum(zarethpr_proynew.filtrado.s * -1) as s, sum(zarethpr_proynew.filtrado.m * -1) as m, sum(zarethpr_proynew.filtrado.l * -1) as l,
        sum(zarethpr_proynew.filtrado.xl * -1) as xl, sum((t04 + t06 + 
        t08 + t10 + t12 + t14 + t16 + t18 + t20 + t22 + t24 + t26 + t28 + t30 + t32 + t34 + t36 + t38 + xs + s + m + l + xl) * -1) AS sum from 
        zarethpr_proynew.filtrado where zarethpr_proynew.filtrado.despacho = "APROBADO" AND zarethpr_proynew.filtrado.nit != "88242589"
        GROUP BY zarethpr_proynew.filtrado.referencia HAVING COUNT(*)>0 ORDER BY zarethpr_proynew.filtrado.referencia ASC');
        $arrayRef = [];
        for ($i=0; $i<count($consulta) ; $i++) { 
            $marca = $this->getMarcaReferencia($consulta[$i]->referencia);
            $arrayRef[$i][0] = [
                                    "referencia" => $consulta[$i]->referencia, "bodega" => "VENTAS NACIONAL", "marca" => $marca,
                                    "t04" => intval($consulta[$i]->t04), "t06" => intval($consulta[$i]->t06), 
                                    "t08" => intval($consulta[$i]->t08), "t10" => intval($consulta[$i]->t10), "t12" => intval($consulta[$i]->t12), 
                                    "t14" => intval($consulta[$i]->t14), "t16" => intval($consulta[$i]->t16), "t18" => intval($consulta[$i]->t18), 
                                    "t20" => intval($consulta[$i]->t20), "t22" => intval($consulta[$i]->t22), "t28" => intval($consulta[$i]->t28), 
                                    "t30" => intval($consulta[$i]->t30), "t32" => intval($consulta[$i]->t32), "t34" => intval($consulta[$i]->t34), 
                                    "t36" => intval($consulta[$i]->t36), "t38" => intval($consulta[$i]->t38), "total" => intval($consulta[$i]->sum)
                                ];  
            $arrayRef[$i][7] = [
                                    "referencia" => $consulta[$i]->referencia, "bodega" => "COMPROMETIDO NACIONAL", "marca" => $marca,
                                    "t04" => 0, "t06" => 0, "t08" => 0, "t10" => 0, 
                                    "t12" => 0, "t14" => 0, "t16" => 0, "t18" => 0, "t20" => 0, "t22" => 0, 
                                    "t28" => 0, "t30" => 0, "t32" => 0, "t34" => 0, "t36" => 0, "t38" => 0, "total" => 0
                                ];  
            $arrayRef[$i][8] = [
                                    "referencia" => $consulta[$i]->referencia, "bodega" => "VENTAS MEDELLIN", "marca" => $marca,
                                    "t04" => 0, "t06" => 0, "t08" => 0, "t10" => 0, 
                                    "t12" => 0, "t14" => 0, "t16" => 0, "t18" => 0, "t20" => 0, "t22" => 0, 
                                    "t28" => 0, "t30" => 0, "t32" => 0, "t34" => 0, "t36" => 0, "t38" => 0, "total" => 0
                                ];  
            $arrayRef[$i][9] = [
                                    "referencia" => $consulta[$i]->referencia, "bodega" => "COMPROMETIDO MEDELLIN", "marca" => $marca,
                                    "t04" => 0, "t06" => 0, "t08" => 0, "t10" => 0, 
                                    "t12" => 0, "t14" => 0, "t16" => 0, "t18" => 0, "t20" => 0, "t22" => 0, 
                                    "t28" => 0, "t30" => 0, "t32" => 0, "t34" => 0, "t36" => 0, "t38" => 0, "total" => 0
                                ];  
            $arrayRef[$i][1] = [
                                    "referencia" => $consulta[$i]->referencia, "bodega" => "PRODUCTO TERMINADO", "marca" => $marca,
                                    "t04" => 0, "t06" => 0, "t08" => 0, "t10" => 0, 
                                    "t12" => 0, "t14" => 0, "t16" => 0, "t18" => 0, "t20" => 0, "t22" => 0, 
                                    "t28" => 0, "t30" => 0, "t32" => 0, "t34" => 0, "t36" => 0, "t38" => 0, "total" => 0
                                ];
            $arrayRef[$i][2] = [
                                    "referencia" => $consulta[$i]->referencia, "bodega" => "CONFECCION INTERNO", "marca" => $marca,
                                    "t04" => 0, "t06" => 0, "t08" => 0, "t10" => 0, 
                                    "t12" => 0, "t14" => 0, "t16" => 0, "t18" => 0, "t20" => 0, "t22" => 0, 
                                    "t28" => 0, "t30" => 0, "t32" => 0, "t34" => 0, "t36" => 0, "t38" => 0, "total" => 0
                                ];
            $arrayRef[$i][3] = [
                                    "referencia" => $consulta[$i]->referencia, "bodega" => "CORTE", "marca" => $marca,
                                    "t04" => 0, "t06" => 0, "t08" => 0, "t10" => 0, 
                                    "t12" => 0, "t14" => 0, "t16" => 0, "t18" => 0, "t20" => 0, "t22" => 0, 
                                    "t28" => 0, "t30" => 0, "t32" => 0, "t34" => 0, "t36" => 0, "t38" => 0, "total" => 0
                                ];  
            $arrayRef[$i][4] = [
                                    "referencia" => $consulta[$i]->referencia, "bodega" => "LAVANDERIA INTERNA", "marca" => $marca,
                                    "t04" => 0, "t06" => 0, "t08" => 0, "t10" => 0, 
                                    "t12" => 0, "t14" => 0, "t16" => 0, "t18" => 0, "t20" => 0, "t22" => 0, 
                                    "t28" => 0, "t30" => 0, "t32" => 0, "t34" => 0, "t36" => 0, "t38" => 0, "total" => 0
                                ];    
            $arrayRef[$i][5] = [
                                    "referencia" => $consulta[$i]->referencia, "bodega" => "TERMINACION", "marca" => $marca,
                                    "t04" => 0, "t06" => 0, "t08" => 0, "t10" => 0, 
                                    "t12" => 0, "t14" => 0, "t16" => 0, "t18" => 0, "t20" => 0, "t22" => 0, 
                                    "t28" => 0, "t30" => 0, "t32" => 0, "t34" => 0, "t36" => 0, "t38" => 0, "total" => 0
                                ];
            $arrayRef[$i][6] = [
                                    "referencia" => $consulta[$i]->referencia, "bodega" => "PRETINTORERIA", "marca" => $marca,
                                    "t04" => 0, "t06" => 0, "t08" => 0, "t10" => 0, 
                                    "t12" => 0, "t14" => 0, "t16" => 0, "t18" => 0, "t20" => 0, "t22" => 0, 
                                    "t28" => 0, "t30" => 0, "t32" => 0, "t34" => 0, "t36" => 0, "t38" => 0, "total" => 0
                                ];           
            
            
            try {

                $Username='bless';
                $Password='orgblessRe$t';
                $arraySiesa=[];
                $arrayBodega=["","PPCNI-","PTCOR-","PPLV-","PPTER-","PTN-","PPCT-"];
                for ($j=0; $j<count($arrayBodega) ; $j++) { 
                    $client = new \GuzzleHttp\Client(['base_uri' => 'http://45.76.251.153']);
                    $response = $client->request('post','/API_GT/api/login/authenticate', [
                        'form_params' => [
                            'Username' => $Username,
                            'Password' => $Password,
                        ]
                    ]);
                    $url="/API_GT/api/orgBless/getInvPorBodega?Referencia=".$arrayBodega[$j].$consulta[$i]->referencia."&CentroOperacion=001";      
                    $token=$response->getBody()->getContents();
                    $token=str_replace('"','',$token);   
                    $response2 = $client->request('get',$url, [
                        'headers' => [ 'Authorization' => 'Bearer '.$token ],
                    ]);        
                    $refSiesa = json_decode($response2->getBody()->getContents());
                    $refSiesa->detail == null ? $refSiesa = [] : $refSiesa = $refSiesa->detail;
                    $arraySiesa = array_merge($arraySiesa,$refSiesa);
                }
                $refSiesa = $this->deleteDuplicateObjects($arraySiesa);
                foreach($refSiesa as $rsie){
                    $rsie->IdBodega = trim($rsie->IdBodega);
                    $array = explode("-", $rsie->Referencia);
                    if(count($array) == 1){
                        if($array[0] == $consulta[$i]->referencia){
                            $rsie->Referencia = $array[0];
                        }
                    }elseif(count($array) == 2){                       
                        if($array[1] == $consulta[$i]->referencia){
                            $rsie->Referencia = $array[1];
                        }
                    }elseif(count($array) == 3){
                        if($array[1]."-".$array[2] == $consulta[$i]->referencia){
                            $rsie->Referencia = $array[1]."-".$array[2];
                        }
                    } 

                    if($rsie->IdBodega == "PT001"){       
                        if($rsie->Referencia == $consulta[$i]->referencia){
                            switch ($rsie->IdExtension1) {
                                case "04":
                                $arrayRef[$i][1]["t04"]+= intval($rsie->Disponible);
                                break;
                                case "06":
                                $arrayRef[$i][1]["t06"]+= intval($rsie->Disponible);
                                break;
                                case "08":
                                $arrayRef[$i][1]["t08"]+= intval($rsie->Disponible);
                                break;
                                case "10":
                                $arrayRef[$i][1]["t10"]+= intval($rsie->Disponible);
                                break;
                                case "12":
                                $arrayRef[$i][1]["t12"]+= intval($rsie->Disponible);
                                break;
                                case "14":
                                $arrayRef[$i][1]["t14"]+= intval($rsie->Disponible);
                                break;
                                case "16":
                                $arrayRef[$i][1]["t16"]+= intval($rsie->Disponible);
                                break;
                                case "18":
                                $arrayRef[$i][1]["t18"]+= intval($rsie->Disponible);
                                break;
                                case "20":
                                $arrayRef[$i][1]["t20"]+= intval($rsie->Disponible);
                                break;
                                case "22":
                                $arrayRef[$i][1]["t22"]+= intval($rsie->Disponible);
                                break;
                                case "28":
                                $arrayRef[$i][1]["t28"]+= intval($rsie->Disponible);
                                break;
                                case "30":
                                $arrayRef[$i][1]["t30"]+= intval($rsie->Disponible);
                                break;
                                case "32":
                                $arrayRef[$i][1]["t32"]+= intval($rsie->Disponible);
                                break;
                                case "34":
                                $arrayRef[$i][1]["t34"]+= intval($rsie->Disponible);
                                break;
                                case "36":
                                $arrayRef[$i][1]["t36"]+= intval($rsie->Disponible);
                                break;
                                case "38":
                                $arrayRef[$i][1]["t38"]+= intval($rsie->Disponible);
                                break;
                            }
                                $arrayRef[$i][1]["total"]+= intval($rsie->Disponible);
                        }
                    }elseif($rsie->IdBodega == "PPCNI"){
                        if($rsie->Referencia == $consulta[$i]->referencia){
                            switch ($rsie->IdExtension1) {
                                case "04":
                                    $arrayRef[$i][2]["t04"]+= intval($rsie->Disponible);
                                    break;
                                case "06":
                                    $arrayRef[$i][2]["t06"]+= intval($rsie->Disponible);
                                    break;
                                case "08":
                                    $arrayRef[$i][2]["t08"]+= intval($rsie->Disponible);
                                    break;
                                case "10":
                                    $arrayRef[$i][2]["t10"]+= intval($rsie->Disponible);
                                    break;
                                case "12":
                                    $arrayRef[$i][2]["t12"]+= intval($rsie->Disponible);
                                    break;
                                case "14":
                                    $arrayRef[$i][2]["t14"]+= intval($rsie->Disponible);
                                    break;
                                case "16":
                                    $arrayRef[$i][2]["t16"]+= intval($rsie->Disponible);
                                    break;
                                case "18":
                                    $arrayRef[$i][2]["t18"]+= intval($rsie->Disponible);
                                    break;
                                case "20":
                                    $arrayRef[$i][2]["t20"]+= intval($rsie->Disponible);
                                    break;
                                case "22":
                                    $arrayRef[$i][2]["t22"]+= intval($rsie->Disponible);
                                    break;
                                case "28":
                                    $arrayRef[$i][2]["t28"]+= intval($rsie->Disponible);
                                    break;
                                case "30":
                                    $arrayRef[$i][2]["t30"]+= intval($rsie->Disponible);
                                    break;
                                case "32":
                                    $arrayRef[$i][2]["t32"]+= intval($rsie->Disponible);
                                    break;
                                case "34":
                                    $arrayRef[$i][2]["t34"]+= intval($rsie->Disponible);
                                    break;
                                case "36":
                                    $arrayRef[$i][2]["t36"]+= intval($rsie->Disponible);
                                    break;
                                case "38":
                                    $arrayRef[$i][2]["t38"]+= intval($rsie->Disponible);
                                    break;
                            }
                                $arrayRef[$i][2]["total"]+= intval($rsie->Disponible);
                        }
                    }elseif($rsie->IdBodega == "PTCOR"){
                        if($rsie->Referencia == $consulta[$i]->referencia){
                            switch ($rsie->IdExtension1) {
                                case "04":
                                    $arrayRef[$i][3]["t04"]+= intval($rsie->Disponible);
                                    break;
                                case "06":
                                    $arrayRef[$i][3]["t06"]+= intval($rsie->Disponible);
                                    break;
                                case "08":
                                    $arrayRef[$i][3]["t08"]+= intval($rsie->Disponible);
                                    break;
                                case "10":
                                    $arrayRef[$i][3]["t10"]+= intval($rsie->Disponible);
                                    break;
                                case "12":
                                    $arrayRef[$i][3]["t12"]+= intval($rsie->Disponible);
                                    break;
                                case "14":
                                    $arrayRef[$i][3]["t14"]+= intval($rsie->Disponible);
                                    break;
                                case "16":
                                    $arrayRef[$i][3]["t16"]+= intval($rsie->Disponible);
                                    break;
                                case "18":
                                    $arrayRef[$i][3]["t18"]+= intval($rsie->Disponible);
                                    break;
                                case "20":
                                    $arrayRef[$i][3]["t20"]+= intval($rsie->Disponible);
                                    break;
                                case "22":
                                    $arrayRef[$i][3]["t22"]+= intval($rsie->Disponible);
                                    break;
                                case "28":
                                    $arrayRef[$i][3]["t28"]+= intval($rsie->Disponible);
                                    break;
                                case "30":
                                    $arrayRef[$i][3]["t30"]+= intval($rsie->Disponible);
                                    break;
                                case "32":
                                    $arrayRef[$i][3]["t32"]+= intval($rsie->Disponible);
                                    break;
                                case "34":
                                    $arrayRef[$i][3]["t34"]+= intval($rsie->Disponible);
                                    break;
                                case "36":
                                    $arrayRef[$i][3]["t36"]+= intval($rsie->Disponible);
                                    break;
                                case "38":
                                    $arrayRef[$i][3]["t38"]+= intval($rsie->Disponible);
                                    break;
                            }
                                $arrayRef[$i][3]["total"]+= intval($rsie->Disponible);
                        }
                    }elseif($rsie->IdBodega == "PPLV"){
                        if($rsie->Referencia == $consulta[$i]->referencia){
                            switch ($rsie->IdExtension1) {
                                case "04":
                                    $arrayRef[$i][4]["t04"]+= intval($rsie->Disponible);
                                    break;
                                case "06":
                                    $arrayRef[$i][4]["t06"]+= intval($rsie->Disponible);
                                    break;
                                case "08":
                                    $arrayRef[$i][4]["t08"]+= intval($rsie->Disponible);
                                    break;
                                case "10":
                                    $arrayRef[$i][4]["t10"]+= intval($rsie->Disponible);
                                    break;
                                case "12":
                                    $arrayRef[$i][4]["t12"]+= intval($rsie->Disponible);
                                    break;
                                case "14":
                                    $arrayRef[$i][4]["t14"]+= intval($rsie->Disponible);
                                    break;
                                case "16":
                                    $arrayRef[$i][4]["t16"]+= intval($rsie->Disponible);
                                    break;
                                case "18":
                                    $arrayRef[$i][4]["t18"]+= intval($rsie->Disponible);
                                    break;
                                case "20":
                                    $arrayRef[$i][4]["t20"]+= intval($rsie->Disponible);
                                    break;
                                case "22":
                                    $arrayRef[$i][4]["t22"]+= intval($rsie->Disponible);
                                    break;
                                case "28":
                                    $arrayRef[$i][4]["t28"]+= intval($rsie->Disponible);
                                    break;
                                case "30":
                                    $arrayRef[$i][4]["t30"]+= intval($rsie->Disponible);
                                    break;
                                case "32":
                                    $arrayRef[$i][4]["t32"]+= intval($rsie->Disponible);
                                    break;
                                case "34":
                                    $arrayRef[$i][4]["t34"]+= intval($rsie->Disponible);
                                    break;
                                case "36":
                                    $arrayRef[$i][4]["t36"]+= intval($rsie->Disponible);
                                    break;
                                case "38":
                                    $arrayRef[$i][4]["t38"]+= intval($rsie->Disponible);
                                    break;
                            }
                                $arrayRef[$i][4]["total"]+= intval($rsie->Disponible);
                        }
                    }elseif($rsie->IdBodega == "PPTER" || $rsie->IdBodega == "TER"){
                        if($rsie->Referencia == $consulta[$i]->referencia){
                            switch ($rsie->IdExtension1) {
                                case "04":
                                    $arrayRef[$i][5]["t04"]+= intval($rsie->Disponible);
                                    break;
                                case "06":
                                    $arrayRef[$i][5]["t06"]+= intval($rsie->Disponible);
                                    break;
                                case "08":
                                    $arrayRef[$i][5]["t08"]+= intval($rsie->Disponible);
                                    break;
                                case "10":
                                    $arrayRef[$i][5]["t10"]+= intval($rsie->Disponible);
                                    break;
                                case "12":
                                    $arrayRef[$i][5]["t12"]+= intval($rsie->Disponible);
                                    break;
                                case "14":
                                    $arrayRef[$i][5]["t14"]+= intval($rsie->Disponible);
                                    break;
                                case "16":
                                    $arrayRef[$i][5]["t16"]+= intval($rsie->Disponible);
                                    break;
                                case "18":
                                    $arrayRef[$i][5]["t18"]+= intval($rsie->Disponible);
                                    break;
                                case "20":
                                    $arrayRef[$i][5]["t20"]+= intval($rsie->Disponible);
                                    break;
                                case "22":
                                    $arrayRef[$i][5]["t22"]+= intval($rsie->Disponible);
                                    break;
                                case "28":
                                    $arrayRef[$i][5]["t28"]+= intval($rsie->Disponible);
                                    break;
                                case "30":
                                    $arrayRef[$i][5]["t30"]+= intval($rsie->Disponible);
                                    break;
                                case "32":
                                    $arrayRef[$i][5]["t32"]+= intval($rsie->Disponible);
                                    break;
                                case "34":
                                    $arrayRef[$i][5]["t34"]+= intval($rsie->Disponible);
                                    break;
                                case "36":
                                    $arrayRef[$i][5]["t36"]+= intval($rsie->Disponible);
                                    break;
                                case "38":
                                    $arrayRef[$i][5]["t38"]+= intval($rsie->Disponible);
                                    break;
                            }
                                $arrayRef[$i][5]["total"]+= intval($rsie->Disponible);
                        }
                    }elseif($rsie->IdBodega == "PTN"){
                        if($rsie->Referencia == $consulta[$i]->referencia){
                            switch ($rsie->IdExtension1) {
                                case "04":
                                    $arrayRef[$i][6]["t04"]+= intval($rsie->Disponible);
                                    break;
                                case "06":
                                    $arrayRef[$i][6]["t06"]+= intval($rsie->Disponible);
                                    break;
                                case "08":
                                    $arrayRef[$i][6]["t08"]+= intval($rsie->Disponible);
                                    break;
                                case "10":
                                    $arrayRef[$i][6]["t10"]+= intval($rsie->Disponible);
                                    break;
                                case "12":
                                    $arrayRef[$i][6]["t12"]+= intval($rsie->Disponible);
                                    break;
                                case "14":
                                    $arrayRef[$i][6]["t14"]+= intval($rsie->Disponible);
                                    break;
                                case "16":
                                    $arrayRef[$i][6]["t16"]+= intval($rsie->Disponible);
                                    break;
                                case "18":
                                    $arrayRef[$i][6]["t18"]+= intval($rsie->Disponible);
                                    break;
                                case "20":
                                    $arrayRef[$i][6]["t20"]+= intval($rsie->Disponible);
                                    break;
                                case "22":
                                    $arrayRef[$i][6]["t22"]+= intval($rsie->Disponible);
                                    break;
                                case "28":
                                    $arrayRef[$i][6]["t28"]+= intval($rsie->Disponible);
                                    break;
                                case "30":
                                    $arrayRef[$i][6]["t30"]+= intval($rsie->Disponible);
                                    break;
                                case "32":
                                    $arrayRef[$i][6]["t32"]+= intval($rsie->Disponible);
                                    break;
                                case "34":
                                    $arrayRef[$i][6]["t34"]+= intval($rsie->Disponible);
                                    break;
                                case "36":
                                    $arrayRef[$i][6]["t36"]+= intval($rsie->Disponible);
                                    break;
                                case "38":
                                    $arrayRef[$i][6]["t38"]+= intval($rsie->Disponible);
                                    break;
                            }
                                $arrayRef[$i][6]["total"]+= intval($rsie->Disponible);
                        }
                    }
                }    
            } catch (\Exception $e) {
                $refSiesa = DB::select('select * from zarethpr_proynew.inventario_sistemas where zarethpr_proynew.inventario_sistemas.sistema = 
                "SIESA" and fecha = "'.Carbon::now()->format('Y-m-d').'" and referencia LIKE "%'.$consulta[$i]->referencia.'%"');
                if(count($refSiesa) == 0){
                    $refSiesa = DB::select('select * from zarethpr_proynew.inventario_sistemas where zarethpr_proynew.inventario_sistemas.sistema = 
                    "SIESA" and fecha = (SELECT MAX(fecha) FROM zarethpr_proynew.inventario_sistemas) and referencia LIKE "%'.$consulta[$i]->referencia.'%"');
                }
                foreach($refSiesa as $rsie){
                    $rsie->bodega = trim($rsie->bodega);
                    $talla = "";
                    $array = explode("-", $rsie->referencia);
                    if(count($array) == 3){
                        if($array[0] == $consulta[$i]->referencia){
                            $rsie->referencia = $array[0];
                            $talla = $array[1];
                        }
                    }elseif(count($array) == 4){
                        if($array[0]."-".$array[1] == $consulta[$i]->referencia){
                            $rsie->referencia = $array[0]."-".$array[1];
                            $talla = $array[2];
                        }elseif($array[1] == $consulta[$i]->referencia){
                            $rsie->referencia = $array[1];
                            $talla = $array[2];
                        }
                    }elseif(count($array) == 5){
                        if($array[1]."-".$array[2] == $consulta[$i]->referencia){
                            $rsie->referencia = $array[1]."-".$array[2];
                            $talla = $array[3];
                        }
                    }

                    if($rsie->bodega == "PT001"){
                        if($rsie->referencia == $consulta[$i]->referencia){
                            switch ($talla){
                                    case "04":
                                        $arrayRef[$i][1]["t04"]+= intval($rsie->existencia);
                                        break;
                                    case "06":
                                        $arrayRef[$i][1]["t06"]+= intval($rsie->existencia);
                                        break;
                                    case "08":
                                        $arrayRef[$i][1]["t08"]+= intval($rsie->existencia);
                                        break;
                                    case "10":
                                        $arrayRef[$i][1]["t10"]+= intval($rsie->existencia);
                                        break;
                                    case "12":
                                        $arrayRef[$i][1]["t12"]+= intval($rsie->existencia);
                                        break;
                                    case "14":
                                        $arrayRef[$i][1]["t14"]+= intval($rsie->existencia);
                                        break;
                                    case "16":
                                        $arrayRef[$i][1]["t16"]+= intval($rsie->existencia);
                                        break;
                                    case "18":
                                        $arrayRef[$i][1]["t18"]+= intval($rsie->existencia);
                                        break;
                                    case "20":
                                        $arrayRef[$i][1]["t20"]+= intval($rsie->existencia);
                                        break;
                                    case "22":
                                        $arrayRef[$i][1]["t22"]+= intval($rsie->existencia);
                                        break;
                                    case "28":
                                        $arrayRef[$i][1]["t28"]+= intval($rsie->existencia);
                                        break;
                                    case "30":
                                        $arrayRef[$i][1]["t30"]+= intval($rsie->existencia);
                                        break;
                                    case "32":
                                        $arrayRef[$i][1]["t32"]+= intval($rsie->existencia);
                                        break;
                                    case "34":
                                        $arrayRef[$i][1]["t34"]+= intval($rsie->existencia);
                                        break;
                                    case "36":
                                        $arrayRef[$i][1]["t36"]+= intval($rsie->existencia);
                                        break;
                                    case "38":
                                        $arrayRef[$i][1]["t38"]+= intval($rsie->existencia);
                                        break;
                            }
                                $arrayRef[$i][1]["total"]+= intval($rsie->existencia);
                        }
                    }elseif($rsie->bodega == "PTCOR"){ 
                        if($rsie->referencia == $consulta[$i]->referencia){
                            switch ($talla){
                                    case "04":
                                        $arrayRef[$i][3]["t04"]+= intval($rsie->existencia);
                                        break;
                                    case "06":
                                        $arrayRef[$i][3]["t06"]+= intval($rsie->existencia);
                                        break;
                                    case "08":
                                        $arrayRef[$i][3]["t08"]+= intval($rsie->existencia);
                                        break;
                                    case "10":
                                        $arrayRef[$i][3]["t10"]+= intval($rsie->existencia);
                                        break;
                                    case "12":
                                        $arrayRef[$i][3]["t12"]+= intval($rsie->existencia);
                                        break;
                                    case "14":
                                        $arrayRef[$i][3]["t14"]+= intval($rsie->existencia);
                                        break;
                                    case "16":
                                        $arrayRef[$i][3]["t16"]+= intval($rsie->existencia);
                                        break;
                                    case "18":
                                        $arrayRef[$i][3]["t18"]+= intval($rsie->existencia);
                                        break;
                                    case "20":
                                        $arrayRef[$i][3]["t20"]+= intval($rsie->existencia);
                                        break;
                                    case "22":
                                        $arrayRef[$i][3]["t22"]+= intval($rsie->existencia);
                                        break;
                                    case "28":
                                        $arrayRef[$i][3]["t28"]+= intval($rsie->existencia);
                                        break;
                                    case "30":
                                        $arrayRef[$i][3]["t30"]+= intval($rsie->existencia);
                                        break;
                                    case "32":
                                        $arrayRef[$i][3]["t32"]+= intval($rsie->existencia);
                                        break;
                                    case "34":
                                        $arrayRef[$i][3]["t34"]+= intval($rsie->existencia);
                                        break;
                                    case "36":
                                        $arrayRef[$i][3]["t36"]+= intval($rsie->existencia);
                                        break;
                                    case "38":
                                        $arrayRef[$i][3]["t38"]+= intval($rsie->existencia);
                                        break;
                            }
                                $arrayRef[$i][3]["total"]+= intval($rsie->existencia);
                        }

                    }elseif($rsie->bodega == "PPLV"){
                        if($rsie->referencia == $consulta[$i]->referencia){
                            switch ($talla){
                                    case "04":
                                        $arrayRef[$i][4]["t04"]+= intval($rsie->existencia);
                                        break;
                                    case "06":
                                        $arrayRef[$i][4]["t06"]+= intval($rsie->existencia);
                                        break;
                                    case "08":
                                        $arrayRef[$i][4]["t08"]+= intval($rsie->existencia);
                                        break;
                                    case "10":
                                        $arrayRef[$i][4]["t10"]+= intval($rsie->existencia);
                                        break;
                                    case "12":
                                        $arrayRef[$i][4]["t12"]+= intval($rsie->existencia);
                                        break;
                                    case "14":
                                        $arrayRef[$i][4]["t14"]+= intval($rsie->existencia);
                                        break;
                                    case "16":
                                        $arrayRef[$i][4]["t16"]+= intval($rsie->existencia);
                                        break;
                                    case "18":
                                        $arrayRef[$i][4]["t18"]+= intval($rsie->existencia);
                                        break;
                                    case "20":
                                        $arrayRef[$i][4]["t20"]+= intval($rsie->existencia);
                                        break;
                                    case "22":
                                        $arrayRef[$i][4]["t22"]+= intval($rsie->existencia);
                                        break;
                                    case "28":
                                        $arrayRef[$i][4]["t28"]+= intval($rsie->existencia);
                                        break;
                                    case "30":
                                        $arrayRef[$i][4]["t30"]+= intval($rsie->existencia);
                                        break;
                                    case "32":
                                        $arrayRef[$i][4]["t32"]+= intval($rsie->existencia);
                                        break;
                                    case "34":
                                        $arrayRef[$i][4]["t34"]+= intval($rsie->existencia);
                                        break;
                                    case "36":
                                        $arrayRef[$i][4]["t36"]+= intval($rsie->existencia);
                                        break;
                                    case "38":
                                        $arrayRef[$i][4]["t38"]+= intval($rsie->existencia);
                                        break;
                            }
                                $arrayRef[$i][4]["total"]+= intval($rsie->existencia);
                        }
                        
                    }elseif($rsie->bodega == "PPTER" || $rsie->bodega == "TER"){
                        if($rsie->referencia == $consulta[$i]->referencia){
                            switch ($talla){
                                    case "04":
                                        $arrayRef[$i][5]["t04"]+= intval($rsie->existencia);
                                        break;
                                    case "06":
                                        $arrayRef[$i][5]["t06"]+= intval($rsie->existencia);
                                        break;
                                    case "08":
                                        $arrayRef[$i][5]["t08"]+= intval($rsie->existencia);
                                        break;
                                    case "10":
                                        $arrayRef[$i][5]["t10"]+= intval($rsie->existencia);
                                        break;
                                    case "12":
                                        $arrayRef[$i][5]["t12"]+= intval($rsie->existencia);
                                        break;
                                    case "14":
                                        $arrayRef[$i][5]["t14"]+= intval($rsie->existencia);
                                        break;
                                    case "16":
                                        $arrayRef[$i][5]["t16"]+= intval($rsie->existencia);
                                        break;
                                    case "18":
                                        $arrayRef[$i][5]["t18"]+= intval($rsie->existencia);
                                        break;
                                    case "20":
                                        $arrayRef[$i][5]["t20"]+= intval($rsie->existencia);
                                        break;
                                    case "22":
                                        $arrayRef[$i][5]["t22"]+= intval($rsie->existencia);
                                        break;
                                    case "28":
                                        $arrayRef[$i][5]["t28"]+= intval($rsie->existencia);
                                        break;
                                    case "30":
                                        $arrayRef[$i][5]["t30"]+= intval($rsie->existencia);
                                        break;
                                    case "32":
                                        $arrayRef[$i][5]["t32"]+= intval($rsie->existencia);
                                        break;
                                    case "34":
                                        $arrayRef[$i][5]["t34"]+= intval($rsie->existencia);
                                        break;
                                    case "36":
                                        $arrayRef[$i][5]["t36"]+= intval($rsie->existencia);
                                        break;
                                    case "38":
                                        $arrayRef[$i][5]["t38"]+= intval($rsie->existencia);
                                        break;
                            }
                                $arrayRef[$i][5]["total"]+= intval($rsie->existencia);
                        }

                    }elseif($rsie->bodega == "PTN"){
                        if($rsie->referencia == $consulta[$i]->referencia){
                            switch ($talla){
                                    case "04":
                                        $arrayRef[$i][6]["t04"]+= intval($rsie->existencia);
                                        break;
                                    case "06":
                                        $arrayRef[$i][6]["t06"]+= intval($rsie->existencia);
                                        break;
                                    case "08":
                                        $arrayRef[$i][6]["t08"]+= intval($rsie->existencia);
                                        break;
                                    case "10":
                                        $arrayRef[$i][6]["t10"]+= intval($rsie->existencia);
                                        break;
                                    case "12":
                                        $arrayRef[$i][6]["t12"]+= intval($rsie->existencia);
                                        break;
                                    case "14":
                                        $arrayRef[$i][6]["t14"]+= intval($rsie->existencia);
                                        break;
                                    case "16":
                                        $arrayRef[$i][6]["t16"]+= intval($rsie->existencia);
                                        break;
                                    case "18":
                                        $arrayRef[$i][6]["t18"]+= intval($rsie->existencia);
                                        break;
                                    case "20":
                                        $arrayRef[$i][6]["t20"]+= intval($rsie->existencia);
                                        break;
                                    case "22":
                                        $arrayRef[$i][6]["t22"]+= intval($rsie->existencia);
                                        break;
                                    case "28":
                                        $arrayRef[$i][6]["t28"]+= intval($rsie->existencia);
                                        break;
                                    case "30":
                                        $arrayRef[$i][6]["t30"]+= intval($rsie->existencia);
                                        break;
                                    case "32":
                                        $arrayRef[$i][6]["t32"]+= intval($rsie->existencia);
                                        break;
                                    case "34":
                                        $arrayRef[$i][6]["t34"]+= intval($rsie->existencia);
                                        break;
                                    case "36":
                                        $arrayRef[$i][6]["t36"]+= intval($rsie->existencia);
                                        break;
                                    case "38":
                                        $arrayRef[$i][6]["t38"]+= intval($rsie->existencia);
                                        break;
                            }
                                $arrayRef[$i][6]["total"]+= intval($rsie->existencia);
                        }
                    }
                }              
            }

            try{
                $refTns = DB::connection('firebird')->select("SELECT B.CODIGO CODBOD , B.NOMBRE NOMBOD , M.CODIGO , M.DESCRIP , SM.EXISTENC
                FROM MATERIAL M INNER JOIN SALMATERIAL SM ON (SM.MATID = M.MATID) INNER JOIN BODEGA B ON (B.BODID = SM.BODID)
                WHERE M.CODIGO LIKE '%".$consulta[$i]->referencia."%' AND (B.CODIGO = 'PT001' OR B.CODIGO = 'PT' OR B.CODIGO = 'PPCNI' 
                OR B.CODIGO = 'PPCOR' OR B.CODIGO = 'PPLV' OR B.CODIGO = 'PPTER' OR B.CODIGO = 'PTN' OR B.CODIGO = 'TER')");
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
                    if($rtns->CODBOD == "PT001" || $rtns->CODBOD == "PT"){
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
                    }elseif($rtns->CODBOD == "PPCNI"){
                        if($rtns->CODIGO == $consulta[$i]->referencia){
                            switch ($talla){
                                    case "04":
                                        $arrayRef[$i][2]["t04"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "06":
                                        $arrayRef[$i][2]["t06"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "08":
                                        $arrayRef[$i][2]["t08"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "10":
                                        $arrayRef[$i][2]["t10"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "12":
                                        $arrayRef[$i][2]["t12"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "14":
                                        $arrayRef[$i][2]["t14"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "16":
                                        $arrayRef[$i][2]["t16"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "18":
                                        $arrayRef[$i][2]["t18"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "20":
                                        $arrayRef[$i][2]["t20"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "22":
                                        $arrayRef[$i][2]["t22"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "28":
                                        $arrayRef[$i][2]["t28"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "30":
                                        $arrayRef[$i][2]["t30"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "32":
                                        $arrayRef[$i][2]["t32"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "34":
                                        $arrayRef[$i][2]["t34"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "36":
                                        $arrayRef[$i][2]["t36"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "38":
                                        $arrayRef[$i][2]["t38"]+= intval($rtns->EXISTENC);
                                        break;
                            }
                                $arrayRef[$i][2]["total"]+= intval($rtns->EXISTENC);
                        }                       
                    }elseif($rtns->CODBOD == "PPCOR"){
                        if($rtns->CODIGO == $consulta[$i]->referencia){
                            switch ($talla){
                                    case "04":
                                        $arrayRef[$i][3]["t04"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "06":
                                        $arrayRef[$i][3]["t06"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "08":
                                        $arrayRef[$i][3]["t08"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "10":
                                        $arrayRef[$i][3]["t10"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "12":
                                        $arrayRef[$i][3]["t12"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "14":
                                        $arrayRef[$i][3]["t14"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "16":
                                        $arrayRef[$i][3]["t16"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "18":
                                        $arrayRef[$i][3]["t18"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "20":
                                        $arrayRef[$i][3]["t20"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "22":
                                        $arrayRef[$i][3]["t22"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "28":
                                        $arrayRef[$i][3]["t28"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "30":
                                        $arrayRef[$i][3]["t30"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "32":
                                        $arrayRef[$i][3]["t32"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "34":
                                        $arrayRef[$i][3]["t34"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "36":
                                        $arrayRef[$i][3]["t36"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "38":
                                        $arrayRef[$i][3]["t38"]+= intval($rtns->EXISTENC);
                                        break;
                            }
                                $arrayRef[$i][3]["total"]+= intval($rtns->EXISTENC);
                        }

                    }elseif($rtns->CODBOD == "PPLV"){
                        if($rtns->CODIGO == $consulta[$i]->referencia){
                            switch ($talla){
                                    case "04":
                                        $arrayRef[$i][4]["t04"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "06":
                                        $arrayRef[$i][4]["t06"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "08":
                                        $arrayRef[$i][4]["t08"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "10":
                                        $arrayRef[$i][4]["t10"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "12":
                                        $arrayRef[$i][4]["t12"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "14":
                                        $arrayRef[$i][4]["t14"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "16":
                                        $arrayRef[$i][4]["t16"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "18":
                                        $arrayRef[$i][4]["t18"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "20":
                                        $arrayRef[$i][4]["t20"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "22":
                                        $arrayRef[$i][4]["t22"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "28":
                                        $arrayRef[$i][4]["t28"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "30":
                                        $arrayRef[$i][4]["t30"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "32":
                                        $arrayRef[$i][4]["t32"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "34":
                                        $arrayRef[$i][4]["t34"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "36":
                                        $arrayRef[$i][4]["t36"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "38":
                                        $arrayRef[$i][4]["t38"]+= intval($rtns->EXISTENC);
                                        break;
                            }
                                $arrayRef[$i][4]["total"]+= intval($rtns->EXISTENC);
                        }
                    }elseif($rtns->CODBOD == "PPTER" || $rtns->CODBOD == "TER"){
                        if($rtns->CODIGO == $consulta[$i]->referencia){
                            switch ($talla){
                                    case "04":
                                        $arrayRef[$i][5]["t04"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "06":
                                        $arrayRef[$i][5]["t06"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "08":
                                        $arrayRef[$i][5]["t08"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "10":
                                        $arrayRef[$i][5]["t10"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "12":
                                        $arrayRef[$i][5]["t12"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "14":
                                        $arrayRef[$i][5]["t14"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "16":
                                        $arrayRef[$i][5]["t16"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "18":
                                        $arrayRef[$i][5]["t18"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "20":
                                        $arrayRef[$i][5]["t20"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "22":
                                        $arrayRef[$i][5]["t22"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "28":
                                        $arrayRef[$i][5]["t28"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "30":
                                        $arrayRef[$i][5]["t30"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "32":
                                        $arrayRef[$i][5]["t32"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "34":
                                        $arrayRef[$i][5]["t34"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "36":
                                        $arrayRef[$i][5]["t36"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "38":
                                        $arrayRef[$i][5]["t38"]+= intval($rtns->EXISTENC);
                                        break;
                            }
                                $arrayRef[$i][5]["total"]+= intval($rtns->EXISTENC);
                        }
                    }elseif($rtns->CODBOD == "PTN"){
                        if($rtns->CODIGO == $consulta[$i]->referencia){
                            switch ($talla){
                                    case "04":
                                        $arrayRef[$i][6]["t04"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "06":
                                        $arrayRef[$i][6]["t06"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "08":
                                        $arrayRef[$i][6]["t08"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "10":
                                        $arrayRef[$i][6]["t10"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "12":
                                        $arrayRef[$i][6]["t12"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "14":
                                        $arrayRef[$i][6]["t14"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "16":
                                        $arrayRef[$i][6]["t16"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "18":
                                        $arrayRef[$i][6]["t18"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "20":
                                        $arrayRef[$i][6]["t20"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "22":
                                        $arrayRef[$i][6]["t22"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "28":
                                        $arrayRef[$i][6]["t28"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "30":
                                        $arrayRef[$i][6]["t30"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "32":
                                        $arrayRef[$i][6]["t32"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "34":
                                        $arrayRef[$i][6]["t34"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "36":
                                        $arrayRef[$i][6]["t36"]+= intval($rtns->EXISTENC);
                                        break;
                                    case "38":
                                        $arrayRef[$i][6]["t38"]+= intval($rtns->EXISTENC);
                                        break;
                            }
                                $arrayRef[$i][6]["total"]+= intval($rtns->EXISTENC);
                            }
                    } 
                }          
            } catch (\Exception $e) {
                $refTns = DB::select('select * from zarethpr_proynew.inventario_sistemas where zarethpr_proynew.inventario_sistemas.sistema = 
                "VISUAL TNS" and fecha = "'.Carbon::now()->format('Y-m-d').'"');
                if(count($refTns) == 0){
                    $refTns = DB::select('select * from zarethpr_proynew.inventario_sistemas where zarethpr_proynew.inventario_sistemas.sistema = 
                    "VISUAL TNS" and fecha = (SELECT MAX(fecha) FROM zarethpr_proynew.inventario_sistemas)');
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

                    if($rtns->bodega == "PT001" || $rtns->bodega == "PT"){
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
                    }elseif($rtns->bodega == "PPCNI"){
                        if($rtns->referencia == $consulta[$i]->referencia){
                            switch ($talla){
                                    case "04":
                                        $arrayRef[$i][2]["t04"]+= intval($rtns->existencia);
                                        break;
                                    case "06":
                                        $arrayRef[$i][2]["t06"]+= intval($rtns->existencia);
                                        break;
                                    case "08":
                                        $arrayRef[$i][2]["t08"]+= intval($rtns->existencia);
                                        break;
                                    case "10":
                                        $arrayRef[$i][2]["t10"]+= intval($rtns->existencia);
                                        break;
                                    case "12":
                                        $arrayRef[$i][2]["t12"]+= intval($rtns->existencia);
                                        break;
                                    case "14":
                                        $arrayRef[$i][2]["t14"]+= intval($rtns->existencia);
                                        break;
                                    case "16":
                                        $arrayRef[$i][2]["t16"]+= intval($rtns->existencia);
                                        break;
                                    case "18":
                                        $arrayRef[$i][2]["t18"]+= intval($rtns->existencia);
                                        break;
                                    case "20":
                                        $arrayRef[$i][2]["t20"]+= intval($rtns->existencia);
                                        break;
                                    case "22":
                                        $arrayRef[$i][2]["t22"]+= intval($rtns->existencia);
                                        break;
                                    case "28":
                                        $arrayRef[$i][2]["t28"]+= intval($rtns->existencia);
                                        break;
                                    case "30":
                                        $arrayRef[$i][2]["t30"]+= intval($rtns->existencia);
                                        break;
                                    case "32":
                                        $arrayRef[$i][2]["t32"]+= intval($rtns->existencia);
                                        break;
                                    case "34":
                                        $arrayRef[$i][2]["t34"]+= intval($rtns->existencia);
                                        break;
                                    case "36":
                                        $arrayRef[$i][2]["t36"]+= intval($rtns->existencia);
                                        break;
                                    case "38":
                                        $arrayRef[$i][2]["t38"]+= intval($rtns->existencia);
                                        break;
                            }
                                $arrayRef[$i][2]["total"]+= intval($rtns->existencia);
                        }
                    }elseif($rtns->bodega == "PPCOR"){
                        if($rtns->referencia == $consulta[$i]->referencia){
                            switch ($talla){
                                    case "04":
                                        $arrayRef[$i][3]["t04"]+= intval($rtns->existencia);
                                        break;
                                    case "06":
                                        $arrayRef[$i][3]["t06"]+= intval($rtns->existencia);
                                        break;
                                    case "08":
                                        $arrayRef[$i][3]["t08"]+= intval($rtns->existencia);
                                        break;
                                    case "10":
                                        $arrayRef[$i][3]["t10"]+= intval($rtns->existencia);
                                        break;
                                    case "12":
                                        $arrayRef[$i][3]["t12"]+= intval($rtns->existencia);
                                        break;
                                    case "14":
                                        $arrayRef[$i][3]["t14"]+= intval($rtns->existencia);
                                        break;
                                    case "16":
                                        $arrayRef[$i][3]["t16"]+= intval($rtns->existencia);
                                        break;
                                    case "18":
                                        $arrayRef[$i][3]["t18"]+= intval($rtns->existencia);
                                        break;
                                    case "20":
                                        $arrayRef[$i][3]["t20"]+= intval($rtns->existencia);
                                        break;
                                    case "22":
                                        $arrayRef[$i][3]["t22"]+= intval($rtns->existencia);
                                        break;
                                    case "28":
                                        $arrayRef[$i][3]["t28"]+= intval($rtns->existencia);
                                        break;
                                    case "30":
                                        $arrayRef[$i][3]["t30"]+= intval($rtns->existencia);
                                        break;
                                    case "32":
                                        $arrayRef[$i][3]["t32"]+= intval($rtns->existencia);
                                        break;
                                    case "34":
                                        $arrayRef[$i][3]["t34"]+= intval($rtns->existencia);
                                        break;
                                    case "36":
                                        $arrayRef[$i][3]["t36"]+= intval($rtns->existencia);
                                        break;
                                    case "38":
                                        $arrayRef[$i][3]["t38"]+= intval($rtns->existencia);
                                        break;
                            }
                                $arrayRef[$i][3]["total"]+= intval($rtns->existencia);
                        }
                    }elseif($rtns->bodega == "PPLV"){
                        if($rtns->referencia == $consulta[$i]->referencia){
                            switch ($talla){
                                    case "04":
                                        $arrayRef[$i][4]["t04"]+= intval($rtns->existencia);
                                        break;
                                    case "06":
                                        $arrayRef[$i][4]["t06"]+= intval($rtns->existencia);
                                        break;
                                    case "08":
                                        $arrayRef[$i][4]["t08"]+= intval($rtns->existencia);
                                        break;
                                    case "10":
                                        $arrayRef[$i][4]["t10"]+= intval($rtns->existencia);
                                        break;
                                    case "12":
                                        $arrayRef[$i][4]["t12"]+= intval($rtns->existencia);
                                        break;
                                    case "14":
                                        $arrayRef[$i][4]["t14"]+= intval($rtns->existencia);
                                        break;
                                    case "16":
                                        $arrayRef[$i][4]["t16"]+= intval($rtns->existencia);
                                        break;
                                    case "18":
                                        $arrayRef[$i][4]["t18"]+= intval($rtns->existencia);
                                        break;
                                    case "20":
                                        $arrayRef[$i][4]["t20"]+= intval($rtns->existencia);
                                        break;
                                    case "22":
                                        $arrayRef[$i][4]["t22"]+= intval($rtns->existencia);
                                        break;
                                    case "28":
                                        $arrayRef[$i][4]["t28"]+= intval($rtns->existencia);
                                        break;
                                    case "30":
                                        $arrayRef[$i][4]["t30"]+= intval($rtns->existencia);
                                        break;
                                    case "32":
                                        $arrayRef[$i][4]["t32"]+= intval($rtns->existencia);
                                        break;
                                    case "34":
                                        $arrayRef[$i][4]["t34"]+= intval($rtns->existencia);
                                        break;
                                    case "36":
                                        $arrayRef[$i][4]["t36"]+= intval($rtns->existencia);
                                        break;
                                    case "38":
                                        $arrayRef[$i][4]["t38"]+= intval($rtns->existencia);
                                        break;
                            }
                                $arrayRef[$i][4]["total"]+= intval($rtns->existencia);
                        }
                    }elseif($rtns->bodega == "PPTER" || $rtns->bodega == "TER"){
                        if($rtns->referencia == $consulta[$i]->referencia){
                            switch ($talla){
                                    case "04":
                                        $arrayRef[$i][5]["t04"]+= intval($rtns->existencia);
                                        break;
                                    case "06":
                                        $arrayRef[$i][5]["t06"]+= intval($rtns->existencia);
                                        break;
                                    case "08":
                                        $arrayRef[$i][5]["t08"]+= intval($rtns->existencia);
                                        break;
                                    case "10":
                                        $arrayRef[$i][5]["t10"]+= intval($rtns->existencia);
                                        break;
                                    case "12":
                                        $arrayRef[$i][5]["t12"]+= intval($rtns->existencia);
                                        break;
                                    case "14":
                                        $arrayRef[$i][5]["t14"]+= intval($rtns->existencia);
                                        break;
                                    case "16":
                                        $arrayRef[$i][5]["t16"]+= intval($rtns->existencia);
                                        break;
                                    case "18":
                                        $arrayRef[$i][5]["t18"]+= intval($rtns->existencia);
                                        break;
                                    case "20":
                                        $arrayRef[$i][5]["t20"]+= intval($rtns->existencia);
                                        break;
                                    case "22":
                                        $arrayRef[$i][5]["t22"]+= intval($rtns->existencia);
                                        break;
                                    case "28":
                                        $arrayRef[$i][5]["t28"]+= intval($rtns->existencia);
                                        break;
                                    case "30":
                                        $arrayRef[$i][5]["t30"]+= intval($rtns->existencia);
                                        break;
                                    case "32":
                                        $arrayRef[$i][5]["t32"]+= intval($rtns->existencia);
                                        break;
                                    case "34":
                                        $arrayRef[$i][5]["t34"]+= intval($rtns->existencia);
                                        break;
                                    case "36":
                                        $arrayRef[$i][5]["t36"]+= intval($rtns->existencia);
                                        break;
                                    case "38":
                                        $arrayRef[$i][5]["t38"]+= intval($rtns->existencia);
                                        break;
                            }
                                $arrayRef[$i][5]["total"]+= intval($rtns->existencia);
                        }
                    }elseif($rtns->bodega == "PTN"){
                        if($rtns->referencia == $consulta[$i]->referencia){
                            switch ($talla){
                                    case "04":
                                        $arrayRef[$i][6]["t04"]+= intval($rtns->existencia);
                                        break;
                                    case "06":
                                        $arrayRef[$i][6]["t06"]+= intval($rtns->existencia);
                                        break;
                                    case "08":
                                        $arrayRef[$i][6]["t08"]+= intval($rtns->existencia);
                                        break;
                                    case "10":
                                        $arrayRef[$i][6]["t10"]+= intval($rtns->existencia);
                                        break;
                                    case "12":
                                        $arrayRef[$i][6]["t12"]+= intval($rtns->existencia);
                                        break;
                                    case "14":
                                        $arrayRef[$i][6]["t14"]+= intval($rtns->existencia);
                                        break;
                                    case "16":
                                        $arrayRef[$i][6]["t16"]+= intval($rtns->existencia);
                                        break;
                                    case "18":
                                        $arrayRef[$i][6]["t18"]+= intval($rtns->existencia);
                                        break;
                                    case "20":
                                        $arrayRef[$i][6]["t20"]+= intval($rtns->existencia);
                                        break;
                                    case "22":
                                        $arrayRef[$i][6]["t22"]+= intval($rtns->existencia);
                                        break;
                                    case "28":
                                        $arrayRef[$i][6]["t28"]+= intval($rtns->existencia);
                                        break;
                                    case "30":
                                        $arrayRef[$i][6]["t30"]+= intval($rtns->existencia);
                                        break;
                                    case "32":
                                        $arrayRef[$i][6]["t32"]+= intval($rtns->existencia);
                                        break;
                                    case "34":
                                        $arrayRef[$i][6]["t34"]+= intval($rtns->existencia);
                                        break;
                                    case "36":
                                        $arrayRef[$i][6]["t36"]+= intval($rtns->existencia);
                                        break;
                                    case "38":
                                        $arrayRef[$i][6]["t38"]+= intval($rtns->existencia);
                                        break;
                            }
                                $arrayRef[$i][6]["total"]+= intval($rtns->existencia);
                        }
                    }
                }       
            }

            $refBmi = DB::select('select * from zarethpr_proynew.inventario_sistemas where zarethpr_proynew.inventario_sistemas.sistema = 
            "BMI" and fecha = "'.Carbon::now()->format('Y-m-d').'"');
            if(count($refBmi) == 0){
                $refBmi = DB::select('select * from zarethpr_proynew.inventario_sistemas where zarethpr_proynew.inventario_sistemas.sistema = 
                "BMI" and fecha = (SELECT MAX(fecha) FROM zarethpr_proynew.inventario_sistemas)');
            }
            
            foreach($refBmi as $rbmi){
                $talla = "";
                $array = explode("-", $rbmi->referencia);
                if(count($array) == 3){
                    if($array[0] == $consulta[$i]->referencia){
                        $rbmi->referencia = $array[0];
                        $talla = $array[1];
                    }
                }elseif(count($array) == 4){
                    if($array[0]."-".$array[1] == $consulta[$i]->referencia){
                        $rbmi->referencia = $array[0]."-".$array[1];
                        $talla = $array[2];
                    }elseif($array[1] == $consulta[$i]->referencia){
                        $rbmi->referencia = $array[1];
                        $talla = $array[2];
                    }
                }elseif(count($array) == 5){
                    if($array[1]."-".$array[2] == $consulta[$i]->referencia){
                        $rbmi->referencia = $array[1]."-".$array[2];
                        $talla = $array[3];
                    }
                }
                if($rbmi->bodega == "PT001"){
                    if($rbmi->referencia == $consulta[$i]->referencia){
                        switch ($talla){
                                case "04":
                                    $arrayRef[$i][1]["t04"]+= intval($rbmi->existencia);
                                    break;
                                case "06":
                                    $arrayRef[$i][1]["t06"]+= intval($rbmi->existencia);
                                    break;
                                case "08":
                                    $arrayRef[$i][1]["t08"]+= intval($rbmi->existencia);
                                    break;
                                case "10":
                                    $arrayRef[$i][1]["t10"]+= intval($rbmi->existencia);
                                    break;
                                case "12":
                                    $arrayRef[$i][1]["t12"]+= intval($rbmi->existencia);
                                    break;
                                case "14":
                                    $arrayRef[$i][1]["t14"]+= intval($rbmi->existencia);
                                    break;
                                case "16":
                                    $arrayRef[$i][1]["t16"]+= intval($rbmi->existencia);
                                    break;
                                case "18":
                                    $arrayRef[$i][1]["t18"]+= intval($rbmi->existencia);
                                    break;
                                case "20":
                                    $arrayRef[$i][1]["t20"]+= intval($rbmi->existencia);
                                    break;
                                case "22":
                                    $arrayRef[$i][1]["t22"]+= intval($rbmi->existencia);
                                    break;
                                case "28":
                                    $arrayRef[$i][1]["t28"]+= intval($rbmi->existencia);
                                    break;
                                case "30":
                                    $arrayRef[$i][1]["t30"]+= intval($rbmi->existencia);
                                    break;
                                case "32":
                                    $arrayRef[$i][1]["t32"]+= intval($rbmi->existencia);
                                    break;
                                case "34":
                                    $arrayRef[$i][1]["t34"]+= intval($rbmi->existencia);
                                    break;
                                case "36":
                                    $arrayRef[$i][1]["t36"]+= intval($rbmi->existencia);
                                    break;
                                case "38":
                                    $arrayRef[$i][1]["t38"]+= intval($rbmi->existencia);
                                    break;
                        }
                            $arrayRef[$i][1]["total"]+= intval($rbmi->existencia);
                    }
                }
            } 
            $comprometido = DB::select('select zarethpr_proynew.despacho.referencia, sum(zarethpr_proynew.despacho.t04) as t04,
            sum(zarethpr_proynew.despacho.t06 * -1) as t06, sum(zarethpr_proynew.despacho.t08 * -1) as t08, sum(zarethpr_proynew.despacho.t10 * -1) as t10,
            sum(zarethpr_proynew.despacho.t12 * -1) as t12, sum(zarethpr_proynew.despacho.t14 * -1) as t14, sum(zarethpr_proynew.despacho.t16 * -1) as t16,
            sum(zarethpr_proynew.despacho.t18 * -1) as t18, sum(zarethpr_proynew.despacho.t20 * -1) as t20, sum(zarethpr_proynew.despacho.t22 * -1) as t22,
            sum(zarethpr_proynew.despacho.t24 * -1) as t24, sum(zarethpr_proynew.despacho.t26 * -1) as t26, sum(zarethpr_proynew.despacho.t28 * -1) as t28,
            sum(zarethpr_proynew.despacho.t30 * -1) as t30, sum(zarethpr_proynew.despacho.t32 * -1) as t32, sum(zarethpr_proynew.despacho.t34 * -1) as t34,
            sum(zarethpr_proynew.despacho.t36 * -1) as t36, sum(zarethpr_proynew.despacho.t38 * -1) as t38, sum(zarethpr_proynew.despacho.xs * -1) as xs,
            sum(zarethpr_proynew.despacho.s * -1) as s, sum(zarethpr_proynew.despacho.m * -1) as m, sum(zarethpr_proynew.despacho.l * -1) as l,
            sum(zarethpr_proynew.despacho.xl * -1) as xl, sum((t04 + t06 + t08 + t10 + t12 + t14 + t16 + t18 + t20 + t22 + t24 + t26 + t28 + t30 + t32 + t34 + t36 + t38 + xs + s + m + l + xl) * -1) AS sum_tallas from zarethpr_proynew.despacho 
            where zarethpr_proynew.despacho.referencia = "'.$consulta[$i]->referencia.'" and (zarethpr_proynew.despacho.estado_detalle_orden = "APROBADO" or zarethpr_proynew.despacho.estado_detalle_orden = "ALISTAR"
            or zarethpr_proynew.despacho.estado_detalle_orden = "FACTURAR") and zarethpr_proynew.despacho.nit != "88242589"'); 
            if(count($comprometido) > 0){
                $comprometido = $comprometido[0];
                $arrayRef[$i][7]["t04"] = intVal($comprometido->t04);
                $arrayRef[$i][7]["t06"] = intVal($comprometido->t06);
                $arrayRef[$i][7]["t08"] = intVal($comprometido->t08);
                $arrayRef[$i][7]["t10"] = intVal($comprometido->t10);
                $arrayRef[$i][7]["t12"] = intVal($comprometido->t12);
                $arrayRef[$i][7]["t14"] = intVal($comprometido->t14);
                $arrayRef[$i][7]["t16"] = intVal($comprometido->t16);
                $arrayRef[$i][7]["t18"] = intVal($comprometido->t18);
                $arrayRef[$i][7]["t20"] = intVal($comprometido->t20);
                $arrayRef[$i][7]["t22"] = intVal($comprometido->t22);
                $arrayRef[$i][7]["t28"] = intVal($comprometido->t28);
                $arrayRef[$i][7]["t30"] = intVal($comprometido->t30);
                $arrayRef[$i][7]["t32"] = intVal($comprometido->t32);
                $arrayRef[$i][7]["t34"] = intVal($comprometido->t34);
                $arrayRef[$i][7]["t36"] = intVal($comprometido->t36);
                $arrayRef[$i][7]["t38"] = intVal($comprometido->t38);
                $arrayRef[$i][7]["total"] = intVal($comprometido->sum_tallas);
            }
            $ventam = DB::select('select zarethpr_proynew.filtrado.referencia, zarethpr_proynew.filtrado.marca, sum(zarethpr_proynew.filtrado.t04) as t04,
            sum(zarethpr_proynew.filtrado.t06 * -1) as t06, sum(zarethpr_proynew.filtrado.t08 * -1) as t08, sum(zarethpr_proynew.filtrado.t10 * -1) as t10,
            sum(zarethpr_proynew.filtrado.t12 * -1) as t12, sum(zarethpr_proynew.filtrado.t14 * -1) as t14, sum(zarethpr_proynew.filtrado.t16 * -1) as t16,
            sum(zarethpr_proynew.filtrado.t18 * -1) as t18, sum(zarethpr_proynew.filtrado.t20 * -1) as t20, sum(zarethpr_proynew.filtrado.t22 * -1) as t22,
            sum(zarethpr_proynew.filtrado.t24 * -1) as t24, sum(zarethpr_proynew.filtrado.t26 * -1) as t26, sum(zarethpr_proynew.filtrado.t28 * -1) as t28,
            sum(zarethpr_proynew.filtrado.t30 * -1) as t30, sum(zarethpr_proynew.filtrado.t32 * -1) as t32, sum(zarethpr_proynew.filtrado.t34 * -1) as t34,
            sum(zarethpr_proynew.filtrado.t36 * -1) as t36, sum(zarethpr_proynew.filtrado.t38 * -1) as t38, sum(zarethpr_proynew.filtrado.xs * -1) as xs,
            sum(zarethpr_proynew.filtrado.s * -1) as s, sum(zarethpr_proynew.filtrado.m * -1) as m, sum(zarethpr_proynew.filtrado.l * -1) as l,
            sum(zarethpr_proynew.filtrado.xl * -1) as xl, sum((t04 + t06 + 
            t08 + t10 + t12 + t14 + t16 + t18 + t20 + t22 + t24 + t26 + t28 + t30 + t32 + t34 + t36 + t38 + xs + s + m + l + xl) * -1) AS sum_tallas from 
            zarethpr_proynew.filtrado where zarethpr_proynew.filtrado.despacho = "APROBADO" AND zarethpr_proynew.filtrado.nit = "88242589"
            and zarethpr_proynew.filtrado.referencia = "'.$consulta[$i]->referencia.'"');
            if(count($ventam) > 0){
                $ventam = $ventam[0];
                $arrayRef[$i][8]["t04"] = intVal($ventam->t04);
                $arrayRef[$i][8]["t06"] = intVal($ventam->t06);
                $arrayRef[$i][8]["t08"] = intVal($ventam->t08);
                $arrayRef[$i][8]["t10"] = intVal($ventam->t10);
                $arrayRef[$i][8]["t12"] = intVal($ventam->t12);
                $arrayRef[$i][8]["t14"] = intVal($ventam->t14);
                $arrayRef[$i][8]["t16"] = intVal($ventam->t16);
                $arrayRef[$i][8]["t18"] = intVal($ventam->t18);
                $arrayRef[$i][8]["t20"] = intVal($ventam->t20);
                $arrayRef[$i][8]["t22"] = intVal($ventam->t22);
                $arrayRef[$i][8]["t28"] = intVal($ventam->t28);
                $arrayRef[$i][8]["t30"] = intVal($ventam->t30);
                $arrayRef[$i][8]["t32"] = intVal($ventam->t32);
                $arrayRef[$i][8]["t34"] = intVal($ventam->t34);
                $arrayRef[$i][8]["t36"] = intVal($ventam->t36);
                $arrayRef[$i][8]["t38"] = intVal($ventam->t38);
                $arrayRef[$i][8]["total"] = intVal($ventam->sum_tallas);
            }
            $comprometidom = DB::select('select zarethpr_proynew.despacho.referencia, sum(zarethpr_proynew.despacho.t04) as t04,
            sum(zarethpr_proynew.despacho.t06 * -1) as t06, sum(zarethpr_proynew.despacho.t08 * -1) as t08, sum(zarethpr_proynew.despacho.t10 * -1) as t10,
            sum(zarethpr_proynew.despacho.t12 * -1) as t12, sum(zarethpr_proynew.despacho.t14 * -1) as t14, sum(zarethpr_proynew.despacho.t16 * -1) as t16,
            sum(zarethpr_proynew.despacho.t18 * -1) as t18, sum(zarethpr_proynew.despacho.t20 * -1) as t20, sum(zarethpr_proynew.despacho.t22 * -1) as t22,
            sum(zarethpr_proynew.despacho.t24 * -1) as t24, sum(zarethpr_proynew.despacho.t26 * -1) as t26, sum(zarethpr_proynew.despacho.t28 * -1) as t28,
            sum(zarethpr_proynew.despacho.t30 * -1) as t30, sum(zarethpr_proynew.despacho.t32 * -1) as t32, sum(zarethpr_proynew.despacho.t34 * -1) as t34,
            sum(zarethpr_proynew.despacho.t36 * -1) as t36, sum(zarethpr_proynew.despacho.t38 * -1) as t38, sum(zarethpr_proynew.despacho.xs * -1) as xs,
            sum(zarethpr_proynew.despacho.s * -1) as s, sum(zarethpr_proynew.despacho.m * -1) as m, sum(zarethpr_proynew.despacho.l * -1) as l,
            sum(zarethpr_proynew.despacho.xl * -1) as xl, sum((t04 + t06 + t08 + t10 + t12 + t14 + t16 + t18 + t20 + t22 + t24 + t26 + t28 + t30 + t32 + t34 + t36 + t38 + xs + s + m + l + xl) * -1) AS sum_tallas from zarethpr_proynew.despacho 
            where zarethpr_proynew.despacho.referencia = "'.$consulta[$i]->referencia.'" and (zarethpr_proynew.despacho.estado_detalle_orden = "APROBADO" or zarethpr_proynew.despacho.estado_detalle_orden = "ALISTAR"
            or zarethpr_proynew.despacho.estado_detalle_orden = "FACTURAR") and zarethpr_proynew.despacho.nit = "88242589"');
            if(count($comprometidom) > 0){
                $comprometidom = $comprometidom[0];
                $arrayRef[$i][9]["t04"] = intVal($comprometidom->t04);
                $arrayRef[$i][9]["t06"] = intVal($comprometidom->t06);
                $arrayRef[$i][9]["t08"] = intVal($comprometidom->t08);
                $arrayRef[$i][9]["t10"] = intVal($comprometidom->t10);
                $arrayRef[$i][9]["t12"] = intVal($comprometidom->t12);
                $arrayRef[$i][9]["t14"] = intVal($comprometidom->t14);
                $arrayRef[$i][9]["t16"] = intVal($comprometidom->t16);
                $arrayRef[$i][9]["t18"] = intVal($comprometidom->t18);
                $arrayRef[$i][9]["t20"] = intVal($comprometidom->t20);
                $arrayRef[$i][9]["t22"] = intVal($comprometidom->t22);
                $arrayRef[$i][9]["t28"] = intVal($comprometidom->t28);
                $arrayRef[$i][9]["t30"] = intVal($comprometidom->t30);
                $arrayRef[$i][9]["t32"] = intVal($comprometidom->t32);
                $arrayRef[$i][9]["t34"] = intVal($comprometidom->t34);
                $arrayRef[$i][9]["t36"] = intVal($comprometidom->t36);
                $arrayRef[$i][9]["t38"] = intVal($comprometidom->t38);
                $arrayRef[$i][9]["total"] = intVal($comprometidom->sum_tallas);
            }
        }
        return Excel::download(new FiltroExport($arrayRef),"INFORME VENTAS VS INV.xlsx");
    }

    public function backupDailyDataSystem()
    {
        ini_set('max_execution_time', 10800);
        $created_at = Carbon::now();
        $fecha = Carbon::now()->format('Y-m-d');
        $idControlInv = DB::select('select * from zarethpr_proynew.inventario_sistemas where fecha = "'.$fecha.'"');
        if(count($idControlInv) == 0){
            $idControlInv = DB::select('select MAX(id_control_inventario) as id_control_inventario from zarethpr_proynew.inventario_sistemas');
            $idControlInv = $idControlInv[0]->id_control_inventario + 1;
        }else{
            $idControlInv = $idControlInv[0]->id_control_inventario;
        }
        $siesa = ""; $tns = "";
        
        try{
            $consultaSIESA = DB::select('select * from zarethpr_proynew.inventario_sistemas where fecha = "'.$fecha.'" and sistema = "SIESA"');
            
            if(count($consultaSIESA) == 0){
                
                $countSincSiesa = 0;
                $Username='bless';
                $Password='orgblessRe$t';
                $client = new \GuzzleHttp\Client(['base_uri' => 'http://45.76.251.153']);
                $response = $client->request('post','/API_GT/api/login/authenticate', [
                    'form_params' => [
                        'Username' => $Username,
                        'Password' => $Password,
                    ]
                ]);
                $url="/API_GT/api/orgBless/getInvPorBodega?CentroOperacion=001";      
                $token=$response->getBody()->getContents();
                $token=str_replace('"','',$token);   
                $response2 = $client->request('get',$url, [
                    'headers' => [ 'Authorization' => 'Bearer '.$token ],
                ]);        
                $refSiesa = json_decode($response2->getBody()->getContents());
                $refSiesa->detail == null ? $refSiesa = [] : $refSiesa = $refSiesa->detail;
                $refSiesa = $this->deleteDuplicateObjects($refSiesa);
                foreach($refSiesa as $rsie){
                    if($rsie->IdBodega == "PT001" || $rsie->IdBodega == "PPCNI" || $rsie->IdBodega == "PTCOR" || $rsie->IdBodega == "PPLV" || $rsie->IdBodega == "PPTER" || $rsie->IdBodega == "PTN"){
                        DB::insert('insert into zarethpr_proynew.inventario_sistemas (id_control_inventario, sistema, bodega, fecha, referencia, existencia, created_at) values (?, ?, ?, ?, ?, ?, ?)', 
                        [$idControlInv, 'SIESA', $rsie->IdBodega, $fecha, $rsie->CodBarras, $rsie->Disponible, $created_at]);
                        $countSincSiesa++;
                    }
                }
                $siesa = "Se ha hecho respaldo local a las ".$created_at." con un total de ".$countSincSiesa." registros de Siesa para el filtro.";
                echo $siesa;
            }else{
                $siesa = "Ya se hizo un respaldo local a las ".$consultaSIESA[0]->created_at." de los registros de Siesa para el filtro.";
                echo $siesa;
            }
        } catch (\Exception $e) {
            $siesa = "No se ha podido respaldar los registros de Siesa para el filtro.";
            echo $siesa;
        }

        try{
            $consultaTNS = DB::select('select * from zarethpr_proynew.inventario_sistemas where fecha = "'.$fecha.'" and sistema = "VISUAL TNS"');
            if(count($consultaTNS) == 0){
                $countSincTNS = 0;
                $refTns = DB::connection('firebird')->select("SELECT B.CODIGO CODBOD , B.NOMBRE NOMBOD , M.CODIGO , M.DESCRIP , SM.EXISTENC
                FROM MATERIAL M INNER JOIN SALMATERIAL SM ON (SM.MATID = M.MATID) INNER JOIN BODEGA B ON (B.BODID = SM.BODID)
                WHERE B.CODIGO = 'PPCNI' OR B.CODIGO = 'PPCOR' OR B.CODIGO = 'PPLV' OR B.CODIGO = 'PPTER' OR B.CODIGO = 'PTN' OR B.CODIGO = 'TER' 
                OR B.CODIGO = 'PT001' OR B.CODIGO = 'PT'");
                foreach($refTns as $rtns){
                    DB::insert('insert into zarethpr_proynew.inventario_sistemas (id_control_inventario, sistema, bodega, fecha, referencia, existencia, created_at) values (?, ?, ?, ?, ?, ?, ?)', 
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
