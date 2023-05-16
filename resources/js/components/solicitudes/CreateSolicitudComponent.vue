<template>
<section>
    <div class="panel-header bg-primary-gradient">
		<div class="page-inner py-5">
		    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
				<div>
					<h2 class="text-white fw-bold">Solicitudes > Crear Solicitud</h2>
					<h5 class="text-white">Formulario de solicitud de impresión de etiquetas</h5>
				</div>
			</div>
		</div>
	</div>
    <div class="panel panel-body page-inner mt--5">
		<div class="row mt--2">
			<div class="col-md-12 col-12 col-xs-12">
				<div class="card full-height">
					<div class="card-body">
					    <div class="form-group form-inline">
							<label for="inlineinput" class="col-md-3 col-form-label">Seleccione el tipo de destino etiquetas</label>
							<div class="col-md-9 p-0">
								<select @change="changeDestino" v-model="selected_tpdocument" class="form-control input-full">
                                    <option value="0" selected disabled>Seleccione un destino</option>
                                    <option v-for="(tiposolicitud,index) in tiposdesolicitud" v-bind:value="tiposolicitud.id">
                                        {{tiposolicitud.descripcion}}
                                    </option>
                                </select>
							</div>
						</div>
                        <div v-if="selectedDestino">
                            <div class="form-group form-inline">
                                <label for="inlineinput" class="col-md-3 col-form-label">Digite la referencia</label>
                                <div class="col-md-9 p-0">
                                    <div class="input-group">
                                        <input type="text" v-model="referenciaItem" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
                                    <div class="input-group-prepend">
                                        <button class="btn btn-default btn-border" @click="buscarReferencia()" type="button">Buscar Referencia</button>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h2>Detalles de la solicitud</h2>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table" id="tabladetalles">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Referencia Item</th>
                                                    <th scope="col">Codigo Barras</th>
                                                    <th scope="col">Tallaje</th>
                                                    <th scope="col">Cantidad</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item,indice) in itemsDeSolicitud">
                                                    <td>{{item.id}}</td>
                                                    <td>{{item.referencia}}</td>
                                                    <td>{{item.codbarra}}</td>
                                                    <td>{{item.talla}}</td>
                                                    <td>{{item.cantidad}}</td>
                                                    <td>
                                                        <button class="btn btn-danger btn-xs" @click="deleteList(indice)">Delete</button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td><b>Total: </b>{{cantidadTotalDetalles}}</td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <button hidden id="buttonsubit" @click="creaSolicitud()" class="w-100 btn btn-info"><i class="fa fa-plus"></i> Crear Solicitud</button>
				    </div>
                    </div>

                    <!--Modals-->
                    <div class="modal fade" id="modref" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                        <div class="modal-header">
                            <label class="modal-title" >Item Referencia {{referenciaItem}}</label>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body"> 
                            <div class="container">
                                <div class="card-body">
                                    <div class="form-group form-inline">
                                        <label for="inlineinput" class="col-md-3 col-form-label">Seleccione Color</label>
                                        <div class="col-md-9 p-0">
                                            <select v-model="selected_color"  @change="cambiaColor()" class="form-control input-full">
                                                <option value="-1" selected disabled>Seleccione un color</option>
                                                <option v-for="(color) in coloresSelect" v-bind:value="color.id">
                                                   {{color.id}} - {{color.name}}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div  id="tabla-items" hidden class="form-group">
                                        <div class="table-responsive">
                                            <table id="encabezado_barras" class="table table-striped table-blank">
                                                <thead>
                                                    <tr>
                                                    <th scope="col">Cod Barras</th>
                                                    <th scope="col">Tallaje</th>
                                                    <th scope="col">Cantidad</th>
                                                    <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                         <td>
                                                            <select @change="changeCodBarras()" v-model="selected_codbarras" class="form-group">
                                                                <option disabled="" selected="" value="">Seleciona Un Codigo De Barras</option>
                                                                <option v-for="(item,indice) in items" v-bind:value="indice">
                                                                    {{ item.barras[item.barras.length - 1] }}
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <label>{{talla_selected}}</label>
                                                        </td>
                                                        <td>
                                                            <input class="form-control" v-model="cantidad_select" placeholder="Ingrese cantidad" />
                                                        </td>
                                                        <td>
                                                            <button @click="addDetalleItem()" class="btn btn-success">Agregar</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                        </div>
                    </div>
                    </div>
                    <!--Modals-->
				</div>
			</div>
		</div>
	</div>
</section>
</template>

<script>
import swal from 'sweetalert';
import VueProgressBar from 'vue-progressbar'

    Vue.use(VueProgressBar, {
    color: 'rgb(143, 255, 199)',
    failedColor: 'red',
    height: '120px'
    })
    export default {
        mounted() {
            this.getTiposSolicitudes();
        },
        data(){
            return{
                itemsolicitud:{
                    referencia: '',
                    codbarra: '',
                    cantidad: 0
                },
                selectedDestino: false,
                selected_tpdocument:'0',
                modal:{title:'Referencia ITEM'},
                tiposdesolicitud: [],
                referenciaItem:'',
                coloresSelect:[],
                selected_color:'',
                cantidad_select: '',
                talla_selected:'',
                selected_codbarras:'',
                items: [],
                itemsDeSolicitud: [],
                cantidadTotalDetalles:0,
                id:0
            }
        },
        methods: {
            deleteList: function(indice){
                this.cantidadTotalDetalles = parseInt(this.cantidadTotalDetalles) - parseInt(this.itemsDeSolicitud[indice].cantidad);
                this.$delete(this.itemsDeSolicitud, indice);
                if(this.cantidadTotalDetalles>0){
                    $('#buttonsubit').removeAttr('hidden');
                }else{3
                    $('#buttonsubit').attr('hidden',true);
                }
            },
            creaSolicitud(){
                let userid = document.querySelector("meta[name='user-id']").getAttribute('content');

                let formData = {
                    _token : document.querySelector('meta[name="csrf-token"]').content,
                    tiposolicitud_id : parseInt(this.selected_tpdocument),
                    estado_id : 1,
                    user_id : userid,
                    observacion : 'El usuario ID: '+userid+' ha creado la solicitud, se remite a insumos la solicitud',
                    cantidadtotal : parseInt(this.cantidadTotalDetalles),
                    items:this.itemsDeSolicitud
                }
                /* Se envia la peticion para guardar la solicitud */    
                axios.post('../solicitud',formData)
                .then(response => {
                    swal({
                        icon: "success",
                        title: 'Exitosa',
                        text: response.data
                    });
                    this.limpiaCampos();
                });
                 /* Se envia la peticion para guardar la solicitud */ 

            },
            changeCodBarras(){ 
                this.itemsolicitud = {
                    referencia: '',
                    codbarra: '',
                    cantidad: 0
                };
                this.talla_selected=this.items[this.selected_codbarras].talla;
            },
            changeDestino(){
                this.selectedDestino = true;
            },
            addDetalleItem(){
                if(parseInt(this.cantidad_select) > 0){
                    $('#buttonsubit').removeAttr('hidden');
                    this.cantidadTotalDetalles=parseInt(this.cantidadTotalDetalles) + parseInt(this.cantidad_select);
                    this.id++;
                    var result=false;
                    for(var i=0;i<this.itemsDeSolicitud.length;i++){
                        if(this.itemsDeSolicitud[i].codbarra == this.items[this.selected_codbarras].barras[this.items[this.selected_codbarras].barras.length - 1]){
                            result=true;
                        }
                    }
                    if(result){
                        swal("Informacion agregar item.",'Ya has agregado este item, por lo tanto, se sumara la cantidad que elegiste '+parseInt(this.cantidad_select), "info");
                        var cantidad=0;
                        for(var i=0;i<this.itemsDeSolicitud.length;i++){
                            if(this.itemsDeSolicitud[i].codbarra == this.items[this.selected_codbarras].barras[this.items[this.selected_codbarras].barras.length - 1]){
                                cantidad = parseInt(this.itemsDeSolicitud[i].cantidad) + parseInt(this.cantidad_select);
                                this.itemsDeSolicitud[i].cantidad = cantidad;
                            }
                        }
                    }else{
                        this.itemsDeSolicitud.push({
                        id: this.id,
                        referencia: this.referenciaItem,
                        codbarra: this.items[this.selected_codbarras].barras[this.items[this.selected_codbarras].barras.length - 1],
                        cantidad:this.cantidad_select,
                        talla: this.talla_selected
                        });
                    } 
                     this.cantidad_select='';
                } 
            },
            getTiposSolicitudes(){
                axios.get('../tiposolicitudes').then(res => {
                   this.tiposdesolicitud=res.data
                })
            },
            buscarReferencia(){
                if(this.referenciaItem){
                    axios.get('../consultaReferencia/'+this.referenciaItem)
                    .then(res => {
                        this.selected_codbarras = '';
                        this.coloresSelect = res.data
                        this.muestraSelectColores()
                    })
                    .catch(err => {
                        swal("Error",'La referencia ingresada no existe o no tiene codigos de barras principales asignados', "error");
                    })
                }else{
                    swal("Error",'No has digitado ninguna referencia para buscar.', "error");
                }
                
            },
            muestraSelectColores(){
                this.selected_color = '-1'
                $('#tabla-items').attr("hidden",true);
                this.items = []
                this.cantidad_select='',
                this.talla_selected='',
                this.selected_codbarras=''
               $('#modref').modal('show');
            },
            cargarTablaItems(){
                    $('#tabla-items').removeAttr('hidden');
            },
            cambiaColor(){
                this.selected_codbarras = '',
                $('#tabla-items').attr("hidden",true);
                this.items = []
                axios.post('../consultaRefColor',{referencia: this.referenciaItem,
                    'color': this.selected_color})
                .then(res => {
                    this.items = res.data
                    this.cargarTablaItems()
                })
                .catch(err => {
                    swal("Error",'No existen codigos de barra principal asociados a este color de la referencia', "error");
                })
            },
            limpiaCampos(){
                this.itemsolicitud = {
                    referencia: '',
                    codbarra: '',
                    cantidad: 0
                },
                this.selectedDestino = false,
                this.selected_tpdocument = '0',
                this.modal = {title:'Referencia ITEM'},
                this.referenciaItem = '',
                this.coloresSelect = [],
                this.selected_color = '',
                this.cantidad_select =  '',
                this.talla_selected = '',
                this.selected_codbarras = '',
                this.items = [],
                this.itemsDeSolicitud = [],
                this.cantidadTotalDetalles = 0,
                this.id = 0
            }
        }
    }
</script>
