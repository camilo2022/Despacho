<?php

namespace App\Http\Controllers;

use App\Models\DetalleSolicitud;
use Illuminate\Http\Request;
use App\Models\Siesa\TercerosModel;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ClientesModelTiendas;
use App\Imports\ProductosImport;
use App\Models\ProductoSiesa;

class SiesaController extends Controller
{
    public function sincronize_clientes(Request $request){
        
       $clientes = $this->getClientes();
       $icount=0;
        TercerosModel::truncate();
       for($i=0;$i<count($clientes);$i++){
            $tercero = json_decode(json_encode(TercerosModel::where('nit','=',$clientes[$i]->NitCli)->first()));
                TercerosModel::create([
                    'centrooperacion' => $clientes[$i]->CentroOperacion,
                    'nit' => $clientes[$i]->NitCli,
                    'idcliente' => $clientes[$i]->IdCliente,
                    'razonsocial' => $clientes[$i]->RazonSocialCli,
                    'idsucursal' => $clientes[$i]->IdSucursal,
                    'descsucursal' => $clientes[$i]->DescSucursal,
                    'departamento' => $clientes[$i]->Departamento,
                    'ciudad' => $clientes[$i]->Ciudad,
                    'direccion' => $clientes[$i]->Direccion,
                    'telefono' => $clientes[$i]->Telefono,
                    'direcciondespacho' => $clientes[$i]->DireccionDespacho,
                    'correo' => $clientes[$i]->Correo,
                    'zona' => $clientes[$i]->Zona,
                    'celulartercero' => $clientes[$i]->CelularTercero,
                    'celularsucursal' => $clientes[$i]->CelularSucursal,
                ]);
                $icount++;
        }
        return response()->json(['message' => 'Se han sincronizada '.$icount.' clientes de SIESA.'],200);
    }

    public function sincronizeProductos(Request $request){
        $productos = $this->getProductos();
        $refe = [];
        for($i=0;$i<count($productos);$i++){
            array_push($refe,$productos[$i]->ReferenciaItem);
        }
        dd($refe);
    }


    public function submitproductosSiesa_Manual(Request $request){
        $file = $request->file;
        $productos = new ProductosImport();
        $ts=Excel::import($productos,$file);
        $items=json_decode(json_encode($productos->sheetData[0]));
        $actualizados = 0;
        $creados = 0;
        for($i=0;$i<count($items);$i++){
            $productoSiesa = ProductoSiesa::where('sku','=',$items[$i]->CODIGO)->first();
            if($productoSiesa == null){
                $productoSiesa = ProductoSiesa::create([
                   'sku' => $items[$i]->CODIGO,
                   'referencia' => $items[$i]->REFERENCIA,
                   'talla' => $items[$i]->EXT1,
                   'color' => $items[$i]->EXT2,
                   'descItem' => $items[$i]->DESC,
                   'precio' => $items[$i]->PRECIO,
                ]);
                $creados++;
            }else{
                $productoSiesa->referencia = $items[$i]->REFERENCIA;
                $productoSiesa->talla = $items[$i]->EXT1;
                $productoSiesa->color = $items[$i]->EXT2;
                $productoSiesa->descItem = $items[$i]->DESC;
                $productoSiesa->precio = $items[$i]->PRECIO;
                $productoSiesa->save();
                $actualizados++;
            }
        }
        return response()->json(['message' => 'Se han creado '.$creados.' productos y se han actualizado '.$actualizados.' productos.'],200);
    }





    public function sincronize_empleados(Request $request){
        //dd('prueba');
       $empleados = $this->getEmpleados();
        $icount = 0;
        $nosincronizados = 0;
        for($i=0;$i<count($empleados);$i++){
            $emp=ClientesModelTiendas::where('documento','=',$empleados[$i]->Documento)->get();
            $empleado = json_decode(json_encode(ClientesModelTiendas::where('documento','=',$empleados[$i]->Documento)->first()));
            if($empleado == null){
                ClientesModelTiendas::create([
                    'nombres' => $empleados[$i]->Nombres,
            		'apellidos'=> $empleados[$i]->Apellidos,
            		'documento'=> $empleados[$i]->Documento,
            		'direccion'=> $empleados[$i]->Direccion,
            		'telefono'=> $empleados[$i]->Celular,
            		'fecha_nacimiento'=> $empleados[$i]->FechaNacimiento,
            		'correo'=> 'empleados@bless.com',
            		'cargo' => $empleados[$i]->Cargo,
            		'id_zona'=> 1,
            		'id_tienda' => 23,
            		'configuraciones' => 1,
            		'puntos' => 0,
            		'aplica_libranza' => 1,
            		'monto_libranza' => 200000,
            		'cupo' => 200000,
            		'porcentaje_descuento' => 0,
            		'entidad' => 2,
            		'estado_empresa'=>1,
                ]);
                $icount++;
            }else{
                $emp[0]->estado_empresa=1;
               // $emp[0]->monto_libranza=150000;
            	//$emp[0]->cupo = 150000;
                //$emp[0]->monto_libranza=$emp[0]->cupo;
                //$emp[0]->correo = 'empleados@bless.com';
                $emp[0]->save();
                $nosincronizados ++;
            }
        }
        $total = $this->sincronizarCargo($empleados);
       return response()->json(['message' => 'Se sincronizaron '.$icount.' empleados correctamente al sistema de tiendas y '.$nosincronizados.' no han sido sincronizados. y '.$total.' se ha cargado su cargo'],200);

    }
    
    private function sincronizarCargo($empleados){
        $j=0;
        for($i=0;$i<count($empleados);$i++){
            $empleado = ClientesModelTiendas::where('documento','=',$empleados[$i]->Documento)->first();
            if($empleado != null){
                $empleado->cargo = $empleados[$i]->Cargo;
                $empleado->save();
                $j++;
            }
        }
        return $j;
    }

    private function getEmpleados(){
        $Username='bless';
        $Password='orgblessRe$t';
        $client = new \GuzzleHttp\Client(['base_uri' => 'http://45.76.251.153']);
        $response = $client->request('post','/API_GT/api/login/authenticate', [
            'form_params' => [
                'Username' => $Username,
                'Password' => $Password,
            ]
        ]);

        $url="/API_GT/api/orgBless/getEmpleados";
       
        $token=$response->getBody()->getContents();
        $token=str_replace('"','',$token);
     
         $response2 = $client->request('get',$url, [
            'headers' => [ 'Authorization' => 'Bearer '.$token ],
        ]);
        
        $variable=json_decode($response2->getBody()->getContents());
        //DD($variable);
        return $variable->detail;
        
    }
    

    private function getProductos(){
        $Username='bless';
        $Password='orgblessRe$t';
        $client = new \GuzzleHttp\Client(['base_uri' => 'http://45.76.251.153']);
        $response = $client->request('post','/API_GT/api/login/authenticate', [
            'form_params' => [
                'Username' => $Username,
                'Password' => $Password,
            ]
        ]);

        $url="/API_GT/api/orgBless/getInfoReferencia";
       
        $token=$response->getBody()->getContents();
        $token=str_replace('"','',$token);
     
         $response2 = $client->request('get',$url, [
            'headers' => [ 'Authorization' => 'Bearer '.$token ],
        ]);
        
        $variable=json_decode($response2->getBody()->getContents());
        return $variable->detail;
    }

    private function getClientes(){
        $Username='bless';
        $Password='orgblessRe$t';
        $client = new \GuzzleHttp\Client(['base_uri' => 'http://45.76.251.153']);
        $response = $client->request('post','/API_GT/api/login/authenticate', [
            'form_params' => [
                'Username' => $Username,
                'Password' => $Password,
            ]
        ]);

        $url="/API_GT/api/orgBless/getClientes";
       
        $token=$response->getBody()->getContents();
        $token=str_replace('"','',$token);
     
         $response2 = $client->request('get',$url, [
            'headers' => [ 'Authorization' => 'Bearer '.$token ],
        ]);
        
        $variable=json_decode($response2->getBody()->getContents());
        return $variable->detail;
    }
}