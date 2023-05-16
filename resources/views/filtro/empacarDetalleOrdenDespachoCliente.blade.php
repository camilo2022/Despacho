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
        EMPACADO DE ORDEN DE DESPACHO
        </div>
        <div class="card-body">
            <div class="row m-2">
                <div class="col-12">
                    <div class="card w-100">
                        <div class="card-header text-center" style="background:#333; color:white;">
                            INFORMACION ORDEN DE DESPACHO
                        </div>
                        <div class="card-body text-center">
                            <strong> <h1><span>CONSECUTIVO {{ $consulta_orden_despacho[0]->consecutivo }}</span></h1> </strong>
                            <strong> <span>EMPACADOS: </span> <span class="@if($empacar==0) badge badge-danger @elseif($empacar==$despachar) badge badge-success @else badge badge-warning @endif" id="num_total_emp">{{ $empacar }}</span> de <span class="badge badge-success" id="num_total_des">{{ $despachar }}</span> </strong>
                            @if(empty($empaque) && $empacar==$despachar)
                            <form method="POST" action="{{route('filtro.listado.ordenes.clientes.empacar.picking.finalizar',$consulta[0]->id)}}">
                                @csrf
                                <button type="submit" class="mt-2 btn btn-success w-100" style="font-weight:bold;"> <b> Finalizar Empacado </b></button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @if(empty($empaque))
            @if($empacar!=$despachar)
                <div class="row m-2">
                    <div class="col-12">
                        <div class="card w-100">
                            <div class="card-header text-center" style="background:#333; color:white;">
                                CREAR EMPAQUE PARA ORDEN DE DESPACHO
                            </div>
                            <div class="card-body text-center">
                                <form id="crearCaja" method="POST" action="{{route('filtro.listado.ordenes.clientes.empacar.picking.crear',$consulta_orden_despacho[0]->id)}}" onsubmit="crearCaja(event)">
                                    @csrf
                                    <input type="hidden" name="tipo" id="tipo" value="CAJA">
                                    <button type="submit" class="mt-2 btn btn-info w-100" style="font-weight:bold;"><i class="fa fa-box"></i> <b> Caja </b></button>      
                                </form>
                                <form id="crearBolsa" method="POST" action="{{route('filtro.listado.ordenes.clientes.empacar.picking.crear',$consulta_orden_despacho[0]->id)}}" onsubmit="crearBolsa(event)">
                                    @csrf
                                    <input type="hidden" name="tipo" id="tipo" value="BOLSA">
                                    <button type="submit" class="mt-2 btn btn-secondary w-100" style="font-weight:bold;"><i class="fa fa-box"></i><b> Bolsa </b></button>                         
                                </form>    
                                <form id="crearSaco" method="POST" action="{{route('filtro.listado.ordenes.clientes.empacar.picking.crear',$consulta_orden_despacho[0]->id)}}" onsubmit="crearSaco(event)">
                                    @csrf
                                    <input type="hidden" name="tipo" id="tipo" value="SACO">
                                    <button type="submit" class="mt-2 btn btn-primary w-100" style="font-weight:bold;"><i class="fa fa-box"></i><b> Saco </b></button>                         
                                </form> 
                            </div>
                        </div>
                    </div>
                </div>     
            @endif   
                <div class="row m-2">
                    <div class="col-12">
                        <div class="card w-100">
                            <div class="card-header text-center" style="background:#333; color:white;">
                                EMPAQUES
                            </div>
                            <div class="card-body text-center">
                                <div class="table-responsive">
                                    
                                    @for ($i = 0; $i < count($empaques); $i++)
                                    <div>
                                        
                                        <button  type="button" class="mb-2 btn w-100 collapsed btn-primary" data-toggle="collapse"  data-target="#collapseExample{{ $i }}" aria-expanded="false" aria-controls="#collapseExample{{ $i }}">
                                            
                                        <b>    
                                            <div class="table-responsive"> 
                                                <i class="fa fa-box"></i>
                                                <span>{{$empaques[$i]->tipo}} #</span> <span class="badge badge-warning">{{$empaques[$i]->id}}</span> | <span> PESO: </span> <span class="badge badge-success">  {{$empaques[$i]->peso}}</span>
                                            </div>
                                        </b>
                                        </button>
                                        <div class="table-responsive collapse" id="collapseExample{{ $i }}">
                
                                            <div class="col-12">
                                                @if($empacar!=$despachar)
                                                <div class="card-body text-center">
                                                    <form id="" method="POST" action="{{route('filtro.listado.ordenes.clientes.empacar.picking.modificar',$empaques[$i]->id)}}">
                                                        @csrf
                                                        <button type="submit" class="mt-2 mb-2 btn btn-info w-100" style="font-weight:bold;">
                                                            <i class="fa fa-solid fa-pen"></i><b><span>MODIFICAR</span></b>
                                                        </button>
                                                    </form>           
                                                </div>
                                                @endif
                                            @for ($j = 0; $j < count($empaques[$i]->detalles); $j++)
                                            <div>
                                                <button  type="button" class="mb-2 btn w-100 collapsed" style="background-color:#23282e; color:white;" data-toggle="collapse"  data-target="#collapseExample{{ $j+100 }}" aria-expanded="false" aria-controls="#collapseExample{{ $j+100 }}">
                                                <b> 
                                                    <div class="table-responsive"> 
                                                    <i class="fa fa-solid fa-paperclip"></i>
                                                    <span>{{$empaques[$i]->detalles[$j]->referencia}}</span> | <span class="badge badge-light">{{$empaques[$i]->detalles[$j]->sum}} UND</span>
                                                    </div>
                                                </b>
                                                </button>
                                                <div class="table-responsive collapse" id="collapseExample{{ $j+100 }}">
                                                <table class="table text-center"> 
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">TALLA</th>
                                                            <th scope="col">CE</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if($empaques[$i]->detalles[$j]->t04 != 0)
                                                        <tr class="04">
                                                            <th scope="col">T04</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->t04}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->t06 != 0)
                                                        <tr class="06">
                                                            <th scope="col">T06</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->t06}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->t08 != 0)
                                                        <tr class="08">
                                                            <th scope="col">T08</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->t08}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->t10 != 0)
                                                        <tr class="10">
                                                            <th scope="col">T10</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->t10}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->t12 != 0)
                                                        <tr class="12">
                                                            <th scope="col">T12</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->t12}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->t14 != 0)
                                                        <tr class="14">
                                                            <th scope="col">T14</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->t14}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->t16 != 0)
                                                        <tr class="16">
                                                            <th scope="col">T16</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->t16}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->t18 != 0)
                                                        <tr class="18">
                                                            <th scope="col">T18</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->t18}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->t20 != 0)
                                                        <tr class="20">
                                                            <th scope="col">T20</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->t20}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->t22 != 0)
                                                        <tr class="22">
                                                            <th scope="col">T22</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->t22}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->t24 != 0)
                                                        <tr class="24">
                                                            <th scope="col">T24</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->t24}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->t26 != 0)
                                                        <tr class="26">
                                                            <th scope="col">T26</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->t26}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->t28 != 0)
                                                        <tr class="28">
                                                            <th scope="col">T28</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->t28}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->t30 != 0)
                                                        <tr class="30">
                                                            <th scope="col">T30</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->t30}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->t32 != 0)
                                                        <tr class="32">
                                                            <th scope="col">T32</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->t32}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->t34 != 0)
                                                        <tr class="34">
                                                            <th scope="col">T34</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->t34}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->t36 != 0)
                                                        <tr class="36">
                                                            <th scope="col">T36</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->t36}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->t38 != 0)
                                                        <tr class="38">
                                                            <th scope="col">T38</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->t38}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->xs != 0)
                                                        <tr class="xs">
                                                            <th scope="col">XS</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->xs}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->s != 0)
                                                        <tr class="s">
                                                            <th scope="col">S</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->ts}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->m != 0)
                                                        <tr class="m">
                                                            <th scope="col">M</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->m}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->l != 0)
                                                        <tr class="l">
                                                            <th scope="col">L</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->l}}" disabled></td>
                                                        </tr>
                                                        @endif
                                                        @if($empaques[$i]->detalles[$j]->xl != 0)
                                                        <tr class="xl">
                                                            <th scope="col">XL</th>
                                                            <td><input type="number" value="{{$empaques[$i]->detalles[$j]->xl}}" disabled></td>
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
                                    @endfor
                                    
                                </div>                         
                            </div>
                        </div>
                    </div>
                </div>
        @else
            <div class="row m-2">
                <div class="col-12">
                    <div class="card w-100">
                        <div class="card-header text-center" style="background:#333; color:white;">
                            EMPAQUE # {{$empaque[0]->id}} - TIPO: {{$empaque[0]->tipo}}
                        </div>
                        <div class="card-body text-center">
                            <form id="cancelarEmpaque" method="POST" action="{{route('filtro.listado.ordenes.clientes.empacar.picking.cancelar',$empaque[0]->id)}}" onsubmit="cancelarEmpaque(event)">
                                @csrf
                                <button type="submit" class="mt-2 btn btn-danger w-100" style="font-weight:bold;"> <b> Cancelar Empaque </b></button>      
                            </form>
                            <form id="cerrarEmpaque" method="POST" action="{{route('filtro.listado.ordenes.clientes.empacar.picking.cerrar',$empaque[0]->id)}}" onsubmit="cerrarEmpaque(event)">
                                @csrf
                                <input type="hidden" name="peso" id="peso" value="">
                                <button type="submit" class="mt-2 btn btn-warning w-100" style="font-weight:bold;"><b> Cerrar Empaque </b></button>                         
                            </form>
                        </div>
                    </div>
                </div>
            </div>    
            
            <div class="table-responsive">
                @for ($i = 0; $i < count($consulta_detalles); $i++)
                    <div>
                        <button  type="button" class="mb-2 btn w-100 collapsed" style="background-color:#23282e; color:white;" data-toggle="collapse"  data-target="#collapseExample{{ $i }}" aria-expanded="false" aria-controls="#collapseExample{{ $i }}">
                        <b> 
                            <div class="table-responsive"> 
                            @if($consulta_detalles[$i]->sum_e != $consulta_detalles[$i]->sum_d)
                                <span>{{$consulta_detalles[$i]->referencia}}</span> <span class="badge badge-light" id="{{$consulta_detalles[$i]->referencia}}-faltan">{{$consulta_detalles[$i]->sum_e}}</span> de <span class="badge badge-warning" id="{{$consulta_detalles[$i]->referencia}}-total">{{$consulta_detalles[$i]->sum_d}}</span> | <span class="badge badge-danger" id="{{$consulta_detalles[$i]->referencia}}-badge"> Hace falta</span>
                            @else
                                <span>{{$consulta_detalles[$i]->referencia}}</span> <span class="badge badge-light" id="{{$consulta_detalles[$i]->referencia}}-faltan">{{$consulta_detalles[$i]->sum_e}}</span> de <span class="badge badge-warning" id="{{$consulta_detalles[$i]->referencia}}-total">{{$consulta_detalles[$i]->sum_d}}</span> | <span class="badge badge-success" id="{{$consulta_detalles[$i]->referencia}}-badge"> Completado</span>
                            @endif
                            </div>
                        </b>
                        </button>
                        <div class="table-responsive collapse" id="collapseExample{{ $i }}">

                            <div class="col-12">
                                <input id="{{$consulta_detalles[$i]->id}}" onkeyup="pickingConteoTallas(this,event,'{{$consulta_detalles[$i]->referencia}}')" type="text" class="mb-2 w-100 form-control" style="border: 1px solid black !important;" value="">
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
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-04-CP" value="{{$consulta_detalles[$i]->t04_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-04-CD" value="{{$consulta_detalles[$i]->t04_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t06_d != 0)
                                    <tr class="06">
                                        <th scope="col">T06</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-06-CP" value="{{$consulta_detalles[$i]->t06_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-06-CD" value="{{$consulta_detalles[$i]->t06_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t08_d != 0)
                                    <tr>
                                        <th scope="col">T08</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-08-CP" value="{{$consulta_detalles[$i]->t08_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-08-CD" value="{{$consulta_detalles[$i]->t08_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t10_d != 0)
                                    <tr>
                                        <th scope="col">T10</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-10-CP" value="{{$consulta_detalles[$i]->t10_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-10-CD" value="{{$consulta_detalles[$i]->t10_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t12_d != 0)
                                    <tr>
                                        <th scope="col">T12</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-12-CP" value="{{$consulta_detalles[$i]->t12_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-12-CD" value="{{$consulta_detalles[$i]->t12_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t14_d != 0)
                                    <tr>
                                        <th scope="col">T14</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-14-CP" value="{{$consulta_detalles[$i]->t14_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-14-CD" value="{{$consulta_detalles[$i]->t14_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t16_d != 0)
                                    <tr>
                                        <th scope="col">T16</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-16-CP" value="{{$consulta_detalles[$i]->t16_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-16-CD" value="{{$consulta_detalles[$i]->t16_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t18_d != 0)
                                    <tr>
                                        <th scope="col">T18</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-18-CP" value="{{$consulta_detalles[$i]->t18_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-18-CD" value="{{$consulta_detalles[$i]->t18_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t20_d != 0)
                                    <tr>
                                        <th scope="col">T20</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-20-CP" value="{{$consulta_detalles[$i]->t20_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-20-CD" value="{{$consulta_detalles[$i]->t20_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t22_d != 0)
                                    <tr>
                                        <th scope="col">T22</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-22-CP" value="{{$consulta_detalles[$i]->t22_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-22-CD" value="{{$consulta_detalles[$i]->t22_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t24_d != 0)
                                    <tr>
                                        <th scope="col">T24</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-24-CP" value="{{$consulta_detalles[$i]->t24_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-24-CD" value="{{$consulta_detalles[$i]->t24_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t26_d != 0)
                                    <tr>
                                        <th scope="col">T26</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-26-CP" value="{{$consulta_detalles[$i]->t26_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-26-CD" value="{{$consulta_detalles[$i]->t26_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t28_d != 0)
                                    <tr>
                                        <th scope="col">T28</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-28-CP" value="{{$consulta_detalles[$i]->t28_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-28-CD" value="{{$consulta_detalles[$i]->t28_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t30_d != 0)
                                    <tr>
                                        <th scope="col">T30</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-30-CP" value="{{$consulta_detalles[$i]->t30_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-30-CD" value="{{$consulta_detalles[$i]->t30_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t32_d != 0)
                                    <tr>
                                        <th scope="col">T32</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-32-CP" value="{{$consulta_detalles[$i]->t32_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-32-CD" value="{{$consulta_detalles[$i]->t32_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t34_d != 0)
                                    <tr>
                                        <th scope="col">T34</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-34-CP" value="{{$consulta_detalles[$i]->t34_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-34-CD" value="{{$consulta_detalles[$i]->t34_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t36_d != 0)
                                    <tr>
                                        <th scope="col">T36</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-36-CP" value="{{$consulta_detalles[$i]->t36_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-36-CD" value="{{$consulta_detalles[$i]->t36_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->t38_d != 0)
                                    <tr>
                                        <th scope="col">T38</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-38-CP" value="{{$consulta_detalles[$i]->t38_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-38-CD" value="{{$consulta_detalles[$i]->t38_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->xs_d != 0)
                                    <tr>
                                        <th scope="col">XS</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-XS-CP" value="{{$consulta_detalles[$i]->xs_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-XS-CD" value="{{$consulta_detalles[$i]->xs_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->s_d != 0)
                                    <tr>
                                        <th scope="col">S</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-S-CP" value="{{$consulta_detalles[$i]->s_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-S-CD" value="{{$consulta_detalles[$i]->s_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->m_d != 0)
                                    <tr>
                                        <th scope="col">M</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-M-CP" value="{{$consulta_detalles[$i]->m_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-M-CD" value="{{$consulta_detalles[$i]->m_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->l_d != 0)
                                    <tr>
                                        <th scope="col">L</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-L-CP" value="{{$consulta_detalles[$i]->l_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-L-CD" value="{{$consulta_detalles[$i]->l_d}}" disabled></td>
                                    </tr>
                                    @endif
                                    @if($consulta_detalles[$i]->xl_d != 0)
                                    <tr>
                                        <th scope="col">XL</th>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-XL-CP" value="{{$consulta_detalles[$i]->xl_e}}" disabled></td>
                                        <td><input type="number" id="{{$consulta_detalles[$i]->referencia}}-XL-CD" value="{{$consulta_detalles[$i]->xl_d}}" disabled></td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endfor
            </div>   
        @endif
            
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
            let consulta = {!! json_encode($consulta) !!};
            id_orden_empacado = consulta[0].id;
            let empaque = {!! json_encode($empaque) !!};
            empaque = empaque[0].id;
            let id_orden_despacho = consulta_orden_despacho[0].id;
            let url = URLdominio + id_orden_despacho +"/add";
            console.log(id_orden_empacado);
            let token = $("meta[name='csrf-token']").attr("content");
            input.value.toUpperCase();
            let id_detalle_orden_despacho = input.id;
            let arrayReferencia = input.value.trim().split("-");
            let referencia = "";
            let talla = "";
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
            let falta_total = document.getElementById('num_total_emp').innerHTML;
            let total_total = document.getElementById('num_total_des').innerHTML;
            let cp = $('#'+referencia+'-'+talla+'-CP').val();
            let cd = $('#'+referencia+'-'+talla+'-CD').val();
            
                if(cp == undefined || cp == null || cp == ""){
                    alertify.error('¡La talla no existe!');
                }else if(cp == cd){
                    alertify.warning('Picking completo en T'+talla);
                }else{
                    $('#'+referencia+'-'+talla+'-CP').val(parseInt(cp)+1);
                    faltan = document.getElementById(referencia+'-faltan').innerHTML = parseInt(faltan)+1
                    falta_total = document.getElementById('num_total_emp').innerHTML = parseInt(falta_total)+1
                    if(parseInt(faltan) == parseInt(total)){
                        const badgeElement = document.getElementById(referencia + "-badge");
                        badgeElement.classList.remove("badge-danger");
                        badgeElement.classList.add("badge-success");
                        document.getElementById(referencia+'-badge').innerHTML = "Completado"
                    }
                    if(parseInt(falta_total) != 0){
                        const badgeElement = document.getElementById("num_total_emp");
                        badgeElement.classList.remove("badge-danger");
                        badgeElement.classList.add("badge-warning");
                    }
                    if(parseInt(falta_total) == parseInt(total_total)){
                        const badgeElement = document.getElementById("num_total_emp");
                        badgeElement.classList.remove("badge-warning");
                        badgeElement.classList.add("badge-success");
                    }
                    if(talla != "xs" && talla != "XS" && talla != "s" && talla != "S" && talla != "m" && talla != "M"&& talla != "l" && talla != "L" && talla != "xl" && talla != "XL"){
                        talla = "t"+talla;
                    }
                    let dataSend = {
                        id_orden_empacado: id_orden_empacado,
                        id_detalle_orden_despacho:id_detalle_orden_despacho,
                        id_empaque:empaque,
                        talla: talla,
                        _token:token
                    };
                        $.ajax({
                        url: url,
                        type: 'POST',
                        data: dataSend,
                        dataType: 'json',
                            success: function(data){
                                alertify.success(data["msg"]);
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

    function crearCaja(event){
        event.preventDefault(); 
        swal({
            title: "¿Está seguro?",
            text: "¡Se creará una caja para empezar a empacar!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                document.getElementById('crearCaja').submit();
            } 
        });
    }

    function crearBolsa(event){
        event.preventDefault();
        swal({
            title: "¿Está seguro?",
            text: "¡Se creará una bolsa para empezar a empacar!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                document.getElementById('crearBolsa').submit();
            } 
        });
    }

    function crearSaco(event){
        event.preventDefault();
        swal({
            title: "¿Está seguro?",
            text: "¡Se creará una saco para empezar a empacar!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                document.getElementById('crearSaco').submit();
            } 
        });
    }

    function cancelarEmpaque(event){
        event.preventDefault(); 
        swal({
            title: "¿Está seguro?",
            text: "¡Se cancelará el empaque! \n Se eliminará los regitros de este empaque.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                document.getElementById('cancelarEmpaque').submit();
            } 
        });
    }

    function cerrarEmpaque(event){
        event.preventDefault();
        swal({
            title: "¿Está seguro?",
            text: "¡Se cerrará el empaque! \n Se cerrará este empaque y no podrá seguir ingresando prendas.\n Antes de continuar debe ingresar el peso del empaque.",
            icon: "warning",
            content: "input",
            buttons: true,
            dangerMode: true,
        })
        .then((value) => {

            if(isNaN(Number(value))){
                swal('Error', 'Debe ingresar un valor numérico', 'error');
            }else if(value.trim() == "" || value == null || value == undefined){
                swal('Error', 'Debe ingresar un valor numérico', 'error');
            }else{
                    $("#peso").val(value.trim());
                    document.getElementById('cerrarEmpaque').submit();
                }
        });

        
    }
</script>    

@endpush