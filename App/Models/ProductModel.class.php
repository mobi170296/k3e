<?php
    namespace App\Models;
    use Core\Model;
    
    class ProductModel extends Model{
        public $id, $name, $description, $quantity, $shop_id, $original_price, $price, $subcategory_id;
        public $weight, $height, $width, $depth;
        public $created_time, $locked, $verified, $verified_time;
        
        public function checkName(){
            if(!is_string($this->name)){
                $this->addErrorMessage('name', 'Tên sản phẩm không hợp lệ!');
            }else{
                if(mb_strlen($this->name)==0){
                    $this->addErrorMessage('name', 'Tên sản phẩm không được phép bỏ trống');
                }elseif(mb_strlen($this->name) > 200){
                    $this->addErrorMessage('name', 'Tên sản phẩm không được vượt quá 200 ký tự');
                }
            }
        }
        
        public function checkDescription(){
            
        }
        
        public function checkQuantity(){
            if(!is_numeric($this->quantity)){
                $this->addErrorMessage('quantity', 'Số lượng của sản phẩm không hợp lệ!');
            }else{
                if($this->quantity > 999999 || $this->quantity < 0){
                    $this->addErrorMessage('quantity', 'Số lượng sản phẩm phải từ 0 đến 999,999 đơn vị');
                }
            }
        }
        
        public function checkShopId(){
            
        }
        
        public function checkOriginal(){
            #0 -> 100e6
            if(!is_numeric($this->original_price)){
                $this->addErrorMessage('original_price', 'Giá gốc của sản phẩm không hợp lệ');
            }else{
                if($this->original_price < 0 || $this->original_price > 100e6){
                    $this->addErrorMessage('original_price', 'Giá gốc của sản phẩm không được vượt quá giới hạn phải từ 0 đến 100 triệu vnđ');
                }
            }
        }
        
        public function checkPrice(){
            if(!is_numeric($this->original_price)){
                $this->addErrorMessage('price', 'Giá bán của sản phẩm không hợp lệ');
            }else{
                if($this->original_price < 0 || $this->original_price > 100e6){
                    $this->addErrorMessage('price', 'Giá bán của sản phẩm không được vượt quá giới hạn phải từ 0 đến 100 triệu vnđ');
                }
            }
        }
        
        public function checkWeight(){
            #units gram
            if(!is_numeric($this->weight)){
                $this->addErrorMessage('weight', 'Cân nặng không hợp lệ!');
            }else{
                #PHP auto convert string to number when context require number
                if($this->weight <= 10){
                    $this->addErrorMessage('weight', 'Cân nặng không thể nhỏ hơn 10g');
                }elseif($this->weight > 1e5){
                    $this->addErrorMessage('weight', 'Trọng lượng không thể vượt quá 100Kg');
                }
            }
        }
    }