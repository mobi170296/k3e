/* global Element, Node */

String.prototype.toCapitalize = function(){
    var si=0;
    var i;
    var rs=this;
    rs=rs.charAt(0).toUpperCase()+rs.slice(1);
    while((i=rs.indexOf(' ', si))!==-1){
        si=i+1;
        rs=rs.slice(0,si)+rs.charAt(si).toUpperCase()+rs.slice(si+1);
    }
    return rs;
}

Element.prototype.css = Node.prototype.css = function(n, v){
    n=n.replace('-',' ');
    n=n.toCapitalize();
    n=n.charAt(0).toLowerCase()+n.slice(1);
    n=n.replace(' ','');
    this.style[n]=v;
    return this;
}

Node.prototype.$ = Element.prototype.$ = $ = function(q){
    var r=document.querySelectorAll(q);
    if(r.length>1){
        return r;
    }else if(r.length===1){
        return r[0];
    }else{
        return null;
    }
}
Node.prototype.addClass = Element.prototype.addClass = function(c){
    this.classList.add(c);
}
Node.prototype.removeClass = Element.prototype.removeClass = function(c){
    this.classList.remove(c);
}
Node.prototype.text = Element.prototype.text = function(t=null){
    if(t!==null)this.innerText = t;else return this.innerText;
}

Element.prototype.html = Node.prototype.html = function(h=null){
    if(h!==null) this.innerHTML = h; else return this.innerHTML;
}

Element.prototype.on = Node.prototype.on = function(e, cb){
    this.addEventListener(e, cb);
}

Element.prototype.data = Node.prototype.data = function(n){
    return this.dataset[n];
}