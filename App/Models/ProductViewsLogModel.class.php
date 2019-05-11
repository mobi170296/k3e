<?php
    namespace App\Models;
    
    use Core\Model;
    
    use Library\Database\DBNumber;
    use Library\Database\DBRaw;
    
    class ProductViewsLogModel extends Model{
        public $user_id, $product_id, $created_time, $views, $latest_time;
        
        public $user, $product;
        
        public function loadData(){
            $rows = $this->database->selectall()->from(DB_TABLE_PRODUCTVIEWSlOG)->where("user_id={$this->user_id} and product_id={$this->product_id}")->execute();
            
            if(count($rows)){
                $row = $rows[0];
                
                foreach($row as $k => $v){
                    $this->$k = $v;
                }
                return true;
            }else{
                return false;
            }
        }
        
        public function loadProduct(){
            $this->product = new ProductModel($this->database);
            $this->product->id = $this->product_id;
            return $this->product->loadData();
        }
        
        public function loadUser(){
            $this->user = new UserModel($this->database);
            $this->user->id = $this->user_id;
            return $this->user->loadData();
        }
        
        public function add(){
            $this->database->insert(DB_TABLE_PRODUCTVIEWSlOG, [
                'user_id' => new DBNumber($this->user_id),
                'product_id' => new DBNumber($this->product_id),
                'views' => new DBNumber(1)
            ]);
        }
        
        public function increase(){
            if($this->loadData()){
                $this->database->update(DB_TABLE_PRODUCTVIEWSlOG, [
                    'views' => new DBRaw('views + 1'),
                    'latest_time' => new DBRaw('now()')
                ], "user_id={$this->user_id} and product_id={$this->product_id}");
            }else{
                $this->add();
            }
        }
        
        public function delete(){
            
        }
    }