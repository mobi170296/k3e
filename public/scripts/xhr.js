/*XMLHttpRequest Controller*/

var XHR = new (function(){
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
    }
    this.send = function(data, cb){
        this._xhr.open(this._method, this._url, this._sync);
        this._xhr.onreadystatechange = cb;
        this._xhr.send(data);
    }
    this.get = function(data, cb){
        this._xhr.open('get', this._url, this._sync);
        this._xhr.onreadystatechange = cb;
        this._xhr.send(data);
    }
    this.post = function(data, cb){
        this._xhr.open('post', this._url, this._sync);
        this._xhr.onreadystatechange = cb;
        this._xhr.send(data);
    }
})();
var Modal = new (function(){
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
            Modal.hide();
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
        return this;
    }
    this.hide = function(){
        if(this.modalwrapper===null){
            this.create();
        }
        this.modalwrapper.css('display', 'none');
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
})();
document.onmousedown = function(e){
    Modal.hide();
}