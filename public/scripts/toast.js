/* Library for toast */
var $Toast = new (function(){
    this.toastbody = $('#toast-wrapper');
    this.createToastBody = function(){
        this.toastbody = document.createElement('div');
        this.toastbody.id = 'toast-wrapper';
        document.body.append(this.toastbody);
    }
    this.makeWarning = function(m,t){
        var b = document.createElement('div');
        b.className = 'toast-box warning';
        b.html(m);
        if(this.toastbody==null){
            this.createToastBody();
        }
        this.toastbody.append(b);
        window.setTimeout(function(){
            window.$Toast.remove();
        }, t);
        b.css('transition-duration', t + 'ms');
        window.setTimeout(function(b){
            b.className += ' fadeout';
        }, 2000, b);
    }
    this.makeError = function(m,t){
        var b = document.createElement('div');
        b.className = 'toast-box error';
        b.html(m);
        if(this.toastbody==null){
            this.createToastBody();
        }
        this.toastbody.append(b);
        window.setTimeout(function(){
            window.$Toast.remove();
        }, t);
        b.css('transition-duration', t + 'ms');
        window.setTimeout(function(b){
            b.className += ' fadeout';
        }, 2000, b);
    }
    this.makeSuccess = function(m,t){
        var b = document.createElement('div');
        b.className = 'toast-box success';
        b.html(m);
        if(this.toastbody==null){
            this.createToastBody();
        }
        console.log(this.toastbody);
        this.toastbody.append(b);
        window.setTimeout(function(){
            window.$Toast.remove();
        }, t);
        b.css('transition-duration', t + 'ms');
        window.setTimeout(function(b){
            b.className += ' fadeout';
        }, 2000, b);
    }
    this.remove = function(){
        this.toastbody.firstElementChild.remove();
    }
})();