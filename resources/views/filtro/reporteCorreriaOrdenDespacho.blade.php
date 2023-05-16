@extends('layouts.appp')

@push('custom-css')
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
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
</style>
@endpush

@section('content')
<div class="container-fluid mt-2">
    <div class="card" style=" font-family:Century Gothic;">
      <div class="card-header text-center" style="background-color: #333; color:white; font-weigth:bold;">
        INFORME ORDENES DE DESPACHO POR CORRERIA
        </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-6 text-center">
                                <div class="form-group c_form_group">
                                    <label>Correrias</label>
                                    <select name="correria" id="correria" class=" required form-control" tabindex="0" required="">
                                        <option value="" selected="selected">-</option>
                                        @foreach ($correrias as $c)
                                            <option style="font-weight: bold" value="{{$c->codigo}}">{{$c->nombre." / ".$c->fechaini." - ".$c->fechafin}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3"></div>
                        </div>
                        <button class="btn w-100 btn-success" style="font-family:Century Gothic; color:white; font-weigth:bold;" id="generar">Generar Consulta</button>
                     </div>
                     
                </div>
            </div>
        </div>
    </div>
    <div id="tablar">

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
<script src="https://harvesthq.github.io/chosen/chosen.jquery.js"></script>
<script>
    function getAbsolutePath() {
      var loc = window.location;
      var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);
      return loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));
    }
    
    $(document).ready(function(){
        $("#generar").click(function() {
            var URLdominio = getAbsolutePath();
            var url = URLdominio + "correria/generar";
            var correria = $("#correria").val();
            
            if(correria == null || correria == "" || correria == undefined){
                alertify.error('Correria requerida');
            }else{
                $("#tablar").html( `<div class="container-fluid mt-2">
                                    <div class="card" style=" font-family:Century Gothic;">
                                    <div class="card-header text-center" style="background-color: #333; color:white; font-weigth:bold;">
                                        CONSULTA ORDENES DESPACHO POR CORRERIA
                                    </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                            <img src="/img/cargando.gif" alt="loading" />
                                            </div>
                                        </div>
                                    </div>
                                </div>`);
                setTimeout(() => {
                    $.ajax({
                            url: url,
                            type: 'GET',
                            data: {
                                correria: correria
                            },
                            dataType: 'json',
                            success: function(data){
                                if(data[0] == 0){
                                    alertify.error(data[1]);
                                    $('#tablar').html("");
                                }
                                if(data[0] == 1){
                                    alertify.success(data[1]);
                                    $('#tablar').html(data[2]);
                                    $(document).ready(function(){
                                        $('#ordenesDespacho').DataTable({
                                            responsive: {
                                            details: {
                                                    type: 'column'
                                                }
                                            },
                                            columnDefs: [ {
                                                className: 'dtr-control',
                                                orderable: false,
                                                targets:   0
                                            } ],
                                            order: [ 1, 'asc' ],
                                            dom: 'Blfrtip',
                                            buttons: [{
                                                    extend: 'excel',
                                                    footer: true,
                                                    title: 'Organización Bless',
                                                    filename: 'ORDENES DE DESPACHO CORRERIA: '+correria,
                                                    text: '<i class="fa fa-light fa-file-excel"></i>'
                                                },
                                                {
                                                    extend: 'pdf',
                                                    footer: true,
                                                    title: 'Organización Bless',
                                                    filename: 'ORDENES DE DESPACHO CORRERIA: '+correria,
                                                    text: '<i class="fa fa-file-pdf"></i>'
                                            }]
                                        });
                                    });
                                }
                            }
                    })
                }, 1000);
            }
        })
    })
</script>    

@endpush