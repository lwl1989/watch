let Cookies = {
    //設置cookie
    setCookie(cname, cvalue, exdays) {
        let d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        let expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires;
        sessionStorage.setItem(cname, cvalue);
    },
    //獲取cookie
    getCookie(cname) {
        let name = cname + "=";
        let ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' '){
                c = c.substring(1);
            }
            if (c.indexOf(name) !== -1) {
                return c.substring(name.length, c.length);
            }
        }
        //新增

        let value = sessionStorage.getItem(cname);
        if (value !== null && value !== '' && cname !== '' && cname === 'google_map_key') {
            value = value.replace(/\"/g, '');
        }

        return value;
        //return "";
    },
    //清除cookie
    clearCookie(cname) {
        this.setCookie(cname, "", -1);
        sessionStorage.setItem(cname,"");
    },
};
export default Cookies;