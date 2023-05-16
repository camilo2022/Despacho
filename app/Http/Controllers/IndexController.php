<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;
use DNS1D;
use App\Imports\ImportExcelInfo;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ReprogramacionLorena;
use DNS2D;
class IndexController extends Controller
{
 
    public function getListadoReprogramaciones(){
         $reprogrma = ReprogramacionLorena::get();
         return response()->json(['data' => $reprogrma]);
    }
 
    public function phpINFO(Request $request){
        return phpinfo();
    }
    
    public function lecturaArchivoLorena(Request $request){
        $file = $request->file;
        $orden = new ImportExcelInfo();
        $ts=Excel::import($orden,$file);
        $items=json_decode(json_encode($orden->sheetData[0]));
        $cargados = 0;
       
        for($i=0;$i<count($items);$i++){
            $reprogrma = ReprogramacionLorena::create([
                   'reprogramacion' => $items[$i]->Reprogramacion,
                   'categoria' => $items[$i]->Categoria,
                   'descripcionref' => $items[$i]->Descripcionref,
                   'mes' => $items[$i]->Mes,
                   'undxmes' => $items[$i]->Undxmes,
                   'undxref' => $items[$i]->Undxref,
                   'nref' => $items[$i]->Nref,
                   'user_id'
            ]);
            $cargados++;
        }
        return response()->json(['message' => 'Se han cargado '.$cargados.' reprogramaciones de lorena.']);
    }
 
    public function index(){
        return view('codbarras.index');
    }

    public function consultaReferencia($referencia){
        $respuesta=$this->buscarEnApi($referencia);
        $respuesta=$respuesta->detail;
        if($respuesta!=null){
                $respuesta=json_encode($respuesta);
                $respuesta=json_decode($respuesta,TRUE);
                $colores=$this->getColorsName($referencia);
                return response()->json($colores);
             }else{
                return response()->setStatusCode(406);
            }
    }

    public function getColorsName($referencia){
        $respuesta=$this->getInformacionReferencia($referencia)->detail;
        $devolvera = [];
        for($i=0;$i<count($respuesta);$i++){
            $arraData=json_encode($respuesta[$i]);
            $arraData=json_decode($arraData);
            $color = [
                "id" => $arraData->CodigoColor,
                'name' => $arraData->Color
            ];
            array_push($devolvera,$color);
        }
        return $devolvera;
    }

    public function consultaRefColor(Request $request){
        $referencia=$request->all()['referencia'];
        $color=$request->all()['color'];
        $variable=$this->buscarEnApi($referencia,$color);

        $respuesta=json_encode($variable->detail);
        $respuesta=json_decode($respuesta,TRUE);


        if($variable->detail==null){
            return view('codbarras.index')->withErrors(['No hay codbarras ni tallas asociadas a la combinacion.']);
        }else{
            $array_final=[];
            $ff=[];
            for ($i = 0; $i< count($variable->detail) ; $i++){
                $array_final[]=$variable->detail[$i]->DetalleExt1;
            }
            $array_final=array_unique($array_final);
            $array_final=array_values($array_final);
            $array_final2=[];
        
            foreach($array_final as $clave => $valor){
                array_push($array_final2,[
                        'talla' => $valor,
                        'barras' =>[]
                    ]);
            }
            for ($i = 0; $i< count($variable->detail) ; $i++){
                $clave = array_search($variable->detail[$i]->DetalleExt1, $array_final);
                $vari= $variable->detail[$i]->CodBarras;
                
                    if($array_final2[$clave]['talla'] == $variable->detail[$i]->DetalleExt1){
    
                      array_push($array_final2[$clave]['barras'],$vari);
                   
                    }
                
            }
            $cantidad=count($array_final2);
           return response()->json($array_final2);
            //return response()->json(view('cod_barras_siesa.tabla', compact('array_final2'))->render());
        }
    }

    private function buscarEnApi($referencia,$color=null){
        $Username='bless';
        $Password='orgblessRe$t';
        $client = new \GuzzleHttp\Client(['base_uri' => 'http://45.76.251.153']);
        $response = $client->request('post','/API_GT/api/login/authenticate', [
            'form_params' => [
                'Username' => $Username,
                'Password' => $Password,
            ]
        ]);

        if($color==null){
            $url="/API_GT/api/orgBless/getRefCBarras?Referencia={$referencia}";
        }else{
            $url="/API_GT/api/orgBless/getRefCBarras?Referencia={$referencia}&extension2={$color}";
        }

        //$referencia=intval($referencia);
        $token=$response->getBody()->getContents();
        $token=str_replace('"','',$token);
     
         $response2 = $client->request('get',$url, [
            'headers' => [ 'Authorization' => 'Bearer '.$token ],
        ]);
        
        $variable=json_decode($response2->getBody()->getContents());
        return $variable;
    }

    private function getInformacionReferencia($referencia){
        $Username='bless';
        $Password='orgblessRe$t';
        $client = new \GuzzleHttp\Client(['base_uri' => 'http://45.76.251.153']);
        $response = $client->request('post','/API_GT/api/login/authenticate', [
            'form_params' => [
                'Username' => $Username,
                'Password' => $Password,
            ]
        ]);
        $url = 'http://45.76.251.153/API_GT/api/orgBless/getInfoReferencia?Referencia='.$referencia;

        //$referencia=intval($referencia);
        $token=$response->getBody()->getContents();
        $token=str_replace('"','',$token);
     
         $response2 = $client->request('get',$url, [
            'headers' => [ 'Authorization' => 'Bearer '.$token ],
        ]);
        
        $variable=json_decode($response2->getBody()->getContents());
        return $variable;
    }

    
    


    

}
