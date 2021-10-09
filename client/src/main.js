import Vue from "vue";
import App from "./App.vue";
import modal from "vue-js-modal";
import VueRouter from "vue-router"
import Agenda from "./components/Agenda";
import Authorization from "./components/Authorization";
import ForgotPassword from "./components/ForgotPassword";
import ResetPassword from "./components/ResetPassword";

// Vue.config.productionTip = false;
Vue.use(modal, { dialog: true, dynamic: true });
Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  routes: [
    {
      path: '/',
      component: Authorization
    },
    {
      path: '/forgot-password',
      component: ForgotPassword
    },
    {
      path: '/reset-password',
      component: ResetPassword
    },
    {
      path: '/home',
      component: Agenda
    },
  ]
})

new Vue({
  render: h => h(App),
  router: router
}).$mount("#app");
