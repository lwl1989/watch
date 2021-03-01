const NewDialog = function (obj) {
    return {
        vue:obj,
        openSuccess(callback,message){
            if(typeof(message) === 'undefined') {
                message = '操作成功'
            }

            this.openDialog('success', callback, message);
        },

        openError(callback, message) {
            if(typeof(message) === 'undefined') {
                message = '操作失敗，請檢查'
            }

            this.openDialog('error', callback, message);
        },

        openWarning(callback,message){
            if(typeof(message) === 'undefined') {
                message = '操作失敗，請檢查'
            }

            this.openDialog('warning', callback, message);
        },

        openDialog(type, callback, message) {
            this.vue.$message({
                type: type,
                message: message
            });

            if(typeof(callback) === 'function') {
                callback();
            }
        },

        openRefresh(message, callback) {
            let h = this.vue.$createElement;
            this.vue.$msgbox({
                title: '提示',
                message: h('p', null, [
                    h('span', null, message)
                ]),
                showCancelButton: true,
                confirmButtonText: '確定',
                cancelButtonText: '取消',
                beforeClose: (action, instance, done) => {

                    if (action === 'confirm') {
                        callback();
                        done();
                    } else {
                        done();
                    }
                },
            }).then(action => {

                //執行完畢
                //console.log(action);
            }).catch(e => {
                //執行異常
                //console.log(e)
            });
        },

        openSelfDialog(doCallback, message, msgTips, confirmTips, title='提示') {
            this.vue.$msgbox({
                title: title,
                message: message,
                showCancelButton: true,
                confirmButtonText: confirmTips ? confirmTips : '確定',
                cancelButtonText: '取消',

                beforeClose: (action, instance, done) => {
                    let callback = function () {
                        instance.confirmButtonWelcome = false;
                        instance.confirmButtonText = confirmTips ? confirmTips : '確定';
                        done();
                    };

                    if (action === 'confirm') {
                        instance.confirmButtonWelcome = true;
                        instance.confirmButtonText = msgTips ? msgTips : '執行中...';
                        doCallback(callback);
                        return;
                    }
                    callback();
                }
            }).then(()=>{}, ()=>{});
        },

        openTip(message, title, confirmTips) {
            this.vue.$msgbox({
                title: title ? title : '提示',
                message: message,
                confirmButtonText: confirmTips ? confirmTips : '確定',

                beforeClose: (action, instance, done) => {
                    let callback = function () {
                        instance.confirmButtonWelcome = false;
                        instance.confirmButtonText = confirmTips ? confirmTips : '確定';
                        done();
                    };

                    callback();
                }
            }).then(()=>{}, ()=>{});
        },

        openCallbackTip(doCallback, message, title, confirmTips) {
            this.vue.$msgbox({
                title: title ? title : '提示',
                message: message,
                confirmButtonText: confirmTips ? confirmTips : '確定',

                beforeClose: (action, instance, done) => {
                    let callback = function () {
                        instance.confirmButtonWelcome = false;
                        instance.confirmButtonText = confirmTips ? confirmTips : '確定';
                        done();
                    };

                    doCallback();
                    callback();
                }
            }).then(()=>{}, ()=>{});
        },

        openDelDialog(doCallback, message) {
            this.vue.$msgbox({
                title: '提示',
                message: message ? message : '確定刪除嗎？資料刪除後將無法復原。',
                showCancelButton: true,
                confirmButtonText: '確定',
                cancelButtonText: '取消',

                beforeClose: (action, instance, done) => {
                    let callback = function () {
                        instance.confirmButtonWelcome = false;
                        instance.confirmButtonText = '確定';
                        done();
                    };
                    if (action === 'confirm') {
                        instance.confirmButtonWelcome = true;
                        instance.confirmButtonText = '執行中...';
                        doCallback(callback);
                        return;
                    }

                    callback();
                }
            });
        }
    }
};

export default NewDialog;