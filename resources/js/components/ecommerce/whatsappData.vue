<template>
  <section>
    <div class="panel-header bg-white">
      <div class="page-inner py-5">
          <div class="card">
              <div class="card-header text-center" style="background-color:#0069ff; color:white;">
                    <h3>NO LE COLOQUE TITULO</h3>
              </div>
              <div class="card-body">
                  <div class="text-center">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="background-color:#0069ff; color:white;" id="basic-addon1"><i class="fa fa-phone"> </i> </span>
                                    </div>
                                    <input @change="cambiaNumeroTelefono()" v-model="item.telefono" type="number" class="form-control" align='right' >
                                </div>
                        </div>
                        <div class="col-sm-6">
                            <div v-if="nombresExistente == ''" class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="background-color:#0069ff; color:white;" id="basic-addon1"><strong>Nombres:</strong> </span>
                                    </div>
                                    <input v-model="item.nombres" type="text" class="form-control" align='right' >
                            </div>
                            <div v-if="nombresExistente != ''" class="input-group mb-3">
                                <span>Cliente name: <b>{{this.nombresExistente}}</b></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="background-color:#0069ff; color:white;" id="basic-addon1"><strong>¿Se concreto la venta?</strong> </span>
                                    </div>
                                    <select v-model="item.ventaConcreta" class="form-control">
                                        <option value="1">SI</option>
                                        <option value="2">NO</option>
                                    </select>
                                </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="background-color:#0069ff; color:white;" id="basic-addon1"><strong>Requerimiento:</strong> </span>
                                    </div>
                                    <select v-model="item.requerimiento" class="form-control">
                                        <option selected value="1">Tipo A</option>
                                        <option value="2">Tipo B</option>
                                        <option value="3">OTRO</option>
                                    </select>
                                </div>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="background-color:#0069ff; color:white;"><b>Observación:</b></span>
                        </div>
                        <textarea placeholder="Digite una observación." v-model="item.observacion" class="form-control" aria-label="With textarea"></textarea>
                    </div>
                    <br>
                    <button @click="saveInfo()" class="btn btn-info w-100" style="background-color:#0069ff !important; font-weight:bold; color:white; font-size:1.4em;"><i class="far fa-save"></i> Guardar</button>
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
          item:{
              telefono:'',
              nombres:'',
              ventaConcreta:1,
              requerimiento:1,
              observacion:''
          },
          nombresExistente:''
      }
    },
  watch: {
  },

  methods: {
      cambiaNumeroTelefono(){
          let ruta = "consultaNumeroTelefono/"+this.item.telefono;
         axios.get(ruta)
         .then(res => {
             if(res.data != 2){
                 this.nombresExistente = res.data.nombres;
             }
         })
      },
      saveInfo(){
          let data ={'requerimiento': this.item.requerimiento,'observacion':this.item.observacion,'telefono': this.item.telefono,'nombres': this.item.nombres,'ventaConcreta': this.item.ventaConcreta,'_token': document.querySelector('meta[name="csrf-token"]').content};
          axios.post('ecommerce/save',data)
          .then(res => {
              swal('El sistema informa:',res.data.message,'success');
              this.limpiaCampos();
          })
      },
      limpiaCampos(){
          this.item.telefono='';
          this.item.nombres='';
          this.item.ventaConcreta=1;
          this.item.requerimiento=1;
          this.item.observacion='';
          this.nombresExistente='';
      }
  }

}
</script>
