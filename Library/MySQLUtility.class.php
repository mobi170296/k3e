<?php
    namespace Library;
    class MySQLUtility{
        public $select, $from, $where, $order, $limit, $groupby, $having;
        
        public $dbcon;

        public function __construct($host, $username, $password, $dbname, $port = 3306){
            $this->select = $this->from = $this->where = $this->order = $this->limit = $this->groupby = $this->having = null;
            $this->dbcon = @new \mysqli($host, $username, $password, $dbname, $port);
        }
        
        public function error(){
            return $this->dbcon->errno;
        }
        
        public function errno(){
            return $this->dbcon->error;
        }
        
        public function connect_errno(){
            return $this->dbcon->connect_errno;
        }
        
        public function connect_error(){
            return $this->dbcon->connect_error;
        }
        
        public function select($select){
            $this->select = $select;
            return $this;
        }
        
        public function from($from){
            $this->from = $from;
            return $this;
        }
        
        public function where($where){
            $this->where = $where;
            return $this;
        }
        
        public function order($order){
            $this->order = $order;
            return $this;
        }
        
        public function limit($limit){
            $this->limit = $limit;
            return $this;
        }
        
        public function groupby($groupby){
            $this->groupby = $groupby;
            return $this;
        }
        
        public function having($having){
            $this->having = $having;
            return $this;
        }

        public function execute(){
            $sql = "SELECT {$this->select} FROM {$this->from}";
            
            $sql .= $this->where != null ? " WHERE {$this->where}" : '';
            $sql .= $this->groupby != null ? " GROUP BY {$this->groupby}" : '';
            $sql .= $this->having != null ? " HAVING {$this->having}" : '';
            $sql .= $this->order != null ? " ORDER BY {$this->order}" : '';
            $sql .= $this->limit != null ? " LIMIT {$this->limit}" : '';
            
            $this->select = $this->from = $this->where = $this->order = $this->limit = $this->having = $this->groupby = null;
            
            return $this->dbcon->query($sql);
        }
        
        public function startTransaction(){
            return $this->dbcon->query('start transaction read write');
        }

        public function commit(){
            return $this->dbcon->query('commit');
        }

        public function rollBack(){
            return $this->dbcon->query('rollback');
        }

        public function query($query){
            return $this->dbcon->query($query);
        }

        public function close(){
            $this->dbcon->close();
        }

        public function delete($table, $where){
            return $this->query('DELETE FROM ' . $table. ' WHERE ' . $where);
        }

        public function update($table, $nvp, $where){
            $arr = [];
            foreach($nvp as $key => $value){
                $arr[] = $key . '=' . $value->toValue();
            }
            $nvpstring = implode($arr, ',');
            $st = 'UPDATE ' . $table . ' SET ' . $nvpstring . ' WHERE ' . $where;
            return $this->dbcon->query($st);
        }

        public function insert($table, $nvp){
            $dimension = 1;
            foreach($nvp as $k => $v){
                if(is_array($v)){
                    $dimension = 2;
                }
                break;
            }

            $col = [];
            $value = [];
            if($dimension == 1){
                foreach($nvp as $k => $v){
                    $col[] = $k;
                    $value[] = $v->toValue();
                }
                $colstring = implode($col, ',');
                $valuestring = implode($value, ',');
                $st = 'INSERT INTO ' . $table . ' (' . $colstring . ') ' . 'VALUES(' . $valuestring .')';
                return $this->dbcon->query($st);
            }else{
                foreach($nvp as $k => $r){
                    foreach($r as $c => $v){
                        $col[] = $c;
                    }
                    break;
                }
                $colstring = implode($col, ',');
                $valuestring = [];
                foreach($nvp as $row){
                    $value = [];
                    foreach($row as $v){
                        $value[] = $v->toValue();
                    }
                    $valuestring[] = '('.implode($value, ',').')';
                }
                $st = 'INSERT INTO ' . $table . ' ('. $colstring . ') ' . 'VALUES' . implode($valuestring, ',');
                return $this->dbcon->query($st);
            }
        }

        public function insertGetId($table, $nvp){
            $dimension = 1;
            foreach($nvp as $k => $v){
                if(is_array($v)){
                    $dimension = 2;
                }
                break;
            }

            $col = [];
            $value = [];
            if($dimension == 1){
                foreach($nvp as $k => $v){
                    $col[] = $k;
                    $value[] = $v->toValue();
                }
                $colstring = implode($col, ',');
                $valuestring = implode($value, ',');
                $st = 'INSERT INTO ' . $table . ' (' . $colstring . ') ' . 'VALUES(' . $valuestring .')';
                if($this->dbcon->query($st)){
                    return $this->dbcon->insert_id;
                }else{
                    return false;
                }
            }else{
                foreach($nvp as $k => $r){
                    foreach($r as $c => $v){
                        $col[] = $c;
                    }
                    break;
                }
                $colstring = implode($col, ',');
                $valuestring = [];
                foreach($nvp as $row){
                    $value = [];
                    foreach($row as $v){
                        $value[] = $v->toValue();
                    }
                    $valuestring[] = '('.implode($value, ',').')';
                }
                $st = 'INSERT INTO ' . $table . ' ('. $colstring . ') ' . 'VALUES' . implode($valuestring, ',');
                if($this->dbcon->query($st)){
                    return $this->dbcon->insert_id;
                }else{
                    return false;
                }
            }
        }
    }