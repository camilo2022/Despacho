@extends('layouts.appp')

@push('custom-css')
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-Y99D9i9hq8KjRsnf0pc0sM7PyBhBa8WkLpNvOrNHuES7gXa+8eQ7jOvAekGLi7IZmbx8I+uVpKjMgjq1z7JX8Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    div.dataTables_wrapper div.dataTables_filter label{
        text-align: right !important;
    }
    .lineLabel{
        display: inline-flex !important;
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
                            {{--<th scope="col">Suc</th>--}}
                            <th scope="col">Fecha</th>
                            {{--<th scope="col">Correria</th>--}}
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
                                    @elseif($ordenCliente->estado == "FACTURANDO")
                                        <span class="badge badge-pill badge-info">
                                    @elseif($ordenCliente->estado == "ALISTANDO")
                                        <span class="badge badge-pill badge-primary">
                                    @elseif($ordenCliente->estado == "DESPACHADO")
                                        <span class="badge badge-pill badge-success">
                                    @elseif($ordenCliente->estado == "CANCELADO")
                                        <span class="badge badge-pill badge-danger">
                                    @endif
                                        {{ strtoupper($ordenCliente->estado) }}
                                    </span>
                                </td>
                                <td>{{ $ordenCliente->nit }}</td>
                                <td>{{ $ordenCliente->cliente }}</td>
                                <td>{{ $ordenCliente->direccion }}</td>
                                {{--<td>{{ $ordenCliente->sucursal }}</td>--}}
                                <td>
                                    @if(\Carbon\Carbon::parse($ordenCliente->updated_at)->format('Y-m-d') == $fecha && $ordenCliente->updated_at != $ordenCliente->created_at)
                                        <span class="badge badge-pill badge-dark">   
                                    @endif
                                    {{ $ordenCliente->fecha }}
                                    </span>
                                </td>
                                {{--<td>{{ $ordenCliente->correria }}</td>--}}
                                <td>
                                    <a style="cursor:pointer;" href="{{route('filtro.medellin.listado.ordenes.clientes.view',$ordenCliente->consecutivo)}}" title="VISUALIZAR Orden de Despacho"><span class="badge badge-info"><i class="fa fa-solid fa-eye"></i></span></a>
                                    <a style="cursor:pointer;" href="{{route('filtro.medellin.listado.ordenes.clientes.print',$ordenCliente->consecutivo)}}" title="IMPRIMIR Orden de Despacho"><span class="badge badge-secondary"><i class="fa fa-solid fa-print"></i></span></a>
                                    @if($ordenCliente->estado == "PREPARANDO")
                                    <a style="cursor:pointer;" href="{{route('filtro.medellin.listado.ordenes.clientes.alistar',$ordenCliente->id)}}" title="ALISTAR Orden de Despacho"><span class="badge badge-primary"><i class="fa fa-solid fa-search"></i></span>
                                    @elseif($ordenCliente->estado == "ALISTANDO")
                                    <a style="cursor:pointer;" href="{{route('filtro.medellin.listado.ordenes.clientes.reversar',$ordenCliente->id)}}" title="REVERSAR Orden de Despacho a PREPARANDO"><span class="badge badge-warning"><i class="fa fa-solid fa-retweet"></i></span>
                                    <a style="cursor:pointer;" href="{{route('filtro.medellin.listado.ordenes.clientes.facturar',$ordenCliente->id)}}" title="FACTURAR Orden de Despacho"><span class="badge badge-dark"><i class="fas fa-hand-holding-usd"></i></span>
                                    @elseif($ordenCliente->estado == "FACTURANDO")
                                    <a style="cursor:pointer;" href="{{route('filtro.medellin.listado.ordenes.clientes.despachar',$ordenCliente->id)}}" title="DESPACHAR Orden de Despacho"><span class="badge badge-success"><i class="fa fa-solid fa-check"></i></span>
                                    <a style="cursor:pointer;" href="{{route('filtro.medellin.listado.ordenes.clientes.cancelar',$ordenCliente->id)}}" title="CANELAR Orden de Despacho"><span class="badge badge-danger"><i class="fa fa-solid">&#x2716</i></span>
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
<script>
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