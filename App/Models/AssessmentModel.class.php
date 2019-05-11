<?php
    namespace App\Models;
    use Core\Model;
    
    use Library\Database\DBDateTime;
    use Library\Database\DBNumber;
    use Library\Database\DBString;
    
    class AssessmentModel extends Model{
        public $order_id, $product_id, $client_id, $comment, $starpoint, $created_time;
        
        public $product, $order, $client;
        
        public function checkComment(){
            #max 10k char
            if(is_string($this->comment)){
                $length = mb_strlen($this->comment);
                if($length > 10e3){
                    $this->addErrorMessage('comment', 'Đánh giá có độ dài tối thiểu là 10 ký tự và nhiều nhất là 10000 ký tự');
                }
            }else{
                $this->addErrorMessage('comment', 'Đánh giá không hợp lệ');
            }
            
            return $this;
        }
        
        public function checkStarPoint(){
            if(!is_numeric($this->starpoint) || $this->starpoint <= 0 || $this->starpoint > 5){
                $this->addErrorMessage('starpoint', 'Số sao đánh giá không hợp lệ');
            }
            return $this;
        }
        
        public function loadData(){
            $rows = $this->database->selectall()->from(DB_TABLE_ASSESSMENT)->where('order_id='.(int)$this->order_id . ' and product_id='.(int)$this->product_id)->execute();
            
            if(count($rows)){
                $row = $rows[0];
                foreach($row as $k => $v){
                    $this->$k = $v;
                }
                
                $this->created_time = DBDateTime::parse($this->created_time);
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
        
        public function loadOrder(){
            $this->order = new OrderModel($this->database);
            $this->order->id = $this->order_id;
            
            return $this->order->loadData();
        }
        
        public function loadClient(){
            $this->client = new UserModel($this->database);
            $this->client->id = $this->client_id;
            
            return $this->client->loadData();
        }
        
        public function add(){
            $this->database->insert(DB_TABLE_ASSESSMENT, [
                'order_id' => new DBNumber($this->order_id),
                'client_id' => new DBNumber($this->client_id),
                'product_id' => new DBNumber($this->product_id),
                'comment' => new DBString($this->database->escape($this->comment)),
                'starpoint' => new DBNumber($this->starpoint)
            ]);
            
            return true;
        }
        
        public function update(){
            
        }
    }