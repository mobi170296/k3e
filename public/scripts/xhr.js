/*XMLHttpRequest Controller*/

function $AJAX(){
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
        this._xhr._cb = cb;
        this._xhr._cb();
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
    this.send = function(data, cb=null, cbs=null){
        this._xhr.open(this._method, this._url, this._sync);
        if(cbs!=null){
            this._xhr._cb = cbs;
            this._xhr._cb();
        }
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
    this.get = function(data=null, cb=null, cbs=null){
        this._xhr.open('get', this._url, this._sync);
        if(cbs!=null){
            this._xhr._cb = cbs;
            this._xhr._cb();
        }
        if(cb!==null){
            this._xhr.onreadystatechange = cb;
        }else{
            //this._xhr.onreadystatechange = this._xhr._onsuccess;
            this._xhr.onreadystatechange = function(e){
                if(this.readyState===4 && this.status===200){
                    if(this._onsuccess != undefined){
                        this._onsuccess(e);
                    }
                }
                if(this.readyState===4 && this.status!==200){
                    if(this._onerror != undefined){
                        this._onerror(e);
                    }
                }
            }
        }
        this._xhr.send(data);
    }
    this.post = function(data=null, cb=null, cbs=null){
        this._xhr.open('post', this._url, this._sync);
        if(cbs!=null){
            this._xhr._cb = cbs;
            this._xhr._cb();
        }
        if(typeof data === "string"){
            this._xhr.setRequestHeader('content-type', 'application/x-www-form-urlencoded');
        }
        if(cb!==null){
            this._xhr.onreadystatechange = cb;
        }else{
//            this._xhr.onreadystatechange = this._xhr._onsuccess;
            this._xhr.onreadystatechange = function(e){
                if(this.readyState===4 && this.status===200){
                    if(this._onsuccess != undefined){
                        this._onsuccess(e);
                    }
                }
                if(this.readyState===4 && this.status!==200){
                    if(this._onerror != undefined){
                        this._onerror(e);
                    }
                }
            }
        }
        this._xhr.send(data);
    }
    this.postForm = function(f, cb=null){
        this.url(f.action).post(new FormData(f), cb);
    }
    this.upprogress = function(cb){
        this._xhr.upload.onprogress = cb;
        return this;
    }
    this.downprogress = function(cb){
        this._xhr.onprogress = cb;
        return this;
    }
}


//Updated for multiple ajax request
$.ajax = function () {
    return new $AJAX();
}


var $Modal = new (function(){
    this.modalwrapper = $('#modal-wrapper')[0];
    if(this.modalwrapper !== null){
        this.modal = $(this.modalwrapper).$('.modal')[0];
        this.modalheader = $(this.modal).$('.modal-header')[0];
        this.modalheadertitle = $(this.modal).$('.modal-header-title')[0];
        this.modalbody = $(this.modal).$('.modal-body')[0];
        this.modalclosebutton = $(this.modaltitle).$('.modal-header-button')[0];
    }
    
    this.create = function(){
        this.modalwrapper = $.create('div');
        this.modalwrapper.id = 'modal-wrapper';
        $(this.modalwwrapper).addClass('u-hidden');
        this.modal = $.create('div');
        $(this.modal).addClass('modal');
        $(this.modal).on('mousedown', function (e) {
            e.stopPropagation();
        });
        this.modalheader = $.create('div');
        $(this.modalheader).addClass('modal-header').addClass('clearfix');
        this.modalheadertitle = $.create('div');
        $(this.modalheadertitle).addClass('modal-header-title');
        $(this.modalheader).addEnd(this.modalheadertitle);
        
        this.modalclosebutton = $.create('div');
        $(this.modalclosebutton).html('&times;');
        $(this.modalclosebutton).addClass('modal-header-button');
        $(this.modalclosebutton).on('click', function (e) {
            window.$Modal.hide();
        });
        
        $(this.modalheader).addEnd(this.modalclosebutton);
        
        $(this.modal).addEnd(this.modalheader);
        
        this.modalbody = $.create('div');
        $(this.modalbody).addClass('modal-body');
        $(this.modal).addEnd(this.modalbody);
        
        $(this.modalwrapper).addEnd(this.modal);
        
        $(document.body).addEnd(this.modalwrapper);
        return this;
    }
    this.show = function(){
        if (this.modalwrapper === undefined){
            this.create();
        }
        $(this.modalwrapper).css('display', 'block');
        $(window.document.body).css('overflow', 'hidden');
        return this;
    }
    this.hide = function(){
        if (this.modalwrapper === undefined){
            this.create();
        }
        $(this.modalwrapper).css('display', 'none');
        $(window.document.body).css('overflow', 'auto');
        return this;
    }
    this.text = function(t){
        if (this.modalwrapper === undefined){
            this.create();
        }
        $(this.modalbody).text(t);
        return this;
    }
    this.html = function(t){
        if (this.modalwrapper === undefined){
            this.create();
        }
        $(this.modalbody).html(t);
        return this;
    }
    this.contenttext = function(t){
        if (this.modalwrapper === undefined){
            this.create();
        }
        $(this.modalbody).text(t);
        return this;
    }
    this.contenthtml = function(t){
        if(this.modalwrapper === undefined){
            this.create();
        }
        $(this.modalbody).html(t);
        return this;
    }
    this.title = function(t){
        if (this.modalwrapper === undefined){
            this.create();
        }
        $(this.modalheadertitle).text(t);
        return this;
    }
    this.waiting = function(){
        if (this.modalwrapper === undefined){
            this.create();
        }
        this.html('<div class="loading-i-wrapper"><div class="loading-i"></div></div>');
        return this;
    }
})();

document.onmousedown = function(e){
    window.$Modal.hide();
}