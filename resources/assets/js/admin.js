/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

//import { Row,Col,Breadcrumb,BreadcrumbItem,Submenu,Menu,MenuItemGroup,MenuItem,DropdownMenu,Dropdown,DropdownItem, Button , Input, Select, Dialog, Pagination, Table, TableColumn} from 'element-ui';
import ElementUi from "element-ui"
import Axios from 'axios'
import router from './router/index'
import App from './components/IndexComponent.vue'
import VUE from 'vue'
//設置繁體中文
import zh_tw from './tools/zh-TW'
import locale from 'element-ui/lib/locale'
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import NewDialog from './tools/element-ui-dialog'

import VueQuillEditor from 'vue-quill-editor';
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'

Vue.use(VueQuillEditor);
window.Vue = VUE;

locale.use(zh_tw);
Vue.use(ElementUi);


window.axios = Axios;
window.axios.defaults.headers.common = {
    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
    'X-Requested-With': 'XMLHttpRequest'
};
//Vue.prototype.$http = Axios;
Vue.prototype.$ajax = Axios;
const app = new Vue({
    router,
    render: h => h(App)
}).$mount('#app');
let openLogout = false;
Axios.interceptors.response.use(
    response => {
        if (response.data.code === 500405 && openLogout === false) {
            openLogout = true;
            NewDialog(app).openCallbackTip(() => {
                window.location.href = '/logout';
            }, '您的權限已被取消，將自動登出管理平台');

            return [];
        }

        return response;
    },
    error => {
        if (error.response) {
            if (error.response.status === 401 && openLogout === false) {
                openLogout = true;
                let dia = NewDialog(app);
                dia.openWarning(function () {
                    window.location.href = '/logout';
                }, '登入逾時，請重新登入');
            }
        }
        return Promise.reject(error)
    }
);
Date.prototype.Format = function (fmt) { //author: meizz
    let o = {
        "M+": this.getMonth() + 1, //月份
        "d+": this.getDate(), //日
        "h+": this.getHours(), //小時
        "m+": this.getMinutes(), //分
        "s+": this.getSeconds(), //秒
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度
        "S": this.getMilliseconds() //毫秒
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (let k in o)
        if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length === 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
};

Date.prototype.toString = function () {
    return this.Format("yyyy-MM-dd hh:mm:ss");
};