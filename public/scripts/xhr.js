/*XMLHttpRequest Controller*/

var $AJAX = new (function(){
    this._xhr = null;
    this._url = null;
    this._method = null;
    this._cb = null;
    this._sync = true;
    this.create = function(){
        this._xhr = new XMLHttpRequest();
        return this;
    }
    this.url = function(url){
        this._url = url;
        return this;
    }
    this.method = function(method){
        this._method = method;
        return this;
    }
    this.setting = function(cb){
        this._cb = cb;
        this._cb();
        return this;
    }
    this.sync = function(b){
        this._sync = b;
        return this;
    }
    this.success = function(cb){
        this._xhr._onsuccess = cb;
        return this;
    }
    this.error = function(cb){
        this._xhr._onerror = cb;
        return this;
    }
    this.send = function(data, cb=null){
        this._xhr.open(this._method, this._url, this._sync);
        if(cb!==null){
            this._xhr.onreadystatechange = cb;
        }else{
            this._xhr.onreadystatechange = function(e){
                if(this.readyState===4 && this.status===200){
                    this._onsuccess(e);
                }
                if(this.readyState===4 && this.status!==200){
                    this._onerror(e);
                }
            }
        }
        this._xhr.send(data);
    }
    this.get = function(data=null, cb=null){
        this._xhr.open('get', this._url, this._sync);
        if(cb!==null){
            this._xhr.onreadystatechange = cb;
        }else{
            this._xhr.onreadystatechange = function(e){
                if(this.readyState===4 && this.status===200){
                    this._onsuccess(e);
                }
                if(this.readyState===4 && this.status!==200){
                    this._onerror(e);
                }
            }
        }
        this._xhr.send(data);
    }
    this.post = function(data=null, cb=null){
        this._xhr.open('post', this._url, this._sync);
        if(typeof data === "string")
            this._xhr.setRequestHeader('content-type', 'application/x-www-form-urlencoded');
        if(cb!==null){
            this._xhr.onreadystatechange = cb;
        }else{
            this._xhr.onreadystatechange = function(e){
                if(this.readyState===4 && this.status===200){
                    this._onsuccess(e);
                }
                if(this.readyState===4 && this.status!==200){
                    this._onerror(e);
                }
            }
        }
        this._xhr.send(data);
    }
    this.upprogress = function(cb){
        this._xhr.upload.onprogress = cb;
        return this;
    }
    this.downprogress = function(cb){
        this._xhr.onprogress = cb;
        return this;
    }
    this.postForm = function(f, cb=null){
        this.url(f.action).post(new FormData(f), cb);
    }
})();

var $Modal = new (function(){
    this.modalwrapper = $('#modal-wrapper');
    if(this.modalwrapper !== null){
        this.modal = this.modalwrapper.$('.modal');
        this.modalheader = this.modal.$('.modal-header');
        this.modalheadertitle = this.modal.$('.modal-header-title');
        this.modalbody = this.modal.$('.modal-body');
        this.modalclosebutton = this.modaltitle.$('.modal-header-button');
    }
    
    this.create = function(){
        this.modalwrapper = document.createElement('div');
        this.modalwrapper.id = 'modal-wrapper';
        this.modalwrapper.css('display', 'none');
        this.modal = document.createElement('div');
        this.modal.className = 'modal';
        this.modal.onmousedown = function(e){
            e.stopPropagation();
        }
        this.modalheader = document.createElement('div');
        this.modalheader.className = 'modal-header clearfix';
        
        this.modalheadertitle = document.createElement('div');
        this.modalheadertitle.className = 'modal-header-title';
        
        this.modalheader.append(this.modalheadertitle);
        
        this.modalclosebutton = document.createElement('div');
        this.modalclosebutton.text('x');
        this.modalclosebutton.className = 'modal-header-button';
        this.modalclosebutton.onclick = function(e){
            window.$Modal.hide();
        }
        
        this.modalheader.append(this.modalclosebutton);
        
        this.modal.append(this.modalheader);
        
        this.modalbody = document.createElement('div');
        this.modalbody.className = 'modal-body';
        this.modal.append(this.modalbody);
        
        this.modalwrapper.append(this.modal);
        
        document.body.append(this.modalwrapper);
        return this;
    }
    this.show = function(){
        if(this.modalwrapper===null){
            this.create();
        }
        this.modalwrapper.css('display', 'block');
        $(window.document.body).css('overflow', 'hidden');
        return this;
    }
    this.hide = function(){
        if(this.modalwrapper===null){
            this.create();
        }
        this.modalwrapper.css('display', 'none');
        $(window.document.body).css('overflow', 'auto');
        return this;
    }
    this.text = function(t){
        if(this.modalwrapper===null){
            this.create();
        }
        this.modalbody.text(t);
        return this;
    }
    this.html = function(t){
        if(this.modalwrapper===null){
            this.create();
        }
        this.modalbody.html(t);
        return this;
    }
    this.title = function(t){
        if(this.modalwrapper===null){
            this.create();
        }
        this.modalheadertitle.text(t);
        return this;
    }
    this.waiting = function(){
        if(this.modalwrapper===null){
            this.create();
        }
        this.html('<div class="loading-i-wrapper"><div class="loading-i"></div></div>');
        return this;
    }
})();

document.onmousedown = function(e){
    window.$Modal.hide();
}