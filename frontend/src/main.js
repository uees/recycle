import Vue from 'vue'
import MuseUI from 'muse-ui';
import App from './App.vue'
import router from './router'
import './registerServiceWorker'

import 'muse-ui/dist/muse-ui.css';
import 'muse-ui-message/dist/muse-ui-message.css';

Vue.use(MuseUI);

Vue.config.productionTip = false

// H5的设计稿一般设计为640x1136px即可, 既满足了显示需求，又能降低用户加载图片需要的带宽
new Vue({
  router,
  render: h => h(App)
}).$mount('#app')
