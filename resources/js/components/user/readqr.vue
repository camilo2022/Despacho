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
            <h2 class="text-white fw-bold">Lector de QR</h2>
            <h5 class="text-white">Validar despachos</h5>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-body page-inner mt--5">
      <div class="row mt--2">
        <div class="col-md-12 col-12 col-xs-12">
          <div class="card full-height">
            <div class="card-body">
              <div style="width: 100%;" class="container">
                <!--<StreamBarcodeReader
                      @decode="onDecode"
                    @loaded="onLoaded"
                ></StreamBarcodeReader>-->
                <qrcode-stream :camera="camera" @decode="onDecode" @init="onInit">
                   
                </qrcode-stream>
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
import download from "downloadjs";
import VueProgressBar from "vue-progressbar";
import { QrcodeStream, QrcodeDropZone, QrcodeCapture } from 'vue-qrcode-reader'

export default {
  watch: {

  },
  mounted() {
  
  },
  components: {
    QrcodeStream,
    QrcodeDropZone,
    QrcodeCapture
  },
  data() {
    return {
        varLectuJson : '',
        camera: 'auto',
        result: null,
        showScanConfirmation: false
    };
  },
  //SELECT SUM(qr_despachos.cantidad) as total_picking FROM `qr_despachos` GROUP BY qr_despachos.npedido
  methods: {
        async onInit (promise) {
          try {
            await promise
          } catch (e) {
            console.error(e)
          } finally {
            this.showScanConfirmation = this.camera === "off"
          }
        },

        async onDecode (content) {
          this.result = content
          this.readQr()
          this.pause()
          await this.timeout(1500)
          this.unpause()
        },

        unpause () {
          this.camera = 'auto'
        },

        pause () {
          this.camera = 'off'
        },

        timeout (ms) {
          return new Promise(resolve => {
            window.setTimeout(resolve, ms)
          })
        },
        readQr() {
            const arrayData = this.result.split(';');
            const part=arrayData[7].split('de');
            var audio = new Audio('http://siversoftware.zarethpremium.com/public/scanner.mp3')
            audio.play();
            if(arrayData[11] != '' && arrayData[11] != null){
              arrayData[11] = arrayData[11];
            }else{
              arrayData[11] = 'No hay.';
            }

            if(arrayData[12] != '' && arrayData[12] != null){
              //CR -> California
              //NA -> Nacional
                let datie = {
                  '_token' : document.querySelector('meta[name="csrf-token"]').content,
                  'destinatario' : arrayData[0],
                  'nit' : arrayData[1],
                  'direccion' : arrayData[2],
                  'departamento' : arrayData[3],
                  'ciudad' : arrayData[4],
                  'phone' : arrayData[5],
                  'factura_numero' : arrayData[6],
                  'ncaja' : part[0],
                  'cajas' : part[1],
                  'cantidad' : arrayData[8],
                  'peso' : arrayData[9],
                  'ndespacho' : arrayData[10],
                  'npedido' : arrayData[11],
                  'filtro' : arrayData[12]
              };

              axios.post('/readqr/store',datie)
              .then(response => {
                var div = document.createElement("div");
                div.innerHTML = '<h1>PEDIDO NACIONAL</h1><br><b>Numero de Pedido:</b> '+arrayData[11]+'<br><b>Cliente:</b>'+arrayData[1]+' '+arrayData[0]+'<br><b>Numero Despacho: </b>'+arrayData[10]+'<br><b>Numero de Caja: </b>'+arrayData[7];
                if(arrayData[11] == 'No hay.' && arrayData[12] == 'NA'){
                  div.innerHTML = '<h1>PEDIDO NACIONAL</h1><small>Despacho subido sin  numero  de pedido.</small><br><b>Numero de Pedido:</b> '+arrayData[11]+'<br><b>Cliente:</b>'+arrayData[1]+' '+arrayData[0]+'<br><b>Numero Despacho: </b>'+arrayData[10]+'<br><b>Numero de Caja: </b>'+arrayData[7];              
                }else if(arrayData[11] == 'No hay.' && arrayData[12] == 'CR'){
                  div.innerHTML = '<h1>PEDIDO CALIFORNIA</h1><small>Despacho subido sin  numero  de pedido.</small><br><b>Numero de Pedido:</b> '+arrayData[11]+'<br><b>Cliente:</b>'+arrayData[1]+' '+arrayData[0]+'<br><b>Numero Despacho: </b>'+arrayData[10]+'<br><b>Numero de Caja: </b>'+arrayData[7];              
                }else if(arrayData[12] == 'CR'){
                   div.innerHTML = '<h1>PEDIDO CALIFORNIA</h1><b>Numero de Pedido:</b> '+arrayData[11]+'<br><b>Cliente:</b>'+arrayData[1]+' '+arrayData[0]+'<br><b>Numero Despacho: </b>'+arrayData[10]+'<br><b>Numero de Caja: </b>'+arrayData[7];              
                }
                
                swal({
                  title: 'Subida exitosa.', 
                  text: response.data.message,
                  content: div,
                  icon: 'success',
                  confirmButtonText: 'OK'
                  })
              }).catch(error => {
                swal("Error",error.response.data.message, "error");
              });

            }else{
              swal("Error",'No hay filtro de Rotulo. CR -> California - NA -> Nacional', "error");
            }


            

            
        }
    },

}

</script>
