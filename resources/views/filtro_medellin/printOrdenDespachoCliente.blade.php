@extends('layouts.appp')

@push('custom-css')
<style>
    table {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }
    thead{
        background-color:#333;
        color:white;
    }
    thead th:nth-child(1) {
        width: 20%;
    }
    thead th:nth-child(2) {
        width: 10%;
    }
    thead th:nth-child(3) {
        width: 13%;
    }
    thead th:nth-child(4) {
        width: 12%;
    }
    thead th {
        width: 5%;
    }
    label{
        font-size: 10px !important;
    }
    .totales{
        background-color:#333;
        color:white;
    }
    table, td {
        border: 1px solid #9b9b9b;
        border-collapse: collapse;
    }
    .datos_cliente{
        font-size: 13px !important;
        width:auto; 
        display:flex; 
        flex-direction:column;
    }
    .imagen_bless{
        width:auto; 
        display:flex; 
        flex-direction:column; 
        align-items:center; 
    }
    .logo_bless{
        width: 160px; 
        height: 160px;
    }
    .orden{
        font-size: 18px;
        padding: 15px;
        font-weight: bold;
    }
    .consecutivo{
        width:100%; 
        height:120px; 
        border: #333 3px solid;
        color: #333;
        font-size:4vw; 
        text-align:center; 
        align-items:center; 
        display:grid;
    }
    @page {  
        size: 25cm 35.7cm; 
        margin: 5mm 5mm 5mm 5mm;
    }
        
</style>
@endpush
@section('content')

<div class="container-fluid mt-2">
    <div class="card" style=" font-family:Century Gothic;">
        <div class="card-header text-center" style="background-color: #333; color:white; font-weigth:bold;">
        IMPRIMIR ORDEN DE DESPACHO PARA {{$cliente->cliente}}
        </div>
            <div class="card-body">
                <div class="table-responsive text-center">
                    <input style="width:25%;" type="button" class="btn btn-dark" onclick="printDiv('areaImprimir')" value="Imprimir" />
                </div>
            </div>
    </div>
</div>

<div class="card m-2" id="areaImprimir">
    <div id="apper">
        <div class="container-fluid tamano">
            <div class="card-body">
                <div class="row">

                    <div class="col-md-6 datos_cliente justify-content-center align-self-center">
                        <div class="row">
                            <div class="col-md-3 datos_cliente"> <label>NIT:</label> </div>
                            <div class="col-md-9 datos_cliente"> <b>{{$cliente->nit}}</b> </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 datos_cliente"> <label>CLIENTE:</label> </div>
                            <div class="col-md-9 datos_cliente"> <b>{{$cliente->cliente}}</b> </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 datos_cliente"> <label>ZONA:</label> </div>
                            <div class="col-md-9 datos_cliente"> <b>{{$cliente->zona}}</b> </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 datos_cliente"> <label>CIUDAD:</label> </div>
                            <div class="col-md-9 datos_cliente"> <b>{{$cliente->ciudad}}</b> </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 datos_cliente"> <label>DIRECCION:</label> </div>
                            <div class="col-md-9 datos_cliente"> <b>{{$cliente->direccion}}</b> </div> 
                        </div>
                        <div class="row">
                            <div class="col-md-3 datos_cliente"> <label>SUCURSAL:</label> </div>
                            <div class="col-md-9 datos_cliente"> <b>{{$cliente->sucursal}}-</b> </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 datos_cliente"> <label>FECHA:</label> </div>
                            <div class="col-md-9 datos_cliente"> <b>{{$cliente->fecha}}</b> </div>
                        </div>  
                        <div class="row">
                            <div class="col-md-3 datos_cliente"> <label>OBSERVACIONES:</label> </div>
                            <div class="col-md-9 datos_cliente"> <b>{{$cliente->obs." - ".$cliente->obscartera}}</b> </div>
                        </div>                    
                    </div>

                    <div class="col-md-2 text-center imagen_bless justify-content-center align-self-center">
                        <img class="logo_bless" src="https://media-exp1.licdn.com/dms/image/C4E0BAQFnW0TdfpMO8w/company-logo_200_200/0/1533585316799?e=2159024400&v=beta&t=L_6VODgFex5zDVs1kvw1dvR9_g-W7j-KfEvVDpkWivo">
                    </div>
                    <div class="col-md-4 text-center justify-content-center align-self-center">
                        <div class="orden">ORDEN DE DESPACHO Nº</div>
                        <div class="consecutivo">{{$cliente->consecutivo}}</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="tablecurva text-center" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Vendedor</th>
                                <th>Pedido</th>
                                <th>Amarrador</th>
                                <th>Referencia</th>
                                <th>T04</th>
                                <th>T06</th>
                                <th>T08</th>
                                <th>T10</th>
                                <th>T12</th>
                                <th>T14</th>
                                <th>T16</th>
                                <th>T18</th>
                                <th>T20</th>
                                <th>T22</th>
                                <th>T28</th>
                                <th>T30</th>
                                <th>T32</th>
                                <th>T34</th>
                                <th>T36</th>
                                <th>T38</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $t04=0;$t06=0;$t08=0;$t10=0;$t12=0;$t14=0;
                                $t16=0;$t18=0;$t20=0;$t22=0;$t28=0;$t30=0;
                                $t32=0;$t34=0;$t36=0;$t38=0;$total=0;
                            @endphp
                            @foreach($detallesOrdenCliente  as $detalle)
                            @php
                                $v = $array = explode(" ", $detalle->vendedor);
                                $detalle->vendedor = count($v)>2 ? $v[2]." ".$v[0] : $v[1]." ".$v[0];
                                $t04 += $detalle->t04;
                                $t06 += $detalle->t06;
                                $t08 += $detalle->t08;
                                $t10 += $detalle->t10;
                                $t12 += $detalle->t12;
                                $t14 += $detalle->t14;
                                $t16 += $detalle->t16;
                                $t18 += $detalle->t18;
                                $t20 += $detalle->t20;
                                $t22 += $detalle->t22;
                                $t28 += $detalle->t28;
                                $t30 += $detalle->t30;
                                $t32 += $detalle->t32;
                                $t34 += $detalle->t34;
                                $t36 += $detalle->t36;
                                $t38 += $detalle->t38;
                                $total += $detalle->t04+$detalle->t06+$detalle->t08+$detalle->t10+$detalle->t12+$detalle->t14+$detalle->t16+$detalle->t18+$detalle->t20+$detalle->t22+$detalle->t28+$detalle->t30+$detalle->t32+$detalle->t34+$detalle->t36+$detalle->t38;
                            @endphp
                            <tr>
                                <td><b style="font-size: 8px !important;">{{$detalle->vendedor}}</b></td>
                                <td><b>{{$detalle->id_pedido}}</b></td>
                                <td><b>{{$detalle->id_amarrador}}</b></td>
                                <td><b>{{$detalle->referencia}}</b></td>
                                <td>{{$detalle->t04}}</td>
                                <td>{{$detalle->t06}}</td>
                                <td>{{$detalle->t08}}</td>
                                <td>{{$detalle->t10}}</td>
                                <td>{{$detalle->t12}}</td>
                                <td>{{$detalle->t14}}</td>
                                <td>{{$detalle->t16}}</td>
                                <td>{{$detalle->t18}}</td>
                                <td>{{$detalle->t20}}</td>
                                <td>{{$detalle->t22}}</td>
                                <td>{{$detalle->t28}}</td>
                                <td>{{$detalle->t30}}</td>
                                <td>{{$detalle->t32}}</td>
                                <td>{{$detalle->t34}}</td>
                                <td>{{$detalle->t36}}</td>
                                <td>{{$detalle->t38}}</td>
                                <td><b>{{$detalle->t04+$detalle->t06+$detalle->t08+$detalle->t10+$detalle->t12+$detalle->t14+$detalle->t16+$detalle->t18+$detalle->t20+$detalle->t22+$detalle->t28+$detalle->t30+$detalle->t32+$detalle->t34+$detalle->t36+$detalle->t38}}</b></td>
                            </tr>
                            @endforeach
                            <tr class="totales">
                                <th colspan="4"></th>
                                <th><b>{{$t04}}</b></th>
                                <th><b>{{$t06}}</b></th>
                                <th><b>{{$t08}}</b></th>
                                <th><b>{{$t10}}</b></th>
                                <th><b>{{$t12}}</b></th>
                                <th><b>{{$t14}}</b></th>
                                <th><b>{{$t16}}</b></th>
                                <th><b>{{$t18}}</b></th>
                                <th><b>{{$t20}}</b></th>
                                <th><b>{{$t22}}</b></th>
                                <th><b>{{$t28}}</b></th>
                                <th><b>{{$t30}}</b></th>
                                <th><b>{{$t32}}</b></th>
                                <th><b>{{$t34}}</b></th>
                                <th><b>{{$t36}}</b></th>
                                <th><b>{{$t38}}</b></th>
                                <th><b>{{$total}}</b></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>  
    </div>
</div>
@endsection

@push('scripts-custom')
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/v/bs4-4.1.1/dt-1.10.18/datatables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js" type="text/javascript"></script>
<script>
    function printDiv(nombreDiv) {
     var contenido= document.getElementById(nombreDiv).innerHTML;
     var contenidoOriginal= document.body.innerHTML;
     var css = ' <html><head>' 
      + $("head").html() 
      + ' <style>body{background-color:white !important;} @page { size: 27cm 18cm;margin: 0cm 0cm 0cm 0cm ; overflow: auto;} .tamano{width: 27cm;height: 18cm;}</style></head>'
        head = document.head || document.getElementsByTagName('head')[0],
        style = document.createElement('style');

    style.type = 'text/css';
    style.media = 'print';

    if (style.styleSheet){
    style.styleSheet.cssText = css;
    } else {
    style.appendChild(document.createTextNode(css));
    }

    head.appendChild(style);
     
     document.body.innerHTML = contenido;
        
     window.print();

     document.body.innerHTML = contenidoOriginal;
    }
    $(document).ready(function(){
        $(".wrapper").addClass("sidebar_minimize");
    })
</script>    

@endpush