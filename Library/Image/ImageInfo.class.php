<?php 
    namespace Library\Image;
    
    class ImageInfo{
        public $filename;
        public $info;
        public function __construct($filename){
            if(file_exists($filename)){
                @$this->info = getimagesize($filename);
                if($this->info==0){
                    throw new ImageInfoException('Not Image', -1);
                }
            }else{
                throw new ImageInfoException('File not found', -1);
            }
            
            $this->filename = $filename;
        }
        
        public function getMimeType(){
            return $this->info['mime'];
        }
        
        public function getRealExtension(){
            $temp = explode('/', $this->info['mime']);
            return $temp[count($temp) - 1];
        }
        
        public function getSize(){
            $result = new \stdClass();
            $result->width = $this->info[0];
            $result->height = $this->info[1];
            return $result;
        }
    }