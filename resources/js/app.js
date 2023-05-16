/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

const { default: Vue } = require('vue');

require('./bootstrap');


window.Vue = require('vue').default;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('createsolicitud',require('./components/solicitudes/CreateSolicitudComponent').default);
Vue.component('misolicitudes',require('./components/solicitudes/MisSolicitudesComponent').default);
Vue.component('bankimg-referencia-check', require('./components/bankimg/ReferenciaChecks').default);
Vue.component('solicitudes-list',require('./components/solicitudes/listarSolicitudes').default);
Vue.component('user-insumos',require('./components/user/insumos').default);
Vue.component('user-solicitante',require('./components/user/solicitante').default);

Vue.component('user-notificaciones-normal',require('./components/user/notificacionesusernormal').default);
Vue.component('user-notificaciones',require('./components/user/notificaciones').default);
Vue.component('user-administrador',require('./components/user/administrador').default);

/* Componentes de despachos*/
Vue.component('listado-ordenes-despachos',require('./components/sisdepachos/OrdenesDespachos').default);
Vue.component('listado-pendientes-alistar',require('./components/sisdepachos/pendienteAlistar').default);
Vue.component('listado-pendientes-empacar',require('./components/sisdepachos/pendienteEmpacar').default);
Vue.component('listado-pendientes-facturar',require('./components/sisdepachos/facturar/listadoPFacturar').default);
Vue.component('listados-pendientes-revisar',require('./components/sisdepachos/listadoPendientesRevisar').default);


/* Componentes de despachos*/


Vue.component('readqr-user', require('./components/user/readqr').default);
Vue.component('readqr-list', require('./components/user/readqrList').default);

Vue.component('administracion-users', require('./components/administrador/admin-users').default);

Vue.component('sincronize-terceros', require('./components/sincronize/terceros').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});

const app2 = new Vue({
    el: '#app2',
});

if (process.env.MIX_ENV_MODE === 'production') {
    Vue.config.devtools = false;
    Vue.config.debug = false;
    Vue.config.silent = true; 
}
