<template>
<section>
    <div class="panel-header bg-primary-gradient">
      <div class="page-inner py-5">
        <div
          class="
            d-flex
            align-items-left align-items-md-center
            flex-column flex-md-row
          "
        >
          <div>
            <h2 class="text-white fw-bold">Administración de Despachos</h2>
            <h5 class="text-white">Lista todos los despachos</h5>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-body page-inner mt--5">
          <div class="card full-height">
            <div class="card-body">
              <div style="container-fluid">
                  <div class="m-4">
                        <a href="/readqr" class="btn btn-info"><i class="fa fa-plus"></i> Subir despacho</a>
                        <export-excel
                            class   = "btn btn-info"
                            :data   = "despachos"
                            :fields = "json_fields"
                            worksheet = "Reporte"
                            type = "xls"
                            name    = 'reporte_despachos'
                            >
                            
                            Descargar Excel
                        
                        </export-excel>

                  </div>
                  
                <div class="table-responsive" style="width:100%; !important">
                    <table id="tablaitems" class="display" cellspacing="0" style="width:100%; !important">
                        <thead style="background-color:#00a6ff; color:white; font-size: 1.2em !important;">
                            <tr>
                              <th>ID</th>
                                <th style="text-align:center;"># Factura</th>
                                <th style="text-align:center;"># Pedido</th>
                                <th style="text-align:center;"># Despacho</th>
                                <th style="text-align:center;">Rotulo</th>
                                <th style="text-align:center;">Fecha Picking</th>
                                <th style="text-align:center;">NIT</th>
                                <th style="text-align:center;">Destinatario</th>
                                <th style="text-align:center;">Dirección</th>
                                <th style="text-align:center;">Departamento</th>
                                <th style="text-align:center;">Ciudad</th>
                                <th style="text-align:center;">Phone</th>
                                <th style="text-align:center;">Num Caja</th>
                                <th style="text-align:center;">Cajas</th>
                                <th style="text-align:center;">Cantidad</th>
                                <th style="text-align:center;">Peso</th>
                                <th style="text-align:center;">Picking</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(despacho,indice) in despachos">
                                 <td>{{(indice+1)}}</td>
                                 <td style="text-align:center;">{{despacho.factura_numero}}</td>
                                 <td style="text-align:center;">{{despacho.npedido}}</td>
                                 <td style="text-align:center;">{{despacho.ndespacho}}</td>
                                 <td style="text-align:center;" v-if="despacho.filtro == 'CR'">California</td>
                                 <td style="text-align:center;" v-else>Nacional</td>
                                 <td style="text-align:center;">{{despacho.created_at}}</td>
                                 <td style="text-align:center;">{{despacho.nit}}</td>
                                 <td style="text-align:center;">{{despacho.destinatario}}</td>
                                 <td style="text-align:center;">{{despacho.direccion}}</td>
                                 <td style="text-align:center;">{{despacho.departamento}}</td>
                                 <td style="text-align:center;">{{despacho.ciudad}}</td>
                                 <td style="text-align:center;">{{despacho.phone}}</td>
                                 <td style="text-align:center;">{{despacho.ncaja}}</td>
                                 <td style="text-align:center;">{{despacho.cajas}}</td>
                                 <td style="text-align:center;">{{despacho.cantidad}}</td>
                                 <td style="text-align:center;">{{despacho.peso}}</td>
                                 <td style="text-align:center;">{{despacho.usuario}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
              </div>
            </div>
          </div>
    </div>  
</section>
</template>
<script>
import excel from 'vue-excel-export';
export default({
    mounted() {
        this.getDespachos();
        const today = new Date();
        this.date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
    },
    data() {
        return {
            json_fields: {
                'Tipo_Despacho': 'filtro',
                'Factura': 'factura_numero',
                'Pedido': 'npedido',
                'Despacho': 'ndespacho',
                'fechaPicking': 'created_at',
                'Nit':'nit',
                'Cliente':'destinatario',
                'Direccion':'direccion',
                'Departamento': 'departamento',
                'Ciudad':'ciudad',
                'Telefono':'phone',
                'ncaja': 'ncaja',
                'cajas':  'cajas',
                'cantidad': 'cantidad',
                'peso': 'peso',
                'usuario_picking': 'usuario',
            },
            json_meta: [
                [
                    {
                        'key': 'charset',
                        'value': 'utf-8'
                    }
                ]
            ],
            despachos: []
        }
    },
    methods: {
        getDespachos(){
           axios.get('/readqr/despacho/list')
            .then(response => {
              this.despachos = response.data
              $("#tablaitems").DataTable().destroy();
              this.iniTablesItems();
            })
        },
        iniTablesItems() {
            this.$nextTick(() => {
                $("#tablaitems").DataTable({
                    lengthMenu: [
                        [10, 15, 20, 25, 30, 100],
                        [10, 15, 20, 25, 30, 100],
                    ],
                });
            });
            },
    },


})
</script>
