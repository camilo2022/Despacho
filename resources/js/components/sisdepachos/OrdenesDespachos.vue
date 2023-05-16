<template>
    <section >
        <div class="container-fluid">
            <div class="card m-2">
                <div class="card-header" style="background-color:#007bff !important; color:white;">
                    <center><h3>Listado Ordenes de Despacho <strong v-if="fechaOrdenes != ''" >DIA {{fechaOrdenes}}</strong> <strong v-if="fechaOrdenes == ''" >DIA {{fechaActual}}</strong></h3></center>
                </div>
                <div class="card-body">
                    
            <div class="row mb-4">
                <div class="col-sm-4">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#cargarMasivoModal">
                Cargar Masivo
            </button>
                </div>
                <div class="col-sm-4">
                    <input type="date" @change="cargarOdenesPorFecha" v-model="fechaOrdenes" class="form-control" >
                </div>
                <div class="col-sm-4">
                    <button v-if="fechaOrdenes != '' "  class="btn btn-success">Generar Reporte</button>
                    <button v-if="fechaOrdenes != '' " class="btn btn-info">Generar Excel</button>
                </div>
            </div>
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
                                    <span v-if="orden.estado == 11" class="badge badge-success">Facturado para Despachar</span>
                                    <span v-if="orden.estado == 12" class="badge badge-black" style="background-color:black !important; color:white !important;">Despachado {{orden.fdespacho}}</span>
                                    </td>
                                    <td><a style="cursor:pointer" v-on:click="getFacturacion(orden.consecutivo)" v-if="orden.estado == 11" class="badge badge-success"><i class="fa fa-file-invoice-dollar"></i></a><a v-bind:href="'view-curva/'+orden.consecutivo" style="margin-left:9px; cursor:pointer;"><i class="fa fa-bezier-curve"></i></a><a v-if="orden.estado == 6" href="" style="margin-left:9px;"><i class="fa fa-check"></i></a> <a v-bind:href="'view-proceso/consecutivo/'+orden.consecutivo"  style="margin-left:9px;"><i class="fa fa-eye"></i></a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--Modal-->
                <div class="modal fade" id="cargarMasivoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Carga Masiva de despachos</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                            <div style="">
                                <div class="row">
                                    <div class="col-md-8">
                                        <input type="file" id="file" ref="file"  class="form-control" v-on:change="handleFileUpload()">
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-success" v-if="disablebutton == false" v-on:click="CargarOrdenes()">Cargar</button>
                                        <label v-if="disablebutton == true">Cargando...</label>
                                    </div>
                                </div>
                                <input class="m-2 form-control" v-model="file.name" disabled>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <b>ADVERTENCIA: Espere porfavor que la pantalla se desaparezca sola.</b>
                    </div>
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
        file:'',
        disablebutton:false,
        ordenesD:[],
        fechaOrdenes:'',
        fechaActual:''
    }
  },
  
  props: {
  },
  methods: {
      handleFileUpload(){
          this.file = this.$refs.file.files[0];
      },
        cargarOdenesPorFecha(){
            let data ={date: this.fechaOrdenes, '_token': document.querySelector('meta[name="csrf-token"]').content};
            axios.post('getForFecha',data)
            .then(response => {
                this.ordenesD=response.data
                $('#tablaOrdenes').DataTable().destroy();
                this.initDatatable();
            })
        },
        getFacturacion(cons){
             var min = window.open("../facturacionPDF/generar/"+cons,'_blank');
                        min.print();
           
           },
      CargarOrdenes(){
          if(this.file != ''){
              const config = {
                    headers: {
                    'content-type': 'multipart/form-data',
                    '_token': document.querySelector('meta[name="csrf-token"]').content,
                    }
              }
              this.disablebutton=true;
              let formData = new FormData();
              formData.append('file',this.file);
              /*Se hace la peticion */
               axios.post('submitordenes',formData,config)
               .then(response => {
                   swal('Aviso',response.data.message,'success');
               })
          }else{
            swal("AVISO","No hay archivo seleccionado","info");
          }
        
      },
      cargarOrdenes(){
        axios.get('getordenes')
        .then(response => {
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
