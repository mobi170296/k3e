function IKTemplate(tmpl){
    var code = 'var __ktpl__r = [];\n';
                
    var left = '', right = '';
    var match;
    while((match = /@.+/.exec(tmpl))){
        left = tmpl.substr(0, match.index);
        right = match[0];

        //xu ly chuoi thong thuong khong phai lenh hay holder
        if(!/^\n\s+$/.test(left)){
            code += '__ktpl__r.push("' + left.replace(/"/g, '\\"').replace(/\n/g, '\\n') + '");\n';
        }

        //xu ly chuoi con lai la lenh hoac holder
        if(/^@(if\(|switch\(|case .*:|\}|for\(|while\(|do\{|break;|\(|\{|default;)/.test(right)){
            //la lenh
            if(right.startsWith('@{')){
                //la khoi khai bao
                tmpl = tmpl.substr(match.index + match[0].length);
                //tim ra danh dau cuoi khoi
                match = /@}/.exec(tmpl);
                code += tmpl.substr(0, match.index);
                tmpl = tmpl.substr(match.index + match[0].length);
            }else if(right.startsWith('@(')){
                //la bieu thuc
                var match2 = /@\(([^@]+)\)/.exec(right);
                code += '__ktpl__r.push(' + match2[1] + ');\n';
                tmpl = tmpl.substr(match.index + match2[0].length);
            }else{
                //la lenh
                code += right.substr(1) + '\n';
                tmpl = tmpl.substr(match.index + match[0].length);
            }
        }else{
            //khong la lenh
            var match2 = /^@([\w\.\[\]]+)/.exec(right);
            code += '__ktpl__r.push(' + match2[0].substr(1) + ');\n';
            tmpl = tmpl.substr(match.index + match2[0].length);
        }
    }

    //phan du con lai neu khong tim thay @ nua => la chuoi thong thuong
    if(!/^\n\s+$/.test(tmpl)){
        code += '__ktpl__r.push("' + tmpl.replace(/"/g, '\\"').replace(/\n/g, '\\n') + '");\n';
    }

    code += 'return __ktpl__r.join("");\n';
    
    this.renderfn = new Function(code);
    
    this.render = function(data){
        return this.renderfn.call(data);
    }
}