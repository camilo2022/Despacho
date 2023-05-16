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
            <h2 class="text-white fw-bold">Usuarios > Administrar</h2>
            <h5 class="text-white">Administracion de usuarios del sistema</h5>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-body page-inner mt--5">
      <div class="row mt--2">
        <div class="col-md-12 col-12 col-xs-12">
          <div class="card full-height">
            <div class="card-body">
              <div class="container">
                <button @click="createUser" class="btn btn-info">
                  <i class="fa fa-user"></i> <i class="fa fa-plus"></i>
                </button>
                <div class="mt-5 table-responsive">
                  <table id="table-user" class="table">
                    <thead>
                      <tr>
                        <th>id</th>
                        <th>Nombres</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>fecha Creación</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(user, indice) in usuarios">
                        <td>{{ indice + 1 }}</td>
                        <td>{{ user.names }}</td>
                        <td>{{ user.email }}</td>
                        <td>{{ user.rol }}</td>
                        <td>{{ user.creacion }}</td>
                        <td>
                          <button class="btn btn-danger btn-xs">
                            <i class="fa fa-times-circle"></i>
                          </button>
                          <button class="btn btn-info btn-xs">
                            <i class="fa fa-cannabis"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--Modals-->
        <div
          class="modal fade"
          id="modaluser"
          tabindex="-1"
          role="dialog"
          aria-labelledby="exampleModalLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <label class="modal-title">Crear nuevo usuario</label>
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
                    <div class="container">
                      <form class="form-group" id="reguser">
                        <div class="input-group mb-3">
                          <span class="input-group-text">Nombres: </span>
                          <input
                            class="form-control"
                            v-model="user.nombres"
                            required
                          />
                        </div>
                        <div class="input-group mb-3">
                          <span class="input-group-text">Apellidos: </span>
                          <input
                            class="form-control"
                            v-model="user.apellido"
                            @input="generateEmail"
                            required
                          />
                        </div>
                        <div class="input-group mb-3">
                          <span class="input-group-text">Correo: </span>
                          <input
                            class="form-control"
                            v-model="user.correo"
                            required
                            disabled
                          />
                        </div>
                        <div class="input-group mb-3">
                          <span class="input-group-text">Pasword: </span>
                          <input
                            type="password"
                            class="form-control"
                            v-model="user.password"
                            required
                          />
                          <p>
                          <small>Contraseña por defecto: <b>bless2021</b></small></p>
                        </div>
                        <div class="input-group mb-3">
                          <span class="input-group-text">Rol</span>
                          <select class="form-control">
                            <option disabled selected>
                              Seleccion el rol de usuario
                            </option>
                            <option
                              v-for="rol in roles"
                              v-model="user.rol"
                              v-bind:value="rol.id"
                            >
                              {{ rol.descripcion }}
                            </option>
                          </select>
                                </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button onClick="registrarUsuario" form="reguser" class="btn btn-success">
                  <i class="fa fa-plus"></i>
                  <i class="fa fa-user"></i> Registrar Usuario
                </button>
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
  </section>
</template>
<script>
export default {
  watch: {
  },
  
  mounted() {
    this.getUsuarios();
    this.getRoles();
  },
  data() {
    return {
      user: {
        nombres: "",
        apellido: "",
        correo: "",
        password: "bless2021",
        rol: "",
      },
      usuarios: [],
      usuario: [],
      roles: [],
    };
  },
  methods: {
    generateEmail(){
      let arApellido = '';
      let arNombre = '';
      let nombre=this.user.nombres+this.user.apellido;
      if(this.user.nombres.includes(" ") && this.user.apellido.includes(" ")){
        arNombre = this.user.nombres.split(" ");
        arApellido = this.user.apellido.split(" ");
        nombre = arNombre[0]+arApellido[0];
        nombre=nombre.toLowerCase();
      }else if(this.user.nombres.includes(" ") && !this.user.apellido.includes(" ")){
        arNombre = this.user.nombres.split(" ");
        nombre = arNombre[0]+this.user.apellido;
        nombre=nombre.toLowerCase();
      }else{
        arApellido = this.user.apellido.split(" ");
        nombre = this.user.nombres+arApellido[0];
        nombre=nombre.toLowerCase();
      }

      this.user.correo = nombre+"@bless.com";
    },
    getRoles() {
      axios.get("../../rol/all").then((res) => {
        this.roles = res.data;
      });
    },
    initDatatable() {
      this.$nextTick(() => {
        $("#table-user").DataTable({
          lengthMenu: [
            [5, 10, 15, 20, 25, 30, 100],
            [5, 10, 15, 20, 25, 30, 100],
          ],
        });
      });
    },
    registrarUsuario(){
      alert('registrar usuario');
    },
    getUsuarios() {
      axios.get("../../user/all").then((res) => {
        this.usuarios = res.data;
        this.initDatatable();
      });
    },
    createUser() {
      $("#modaluser").modal("show");
    },
  },
};
</script>