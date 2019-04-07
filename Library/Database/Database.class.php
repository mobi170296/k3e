<?php
    namespace Library\Database;
    class Database{
        public $connection;
        public $_select, $_from, $_join, $_on, $_where, $_groupby, $_having, $_order, $_orderby, $_limit;
        public function __construct(){
            global $k3_config;
            $this->connection = @new \mysqli($k3_config['db']['host'], $k3_config['db']['username'], $k3_config['db']['password'], $k3_config['db']['dbname']);
            if($this->connection->connect_errno){
                throw new DBException($this->connection->connect_error, $this->connection->connect_errno);
            }
            
            $this->_select = $this->_from = $this->_where = $this->_groupby = $this->_having = $this->_order = $this->_orderby = $this->_limit = "";
            $this->_join = [];
            $this->_on = [];
        }
        
        public function getErrorCode(){
            return $this->connection->errno;
        }
        
        public function getErrorMessage(){
            return $this->connection->error;
        }
        
        public function select($s){
            $this->_select = $this->_from = $this->_where = $this->_groupby = $this->_having = $this->_order = $this->_orderby = $this->_limit = "";
            $this->_join = [];
            $this->_on = [];
            $this->_select = $s;
            return $this;
        }
        
        public function from($s){
            $this->_from = $s;
            return $this;
        }
        
        public function join($s){
            $this->_join[] = $s;
            return $this;
        }
        
        public function on($s){
            $this->_on[] = $s;
            return $this;
        }
        
        public function where($s){
            $this->_where = $s;
            return $this;
        }
        
        public function groupby($s){
            $this->_groupby = $s;
            return $this;
        }
        
        public function having($s){
            $this->_having = $s;
            return $this;
        }
        
        public function asc(){
            $this->_order = "ASC";
            return $this;
        }
        
        public function desc(){
            $this->_order = "DESC";
            return $this;
        }
        
        public function orderby($s){
            $this->_orderby = $s;
            return $this;
        }
        
        public function limit($s, $t){
            $this->_limit = "$s, $t";
            return $this;
        }
        
        public function execute(){
            $query = "";
            $query .= "SELECT {$this->_select}";
            $query .= empty($this->_from) ? "" : " FROM {$this->_from}";
            for($i=0; $i<count($this->_join); $i++){
                $query .= " JOIN {$this->_join[$i]} ON {$this->_on[$i]}";
            }
            $query .= empty($this->_where) ? "" : " WHERE {$this->_where}";
            $query .= empty($this->_groupby) ? "" : " GROUP BY {$this->_groupby}";
            $query .= empty($this->_having) ? "" : " HAVING {$this->_having}";
            $query .= empty($this->_orderby) ? "" : " ORDER BY {$this->_orderby} {$this->_order}";
            $query .= empty($this->_limit) ? "" : " LIMIT {$this->_limit}";
            
            
            $result = $this->connection->query($query);
            
            if($this->connection->errno){
                throw new DBException($this->connection->error, $this->connection->errno);
            }

            $aresult = [];

            while($row = $result->fetch_assoc()){
                $d = new \stdClass();
                foreach($row as $k => $v){
                    $d->$k = $v;
                }
                $aresult[] = $d;
            }

            return $aresult;
        }
        
        public function startTransaction(){
            $this->connection->query('start transaction read write');
            
            if($this->connection->errno){
                throw new DBException($this->connection->error, $this->connection->errno);
            }
        }
        
        public function rollback(){
            $this->connection->query('rollback');
            
            if($this->connection->errno){
                throw new DBException($this->connection->error, $this->connection->errno);
            }
        }
        
        public function commit(){
            $this->connection->query('commit');
            
            if($this->connection->errno){
                throw new DBException($this->connection->error, $this->connection->errno);
            }
        }
        
        public function insert($table, $keyvalue){
            foreach($keyvalue as $k => $v){
                $keys[] = $k;
                $values[] = $v->SqlValue();
            }
            
            $keystring = implode(',', $keys);
            $valuestring = implode(',', $values);
            
            $query = "INSERT INTO $table ($keystring) VALUES ($valuestring)";
            
            $result = $this->connection->query($query);
            
            if($this->connection->errno){
                throw new DBException($this->connection->error, $this->connection->errno);
            }else{
                return $result;
            }
        }
        
        public function update($table, $keyvalue, $where){
            foreach($keyvalue as $k => $v){
                $pairs[] = $k . '=' . $v->SqlValue();
            }
            
            $query = "UPDATE $table SET " . implode(',', $pairs) . " $where";
            $result =  $this->connection->query($query);
            
            if($this->connection->errno){
                throw new DBException($this->connection->error, $this->connection->errno);
            }else{
                return $result;
            }
        }
        
        public function delete($table, $where){
            $query = "DELETE FROM $table WHERE $where";
            $result = $this->connection->query($query);
            
            if($this->connection->errno){
                throw new DBException($this->connection->error, $this->connection->errno);
            }else{
                return $result;
            }
        }
        
        public function escape($s){
            return $this->connection->real_escape_string($s);
        }
        
        #Test function
        public function get(){
            $query = "";
            $query .= "SELECT {$this->_select}";
            $query .= empty($this->_from) ? "" : " FROM {$this->_from}";
            for($i=0; $i<count($this->_join); $i++){
                $query .= " JOIN {$this->_join[$i]} ON {$this->_on[$i]}";
            }
            $query .= empty($this->_where) ? "" : " WHERE {$this->_where}";
            $query .= empty($this->_groupby) ? "" : " GROUP BY {$this->_groupby}";
            $query .= empty($this->_having) ? "" : " HAVING {$this->_having}";
            $query .= empty($this->_orderby) ? "" : " ORDER BY {$this->_orderby} {$this->_order}";
            $query .= empty($this->_limit) ? "" : " LIMIT {$this->_limit}";
            
            return $query;
        }
    }