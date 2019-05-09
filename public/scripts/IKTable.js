function IKDataTable(table, config){
    this.table = table;
    this.rowTemplate = null;

    this.clearHeader = function(){
        while(this.table.tHead.rows.length){
            this.table.tHead.rows[0].remove();
        }
    }
    
    this.clearRows = function(){
        var body = this.table.tBodies[0];
        while(body.rows.length){
            body.rows[0].remove();
        }
    }

    this.addHeader = function(a = []){
        var header = this.table.tHead;
        var row = header.insertRow();
        for(var c of a){
            var cell = row.insertCell();
            cell.innerHTML = c;
        }
    }

    this.addRow = function(data){
        var body = this.table.tBodies[0];
        var row = body.insertRow();
//        row.innerHTML = this.rowTemplate.render(data);
        this.rowTemplate.render(data, row);
    }

    this.removeRow = function(i){
        var body = this.table.tBodies[0];
        if(body.rows[i] !== undefined){
            body.rows[i].remove();
        }
    }
    
    this.setMessage = function(msg){
        this.clearRows();
        var body = this.table.tBodies[0];
        var row = body.insertRow();
        var cell = row.insertCell();
        var colsnum = this.table.tHead.rows[0].children.length;
        cell.colSpan = colsnum;
        cell.align = 'center';
        cell.innerHTML = msg;
    }

    this.setRowTemplate = function(tpl){
        this.rowTemplate = new IKTemplate(tpl);
    }
    
    this.setEvent = function(name, fn){
        this.table.addEventListener(name, fn);
    }
}