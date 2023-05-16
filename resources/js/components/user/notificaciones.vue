<template>
    <li class="nav-item dropdown hidden-caret">
		<a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<i class="fa fa-bell"></i>
			<span class="notification">{{notificaciones.length}}</span>
		</a>
		<ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
		    <li>
				<div class="dropdown-title">Tienes {{notificaciones.length}} nuevas notificaciones</div>
			</li>
			<li>
				<div class="notif-scroll scrollbar-outer">
                    <div class="notif-center">
                        <a v-for="notificacion in notificaciones" href="#">
                            <div class="notif-img"> 
                                <i class="fa fa-home"></i>
                            </div>
                            <div class="notif-content">
                                <span class="block">
                                   {{notificacion.title}}
                                </span>
                                <span class="time">{{notificacion.descripcion}}</span> 
                            </div>
                        </a>
                    </div>
				</div>
			</li>
			<li>
				<a class="see-all" href="javascript:void(0);">Ver todas las notificaciones<i class="fa fa-angle-right"></i> </a>
			</li>
		</ul>
	</li>
</template>
<script>
export default {
  created () {
      this.getNotificaciones();
      this.startInterval();
  },
  data () {
    return {
        notificaciones:[]
    }
  },
  methods: {
      startInterval() {
      setInterval(() => {
        this.getNotificaciones();
      }, 3000);
    },
      getNotificaciones(){
          let url = "/user/notificaciones";
          let baseURL = document.querySelector('meta[name="base-url"]').content; 
          axios.get(baseURL+url)
          .then(res => {
              this.notificaciones = res.data
          })
          .catch(err => {
              console.error(err); 
          })
      }
  },
    
}
</script>
