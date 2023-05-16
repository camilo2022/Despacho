<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proceso Facturación.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <style>

     @page contenedor-rotulo{
         size:8.27in 11.69in;
         margin:.5in .5in .5in .5in;
         mso-header-margin:.5in;
         mso-footer-margin:.5in;
         mso-paper-source:0;
        }
     div.contenedor-rotulo {
         page:Section1;
         
         margin:22px;
        }
       
    .tablecurva {
      width: 100%;
      table-layout: fixed;
      width: 100%;
      border-collapse: collapse;
        font-family: Courier, "Lucida Console", monospace !important;
    }
    .tablecurva>td{
        
    }
    .tablecurva>thead{
        width: 100%;
        background-color:#0079fc;
        color:white;
    }
    .tablecurva>thead th:nth-child(1) {
      width: 7%;
    }
    
    .tablecurva>thead th:nth-child(2) {
      width: 10%;
    }
    .tablecurva>thead th:nth-child(3) {
      width: 7%;
    }
    
    .totales{
        background-color:#0079fc;
        color:white;
    }
    
    textarea{
        border:none;
    }

    body{
        background-color: white !important;
    } 
    b{
        font-size:10px;
    }
    </style>
</head>
<body>
    @if(empty($empaques))
        <div class="row">
            <div class="col-2"></div>
            <div class="col-8 mt-4 alert alert-danger" role="alert">
                <h4 class="alert-heading">¡Ooops!</h4>
                <p>Se ha consultado la base de datos y no se han encontrado registros de empacado de la Orden de Despacho.</p>
                <hr>
                <p class="mb-0">Si desea generar un rotulo para varias Ordenes de Despacho dirijase al formulario "Generar Rotulo".</p>
            </div>
            <div class="col-2"></div>
        </div>
    @endif
    <div class="contenedor-rotulo">
        <div class="contenedor-general">
            <div class="contenido1">
                <table class="table table-bordered">
                   <tbody>
                   
                    @for($i=0;$i<count($empaques);$i++)
                      
                      <tr>
                          <td style="width:25%;" class="text-center" rowspan="3">                           
                                <img style="width:130px;" src="{{asset('img/bless.jpeg')}}">                             
                            </td>
                          <td style="width:10%"><b>FECHA:</b></td>
                          <td colspan="3"><b>{{$empaques[$i]->fecha}}</b></td>
                          <td style="width:25%" rowspan="3" class="text-center">
                              <div class="title m-b-md">
                                  {!!QrCode::size(150)->generate($empaques[$i]->cliente.";".$empaques[$i]->nit.";".$empaques[$i]->direccion.";".$empaques[$i]->departamento." - ".$empaques[$i]->ciudad.";".$empaques[$i]->peso." - ".$empaques[$i]->unidades." UNDS;".($i+1)."de".count($empaques).";".$empaques[$i]->consecutivo) !!}
                               </div>
                          </td>
                     </tr>
                     <tr style="width: 50%;">
                         <td style="width: 10%" ><b>FACTURA:</b></td>
                         <td colspan="3"><b>{{$empaques[$i]->facturas}}</b></td>
                     </tr>                   
                     <tr>
                         <td style="width: 10%"><b>EMPAQUES (<b>{{$empaques[$i]->tipo}}</b>):</b></td>
                         <td style="width: 20%" class="text-center"><b>{{$i+1}}</b></td>
                         <td class="text-center" style="width: 10%"><b>DE</b></td>
                         <td style="width: 20%" class="text-center"><b>{{count($empaques)}}</b></td>
                     </tr>
                     <tr>
                        <td style="width:25%;"><b>DESTINATARIO:</b></td>
                        <td colspan="2"><b>{{$empaques[$i]->cliente}}</b></td>
                        <td><b>NIT:</b></td>
                        <td colspan="2"><b>{{$empaques[$i]->nit}}</b></td>
                    </tr>
                    <tr>
                        <td style="width:25%;"><b>DEPARTAMENTO - CIUDAD:</b></td>
                        <td colspan="2"><b>{{$empaques[$i]->departamento." - ".$empaques[$i]->ciudad}}</b></td>
                        <td><b>DIRECCIÓN:</b></td>
                        <td colspan="2"><b>{{$empaques[$i]->direccion}}</b></td>
                    </tr>
                    <tr>
                        <td style="width:25%;"><b>PESO - PRENDAS:</b></td>
                        <td colspan="2"><b>{{$empaques[$i]->peso." - ".$empaques[$i]->unidades." UNDS"}}</b></td>
                        <td><b>DESPACHO:</b></td>
                        <td colspan="2"><b>{{$empaques[$i]->consecutivo}}</b></td>
                    </tr>
                    <tr>
                       <td style="width:25%;"><b>ALISTÓ:</b></td>
                       <td colspan="2"><b>{{$empaques[$i]->alistador}}</b></td>
                       <td colspan="1"><b>EMPACÓ:</b></td>
                       <td colspan="2"><b>{{$empaques[$i]->empacador}}</b></td>
                    </tr>
                    <tr>
                        <td style="width:25%;"><b>FACTURÓ</b></td>
                        <td colspan="2"><b>{{$empaques[$i]->facturador}}</b></td>
                        <td><b>DESPACHÓ:</b></td>
                        <td colspan="2"><b>{{$empaques[$i]->filtrador}}</b></td>
                    </tr>
                    <tr>
                        <td colspan="9" class="text-center"><b>Está caja es propiedad de la Organización Bless SAS, en caso de pérdida favor comunicarse a los siguientes números de contacto <br> Tel:  (7) 5956487  Cel:  3107506812 - 3112520687</b></td>
                    </tr>
                    <tr>
                        <td colspan="9" class="text-center"><b> ORGANIZACIÓN BLESS S.A.S / NIT 900835084-7 / Cll 17 N # 5-65 Zona Industrial / San José de Cúcuta - Norte de Santander</b></td>
                    </tr>
                    <tr>
                        <td style=""colspan="9" rowspan="1" >
                            <div class="mx-auto my-0 text-center" id="dian" > 
                                <img style="width:100%;" src="{{asset('img/dian.jpg')}}"> 
                            </div>
                        </td> 
                    </tr>
                     

                    <tr style="height:70px"></tr>

                @endfor
                      
                    </tbody>
                    
            </div>
            
        </div>
    </div>
</body>
</html>