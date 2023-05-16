@extends('layouts.appp')

@section('content')
    <div id="app"></div>
        <div id="appv">
            <section>
            <div class="panel-header bg-primary-gradient">
              <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
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
                        <button v-on:click="AdicionarUsuarioModal" class="btn btn-info">
                          <i class="fa fa-user"></i> <i class="fa fa-plus"></i> Adicionar Usuario
                        </button>
                        <div class="mt-5 table-responsive">
                          <table id="table-user" class="table">
                            <thead>
                              <tr>
                                <th>id</th>
                                <th>Nombres</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Tienda Cargo</th>
                                <th>fecha Creación</th>
                                <th>Acciones</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(user,index) in users">
                                  <td v-html="user.id">02</td>
                                  <td v-html="user.names+' '+user.apellidos">Juan Fernando</td>
                                  <td v-html="user.email">juanfer@siver.com</td>
                                  <td v-html="user.rol">Administrador</td>
                                  <td v-if="user.tiendacargo" v-html="user.tiendacargo"></td>
                                  <td v-else>N/A</td>
                                  <td v-html="user.creacion">18/04/2022</td>
                                  <td ><i style="cursor:pointer;" v-on:click="modificarUsuario(index)" class="fa fa-edit" aria-hidden="true"></i></td>
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
                        <label class="modal-title"  v-if="editUser==false">Crear nuevo usuario</label>
                        <label class="modal-title"  v-if="editUser==true">Modificar Usuario</label>
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
                              <form class="form-group"  id="reguser">
                                  <div class="input-group mb-3">
                                  <span class="input-group-text">Documento de identidad: </span>
                                  <input
                                    class="form-control"
                                    v-model="user.documento"
                                    @input="verificarUsuario"
                                    type="number"
                                    required
                                    v-if="editUser == false || !user.documento" />
                                    
                                    <input
                                    class="form-control"
                                    v-model="user.documento"
                                    @input="verificarUsuario"
                                    type="number"
                                    required
                                    v-if="editUser == true && user.documento" disabled/>
                                </div>
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
                                <div v-if="editUser==false" class="input-group mb-3">
                                  <span class="input-group-text">Pasword: </span>
                                  <input
                                  id="txtPassword"
                                    type="password"
                                    class="form-control"
                                    v-model="user.password"
                                    required
                                  />
                                  <div class="input-group-append">
                                        <button id="show_password" class="btn btn-primary" type="button" v-on:click="mostrarPassword()"> <span class="fa fa-eye-slash icon"></span> </button>
                                  </div>
                                </div>
                                <div class="input-group mb-3">
                                  <span class="input-group-text">Rol</span>
                                  <select class="form-control" v-model="user.rol" required>
                                    <option
                                      v-for="rol in roles"
                                      
                                      v-bind:value="rol.id"
                                      v-html="rol.descripcion"
                                    >
                                    </option>
                                  </select>
                                </div>
                                <div class="input-group mb-3">
                                  <span class="input-group-text">Tienda a cargo: </span>
                                  <input
                                    class="form-control"
                                    v-model="user.tiendacargo"
                                    required
                                  />
                                </div>
                                <p>La tienda a cargo se especifica para los usuarios que realizan las libranzas en cada tienda.</p>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button v-if="showRegisterBtnUser==true" v-on:click="registrarUsuario" form="reguser" class="btn btn-success">
                          <i class="fa fa-plus"></i>
                          <i class="fa fa-user"></i> Registrar Usuario
                        </button>
                        <button class="btn btn-success" v-if="editUser==true" v-on:click="saveEditUser()" form="reguser"><i class="fa fa-edit"></i> Modificar Usuario</button>
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
        </div>
@endsection

@push('scripts-custom')
<script src="https://unpkg.com/axios@0.27.2/dist/axios.min.js"></script>
<script src="https://unpkg.com/vue@next"></script>
    
    <script>
        const AttributeBinding = {
          mounted() {
            this.getRolesOfUser();
            this.getUsuariosAll();
          },
          data() {
              return{
                  user: {
                    documento: "",
                    nombres: "",
                    apellido: "",
                    correo: "",
                    password: "bless2022",
                    rol: 1,
                    tiendacargo: "N/A"
                  },
                  roles:[],
                  showRegisterBtnUser:false,
                  users:[],
                  editUser:false,
                  
              }
          },
          methods: {
              saveEditUser:  function(e){
                 e.preventDefault();
                    if(this.validateFormCreateUser()){
                        return this.enviarPeticionModificacionDeUsuario();
                    }
              },
              modificarUsuario(index){
                this.editUser=true
                let user = this.users[index];
                this.user.documento=user.documento
                this.user.nombres=user.names
                this.user.apellido=user.apellidos
                this.user.correo=user.email
                this.user.password=''
                this.user.rol=user.rol_id
                this.user.tiendacargo=user.tiendacargo? user.tiendacargo:"N/A" 
                $('#modaluser').modal('show');
              },
              getUsuariosAll(){
                axios.get('../user/all').then(response => {
                    this.users = response.data
                    this.initDatatable();
                });  
              },
              initDatatable(){
                this.$nextTick(() => {
                    $('#table-user').DataTable();
                });
              },
              mostrarPassword(){
                  var cambio = document.getElementById("txtPassword");
        		if(cambio.type == "password"){
        			cambio.type = "text";
        			$('.icon').removeClass('fa fa-eye-slash').addClass('fa fa-eye');
        		}else{
        			cambio.type = "password";
        			$('.icon').removeClass('fa fa-eye').addClass('fa fa-eye-slash');
        		}
              },
              registrarUsuario : function(e){
                  e.preventDefault();
                    if(this.validateFormCreateUser()){
                        return this.enviarPeticionCreacionDeUsuario();
                    }
              },
              enviarPeticionModificacionDeUsuario(){
                  let data = {user:this.user, _token: document.querySelector('meta[name="csrf-token"]').content};
                  axios.put('users/editar',data).then(response => {
                        if(response.data.message){
                            swal(response.data.message, 'success')
                            this.user.documento=""
                            this.user.nombres=""
                            this.user.apellido=""
                            this.user.correo=""
                            this.user.password="bless2022"
                            this.user.rol=1
                            this.user.tiendacargo="N/A"
                            $('#modaluser').modal('hide');
                        }
                    });
              },
              enviarPeticionCreacionDeUsuario(){
                  let data = {user:this.user, _token: document.querySelector('meta[name="csrf-token"]').content};
                    axios.put('users/create',data).then(response => {
                        if(response.data.message){
                            swal(response.data.message, 'success')
                            this.user.documento=""
                            this.user.nombres=""
                            this.user.apellido=""
                            this.user.correo=""
                            this.user.password="bless2022"
                            this.user.rol=1
                            this.user.tiendacargo="N/A"
                            $('#modaluser').modal('hide');
                        }
                    });
              },
              validateFormCreateUser :  function (e) {
                  if(this.user.documento != "" && this.user.nombres != "" && this.user.apellido != "" && this.user.correo != "" && this.user.password != ""){
                      return true;
                  }
                    swal("Hay campos sin rellenar que son necesarios para la creación del usuario.")
                    return e.preventDefault();
              },
              AdicionarUsuarioModal(){
                  this.editUser=false
                  this.user.documento=""
                            this.user.nombres=""
                            this.user.apellido=""
                            this.user.correo=""
                            this.user.password="bless2022"
                            this.user.rol=1
                            this.user.tiendacargo="N/A"
                 $('#modaluser').modal('show');
              },
              getRolesOfUser(){
                  axios.get("../../rol/all").then(res => {
                    this.roles = res.data;
                  });
              },
              verificarUsuario(){
                    axios.get('users/getUserByDocument/'+this.user.documento).then(response => {
                       if(response.data.user){
                           this.showRegisterBtnUser = false;
                           swal("El usuario con ese documento ya existe. correo: "+response.data.user.email)
                       }else{
                           if(this.editUser == false){
                               this.showRegisterBtnUser = true;
                           }
                           
                       }
                    });                  
              },
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
                
                      this.user.correo = nombre+"@siver.com";
                },
          },
        }
        var example2=Vue.createApp(AttributeBinding).mount('#appv')

    </script>
@endpush