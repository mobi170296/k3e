<?php
    namespace Library\Database;
    class Database{
        public $lastquery;
        public static $connection = null;
        public $_select, $_from, $_join, $_on, $_where, $_groupby, $_having, $_order, $_orderby, $_limit, $_forupdate = false;
        public function __construct(){
            global $k3_config;
            if(self::$connection == null){
                self::$connection = @new \mysqli($k3_config['db']['host'], $k3_config['db']['username'], $k3_config['db']['password'], $k3_config['db']['dbname']);
            }
            if(self::$connection->connect_errno){
                throw new DBException(self::$connection->connect_error, self::$connection->connect_errno);
            }
            
            $this->_select = $this->_from = $this->_where = $this->_groupby = $this->_having = $this->_order = $this->_orderby = $this->_limit = "";
            $this->_join = [];
            $this->_on = [];
        }
        
        public function getErrorCode(){
            return self::$connection->errno;
        }
        
        public function getErrorMessage(){
            return self::$connection->error;
        }
        public function lock(){
            $this->_forupdate = true;
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
            $query .= $this->_forupdate ? " FOR UPDATE" : "";
            
            
            $result = self::$connection->query($query);
            
            if(self::$connection->errno){
                throw new DBException(self::$connection->error, self::$connection->errno);
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
        public function select($s){
            $this->_select = $this->_from = $this->_where = $this->_groupby = $this->_having = $this->_order = $this->_orderby = $this->_limit = "";
            $this->_join = [];
            $this->_on = [];
            $this->_forupdate = false;
            
            $this->_select = $s;
            return $this;
        }
        
        public function selectall(){
            $this->_select = $this->_from = $this->_where = $this->_groupby = $this->_having = $this->_order = $this->_orderby = $this->_limit = "";
            $this->_join = [];
            $this->_on = [];
            $this->_forupdate = false;
            
            $this->_select = '*';
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
            
            #
            $this->lastquery = $query;
            
            $result = self::$connection->query($query);
            
            if(self::$connection->errno){
                throw new DBException(self::$connection->error, self::$connection->errno);
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
        
        public function lastInsertId(){
            return self::$connection->insert_id;
        }
        
        public function startTransaction(){
            self::$connection->query('start transaction read write');
            
            if(self::$connection->errno){
                throw new DBException(self::$connection->error, self::$connection->errno);
            }
        }
        
        public function rollback(){
            self::$connection->query('rollback');
            
            if(self::$connection->errno){
                throw new DBException(self::$connection->error, self::$connection->errno);
            }
        }
        
        public function commit(){
            self::$connection->query('commit');
            
            if(self::$connection->errno){
                throw new DBException(self::$connection->error, self::$connection->errno);
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
            
            #
            $this->lastquery = $query;
            
            $result = self::$connection->query($query);
            
            if(self::$connection->errno){
                throw new DBException(self::$connection->error, self::$connection->errno);
            }else{
                return $result;
            }
        }
        
        public function update($table, $keyvalue, $where){
            foreach($keyvalue as $k => $v){
                $pairs[] = $k . '=' . $v->SqlValue();
            }
            
            $query = "UPDATE $table SET " . implode(',', $pairs) . " WHERE $where";
            
            $this->lastquery = $query;
            
            $result =  self::$connection->query($query);
            
            if(self::$connection->errno){
                throw new DBException(self::$connection->error, self::$connection->errno);
            }else{
                return $result;
            }
        }
        
        public function delete($table, $where){
            $query = "DELETE FROM $table WHERE $where";
            
            $this->lastquery = $query;
            
            $result = self::$connection->query($query);
            
            if(self::$connection->errno){
                throw new DBException(self::$connection->error, self::$connection->errno);
            }else{
                return $result;
            }
        }
        
        public function escape($s){
            return self::$connection->real_escape_string($s);
        }
        
        public function  unescape($s){
            return stripslashes($s);
        }


        public function close(){
            @self::$connection->close();
        }
        
        public function query($str){
            $result = self::$connection->query($str);
            if($result === false){
                throw new DBException(self::$connection->error, self::$connection->errno);
            }
            $rows = [];
            while($r = $result->fetch_assoc()){
                $row = new \stdClass();
                foreach($r as $k => $v){
                    $row->$k = $v;
                }
                
                $rows[] = $row;
            }
            
            return $rows;
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
        
        public function lastquery(){
            return $this->lastquery;
        }
    }