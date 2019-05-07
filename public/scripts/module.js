var modules = (function(){
    var _file_cached = {};
    var _files = {};
    function defined(ns){
        var ref = window, pa = ns.split('.');
        for(var i=0; i<pa.length; i++){
            if(ref[pa[i]] === undefined){
                return false;
            }

            ref = ref[pa[i]];
        }
        return true;
    }

    function define_ns(ns){
        var ref = window, pa = ns.split('.');
        for(var i=0; i<pa.length; i++){
            if(ref[pa[i]] === undefined){
                ref[pa[i]] = {};
            }

            if(i !== pa.length - 1){
                ref = ref[pa[i]];
            }
        }

        return {
            ref: ref,
            prop: pa[pa.length-1]
        }
    }

    function define(ns, f){
        if(defined(ns)){
            console.warn('Namespace exists');
        }

        var args = [];

        if(arguments.length > 2){
            args = [].slice.call(arguments, 2);
        }

        var oref = define_ns(ns);

        if(typeof f === 'string'){
            //truong hop la file
            if(_file_cached[f]===undefined){
                //file nay chua duoc cache
                $.ajax().create().async(false).url(f).success(function(e){
                    _file_cached[f] = true;
                    _files[f] = this.response;
                }).error(function(e){
                    console.warn('Lỗi kết nối');
                }).get();
            }

            var mfn = new Function(_files[f]);
            ref = mfn.apply(null, args);
        }else{
            //truong hop la function
            oref.ref[oref.prop] = f.apply(null, args);
        }
    }

    return {
        define: define
    }
})();