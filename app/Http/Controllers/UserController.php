<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Solicitud;
use App\Models\User;
use App\Models\QrDespachos;
use Illuminate\Database\QueryException;


class UserController extends Controller
{
    public function showMySolicitudes(Request $request){
        $user = Auth::user();
        //$solicitudes = Solicitud::where('user_id','=',$user->id)->where('estado_id','=',1)->whereBetween("created_at",array('2021-08-09','2021-08-12'))->get();
        $solicitudes = Solicitud::where('user_id','=',$user->id)->where('estado_id','=',1)->orWhere('estado_id','=',5)->orWhere('estado_id','=',3)->orWhere('estado_id','=',4)->get();
        
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

    public function show($id){
        return response()->json(User::find($id));
    }


    /*Este metodo sube la informacion de las cajas despachadas. */

    public function storeDespachoQR(Request $request){
        try {
            $despacho = QrDespachos::create([
                'destinatario' => $request->destinatario,
                'nit' => $request->nit,
                'direccion' => $request->direccion,
                'departamento' => $request->departamento,
                'ciudad' => $request->ciudad,
                'phone' => $request->phone,
                'factura_numero' => $request->factura_numero,
                'ncaja' => $request->ncaja,
                'cajas' => $request->cajas,
                'cantidad' => $request->cantidad,
                'peso' => $request->peso,
                'ndespacho' => $request->ndespacho,
                'npedido' => $request->npedido,
                'filtro' => $request->filtro,
                'user_id'  => Auth::user()->id
            ]);
            return response()->json(['message' => 'Despacho subido correctamente. [CAJA]: '.$request->ncaja],201);
        } catch (QueryException $ex) {
            return response()->json(['message' => 'Ya existe registro de despacho de esta caja.'],400);
        }
    }

    public function getNotificacionesNormal(){
        $noti=[];
        $j=0;
        $notificaciones = Auth::user()->notificacions;
        for($i=0;$i<count($notificaciones);$i++){
            if($notificaciones[$i]->estado == 1 && str_contains($notificaciones[$i]->descripcion,'Insumos informa')){
                if($j<=5){
                    $noti[$j]=$notificaciones[$i];
                    $j++;
                }
            }
        }
        return response()->json($noti);
    }
    public function getNotificaciones(){
        $noti=[];
        $j=0;
        $notificaciones = Auth::user()->rol->notificacions;
        for($i=0;$i<count($notificaciones);$i++){
            if($notificaciones[$i]->estado == 1){
                if($j<=5){
                    $noti[$j]=$notificaciones[$i];
                    $j++;
                }
            }
        }
        return response()->json($noti);
    }
    
    
    
    
    public function findByDocument(Request $request){
        $documento = $request->documento;
        $user = User::where('documento','=',$documento)->first();
        if(isset($user)) return response()->json(['user' => $user],200);
        return response()->json(['message' => 'El usuario no existe']);
    }
    
    
    public function create(Request $request){
        //$user = $request->user;
        $user = [
            'documento' => $request->user['documento'],
    		'names' => $request->user['nombres'],
    		'apellidos' => $request->user['apellido'],
    		'rol_id' => $request->user['rol'],
    		'email' => $request->user['correo'],
    		'password' => bcrypt($request->user['password']),
    		'tiendacargo' => $request->user['tiendacargo'],
        ];
        
        User::create($user);
        
        return response()->json(['message' => 'Se ha creado exitosamente el usuario.']);
    }

    public function all(Request $request){
        $user = [];
        $users = User::get();
        foreach($users as $u){
            $item = [
                'id' => $u->id,
                'documento' => $u->documento,
                'names' => $u->names,
                'apellidos' => $u->apellidos,
                'email' => $u->email,
                'tiendacargo' => $u->tiendacargo,
                'rol' => $u->rol->descripcion,
                'rol_id' => $u->rol->id,
                'creacion' => $u->created_at,
            ];
            array_push($user, $item);
        }
        return response()->json($user);
    }
}
