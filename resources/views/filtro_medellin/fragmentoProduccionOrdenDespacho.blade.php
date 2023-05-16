


<div class="container-fluid mt-2">
    <div class="card" style=" font-family:Century Gothic;">
      <div class="card-header text-center" style="background-color: #333; color:white; font-weigth:bold;">
        CONSULTA REPORTE PRODUCCION
      </div>
        <div class="card-body">
            <div class="table-responsive">

                        <table id="ordenesDespacho" class="table tableOrdenesDespacho display nowrap" style="">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Id Amarrador</th>
                                    <th>Estado Pedido</th>
                                    <th>Estado Cartera</th>
                                    <th>Codigo</th>
                                    <th>Fecha</th>
                                    <th>Vendedor</th>
                                    <th>Ped</th>
                                    <th>Obser</th>
                                    <th>Nit</th>
                                    <th>Cliente</th>
                                    <th>Zona</th>
                                    <th>Direccion</th>
                                    <th>Zona</th>
                                    <th>Ciudad</th>
                                    <th>Referencia</th>
                                    <th>Color</th>
                                    <th>Identificador</th>
                                    <th>04</th>
                                    <th>06</th>
                                    <th>08</th>
                                    <th>10</th>
                                    <th>12</th>
                                    <th>14</th>
                                    <th>16</th>
                                    <th>18</th>
                                    <th>20</th>
                                    <th>22</th>
                                    <th>24</th>
                                    <th>28</th>
                                    <th>30</th>
                                    <th>32</th>
                                    <th>34</th>
                                    <th>36</th>
                                    <th>38</th>
                                    <th>Total</th>
                                    <th>Marca</th>
                                    <th>Pedido</th>
                                    <th>Observacion</th>
                                    <th>Estado despacho</th>
                                    <th>Consecutivo Orden Despacho</th>
                                    <th>Fecha despacho</th>
                                    <th>Fecha detalle</th>
                                    <th>Fecha pedido</th>
                                    <th>Filtrador</th>
                                </tr>
                            </thead>

                            
                       
                            <tbody>
                                @foreach($produccion as $p)
                                    <tr>
                                        <td>{{ $p->tipo }}</td>
                                        <td>{{ $p->idamarrador }}</td>
                                        <td>{{ $p->estado }}</td>
                                        <td>{{ $p->aprobado }}</td>
                                        <td>{{ $p->nit }}</td>
                                        <td>{{ $p->fecha }}</td>
                                        <td>{{ $p->vendedor }}</td>
                                        <td>{{ $p->nped }}</td>
                                        <td>{{ $p->observacion }}</td>
                                        <td>{{ $p->nit }}</td>
                                        <td>{{ $p->nombre }}</td>
                                        <td>{{ $p->z }}</td>
                                        <td>{{ $p->direccion }}</td>
                                        <td>{{ $p->zona }}</td>
                                        <td>{{ $p->ciudad }}</td>
                                        <td>{{ $p->referencia }}</td>
                                        <td>{{ $p->color }}</td>
                                        <td>{{ $p->identificador }}</td>
                                        <td>{{ $p->t04 }}</td>
                                        <td>{{ $p->t06 }}</td>
                                        <td>{{ $p->t08 }}</td>
                                        <td>{{ $p->t10 }}</td>
                                        <td>{{ $p->t12 }}</td>
                                        <td>{{ $p->t14 }}</td>
                                        <td>{{ $p->t16 }}</td>
                                        <td>{{ $p->t18 }}</td>
                                        <td>{{ $p->t20 }}</td>
                                        <td>{{ $p->t22 }}</td>
                                        <td>{{ $p->t24 }}</td>
                                        <td>{{ $p->t28 }}</td>
                                        <td>{{ $p->t30 }}</td>
                                        <td>{{ $p->t32 }}</td>
                                        <td>{{ $p->t34 }}</td>
                                        <td>{{ $p->t36 }}</td>
                                        <td>{{ $p->t38 }}</td>
                                        <td>{{ $p->total}}</td>
                                        <td>{{ $p->marca }}</td>
                                        <td>{{ $p->tipo }}</td>
                                        <td>{{ $p->obs }}</td>
                                        <td>{{ $p->despachor }}</td>
                                        <td>{{ $p->consecutivo }}</td>
                                        <td>{{ $p->fdespacho }}</td>
                                        <td>{{ $p->fitem }}</td>
                                        <td>{{ $p->fpedido }}</td>
                                        <td>{{ $p->filtrador }}</td>
                                    </tr>                             
                                    @endforeach                         
                                </tbody>
                        </table>

                </div>
            </div>
        </div>
    </div>
