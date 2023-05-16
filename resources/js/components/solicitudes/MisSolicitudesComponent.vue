<template>
    <section>

        <div class="panel-header bg-primary-gradient">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h2 class="text-white fw-bold">Solicitudes > Mis Solicitudes</h2>
                        <h5 class="text-white">Administraciòn de solicitudes realizadas por ti.</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="m-2 card card-body">
            <div class="card-header">
                A continuación se mostrarán las solicitudes realizadas en las semana actual
            </div>
           <div class="mt-5 table-responsive">
               <table id="tablasolicitudes" class="table m-4">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Fecha Creacion</th>
                            <th scope="col">Solicitante</th>
                            <th scope="col">Codigo</th>
                            <th scope="col">Destino</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Observación</th>
                            <th scope="col">Ultima Revisión</th>
                            <th scope="col">Encargado revision</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="solicitud in solicitudes">
                            <th scope="row">{{solicitud.id}}</th>
                            <td>{{solicitud.fechaCreacion}}</td>
                            <td>{{solicitud.solicitante}}</td>
                            <td>{{solicitud.codigo}}</td>
                            <td>{{solicitud.tipoSolicitud}}</td>
                            <td>{{solicitud.estado}}</td>
                            <td style="text-align:center;">
                                <button @click="getDescripcion(solicitud.codigo)" alt="ver Detalle de Solicitud" class="btn btn-xs btn-info btn-round">
									<span class="btn-label">
									    <i class="fa fa-eye"></i>
									</span>
								</button>
                            </td>
                            <td>{{solicitud.revsolicitud}}</td>
                            <td>{{solicitud.encargadorev}}</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <button @click="viewDetailsItems(solicitud.codigo)" alt="ver Detalle de Solicitud" class="btn btn-xs btn-info btn-round">
											<span class="btn-label">
												<i class="fa fa-eye"></i>
											</span>
										</button>
                                    </div>
                                    <div v-if="solicitud.estado === 'Creada' " class="col-sm-6">
                                        <button @click="cancelarSolicitud(solicitud.codigo)" class="text-center btn btn-xs btn-danger btn-round">
											<span class="btn-label text-center">
												<i class="flaticon-cross"></i>
											</span>
										</button>
                                    </div>
                                    <!--<div class="col-sm-3">
                                        <button class="btn btn-xs btn-success btn-round">
											<span class="btn-label">
												<i class="fa fa-check"></i>
											</span>
										</button>
                                    </div>-->
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!--Modals-->
                    <div class="modal fade" id="modalDescripcion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                        <div class="modal-header text-center">
                            <label class="modal-title text-center" >Observación  de la solicitud #<b>{{codigo}}</b></label>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body"> 
                            <div class="container">
                                <div class="card-body">
                                    {{descripcion}}
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
                    <!--Modals-->
                    <div class="modal fade" id="modalItems" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                        <div class="modal-header text-center">
                            <label class="modal-title text-center" >Items de la Solicitud # <b>{{codigo}}</b></label>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body"> 
                            <div class="container">
                                <div class="card-body">
                                    <span>Hay una cantidad Total de: <b>{{cantotal}}</b></span>
                                    <div class="mt-5 w-100 table-responsive">
                                        <table id="tablaitems" class="table m-4">
                                            <thead>
                                                <tr>
                                                    <th scope="col">id</th>
                                                    <th scope="col">referencia</th>
                                                    <th scope="col">codbarra</th>
                                                    <th scope="col">cantidad</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                   <tr v-for="item in items">
                                                       <th>{{item.id}}</th>
                                                       <td>{{item.referencia}}</td>
                                                       <td>{{item.codbarra}}</td>
                                                       <td>{{item.cantidad}}</td>
                                                    </tr>
                                                    
                                            </tbody>
                                        </table>
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
    </section>
</template>
<script>

import 'datatables.net-buttons-bs4';
import 'datatables.net-bs4';
import 'datatables.net-buttons/js/buttons.html5.js';
import 'datatables.net-buttons/js/buttons.print.js';
import 'datatables.net-bs4/css/dataTables.bootstrap4.css';

 export default {
        data () {
            return {
                solicitudes :[],
                items:[],
                descripcion: 'Se remite a insumos',
                codigo:'',
                cantotal : 0
            }
        },
        mounted() {
         this.getSolicitudes();
        },
        methods: {
            initDatatable(){
                this.$nextTick(() => {
                    $('#tablasolicitudes').DataTable({
                        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                            "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                    });
                });
            },
            getSolicitudes(){
                axios.get('../../user/mysolicitudes').then(response => {
                    this.solicitudes = response.data
                    $('#tablasolicitudes').DataTable().destroy();
                    this.initDatatable();
                })
            },
            getDescripcion(codigo){
                this.codigo = codigo
                axios.get('../../solicitud/'+codigo).then(response => {
                    this.descripcion = response.data[0]['observacion']
                })
                $('#modalDescripcion').modal('show');
            },
            iniTablesItems(){
                this.$nextTick(() => {
                    $('#tablaitems').DataTable();
                });
            },
            viewDetailsItems(codigo){
               axios.get('../../solicitud/items/'+codigo).then(response => {
                   this.codigo=codigo
                   this.cantotal = response.data.cantotal
                   this.items=response.data.items
                   this.iniTablesItems();
                })
                
                $('#modalItems').modal('show');
            },
            cancelarSolicitud(codigo){
            
                swal({
                    title: "¿Estás segur@?",
                    text: "Insumos no podrá saber de esta solicitud ya que la cancelaràs totalmente.",
                    icon: "warning",
                    buttons: [
                        'Cancelar',
                        'Si, Estoy seguro'
                    ],
                    dangerMode: true,
                    }).then(isConfirm =>{
                        if (isConfirm) {
                                swal("Describa el motivo de la cancelación: ", {
                                    content: "input",
                                })
                                .then((value) => {
                                        let url="../../solicitud/";
                                        let params = {
                                            _token : document.querySelector('meta[name="csrf-token"]').content,
                                            _method: 'DELETE',
                                            codigo : codigo,
                                            observacion : value,
                                            tipo: 'user'
                                        }
                                        axios.post(url, params).then( response => {
                                            this.getSolicitudes();
                                            swal("Exitoso", response.data.message, "success");
                                        });
                                }); 
                        } else {
                            swal("Cancelado", "Has cancelado eliminar la solicitud:)", "error");
                        }
                })


            }
        }
    }
</script>
