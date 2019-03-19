function $qr(){
    this.selector = '';
    this.length = 0;
    $qr.prototype.__init = function(s){
        this.selector = s;
        var r=document.querySelectorAll(s);
        this.length=r.length;
        for(var i=0;i<this.length;i++){
            this[i]=r[i];
        }
        return this;
    }
    $qr.prototype.css = function(n,v){
        var a=n.split('-');
        for(var i=1;i<a.length;i++){
            a[i]=a[i][0].toUpperCase() + a[i].substr(1);
        }
        n=a.join('');
        for(var i=0; i<this.length; i++){
            this[i].style[n]=v;
        }
        return this;
    }
    $qr.prototype.text = function(t=null){
        if(t===null){
            if(this.length){
                return this[0].innerText;
            }else{
                return null;
            }
        }else{
            if(this.length){
                for(var i=0;i<this.length;i++){
                    this[i].innerText = t;
                }
            }
        }
    }
    $qr.prototype.html = function(t=null){
        if(t===null){
            if(this.length){
                return this[0].innerHTML;
            }else{
                return null;
            }
        }else{
            if(this.length){
                for(var i=0;i<this.length;i++){
                    this[i].innerHTML = t;
                }
            }
        }
    }
    $qr.prototype.val = function(t=null){
        if(t===null){
            if(this.length){
                return this[0].value===undefined?'':this[0].value;
            }else{
                return undefined;
            }
        }else{
            if(this.length){
                for(var i=0;i<this.length;i++){
                    if(this[i].value!==undefined){
                        this[i].value = t;
                    }
                }
            }
        }
    }
    $qr.prototype.on = function(e,c){
        for(var i=0;i<this.length;i++){
            this[i].addEventListener(e,c);
        }
    }
    $qr.prototype.off = function(e,c){
        for(var i=0;i<this.length;i++){
            this[i].removeEventListener(e,c);
        }
    }
    $qr.prototype.addClass = function(c){
        for(var i=0;i<this.length;i++){
            this[i].classList.add(c);
        }
    }
    $qr.prototype.removeClass = function(c){
        for(var i=0;i<this.length;i++){
            this[i].classList.remove(c);
        }
    }
    $qr.prototype.data = function(f){
		if(this.length){
			return this[0].dataset[f];
		}
	}
	$qr.prototype.next = function(){
		if(this.length){
			return $(this[0].nextElementSibling);
		}
	}
	$qr.prototype.previous = function(){
		if(this.length){
			return $(this[0].previousElementSibling);
		}
	}
	$qr.prototype.remove = function(){
		for(var i=0;i<this.length;i++){
			this[i].remove();
		}
	}
}

function $(s){
    switch(typeof(s)){
        case 'string':
            var r=new $qr();
            return r.__init(s);
        case 'object':
            if(s instanceof $qr){
                return $rq;
            }
            if(s instanceof Element || s instanceof Node){
                var r=new $qr();
                r.length = 1;
                r[0] = s;
                return r;
            }
            if(s instanceof Array){
                var r=new $qr();
                r.length = s.length;
                for(var i=0;i<s.length;i++){
                    r[i]=s[i];
                }
                return r;
            }
    }
    return new $qr;
}