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

Element.prototype.css = function(n, v){
    n=n.replace('-',' ');
    n=n.toCapitalize();
    n=n.charAt(0).toLowerCase()+n.slice(1);
    n=n.replace(' ','');
    this.style[n]=v;
    return this;
}

Node.prototype.css = function(n, v){
    n=n.replace('-',' ');
    n=n.toCapitalize();
    n=n.charAt(0).toLowerCase()+n.slice(1);
    n=n.replace(' ','');
    this.style[n]=v;
    return this;
}

function $(q){
    var r=document.querySelectorAll(q);
    if(r.length==1){
        return r[0];
    }else{
        return r;
    }
}

Node.prototype.$ = function(q){
    var r=this.querySelectorAll(q);
    if(r.length==1){
        return r[0];
    }else{
        return r;
    }
}

Element.prototype.$ = function(q){
    var r=this.querySelectorAll(q);
    if(r.length==1){
        return r[0];
    }else{
        return r;
    }
}