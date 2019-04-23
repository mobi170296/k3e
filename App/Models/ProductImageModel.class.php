<?php
    namespace App\Models;
    use Core\Model;
    
    class ProductImageModel extends Model{
        public $product_id, $norder, $imagemap_id;
        
        public $imagemap;
        
        public $product;
        
        public function loadData(){
            $rows = $this->database->selectall()->from(DB_TABLE_PRODUCTIMAGE)->where('product_id=' . $this->product_id . ' and norder=' . (int)$this->norder)->execute();
            if(count($rows)){
                $row = $rows[0];
                $this->product_id = $row->product_id;
                $this->norder = $row->norder;
                $this->imagemap_id = $row->imagemap_id;
                return true;
            }else{
                return false;
            }
        }
        
        public function loadImageMap(){
            $this->imagemap = new ImageMapModel($this->database);
            $this->imagemap->id = $this->imagemap_id;
            return $this->imagemap->loadData();
        }
        
        public function loadProduct(){
            $this->product = new ProductModel($this->database);
            $this->product->id = $this->product_id;
            return $this->product->loadData();
        }
        
        public function add(){
            $this->database->insert(DB_TABLE_PRODUCTIMAGE, ['product_id'=>new DBNumber($this->product_id), 'norder' => new DBNumber($this->norder), 'imagemap_id' => new DBNumber($this->imagemap_id)]);
            return true;
        }
        
        public function delete(){
            $this->database->delete(DB_TABLE_PRODUCTIMAGE, "product_id={$this->product_id} and norder={$this->norder}");
            return true;
        }
    }
