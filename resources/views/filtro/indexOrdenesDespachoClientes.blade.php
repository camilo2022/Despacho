@extends('layouts.appp')

@push('custom-css')
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-Y99D9i9hq8KjRsnf0pc0sM7PyBhBa8WkLpNvOrNHuES7gXa+8eQ7jOvAekGLi7IZmbx8I+uVpKjMgjq1z7JX8Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/semantic.min.css"/>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css"/>
<style>
    .swal-text {
        text-align: center;
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
    div.dataTables_wrapper div.dataTables_filter label{
        text-align: right !important;
    }
    .lineLabel{
        display: inline-flex !important;
    }
    .badge-danger{
        color: #ffffff;
        background-color: #f6000d !important;
    }
    .badge-light {
        color: #212529;
        background-color: #dddddd !important;
    }
    .badge-gray {
        color: #ffffff;
        background-color: #708090 !important;
    }
    .badge-yellow{
        color: #ffffff;
        background-color: #ffc107 !important;
    }
    .badge-morado{
        color: #ffffff;
        background-color: #3f51b5 !important;
    }
    .badge-pink{
        color: #ffffff;
        background-color: #e83e8c;
    }
</style>
@endpush

@section('content')
<div class="container-fluid mt-2">
    <div class="card" style=" font-family:Century Gothic;">
    <div class="card-header text-center" style="background:#333; color:white;">
    LISTADO ORDENES DE DESPACHO CLIENTES
    </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableOrdenesDesachoClientes" class="table dataTable_width_auto display nowrap" >
                    <thead class="">
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Consecutivo</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Nit</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Direccion</th>
                            <th scope="col">Suc</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Correria</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ordenesClientes as $ordenCliente)
                        @php
                            if($ordenCliente->updated_at == null){
                                $ordenCliente->updated_at = $ordenCliente->created_at;
                            }
                        @endphp
                            <tr>
                                <td>{{ $ordenCliente->id }}</td>
                                <td>{{ $ordenCliente->consecutivo }}</td>
                                <td>
                                    @if($ordenCliente->estado == "PREPARANDO")
                                        <span class="badge badge-pill badge-warning">
                                            {{ strtoupper($ordenCliente->estado) }}
                                        </span>
                                    @elseif($ordenCliente->estado == "FACTURANDO")
                                        <span class="badge badge-pill badge-info">
                                            {{ strtoupper($ordenCliente->estado) }}
                                        </span>
                                            @if($ordenCliente->facturas != "[]" && !empty($ordenCliente->facturas))
                                                <span class="badge badge-pill badge-success"><i class="fa fa-solid fa-check"></i><span>
                                            @else
                                                <span class="badge badge-pill badge-danger"><i class="fa fa-solid fa-times"></i><span> 
                                            @endif
                                    @elseif($ordenCliente->estado == "ALISTANDO")
                                        <span class="badge badge-pill badge-primary">
                                            {{ strtoupper($ordenCliente->estado) }}
                                        </span>
                                            @if(empty($ordenCliente->alistamiento))
                                                <span class="badge badge-pill badge-danger"><i class="fa fa-solid fa-times"></i><span>                                           
                                            @elseif($ordenCliente->alistamiento == "INICIADO")
                                                <span class="badge badge-pill badge-warning"><i class="fa fa-shopping-cart"></i><span>
                                            @elseif($ordenCliente->alistamiento == "REVISION")
                                                <span class="badge badge-pill badge-info"><i class="fa fa-solid fa-exclamation"></i><span>
                                            @elseif($ordenCliente->alistamiento == "FINALIZADO")
                                                <span class="badge badge-pill badge-success"><i class="fa fa-solid fa-check"></i><span>
                                            @endif
                                    @elseif($ordenCliente->estado == "EMPACANDO")
                                        <span class="badge badge-pill badge-light"><b>
                                            {{ strtoupper($ordenCliente->estado) }}
                                        </b></span>
                                            @if(empty($ordenCliente->empacado))
                                                <span class="badge badge-pill badge-danger"><i class="fa fa-solid fa-times"></i><span>                                           
                                            @elseif($ordenCliente->empacado == "INICIADO")
                                                <span class="badge badge-pill badge-warning"><i class="fa fa-solid fa-box"></i><span>
                                            @elseif($ordenCliente->empacado == "FINALIZADO")
                                                <span class="badge badge-pill badge-success"><i class="fa fa-solid fa-check"></i><span>
                                            @endif
                                    @elseif($ordenCliente->estado == "DESPACHADO")
                                        <span class="badge badge-pill badge-success">
                                            {{ strtoupper($ordenCliente->estado) }}
                                        </span>
                                    @elseif($ordenCliente->estado == "CANCELADO")
                                        <span class="badge badge-pill badge-danger">
                                            {{ strtoupper($ordenCliente->estado) }}
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $ordenCliente->nit }}</td>
                                <td>{{ $ordenCliente->cliente }}</td>
                                <td>{{ $ordenCliente->direccion }}</td>
                                <td>{{ $ordenCliente->sucursal }}</td>
                                <td>
                                    @if(\Carbon\Carbon::parse($ordenCliente->updated_at)->format('Y-m-d') == $fecha && $ordenCliente->updated_at != $ordenCliente->created_at)
                                        <span class="badge badge-pill badge-dark">   
                                    @endif
                                    {{ $ordenCliente->fecha }}
                                    </span>
                                </td>
                                <td>{{ $ordenCliente->correria }}</td>
                                <td>
                                    <a style="cursor:pointer;" href="{{route('filtro.listado.ordenes.clientes.view',$ordenCliente->consecutivo)}}" title="VISUALIZAR Orden de Despacho"><span class="badge badge-info"><i class="fa fa-solid fa-eye"></i></span></a>
                                    <a style="cursor:pointer;" href="{{route('filtro.listado.ordenes.clientes.print',$ordenCliente->consecutivo)}}" title="IMPRIMIR Orden de Despacho"><span class="badge badge-secondary"><i class="fa fa-solid fa-print"></i></span></a>
                                    @if($ordenCliente->estado == "PREPARANDO")
                                        <a style="cursor:pointer;" href="{{route('filtro.listado.ordenes.clientes.alistar',$ordenCliente->id)}}" title="ALISTAR Orden de Despacho"><span class="badge badge-primary"><i class="fa fa-solid fa-search"></i></span>
                                    @elseif($ordenCliente->estado == "ALISTANDO")
                                        @if(empty($ordenCliente->alistamiento))
                                            @if(Auth::user()->rol->slug == 'AD' || Auth::user()->rol->slug == 'ADFILTRONAC') 
                                                {{--<a style="cursor:pointer;" href="{{route('filtro.listado.ordenes.clientes.reversar',$ordenCliente->id)}}" title="REVERSAR Orden de Despacho a PREPARANDO"><span class="badge badge-warning"><i class="fa fa-solid fa-retweet"></i></span>--}}
                                            @endif
                                            <a style="cursor:pointer;" href="{{route('filtro.listado.ordenes.clientes.alistar.picking',$ordenCliente->id)}}" title="PICKING ALISTAR Orden de Despacho"><span class="badge badge-light"><i class="fa fa-shopping-cart"></i></span>
                                        @elseif($ordenCliente->alistamiento == "REVISION" && (Auth::user()->rol->slug == 'AD' || Auth::user()->rol->slug == 'ADFILTRONAC'))   
                                            <a style="cursor:pointer;" href="{{route('filtro.listado.ordenes.clientes.revisar',$ordenCliente->consecutivo)}}" title="REVISAR Orden de Alistamiento"><span class="badge badge-gray"><i class="fa fa-solid fa-exclamation"></i></span>
                                        @elseif($ordenCliente->alistamiento == "FINALIZADO" && (Auth::user()->rol->slug == 'AD' || Auth::user()->rol->slug == 'ADFILTRONAC'))
                                            <a style="cursor:pointer;" href="{{route('filtro.listado.ordenes.clientes.empacar',$ordenCliente->id)}}" title="EMPACAR Orden de Despacho"><span class="badge badge-warning"><i class="fa fa-solid fa-box"></i></span>
                                            <a style="cursor:pointer;" href="{{route('filtro.listado.ordenes.clientes.facturar',$ordenCliente->id)}}" title="FACTURAR Orden de Despacho"><span class="badge badge-dark"><i class="fas fa-hand-holding-usd"></i></span>
                                        @endif
                                    @elseif($ordenCliente->estado == "EMPACANDO")
                                        @if(empty($ordenCliente->empacado))
                                            @if(Auth::user()->rol->slug == 'AD' || Auth::user()->rol->slug == 'ADFILTRONAC') 
                                                {{--<a style="cursor:pointer;" href="{{route('filtro.listado.ordenes.clientes.reversar',$ordenCliente->id)}}" title="REVERSAR Orden de Despacho a PREPARANDO"><span class="badge badge-warning"><i class="fa fa-solid fa-retweet"></i></span>--}}
                                            @endif
                                            <a style="cursor:pointer;" href="{{route('filtro.listado.ordenes.clientes.empacar.picking',$ordenCliente->id)}}" title="PICKING EMPACAR Orden de Despacho"><span class="badge badge-morado"><i class="fas fa-box-open"></i></span>
                                        @elseif($ordenCliente->empacado == "FINALIZADO" && (Auth::user()->rol->slug == 'AD' || Auth::user()->rol->slug == 'ADFILTRONAC'))
                                            <a style="cursor:pointer;" href="{{route('filtro.listado.ordenes.clientes.facturar',$ordenCliente->id)}}" title="FACTURAR Orden de Despacho"><span class="badge badge-dark"><i class="fas fa-hand-holding-usd"></i></span>
                                        @endif
                                        
                                    @elseif($ordenCliente->estado == "FACTURANDO")
                                        @if(!empty($ordenCliente->facturas))
                                            <a style="cursor:pointer;" href="{{route('filtro.listado.ordenes.clientes.rotulos',$ordenCliente->id)}}" target="_blank" title="GENERAR rotulos"><span class="badge badge-pink"><i class="fa fa-file-pdf"></i></span>
                                            @if(Auth::user()->rol->slug == 'AD' || Auth::user()->rol->slug == 'ADFILTRONAC')
                                                <a style="cursor:pointer;" href="{{route('filtro.listado.ordenes.clientes.despachar',$ordenCliente->id)}}" title="DESPACHAR Orden de Despacho"><span class="badge badge-success"><i class="fa fa-solid fa-check"></i></span>
                                            @endif   
                                        @else
                                            <a style="cursor:pointer;" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="agregarFacturas({{json_encode($ordenCliente)}}, this)" data-id="{{$ordenCliente->id}}" title="AGREGAR facturas a la Orden de Despacho"><span class="badge badge-yellow"><i class="fa fa-solid fa-file-invoice"></i></span>
                                            @if(Auth::user()->rol->slug == 'AD' || Auth::user()->rol->slug == 'ADFILTRONAC')
                                                <a style="cursor:pointer;" href="{{route('filtro.listado.ordenes.clientes.cancelar',$ordenCliente->id)}}" title="CANELAR Orden de Despacho"><span class="badge badge-danger"><i class="fa fa-solid fa-times"></i></span>
                                            @endif
                                        @endif
                                    @elseif($ordenCliente->estado == "DESPACHADO")
                                        <a style="cursor:pointer;" href="{{route('filtro.listado.ordenes.clientes.rotulos',$ordenCliente->id)}}" target="_blank" title="GENERAR rotulos"><span class="badge badge-pink"><i class="fa fa-file-pdf"></i></span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#333; color:white;">
                <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" >
                <div class="card-body">
                       
                        <div class="card-header text-center" style="background:#333; color:white;">
                            DIGITE EL NUMERO DE FACTURA
                        </div>
                        <form action="" method="POST" id="addFacturas" onsubmit="addFacturas(event)">
                            @csrf
                            <div id="inputs">
                                <div class="input-container row m-2">
                                    <div class="col-12">
                                        <input type="text" class="form-control " name="facturas[]" >
                                    </div>
                                </div>
                            </div>
                        <div class="row m-2">
                            <div class="col-4">
                                <button type="button" id="remove-input" class="mb-2 w-100 btn btn-danger" onclick="removeInput()">Quitar</button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="mb-2 w-100 btn btn-info" onclick="addInput()">Agregar</button>
                            </div>
                            <div class="col-4">
                                <button type="submit" class="mb-2 w-100 btn btn-success">Guardar</button>
                            </div>
                        </div>
                        </form>
                    
                </div>    
            </div>
            <div class="modal-footer"><button type="button" class="btn" data-dismiss="modal" style="background-color: #333; color: white;">Close</button></div>
        </div>
    </div>
</div>
@endsection

@push('scripts-custom')
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
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
    function addFacturas(event){
        event.preventDefault();
        const lastInput = document.getElementById('inputs').lastElementChild.querySelector('input');

        if (lastInput.value.trim() === '') {
            alertify.error('¡HAY CAMPOS VACIOS!');
            return;
        }
        swal({
            title: "¿Está seguro?",
            text: "¡Se guardarán las facturas a la orden de despacho y no podrá modificarlas!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                document.getElementById('addFacturas').submit();
            } 
        });
    }

    function agregarFacturas(dataOrdenCliente, button){
        document.getElementById("exampleModalLongTitle").textContent = "AGREGAR FACTURAS A LA ORDEN DE DESPACHO "+dataOrdenCliente.consecutivo;
        console.log(dataOrdenCliente);
        const dataOrdenClienteId = button.dataset.id;
        const form = document.getElementById('addFacturas');
        form.action = '{{ route("filtro.listado.ordenes.clientes.facturar.add", ["id" => ":id"]) }}'.replace(":id", dataOrdenClienteId);
        $("#inputs").html(`<div class="input-container row m-2">
                <div class="col-12">
                    <input type="text" class="form-control " name="facturas[]" >
                </div>
            </div>`)
    }

    function addInput() {
        let inputsContainer = document.getElementById("inputs");
        const lastInput = document.getElementById('inputs').lastElementChild.querySelector('input');

        if (lastInput.value.trim() === '') {
            alertify.error('¡INGRESE UNA FACTURA ANTES DE CREAR OTRO CAMPO!');
            return;
        }
        let newInput = document.createElement("div");
        newInput.classList.add("input-container");
        newInput.classList.add("row")
        newInput.classList.add("m-2");
        newInput.innerHTML = `
                <div class="col-12">
                    <input type="text" class="form-control " name="facturas[]" >
                </div>
        `;
        inputsContainer.appendChild(newInput);
    }

    function removeInput() {
        var inputs = document.getElementsByClassName("input-container");
        if (inputs.length > 1) {
            inputs[inputs.length - 1].remove();
        }
    }
    
    $(document).ready(function(){
        $(".wrapper").addClass("sidebar_minimize");
        var table = $('#tableOrdenesDesachoClientes').DataTable({
            responsive: {
                details: {
                    type: 'column'
                }
            },
            columnDefs: [ {
                className: 'dtr-control',
                orderable: false,
                targets:   0
            }],
            order: [ 1, 'asc' ],            
            dom: 'Blfrtip',
            buttons: [{
                    extend: 'copy',
                    footer: true,
                    title: 'LISTADO ORDENES DE DESPACHO CLIENTES',
                    filename: 'Date Copy',
                    text: '<i class="fa fa-light fa-copy"></i>'
                },
                {
                    //Botón para Excel
                    extend: 'excel',
                    footer: true,
                    title: 'LISTADO ORDENES DE DESPACHO CLIENTES',
                    filename: 'Ordenes Despacho',

                    //Aquí es donde generas el botón personalizado
                    text: '<i class="fa fa-light fa-file-excel"></i>'
                },
                //Botón para PDF
                {
                    extend: 'pdf',
                    footer: true,
                    title: 'LISTADO ORDENES DE DESPACHO CLIENTES',
                    filename: 'Ordenes Despacho',
                    text: '<i class="fa fa-file-pdf"></i>'
                },
                {
                    extend: 'print',
                    footer: true,
                    title: 'LISTADO ORDENES DE DESPACHO CLIENTES',
                    filename: 'Ordenes Despacho',
                    text: '<i class="fa fa-light fa-print"></i>'
                }
            ]
        });
    })
</script>    

@endpush