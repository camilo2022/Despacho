<template>
  <section>
    <div class="panel-header bg-white">
      <div class="page-inner py-5">
         <div class="panel panel-body page-inner mt--5">
          <div class="card full-height">
           <div class="card-header" style="font-weight:bold; background-color:#0060ff; color:white;">
               <h2 v-if="verInfo==false">Listado de Clientes Ecommerce</h2>
                <h2 v-if="verInfo==true">Contacto realizado por Cliente Yorgen Galvis</h2> 
           </div>
           <div class="card-body">
               <div v-if="verInfo==true">
                   <b>Numero de contacto whatsapp:</b> <label>+573045864456</label>
                   <a target="_blank" href="https://api.whatsapp.com/send?phone=573045864456&text=Hola%20te%20%20contacto%20desde%20canal%20de%20ventas%20stara,%20mi%20nombre%20es%20" class="btn btn-success"><i class="fab fa-whatsapp"></i> Contactar</a>
               </div>
               <div v-if="verInfo==false" class="row">
                   <div class="col-sm-2">
                        <input type="number" class="form-control" placeholder="Digite numero del cliente"/>
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-success">Buscar Cliente</button>
                    </div>
               </div>
               <div v-if="verInfo==false" class="container-fluid m-2">
                   <small>Listado de clientes que contactaron el canal</small>
                  <div class="row">
                      <div class="col-sm-3">
                          <div  @click="verInfoE()" class="mt-4 card text-center" style="cursor:pointer; background-color:#0095ff;  color:white;">
                            <h2 class="p-2"><b>ID {{this.clientes[0].cliente.id}} </b> - {{this.clientes[0].cliente.nombres}}</h2>
                          </div>
                      </div>
                      
                  </div>
               </div>
               <div v-if="verInfo==true" class="container-fluid m-2">
                  <div class="row">
                      <div class="table-responsive">
                          <table class="table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Nombre Ecommerce</th>
                                    <th>¿Venta concreta?</th>
                                    <th>Tipo Requerimiento</th>
                                    <th>Observacion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>02/11/2021 09:38 A.M.</td>
                                    <td>Joselyn STARA</td>
                                    <td><span class="badge badge-success">Si</span></td>
                                    <td>Tipo A</td>
                                    <td>Venta exitosa</td>
                                </tr>
                            </tbody>
                        </table>
                      </div>
                  </div>
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
import VueProgressBar from "vue-progressbar";
import { loadProgressBar } from 'axios-progress-bar';
import 'axios-progress-bar/dist/nprogress.css';

export default {
  data () {
      return{
         clientes:[],
         verInfo:false,
      }
    },
  watch: {
  },
    mounted(){
        this.buscarClientes();
    },
  methods: {
      verInfoE(){
          this.verInfo=true
      },
     buscarClientes(){
         axios.get('listClientes')
         .then(res => {
            this.clientes = res.data
            console.log(this.clientes)
         })
     }
  }

}
</script>