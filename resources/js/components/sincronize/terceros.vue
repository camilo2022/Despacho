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
            <h2 class="text-white fw-bold"><i class="fa fa-sync fa-lg"></i> Sincronización de Terceros SIESA -> BLESS</h2>
            <h5 class="text-white">
              Sincronización de clientes (terceros) para el aplicativo Bless.
            </h5>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-body page-inner mt--5">
          <div class="card full-height">
              <div class="card-body">
                 <button class="m-2 btn btn-info btn-lg w-100" @click="sincronize"><i class="fa fa-sync"></i> Sincronizar Ahora</button>
                 <button class="m-2 btn btn-info btn-lg w-100" @click="sincronizeEmpleados"><i class="fa fa-sync"></i> Sincronizar Empleados</button>
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
  watch: {
  },
  watch: {
  },

  methods: {
      sincronize(){
          let url='../../../sincronize/siesa_terceros';
          swal("Informativo","Ha comenzado a sincronizar clientes, fijese en la barra superior","info");
        loadProgressBar()
        axios.post(url)
        .then(res => {
          let data=res.data.message;
          swal("Buen trabajo!",data,"success");
        })
         
      },
      sincronizeEmpleados(){
          let url='../../../sincronize/siesa_empleados';
          swal("Informativo","Ha comenzado a sincronizar Empleados a Tiendas, fijese en la barra superior","info");
        loadProgressBar()
        axios.post(url)
        .then(res => {
          let data=res.data.message;
          swal("Buen trabajo!",data,"success");
        })
      }
  }

}
</script>
