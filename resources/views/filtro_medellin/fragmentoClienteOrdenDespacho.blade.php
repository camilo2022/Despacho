


<div class="container-fluid mt-2">
    <div class="card" style=" font-family:Century Gothic;">
      <div class="card-header text-center" style="background-color: #333; color:white; font-weigth:bold;">
        CONSULTA ORDENES DESPACHO POR CLIENTE
      </div>
        <div class="card-body">
            <div class="table-responsive">

                        <table id="ordenesDespacho" class="table tableOrdenesDespacho display nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Orden</th>
                                    <th>Detalle</th>
                                    <th>Pedido</th>
                                    <th>Amarrador</th>
                                    <th>Consecutivo</th>
                                    <th>Despacho</th>
                                    <th>Nit</th>
                                    <th>Cliente</th>
                                    <th>Zona</th>
                                    <th>Ciudad</th>
                                    <th>Direccion</th>
                                    <th>Referencia</th>
                                    <th>T04</th>
                                    <th>T06</th>
                                    <th>T08</th>
                                    <th>T10</th>
                                    <th>T12</th>
                                    <th>T14</th>
                                    <th>T16</th>
                                    <th>T18</th>
                                    <th>T20</th>
                                    <th>T22</th>
                                    <th>T28</th>
                                    <th>T30</th>
                                    <th>T32</th>
                                    <th>T34</th>
                                    <th>T36</th>
                                    <th>T38</th>
                                    <th>Estado Orden</th>
                                    <th>Estado Detalle Orden</th>
                                    <th>Fecha</th>
                                    <th>Despachar</th>
                                    <th>Filtrador</th>
                                    <th>Vendedor</th>
                                </tr>
                            </thead>

                            
                       
                            <tbody>
                                @foreach($consulta as $cons)
                                    <tr>
                                        <td>{{ $cons->id_od }}</td>
                                        <td>{{ $cons->id_dod }}</td>
                                        <td>{{ $cons->id_pedido }}</td>
                                        <td>{{ $cons->id_amarrador }}</td>
                                        <td>{{ $cons->consecutivo }}</td>
                                        <td>
                                            @if($cons->despachor == 4)
                                                NEGADO
                                            @elseif($cons->despachor == 1)
                                                DESPACHADO
                                            @elseif($cons->despachor == 0)
                                                EN CURSO
                                            @elseif($cons->despachor == 12)
                                                COMPROMETIDO
                                            @endif
                                        </td>
                                        <td>{{ $cons->nit }}</td>
                                        <td>{{ $cons->cliente }}</td>
                                        <td>{{ $cons->zona }}</td>
                                        <td>{{ $cons->ciudad }}</td>
                                        <td>{{ $cons->direccion }}</td>
                                        <td>{{ $cons->referencia }}</td>
                                        <td>{{ $cons->t04 }}</td>
                                        <td>{{ $cons->t06 }}</td>
                                        <td>{{ $cons->t08 }}</td>
                                        <td>{{ $cons->t10 }}</td>
                                        <td>{{ $cons->t12 }}</td>
                                        <td>{{ $cons->t14 }}</td>
                                        <td>{{ $cons->t16 }}</td>
                                        <td>{{ $cons->t18 }}</td>
                                        <td>{{ $cons->t20 }}</td>
                                        <td>{{ $cons->t22 }}</td>
                                        <td>{{ $cons->t28 }}</td>
                                        <td>{{ $cons->t30 }}</td>
                                        <td>{{ $cons->t32 }}</td>
                                        <td>{{ $cons->t34 }}</td>
                                        <td>{{ $cons->t36 }}</td>
                                        <td>{{ $cons->t38 }}</td>
                                        <td>{{ $cons->estado_orden }}</td>
                                        <td>{{ $cons->estado_detalle_orden }}</td>
                                        <td>{{ $cons->fecha }}</td>
                                        <td>{{ $cons->despachar }}</td>
                                        <td>{{ $cons->filtrador }}</td>
                                        <td>{{ $cons->vendedor }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                </div>
            </div>
        </div>
    </div>
