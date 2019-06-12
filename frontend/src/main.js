import Vue from 'vue'
import MuseUI from 'muse-ui'
import App from './App.vue'
import router from './router'
import './registerServiceWorker'

import 'material-design-icons'
import 'typeface-roboto'
import 'muse-ui/dist/muse-ui.css'
import 'muse-ui-message/dist/muse-ui-message.css'

Vue.use(MuseUI);

Vue.config.productionTip = false

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')
