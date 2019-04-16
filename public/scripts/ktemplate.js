function KTemplate(c){
    "use strict";
    this.ps = [];
    
    this.render = function(d){
        var m = [], r = [];
        //clone this.ps -> r
        for(var x = 0; x < this.ps.length; x++){
            r.push(this.ps[x]);
        }
        for(var x in d){
            if(typeof d[x] !== "function" && typeof d[x] !== "undefined"){
                for(var i = 0; i<r.length; i++){
                    var reg = new RegExp('^@' + x + '$');
                    if(reg.test(r[i]) && m.indexOf(i)===-1){
                        r[i] = r[i].replace(reg, d[x]);
                        m.push(i);
                    }
                }
            }
        }
        return r.join('');
    }
    
    var m;
    while((m = /@\w+/.exec(c))!==null){
        var o = c.substr(0, m.index);
        var t = c.substr(m.index, m[0].length);
        if(o.length){
            this.ps.push(o);
        }
        if(t.length){
            this.ps.push(t);
        }
        c = c.substr(m.index+m[0].length);
    }
    if(c.length){
        this.ps.push(c);
    }
}

function KTemplateInflater(e, d){
    var ktpl = new KTemplate(e.innerHTML);
    e.innerHTML = ktpl.render(d);
}