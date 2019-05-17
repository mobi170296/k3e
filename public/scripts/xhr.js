/*XMLHttpRequest Controller*/

function $AJAX(){
    this._xhr = null;
    this._url = null;
    this._method = 'get';
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
    this.async = function(b){
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
//                if(this.readyState===4 && this.status!==200){
//                    this._onerror(e);
//                }
            }
            
            this._xhr.onerror = this._xhr._onerror;
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
//                if(this.readyState===4 && this.status!==200){
//                    if(this._onerror != undefined){
//                        this._onerror(e);
//                    }
//                }
            }
            
            this._xhr.onerror = this._xhr._onerror;
        }
        this._xhr.send(data);
    }
    this.post = function(data=null, cb=null, cbs=null){
        this._xhr.open('post', this._url, this._sync);
        if(cbs!==null){
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
                    if(this._onsuccess !== undefined){
                        this._onsuccess(e);
                    }
                }
//                if(this.readyState===4 && this.status!==200){
//                    if(this._onerror != undefined){
//                        this._onerror(e);
//                    }
//                }
            }
            
            
            this._xhr.onerror = this._xhr._onerror;
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
    this.abort = function(){
        this._xhr.abort();
        return this;
    }
}


//Updated for multiple ajax request
$.ajax = function () {
    return new $AJAX();
}