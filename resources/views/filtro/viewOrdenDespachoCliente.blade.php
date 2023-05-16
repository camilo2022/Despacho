@extends('layouts.appp')

@push('custom-css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<style>
    .tabledetalles {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }
    .tabledetalles thead th:nth-child(1) {
        width: 120px;
    }
    .tabledetalles thead th:nth-child(2) {
        width: 150px;
    }
    .tabledetalles thead th:nth-child(3) {
        width: 140px;
    }
    .tabledetalles thead th:nth-child(4) {
        width: 120px;
    }
    .tabledetalles thead th:nth-child(5) {
        width: 120px;
    }
    .tabledetalles thead th:nth-child(6) {
        width: 140px;
    }
    .tabledetalles thead th:nth-last-child(1) {
        width: 180px;
    }
    .tabledetalles thead th:nth-last-child(2) {
        width: 140px;
    }
    .tabledetalles thead th:nth-last-child(3) {
        width: 95px;
    }
    .tabledetalles thead th {
        width: 75px;
    }
    table thead{
        background: #333;
        color: white;
        font-weight: bold;
    }
    .title {
        padding-left: 10px;
        font-size: 15px;
        font-weight: bold;
    }
    .data{
        font-size: 15px;
    }
    .row{
        padding: 5px;   
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
    .badge-light {
        color: #212529;
        background-color: #dddddd !important;
        font-weight: bold !important;
        font-size: 12px !important;
    }
    table tbody{
        font-weight: bold;
    }
</style>
@endpush

@section('content')
<div class="container-fluid mt-2">
    <div class="card" style="" >
    <div class="card-header text-center" style="font-family:Century Gothic; background:#333; color:white;">
    INFORMACION DEL CLIENTE
    </div>
        <div class="card-body">
            <div class="row">
                <div class="col-2">
                    <span class="title">CLIENTE: </span>
                </div>
                <div class="col-4">
                    <span class="data">{{$cliente->cliente}}</span>
                </div>
                <div class="col-2">
                    <span class="title">NIT: </span>
                </div>
                <div class="col-4">
                    <span class="data">{{$cliente->nit}}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <span class="title">DIRECCIÓN: </span>
                </div>
                <div class="col-4">
                    <span class="data">{{$cliente->direccion}}</span>
                </div>
                <div class="col-2">
                    <span class="title">SUCURSAL: </span>
                </div>
                <div class="col-4">
                    <span class="data">{{$cliente->sucursal}}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <span class="title">DEPARTAMENTO: </span>
                </div>
                <div class="col-4">
                    <span class="data">{{$cliente->departamento}}</span>
                </div>
                <div class="col-2">
                    <span class="title">CIUDAD: </span>
                </div>
                <div class="col-4">
                    <span class="data">{{$cliente->ciudad}}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt-2">
    <div class="card" style=" font-family:Century Gothic;">
    <div class="card-header text-center" style="background:#333; color:white;">
    DETALLES ORDEN DESPACHO
    </div>
        <div class="card-body">
            <div class="table-responsive">
                <div class="row">
                    <div class="col-12">
                        <table id="tableDetallesOrdenCliente" class="tabledetalles table text-center" style="width:100%">
                            <thead class="">
                                <tr>
                                    <th scope="col">Accion</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Pedido</th>
                                    <th scope="col">Id</th>
                                    <th scope="col">Ref</th>
                                    <th scope="col">T04</th>
                                    <th scope="col">T06</th>
                                    <th scope="col">T08</th>
                                    <th scope="col">T10</th>
                                    <th scope="col">T12</th>
                                    <th scope="col">T14</th>
                                    <th scope="col">T16</th>
                                    <th scope="col">T18</th>
                                    <th scope="col">T20</th>
                                    <th scope="col">T22</th>
                                    <th scope="col">T28</th>
                                    <th scope="col">T30</th>
                                    <th scope="col">T32</th>
                                    <th scope="col">T34</th>
                                    <th scope="col">T36</th>
                                    <th scope="col">T38</th>
                                    <th scope="col">TOTAL</th>
                                    <th scope="col">Despachar</th>
                                    <th scope="col">Estado cartera</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php
                                    $t04=0;$t06=0;$t08=0;$t10=0;$t12=0;$t14=0;
                                    $t16=0;$t18=0;$t20=0;$t22=0;$t28=0;$t30=0;
                                    $t32=0;$t34=0;$t36=0;$t38=0;$total=0;
                                @endphp
                                @foreach($detallesOrdenCliente as $d)
                                @php
                                    if($d->estado_detalle_orden != "CANCELADO" && $d->estado_detalle_orden != "PENDIENTE"){
                                        $t04 += $d->t04;
                                        $t06 += $d->t06;
                                        $t08 += $d->t08;
                                        $t10 += $d->t10;
                                        $t12 += $d->t12;
                                        $t14 += $d->t14;
                                        $t16 += $d->t16;
                                        $t18 += $d->t18;
                                        $t20 += $d->t20;
                                        $t22 += $d->t22;
                                        $t28 += $d->t28;
                                        $t30 += $d->t30;
                                        $t32 += $d->t32;
                                        $t34 += $d->t34;
                                        $t36 += $d->t36;
                                        $t38 += $d->t38;
                                        $total += $d->t04+$d->t06+$d->t08+$d->t10+$d->t12+$d->t14+$d->t16+$d->t18+$d->t20+$d->t22+$d->t28+$d->t30+$d->t32+$d->t34+$d->t36+$d->t38;
                                    }
                                @endphp
                                    <tr>
                                        <td>
                                            @if($d->estado_detalle_orden == "APROBADO")
                                                <a style="cursor:pointer;" href="{{route('filtro.listado.ordenes.clientes.estado.cancelado',[$d->id_dod, $d->id_amarrador])}}" title="CANCELA referencia en pedido y orden"><span class="badge badge-danger">&#x2716</span>
                                                <a style="cursor:pointer;" href="{{route('filtro.listado.ordenes.clientes.estado.pendiente',[$d->id_dod, $d->id_amarrador])}}" title="PENDIENTE por filtrar nuevamente"><span class="badge badge-warning"><i class="fa fa-solid fa-retweet"></i></span>
                                            @elseif($d->estado_detalle_orden == "ALISTAR" && $d->alistamiento == "FINALIZADO")
                                                <a style="cursor:pointer;" href="{{route('filtro.listado.ordenes.clientes.modificar.detalle',[$consecutivo, $d->id_amarrador])}}" title="EDITAR curva a despachar"><span class="badge badge-info"><i class="fa fa-solid fa-marker"></i></span>
                                            @elseif(($cliente->estado_orden == "ALISTANDO" || $cliente->estado_orden == "PREPARANDO") && ($d->estado_detalle_orden == "CANCELADO" || $d->estado_detalle_orden == "PENDIENTE"))
                                                <a style="cursor:pointer;" href="{{route('filtro.listado.ordenes.clientes.estado.aprobado',[$d->id_dod, $d->id_amarrador])}}"><span class="badge badge-success">&#x2714</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($d->estado_detalle_orden == "APROBADO")
                                            <span class="badge badge-pill badge-success badge-outlined">
                                            @elseif($d->estado_detalle_orden == "PENDIENTE")
                                            <span class="badge badge-pill badge-warning badge-outlined">
                                            @elseif($d->estado_detalle_orden == "ALISTAR")
                                            <span class="badge badge-pill badge-secondary badge-outlined">
                                            @elseif($d->estado_detalle_orden == "EMPACAR")
                                            <span class="badge badge-pill badge-light badge-outlined">
                                            @elseif($d->estado_detalle_orden == "FACTURAR")
                                            <span class="badge badge-pill badge-info badge-outlined">
                                            @elseif($d->estado_detalle_orden == "CANCELADO")
                                            <span class="badge badge-pill badge-danger badge-outlined">
                                            @elseif($d->estado_detalle_orden == "DESPACHADO")
                                            <span class="badge badge-pill badge-primary badge-outlined">
                                            @endif
                                            {{ $d->estado_detalle_orden }}
                                            </span>
                                        </td>
                                        <td>{{ $d->fecha }}</td>
                                        <td>{{ $d->id_pedido }}</td>
                                        <td>{{ $d->id_amarrador }}</td>
                                        <td>{{ $d->referencia }}</td>                                        
                                        <td>{{ $d->t04 }}</td>
                                        <td>{{ $d->t06 }}</td>
                                        <td>{{ $d->t08 }}</td>
                                        <td>{{ $d->t10 }}</td>
                                        <td>{{ $d->t12 }}</td>
                                        <td>{{ $d->t14 }}</td>
                                        <td>{{ $d->t16 }}</td>
                                        <td>{{ $d->t18 }}</td>
                                        <td>{{ $d->t20 }}</td>
                                        <td>{{ $d->t22 }}</td>
                                        <td>{{ $d->t28 }}</td>
                                        <td>{{ $d->t30 }}</td>
                                        <td>{{ $d->t32 }}</td>
                                        <td>{{ $d->t34 }}</td>
                                        <td>{{ $d->t36 }}</td>
                                        <td>{{ $d->t38 }}</td>  
                                        <td>{{ $d->t04+$d->t06+$d->t08+$d->t10+$d->t12+$d->t14+$d->t16+$d->t18+$d->t22+$d->t22+$d->t28+$d->t30+$d->t32+$d->t34+$d->t36+$d->t38 }}</td> 
                                        <td>
                                            <span class="badge badge-pill badge-warning">
                                                {{ $d->despachar }}
                                            </span>
                                        </td>
                                        <td>{{ $d->despacho }}</td>                                                                 
                                    </tr>
                                @endforeach
                            </tbody>
                            </tbody>
                            <tfoot>
                            <tr>
                                        <th scope="col" colspan="6">SUMA TOTAL DE CANTIDADES POR TALLA</th>                                     
                                        <td>{{ $t04 }}</td>
                                        <td>{{ $t06 }}</td>
                                        <td>{{ $t08 }}</td>
                                        <td>{{ $t10 }}</td>
                                        <td>{{ $t12 }}</td>
                                        <td>{{ $t14 }}</td>
                                        <td>{{ $t16 }}</td>
                                        <td>{{ $t18 }}</td>
                                        <td>{{ $t20 }}</td>
                                        <td>{{ $t22 }}</td>
                                        <td>{{ $t28 }}</td>
                                        <td>{{ $t30 }}</td>
                                        <td>{{ $t32 }}</td>
                                        <td>{{ $t34 }}</td>
                                        <td>{{ $t36 }}</td>
                                        <td>{{ $t38 }}</td>  
                                        <td>{{ $t04+$t06+$t08+$t10+$t12+$t14+$t16+$t18+$t22+$t22+$t28+$t30+$t32+$t34+$t36+$t38 }}</td> 
                                        <td colspan = "2"></td>                                                                 
                                    </tr>
                            </tfoot>
                        </table>
                    </div>
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
    $(document).ready(function(){
        $(".wrapper").addClass("sidebar_minimize");
    })
</script>    

@endpush