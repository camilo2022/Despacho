<template>
  <div class="container-fluid">
    <p style="font-size: 1.2em">
      Se mostrarán las solicitudes del dia actual. <br />
      Hoy es <b style="font-size: 1.2em">{{ fechaActual }}</b>
    </p>
    <div class="row w-100">
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-info card-round">
          <div class="card-body">
            <div class="row">
              <div class="col-5">
                <div class="icon-big text-center">
                  <i class="flaticon-users"></i>
                </div>
              </div>
              <div class="col-7 col-stats">
                <div class="numbers">
                  <p class="card-category">Solicitudes</p>
                  <h4 class="card-title">{{solicitudes.length}}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-warning card-round">
          <div class="card-body">
            <div class="row">
              <div class="col-5">
                <div class="icon-big text-center">
                  <i class="flaticon-interface-6"></i>
                </div>
              </div>
              <div class="col-7 col-stats">
                <div class="numbers">
                  <p class="card-category">Solicitudes por aprobar</p>
                  <h4 class="card-title">{{pendAprobar}}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-success card-round">
          <div class="card-body">
            <div class="row">
              <div class="col-5">
                <div class="icon-big text-center">
                  <i class="flaticon-analytics"></i>
                </div>
              </div>
              <div class="col-7 col-stats">
                <div class="numbers">
                  <p class="card-category">Pendiente por Imprimir</p>
                  <h4 class="card-title">{{pendImprimir}}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-danger card-round">
          <div class="card-body">
            <div class="row">
              <div class="col-5">
                <div class="icon-big text-center">
                  <i class="flaticon-interface-6"></i>
                </div>
              </div>
              <div class="col-7 col-stats">
                <div class="numbers">
                  <p class="card-category">Solicitudes Rechazadas</p>
                  <h4 class="card-title">{{pendRechazadas}}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid">
        <div class="card card-body table-responsive">
      <table id="solicitudesTabla" class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th style="width: 15%" scope="col">Fecha Creacion</th>
            <th scope="col">Solicitante</th>
            <th scope="col">Codigo</th>
            <th style="width: 15%" scope="col">Destino</th>
            <th scope="col">Estado</th>
            <th scope="col">Observación</th>
            <th style="width: 20%" scope="col">Ultima Revisión</th>
            <th style="width: 15%" scope="col">Encargado revision</th>
            <th style="width: 20%" scope="col">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="solicitud in solicitudes">
            <th scope="row">{{ solicitud.id }}</th>
            <td>{{ solicitud.fechaCreacion }}</td>
            <td>{{ solicitud.solicitante }}</td>
            <td>{{ solicitud.codigo }}</td>
            <td>{{ solicitud.tipoSolicitud }}</td>
            <td v-if="solicitud.estado === 'Creada'">
              <span class="btn w-100 btn-warning btn-round">Por Aprobar</span>
            </td>
            <td v-if="solicitud.estado === 'Impresas'">
              <span class="btn w-100 btn-success btn-round"
                >Tickets Impresos</span
              >
            </td>
            <td v-else-if="solicitud.estado === 'Aceptada'">
              <span class="btn w-100 btn-success btn-round">Aprobada</span>
            </td>
            <td v-if="solicitud.estado === 'No Aprobada'">
              <span class="btn w-100 btn-danger btn-round">No Aprobada</span>
            </td>
            <td style="text-align: center">
              <button
                @click="getDescripcion(solicitud.codigo)"
                alt="ver Detalle de Solicitud"
                class="btn btn-xs btn-info btn-round"
              >
                <span class="btn-label">
                  <i class="fa fa-eye"></i>
                </span>
              </button>
            </td>
            <td>{{ solicitud.revsolicitud }}</td>
                <td>{{ solicitud.encargadorev }}</td>
                <td>
                  <button
                    @click="viewDetailsItems(solicitud.codigo)"
                    alt="ver Detalle de Solicitud"
                    class="btn btn-xs btn-info btn-round"
                  >
                    <span class="btn-label">
                      <i class="fa fa-eye"></i>
                    </span>
                  </button>
                  <button
                    v-if="solicitud.estado === 'Creada'"
                    @click="cancelarSolicitud(solicitud.codigo)"
                    class="text-center btn btn-xs btn-danger btn-round"
                  >
                    <span class="btn-label text-center">
                      <i class="flaticon-cross"></i>
                    </span>
                  </button>

                  <button
                    v-if="solicitud.estado === 'Creada'"
                    @click="aprobarSolicitud(solicitud.codigo)"
                    class="btn btn-xs btn-success btn-round"
                  >
                    <span class="btn-label">
                      <i class="fa fa-check"></i>
                    </span>
                  </button>
                  <button
                    v-if="solicitud.estado === 'Aceptada'"
                    @click="informarImpresionTickets(solicitud.codigo)"
                    class="btn btn-xs btn-success btn-round"
                  >
                    <span class="btn-label">
                      <i class="flaticon-list"></i>
                    </span>
                  </button>
                </td>
          </tr>
        </tbody>
        <!--Modals-->
        <div
          class="modal fade"
          id="modalDescripcion"
          tabindex="-1"
          role="dialog"
          aria-labelledby="exampleModalLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header text-center">
                <label class="modal-title text-center"
                  >Observación de la solicitud #<b>{{ codigo }}</b></label
                >
                <button
                  type="button"
                  class="close"
                  data-dismiss="modal"
                  aria-label="Close"
                >
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="container">
                  <div class="card-body">
                    {{ descripcion }}
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button
                  type="button"
                  class="btn btn-secondary"
                  data-dismiss="modal"
                >
                  Close
                </button>
              </div>
            </div>
          </div>
        </div>
        <!--Modals-->
        <!--Modals-->
        <div
          class="modal fade"
          id="modalItems"
          tabindex="-1"
          role="dialog"
          aria-labelledby="exampleModalLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header text-center">
                <h2 class="modal-title text-center"
                  >Items de la Solicitud # <b>{{ codigo }}</b></h2
                >
                <button
                  type="button"
                  class="close"
                  data-dismiss="modal"
                  aria-label="Close"
                >
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="container">
                  <div class="card-body">
                    <span
                      >Hay una cantidad Total de: <b>{{ cantotal }}</b></span
                    >
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
                            <th>{{ item.id }}</th>
                            <td>{{ item.referencia }}</td>
                            <td>{{ item.codbarra }}</td>
                            <td>{{ item.cantidad }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button
                  type="button"
                  class="btn btn-secondary"
                  data-dismiss="modal"
                >
                  Close
                </button>
              </div>
            </div>
          </div>
        </div>
        <!--Modals-->
      </table>
    </div>
    </div>
  </div>
</template>
<script>
import swal from "sweetalert";
import download from "downloadjs";
import VueProgressBar from "vue-progressbar";
export default {
  mounted() {
    this.getFechaActual();
    this.getSolicitudes();
    this.startInterval();
    this.getCantidad();
  },

  data() {
    return {
      fechaActual: "",
      solicitudes: [],
      descripcion: '',
      pendAprobar : 0,
      pendImprimir : 0,
      pendRechazadas:0,
      items: [],
      cantotal: 0,
      codigo:0
    };
  },
  methods: {
    startInterval() {
      setInterval(() => {
        this.getSolicitudes();
        this.getCantidad();
      }, 60000);
    },
    getFechaActual() {
      var meses = new Array(
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre"
      );
      let f = new Date();
      this.fechaActual =
        f.getDate() + " de " + meses[f.getMonth()] + " de " + f.getFullYear();
    },
    initDatatable() {
      this.$nextTick(() => {
        $("#solicitudesTabla").DataTable({
          dom:
            "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            lengthMenu: [
            [5, 10, 15, 20, 25, 30, 100],
            [5, 10, 15, 20, 25, 30, 100],
          ],
        });
      });
    },
    getSolicitudes() {
      let url = "../../solicitudes/allday";
      axios.get(url).then((res) => {
        this.solicitudes = res.data;
        $("#solicitudesTabla").DataTable().destroy();
        this.initDatatable();
      });
    },
    informarImpresionTickets(codigo) {
      let params = {
        _token: document.querySelector('meta[name="csrf-token"]').content,
        codigo: codigo,
      };
      swal({
        title: "¿Estás segur@?",
        text:
          "Que deseas informarle al solicitante que ya estan impresas las etiquetas de la solicitud #" +
          codigo,
        icon: "warning",
        buttons: ["Cancelar", "Si, Estoy seguro"],
        dangerMode: true,
      }).then((confirm) => {
        if (confirm) {
          axios
            .post("../../solicitudes/informarSolicitante", params)
            .then((response) => {
              this.getSolicitudes();
              swal("Exitoso", response.data, "success");
            });
        }
      });
    },

    aprobarSolicitud(codigo) {
      this.$Progress.start();
      axios
        .post("../../solicitudes/aprove", { codigo: codigo })
        .then((response) => {
          this.$Progress.finish();
          const content = response.headers["content-type"];
          const filename = response.headers["content-disposition"]
            .split("; ")[1]
            .split("=")[1];
          download(response.data, filename, content);
          this.getSolicitudes();
          swal("Exitoso", "Se ha aprobado con èxito la solicitud", "success");
        })
    },
    cancelarSolicitud(codigo) {
      swal({
        title: "¿Estás segur@?",
        text: "Insumos no podrá saber de esta solicitud ya que la cancelaràs totalmente.",
        icon: "warning",
        buttons: ["Cancelar", "Si, Estoy seguro"],
        dangerMode: true,
      }).then((isConfirm) => {
        if (isConfirm) {
          swal("Describa el motivo de la cancelación: ", {
            content: "input",
          }).then((value) => {
            let url = "../../solicitud/";
            let params = {
              _token: document.querySelector('meta[name="csrf-token"]').content,
              _method: "DELETE",
              codigo: codigo,
              observacion: value,
              tipo: "insumos",
            };
            axios.post(url, params).then((response) => {
              this.getSolicitudes();
              this.getCantidad();
              swal("Exitoso", response.data.message, "success");
            });
          });
        } else {
          swal("Cancelado", "Has cancelado eliminar la solicitud:)", "error");
        }
      });
    },
    getDescripcion(codigo) {
      this.codigo = codigo;
      axios.get("../../solicitud/" + codigo).then((response) => {
        this.descripcion = response.data[0]["observacion"];
      });
      $("#modalDescripcion").modal("show");
    },
    getCantidad(){
        this.pendAprobar = 0;
        this.pendImprimir = 0;
        this.pendRechazadas = 0;
        let url = "../../solicitudes/allday";
        axios.get(url).then(response => {
           let i=0;
            for(i;i<response.data.length;i++){
                if(response.data[i].estado == 'Creada'){
                    this.pendAprobar++;
                }else  if(response.data[i].estado == 'Aceptada'){
                    this.pendImprimir++;
                }else if(response.data[i].estado == 'No Aprobada'){
                    this.pendRechazadas++;
                }
            }

        });

    },
    iniTablesItems() {
      this.$nextTick(() => {
        $("#tablaitems").DataTable({
          lengthMenu: [
            [5, 10, 15, 20, 25, 30, 100],
            [5, 10, 15, 20, 25, 30, 100],
          ],
        });
      });
    },
    viewDetailsItems(codigo) {
      axios.get("../../solicitud/items/" + codigo).then((response) => {
        this.codigo = codigo;
        this.cantotal = response.data.cantotal;
        this.items = response.data.items;
        $("#tablaitems").DataTable().destroy();
        this.iniTablesItems();
      });

      $("#modalItems").modal("show");
    },
  },
};
</script>