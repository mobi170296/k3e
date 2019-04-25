<?php
    namespace App\Models;
    use Core\Model;
    use Library\Database\DBString;
    use Library\Database\DBNumber;
    
    class ProductAttributeModel extends Model{
        public $product_id, $norder, $attributename, $attributevalue;
        
        public $product;
        
        public function loadData(){
            $rows = $this->database->selectall()->from(DB_TABLE_PRODUCTATTRIBUTE)->where('product_id=' . (int)$this->product_id . ' and ' . 'norder=' . (int)$this->norder)->execute();
            if(count($rows)){
                $row = $rows[0];
                $this->product_id = $row->product_id;
                $this->norder = $row->norder;
                $this->attributename = $row->attributename;
                $this->attributevalue = $row->attributevalue;
                return true;
            }else{
                return false;
            }
        }
        
        public function loadProduct(){
            $this->product = new ProductModel($this->database);
            $this->product->id = $this->product_id;
            return $this->loadProduct();
        }
        
        public function checkKey(){
            if(!is_string($this->attributename) || mb_strlen($this->attributename) == 0 || mb_strlen($this->attributename) > 100){
                $this->addErrorMessage('attributename', 'Thuộc tính có chiều dài không hợp lệ!');
            }
            return $this;
        }
        
        public function checkValue(){
            if(!is_string($this->attributevalue) || mb_strlen($this->attributevalue) == 0 || mb_strlen($this->attributevalue) > 1024){
                $this->addErrorMessage('attributevalue', 'Thuộc tính có chiều dài không hợp lệ!');
            }
            return $this;
        }
        
        public function add(){
            $this->database->insert(DB_TABLE_PRODUCTATTRIBUTE, ['product_id' => new DBNumber($this->product_id), 'norder' => new DBNumber($this->norder), 'attributename' =>  new DBString($this->database->escape($this->attributename)), 'attributevalue' => new DBString($this->database->escape($this->attributevalue))]);
        }
        
        public function delete(){
            $this->database->delete(DB_TABLE_PRODUCTATTRIBUTE, "product_id={$this->product_id} and norder={$this->norder}");
        }
    }