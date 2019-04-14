<?php
    namespace Library\File;
    
    class ImageUploader{
        public $filename, $dir;
        public $autopath;
        public function __construct(){
            
        }
        public function generateFileName($tempname, $extension){
            return md5($tempname) . '_' . \uniqid() . '_' . \rand() . '_' . \rand() . '.' . $extension;
        }
        
        public function upload($tempname, $extension, $dir){
            #Copy file dont write to DB because this is library for upload not model
            $date = \getdate();
            $year = $date['year'];
            $month = $date['mon'];
            $day = $date['mday'];
            
            $prefixdir = $dir . DS . $year;
            $autopath = $year;
            if(!file_exists($prefixdir)){
                mkdir($prefixdir);
            }
            
            $prefixdir .= DS . $month;
            $autopath .= DS . $month;
            if(!file_exists($prefixdir)){
                mkdir($prefixdir);
            }
            
            $prefixdir .= DS . $day;
            $autopath .= DS . $day;
            if(!file_exists($prefixdir)){
                mkdir($prefixdir);
            }
            
            $try = 5;
            $i = 0;
            $filename = '';
            
            while($i++ < $try && file_exists($prefixdir . DS . ($filename = $this->generateFileName($tempname, $extension))));
            
            if($i == $try){
                throw new UploadImageException(['try'=>'Không thể tìm tên tập tin phù hợp vui lòng thử lại!']);
            }
            
            if(!move_uploaded_file($tempname, $prefixdir . DS . $filename)){
                throw new UploadImageException(['move' => 'Ghi tập tin thất bại vui lòng thử lại']);
            }
            
            #test in CLI
//            if(!rename($tempname, $prefixdir . DS . $filename)){
//                throw new UploadImageException(['move' => 'Ghi tập tin thất bại vui lòng thử lại']);
//            }   
            $this->dir = $prefixdir;
            $this->filename = $filename;
            $this->autopath = $autopath;
        }
        public function getFileName(){
            return $this->filename;
        }
        public function getDir(){
            return $this->dir;
        }
        public function getAutoPath(){
            return $this->autopath;
        }
    }