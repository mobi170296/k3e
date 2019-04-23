<form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="anh"/>
    <button type="submit" name="upload" value="upload">Upload</button>
</form>

<button onclick="exec()">
    Click to execute
</button>

<script>
    function exec(){
        xhr = new XMLHttpRequest();
        xhr.open('get', '/');
        
        xhr.onreadystatechange = function(e){
            result = this.response;
            html = document.querySelector('html');
            html.innerHTML = result;
            
            scripts = document.scripts;
            for(ix=0 ;ix< scripts.length ;ix++){
                a = [];
                a.push(new XMLHttpRequest());
                x = scripts[ix];
                if(x.src != ''){
                    xhr = a.pop();
                    xhr.open('get', x.src, false);
                    xhr.onreadystatechange = function(e){
                        result = this.response;
                        eval(result);
                    }
                    xhr.send();
                }
                
                eval(x.innerHTML);
            }
        }
        
        xhr.send();
        
    }
    </script>