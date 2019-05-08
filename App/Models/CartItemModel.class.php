<?php
    namespace App\Models;
    use Core\Model;
    
    use Library\Database\DBDateTime;
    use Library\Database\DBNumber;
    
    class CartItemModel extends Model{
        public $product_id, $client_id, $quantity, $created_time;
        
        public $product, $client;
        
        public function loadData(){
            $rows = $this->database->selectall()->from(DB_TABLE_CARTITEM)->where('client_id=' . (int)$this->client_id . ' and product_id=' . (int)$this->product_id)->execute();
            
            if(count($rows)){
                $row = $rows[0];
                
                $this->quantity = $row->quantity;
                $this->created_time = DBDateTime::parse($row->created_time);
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
        
        public function loadClient(){
            $this->client = new UserModel($this->database);
            $this->client->id = $this->client_id;
            return $this->client->loadData();
        }
        
        public function add(){
            $this->database->insert(DB_TABLE_CARTITEM, [
                'product_id' => new DBNumber($this->product_id),
                'client_id' => new DBNumber($this->client_id),
                'quantity' => new DBNumber($this->quantity)
            ]);
            
            return true;
        }
        
        public function delete(){
            $this->database->delete(DB_TABLE_CARTITEM, 'client_id=' . (int)$this->client_id . ' and product_id=' . (int)$this->product_id);
            return true;
        }
        
        public function update($cartitem){
            $this->database->update(DB_TABLE_CARTITEM, [
                'quantity' => new DBNumber($cartitem->quantity)
            ], 'client_id=' . (int)$this->client_id . ' and product_id=' . (int)$this->product_id);
            $this->quantity = $cartitem->quantity;
            
            return true;
        }
        
        public function updateQuantity($newquantity){
            $this->database->update(DB_TABLE_CARTITEM, [
                'quantity' => new DBNumber($newquantity)
            ], 'client_id=' . (int)$this->client_id . ' and product_id=' . (int)$this->product_id);
            
            $this->quantity = $newquantity;
            return true;
        }
    }