<template>
  <section>
    <div class="panel-header bg-white">
      <div class="page-inner py-5">
          <div class="card">
             <div class="card-header" style="background-color:#0069ff; color:white;">
                 Exportar Listado por fecha
             </div>
             <div class="card-body">
                <div class="row">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-3">
                        <vuejs-datepicker  bootstrap-styling v-model="date1" class="form-control"></vuejs-datepicker>
                    </div>
                    <div class="col-sm-3">
                        <vuejs-datepicker  bootstrap-styling v-model="date2" class="form-control"></vuejs-datepicker>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
                <br>
                <button @click="BuscarFecha()" class="btn w-100" style="font-size:1.3em; background-color:#0069ff; color:white;">Exportar Ahora</button>
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
    components: {
        vuejsDatepicker
    },
  data () {
      return{
         date1: '',
         date2: '',
      }
    },
  watch: {
  },

  methods: {
      BuscarFecha(){
          if(this.date1 == '') return swal('Siver Informa:','Fecha 1 esta vacia','warning');
          if(this.date2 == '') return swal('Siver Informa:','Fecha 2 esta vacia','warning');
          let f1 = new Date(this.date1);
          let f2 = new Date(this.date2);
          f1 = f1.getFullYear()+'-'+f1.getMonth()+'-'+f1.getDay();
          f2 = f2.getFullYear()+'-'+f2.getMonth()+'-'+f2.getDay();
          let data ={'fecha1':f1,'fecha2':f2,'_token': document.querySelector('meta[name="csrf-token"]').content};
          axios.post('../exportarListadoExcelController',data)
          .then(response => {
                var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                var fileLink = document.createElement('a');
                fileLink.href = fileURL;
                fileLink.setAttribute('download', 'listadoEcommerceDo.xlsx');
                document.body.appendChild(fileLink);
                fileLink.click();
          })
          swal('Siver Informa: ','He descargado para ti, un informe de listado con fecha de inici: '+f1+ ' hasta '+f2);
      }
  }

}
</script>
