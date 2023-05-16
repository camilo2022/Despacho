@extends('layouts.appp')

@push('custom-css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/semantic.min.css"/>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css"/>
<link href="https://harvesthq.github.io/chosen/chosen.css" rel="stylesheet"/>
<style>
    .card .card-body, .card-light .card-body {
        padding: 1.25rem;
    }
    .form-control{
        font-size: 14px !important;
        border-color: #ebedf2 !important;
        padding: 0.6rem 1rem !important;
        height: 44.19px !important;
        width: 100% !important;
    }
    .chosen-container-single .chosen-single {
        font-size: 14px !important;
        border-color: #ebedf2 !important;
        padding: 0.6rem 1rem !important;
        height: 44.19px !important;
        width: 100% !important;
        background: #fff !important;
        font-family: inherit !important;
        text-align: left !important;
    }
    .chosen-container-single .chosen-single .chosen-container-active .chosen-with-drop{
        width: 100% !important;
        font-size: 14px !important;
        align-items: center !important;
        font-family: inherit !important;
        text-align: left !important;
    }
    .chosen-container-single .chosen-single div b {
        display: none !important;
        font-family: inherit !important;
        text-align: left !important;
    }
    .chosen-container .chosen-container-single .chosen-with-drop .chosen-container-active{
        height: 100px !important;
    }
    .c_form_group {
        border: 1px solid #e9ecef;
        text-align: left;
        padding: 10px;
    }
    .form-group {
        margin-bottom: 1rem;
    }
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
        GENERAR ROTULO PARA ORDENES DISTINTAS
        </div>
        <div class="card-body">
            <div class="card-body">
                <form id="crearCaja" method="POST" action="{{route('filtro.listado.ordenes.clientes.rotulos.multiples.view')}}">
                    @csrf
                <div class="row">
                    <div class="col-sm-4 text-center">
                        <div class="form-group c_form_group">
                            <label>CLIENTES</label>
                            <select data-placeholder="Seleccione" onchange="searchOrdenes(this)" class="chosen-select form-control" id="cliente" name="cliente">
                                <option value="" selected disabled></option>
                                @foreach($clientes as $cliente)
                                    <option value="{{$cliente->nit.' / '.$cliente->sucursal.' / '.$cliente->ciudad.'. / '.$cliente->departamento.'. / '.$cliente->direccion.". / ".$cliente->cliente}}">{{$cliente->cliente.' - '.$cliente->nit.' - '.$cliente->sucursal}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4 text-center ordenes">
                        <div class="form-group c_form_group">
                            <label>ORDENES DE DESPACHO</label>
                            <select multiple data-placeholder="Seleccione" class="chosen-select chosen-select-multiple form-control" id="ordenes" name="ordenes[]">
                                <option value="" ></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4 text-center">
                        <div class="form-group c_form_group">
                            <label>CANTIDAD DE EMPAQUES</label>
                            <input type="range" class="form-control-range" id="range" name="range" min="1" max="25" onInput="$('#rangeval').html($(this).val())">
                            <span id="rangeval">-</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card w-100">
                            <div class="card-header text-center" style="background:#333; color:white;">
                                EMPAQUES
                            </div>
                            <div class="card-body text-center row2">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn w-100 btn-success" style="font-family:Century Gothic; color:white; font-weigth:bold;" id="generar" onclick="generarConsulta()">Generar Consulta</button>
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
<script src="https://harvesthq.github.io/chosen/chosen.jquery.js"></script>
<script>
    $(document).ready(function(){
        //$(".wrapper").addClass("sidebar_minimize");
        $("#ordenes_chosen").addClass("form-control");
        $(".chosen-search-input default").addClass("form-control");
    })
    $(".chosen-select").chosen();
    $(".chosen-select").chosen({no_results_text:'No hay resultados para '});
    function searchOrdenes(option){
        let ordenes = {!! json_encode($ordenes) !!};
        let data = option.value.split(" / ");
        let nit = data[0];
        let suc = data[1];
        let options = "";
        for(let i = 0; i < ordenes.length; i++){
            if(ordenes[i].nit == nit && ordenes[i].sucursal == suc){
                options = options+'<option value="'+ordenes[i].consecutivo+'">'+ordenes[i].consecutivo+'</option>';
            }
        }
        $(".ordenes").html(`<div class="form-group c_form_group">
                            <label>ORDENES DE DESPACHO</label>
                            <select multiple data-placeholder="Seleccione" class="chosen-select chosen-select-multiple form-control" id="ordenes" name="ordenes[]">
                                <option value="" disabled></option>
                                `+options+`
                            </select>
                        </div>`);
        
        $(document).ready(function() {
            $.getScript("https://harvesthq.github.io/chosen/chosen.jquery.js", function() {
                $(".chosen-select-multiple").chosen();
                $(".chosen-select-multiple").chosen({no_results_text:'No hay resultados para '});
                $("#ordenes_chosen").addClass("form-control");
                $("#ordenes #ordenes_chosen .chosen-choices .search-field .chosen-search-input").addClass("form-control");
            });
        })
    }
    function generarConsulta(){
        let ordenes = $("#ordenes").val();
        let range = parseInt($("#range").val());
        let empaque = "";
        for (let i = 0; i < range; i++) {
            empaque = empaque+`<div class="col-12">
                <div class="table-responsive">
                    <div>
                        <button  type="button" class="mb-2 btn w-100 collapsed" style="background-color:#23282e; color:white;" data-toggle="collapse"  data-target="#collapseExample`+i+`" aria-expanded="false" aria-controls="#collapseExample`+i+`">
                        <b> 
                            <i class="fa fa-box"></i>
                            <span id="tipo`+i+`"> - </span> # <span class="badge badge-warning"> `+(i+1)+` </span> | <span class="badge badge-primary" id="unds`+i+`">-</span> | <span> PESO: </span> <span class="badge badge-success" id="peso`+i+`">-</span>
                        </b>
                        </button>
                        <div class="table-responsive collapse" id="collapseExample`+i+`">
                            <div class="row">   
                                <div class="col-sm-4 text-center">
                                    <div class="form-group c_form_group">
                                        <label>TIPO EMPAQUE</label>
                                        <select class="form-control" name="tipo_`+i+`" onInput="$('#tipo`+i+`').html($(this).val())">
                                            <option value="Seleccione" selected disabled></option>
                                            <option value="CAJA">CAJA</option>
                                            <option value="BOLSA">BOLSA</option>
                                            <option value="SACO">SACO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 text-center">
                                    <div class="form-group c_form_group">
                                        <label>UNIDADES EMPAQUE</label>
                                        <input type="number" class="form-control" name="unds_`+i+`" onInput="$('#unds`+i+`').html($(this).val()+' UNDS')">
                                    </div>
                                </div>
                                <div class="col-sm-4 text-center">
                                    <div class="form-group c_form_group">
                                        <label>PESO EMPAQUE</label>
                                        <input type="number" class="form-control" name="peso_`+i+`" step="any" onInput="$('#peso`+i+`').html($(this).val()+' KG')">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <br>`; 
        }
        empaque = empaque+`<button class="btn w-100 btn-primary" style="font-family:Century Gothic; color:white; font-weigth:bold;" id="generar">Generar Rotulos</button></form>`;
        $(".row2").html(empaque);
            console.log(ordenes, range);    
    }
</script>    

@endpush