<template>
    <section>
        <div class="container-fluid">
            <div class="card m-2">
                <div class="card-header" style="background-color:#007bff !important; color:white;">
                   <center><h3 v-if="display==false">Revisión de Orden #{{idordenDespacho}}</h3><h3 v-if="display==true">Listado Ordenes Pendientes de Revisar</h3></center>
                </div>
                <div v-if="display==true" class="card-body">
                    <div class="card" style="width:100%;">
                        <div class="card-header" style="background-color:#0095ff; color:white;">
                            <center>Para recordar</center>
                        </div>
                        <div class="card-body">
                            <p><b>CANT.D</b> . Cantidad Real de Despacho | 
                            
                            <b>CANT.A</b> . Cantidad Alistada | 
                            
                            <b>CANT.R</b> . Cantidad Restante  | 
                            
                            <b>OPR</b> . Operario que alista y manda a revisión</p>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width:14%;">Consecutivo</th>
                                    <th style="width:23%;">Cliente</th>
                                    <th style="width:9%;">CANT.D</th>
                                    <th style="width:9%;">CANT.A</th>
                                    <th style="width:9%;">CANT.R</th>
                                    <th style="width:19%;">OPR</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="orden in ordenesDespachoRevisar">
                                    <td>{{orden.ordenDespacho.consecutivo}}</td>
                                    <td>{{orden.ordenDespacho.cliente}} {{orden.ordenDespacho.nit}}</td>
                                    <td>{{orden.cantidadDespacho}}</td>
                                    <td>{{orden.cantidadAlistada}}</td>
                                    <td>{{orden.cantidadDespacho - orden.cantidadAlistada}}</td>
                                    <td>{{orden.operario}}</td>
                                    <td><a style="cursor:pointer;" v-on:click="updateOrden(orden.ordenDespacho.consecutivo)"><span class="badge badge-success"><i class="fa fa-eye"></i></span></a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div v-if="display==false" class="card-body">
                    <div class="card-header">
                        <center><h2>Información del Cliente</h2></center>
                    </div>
                    <div class="card-body">
                        <div class="row mb-5">
                            <div class="col-sm-4"> </div>
                            <div class="col-sm-4"></div>
                            <div class="col-sm-4">
                                <button @click="alistarNuevamente()" type="button"  class="btn btn-info" >Alistar de Nuevo</button>
                                <button @click="rechazaOrden()" type="button" class="btn btn-danger">Rechazar Orden</button>
                                <button @click="aceptaOrden()" ype="button" class="btn btn-success">Aprobar Orden</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3" style="display:flex; flex-direction:column;">
                                <label>NIT: <b>{{orden.nit}}</b></label>
                                <label>DIRECCIÓN: <b>{{orden.direccion}}</b></label>
                                <label>CIUDAD <b>{{orden.ciudad}}</b></label>
                                <label>OBSERV.  </label> <textarea disabled>{{orden.observacion}} </textarea>
                                <label>CLIENTE: <b style="font-size: 1.1em;">{{orden.cliente}}</b></label>
                            </div>
                            <div class="col-md-3" style="display:flex; flex-direction:column;">
                                Fecha: {{orden.created_at}}
                                <img width="120px" heigth="120px" src="https://media-exp1.licdn.com/dms/image/C4E0BAQFnW0TdfpMO8w/company-logo_200_200/0/1533585316799?e=2159024400&v=beta&t=L_6VODgFex5zDVs1kvw1dvR9_g-W7j-KfEvVDpkWivo">
                            Operario que remite a revision: 
                              <b>{{ordenAlistamiento.user_id_alista}}</b>
                            </div>
                            <div class="col-md-6" style="display:flex; flex-direction:column;">
                                ORDEN DE DESPACHO Nº
                                <div style="width:100%; background-color:#0079fc; color:white; font-size:5em; text-align:center;">{{orden.consecutivo}}</div>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary mb-2 w-100" type="button" data-toggle="collapse" data-target="#collapseCurvaDespacho" aria-expanded="false" aria-controls="collapseExample">
                        Curva Despacho <span class="badge badge-success">Total: {{totalesDespacho.total}}</span>
                    </button>
                    <div class="collapse" id="collapseCurvaDespacho">
                        <div class="card card-body">
                            <table class="tablecurva">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">Pedido</th>
                                        <th>Vendedor</th>
                                        <th>referencia</th>
                                        <th>T4</th>
                                        <th>T6</th>
                                        <th>T8</th>
                                        <th>T10</th>
                                        <th>T12</th>
                                        <th>T14</th>
                                        <th>T16</th>
                                        <th>T18</th>
                                        <th>T20</th>
                                        <th>T22</th>
                                        <th>T24</th>
                                        <th>T26</th>
                                        <th>T28</th>
                                        <th>T30</th>
                                        <th>T32</th>
                                        <th>T34</th>
                                        <th>T36</th>
                                        <th>T38</th>
                                        <th>S</th>
                                        <th>M</th>
                                        <th>L</th>
                                        <th>XL</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(detalle,indice) in detailsOrdenDespacho">
                                        <td>{{detalle.pedido_id}}</td>
                                        <td>Archila</td>
                                        <td>{{detalle.referencia}}</td>
                                        <td>{{detalle.t4}}</td>
                                        <td>{{detalle.t6}}</td>
                                        <td>{{detalle.t8}}</td>
                                        <td>{{detalle.t10}}</td>
                                        <td>{{detalle.t12}}</td>
                                        <td>{{detalle.t14}}</td>
                                        <td>{{detalle.t16}}</td>
                                        <td>{{detalle.t18}}</td>
                                        <td>{{detalle.t20}}</td>
                                        <td>{{detalle.t22}}</td>
                                        <td>{{detalle.t24}}</td>
                                        <td>{{detalle.t26}}</td>
                                        <td>{{detalle.t28}}</td>
                                        <td>{{detalle.t30}}</td>
                                        <td>{{detalle.t32}}</td>
                                        <td>{{detalle.t34}}</td>
                                        <td>{{detalle.t36}}</td>
                                        <td>{{detalle.t38}}</td>
                                        <td>{{detalle.s}}</td>
                                        <td>{{detalle.m}}</td>
                                        <td>{{detalle.l}}</td>
                                        <td>{{detalle.xl}}</td>
                                        <td>{{detalle.total}}</td>
                                    </tr>
                                    <tr style="background-color:#008cff; color:white;">
                                        <td colspan="3" style="text-align:center">Totales</td>
                                        <td>{{totalesDespacho.t4}}</td>
                                        <td>{{totalesDespacho.t6}}</td>
                                        <td>{{totalesDespacho.t8}}</td>
                                        <td>{{totalesDespacho.t10}}</td>
                                        <td>{{totalesDespacho.t12}}</td>
                                        <td>{{totalesDespacho.t14}}</td>
                                        <td>{{totalesDespacho.t16}}</td>
                                        <td>{{totalesDespacho.t18}}</td>
                                        <td>{{totalesDespacho.t20}}</td>
                                        <td>{{totalesDespacho.t22}}</td>
                                        <td>{{totalesDespacho.t24}}</td>
                                        <td>{{totalesDespacho.t26}}</td>
                                        <td>{{totalesDespacho.t28}}</td>
                                        <td>{{totalesDespacho.t30}}</td>
                                        <td>{{totalesDespacho.t32}}</td>
                                        <td>{{totalesDespacho.t34}}</td>
                                        <td>{{totalesDespacho.t36}}</td>
                                        <td>{{totalesDespacho.t38}}</td>
                                        <td>{{totalesDespacho.s}}</td>
                                        <td>{{totalesDespacho.m}}</td>
                                        <td>{{totalesDespacho.l}}</td>
                                        <td>{{totalesDespacho.xl}}</td>
                                        <td>{{totalesDespacho.total}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <button class="btn btn-danger w-100" type="button" data-toggle="collapse" data-target="#collapseCurvaAlistamiento" aria-expanded="false" aria-controls="collapseExample">
                        Curva Alistada <span class="badge badge-success"> Total: {{totalesAlistamiento.total}}</span>
                    </button>
                    <div class="collapse" id="collapseCurvaAlistamiento">
                        <div class="card card-body">
                            <table class="tablecurva">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">Pedido</th>
                                        <th>Vendedor</th>
                                        <th>referencia</th>
                                        <th>T4</th>
                                        <th>T6</th>
                                        <th>T8</th>
                                        <th>T10</th>
                                        <th>T12</th>
                                        <th>T14</th>
                                        <th>T16</th>
                                        <th>T18</th>
                                        <th>T20</th>
                                        <th>T22</th>
                                        <th>T24</th>
                                        <th>T26</th>
                                        <th>T28</th>
                                        <th>T30</th>
                                        <th>T32</th>
                                        <th>T34</th>
                                        <th>T36</th>
                                        <th>T38</th>
                                        <th>S</th>
                                        <th>M</th>
                                        <th>L</th>
                                        <th>XL</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(detalle,indice) in detailsOrdenAlistamiento">
                                        <td>{{detalle.pedido_id}}</td>
                                        <td>Archila</td>
                                        <td>{{detalle.referencia}}</td>
                                        <td v-if="detalle.t4 == detailsOrdenDespacho[indice].t4"><span class="badge badge-success">{{detalle.t4}}</span></td>
                                        <td v-if="detalle.t4 != detailsOrdenDespacho[indice].t4"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].t4 - detalle.t4}}</span></td>
                                        <td v-if="detalle.t6 == detailsOrdenDespacho[indice].t6"><span class="badge badge-success">{{detalle.t6}}</span></td>
                                        <td v-if="detalle.t6 != detailsOrdenDespacho[indice].t6"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].t6 - detalle.t6}}</span></td>
                                        <td v-if="detalle.t8 == detailsOrdenDespacho[indice].t8"><span class="badge badge-success">{{detalle.t8}}</span></td>
                                        <td v-if="detalle.t8 != detailsOrdenDespacho[indice].t8"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].t8 - detalle.t8}}</span></td>
                                        <td v-if="detalle.t10 == detailsOrdenDespacho[indice].t10"><span class="badge badge-success">{{detalle.t10}}</span></td>
                                        <td v-if="detalle.t10 != detailsOrdenDespacho[indice].t10"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].t10 - detalle.t10}}</span></td>
                                        <td v-if="detalle.t12 == detailsOrdenDespacho[indice].t12"><span class="badge badge-success">{{detalle.t12}}</span></td>
                                        <td v-if="detalle.t12 != detailsOrdenDespacho[indice].t12"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].t12 - detalle.t12}}</span></td>
                                        <td v-if="detalle.t14 == detailsOrdenDespacho[indice].t14"><span class="badge badge-success">{{detalle.t14}}</span></td>
                                        <td v-if="detalle.t14 != detailsOrdenDespacho[indice].t14"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].t14 - detalle.t14}}</span></td>
                                        <td v-if="detalle.t16 == detailsOrdenDespacho[indice].t16"><span class="badge badge-success">{{detalle.t16}}</span></td>
                                        <td v-if="detalle.t16 != detailsOrdenDespacho[indice].t16"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].t16 - detalle.t16}}</span></td>
                                        <td v-if="detalle.t18 == detailsOrdenDespacho[indice].t18"><span class="badge badge-success">{{detalle.t18}}</span></td>
                                        <td v-if="detalle.t18 != detailsOrdenDespacho[indice].t18"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].t18 - detalle.t18}}</span></td>
                                        <td v-if="detalle.t20 == detailsOrdenDespacho[indice].t20"><span class="badge badge-success">{{detalle.t20}}</span></td>
                                        <td v-if="detalle.t20 != detailsOrdenDespacho[indice].t20"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].t20 - detalle.t20}}</span></td>
                                        <td v-if="detalle.t22 == detailsOrdenDespacho[indice].t22"><span class="badge badge-success">{{detalle.t22}}</span></td>
                                        <td v-if="detalle.t22 != detailsOrdenDespacho[indice].t22"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].t22 - detalle.t22}}</span></td>
                                        <td v-if="detalle.t24 == detailsOrdenDespacho[indice].t24"><span class="badge badge-success">{{detalle.t24}}</span></td>
                                        <td v-if="detalle.t24 != detailsOrdenDespacho[indice].t24"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].t24 - detalle.t24}}</span></td>
                                        <td v-if="detalle.t26 == detailsOrdenDespacho[indice].t26"><span class="badge badge-success">{{detalle.t26}}</span></td>
                                        <td v-if="detalle.t26 != detailsOrdenDespacho[indice].t26"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].t26 - detalle.t26}}</span></td>
                                        <td v-if="detalle.t28 == detailsOrdenDespacho[indice].t28"><span class="badge badge-success">{{detalle.t28}}</span></td>
                                        <td v-if="detalle.t28 != detailsOrdenDespacho[indice].t28"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].t28 - detalle.t28}}</span></td>
                                        <td v-if="detalle.t30 == detailsOrdenDespacho[indice].t30"><span class="badge badge-success">{{detalle.t30}}</span></td>
                                        <td v-if="detalle.t30 != detailsOrdenDespacho[indice].t30"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].t30 - detalle.t30}}</span></td>
                                        <td v-if="detalle.t32 == detailsOrdenDespacho[indice].t32"><span class="badge badge-success">{{detalle.t32}}</span></td>
                                        <td v-if="detalle.t32 != detailsOrdenDespacho[indice].t32"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].t32 - detalle.t32}}</span></td>
                                        <td v-if="detalle.t34 == detailsOrdenDespacho[indice].t34"><span class="badge badge-success">{{detalle.t34}}</span></td>
                                        <td v-if="detalle.t34 != detailsOrdenDespacho[indice].t34"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].t34 - detalle.t34}}</span></td>
                                        <td v-if="detalle.t36 == detailsOrdenDespacho[indice].t36"><span class="badge badge-success">{{detalle.t36}}</span></td>
                                        <td v-if="detalle.t36 != detailsOrdenDespacho[indice].t36"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].t36 - detalle.t36}}</span></td>
                                        <td v-if="detalle.t38 == detailsOrdenDespacho[indice].t38"><span class="badge badge-success">{{detalle.t38}}</span></td>
                                        <td v-if="detalle.t38 != detailsOrdenDespacho[indice].t38"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].t38 - detalle.t38}}</span></td>
                                        <td v-if="detalle.s == detailsOrdenDespacho[indice].s"><span class="badge badge-success">{{detalle.s}}</span></td>
                                        <td v-if="detalle.s != detailsOrdenDespacho[indice].s"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].s - detalle.s}}</span></td>
                                        <td v-if="detalle.m == detailsOrdenDespacho[indice].m"><span class="badge badge-success">{{detalle.m}}</span></td>
                                        <td v-if="detalle.m != detailsOrdenDespacho[indice].m"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].m - detalle.m}}</span></td>
                                        <td v-if="detalle.l == detailsOrdenDespacho[indice].l"><span class="badge badge-success">{{detalle.l}}</span></td>
                                        <td v-if="detalle.l != detailsOrdenDespacho[indice].l"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].l - detalle.l}}</span></td>
                                        <td v-if="detalle.xl == detailsOrdenDespacho[indice].xl"><span class="badge badge-success">{{detalle.xl}}</span></td>
                                        <td v-if="detalle.xl != detailsOrdenDespacho[indice].xl"><span class="badge badge-danger">{{detailsOrdenDespacho[indice].xl - detalle.xl}}</span></td>
                                        
                                        <td><span class="badge badge-info">{{detalle.total}}</span></td>
                                    </tr>
                                    <tr style="background-color:#008cff; color:white;">
                                        <td colspan="3" style="text-align:center">Totales</td>
                                        <td>{{totalesAlistamiento.t4}}</td>
                                        <td>{{totalesAlistamiento.t6}}</td>
                                        <td>{{totalesAlistamiento.t8}}</td>
                                        <td>{{totalesAlistamiento.t10}}</td>
                                        <td>{{totalesAlistamiento.t12}}</td>
                                        <td>{{totalesAlistamiento.t14}}</td>
                                        <td>{{totalesAlistamiento.t16}}</td>
                                        <td>{{totalesAlistamiento.t18}}</td>
                                        <td>{{totalesAlistamiento.t20}}</td>
                                        <td>{{totalesAlistamiento.t22}}</td>
                                        <td>{{totalesAlistamiento.t24}}</td>
                                        <td>{{totalesAlistamiento.t26}}</td>
                                        <td>{{totalesAlistamiento.t28}}</td>
                                        <td>{{totalesAlistamiento.t30}}</td>
                                        <td>{{totalesAlistamiento.t32}}</td>
                                        <td>{{totalesAlistamiento.t34}}</td>
                                        <td>{{totalesAlistamiento.t36}}</td>
                                        <td>{{totalesAlistamiento.t38}}</td>
                                        <td>{{totalesAlistamiento.s}}</td>
                                        <td>{{totalesAlistamiento.m}}</td>
                                        <td>{{totalesAlistamiento.l}}</td>
                                        <td>{{totalesAlistamiento.xl}}</td>
                                        <td>{{totalesAlistamiento.total}}</td>
                                    </tr>
                                </tbody>
                            </table>
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
        ordenesDespachoRevisar:[],
        display:true,
        idordenDespacho:'',
        orden:[],
        detailsOrdenDespacho:[],
        detailsOrdenAlistamiento:[],
        ordenAlistamiento:[],
        totalesDespacho:{
            t4:0,
            t6:0,
            t8:0,
            t10:0,
            t12:0,
            t14:0,
            t16:0,
            t18:0,
            t20:0,
            t22:0,
            t24:0,
            t26:0,
            t28:0,
            t30:0,
            t32:0,
            t34:0,
            t36:0,
            t38:0,
            s:0,
            m:0,
            l:0,
            xl:0,
            total:0
        },
        totalesAlistamiento:{
            t4:0,
            t6:0,
            t8:0,
            t10:0,
            t12:0,
            t14:0,
            t16:0,
            t18:0,
            t20:0,
            t22:0,
            t24:0,
            t26:0,
            t28:0,
            t30:0,
            t32:0,
            t34:0,
            t36:0,
            t38:0,
            s:0,
            m:0,
            l:0,
            xl:0,
            total:0
        }
    }
  },
    props: {

    },
    methods: {
        updateOrden(numero){
            this.idordenDespacho = numero
            this.display=false
            this.consultarOrdenDespacho()
            this.getDetailsOrdenDespacho()
            this.getDetailsAlistamiento()
            this.getOrdenAlistamiento()
        },
        getOrdenAlistamiento(){
            axios.get('infoOrdenAlistamiento/'+this.idordenDespacho)
            .then(response => {
                this.ordenAlistamiento = response.data
            })
        },
        ordenDespachoRevisar(){
            axios.get('ordenesRevisionGet')
            .then(response => {
                this.ordenesDespachoRevisar = response.data
            })
        },
        consultarOrdenDespacho(){
            axios.get('getOrdenDespacho/'+this.idordenDespacho)
            .then(response => {
                this.orden=response.data
            })
        },
        rechazaOrden(){
             swal({
                title: "Alerta!",
                text: "Si aceptas rechazar esta orden, se cancelara totalmente la orden.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                })
                .then((willDelete) => {
                if (willDelete) {
                    swal({
                        title: '¿Cual es la razòn por la que esta orden se rechaza?',
                        html: '<br><input class="form-control" placeholder="Ingrese razon" id="input-field">',
                        content: {
                            element: "input",
                                attributes: {
                                    placeholder: "Digite la razon",
                                    type: "text",
                                    id: "input-field",
                                    className: "form-control"
                                },
                            },
                            buttons: {
                                cancel: {
                                    visible: true,
                                    className: 'btn btn-danger',
                                    text : 'Cancelar'
                                },        			
                                confirm: {
                                    className : 'btn btn-success',
                                    text: 'Guardar'
                                }
                            },
                    }).then((razon) => {
                         let data = {razon:razon,consecutivo:this.idordenDespacho, _token: document.querySelector('meta[name="csrf-token"]').content};
                            axios.post('rechazaOrdenDespacho',data)
                            .then(response => {
                                swal('Informativo',response.data.message,'info').then((ref) => {
                                    if(ref){
                                        this.display = true
                                        this.ordenDespachoRevisar()
                                    }
                                });
                            })
                    });
                }
            });
        },
        aceptaOrden(){
            swal({
                title: "Alerta!",
                text: "Si apruebas esta orden, pasara directamente a empacado.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                })
                .then((willDelete) => {
                if (willDelete) {
                    swal({
                        title: '¿Cual es la razòn por la que esta orden se aprueba?',
                        html: '<br><input class="form-control" placeholder="Ingrese razon" id="input-field">',
                        content: {
                            element: "input",
                                attributes: {
                                    placeholder: "Digite la razon",
                                    type: "text",
                                    id: "input-field",
                                    className: "form-control"
                                },
                            },
                            buttons: {
                                cancel: {
                                    visible: true,
                                    className: 'btn btn-danger',
                                    text : 'Cancelar'
                                },        			
                                confirm: {
                                    className : 'btn btn-success',
                                    text: 'Guardar'
                                }
                            },
                    }).then((razon) => {
                         let data = {razon:razon,consecutivo:this.idordenDespacho, _token: document.querySelector('meta[name="csrf-token"]').content};
                            axios.post('apruebaOrdenDespacho',data)
                            .then(response => {
                                swal('Informativo',response.data.message,'success').then((ref) => {
                                    if(ref){
                                        this.display = true
                                        this.ordenDespachoRevisar()
                                    }
                                });
                            })
                    });
                }
            });
        },
        getDetailsOrdenDespacho(){
            axios.get('getDetailsOrdenDespacho/'+this.idordenDespacho).then(response => {
                this.detailsOrdenDespacho = response.data
                for (let index = 0; index < response.data.length; index++) {
                    const element = response.data[index];
                    this.totalesDespacho.t4 += element.t4;
                    this.totalesDespacho.t6 += element.t6;
                    this.totalesDespacho.t8 += element.t8;
                    this.totalesDespacho.t10 += element.t10;
                    this.totalesDespacho.t12 += element.t12;
                    this.totalesDespacho.t14 += element.t14;
                    this.totalesDespacho.t16 += element.t16;
                    this.totalesDespacho.t18 += element.t18;
                    this.totalesDespacho.t20 += element.t20;
                    this.totalesDespacho.t22 += element.t22;
                    this.totalesDespacho.t24 += element.t24;
                    this.totalesDespacho.t26 += element.t26;
                    this.totalesDespacho.t28 += element.t28;
                    this.totalesDespacho.t30 += element.t30;
                    this.totalesDespacho.t32 += element.t32;
                    this.totalesDespacho.t34 += element.t34;
                    this.totalesDespacho.t36 += element.t36;
                    this.totalesDespacho.t38 += element.t38;
                    this.totalesDespacho.s += element.s;
                    this.totalesDespacho.m += element.m;
                    this.totalesDespacho.l += element.l;
                    this.totalesDespacho.xl += element.xl;
                    this.totalesDespacho.total += element.total;
                }
            });
        },
        getDetailsAlistamiento(){
             axios.get('getDetailAlistamiento/'+this.idordenDespacho)
            .then(response => {
                this.detailsOrdenAlistamiento=response.data
                for (let index = 0; index < response.data.length; index++) {
                    const element = response.data[index];
                    this.totalesAlistamiento.t4 += element.t4;
                    this.totalesAlistamiento.t6 += element.t6;
                    this.totalesAlistamiento.t8 += element.t8;
                    this.totalesAlistamiento.t10 += element.t10;
                    this.totalesAlistamiento.t12 += element.t12;
                    this.totalesAlistamiento.t14 += element.t14;
                    this.totalesAlistamiento.t16 += element.t16;
                    this.totalesAlistamiento.t18 += element.t18;
                    this.totalesAlistamiento.t20 += element.t20;
                    this.totalesAlistamiento.t22 += element.t22;
                    this.totalesAlistamiento.t24 += element.t24;
                    this.totalesAlistamiento.t26 += element.t26;
                    this.totalesAlistamiento.t28 += element.t28;
                    this.totalesAlistamiento.t30 += element.t30;
                    this.totalesAlistamiento.t32 += element.t32;
                    this.totalesAlistamiento.t34 += element.t34;
                    this.totalesAlistamiento.t36 += element.t36;
                    this.totalesAlistamiento.t38 += element.t38;
                    this.totalesAlistamiento.s += element.s;
                    this.totalesAlistamiento.m += element.m;
                    this.totalesAlistamiento.l += element.l;
                    this.totalesAlistamiento.xl += element.xl;
                    this.totalesAlistamiento.total += element.total;
                }
            })
        },
        alistarNuevamente(){
             swal({
                title: "Alerta!",
                text: "Una vez aceptes mandar a alistar nuevamente la orden, esta será mostrada al operario.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                })
                .then((willDelete) => {
                if (willDelete) {
                     let data = {consecutivo:this.idordenDespacho, _token: document.querySelector('meta[name="csrf-token"]').content};
                    axios.post('alistarDeNuevo',data)
                    .then(response => {
                       swal('Informativo',response.data,'info').then((ref) => {
                           if(ref){
                               this.display = true
                               this.ordenDespachoRevisar()
                           }
                       });
                    })
                }
            });
        }


    },
    mounted(){
        this.ordenDespachoRevisar();
    },

    

}
</script>