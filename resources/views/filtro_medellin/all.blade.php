@extends('layouts.appp')

@push('custom-css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/semantic.min.css"/>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css"/>
<style>
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
    [type=checkbox] {
        width: 1.5rem;
        height: 1.5rem;
        color: dodgerblue;
        vertical-align: middle;
        -webkit-appearance: none;
        background: none;
        border: 0;
        outline: 0;
        flex-grow: 0;
        border-radius: 50%;
        background-color: #FFFFFF;
        transition: background 300ms;
        cursor: pointer;
    }
    [type=checkbox]::before {
        content: "";
        color: transparent;
        display: block;
        width: inherit;
        height: inherit;
        border-radius: inherit;
        border: 0;
        background-color: transparent;
        background-size: contain;
        box-shadow: inset 0 0 0 1px #CCD3D8;
    }
    [type=checkbox]:checked {
        background-color: currentcolor;
    }

    [type=checkbox]:checked::before {
        box-shadow: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E %3Cpath d='M15.88 8.29L10 14.17l-1.88-1.88a.996.996 0 1 0-1.41 1.41l2.59 2.59c.39.39 1.02.39 1.41 0L17.3 9.7a.996.996 0 0 0 0-1.41c-.39-.39-1.03-.39-1.42 0z' fill='%23fff'/%3E %3C/svg%3E");
    }
    [type=checkbox]:disabled {
        background-color: #CCD3D8;
        opacity: 0.84;
        cursor: not-allowed;
    }
    [type=checkbox]::-ms-check {
        content: "";
        color: transparent;
        display: block;
        width: inherit;
        height: inherit;
        border-radius: inherit;
        border: 0;
        background-color: transparent;
        background-size: contain;
        box-shadow: inset 0 0 0 1px #CCD3D8;
    }
    [type=checkbox]:checked::-ms-check {
        box-shadow: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E %3Cpath d='M15.88 8.29L10 14.17l-1.88-1.88a.996.996 0 1 0-1.41 1.41l2.59 2.59c.39.39 1.02.39 1.41 0L17.3 9.7a.996.996 0 0 0 0-1.41c-.39-.39-1.03-.39-1.42 0z' fill='%23fff'/%3E %3C/svg%3E");
    }
    .page-item.active .page-link {
        z-index: 1;
        color: #333 !important;
        background-color: #ffffff !important;
        border-color: #333 !important;
    }
    button.principales {
        font-size: 15px;
        text-align: center;
        width: 90%;
        height: 60px;
        margin: 10px;
        font-weight: bold;
        padding: 5%;
    }
    .btn.btn-link{
        padding: 3px 9px !important;
    }
    .ref{
        font-family:Century Gothic !important; 
        background-color: #333 !important; 
        color:white !important; 
        font-weigth:bold !important;
    }
    .center{
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .tableexistencia {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }
    .tableexistencia thead th:nth-child(1) {
        width: 525px;
    }
    .tableexistencia thead th:nth-last-child(1) {
        width: 100px;
    }
    .tableexistencia thead th {
        width: 75px;
    }
    .table td{
        padding: 0 5px !important;
    }
    .tableclientes {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }
    .tableclientes thead th:nth-child(1) {
        width: 75px;
    }
    .tableclientes thead th:nth-child(2) {
        width: 140px;
    }
    .tableclientes thead th:nth-child(3) {
        width: 140px;
    }
    .tableclientes thead th:nth-child(4) {
        width: 170px !important;
    }
    .tableclientes thead th:nth-child(4) {
        width: 140px;
    }
    .tableclientes thead th:nth-last-child(1) {
        width: 100px;
    }
    .tableclientes thead th {
        width: 75px;
    }
    table thead{
        background: #333;
        color: white;
        font-weight: bold;
    }
    .referencia{
        font-family: 'Time New Roman';
        font-size: 85px;
        font-weight: bold;
        border-color: #26292E;
        border-width: 5px;
        border-style: solid; 
        padding: 30px;
    }
    .refe{
        color: transparent;
        transform: translate(-50%, -50%);
        top: 50%;
        left: 50%;
        font-size: 7vw;
        font-family: 'Times New Roman', serif;
        letter-spacing: 5px;
        font-weight: bold;
        background-image: linear-gradient(
            to right,
            #544C1B,
            #7D7128,
            #A39334,
            #B8A53B,
            #A39334,
            #7D7128,
            #544C1B 
        );
        -webkit-background-clip: text; 
    }
    input[type=text]{
        background: transparent;
        max-width: 130px;
        text-align: center;
        border: none;
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
    td.ultima_refe{
        color: #000000;
        font-size: 15px;
        font-weight: bold;
        height: 30px;
    }
    .negativo {
        color: #ffffff;
        font-weight: bold;
    }
    td.negativo {
        background-color: rgb(255, 51, 51) !important;
        font-weight: bold;
    }
    .intermitente{
        box-shadow: 0px 0px 20px;
        animation: infinite resplandorAnimation 2s;
    }
    @keyframes resplandorAnimation {
        0%,100%{
            box-shadow: 0px 0px 20px;
        }
        50%{
            box-shadow: 0px 0px 0px;
        }
    }

</style>
@endpush

@section('content')
<div class="container-fluid mt-2">
    <div class="card" style=" font-family:Century Gothic;">
    <div class="card-header text-center" style="background-color: #333; color:white; font-weigth:bold;">
    FILTRADO DE REFERENCIAS
    </div>
        <div class="card-body">
            <div class="table-responsive">
<body onload="getPedidosAndInventario()">
    <div class="row" id="alerts">
        
    </div>
    <div class="row">
        <div class="col-3 text-center">
            <div class="container-fluid mt-2">
                <div class="card" style=" font-family:Century Gothic;">
                    <div class="card-body">
                        <div class="table-responsive">
                            <div style="width: 100%; height: 250px;">
                                <button id="filtrar" class="principales btn btn-success" onclick="filtrarReferencia()">FILTRADA</button>
                                <button id="priorizar" class="principales btn btn-warning" 
                                data-toggle="modal" data-target=".bd-example-modal-lg">PRIORIZAR</button>
                                <button id="saltar" class="principales btn btn-danger" onclick="saltarReferencia()">SALTAR</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-6 center">
            <div class="referencia text-center">
                <span id="referencia" class="refe">{{$referencias[0]->referencia}}</span>
            </div>
        </div>

        <div class="col-3">

            <div class="card-header text-center ref">
                <span id="position_referencia"> 1 </span> / <span id="total_referencia"> {{count($referencias)}} </span> REFERENCIAS
            </div>
            <div class="container-fluid mt-2">
                <div class="card" style=" font-family:Century Gothic;">
                    <div class="card-body">
                        <div class="table-responsive">
                            <div style="width: auto; height: 200px;">
                                <table id="mydatatable" class="table text-center" style="width:100%">
                                    <thead class="">
                                        <tr>
                                            <th scope="col">{{count($ult_ref_fil)}} Referencias filtradas </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ult_ref_fil as $ult_refes)
                                        <tr>
                                            <td class="ultima_refe">{{ $ult_refes->referencia }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-2 justify-content-center">
        <div class="card" style=" font-family:Century Gothic;">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="row">
                        <div class="col-12">
                                                                
                            <table id="tableExistencia" class="table text-center tableexistencia" style="width:100%">
                                <thead class="">
                                    <tr>
                                        <th scope="col" colspan="3">REFERENCIA</th>
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
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bodegas" id="trBodegas">
                                        
                                    </tr>
                                    <tr class="existencia" id="trExistencia">
                                        
                                    </tr>
                
                                </tbody>
                                <tfoot>
                                    <tr class="restante" id="trRestante">
                                        
                                    </tr>
                                </tfoot>
                            </table>
                            <div id="contentExistencia">
                                <img src="/img/cargando.gif" alt="loading" />
                            </div>
                                        
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <table id="tableClientes" class="table text-center tableclientes" style="width:100%">
                                <thead class="">
                                    <tr>
                                        <th scope="col">OD</th>
                                        <th scope="col">PED</th>
                                        <th scope="col">CLIENTE</th>
                                        <th scope="col">OBSERVACIONES</th>
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
                                    </tr>
                                </thead>
                                <tbody id="bodyClientes">
                                    
                
                                </tbody>
                                <tfoot>
                                    <tr class="sum_pedidos" id="trTotalPedidos">
                                        
                                    </tr>
                                </tfoot>
                            </table>
                            <div id="contentCliente">
                                <img src="/img/cargando.gif" alt="loading" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#333; color:white;">
                <h5 class="modal-title" id="exampleModalLongTitle">Listado referencias por filtrar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" >
                <div class="card-body">
                    <div class="table-responsive">    
                        <table id="tableReferencias" class="table text-center" style="width: 145%;">
                            <thead class="">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Referencia</th>
                                    <th scope="col">Filtrar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($referencias as $refe)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $refe->referencia }}</td>
                                        <td><button class="btn btn-dark" onclick="priorizarReferencia('{{$refe->referencia}}','{{$loop->iteration}}')">Priorizar</button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>    
            </div>
            <div class="modal-footer"><button type="button" class="btn" data-dismiss="modal" style="background-color: #333; color: white;">Close</button></div>
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
    function getAbsolutePath() {
        var loc = window.location;
        var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);
        return loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));
    };

    function cssCambioReferencia(){
        $(".referencia").addClass("intermitente");
        setTimeout(() => {
            $(".referencia").removeClass("intermitente");
        }, 3000);
        alertify.warning('Se hizo cambio de referencia');
    };
    
    function getPedidosAndInventario(){
        var URLdominio = getAbsolutePath();
        var url = URLdominio + "referencia/consulta";
        let referencia = document.getElementById('referencia').innerHTML;
        var dataSend = {
            referencia:referencia,
        };
        const botonFiltrar = document.getElementById("filtrar");
        const botonPriorizar = document.getElementById("priorizar");
        const botonSaltar = document.getElementById("saltar");
        botonFiltrar.setAttribute('disabled', "true");
        botonPriorizar.setAttribute('disabled', "true");
        botonSaltar.setAttribute('disabled', "true");
        $("#trBodegas").html("");
        $("#trExistencia").html("");
        $("#trRestante").html("");
        $("#bodyClientes").html("");
        $("#alerts").html("");
        $("#trTotalPedidos").html("");
        for (let i=2; i<18; i++) {
            let j = i+3
            $('.tableexistencia thead>tr>th:nth-child('+i+')').show();
            $('.tableexistencia tbody>tr>td:nth-child('+i+')').show();
            $('.tableexistencia tfoot>tr>td:nth-child('+i+')').show();
            $('.tableclientes thead>tr>th:nth-child('+j+')').show();
            $('.tableclientes tbody>tr>td:nth-child('+j+')').show();
            $('.tableclientes tfoot>tr>td:nth-child('+j+')').show();
        }
        $.ajax({
        url: url,
        type: 'GET',
        data: dataSend,
        dataType: 'json',
            success: function(data){
                $("#alerts").html(data[3])
                var obj = [
                    [ [2,5],data[1].t04, data[2].t04],  
                    [ [3,6],data[1].t06, data[2].t06],  
                    [ [4,7],data[1].t08, data[2].t08],  
                    [ [5,8],data[1].t10, data[2].t10],  
                    [ [6,9],data[1].t12, data[2].t12],  
                    [ [7,10],data[1].t14, data[2].t14],  
                    [ [8,11],data[1].t16, data[2].t16],  
                    [ [9,12],data[1].t18, data[2].t18], 
                    [ [10,13],data[1].t20, data[2].t20],  
                    [ [11,14],data[1].t22, data[2].t22],  
                    [ [12,15],data[1].t28, data[2].t28],  
                    [ [13,16],data[1].t30, data[2].t30],  
                    [ [14,17],data[1].t32, data[2].t32],  
                    [ [15,18],data[1].t34, data[2].t34], 
                    [ [16,19],data[1].t36, data[2].t36],  
                    [ [17,20],data[1].t38, data[2].t38],   
                ];
                $("#contentExistencia").html("");
                $("#contentCliente").html("");
                $("#trBodegas").html("");
                    var td_b = `<th scope="col" colspan="3">EXISTENCIA EN OTRAS BODEGA</th>
                    <td><input type="number" id="bod_t04" value="`+data[2].t04+`" disabled></td>
                    <td><input type="number" id="bod_t06" value="`+data[2].t06+`" disabled></td>
                    <td><input type="number" id="bod_t08" value="`+data[2].t08+`" disabled></td>
                    <td><input type="number" id="bod_t10" value="`+data[2].t10+`" disabled></td>
                    <td><input type="number" id="bod_t12" value="`+data[2].t12+`" disabled></td>
                    <td><input type="number" id="bod_t14" value="`+data[2].t14+`" disabled></td>
                    <td><input type="number" id="bod_t16" value="`+data[2].t16+`" disabled></td>
                    <td><input type="number" id="bod_t18" value="`+data[2].t18+`" disabled></td>
                    <td><input type="number" id="bod_t20" value="`+data[2].t20+`" disabled></td>
                    <td><input type="number" id="bod_t22" value="`+data[2].t22+`" disabled></td>
                    <td><input type="number" id="bod_t28" value="`+data[2].t28+`" disabled></td>
                    <td><input type="number" id="bod_t30" value="`+data[2].t30+`" disabled></td>
                    <td><input type="number" id="bod_t32" value="`+data[2].t32+`" disabled></td>
                    <td><input type="number" id="bod_t34" value="`+data[2].t34+`" disabled></td>
                    <td><input type="number" id="bod_t36" value="`+data[2].t36+`" disabled></td>
                    <td><input type="number" id="bod_t38" value="`+data[2].t38+`" disabled></td>
                    <td><input type="number" id="bod_total" value="`+data[2].total+`" disabled></td>`
                $("#trBodegas").append(td_b);  
                $("#trExistencia").html("");
                    var td_e = `<th scope="col" colspan="3">EXISTENCIA EN BODEGA PRODUCTO TERMINADO</th>
                    <td><input type="number" id="exi_t04" value="`+data[1].t04+`" disabled></td>
                    <td><input type="number" id="exi_t06" value="`+data[1].t06+`" disabled></td>
                    <td><input type="number" id="exi_t08" value="`+data[1].t08+`" disabled></td>
                    <td><input type="number" id="exi_t10" value="`+data[1].t10+`" disabled></td>
                    <td><input type="number" id="exi_t12" value="`+data[1].t12+`" disabled></td>
                    <td><input type="number" id="exi_t14" value="`+data[1].t14+`" disabled></td>
                    <td><input type="number" id="exi_t16" value="`+data[1].t16+`" disabled></td>
                    <td><input type="number" id="exi_t18" value="`+data[1].t18+`" disabled></td>
                    <td><input type="number" id="exi_t20" value="`+data[1].t20+`" disabled></td>
                    <td><input type="number" id="exi_t22" value="`+data[1].t22+`" disabled></td>
                    <td><input type="number" id="exi_t28" value="`+data[1].t28+`" disabled></td>
                    <td><input type="number" id="exi_t30" value="`+data[1].t30+`" disabled></td>
                    <td><input type="number" id="exi_t32" value="`+data[1].t32+`" disabled></td>
                    <td><input type="number" id="exi_t34" value="`+data[1].t34+`" disabled></td>
                    <td><input type="number" id="exi_t36" value="`+data[1].t36+`" disabled></td>
                    <td><input type="number" id="exi_t38" value="`+data[1].t38+`" disabled></td>
                    <td><input type="number" id="exi_total" value="`+data[1].total+`" disabled></td>`
                $("#trExistencia").append(td_e);  
                $("#trRestante").html("");
                    var td_r = `<th scope="col" colspan="3">RESTANTE DE EXISTENCIA - FILTRADO</th>
                    <td id="r_t04"><input type="number" id="res_t04" value="`+data[1].t04+`" disabled></td>
                    <td id="r_t06"><input type="number" id="res_t06" value="`+data[1].t06+`" disabled></td>
                    <td id="r_t08"><input type="number" id="res_t08" value="`+data[1].t08+`" disabled></td>
                    <td id="r_t10"><input type="number" id="res_t10" value="`+data[1].t10+`" disabled></td>
                    <td id="r_t12"><input type="number" id="res_t12" value="`+data[1].t12+`" disabled></td>
                    <td id="r_t14"><input type="number" id="res_t14" value="`+data[1].t14+`" disabled></td>
                    <td id="r_t16"><input type="number" id="res_t16" value="`+data[1].t16+`" disabled></td>
                    <td id="r_t18"><input type="number" id="res_t18" value="`+data[1].t18+`" disabled></td>
                    <td id="r_t20"><input type="number" id="res_t20" value="`+data[1].t20+`" disabled></td>
                    <td id="r_t22"><input type="number" id="res_t22" value="`+data[1].t22+`" disabled></td>
                    <td id="r_t28"><input type="number" id="res_t28" value="`+data[1].t28+`" disabled></td>
                    <td id="r_t30"><input type="number" id="res_t30" value="`+data[1].t30+`" disabled></td>
                    <td id="r_t32"><input type="number" id="res_t32" value="`+data[1].t32+`" disabled></td>
                    <td id="r_t34"><input type="number" id="res_t34" value="`+data[1].t34+`" disabled></td>
                    <td id="r_t36"><input type="number" id="res_t36" value="`+data[1].t36+`" disabled></td>
                    <td id="r_t38"><input type="number" id="res_t38" value="`+data[1].t38+`" disabled></td>
                    <td id="r_total"><input type="number" id="res_total" value="`+data[1].total+`" disabled></td>`
                $("#trRestante").append(td_r); 
                $("#bodyClientes").html("");
                for(var i=0; i<data[0].length; i++){
                    obj[0].push(data[0][i].t04);
                    obj[1].push(data[0][i].t06);
                    obj[2].push(data[0][i].t08);
                    obj[3].push(data[0][i].t10);
                    obj[4].push(data[0][i].t12);
                    obj[5].push(data[0][i].t14);
                    obj[6].push(data[0][i].t16);
                    obj[7].push(data[0][i].t18);
                    obj[8].push(data[0][i].t20);
                    obj[9].push(data[0][i].t22);
                    obj[10].push(data[0][i].t28);
                    obj[11].push(data[0][i].t30);
                    obj[12].push(data[0][i].t32);
                    obj[13].push(data[0][i].t34);
                    obj[14].push(data[0][i].t36);
                    obj[15].push(data[0][i].t38);
                    data[0][i].observaciones == null ? data[0][i].observaciones=" " : data[0][i].observaciones;
                    data[0][i].obscartera == null ? data[0][i].obscartera=" " : data[0][i].obscartera;
                    var sum_tallas = data[0][i].t04+data[0][i].t06+data[0][i].t08+data[0][i].t10+data[0][i].t12+data[0][i].t14+data[0][i].t16+data[0][i].t18+data[0][i].t20+data[0][i].t22+data[0][i].t28+data[0][i].t30+data[0][i].t32+data[0][i].t34+data[0][i].t36+data[0][i].t38;
                    var tr = `<tr class="pedidos">
                    <td> <input type="checkbox" onclick="validarCheck(this)" name="check" id="`+data[0][i].idamarrador+`"></td>
                    <td><input type="text" id="pedido" value="`+data[0][i].nped+`" disabled></td>
                    <td>`+data[0][i].nombre+" - "+data[0][i].nit+" - "+data[0][i].ciudad+`</td>
                    <td>OBS PED: `+data[0][i].observaciones+` - FECHA PED: `+data[0][i].fecha+`</td>
                    <td><input type="number" onkeyup="" class="t04" id="t04" value="-`+data[0][i].t04+`"></td>
                    <td><input type="number" onkeyup="" class="t06" id="t06" value="-`+data[0][i].t06+`"></td>
                    <td><input type="number" onkeyup="" class="t08" id="t08" value="-`+data[0][i].t08+`"></td>
                    <td><input type="number" onkeyup="" class="t10" id="t10" value="-`+data[0][i].t10+`"></td>
                    <td><input type="number" onkeyup="" class="t12" id="t12" value="-`+data[0][i].t12+`"></td>
                    <td><input type="number" onkeyup="" class="t14" id="t14" value="-`+data[0][i].t14+`"></td>
                    <td><input type="number" onkeyup="" class="t16" id="t16" value="-`+data[0][i].t16+`"></td>
                    <td><input type="number" onkeyup="" class="t18" id="t18" value="-`+data[0][i].t18+`"></td>
                    <td><input type="number" onkeyup="" class="t20" id="t20" value="-`+data[0][i].t20+`"></td>
                    <td><input type="number" onkeyup="" class="t22" id="t22" value="-`+data[0][i].t22+`"></td>
                    <td><input type="number" onkeyup="" class="t28" id="t28" value="-`+data[0][i].t28+`"></td>
                    <td><input type="number" onkeyup="" class="t30" id="t30" value="-`+data[0][i].t30+`"></td>
                    <td><input type="number" onkeyup="" class="t32" id="t32" value="-`+data[0][i].t32+`"></td>
                    <td><input type="number" onkeyup="" class="t34" id="t34" value="-`+data[0][i].t34+`"></td>
                    <td><input type="number" onkeyup="" class="t36" id="t36" value="-`+data[0][i].t36+`"></td>
                    <td><input type="number" onkeyup="" class="t38" id="t38" value="-`+data[0][i].t38+`"></td>
                    <td><input type="number" onkeyup="" class="total" id="total" value="-`+sum_tallas+`" disabled></td>
                    </tr>`;
                    $("#bodyClientes").append(tr)
                }
                $("#trTotalPedidos").html("");
                    var td_tp = `<td><input type="checkbox" onclick="checkAll(this)" name="check_total" id=""></td>
                    <th>N° PEDIDO</th><th>CLIENTE</th><th>OBSERVACIONES</th>
                    <td><input type="number" value="0" id="sum_t04"  ></td>
                    <td><input type="number" value="0" id="sum_t06"  ></td>
                    <td><input type="number" value="0" id="sum_t08"  ></td>
                    <td><input type="number" value="0" id="sum_t10"  ></td>
                    <td><input type="number" value="0" id="sum_t12"  ></td>
                    <td><input type="number" value="0" id="sum_t14"  ></td>
                    <td><input type="number" value="0" id="sum_t16"  ></td>
                    <td><input type="number" value="0" id="sum_t18"  ></td>
                    <td><input type="number" value="0" id="sum_t20"  ></td>
                    <td><input type="number" value="0" id="sum_t22"  ></td>
                    <td><input type="number" value="0" id="sum_t28"  ></td>
                    <td><input type="number" value="0" id="sum_t30"  ></td>
                    <td><input type="number" value="0" id="sum_t32"  ></td>
                    <td><input type="number" value="0" id="sum_t34"  ></td>
                    <td><input type="number" value="0" id="sum_t36"  ></td>
                    <td><input type="number" value="0" id="sum_t38"  ></td>
                    <td><input type="number" value="0" id="sum_total"></td>`
                $("#trTotalPedidos").append(td_tp); 
                restanteExistenciaVsFiltrado();
                //var ocultarColumnas = "";
                const todoIgual = arr => {
                    for (let index = 1; index < arr.length; index++) {
                        if (arr[index] == 0) { // <-- Siempre comparamos con el primero
                            continue; // <-- Si está todo bien seguimos con el siguiente item 
                        }else{
                            return false; // <-- Se termina la función y la iteración en false
                        }
                    }
                    return true; 
                }
                for (let i=0; i<obj.length; i++) {
                    var bol = todoIgual(obj[i]);
                    if(bol){
                        $('.tableexistencia thead>tr>th:nth-child('+obj[i][0][0]+')').hide();
                        $('.tableexistencia tbody>tr>td:nth-child('+obj[i][0][0]+')').hide();
                        $('.tableexistencia tfoot>tr>td:nth-child('+obj[i][0][0]+')').hide();
                        $('.tableclientes thead>tr>th:nth-child('+obj[i][0][1]+')').hide();
                        $('.tableclientes tbody>tr>td:nth-child('+obj[i][0][1]+')').hide();
                        $('.tableclientes tfoot>tr>td:nth-child('+obj[i][0][1]+')').hide();
                        //ocultarColumnas +='.tableexistencia thead>tr>th:nth-child('+obj[i][0][0]+')'
                        //'.tableexistencia tbody>tr>td:nth-child('+obj[i][0][0]+')'
                        //'.tableexistencia tfoot>tr>td:nth-child('+obj[i][0][0]+')'
                        //'.tableclientes thead>tr>th:nth-child('+obj[i][0][1]+')'
                        //'.tableclientes tbody>tr>td:nth-child('+obj[i][0][1]+')';
                    }
                }
                //var styleNode = document.createElement('style');
                //styleNode.setAttribute("type", "text/css");
                //var headTag = document.getElementsByTagName("head")[0];
                //headTag.appendChild(styleNode);
                //var aStyleTags = headTag.getElementsByTagName("style");
                //var justAddedStyleTag = aStyleTags[aStyleTags.length-1];
                //justAddedStyleTag.innerHTML = ocultarColumnas;

                botonFiltrar.removeAttribute('disabled');
                botonPriorizar.removeAttribute('disabled');
                botonSaltar.removeAttribute('disabled');
            },
        })
        
    };

    function checkAll(source) {
        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i] != source){
                checkboxes[i].checked = source.checked;
            }
        }
        restanteExistenciaVsFiltrado();
    }

    function priorizarReferencia(referencia, posicion){
        $("#contentExistencia").html(`<img src="/img/cargando.gif" alt="loading" />`);
        $("#contentCliente").html(`<img src="/img/cargando.gif" alt="loading" />`);
        document.getElementById('referencia').innerHTML = referencia;
        document.getElementById('position_referencia').innerHTML = posicion;
        getPedidosAndInventario();
        cssCambioReferencia();
        $("[data-dismiss=modal]").trigger({ type: "click" });
    };

    function saltarReferencia(){
        $("#contentExistencia").html(`<img src="/img/cargando.gif" alt="loading" />`);
        $("#contentCliente").html(`<img src="/img/cargando.gif" alt="loading" />`);
        var referencias = {!! json_encode($referencias) !!};
        var posicion = parseInt(document.getElementById('position_referencia').innerHTML)-1;
        if(parseInt(posicion)<referencias.length-1){
            document.getElementById('referencia').innerHTML = referencias[parseInt(posicion)+1]["referencia"];
            getPedidosAndInventario();  
            document.getElementById('position_referencia').innerHTML = parseInt(posicion)+2;
        }else{
            document.getElementById('referencia').innerHTML = referencias[0]["referencia"];
            getPedidosAndInventario();  
            document.getElementById('position_referencia').innerHTML = 1;
        }             
        cssCambioReferencia();
    };

    function validarCheck(check){
        if(check.checked){
            restanteExistenciaVsFiltrado();
        }else{
            restanteExistenciaVsFiltrado();
        }
    };

    function restanteExistenciaVsFiltrado(){
        const tableClientesRows = document.querySelectorAll('#tableClientes tr.pedidos');
        const tableExistenciaRowPed = document.querySelectorAll('#tableClientes tr.sum_pedidos');
        const tableExistenciaRowExi = document.querySelectorAll('#tableExistencia tr.existencia');
        const tableExistenciaRowRes = document.querySelectorAll('#tableExistencia tr.restante');
        const arrayTallasRows = {
            t04: [], t06: [], t08: [], t10: [], 
            t12: [], t14: [], t16: [], t18: [],
            t20: [], t22: [], t28: [], t30: [], 
            t32: [], t34: [], t36: [], t38: [],
            total: []
        }; 
        const arraySumPedidos = {
            t04: 0, t06: 0, t08: 0, t10: 0, 
            t12: 0, t14: 0, t16: 0, t18: 0,
            t20: 0, t22: 0, t28: 0, t30: 0, 
            t32: 0, t34: 0, t36: 0, t38: 0,
            total: 0
        }; 
        for(let i=0; i<tableClientesRows.length; i++){
            if(tableClientesRows[i].querySelector('input[name="check"]').checked){

                tableClientesRows[i].querySelector('#t04').setAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t06').setAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t08').setAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t10').setAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t12').setAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t14').setAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t16').setAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t18').setAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t20').setAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t22').setAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t28').setAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t30').setAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t32').setAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t34').setAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t36').setAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t38').setAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                arrayTallasRows.t04.push(parseInt($(tableClientesRows[i].querySelector('#t04')).val()));
                arrayTallasRows.t06.push(parseInt($(tableClientesRows[i].querySelector('#t06')).val()));
                arrayTallasRows.t08.push(parseInt($(tableClientesRows[i].querySelector('#t08')).val()));
                arrayTallasRows.t10.push(parseInt($(tableClientesRows[i].querySelector('#t10')).val()));
                arrayTallasRows.t12.push(parseInt($(tableClientesRows[i].querySelector('#t12')).val()));
                arrayTallasRows.t14.push(parseInt($(tableClientesRows[i].querySelector('#t14')).val()));
                arrayTallasRows.t16.push(parseInt($(tableClientesRows[i].querySelector('#t16')).val()));
                arrayTallasRows.t18.push(parseInt($(tableClientesRows[i].querySelector('#t18')).val()));
                arrayTallasRows.t20.push(parseInt($(tableClientesRows[i].querySelector('#t20')).val()));
                arrayTallasRows.t22.push(parseInt($(tableClientesRows[i].querySelector('#t22')).val()));
                arrayTallasRows.t28.push(parseInt($(tableClientesRows[i].querySelector('#t28')).val()));
                arrayTallasRows.t30.push(parseInt($(tableClientesRows[i].querySelector('#t30')).val()));
                arrayTallasRows.t32.push(parseInt($(tableClientesRows[i].querySelector('#t32')).val()));
                arrayTallasRows.t34.push(parseInt($(tableClientesRows[i].querySelector('#t34')).val()));
                arrayTallasRows.t36.push(parseInt($(tableClientesRows[i].querySelector('#t36')).val()));
                arrayTallasRows.t38.push(parseInt($(tableClientesRows[i].querySelector('#t38')).val()));
                arraySumPedidos.t04+=parseInt($(tableClientesRows[i].querySelector('#t04')).val());
                arraySumPedidos.t06+=parseInt($(tableClientesRows[i].querySelector('#t06')).val());
                arraySumPedidos.t08+=parseInt($(tableClientesRows[i].querySelector('#t08')).val());
                arraySumPedidos.t10+=parseInt($(tableClientesRows[i].querySelector('#t10')).val());
                arraySumPedidos.t12+=parseInt($(tableClientesRows[i].querySelector('#t12')).val());
                arraySumPedidos.t14+=parseInt($(tableClientesRows[i].querySelector('#t14')).val());
                arraySumPedidos.t16+=parseInt($(tableClientesRows[i].querySelector('#t16')).val());
                arraySumPedidos.t18+=parseInt($(tableClientesRows[i].querySelector('#t18')).val());
                arraySumPedidos.t20+=parseInt($(tableClientesRows[i].querySelector('#t20')).val());
                arraySumPedidos.t22+=parseInt($(tableClientesRows[i].querySelector('#t22')).val());
                arraySumPedidos.t28+=parseInt($(tableClientesRows[i].querySelector('#t28')).val());
                arraySumPedidos.t30+=parseInt($(tableClientesRows[i].querySelector('#t30')).val());
                arraySumPedidos.t32+=parseInt($(tableClientesRows[i].querySelector('#t32')).val());
                arraySumPedidos.t34+=parseInt($(tableClientesRows[i].querySelector('#t34')).val());
                arraySumPedidos.t36+=parseInt($(tableClientesRows[i].querySelector('#t36')).val());
                arraySumPedidos.t38+=parseInt($(tableClientesRows[i].querySelector('#t38')).val());
                var total = parseInt($(tableClientesRows[i].querySelector('#t04')).val()) + parseInt($(tableClientesRows[i].querySelector('#t06')).val()) +
                            parseInt($(tableClientesRows[i].querySelector('#t08')).val()) + parseInt($(tableClientesRows[i].querySelector('#t10')).val()) + 
                            parseInt($(tableClientesRows[i].querySelector('#t12')).val()) + parseInt($(tableClientesRows[i].querySelector('#t14')).val()) + 
                            parseInt($(tableClientesRows[i].querySelector('#t16')).val()) + parseInt($(tableClientesRows[i].querySelector('#t18')).val()) + 
                            parseInt($(tableClientesRows[i].querySelector('#t20')).val()) + parseInt($(tableClientesRows[i].querySelector('#t22')).val()) + 
                            parseInt($(tableClientesRows[i].querySelector('#t28')).val()) + parseInt($(tableClientesRows[i].querySelector('#t30')).val()) + 
                            parseInt($(tableClientesRows[i].querySelector('#t32')).val()) + parseInt($(tableClientesRows[i].querySelector('#t34')).val()) + 
                            parseInt($(tableClientesRows[i].querySelector('#t36')).val()) + parseInt($(tableClientesRows[i].querySelector('#t38')).val())
                arrayTallasRows.total.push(total);
                arraySumPedidos.total+=total;
                $(tableClientesRows[i].querySelector('#total')).val(total)
            }else{
                tableClientesRows[i].querySelector('#t04').removeAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t06').removeAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t08').removeAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t10').removeAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t12').removeAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t14').removeAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t16').removeAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t18').removeAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t20').removeAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t22').removeAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t28').removeAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t30').removeAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t32').removeAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t34').removeAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t36').removeAttribute("onkeyup","restanteExistenciaVsFiltrado()")
                tableClientesRows[i].querySelector('#t38').removeAttribute("onkeyup","restanteExistenciaVsFiltrado()")
            }
        }

        $(tableExistenciaRowRes[0].querySelector('#res_t04')).val(
            parseInt($(tableExistenciaRowExi[0].querySelector('#exi_t04')).val()) + arrayTallasRows.t04.reduce((acum, val) => acum + val, 0)
        );
        $(tableExistenciaRowRes[0].querySelector('#res_t06')).val(
            parseInt($(tableExistenciaRowExi[0].querySelector('#exi_t06')).val()) + arrayTallasRows.t06.reduce((acum, val) => acum + val, 0)
        );
        $(tableExistenciaRowRes[0].querySelector('#res_t08')).val(
            parseInt($(tableExistenciaRowExi[0].querySelector('#exi_t08')).val()) + arrayTallasRows.t08.reduce((acum, val) => acum + val, 0)
        );
        $(tableExistenciaRowRes[0].querySelector('#res_t10')).val(
            parseInt($(tableExistenciaRowExi[0].querySelector('#exi_t10')).val()) + arrayTallasRows.t10.reduce((acum, val) => acum + val, 0)
        );
        $(tableExistenciaRowRes[0].querySelector('#res_t12')).val(
            parseInt($(tableExistenciaRowExi[0].querySelector('#exi_t12')).val()) + arrayTallasRows.t12.reduce((acum, val) => acum + val, 0)
        );
        $(tableExistenciaRowRes[0].querySelector('#res_t14')).val(
            parseInt($(tableExistenciaRowExi[0].querySelector('#exi_t14')).val()) + arrayTallasRows.t14.reduce((acum, val) => acum + val, 0)
        );
        $(tableExistenciaRowRes[0].querySelector('#res_t16')).val(
            parseInt($(tableExistenciaRowExi[0].querySelector('#exi_t16')).val()) + arrayTallasRows.t16.reduce((acum, val) => acum + val, 0)
        );
        $(tableExistenciaRowRes[0].querySelector('#res_t18')).val(
            parseInt($(tableExistenciaRowExi[0].querySelector('#exi_t18')).val()) + arrayTallasRows.t18.reduce((acum, val) => acum + val, 0)
        );
        $(tableExistenciaRowRes[0].querySelector('#res_t20')).val(
            parseInt($(tableExistenciaRowExi[0].querySelector('#exi_t20')).val()) + arrayTallasRows.t20.reduce((acum, val) => acum + val, 0)
        );
        $(tableExistenciaRowRes[0].querySelector('#res_t22')).val(
            parseInt($(tableExistenciaRowExi[0].querySelector('#exi_t22')).val()) + arrayTallasRows.t22.reduce((acum, val) => acum + val, 0)
        );
        $(tableExistenciaRowRes[0].querySelector('#res_t28')).val(
            parseInt($(tableExistenciaRowExi[0].querySelector('#exi_t28')).val()) + arrayTallasRows.t28.reduce((acum, val) => acum + val, 0)
        );
        $(tableExistenciaRowRes[0].querySelector('#res_t30')).val(
            parseInt($(tableExistenciaRowExi[0].querySelector('#exi_t30')).val()) + arrayTallasRows.t30.reduce((acum, val) => acum + val, 0)
        );
        $(tableExistenciaRowRes[0].querySelector('#res_t32')).val(
            parseInt($(tableExistenciaRowExi[0].querySelector('#exi_t32')).val()) + arrayTallasRows.t32.reduce((acum, val) => acum + val, 0)
        );
        $(tableExistenciaRowRes[0].querySelector('#res_t34')).val(
            parseInt($(tableExistenciaRowExi[0].querySelector('#exi_t34')).val()) + arrayTallasRows.t34.reduce((acum, val) => acum + val, 0)
        );
        $(tableExistenciaRowRes[0].querySelector('#res_t36')).val(
            parseInt($(tableExistenciaRowExi[0].querySelector('#exi_t36')).val()) + arrayTallasRows.t36.reduce((acum, val) => acum + val, 0)
        );
        $(tableExistenciaRowRes[0].querySelector('#res_t38')).val(
            parseInt($(tableExistenciaRowExi[0].querySelector('#exi_t38')).val()) + arrayTallasRows.t38.reduce((acum, val) => acum + val, 0)
        );
        $(tableExistenciaRowRes[0].querySelector('#res_total')).val(
            parseInt($(tableExistenciaRowExi[0].querySelector('#exi_total')).val()) + arrayTallasRows.total.reduce((acum, val) => acum + val, 0)
        );


        $(tableExistenciaRowPed[0].querySelector('#sum_t04')).val(arraySumPedidos.t04);
        $(tableExistenciaRowPed[0].querySelector('#sum_t06')).val(arraySumPedidos.t06);
        $(tableExistenciaRowPed[0].querySelector('#sum_t08')).val(arraySumPedidos.t08);
        $(tableExistenciaRowPed[0].querySelector('#sum_t10')).val(arraySumPedidos.t10);
        $(tableExistenciaRowPed[0].querySelector('#sum_t12')).val(arraySumPedidos.t12);
        $(tableExistenciaRowPed[0].querySelector('#sum_t14')).val(arraySumPedidos.t14);
        $(tableExistenciaRowPed[0].querySelector('#sum_t16')).val(arraySumPedidos.t16);
        $(tableExistenciaRowPed[0].querySelector('#sum_t18')).val(arraySumPedidos.t18);
        $(tableExistenciaRowPed[0].querySelector('#sum_t20')).val(arraySumPedidos.t20);
        $(tableExistenciaRowPed[0].querySelector('#sum_t22')).val(arraySumPedidos.t22);
        $(tableExistenciaRowPed[0].querySelector('#sum_t28')).val(arraySumPedidos.t28);
        $(tableExistenciaRowPed[0].querySelector('#sum_t30')).val(arraySumPedidos.t30);
        $(tableExistenciaRowPed[0].querySelector('#sum_t32')).val(arraySumPedidos.t32);
        $(tableExistenciaRowPed[0].querySelector('#sum_t34')).val(arraySumPedidos.t34);
        $(tableExistenciaRowPed[0].querySelector('#sum_t36')).val(arraySumPedidos.t36);
        $(tableExistenciaRowPed[0].querySelector('#sum_t38')).val(arraySumPedidos.t38);
        $(tableExistenciaRowPed[0].querySelector('#sum_total')).val(arraySumPedidos.total);

        cssValoresNegativos();
    };

    function cssValoresNegativos(){
        const tableExistenciaRowRes = document.querySelectorAll('#tableExistencia tr.restante');
        if(parseInt($(tableExistenciaRowRes[0].querySelector('#res_t04')).val()) < 0){
            tableExistenciaRowRes[0].querySelector('#res_t04').setAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t04').setAttribute("class","negativo")
        }else{
            tableExistenciaRowRes[0].querySelector('#res_t04').removeAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t04').removeAttribute("class","negativo");
        }

        if(parseInt($(tableExistenciaRowRes[0].querySelector('#res_t06')).val()) < 0){
            tableExistenciaRowRes[0].querySelector('#res_t06').setAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t06').setAttribute("class","negativo")
        }else{
            tableExistenciaRowRes[0].querySelector('#res_t06').removeAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t06').removeAttribute("class","negativo")
        }

        if(parseInt($(tableExistenciaRowRes[0].querySelector('#res_t08')).val()) < 0){
            tableExistenciaRowRes[0].querySelector('#res_t08').setAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t08').setAttribute("class","negativo")
        }else{
            tableExistenciaRowRes[0].querySelector('#res_t08').removeAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t08').removeAttribute("class","negativo")
        }

        if(parseInt($(tableExistenciaRowRes[0].querySelector('#res_t10')).val()) < 0){
            tableExistenciaRowRes[0].querySelector('#res_t10').setAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t10').setAttribute("class","negativo")
        }else{
            tableExistenciaRowRes[0].querySelector('#res_t10').removeAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t10').removeAttribute("class","negativo")
        }

        if(parseInt($(tableExistenciaRowRes[0].querySelector('#res_t12')).val()) < 0){
            tableExistenciaRowRes[0].querySelector('#res_t12').setAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t12').setAttribute("class","negativo")
        }else{
            tableExistenciaRowRes[0].querySelector('#res_t12').removeAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t12').removeAttribute("class","negativo")
        }   
        
        if(parseInt($(tableExistenciaRowRes[0].querySelector('#res_t14')).val()) < 0){
            tableExistenciaRowRes[0].querySelector('#res_t14').setAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t14').setAttribute("class","negativo")
        }else{
            tableExistenciaRowRes[0].querySelector('#res_t14').removeAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t14').removeAttribute("class","negativo")
        }

        if(parseInt($(tableExistenciaRowRes[0].querySelector('#res_t16')).val()) < 0){
            tableExistenciaRowRes[0].querySelector('#res_t16').setAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t16').setAttribute("class","negativo")
        }else{
            tableExistenciaRowRes[0].querySelector('#res_t16').removeAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t16').removeAttribute("class","negativo")
        }

        if(parseInt($(tableExistenciaRowRes[0].querySelector('#res_t18')).val()) < 0){
            tableExistenciaRowRes[0].querySelector('#res_t18').setAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t18').setAttribute("class","negativo")
        }else{
            tableExistenciaRowRes[0].querySelector('#res_t18').removeAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t18').removeAttribute("class","negativo")
        }

        if(parseInt($(tableExistenciaRowRes[0].querySelector('#res_t20')).val()) < 0){
            tableExistenciaRowRes[0].querySelector('#res_t20').setAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t20').setAttribute("class","negativo")
        }else{
            tableExistenciaRowRes[0].querySelector('#res_t20').removeAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t20').removeAttribute("class","negativo")
        }

        if(parseInt($(tableExistenciaRowRes[0].querySelector('#res_t22')).val()) < 0){
            tableExistenciaRowRes[0].querySelector('#res_t22').setAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t22').setAttribute("class","negativo")
        }else{
            tableExistenciaRowRes[0].querySelector('#res_t22').removeAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t22').removeAttribute("class","negativo")
        }

        if(parseInt($(tableExistenciaRowRes[0].querySelector('#res_t28')).val()) < 0){
            tableExistenciaRowRes[0].querySelector('#res_t28').setAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t28').setAttribute("class","negativo")
        }else{
            tableExistenciaRowRes[0].querySelector('#res_t28').removeAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t28').removeAttribute("class","negativo")
        }

        if(parseInt($(tableExistenciaRowRes[0].querySelector('#res_t30')).val()) < 0){
            tableExistenciaRowRes[0].querySelector('#res_t30').setAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t30').setAttribute("class","negativo")
        }else{
            tableExistenciaRowRes[0].querySelector('#res_t30').removeAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t30').removeAttribute("class","negativo")
        }

        if(parseInt($(tableExistenciaRowRes[0].querySelector('#res_t32')).val()) < 0){
            tableExistenciaRowRes[0].querySelector('#res_t32').setAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t32').setAttribute("class","negativo")
        }else{
            tableExistenciaRowRes[0].querySelector('#res_t32').removeAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t32').removeAttribute("class","negativo")
        }

        if(parseInt($(tableExistenciaRowRes[0].querySelector('#res_t34')).val()) < 0){
            tableExistenciaRowRes[0].querySelector('#res_t34').setAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t34').setAttribute("class","negativo")
        }else{
            tableExistenciaRowRes[0].querySelector('#res_t34').removeAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t34').removeAttribute("class","negativo")
        }

        if(parseInt($(tableExistenciaRowRes[0].querySelector('#res_t36')).val()) < 0){
            tableExistenciaRowRes[0].querySelector('#res_t36').setAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t36').setAttribute("class","negativo")
        }else{
            tableExistenciaRowRes[0].querySelector('#res_t36').removeAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t36').removeAttribute("class","negativo")
        }

        if(parseInt($(tableExistenciaRowRes[0].querySelector('#res_t38')).val()) < 0){
            tableExistenciaRowRes[0].querySelector('#res_t38').setAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t38').setAttribute("class","negativo")
        }else{
            tableExistenciaRowRes[0].querySelector('#res_t38').removeAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_t38').removeAttribute("class","negativo")
        }

        if(parseInt($(tableExistenciaRowRes[0].querySelector('#res_total')).val()) < 0){
            tableExistenciaRowRes[0].querySelector('#res_total').setAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_total').setAttribute("class","negativo")
        }else{
            tableExistenciaRowRes[0].querySelector('#res_total').removeAttribute("class","negativo")
            tableExistenciaRowRes[0].querySelector('#r_total').removeAttribute("class","negativo")
        }
    };

    function filtrarReferencia(){
        var URLdominio = getAbsolutePath();
        var url = URLdominio + "referencia/store";
        var listReferencias = {!! json_encode($referencias) !!};
        var referencia = document.getElementById('referencia').innerHTML;
        var posicion = parseInt(document.getElementById('position_referencia').innerHTML)-1;
        var token = $("meta[name='csrf-token']").attr("content");
        var tableClientesRows = document.querySelectorAll('#tableClientes tr.pedidos');
        var arrayClientesFiltrados = [];
        var countChecked = 0;
        var validacion = validarRestante();
        for(let i=0; i<tableClientesRows.length; i++){
            if(tableClientesRows[i].querySelector('input[name="check"]').checked){
                const obj = {
                    idamarrador: tableClientesRows[i].querySelector('input[name="check"]').id,
                    ped: $(tableClientesRows[i].querySelector('#pedido')).val(),
                    t04: parseInt($(tableClientesRows[i].querySelector('#t04')).val())*-1, 
                    t06: parseInt($(tableClientesRows[i].querySelector('#t06')).val())*-1, 
                    t08: parseInt($(tableClientesRows[i].querySelector('#t08')).val())*-1, 
                    t10: parseInt($(tableClientesRows[i].querySelector('#t10')).val())*-1, 
                    t12: parseInt($(tableClientesRows[i].querySelector('#t12')).val())*-1, 
                    t14: parseInt($(tableClientesRows[i].querySelector('#t14')).val())*-1, 
                    t16: parseInt($(tableClientesRows[i].querySelector('#t16')).val())*-1, 
                    t18: parseInt($(tableClientesRows[i].querySelector('#t18')).val())*-1,
                    t20: parseInt($(tableClientesRows[i].querySelector('#t20')).val())*-1, 
                    t22: parseInt($(tableClientesRows[i].querySelector('#t22')).val())*-1, 
                    t28: parseInt($(tableClientesRows[i].querySelector('#t28')).val())*-1, 
                    t30: parseInt($(tableClientesRows[i].querySelector('#t30')).val())*-1, 
                    t32: parseInt($(tableClientesRows[i].querySelector('#t32')).val())*-1, 
                    t34: parseInt($(tableClientesRows[i].querySelector('#t34')).val())*-1,
                    t36: parseInt($(tableClientesRows[i].querySelector('#t36')).val())*-1, 
                    t38: parseInt($(tableClientesRows[i].querySelector('#t38')).val())*-1
                }
                arrayClientesFiltrados.push(obj);
                countChecked++;
            }
        }
        let dataSend = {
            data:arrayClientesFiltrados,
            referencia: referencia,
            _token:token
        };
        if(countChecked == 0){
            alertify.error('No se seleccionaron clientes a filtrar.');
        }else if(!validacion){
            alertify.error('El restante no puede ser negativo.');
        }else{
            alertify.confirm("FILTRAR REFERENCIA: "+referencia,"¿Desea filtrar esta referencia? Una vez lo haga esta no aparecerá mas en el dia de hoy.",
            function(){
                $.ajax({
                url: url,
                type: 'POST',
                data: dataSend,
                dataType: 'json',
                    success: function(data){
                        alertify.success('Referencia filtrada.');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    },
                })
            },
            function(){
                alertify.error('Acción cancelada');
            });
        }
    };

    function validarRestante(){
        const tableExistenciaRowRes = document.querySelectorAll('#tableExistencia tr.restante');
        if(parseInt($(tableExistenciaRowRes[0].querySelector('#res_t04')).val()) < 0 ||
        parseInt($(tableExistenciaRowRes[0].querySelector('#res_t06')).val()) < 0 || parseInt($(tableExistenciaRowRes[0].querySelector('#res_t08')).val()) < 0 ||
        parseInt($(tableExistenciaRowRes[0].querySelector('#res_t10')).val()) < 0 || parseInt($(tableExistenciaRowRes[0].querySelector('#res_t12')).val()) < 0 ||
        parseInt($(tableExistenciaRowRes[0].querySelector('#res_t14')).val()) < 0 || parseInt($(tableExistenciaRowRes[0].querySelector('#res_t16')).val()) < 0 ||
        parseInt($(tableExistenciaRowRes[0].querySelector('#res_t18')).val()) < 0 || parseInt($(tableExistenciaRowRes[0].querySelector('#res_t20')).val()) < 0 ||
        parseInt($(tableExistenciaRowRes[0].querySelector('#res_t22')).val()) < 0 || parseInt($(tableExistenciaRowRes[0].querySelector('#res_t28')).val()) < 0 ||
        parseInt($(tableExistenciaRowRes[0].querySelector('#res_t30')).val()) < 0 || parseInt($(tableExistenciaRowRes[0].querySelector('#res_t32')).val()) < 0 ||
        parseInt($(tableExistenciaRowRes[0].querySelector('#res_t34')).val()) < 0 || parseInt($(tableExistenciaRowRes[0].querySelector('#res_t36')).val()) < 0 ||
        parseInt($(tableExistenciaRowRes[0].querySelector('#res_t38')).val()) < 0 ){
            return false
        }else{
            return true
        }
    };
        
    $(document).ready(function(){
        $('#tableReferencias').DataTable({})
        $(".wrapper").addClass("sidebar_minimize");
        var referencias = {!! json_encode($referencias) !!};
    });
</script>
@endpush