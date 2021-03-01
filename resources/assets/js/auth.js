
import VUE from 'vue'
import ElementUi from "element-ui"
import Axios from 'axios'
import App from  './components/auth/LoginComponent.vue'
import zh_tw from "./tools/zh-TW";

window.Vue = VUE;
import locale from 'element-ui/lib/locale'
locale.use(zh_tw);

Vue.use(ElementUi);
window.axios = Axios;
window.axios.defaults.headers.common = {
    'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').getAttribute('content'),
    'X-Requested-With': 'XMLHttpRequest'
};

const app = new Vue({
    render: h => h(App)
}).$mount('#app');


