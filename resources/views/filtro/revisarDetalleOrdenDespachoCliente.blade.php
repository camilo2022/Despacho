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
        width: 10%;
    }
    thead th:nth-child(2) {
        width: 10%;
    }
    thead th:nth-child(3) {
        width: 10%;
    }
    thead th {
        width: 5%;
    }
    label{
        font-size: 12px !important;
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
</style>
@endpush

@section('content')
<div class="container-fluid mt-2">
    <div class="card" style=" font-family:Century Gothic;">
        <div class="card-header text-center" style="background:#333; color:white;">
        REVISION ALISTAMIENTO DE ORDEN DE DESPACHO {{$consecutivo}}
        </div>
        <div class="card-body">
            <div class="row m-2">
                <div class="col-12">
                    <div class="card w-100">
                        <div class="card-body">
                            <div class="card-header text-center" style="background:#333; color:white;">
                                INFORMACION DEL CLIENTE 
                            </div>
                            <div class="row m-2">
                                <div class="col-sm-6">
                                    <form id="alistarNuevamente" method="POST" action="{{ route('filtro.listado.ordenes.clientes.revisar.cancelar',[$consecutivo,$orden_despacho]) }}" onsubmit="alistarNuevamente(event)">
                                        @csrf
                                        <button type="submit" class="mb-2 w-100 btn btn-danger">Alistar Nuevamente</button>
                                    </form>
                                </div>
                                <div class="col-sm-6">
                                    <form id="aprobarAlistamiento" method="POST" action="{{ route('filtro.listado.ordenes.clientes.revisar.aprobar',[$consecutivo,$orden_despacho]) }}" onsubmit="aprobarAlistamiento(event)">
                                        @csrf
                                        <button type="submit" class="mb-2 w-100 btn btn-success">Aprobar Alistamiento</button>
                                    </form>
                                </div>
                            </div>

                            <div class="row m-2">
                                <div class="col-md-12 datos_cliente justify-content-center align-self-center">
                                    <div class="row m-2">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-3 datos_cliente"> 
                                                    <label>NIT:</label> 
                                                </div>
                                                <div class="col-md-9 datos_cliente"> 
                                                    <b>{{$consulta->nit}}</b> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-3 datos_cliente"> 
                                                    <label>CLIENTE:</label> 
                                                </div>
                                                <div class="col-md-9 datos_cliente"> 
                                                    <b>{{$consulta->cliente}}</b> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>                 
                                
                                    <div class="row m-2">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-3 datos_cliente"> 
                                                    <label>DEPARTAMENTO:</label> 
                                                </div>
                                                <div class="col-md-9 datos_cliente"> 
                                                    <b>{{$consulta->departamento}}</b> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-3 datos_cliente"> 
                                                    <label>CIUDAD:</label> 
                                                </div>
                                                <div class="col-md-9 datos_cliente"> 
                                                    <b>{{$consulta->ciudad}}</b> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row m-2">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-3 datos_cliente"> 
                                                    <label>DIRECCION:</label> 
                                                </div>
                                                <div class="col-md-9 datos_cliente"> 
                                                    <b>{{$consulta->direccion}}</b> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-3 datos_cliente"> 
                                                    <label>SUCURSAL:</label> 
                                                </div>
                                                <div class="col-md-9 datos_cliente"> 
                                                    <b>{{$consulta->sucursal}}</b> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row m-2">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-3 datos_cliente"> 
                                                    <label>FECHA:</label> 
                                                </div>
                                                <div class="col-md-9 datos_cliente"> 
                                                    <b>{{$consulta->fecha}}</b> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-3 datos_cliente"> 
                                                    <label>OBSERVACIONES:</label> 
                                                </div>
                                                <div class="col-md-9 datos_cliente"> 
                                                    <b>{{"OBS: ".$consulta->observaciones." - OBS CARTERA:".$consulta->obs_cartera}}</b> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-header text-center" style="background:#333; color:white;">
                                COMPARACION DE CURVA FILTRADA VS CURVA ALISTADA
                            </div>
                            <div class="row m-2">
                                <button class="btn btn-primary mb-2 w-100" type="button" data-toggle="collapse" data-target="#collapseCurvaDespacho" aria-expanded="false" aria-controls="collapseExample">
                                    Curva Despacho <span class="badge badge-success"></span>
                                </button>
                                <div class="collapse" id="collapseCurvaDespacho">
                                    <div class="card card-body">
                                        <table class="tabledespacho text-center">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Pedido</th>
                                                    <th scope="col">Amarrador</th>
                                                    <th scope="col">Referencia</th>
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
                                                    <th scope="col">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $t04=0;$t06=0;$t08=0;$t10=0;$t12=0;$t14=0;
                                                    $t16=0;$t18=0;$t20=0;$t22=0;$t28=0;$t30=0;
                                                    $t32=0;$t34=0;$t36=0;$t38=0;$total=0;
                                                @endphp
                                                @foreach($consulta_detalles as $d)
                                                @php
                                                    $t04+=$d->t04_a; $t06+=$d->t06_a; $t08+=$d->t08_a;
                                                    $t10+=$d->t10_a; $t12+=$d->t12_a; $t14+=$d->t14_a;
                                                    $t16+=$d->t16_a; $t18+=$d->t18_a; $t20+=$d->t20_a;
                                                    $t22+=$d->t22_a; $t28+=$d->t28_a; $t30+=$d->t30_a;
                                                    $t32+=$d->t32_a; $t34+=$d->t34_a; $t36+=$d->t36_a;
                                                    $t38+=$d->t38_a; $total+=$d->sum_a;
                                                @endphp
                                                <tr>
                                                    <td>{{ $d->id_pedido }}</td>
                                                    <td>{{ $d->id_amarrador }}</td>
                                                    <td>{{ $d->referencia }}</td>
                                                    <td>{{ $d->t04_d }}</td>
                                                    <td>{{ $d->t06_d }}</td>
                                                    <td>{{ $d->t08_d }}</td>
                                                    <td>{{ $d->t10_d }}</td>
                                                    <td>{{ $d->t12_d }}</td>
                                                    <td>{{ $d->t14_d }}</td>
                                                    <td>{{ $d->t16_d }}</td>
                                                    <td>{{ $d->t18_d }}</td>
                                                    <td>{{ $d->t20_d }}</td>
                                                    <td>{{ $d->t22_d }}</td>
                                                    <td>{{ $d->t24_d }}</td>
                                                    <td>{{ $d->t30_d }}</td>
                                                    <td>{{ $d->t32_d }}</td>
                                                    <td>{{ $d->t34_d }}</td>
                                                    <td>{{ $d->t36_d }}</td>
                                                    <td>{{ $d->t38_d }}</td>
                                                    <td>{{ $d->sum_d }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr style="background:#333; color:white;">
                                                    <td colspan="3"></td>
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
                                                    <td>{{ $total }}</td> 
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row m-2">
                                <button class="btn btn-danger w-100" type="button" data-toggle="collapse" data-target="#collapseCurvaAlistamiento" aria-expanded="false" aria-controls="collapseExample">
                                    Curva Alistada <span class="badge badge-success"></span>
                                </button>
                                <div class="collapse" id="collapseCurvaAlistamiento">
                                    <div class="card card-body">
                                        <table class="tablealistado text-center">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Pedido</th>
                                                    <th scope="col">Amarrador</th>
                                                    <th scope="col">Referencia</th>
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
                                                    <th scope="col">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $t04=0;$t06=0;$t08=0;$t10=0;$t12=0;$t14=0;
                                                    $t16=0;$t18=0;$t20=0;$t22=0;$t28=0;$t30=0;
                                                    $t32=0;$t34=0;$t36=0;$t38=0;$total=0;
                                                @endphp
                                                @foreach($consulta_detalles as $d)
                                                @php
                                                    $t04+=$d->t04_a; $t06+=$d->t06_a; $t08+=$d->t08_a;
                                                    $t10+=$d->t10_a; $t12+=$d->t12_a; $t14+=$d->t14_a;
                                                    $t16+=$d->t16_a; $t18+=$d->t18_a; $t20+=$d->t20_a;
                                                    $t22+=$d->t22_a; $t28+=$d->t28_a; $t30+=$d->t30_a;
                                                    $t32+=$d->t32_a; $t34+=$d->t34_a; $t36+=$d->t36_a;
                                                    $t38+=$d->t38_a; $total+=$d->sum_a;
                                                @endphp
                                                <tr>
                                                    <td>{{ $d->id_pedido }}</td>
                                                    <td>{{ $d->id_amarrador }}</td>
                                                    <td>{{ $d->referencia }}</td>
                                                    <td>
                                                        @if($d->t04_a == $d->t04_d)
                                                            <span class="badge badge-success">{{ $d->t04_a }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $d->t04_a }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($d->t06_a == $d->t06_d)
                                                            <span class="badge badge-success">{{ $d->t06_a }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $d->t06_a }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($d->t08_a == $d->t08_d)
                                                            <span class="badge badge-success">{{ $d->t08_a }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $d->t08_a }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($d->t10_a == $d->t10_d)
                                                            <span class="badge badge-success">{{ $d->t10_a }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $d->t10_a }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($d->t12_a == $d->t12_d)
                                                            <span class="badge badge-success">{{ $d->t12_a }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $d->t12_a }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($d->t14_a == $d->t14_d)
                                                            <span class="badge badge-success">{{ $d->t14_a }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $d->t14_a }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($d->t16_a == $d->t16_d)
                                                            <span class="badge badge-success">{{ $d->t16_a }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $d->t16_a }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($d->t18_a == $d->t18_d)
                                                            <span class="badge badge-success">{{ $d->t18_a }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $d->t18_a }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($d->t20_a == $d->t20_d)
                                                            <span class="badge badge-success">{{ $d->t20_a }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $d->t20_a }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($d->t22_a == $d->t22_d)
                                                            <span class="badge badge-success">{{ $d->t22_a }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $d->t22_a }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($d->t24_a == $d->t24_d)
                                                            <span class="badge badge-success">{{ $d->t24_a }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $d->t24_a }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($d->t30_a == $d->t30_d)
                                                            <span class="badge badge-success">{{ $d->t30_a }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $d->t30_a }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($d->t32_a == $d->t32_d)
                                                            <span class="badge badge-success">{{ $d->t32_a }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $d->t32_a }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($d->t34_a == $d->t34_d)
                                                            <span class="badge badge-success">{{ $d->t34_a }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $d->t34_a }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($d->t36_a == $d->t36_d)
                                                            <span class="badge badge-success">{{ $d->t36_a }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $d->t36_a }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($d->t38_a == $d->t38_d)
                                                            <span class="badge badge-success">{{ $d->t38_a }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $d->t38_a }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $d->sum_a }}
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr style="background:#333; color:white;">
                                                    <td colspan="3"></td>
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
                                                    <td>{{ $total }}</td> 
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
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
<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<script>
    $(document).ready(function(){
        $(".wrapper").addClass("sidebar_minimize");
    })

    function alistarNuevamente(event){
        event.preventDefault(); 
        swal({
            title: "¿Está seguro?",
            text: "¡Se cancelará el alistamiento! \n Se eliminará los regitros de esta orden de alistamiento para volver a alistar.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                document.getElementById('alistarNuevamente').submit();
            } 
        });
    }

    function aprobarAlistamiento(event){
        event.preventDefault();
        swal({
            title: "¿Está seguro?",
            text: "¡Se aprobará el alistamiento! \n Se guardará el alistamiento en la orden de despacho.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                document.getElementById('aprobarAlistamiento').submit();
            } 
        });
    }
</script>    

@endpush