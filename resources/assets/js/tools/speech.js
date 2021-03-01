(function(global, factory){
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
    typeof define === 'function' && define.amd ? define(factory) :
    (global.Speech = factory());
})(this, function() {
    var tipId = '';
    var isPlay = false;
    var setTimeout = null;
    
    var speech = {
        play : function (language, content) {
            var device = this.env();
            if (device) {
                //先強行截取前4000個字符
                content = this.getInterceptedStr(content, 4000);

                if (device === 'android') {
                    window.JSImageObj.textToSpeech(language, content);
                } else {
                    window.webkit.messageHandlers.textToSpeech.postMessage({'language': language, 'content': content});
                }

                isPlay = true;

                return true;
            } else {
                return false;
            }
        },

        stop : function (language) {
            if (!isPlay) {
                return false;
            }

            var device = this.env();
            if (device) {
                if (device === 'android') {
                    window.JSImageObj.stopTextToSpeech(language);
                } else {
                    window.webkit.messageHandlers.stopTextToSpeech.postMessage({'language': language});
                }

                isPlay = false;
            }
        },

        env : function () {
            var ua = navigator.userAgent.toLowerCase();

            if(/iphone|ipad|ipod/.test(ua)) {
                if (!('webkit' in window)) {
                    return false;
                }

                return 'ios';
            } else {
                if (typeof window.JSImageObj === 'undefined') {
                    return false;
                }

                return 'android';
            }
        },

        isPlay : function () {
            return isPlay;
        },

        tip : function(msg, time) {
            if (setTimeout !== null) {
                clearTimeout(setTimeout);
            }

            if (tipId) {
                $('#'+tipId).remove();
            }

            tipId = (new Date()).getTime();
            time = time ? time : 3000;

            $('<div></div>', {
                id : tipId,
                css : {
                    position: 'fixed',
                    bottom: '10%',
                    left: '50%',
                    background: '#000000',
                    color: 'white',
                    padding: '5px 12px',
                    borderRadius : '4px',
                    fontSize : '14px',
                    letterSpacing : '1px',
                    transform: 'translateX(-50%)'
                },
                text : msg
            }).appendTo('body');

            window.setTimeout(function(){
                $('#'+tipId).remove();
            }, time);
        },

        getInterceptedStr : function(sSource, iLen) {
            if (sSource.replace(/[^\x00-\xff]/g, "xx").length <= iLen) {
                return sSource;
            }

            var str = "";
            var l = 0;
            var schar;
            for (var i = 0; schar = sSource.charAt(i); i++) {
                str += schar;
                l += (schar.match(/[^\x00-\xff]/) != null ? 2 : 1);
                if (l >= iLen) {
                    break;
                }
            }

            return str;
        }
    };

    return speech;
});