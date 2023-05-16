<template>
    <section >
        <div class="container-fluid">
            <div class="card m-2">
                <div class="card-header" style="background-color:#007bff !important; color:white;">
                    <center><h3><b>Listado Pendiente de Facturación</b> <b>{{fechaActual}}</b></h3></center>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaOrdenes" style="width:105%;" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Consecutivo</th>
                                    <th style="width:4%;">Cliente</th>
                                    <th>Nit</th>
                                    <th>Prioridad</th>
                                    <th style="width:3%;">Ciudad</th>
                                    <th>Direccion</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="orden in ordenesD">
                                    <td>{{orden.consecutivo}}</td>
                                    <td>{{orden.cliente}}</td>
                                    <td>{{orden.nit}}</td>
                                    <td>{{orden.prioridad}}</td>
                                    <td>{{orden.ciudad}}</td>
                                    <td>{{orden.direccion}}</td>
                                    <td><span v-if="orden.estado == 1" class="badge badge-danger" style="background-color:#083aab !important;">Cargado</span>
                                    <span v-if="orden.estado == 2" class="badge badge-warning">En Alistamiento</span>
                                    <span v-if="orden.estado == 3" class="badge badge-warning" style="background-color:#ef6f08 !important">Alistado</span>
                                    <span v-if="orden.estado == 4" class="badge badge-info">Empacando</span>
                                    <span v-if="orden.estado == 5" class="badge badge-success" style="background-color:#ef6f08 !important">Empacado</span>
                                    <span v-if="orden.estado == 6" class="badge badge-danger">Espera Verificacion</span>
                                    <span v-if="orden.estado == 7" class="badge badge-danger">Rechazado</span>
                                    <span v-if="orden.estado == 8" class="badge badge-success">Despachado</span>
                                    <span v-if="orden.estado == 9" class="badge badge-warning">Alistamiento Abandonado</span>
                                    <span v-if="orden.estado == 10" class="badge badge-danger">Empacado Abandonado</span>
                                    </td>
                                    <td><a v-bind:href="'../view-proceso/consecutivo/'+orden.consecutivo"><span class="badge badge-success"><i class="fa fa-eye"></i></span></a><button type="button" v-on:click="updateConsecutivo(orden.consecutivo)" v-if="orden.estado == 5" style="cursor:pointer; border:none; background-color:none;" data-toggle="modal" data-target="#modalIngresarFacturas"><span class="badge badge-info"><i class="fa fa-file-invoice"></i></span></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="modalIngresarFacturas" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><h3>Ingresar Codigos de factura al Despacho ID: {{consecutivo}}</h3></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>En este modulo digitarás los numero de factura generados una vez termine su proceso de facturación, una vez digitado y guardado se marcará como facturado la orden. </p>
                        <b>Numeros de factura:</b>
                        <input type="text" v-model="textFacturas" class="form-control" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info" data-dismiss="modal">Cerrar</button>
                        <button type="button" v-on:click="saveNumFacturas()" class="btn btn-success"><i class="fa fa-save"></i> Guardar</button>
                    </div>
                    </div>
                </div>
            </div>


        </div>
    </section>
   
</template>
<script>
import swal from "sweetalert";
export default {    
  data () {
    return {
       fechaActual: '',
       ordenesD:[],
       textFacturas:'',
       consecutivo: ''
    }
  },
  
  props: {
  },
  methods: {
      saveNumFacturas(){
          let data = {consecutivo:this.consecutivo,numFacturas:this.textFacturas, _token: document.querySelector('meta[name="csrf-token"]').content};
          axios.post('saveNumFacturas',data)
          .then(response => {
              swal('INFO FACTURACIÓN: ',response.data.message,'success').then((key) => {
                  if(key){
                      $('#modalIngresarFacturas').modal('hide');
                      this.cargarOrdenes();
                      var min = window.open("../facturacionPDF/generar/"+this.consecutivo,'_blank');
                        min.print();
                  }
              });
          })
      },
      updateConsecutivo(conse){
          this.consecutivo = conse
      },
      startInterval() {
              setInterval(() => {
                this.cargarOrdenes();
            }, 10000);
        },
      cargarOrdenes(){
        axios.get('getordenesParaFacturar')
        .then(response => {
            $('#tablaOrdenes').DataTable().destroy();
            this.ordenesD=response.data
            this.initDatatable();
        })
      },
      initDatatable(){
        this.$nextTick(() => {
            $('#tablaOrdenes').DataTable({
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>", 
                });
            });
      },
      getFechaActual(){
            var meses = new Array ("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            var f = new Date();
            this.fechaActual = f.getDate() + " de " + meses[f.getMonth()] + " de " + f.getFullYear();
        },

  },
    mounted(){
        this.cargarOrdenes();
        this.getFechaActual();
    },

    

}
</script>
