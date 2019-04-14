<?php 
    namespace App\Models;
    use Core\Model;
    use Library\Database\DBString;
    use Library\Database\DBNumber;
    
    class ImageMapModel extends Model{
        const LINKED = 1, UNLINKED = 0;

        public $id, $diskpath, $urlpath, $user_id, $linked, $created_time, $mimetype;
        
        public function loadData(){
            $rows = $this->database->selectall()->from(DB_TABLE_IMAGEMAP)->where('id=' . (int)$this->id)->execute();
            if(count($rows)){
                $row = $rows[0];
                $this->id = $row->id;
                $this->diskpath = $row->diskpath;
                $this->urlpath = $row->urlpath;
                $this->user_id = $row->user_id;
                $this->linked = $row->linked;
                $this->created_time = $row->created_time;
                $this->mimetype = $row->mimetype;
                return true;
            }else{
                return false;
            }
        }
        
        public function add(){
            $this->database->insert(DB_TABLE_IMAGEMAP, ['diskpath' => new DBString($this->database->escape($this->diskpath)), 'urlpath' => new DBString($this->database->escape($this->urlpath)), 'user_id' => new DBNumber((int)$this->user_id), 'mimetype' => new DBString($this->database->escape($this->mimetype)), 'linked' => new DBNumber((int)$this->linked)]);
        }
        
        public function delete(){
            $this->database->delete(DB_TABLE_IMAGEMAP, 'id=' . $this->id);
        }
    }