<template>
<section>
    <div class="panel-header bg-primary-gradient">
		<div class="page-inner py-5">
		    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
				<div>
					<h2 class="text-white fw-bold">Banco de Imagenes > Verificacion </h2>
					<h5 class="text-white">Formulario de verificacion de etiquetas</h5>
				</div>
			</div>
		</div>
	</div>
    <div class="panel panel-body page-inner mt--5">
		<div class="row mt--2">
			<div class="col-md-12 col-12 col-xs-12">
				<div class="card full-height">
					<div class="card-body">
                        <div class="form-group form-inline" >
							<label for="inlineinput" @change="cambia()" class="col-md-3 col-form-label">Codigo de barras:</label>
							<div class="col-md-6 p-0">
								<div class="input-group">
                                    <input type="text"  v-model="codbarra" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
                                <div class="input-group-prepend">
                                    <button class="btn btn-default btn-border" @click="buscarCodBarra()" type="button">Buscar</button>
                                </div>
                                </div>
							</div>
						</div>
                        <div v-if="displaydiv==true" class="card">
                           <div class="container-fluid pt-3">
                               <div class="row">
                                    <div class="col-sm-6 col-xs-12 col-12 col-lg-6">
                                        <div class="card">
                                            <div v-if="tieneimagen" class="p-3">
                                                <div class="row">
                                                    <div class="mb-3 col-lg-12 col-sm-12 col-xs-12 col-12">
                                                       <img class="img-fluid" v-bind:src="item.img1" /> 
                                                    </div>
                                                    <div class="col-lg-12 col-sm-12 col-xs-12 col-12">
                                                        <img class="img-fluid" v-bind:src="item.img2" /> 
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-else class="p-3">

                                                <span v-if="userLogged.rol_id == 1 || userLogged.rol_id == 4">
                                                    
                                                    <div class="row">
                                                    <form @submit="submitForm" enctype="multipart/form-data">
                                                    <div class="col-sm-8 col-12 col-xs-12 col-md-12">
                                                        <div class="col-lg-12 col-sm-12 col-xs-12 col-12 pb-2">
                                                            <vue-progress-bar></vue-progress-bar>
                                                        </div>
                                                        <div class="col-lg-12 col-sm-12 col-xs-12 col-12 pb-3">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlFile1">Elige la Imagen de Tiro</label>
                                                                <input required type="file" @change="onFileChange1" class="form-control-file" accept="image/gif, image/png, image/jpeg" >
                                                            </div>
                                                            <div id="preview1" class="form-group">
                                                                <img class="pb-3 img-fluid" v-if="urlimg1" :src="urlimg1" />
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 col-sm-12 col-xs-12 col-12">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlFile1">Elige la imagen de Retiro</label>
                                                                <input type="file" @change="onFileChange2" class="form-control-file" accept="image/gif, image/png, image/jpeg" >
                                                            </div>
                                                            <div id="preview2" class="form-group">
                                                                <img class="img-fluid" v-if="urlimg2" :src="urlimg2" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div  v-if="urlimg1 || urlimg2"  class="col-sm-4 col-12 col-xs-12 col-md-12 m-auto">
                                                        <button type="submit" class="w-100 btn btn-success">Subir Imagenes</button>
                                                    </div>
                                                    </form>
                                                </div>

                                                </span>
                                                <span v-else>
                                                   No hay imagenes Cargadas de la referencia que has digitado
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xs-12 col-12 col-lg-6">
                                        <div class="card">
                                            <div class="row p-3">
                                                <div class="col-sm-12 col-xs-12 col-12 col-lg-12">
                                                    <div class="form-group form-inline">
                                                        <label for="inlineinput" class="col-md-6 col-form-label">Referencia: </label>
                                                        <div class="col-md-6 p-0">
                                                                <span class="text-center form-control">{{item.referencia}}</span>
                                                        </div>
                                                    </div>            
                                                </div>
                                                <div class="col-sm-12 col-xs-12 col-12 col-lg-12">
                                                    <div class="form-group form-inline">
                                                        <label for="inlineinput" class="col-md-6 col-form-label">Item: </label>
                                                        <div class="col-md-6 p-0">
                                                                <span class="ml-2 form-control">{{item.descripcionlarga}}</span>
                                                        </div>
                                                    </div>            
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="row p-3">
                                                <div class="col-sm-12 col-xs-12 col-12 col-lg-12">
                                                    <div class="form-group form-inline">
                                                        <label for="inlineinput" class="col-md-6 col-form-label">Marca: </label>
                                                        <div class="col-6 col-xs-12 col-sm-12 col-md-12 col-lg-6 p-0">
                                                                <span class="ml-2 form-control">{{item.marca}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-xs-12 col-12 col-lg-12">
                                                    <div class="form-group form-inline">
                                                        <label for="inlineinput" class="col-md-6 col-form-label">Categoria </label>
                                                        <div class="col-md-6 p-0">
                                                            <span class="form-control">{{item.categoria}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-xs-12 col-12 col-lg-12">
                                                    <div class="form-group form-inline">
                                                        <label for="inlineinput" class="col-md-6 col-form-label">SubCategoria:</label>
                                                        <div class="col-md-6 p-0">
                                                                <span class="ml-2 form-control">{{item.subcategoria}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="row p-3">
                                                <div class="col-sm-12 col-xs-12 col-12 col-lg-12">
                                                    <div class="form-group form-inline">
                                                        <label for="inlineinput" class="col-md-6 col-form-label">Año: </label>
                                                        <div class="col-md-6 p-0">
                                                                <span class="ml-2 form-control">{{item.anio}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-xs-12 col-12 col-lg-12">
                                                    <div class="form-group form-inline">
                                                        <label for="inlineinput" class="col-md-6 col-form-label">Desc. Corta: </label>
                                                        <div class="col-md-6 p-0">
                                                            <span class="ml-2 form-control">{{item.descorta}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-xs-12 col-12 col-lg-12">
                                                    <div class="form-group form-inline">
                                                        <label for="inlineinput" class="col-md-6 col-form-label">Colección: </label>
                                                        <div class="col-md-6 p-0">
                                                                <span class="ml-2 form-control">{{item.coleccion}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="row p-3">
                                                <div class="col-sm-12 col-xs-12 col-12 col-lg-12">
                                                    <div class="form-group form-inline">
                                                        <label for="inlineinput" class="col-md-6 col-form-label">Talla: </label>
                                                        <div class="col-md-6 p-0">
                                                            <span class="ml-2 form-control">{{item.talla}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-xs-12 col-12 col-lg-12">
                                                    <div class="form-group form-inline">
                                                        <label for="inlineinput" class="col-md-6 col-form-label">Color: </label>
                                                        <div class="col-md-6 p-0">
                                                            <span class="ml-2 form-control">{{item.color}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card">
                                            <div class="card-head" style="border-start-end-radius:10px; border-start-start-radius:10px; background-color:#0083ff; color:white; text-align:center;">
                                               <h2 style="font-family: 'Georama', sans-serif;"> Información de bodega </h2>
                                            </div>
                                            <div class="panel panel-header">
                                                <div class="row p-3">
                                                        <div class="col-sm-12 col-xs-12 col-md-12 col-12 col-lg-12">
                                                            <div class="form-group form-inline">
                                                                <label for="inlineinput" class="col-md-6 col-form-label">Codigo Bodega: </label>
                                                                <div class="col-md-6 p-0">
                                                                    <span class="ml-2 form-control">{{item.codbodega}}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12 col-xs-12 col-md-12 col-12 col-lg-12">
                                                            <div class="form-group form-inline">
                                                                <label for="inlineinput" class="col-md-6 col-form-label">Nombre Bodega: </label>
                                                                <div class="col-md-6 p-0">
                                                                    <span class="ml-2 form-control">{{item.namebodega}}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12 col-xs-12 col-md-12 col-12 col-lg-12">
                                                            <div class="form-group form-inline">
                                                                <label for="inlineinput" class="col-md-6 col-form-label">Cantidad Disponible: </label>
                                                                <div class="col-md-6 p-0">
                                                                    <span class="ml-2 form-control">{{item.cantdisponible}}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-body page-inner mt--5">
                                                <div class="card p-2 text-center">
                                                    <b style="font-size:1.5em;">$56.000</b>
                                                </div>
                                            </div>
                                    </div>

                                    <div class="row w-100 p-3">
                                        <div class="col-sm-6">
                                            <button class="w-100 btn btn-danger">Desaprobar</button>
                                        </div>
                                         <div class="col-sm-6">
                                            <button class="w-100 btn btn-success">Aprobar</button>
                                        </div>
                                    </div>
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
    import VueProgressBar from 'vue-progressbar'

    Vue.use(VueProgressBar, {
    color: 'rgb(143, 255, 199)',
    failedColor: 'red',
    height: '120px'
    })
    export default {

        mounted() {
            this.getRolUser();
        },
        data () {
            return {
                displaydiv:false,
                tieneimagen:false,
                urlimg1:'',
                urlimg2:'',
                file1:'',
                file2:'',
                codbarra:'',
                userLogged: [],
                item:{
                    descripcionlarga: 'STARA POWER REAR ZIPPER',
                    marca: '30  - STARA',
                    categoria:'21 - STARA PRENDS',
                    subcategoria: 'SST',
                    anio:'2014',
                    descorta:'CAMISA',
                    coleccion:'C1 - COLECCION',
                    referencia: '31073',
                    talla: 'S',
                    codbodega: 'PV02',
                    namebodega: 'VENTURA PLAZA VB02',
                    color:'50 - GRIS',
                    img2:'',
                    img1:'',
                    cantdisponible:23
                }
            }
        },
        methods: 
        {
            getRolUser(){
                this.userLogged = [];
                let userid = document.querySelector("meta[name='user-id']").getAttribute('content');
                axios.get('../../user/'+userid)
                .then(res => {
                   this.userLogged = res.data
                })
            },
            cambia(){
                this.displaydiv=false;
            },
            onFileChange1(e) {
                this.file1 = e.target.files[0];
                this.urlimg1 = URL.createObjectURL(this.file1);
            },
            onFileChange2(e) {
                this.file2 = e.target.files[0];
                this.urlimg2 = URL.createObjectURL(this.file2);
            },
            async submitForm(e){
                e.preventDefault();
                const config = {
                    headers: {
                    'content-type': 'multipart/form-data',
                    '_token': document.querySelector('meta[name="csrf-token"]').content,
                    }
                }
                let formData = new FormData();
                formData.append('file1', this.file1);
                formData.append('file2', this.file2);
                formData.append('codbarras', this.codbarra);
                this.$Progress.start();
                axios.post('../../bank/store/img',formData,config).then(response => {
                   this.$Progress.finish();
                   this.buscarCodBarra();
                   swal ( "Subida Exitosa." ,response.data,  "success" )
                });
                
            },
            validarSiTieneImagen(){
                let data ={codbarra: this.codbarra, '_token': document.querySelector('meta[name="csrf-token"]').content};

                axios.post('../../bank/item/get', data)
                .then(res => {
                    this.tieneimagen=true;
                    this.item.img1 = 'https://drive.google.com/uc?id='+res.data[0].pathimg1;
                    this.item.img2 = 'https://drive.google.com/uc?id='+res.data[0].pathimg2;
                })
                .catch(err => {
                   this.tieneimagen = false;
                })
                
            },
            limpiarCampos(){
                this.displaydiv = false;
                this.tieneimagen = false;
                this.urlimg1='';
                this.urlimg2='';
                this.file1='';
                this.file2='';
                this.codbarra ='';
                this.item.img1 = '';
                this.item.img2 = '';
            },
            buscarCodBarra(){
                if(this.codbarra){
                    this.urlimg1='';
                    this.urlimg2='';
                    this.file1='';
                    this.file2='';
                    this.item.img2 = '';
                    this.item.img1 = '';
                    this.displaydiv = true;
                    this.validarSiTieneImagen();
                }else{
                   swal ( "Error al procesar" , 'No ha digitado ningún codigo de barras',  "error" )
                }
            },
            
        },
    }
</script>
