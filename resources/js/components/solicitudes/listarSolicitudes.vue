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
            <h2 class="text-white fw-bold">Solicitudes > ver Solicitudes</h2>
            <h5 class="text-white">
              Formulario de recepción de solicitudes para la impresión
            </h5>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-body page-inner mt--5">
      <div class="card full-height">
        <div class="form-group form-inline table-responsive">
          <table id="solicitudes" class="table m-4">
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
                  <span class="btn w-100 btn-warning btn-round"
                    >Por Aprobar</span
                  >
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
                  <span class="btn w-100 btn-danger btn-round"
                    >No Aprobada</span
                  >
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
          </table>
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
        </div>
      </div>
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
              <label class="modal-title text-center"
                >Items de la Solicitud # <b>{{ codigo }}</b></label
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
    </div>
  </section>
</template>
<script>
import swal from "sweetalert";
import download from "downloadjs";
import VueProgressBar from "vue-progressbar";

export default {
  mounted() {
    this.getSolicitudes();
    this.startInterval();
  },
  data() {
    return {
      solicitudes: [],
      codigo: 0,
      descripcion: null,
      items: [],
      cantotal: 0,
    };
  },
  methods: {
    startInterval() {
      setInterval(() => {
        this.getSolicitudes();
      }, 60000);
    },
    initDatatable() {
      this.$nextTick(() => {
        $("#solicitudes").DataTable({
          dom:
            "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        });
      });
    },
    getSolicitudes() {
      let url = "../../solicitudes/all";
      axios.get(url).then((res) => {
        this.solicitudes = res.data;
        $("#solicitudes").DataTable().destroy();
        this.initDatatable();
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
        .catch((err) => {
          swal(
            "Error",
            "Ha ocurrido un error al aprobar la solicitud",
            "error"
          );
        });
    },
    getDescripcion(codigo) {
      this.codigo = codigo;
      axios.get("../../solicitud/" + codigo).then((response) => {
        this.descripcion = response.data[0]["observacion"];
      });
      $("#modalDescripcion").modal("show");
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
              swal("Exitoso", response.data.message, "success");
            });
          });
        } else {
          swal("Cancelado", "Has cancelado eliminar la solicitud:)", "error");
        }
      });
    },
  },
};
</script>