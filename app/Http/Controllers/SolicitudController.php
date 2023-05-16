<?php

namespace App\Http\Controllers;

use App\Models\Detallesolicitud;
use App\Models\Revisionsolicitud;
use App\Models\Solicitud;
use Illuminate\Support\Facades\Auth;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SolicitudController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('solicitud.crear')->with('notificaciones',$this->getNotificaciones());;
    }

    public function misolicitudes(){
        return view('solicitud.misolicitudes')->with('notificaciones',$this->getNotificaciones());;
    }

    private function getNotificaciones(){
        $noti=[];
        $j=0;
        $notificaciones = auth()->user()->rol->notificacions;
        for($i=0;$i<count($notificaciones);$i++){
            if($notificaciones[$i]->estado == 1){
                if($j<=5){
                    $noti[$j]=$notificaciones[$i];
                    $j++;
                }
            }
        }
        return $noti;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $uid=Str::random(5);
        $solicitud = Solicitud::create([
            'tiposolicitud_id' => $request->tiposolicitud_id,
            'estado_id' => $request->estado_id,
            'solicituduid' => $uid,
            'user_id' => $request->user_id,
            'observacion' => $request->observacion,
            'cantidadtotal' => $request->cantidadtotal,
            'fechaemision' => \Carbon\Carbon::now()->format('Y-m-d')
        ]);

        for($i=0;$i<count($request->items);$i++){
            Detallesolicitud::create([
                'solicitud_id' => $solicitud->id,
                'refItem' => $request->items[$i]['referencia'],
                'codbarras' => $request->items[$i]['codbarra'],
                'cantidad' => $request->items[$i]['cantidad']
            ]);
        }

        $notificacion = Notificacion::create([
            'rol_id' => 2,
            'user_id' => $request->user_id,
            'title' => 'Nueva Solicitud #'.$solicitud->solicituduid,
            'descripcion' => '[Solicitud de Impresion]#'.$solicitud->solicituduid.' '.$solicitud->user->names.' ha registrado una nueva solicitud'
        ]);

        return response()->json('La solicitud ha sido creada con <b>ID: '.$solicitud->solicituduid.'</b> dirigase a Solicitudes > Mis Solicitudes para gestionar sus solicitudes.');
    }


    /*Metodo que informa al solicitante que los tickets ya estan impresos*/
    public function informarSolicitante(Request $request){
        Notificacion::where('descripcion','LIKE','%'.$request->codigo.'%')->update(['rol_id' => 4,'estado' => 1,'title' => 'Tickets Impresos','descripcion' => 'Insumos informa: Sus tickets de la solicitud #'.$request->codigo.'han sido impresos.']);
        Solicitud::where('solicituduid','=',$request->codigo)->update(['estado_id' => 4, 'observacion' => 'Los tickets han sido impresos.']);    
        return response()->json(['message' => 'Se ha informado correctamente al solicitante.']);
    }



    public function destroy(Request $request)
    {
        $codigo = $request->codigo;
        $tipo = $request->tipo;
        $observacionn = $request->observacion;
        $user = Auth::user();

        if($tipo == 'user'){
            $observacion = 'El creador de la solicitud ['.$user->names.' '.$user->apellidos.' ['.$user->rol->descripcion.'] ha cancelado. MOTIVO: '.$observacionn;
        }else if($tipo == 'insumos'){
            $observacion = 'Operador de Insumos ['.$user->names.' '.$user->apellidos.' ['.$user->rol->descripcion.'] ha cancelado. MOTIVO: '.$observacionn;
        }
        $solicitud = Solicitud::where('solicituduid','=',$codigo)->get();
        Solicitud::where('solicituduid','=',$codigo)->update(['estado_id' => 5, 'observacion' => $observacion]);
        Notificacion::where('title','LIKE','%'.$codigo.'%')->update(['estado' => 2]);
        Revisionsolicitud::create([
            'solicitud_id' => $solicitud[0]->id,
            'user_id' => auth()->user()->id
        ]);

        return response()->json(['message' => 'Solicitud eliminada correctamente '], 200);
    }

    public function get(Request $request){
        $solicitud = Solicitud::where('solicituduid','=',$request->codigo)->get();
        return response()->json($solicitud);
    }

    public function getItems(Request $request){
        $solicitud = Solicitud::where('solicituduid','=',$request->codigo)->get();
        $arrayItems = [];
        for($i=0;$i<count($solicitud[0]->detallesolicituds);$i++){
            $item = [
                'id' => $i+1,
                'referencia' => $solicitud[0]->detallesolicituds[$i]->refItem,
                'codbarra' => $solicitud[0]->detallesolicituds[$i]->codbarras,
                'cantidad' =>$solicitud[0]->detallesolicituds[$i]->cantidad
            ];
            array_push($arrayItems,$item);
        }
        $array = [
            'cantotal' => $solicitud[0]->cantidadtotal,
            'items' => $arrayItems
        ];
        return response()->json($array);
    }

    public function listar(){
        return view('solicitud.listar')->with('notificaciones',$this->getNotificaciones());
    }

    public function all(){
        //$solicitudes = Solicitud::get();
        $solicitudes = Solicitud::where('estado_id','=',1)->orWhere('estado_id','=',3)->orWhere('estado_id','=',5)->orWhere('estado_id','=',4)->get();
        $arrayDevuelve = [];

        for($i=0;$i<count($solicitudes);$i++){
            $fechaCreacion = $solicitudes[$i]['created_at'];
            $solicitante = $solicitudes[$i]['user']->names.' ['.$solicitudes[0]['user']->rol->slug.']';
            $tipoSolicitud = $solicitudes[$i]['tiposolicitud']->descripcion;
            $estado = $solicitudes[$i]['estado']->estado;
            $observacion = $solicitudes[$i]['observacion'];
            $cantotal = $solicitudes[$i]['cantidadtotal'];
            $revsolicitud = $solicitudes[$i]['revisionsolicituds'];
            $solicituduid = $solicitudes[$i]['solicituduid'];
            if($revsolicitud != '[]'){
                $item = [
                    'id' => $i+1,
                    'fechaCreacion' => $fechaCreacion->format('Y-m-d H:00'),
                    'solicitante' => $solicitante,
                    'tipoSolicitud' => $tipoSolicitud,
                    'estado' => $estado,
                    'observacion' => $observacion,
                    'codigo' => $solicituduid,
                    'cantotal' => $cantotal,
                    'revsolicitud' => $revsolicitud[0]->created_at->format('Y-m-d H:00'),
                    'encargadorev' => $revsolicitud[0]->user->names.' ['.$revsolicitud[0]->user->rol->slug.']'
                ];
            }else{
                $item = [
                    'id' => $i+1,
                    'fechaCreacion' => $fechaCreacion->format('Y-m-d H:00'),
                    'solicitante' => $solicitante,
                    'tipoSolicitud' => $tipoSolicitud,
                    'estado' => $estado,
                    'observacion' => $observacion,
                    'codigo' => $solicituduid,
                    'cantotal' => $cantotal,
                    'revsolicitud' => 'Sin revisión',
                    'encargadorev' => 'No hay revisión'
                ];
            }
            array_push($arrayDevuelve,$item);
        }

        return response()->json($arrayDevuelve);
    }

    public function allday(){
        //$solicitudes = Solicitud::get();
        //$solicitudes = Solicitud::where('estado_id','=',1)->orWhere('estado_id','=',3)->orWhere('estado_id','=',5)->orWhere('estado_id','=',4)->get();
        $solicitudes = Solicitud::whereDate('created_at','=',date('Y-m-d'))->orderBy('id', 'desc')->get();
        $arrayDevuelve = [];

        for($i=0;$i<count($solicitudes);$i++){
            $fechaCreacion = $solicitudes[$i]['created_at'];
            $solicitante = $solicitudes[$i]['user']->names.' ['.$solicitudes[0]['user']->rol->slug.']';
            $tipoSolicitud = $solicitudes[$i]['tiposolicitud']->descripcion;
            $estado = $solicitudes[$i]['estado']->estado;
            $observacion = $solicitudes[$i]['observacion'];
            $cantotal = $solicitudes[$i]['cantidadtotal'];
            $revsolicitud = $solicitudes[$i]['revisionsolicituds'];
            $solicituduid = $solicitudes[$i]['solicituduid'];
            if($revsolicitud != '[]'){
                $item = [
                    'id' => $i+1,
                    'fechaCreacion' => $fechaCreacion->format('Y-m-d H:00'),
                    'solicitante' => $solicitante,
                    'tipoSolicitud' => $tipoSolicitud,
                    'estado' => $estado,
                    'observacion' => $observacion,
                    'codigo' => $solicituduid,
                    'cantotal' => $cantotal,
                    'revsolicitud' => $revsolicitud[0]->created_at->format('Y-m-d H:00'),
                    'encargadorev' => $revsolicitud[0]->user->names.' ['.$revsolicitud[0]->user->rol->slug.']'
                ];
            }else{
                $item = [
                    'id' => $i+1,
                    'fechaCreacion' => $fechaCreacion->format('Y-m-d H:00'),
                    'solicitante' => $solicitante,
                    'tipoSolicitud' => $tipoSolicitud,
                    'estado' => $estado,
                    'observacion' => $observacion,
                    'codigo' => $solicituduid,
                    'cantotal' => $cantotal,
                    'revsolicitud' => 'Sin revisión',
                    'encargadorev' => 'No hay revisión'
                ];
            }
            array_push($arrayDevuelve,$item);
        }

        return response()->json($arrayDevuelve);
    }



    public function getsolicitud($solicitud){
        $j=$this->buscarEnApi($solicitud)->detail;
        return dd($j[0]->DetalleExt1);
        //return Solicitud::where('solicituduid','=',$solicitud)->get()[0]->detallesolicituds;
    }

    public function aprove(Request $request){
        $solicitud = Solicitud::where('solicituduid','=',$request->codigo)->get();
        Solicitud::where('solicituduid','=',$request->codigo)->update(['estado_id' => 3, 'observacion' => 'Insumos ha aprobado la solicitud, se remite a imprimir']);
        Revisionsolicitud::create([
            'solicitud_id' => $solicitud[0]->id,
            'user_id' => auth()->user()->id
        ]);


        switch ($solicitud[0]->tiposolicitud->id) {
            case 2:
                return $this->getCSVCalifornia($solicitud);
                break;
            case 3:
                return $this->getCSVNacional($solicitud);
                break;
            case 4:
                return $this->getCSVCatalogoTiendas($solicitud);
                break;
            case 5:
                return $this->getCSVReimpresionConAnio($solicitud);
                break;
            case 6:
                return $this->getCSVReimpresionConAnioTallaColor($solicitud);
                break;
            case 7:
                return $this->getCSVReimpresionConColeccion($solicitud);
                break;
            default:
            return $this->getCSVCatalogo($solicitud);
            break;
        }
    }

    private function getCSVCatalogo($solicitud){
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=for_catalogo.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array(
            'descorta',
            'codbarras',
            'descodbarras',
            'sku' 
        );

        $items = $solicitud[0]->detallesolicituds;
        $stickers = array();

        for($i=0;$i<count($items);$i++){
            $itemInfo=$this->buscarEnApi($items[$i]->codbarras)->detail;
            $talla = $itemInfo[0]->DetalleExt1;
            $color = $itemInfo[0]->DetalleExt2;

            $sticker = array(
                'descorta' => 'PANTALON',
                'codbarras' => $items[$i]->codbarras,
                'descodbarras' => $items[$i]->refItem.'-'.$talla.'-'.$color,
                'sku' =>$items[$i]->codbarras
            );

        array_push($stickers,$sticker);
        }

        $callback = function() use ($stickers, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            for($i=0;$i<count($stickers);$i++){
                fputcsv($file,array(
                    $stickers[$i]['descorta'],
                    $stickers[$i]['codbarras'],
                    $stickers[$i]['descodbarras'],
                    $stickers[$i]['sku']
                ));
            }                
            fclose($file);
        };
        return Response()->stream($callback, 200, $headers);
    }

    private function buscarEnApi($codbarra){
        $Username='bless';
        $Password='orgblessRe$t';
        $client = new \GuzzleHttp\Client(['base_uri' => 'http://45.76.251.153']);
        $response = $client->request('post','/API_GT/api/login/authenticate', [
            'form_params' => [
                'Username' => $Username,
                'Password' => $Password,
            ]
        ]);

        $url="/API_GT/api/orgBless/getRefPorMarca?CodBarras={$codbarra}";
       
        $token=$response->getBody()->getContents();
        $token=str_replace('"','',$token);
     
         $response2 = $client->request('get',$url, [
            'headers' => [ 'Authorization' => 'Bearer '.$token ],
        ]);
        
        $variable=json_decode($response2->getBody()->getContents());
        return $variable;
    }




}
