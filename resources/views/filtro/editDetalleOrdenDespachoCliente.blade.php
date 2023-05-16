@extends('layouts.appp')

@push('custom-css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<style>
    .c_form_group {
        border: 1px solid #e9ecef;
        text-align: left;
        padding: 10px;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .form-control:disabled, .form-control[readonly] {
        background: #ffffff !important;
        color: #495057 !important;
        opacity: 1 !important;
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
                    <form method="post"action="{{route('filtro.listado.ordenes.clientes.update.detalle',[$consecutivo, $amarrador, $detalleOrdenCliente->id_dod])}}" accept-charset="UTF-8">
                    @csrf
                        <div class="form-group c_form_group">
                            <label id="tx04">REFERENCIA</label>
                            <input type="text" class="form-control" placeholder="" autocomplete="off" id="refe" name="refe" value="{{$detalleOrdenCliente->referencia}}" disabled>
                        </div>
                        <div class="form-group c_form_group">
							<label id="tx04">Talla  04 ( Existencia : {{$detalleOrdenCliente->t04}} )</label>
	 	                    <input type="number" class="form-control" placeholder=" " autocomplete="off" id="t04" name="t04" value="{{$detalleOrdenCliente->t04}}">
						</div>
                        <div class="form-group c_form_group">
							<label id="tx04">Talla  06 ( Existencia : {{$detalleOrdenCliente->t06}} )</label>
	 	                    <input type="number" class="form-control" placeholder=" " autocomplete="off" id="t06" name="t06" value="{{$detalleOrdenCliente->t06}}">
						</div>
                        <div class="form-group c_form_group">
							<label id="tx04">Talla  08 ( Existencia : {{$detalleOrdenCliente->t08}} )</label>
	 	                    <input type="number" class="form-control" placeholder=" " autocomplete="off" id="t08" name="t08" value="{{$detalleOrdenCliente->t08}}">
						</div>
                        <div class="form-group c_form_group">
							<label id="tx04">Talla  10 ( Existencia : {{$detalleOrdenCliente->t10}} )</label>
	 	                    <input type="number" class="form-control" placeholder=" " autocomplete="off" id="t10" name="t10" value="{{$detalleOrdenCliente->t10}}">
						</div>
                        <div class="form-group c_form_group">
							<label id="tx04">Talla  12 ( Existencia : {{$detalleOrdenCliente->t12}} )</label>
	 	                    <input type="number" class="form-control" placeholder=" " autocomplete="off" id="t12" name="t12" value="{{$detalleOrdenCliente->t12}}">
						</div>
                        <div class="form-group c_form_group">
							<label id="tx04">Talla  14 ( Existencia : {{$detalleOrdenCliente->t14}} )</label>
	 	                    <input type="number" class="form-control" placeholder=" " autocomplete="off" id="t14" name="t14" value="{{$detalleOrdenCliente->t14}}">
						</div>
                        <div class="form-group c_form_group">
							<label id="tx04">Talla  16 ( Existencia : {{$detalleOrdenCliente->t16}} )</label>
	 	                    <input type="number" class="form-control" placeholder=" " autocomplete="off" id="t16" name="t16" value="{{$detalleOrdenCliente->t16}}">
						</div>
                        <div class="form-group c_form_group">
							<label id="tx04">Talla  18 ( Existencia : {{$detalleOrdenCliente->t18}} )</label>
	 	                    <input type="number" class="form-control" placeholder=" " autocomplete="off" id="t18" name="t18" value="{{$detalleOrdenCliente->t18}}">
						</div>
                        <div class="form-group c_form_group">
							<label id="tx04">Talla  20 ( Existencia : {{$detalleOrdenCliente->t20}} )</label>
	 	                    <input type="number" class="form-control" placeholder=" " autocomplete="off" id="t20" name="t20" value="{{$detalleOrdenCliente->t20}}">
						</div>
                        <div class="form-group c_form_group">
							<label id="tx04">Talla  22 ( Existencia : {{$detalleOrdenCliente->t22}} )</label>
	 	                    <input type="number" class="form-control" placeholder=" " autocomplete="off" id="t22" name="t22" value="{{$detalleOrdenCliente->t22}}">
						</div>
                        <div class="form-group c_form_group">
							<label id="tx04">Talla  28 ( Existencia : {{$detalleOrdenCliente->t28}} )</label>
	 	                    <input type="number" class="form-control" placeholder=" " autocomplete="off" id="t28" name="t28" value="{{$detalleOrdenCliente->t28}}">
						</div>
                        <div class="form-group c_form_group">
							<label id="tx04">Talla  30 ( Existencia : {{$detalleOrdenCliente->t30}} )</label>
	 	                    <input type="number" class="form-control" placeholder=" " autocomplete="off" id="t30" name="t30" value="{{$detalleOrdenCliente->t30}}">
						</div>
                        <div class="form-group c_form_group">
							<label id="tx04">Talla  32 ( Existencia : {{$detalleOrdenCliente->t32}} )</label>
	 	                    <input type="number" class="form-control" placeholder=" " autocomplete="off" id="t32" name="t32" value="{{$detalleOrdenCliente->t32}}">
						</div>
                        <div class="form-group c_form_group">
							<label id="tx04">Talla  34 ( Existencia : {{$detalleOrdenCliente->t34}} )</label>
	 	                    <input type="number" class="form-control" placeholder=" " autocomplete="off" id="t34" name="t34" value="{{$detalleOrdenCliente->t34}}">
						</div>
                        <div class="form-group c_form_group">
							<label id="tx04">Talla  36 ( Existencia : {{$detalleOrdenCliente->t36}} )</label>
	 	                    <input type="number" class="form-control" placeholder=" " autocomplete="off" id="t36" name="t36" value="{{$detalleOrdenCliente->t36}}">
						</div>
                        <div class="form-group c_form_group">
							<label id="tx04">Talla  38 ( Existencia : {{$detalleOrdenCliente->t38}} )</label>
	 	                    <input type="number" class="form-control" placeholder=" " autocomplete="off" id="t38" name="t38" value="{{$detalleOrdenCliente->t38}}">
						</div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Actualizar Curva</button>
                        </div>
                    </form>
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