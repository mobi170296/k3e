/* global Element, Node */

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
        for (var i = 0; i < this.length; i++){
            if (this[i].style!==undefined) {
                this[i].style[n] = v;
            }
        }
        return this;
    }
    $qr.prototype.joinAndCapitalize = function (n) {
        var a = n.split('-');
        for (var i = 1; i < a.length; i++) {
            a[i] = a[i][0].toUpperCase() + a[i].substr(1);
        }
        return a.join('');
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
                for (var i = 0; i < this.length; i++){
                    this[i].innerText = t;
                }
            }
            return this;
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
            return this;
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
            return this;
        }
    }
    $qr.prototype.oncontextmenu = function (c) {
        for (var i = 0; i < this.length; i++) {
            if (this[i].oncontextmenu !== undefined) {
                this[i].oncontextmenu = c;
            }
        }
        return this;
    }
    $qr.prototype.on = function(e,c){
        for(var i=0;i<this.length;i++){
            this[i].addEventListener(e,c);
        }
        return this;
    }
    $qr.prototype.off = function(e,c){
        for(var i=0;i<this.length;i++){
            this[i].removeEventListener(e,c);
        }
        return this;
    }
    $qr.prototype.haveClass = function (c) {
        if (this.length && typeof this[0].classList != 'undefined') {
            return this[0].classList.contains(c);
        }
        return false;
    }
	$qr.prototype.hasClass = function(c){
		return this.length&&this[0].classList!=undefined&&this[0].classList.contains(c);
	}
    $qr.prototype.addClass = function(c){
        for(var i=0;i<this.length;i++){
            this[i].classList.add(c);
        }
        return this;
    }
    $qr.prototype.removeClass = function(c){
        for(var i=0;i<this.length;i++){
            this[i].classList.remove(c);
        }
        return this;
    }
    $qr.prototype.toggleClass = function(c){
        for(var i=0; i<this.length;i++){
            this[i].classList.toggle(c);
        }
        return this;
    }
    $qr.prototype.data = function (f, v = undefined) {
        if (v === undefined) {
            if (this.length) {
                return this[0].dataset[this.joinAndCapitalize(f)];
            }
        } else {
            for (var i = 0; i < this.length; i++) {
                this[i].dataset[this.joinAndCapitalize(f)] = v;
            }
            return this;
        }
    }
    $qr.prototype.cssData = function (p, d) {
        for (var i = 0; i < this.length; i++) {
            $(this[i]).css(p, $(this[i]).data(d));
        }
        return this;
    }
    $qr.prototype.attr = function (k, v = null) {
        if (arguments.length === 1) {
            if (this.length && typeof this[0].getAttribute === 'function') {
                return this[0].getAttribute(k);
            } else {
                return null;
            }
        } else {
            for (var i = 0; i < this.length; i++) {
                if (typeof this[i].setAttribute === 'function') {
                    this[i].setAttribute(k, v);
                }
            }
            return this;
        }
    }
    
    $qr.prototype.prop = function (k, v = null) {
        if (arguments.length === 1) {
            return this[0][k];
        } else {
            for (var i = 0; i < this.length; i++) {
                this[i][k] = v;
            }
            return this;
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
    $qr.prototype.parent = function (s=null) {
        if(s===null){
            if (this.length) {
                return $(this[0].parentElement);
            }
        }else{
            if(this.length){
                var o = this[0];
                while(o.parentElement!==null){
                    if(o.parentElement.matches(s)){
                        return $(o.parentElement);
                    }
                    o = o.parentElement;
                }
                return $(null);
            }
        }
        
    }
    $qr.prototype.child = function () {
        if (this.length && typeof this[0].children != 'undefined') {
            return $(this[0].children);
        } else {
            var r = new $qr();
            r.length = 0;
            return r;
        }
    }
    $qr.prototype.children = function(){
        var r = new $qr();
        for(var i=0;i<this.length; i++){
            for(var j=0;j<this[i].children.length;j++){
                r[r.length++]=this[i].children[j];
            }
        }
        return r;
    }
    $qr.prototype.remove = function(){
        for(var i=0;i<this.length;i++){
                this[i].remove();
        }
    }
    $qr.prototype.append = function (o) {
        if (this.length && typeof this[0].appendChild === "function") {
            this[0].appendChild(o);
        }
    }
    $qr.prototype.addBegin = function (o) {
        if (this.length && typeof this[0].insertBefore === "function") {
            this[0].insertBefore(o, this[0].children[0]);
        }
        return this;
    }
    $qr.prototype.addEnd = function (o) {
        if (this.length && typeof this[0].appendChild === "function") {
            this[0].appendChild(o);
        }
        return this;
    }
    $qr.prototype.addNext = function (e) {
        if (this.length && typeof this[0].insertAdjacentElement === "function") {
            this[0].insertAdjacentElement('afterend', e);
        }
        return this;
    }
    $qr.prototype.addPrevious = function (e) {
        if (this.length && typeof this[0].insertAdjacentElement === "function") {
            this[0].insertAdjacentElement('beforebegin', e);
        }
        return this;
    }
    $qr.prototype.is = function(s){
        if(this.length && this[0] instanceof HTMLElement){
            return this[0].matches(s);
        }
        return false;
    }
    $qr.prototype.$ = function (s) {
        if (this.length) {
            if (typeof this[0].querySelectorAll === 'function') {
                var es = this[0].querySelectorAll(s);
                var r = new $qr();
                r.length = es.length;
                for (var i = 0; i < es.length; i++) {
                    r[i] = es[i];
                }
                return r;
            }
        }
        var r = new $qr();
        r.length = 0;
        return r;
    }
}


function $(s) {
    var r = new $qr();
    switch(typeof(s)){
        case 'string':
            return r.__init(s);
        case 'object':
            if(s instanceof $qr){
                return s;
            }
            if(s instanceof Element || s instanceof Node){
                r.length = 1;
                r[0] = s;
                return r;
            }
            if (s instanceof Array || s instanceof HTMLCollection) {
                r.length = s.length;
                for(var i=0;i<s.length;i++){
                    r[i]=s[i];
                }
                return r;
            }
        case "function":
            window.addEventListener('load', s);
            r.length = 1;
            r[0] = document;
            return r;
    }
    if (s == null) {
        r.length = 0;
        return r;
    }
    r.length = 1;
    r[0] = s;
    return r;
}


$.create = function (n, p = {}){
    var e = document.createElement(n);
    for (var x in p) {
        e.style[x] = p[x];
    }
    return e;
}


$COMMON = {
    moneyFormat: function(m, d, p, dp, pp){
        var n = parseFloat(m);
        n *= Math.pow(10, p);
        n = Math.round(n);
        n /= Math.pow(10, p);
        m = '' + n;
        var result = '';
        if(p === 0){
            var l=m.split('.')[0];
            while(l.length!==0){
                result = l.slice(-d) + result;
                l = l.substr(0, l.length - d);
                if(l.length!==0 && l!='-'){
                    result = dp + result;
                }
            }
            return result;
        }else{
            var a = m.split('.');
            var l = a[0];
            if(a.length !== 1){
                var r = a[1];
                result = pp + r.substr(0, p);
                var z = p - r.length;
                if(z > 0){
                    result += '00000000000000000000000000000000000000'.substr(0,z);
                }
            }else{
                result = pp + '000000000000000000000000000000000000000'.substr(0, p);
            }
            if(l.length === 0){
                return 0 + result;
            }
            while(l.length){
                result = l.slice(-d) + result;
                l = l.substr(0, l.length - d);
                if(l.length!==0 && l!='-'){
                    result = dp + result;
                }
            }
            return result;
        }
    },
    
    
    isInt: function(s){
        return !isNaN(parseInt(s));
    }
};

$DOMAIN = new (function(){
    this.uri = location.href;
    this.host = location.host;
    this.port = location.port;
    this.querystring = location.search;
    this.protocol = location.protocol;

    this.getQueryParams = function(){
        var querystring = location.search.substr(1);
        var query = {};
        var kvp = querystring.split('&');
        for(var i=0; i<kvp.length; i++){
            var akvp = kvp[i].split('=');
            query[akvp[0]] = decodeURIComponent(akvp[1]);
        }

        return query;
    }

    this.buildQueryString = function(params, encode = true){
        var querystring = '';

        for(var k in params){
            querystring += k + '=' + (encode ? encodeURIComponent(params[k]) : params[k]) + '&';
        }
        
        return querystring.substr(0, querystring.length - 1);
    }
})();