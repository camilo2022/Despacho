<!DOCTYPE html>
<html lang="es">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>ORGANIZACION BLESS S.A.S</title>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->
<body class="skin-blue fixed-layout">
<div  class="container">
<div class="row">
<div class="col-7 mx-auto p-4 m-5 border-light shadow-sm">
        <h3 class="pb-3"><center >VALIDAR EMPLEADO</center></h3>
    
<br>
<div id="appvue">
    
<div class="form-style">
    <h5 id="nombres"></h5>
    <br>
    <h6 id="cargo"></h5>
    <br>
    <h6 id="cupo"></h5>
  <div class="form-group pb-3">   
    <input type="number" placeholder="DOCUMENTO." v-model="documento" class="form-control" id="referencia">
  </div>
  <div class="form-group pb-3" id="tabla_render">   
    
  </div>
    <div v-if="displayDef == true" class="d-flex align-items-center justify-content-between">
       <div class="card-body">
           <h3 v-html="nombres"></h3>
           <h3 v-html="cc"></h3>
           <h5 v-html="cupo"></h5>
           <h5 v-html="cupoDispo"></h5>
       </div>
       <div class="card-body">
           Ingrese la cantidad de cupo que uso.
           $<input class="form-control" v-model="cupoGastado" type="number">
           <br>
           <button type="button" @click="agregarGastoCupo" class="btn btn-info">Agregar Gasto de cupo</button>
       </div>
       
    </div>
 <div class="pb-2" v-if="documento != '' ">
  <button type="button" @click="buscarEmpleado()" class="btn btn-dark w-10 font-weight-bold mt-2 w-100">Consultar</button>
 </div>
  
</div>

</div>
</div>
</div>
</div>
<div class="container text-center small text-muted mb-5">
    
    Design by <a href="https://siversoftware.zarethpremium.com/validar_cupo_empleado" >ORGANIZACION BLESS.</a>
</div>
<div style="display:none" id="preloader">
  <div id="loader"></div>
</div>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/vue@next"></script>
    
    <script>
        const AttributeBinding = {
            mounted(){
                
            },
            data() {
                return {
                    documento:'',
                    displayDIV:false,
                    cupoGastado:'',
                    nombres:'',
                    cupoDispo:'CUPO: $300000 COP',
                    cc: 'CC',
                    cupo:'CUPO: $300000 COP',
                    cupoValidate: 0,
                    displayDef:false,
                
                }
            },
            methods:{
                buscarEmpleado(){
                    axios.get('empleado/'+this.documento)
                    .then(response => {
                        if(response.data == 1){
                            swal({
                              title: "No es un empleado",
                              text: "El documento digitado no pertenece a ningun empleado",
                              icon: "error",
                            });
                            this.limpiarCampos();
                        }else{
                            this.displayDef = true;
                            this.cc = "CC "+this.documento;
                            this.nombres= response.data[0].nombres+" "+response.data[0].apellidos;
                            this.cupo = "Cupo Asignado: $"+response.data[0].cupo+" COP";
                            this.cupoDispo = "Cupo Disponible: $"+response.data[0].monto_libranza+" COP";
                            this.cupoValidate =  response.data[0].monto_libranza;
                            swal({
                                  title: "Si es un empleado",
                                  text: " "+this.nombres+" hace parte de Organización Bless.",
                                  icon: "success",
                                });
                        }
                    })
                },
                limpiarCampos(){
                    this.documento = '';
                    this.displayDIV = false;
                    this.cupoGastado = '';
                    this.nombres = '';
                    this.cupoDispo = 'CUPO: $300000 COP';
                    this.cc =  'CC';
                    this.cupo = 'CUPO: $300000 COP';
                    this.cupoValidate = 0;
                    this.displayDef = false;
                },
                agregarGastoCupo(){
                    if(this.cupoGastado != ''){
                        if(this.cupoGastado <= this.cupoValidate){
                            const form_data = new FormData();
                            form_data.append('documento', this.documento);
                            form_data.append('cupoGastado', this.cupoGastado);
                            axios.post('empleadoModificar',form_data)
                            .then(response => {
                                if(response.data == 1){
                                    swal({
                                      title: "Error en disminuir cupo",
                                      text: "Contacta con el administrador del sistema.",
                                      icon: "success",
                                    }); 
                                }else{
                                    swal({
                                      title: "Cupo enviado!",
                                      text: "Se le ha registrado el cupo gastado al empleado.",
                                      icon: "success",
                                    }); 
                                    this.limpiarCampos();
                                }
                            });
                        }else{
                           swal({
                              title: "Error en transacción.",
                              text: "Imposible superar el cupo asignado.",
                              icon: "error",
                            });
                        }
                    }else {
                        swal({
                              title: "Error en transacción",
                              text: "Ingrese un monto válido.",
                              icon: "error",
                            });
                    }
                    
                }
            }
        }
        var example2=Vue.createApp(AttributeBinding).mount('#appvue')

    </script>
</body>

</html>