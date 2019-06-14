import Vue from 'vue'
import MuseUI from 'muse-ui'
import Loading from 'muse-ui-loading';
import App from './App.vue'
import store from './store'
import router from './router'
import './permission'
import './registerServiceWorker'

import 'typeface-roboto'
import 'muse-ui/dist/muse-ui.css'
import 'muse-ui-loading/dist/muse-ui-loading.css';
// import 'muse-ui-message/dist/muse-ui-message.css'

Vue.use(MuseUI);
Vue.use(Loading);

Vue.config.productionTip = false

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
