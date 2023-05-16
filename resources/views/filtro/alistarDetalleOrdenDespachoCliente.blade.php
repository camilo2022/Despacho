@extends('layouts.appp')

@push('custom-css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/semantic.min.css"/>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css"/>
<style>
    .swal-text {
        text-align: center;
    }
    .alert{
        position: relative !important;
        padding: 0.75rem 1.25rem !important;
        margin-bottom: 1rem !important;
        border: 1px solid transparent !important;
        border-radius: 0.25rem !important;
        font-size: 18px;
        font-weight: bold;
    }
    .alert-success {
        background-color: #d4edda !important;
        border-color: #c3e6cb !important;
    }
    .alert-warning {
        background-color: #fff3cd !important;
        border-color: #ffeeba !important;
    }
    .alert-danger {
        background-color: #f8d7da !important;
        border-color: #f5c6cb !important;
    }
    input[type=number]{
        background: transparent;
        max-width: 60px;
        text-align: center;
        border: none;
        font-size: 15px;
        font-weight: bold;
    }
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    .badge-info {
        background: #48ABF7 !important;
        font-weight: bold !important;
        font-size: 12px !important;
    }
    .badge-success {
        background: #40c22f !important;
        font-weight: bold !important;
        font-size: 12px !important;
    }
    .badge-danger {
        background: #dd1010 !important;
        font-weight: bold !important;
        font-size: 12px !important;
    }
    table thead{
        background: #333;
        color: white;
        font-weight: bold;
    }
</style>
@endpush

@section('content')
<div class="container-fluid mt-2">
    <div class="card" style=" font-family:Century Gothic;">
        <div class="card-header text-center" style="background:#333; color:white;">
        ALISTAMIENTO DE ORDEN DE DESPACHO
        </div>
        <div class="card-body">
            <div class="row m-2">
                <div class="col-12">
                    <div class="card w-100">
                        <div class="card-header text-center" style="background:#333; color:white;">
                            INFORMACION ORDEN DE DESPACHO
                        </div>
                        <div class="card-body text-center">
                            <strong><h1><span>CONSECUTIVO {{$consulta_orden_despacho[0]->consecutivo}}</span></h1></strong>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card w-100">
                        <div class="card-header text-center" style="background:#333; color:white;">
                            OPCIONES DE ALISTAMIENTO PARA ORDEN DE DESPACHO
                        </div>
                        <div class="card-body text-center opciones">
                            
                        </div>
                    </div>
                </div>
            </div>
            
           
            <div class="table-responsive">
                @for ($i = 0; $i < count($consulta_detalles); $i++)
                    <div>
                        <button  type="button" class="mb-2 btn w-100 collapsed" style="background-color:#23282e; color:white;" data-toggle="collapse"  data-target="#collapseExample{{ $i }}" aria-expanded="false" aria-controls="#collapseExample{{ $i }}">
                        <b> 
                            @if($consulta_detalles[$i]->sum_a != $consulta_detalles[$i]->sum_d)
                                <span>{{$consulta_detalles[$i]->referencia}}</span> <span class="badge badge-light" id="{{$consulta_detalles[$i]->referencia}}-faltan">{{$consulta_detalles[$i]->sum_a}}</span> de <span class="badge badge-warning" id="{{$consulta_detalles[$i]->referencia}}-total">{{$consulta_detalles[$i]->sum_d}}</span> | <span class="badge badge-danger" id="{{$consulta_detalles[$i]->referencia}}-badge"> Hace falta</span>
                            @else
                                <span>{{$consulta_detalles[$i]->referencia}}</span> <span class="badge badge-light" id="{{$consulta_detalles[$i]->referencia}}-faltan">{{$consulta_detalles[$i]->sum_a}}</span> de <span class="badge badge-warning" id="{{$consulta_detalles[$i]->referencia}}-total">{{$consulta_detalles[$i]->sum_d}}</span> | <span class="badge badge-success" id="{{$consulta_detalles[$i]->referencia}}-badge"> Completado</span>
                            @endif
                        </b>
                        </button>
                        <div class="table-responsive collapse" id="collapseExample{{ $i }}">

                            <div class="col-12">
                                <input id="{{$consulta_detalles[$i]->id_detalle_orden_alistamiento}}" onkeyup="pickingConteoTallas(this,event,'{{$consulta_detalles[$i]->referencia}}')" type="text" class="mb-2 w-100 form-control" style="border: 1px solid black !important;" value="">
                            </div>
                            <table id="{{$consulta_detalles[$i]->referencia}}-table" class="table text-center"> 
                                <thead>
                                    <tr>
                                        <th scope="col">TALLA</th>
                                        <th scope="col">CP</th>
                                        <th scope="col">CD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($consulta_detalles[$i]->t04_d != 0)
                                    <tr class="04">
                                        <th scope="col">T04</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-04-CP" value="{{$consulta_detalles[$i]->t04_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-04-CD" value="{{$consulta_detalles[$i]->t04_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t06_d != 0)
                                    <tr class="06">
                                        <th scope="col">T06</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-06-CP" value="{{$consulta_detalles[$i]->t06_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-06-CD" value="{{$consulta_detalles[$i]->t06_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t08_d != 0)
                                    <tr>
                                        <th scope="col">T08</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-08-CP" value="{{$consulta_detalles[$i]->t08_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-08-CD" value="{{$consulta_detalles[$i]->t08_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t10_d != 0)
                                    <tr>
                                        <th scope="col">T10</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-10-CP" value="{{$consulta_detalles[$i]->t10_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-10-CD" value="{{$consulta_detalles[$i]->t10_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t12_d != 0)
                                    <tr>
                                        <th scope="col">T12</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-12-CP" value="{{$consulta_detalles[$i]->t12_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-12-CD" value="{{$consulta_detalles[$i]->t12_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t14_d != 0)
                                    <tr>
                                        <th scope="col">T14</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-14-CP" value="{{$consulta_detalles[$i]->t14_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-14-CD" value="{{$consulta_detalles[$i]->t14_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t16_d != 0)
                                    <tr>
                                        <th scope="col">T16</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-16-CP" value="{{$consulta_detalles[$i]->t16_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-16-CD" value="{{$consulta_detalles[$i]->t16_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t18_d != 0)
                                    <tr>
                                        <th scope="col">T18</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-18-CP" value="{{$consulta_detalles[$i]->t18_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-18-CD" value="{{$consulta_detalles[$i]->t18_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t20_d != 0)
                                    <tr>
                                        <th scope="col">T20</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-20-CP" value="{{$consulta_detalles[$i]->t20_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-20-CD" value="{{$consulta_detalles[$i]->t20_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t22_d != 0)
                                    <tr>
                                        <th scope="col">T22</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-22-CP" value="{{$consulta_detalles[$i]->t22_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-22-CD" value="{{$consulta_detalles[$i]->t22_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t24_d != 0)
                                    <tr>
                                        <th scope="col">T24</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-24-CP" value="{{$consulta_detalles[$i]->t24_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-24-CD" value="{{$consulta_detalles[$i]->t24_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t26_d != 0)
                                    <tr>
                                        <th scope="col">T26</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-26-CP" value="{{$consulta_detalles[$i]->t26_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-26-CD" value="{{$consulta_detalles[$i]->t26_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t28_d != 0)
                                    <tr>
                                        <th scope="col">T28</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-28-CP" value="{{$consulta_detalles[$i]->t28_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-28-CD" value="{{$consulta_detalles[$i]->t28_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t30_d != 0)
                                    <tr>
                                        <th scope="col">T30</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-30-CP" value="{{$consulta_detalles[$i]->t30_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-30-CD" value="{{$consulta_detalles[$i]->t30_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t32_d != 0)
                                    <tr>
                                        <th scope="col">T32</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-32-CP" value="{{$consulta_detalles[$i]->t32_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-32-CD" value="{{$consulta_detalles[$i]->t32_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t34_d != 0)
                                    <tr>
                                        <th scope="col">T34</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-34-CP" value="{{$consulta_detalles[$i]->t34_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-34-CD" value="{{$consulta_detalles[$i]->t34_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t36_d != 0)
                                    <tr>
                                        <th scope="col">T36</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-36-CP" value="{{$consulta_detalles[$i]->t36_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-36-CD" value="{{$consulta_detalles[$i]->t36_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t38_d != 0)
                                    <tr>
                                        <th scope="col">T38</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-38-CP" value="{{$consulta_detalles[$i]->t38_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-38-CD" value="{{$consulta_detalles[$i]->t38_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->xs_d != 0)
                                    <tr>
                                        <th scope="col">XS</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-XS-CP" value="{{$consulta_detalles[$i]->xs_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-XS-CD" value="{{$consulta_detalles[$i]->xs_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->s_d != 0)
                                    <tr>
                                        <th scope="col">S</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-S-CP" value="{{$consulta_detalles[$i]->s_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-S-CD" value="{{$consulta_detalles[$i]->s_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->m_d != 0)
                                    <tr>
                                        <th scope="col">M</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-M-CP" value="{{$consulta_detalles[$i]->m_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-M-CD" value="{{$consulta_detalles[$i]->m_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->l_d != 0)
                                    <tr>
                                        <th scope="col">L</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-L-CP" value="{{$consulta_detalles[$i]->l_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-L-CD" value="{{$consulta_detalles[$i]->l_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->xl_d != 0)
                                    <tr>
                                        <th scope="col">XL</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-XL-CP" value="{{$consulta_detalles[$i]->xl_a}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-XL-CD" value="{{$consulta_detalles[$i]->xl_d}}" disabled></td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endfor
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
<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<script>
    $(document).ready(function(){
        $(".wrapper").addClass("sidebar_minimize");
        $(".opciones").html(`<div class="col-12">
                    <form id="cancelarAlistamiento" method="POST" action="{{route('filtro.listado.ordenes.clientes.alistar.picking.cancelar',$consulta_orden_despacho[0]->id)}}" onsubmit="cancelarAlistamiento(event)">
                        @csrf
                        <button type="submit" class="mb-2 w-100 btn btn-danger">
                            <div class="table-responsive">Cancelar Alistamiento</div>
                        </button>
                    </form>
                </div>
                <div class="col-12">
                    <form id="revisarAlistamiento" method="POST" action="{{route('filtro.listado.ordenes.clientes.alistar.picking.revisar',$consulta_orden_despacho[0]->id)}}" onsubmit="revisarAlistamiento(event)">
                        @csrf
                        <input type="hidden" class="form-control" id="revisar" name="revisar" value="">
                        <button type="submit" class="mb-2 w-100 btn btn-warning">
                            <div class="table-responsive">Revisar Alistamiento</div>
                        </button>
                    </form>
                </div>
                @if(empty($od))
                <div class="col-12">
                    <form id="aceptarAlistamiento" method="POST" action="{{route('filtro.listado.ordenes.clientes.alistar.picking.aceptar',$consulta_orden_despacho[0]->id)}}" onsubmit="aceptarAlistamiento(event)">
                        @csrf
                        <button type="submit" class="mb-2 w-100 btn btn-success">
                            <div class="table-responsive">Aceptar Alistamiento</div>
                        </button>
                    </form>
                </div>@endif`);
    })
    function getAbsolutePath() {
        var loc = window.location;
        var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);
        return loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));
    }

    function pickingConteoTallas(input,e,refe){
        if(e.which == 13){
            let URLdominio = getAbsolutePath();
            let consulta_orden_despacho = {!! json_encode($consulta_orden_despacho) !!};
            let consulta_detalles = {!! json_encode($consulta_detalles) !!};
            let id_orden_despacho = consulta_orden_despacho[0].id;
            let id_orden_alistamiento = consulta_detalles[0].orden_alistamiento;
            let url = URLdominio + id_orden_despacho +"/add";
            let token = $("meta[name='csrf-token']").attr("content");
            input.value.toUpperCase();
            let arrayReferencia = input.value.split("-");
            let referencia = "";
            let talla = "";
            let id_detalle_orden_alistamiento = input.id;
            let estado = "FALTAN";
            switch(arrayReferencia.length){
                case 2:
                    referencia = arrayReferencia[0];
                    talla = arrayReferencia[1];
                    break;
                case 3:
                    referencia = arrayReferencia[0];
                    talla = arrayReferencia[1];
                    break;
                case 4:
                    referencia = arrayReferencia[0]+"-"+arrayReferencia[1];
                    talla = arrayReferencia[2];
                    break;
                default:
                    referencia = arrayReferencia[0];
                    talla = "00";
                    break;
            }
            referencia = referencia.toUpperCase();
            refe = refe.toUpperCase();
            if(referencia == refe){
            let faltan = document.getElementById(referencia+'-faltan').innerHTML;
            let total = document.getElementById(referencia+'-total').innerHTML;
            let cp = $('#'+referencia+'-'+talla+'-CP').val();
            let cd = $('#'+referencia+'-'+talla+'-CD').val();
            
                if(cp == undefined || cp == null || cp == ""){
                    alertify.error('¡La talla no existe!');
                }else if(cp == cd){
                    alertify.warning('Picking completo en T'+talla);
                }else{
                    $('#'+referencia+'-'+talla+'-CP').val(parseInt(cp)+1);
                    faltan = document.getElementById(referencia+'-faltan').innerHTML = parseInt(faltan)+1
                    estado = "FALTAN";
                    if(parseInt(faltan) == parseInt(total)){
                        const badgeElement = document.getElementById(referencia + "-badge");
                        badgeElement.classList.remove("badge-danger");
                        badgeElement.classList.add("badge-success");
                        document.getElementById(referencia+'-badge').innerHTML = "Completado"
                        estado = "COMPLETADO";
                    }
                    if(talla != "xs" && talla != "XS" && talla != "s" && talla != "S" && talla != "m" && talla != "M"&& talla != "l" && talla != "L" && talla != "xl" && talla != "XL"){
                        talla = "t"+talla;
                    }
                    let dataSend = {
                        id_orden_alistamiento:id_orden_alistamiento,
                        id_detalle_orden_alistamiento:id_detalle_orden_alistamiento,
                        talla: talla,
                        estado:estado,
                        _token:token
                    };
                    $.ajax({
                    url: url,
                    type: 'POST',
                    data: dataSend,
                    dataType: 'json',
                        success: function(data){
                            alertify.success(data["msg"]);
                            if(data["estado"] == 1){
                                $(".opciones").html(`<div class="col-12">
                                    <form id="cancelarAlistamiento" method="POST" action="{{route('filtro.listado.ordenes.clientes.alistar.picking.cancelar',$consulta_orden_despacho[0]->id)}}" onsubmit="cancelarAlistamiento(event)">
                                        @csrf
                                        <button type="submit" class="mb-2 w-100 btn btn-danger">
                                            <div class="table-responsive">Cancelar Alistamiento</div>
                                        </button>
                                    </form>
                                </div>
                                <div class="col-12">
                                    <form id="revisarAlistamiento" method="POST" action="{{route('filtro.listado.ordenes.clientes.alistar.picking.revisar',$consulta_orden_despacho[0]->id)}}" onsubmit="revisarAlistamiento(event)">
                                        @csrf
                                        <input type="hidden" class="form-control" id="revisar" name="revisar" value="">
                                        <button type="submit" class="mb-2 w-100 btn btn-warning">
                                            <div class="table-responsive">Revisar Alistamiento</div>
                                        </button>
                                    </form>
                                </div>
                                <div class="col-12">
                                    <form id="aceptarAlistamiento" method="POST" action="{{route('filtro.listado.ordenes.clientes.alistar.picking.aceptar',$consulta_orden_despacho[0]->id)}}" onsubmit="aceptarAlistamiento(event)">
                                        @csrf
                                        <button type="submit" class="mb-2 w-100 btn btn-success">
                                            <div class="table-responsive">Aceptar Alistamiento</div>
                                        </button>
                                    </form>
                                </div>`);
                            }
                        },
                    })
                }
            }else{
                alertify.error('¡REFERENCIA INCORRECTA!');
                alertify.warning('REFERENCIA-TALLA');
                alertify.warning('REFERENCIA-TALLA-COLOR');
                alertify.warning('REFERENCIA-TONO-TALLA-COLOR');
            }
            input.value = "";
        }
    }

    function cancelarAlistamiento(event){
        event.preventDefault(); 
        swal({
            title: "¿Está seguro?",
            text: "¡Se cancelará el alistamiento! \n Se eliminará los regitros de esta orden de alistamiento.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                document.getElementById('cancelarAlistamiento').submit();
            } 
        });
    }

    function revisarAlistamiento(event){
        event.preventDefault();
        const select = document.createElement('select');
        select.classList = 'form-control';
        const option0 = document.createElement('option');
        option0.value = '0';
        option0.text = 'SELECCIONE';
        const option1 = document.createElement('option');
        option1.value = '1';
        option1.text = 'MANDAR A REVISION';
        const option2 = document.createElement('option');
        option2.value = '2';
        option2.text = 'EMPACAR DE UNA VEZ CON LAS PRENDAS ALISTADAS';

        select.appendChild(option0);
        select.appendChild(option1);
        select.appendChild(option2);
        swal({
            title: "¿Está seguro?",
            text: "¡Elija que desea hacer!",
            content: select,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                if(select.value == 0){
                    swal({
                        title: "¡Ooops!",
                        text: "¡Seleccione una opcion valida!",
                        icon: "error"
                    })
                }
                if(select.value == 1){
                    $("#revisar").val(select.value);
                    document.getElementById('revisarAlistamiento').submit();
                }
                if(select.value == 2){
                    $("#revisar").val(select.value);
                    document.getElementById('revisarAlistamiento').submit();
                }
            }
        });
    }

    function aceptarAlistamiento(event){
        event.preventDefault(); 
        const select = document.createElement('select');
        select.classList = 'form-control';
        const option0 = document.createElement('option');
        option0.value = '0';
        option0.text = 'SELECCIONE';
        const option1 = document.createElement('option');
        option1.value = '1';
        option1.text = 'AGREGAR UNIDADES EXTRAS A ALGUNAS TALLAS';
        const option2 = document.createElement('option');
        option2.value = '2';
        option2.text = 'EMPACAR DE UNA VEZ';

        select.appendChild(option0);
        select.appendChild(option1);
        select.appendChild(option2);
        swal({
            title: "¿Está seguro?",
            text: "¡Elija que desea hacer!",
            content: select,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                if(select.value == 0){
                    swal({
                        title: "¡Ooops!",
                        text: "¡Seleccione una opcion valida!",
                        icon: "error"
                    })
                }
                if(select.value == 1){
                    document.getElementById('aceptarAlistamiento').submit();
                }
                if(select.value == 2){
                    setTimeout(() => {
                        let id = {!! $id !!};
                        window.location.href = ('{{ route("filtro.listado.ordenes.clientes.empacar.picking",["id" => ":id"]) }}').replace(':id', id);
                    }, 1000);
                }
            }
        });
    }
</script>    

@endpush