/* Library for toast */
var $Toast = new (function(){
    this.toastbody = null;
    this.createToastBody = function(){
        this.toastbody = $.create('div');
        this.toastbody.id = 'toast-wrapper';
        $(document.body).addEnd(this.toastbody);
    }
    this.makeWarning = function(m,t=2000){
        var b = $.create('div');
        $(b).addClass('toast-box').addClass('warning').html(m);
        if(this.toastbody===null){
            this.createToastBody();
        }
        $(this.toastbody).addEnd(b);
        window.setTimeout(function(){
            window.$Toast.remove();
        }, t);
        $(b).css('transition-duration',t+'ms');
        window.setTimeout(function(){
            $(b).addClass('fadeout');
        }, 2000);
    }
    this.makeError = function(m,t=2000){
        var b = $.create('div');
        $(b).addClass('toast-box').addClass('error').html(m);
        if(this.toastbody==null){
            this.createToastBody();
        }
        $(this.toastbody).addEnd(b);
        window.setTimeout(function(){
            window.$Toast.remove();
        }, t);
        $(b).css('transition-duration', t + 'ms');
        window.setTimeout(function(){
            $(b).addClass('fadeout');
        }, 2000);
    }
    this.makeSuccess = function(m,t=2000){
        var b = $.create('div');
        $(b).addClass('toast-box').addClass('success').html(m);
        if(this.toastbody==null){
            this.createToastBody();
        }
        $(this.toastbody).addEnd(b);
        window.setTimeout(function(){
            window.$Toast.remove();
        }, t);
        $(b).css('transition-duration', t + 'ms');
        window.setTimeout(function(){
            $(b).addClass('fadeout');
        },2000);
    }
    this.remove = function(){
        this.toastbody.firstElementChild.remove();
    }
})();