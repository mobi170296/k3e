<?php
    namespace App\Models;
    use Core\Model;
    
    use Library\Database\DBNumber;
    use Library\Database\DBString;
    
    class OrderItemModel extends Model{
        public $order_id, $product_id, $quantity, $price, $warranty_months_number;
        
        
        public $order, $product;
        
        public function loadData(){
            $rows = $this->database->selectall()->from(DB_TABLE_ORDERITEM)->where('order_id=' . (int)$this->order_id . ' and product_id=' . (int)$this->product_id)->execute();
            
            if(count($rows)){
                $row = $rows[0];
                foreach($row as $key => $value){
                    $this->$key = $value;
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
        
        public function add(){
            $this->database->insert(DB_TABLE_ORDERITEM, [
                'order_id' => new DBNumber($this->order_id),
                'product_id' => new DBNumber($this->product_id),
                'quantity' => new DBNumber($this->quantity),
                'price' => new DBNumber($this->price),
                'warranty_months_number' => new DBNumber($this->warranty_months_number)
            ]);
            
            return true;
        }
    }