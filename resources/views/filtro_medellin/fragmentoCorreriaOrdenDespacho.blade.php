


<div class="container-fluid mt-2">
    <div class="card" style=" font-family:Century Gothic;">
      <div class="card-header text-center" style="background-color: #333; color:white; font-weigth:bold;">
        CONSULTA ORDENES DESPACHO POR CORRERIA
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
                                    <th>Sucursal</th>
                                    <th>Departamento</th>
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
                                    <th>TOTAL</th>
                                    <th>Estado Orden</th>
                                    <th>Estado Detalle Orden</th>
                                    <th>Fecha</th>
                                    <th>Despachar</th>
                                    <th>Filtrador</th>
                                    <th>Vendedor</th>
                                    <th>Correria</th>
                                </tr>
                            </thead>

                            
                       
                            <tbody>
                                @foreach($ordenes as $cons)
                                    <tr>
                                        <td>{{ $cons->id_od }}</td>
                                        <td>{{ $cons->id_dod }}</td>
                                        <td>{{ $cons->id_pedido }}</td>
                                        <td>{{ $cons->id_amarrador }}</td>
                                        <td>{{ $cons->consecutivo }}</td>
                                        <td>{{ $cons->despacho }}</td>
                                        <td>{{ $cons->nit }}</td>
                                        <td>{{ $cons->cliente }}</td>
                                        <td>{{ $cons->sucursal }}</td>
                                        <td>{{ $cons->departamento }}</td>
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
                                        <td>{{$cons->t04+$cons->t06+$cons->t08+$cons->t10+$cons->t12+$cons->t14+$cons->t16+$cons->t18+$cons->t20+$cons->t22+$cons->t28+$cons->t30+$cons->t32+$cons->t34+$cons->t36+$cons->t38}}</td>
                                        <td>{{ $cons->estado_orden }}</td>
                                        <td>{{ $cons->estado_detalle_orden }}</td>
                                        <td>{{ $cons->fecha }}</td>
                                        <td>{{ $cons->despachar }}</td>
                                        <td>{{ $cons->filtrador }}</td>
                                        <td>{{ $cons->vendedor }}</td>
                                        <td>{{ $cons->correria }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                </div>
            </div>
        </div>
    </div>
