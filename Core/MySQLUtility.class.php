<?php
    namespace Core;
    class MySQLUtility{
            public $dbcon;

            public function __construct($host, $username, $password, $dbname, $port = 3306){
                $this->dbcon = new \mysqli($host, $username, $password, $dbname, $port);
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
                if(count($cname)!=count($cvalue)){
                    return false;
                }
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