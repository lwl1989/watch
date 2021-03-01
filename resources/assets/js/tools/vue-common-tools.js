import Cookies from './vue-cookies'
import Dialogs from './element-ui-dialog'

let Tools = {
    OpenNewWindow(url, name, iHeight, iWidth) {
        let iTop = (window.screen.height - 30 - iHeight) / 2; //獲得窗口的垂直位置;
        let iLeft = (window.screen.width - 10 - iWidth) / 2; //獲得窗口的水平位置;
        window.open(url, name, 'height=' + iHeight + ',,innerHeight='
            + iHeight
            + ',width='
            + iWidth + ',innerWidth='
            + iWidth + ',top=' + iTop
            + ',left=' + iLeft
            + ',toolbar=0,menubar=0,scrollbars=1,resizeable=1,location=0,status=1');
        //window.open("/#/edit/company/0","修改特約商店","menubar=0,scrollbars=1,resizable=1,status=1,titlebar=0,toolbar=0,location=0,width=1024,height=800,top=100px,left=100px");

    },
    Cookies: Cookies,
    Dialog: Dialogs,

    BuildQueryString : (search, page, limit) => {
        if (search.page && page) {
            search.page = page;
        }

        if (search.limit && limit) {
            search.limit = limit;
        }

        let queryString = '';
        Object.keys(search).forEach(function(key) {
            if (search[key] !== '' || search[key]) {
                queryString += key + '=' + search[key] + '&';
            }
        });

        return '?' + queryString.replace(/\&$/ig, '');
    },

    CanOpen: (vue) => {
        let hash = window.location.hash;
        let userRouter = Cookies.getCookie('router');
        vue.$router.options.routes.forEach((key,value)=>{

        });
    },
    copyObj(old) {
        let data = {};
        Object.keys(old).forEach(function(key){
            data[key] = old[key];
        });
        return data;
    },
    coverObj(now, old) {
        Object.keys(old).forEach(function(key){
            now[key] = old[key];
        });
    },
};
export default  Tools;