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
        font-size: 15px;
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
            CARGAR ARCHIVO DE REFERENCIA CON EXISTENCIA BLESS MANUFACTURING
        </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="card-body">
                        @if(session('msg'))
                            <div class="alert alert-{{session('alert')}} text-center">
                                {{ session('msg') }}
                            </div>
                        @endif
                        <div class="row">
                                <div class="col-md-12 mb-2 text-center">
                                    <a type="button" class="btn btn-info" style="width: 100%"
                                    href="../../../public/Formato_Inventario_Disponible_Bless_Manufacturing.xlsx"
                                    >Descargar Formato</a>
                                </div>
                        </div>
                        <div class="row">
                                <div class="col-md-12 mb-2">
                                    <form method="POST" id="formulario" action="{{route('filtro.cargar.store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
                                        @csrf
                                    <div class="form-group c_form_group">
                                        <label>Cargar archivo de inventario de Bless Manufacturing</label>
                                        <input type="file" class="form-control" name="archivo" id="archivo" accept=".xlsx,.xls" require >
                                    </div>
                                </div>
                        </div> 
                        <div class="row">
                                <div class="col-md-12 mb-2 text-center">
                                    <button disabled="disabled" type="submit" class="btn btn-success" style="width: 100%" id="cargar">Cargar Archivo</button>
                                </form>
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
    function getAbsolutePath() {
      var loc = window.location;
      var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);
      return loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));
    }
    $(document).ready(function(){
        $(".wrapper").addClass("sidebar_minimize");
        $("#archivo").change(function(){
            $("#cargar").prop("disabled", this.files.length == 0);
        });
    })
</script>    

@endpush