function KDataTable(table, config){
    this.table = table;
    this.rowTemplate = null;

    this.clearHeader = function(){
        while(this.table.tHead.rows.length){
            this.table.tHead.rows[0].remove();
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
        row.innerHTML = this.rowTemplate.render(data);
    }

    this.removeRow = function(i){
        if(this.tBodies[0].rows[i] !== undefined){
            this.tBodies[0].rows[i].remove();
        }
    }

    this.setRowTemplate = function(tpl){
        this.rowTemplate = new KTemplate(tpl);
    }
    
    this.setEvent = function(name, fn){
        this.table.addEventListener(name, fn);
    }
}