<?php
    namespace Library\Image;
    
    class ImageResizer{
        public $filename;
        public $imageinfo;
        public $sw, $sh;
        public $sourcehandle;
        public function __construct($filename){
            $this->filename = $filename;
            try{
                $this->imageinfo = new ImageInfo($filename);
                $this->sw = $this->imageinfo->getSize()->width;
                $this->sh = $this->imageinfo->getSize()->height;
                $mimetype = $this->imageinfo->getMimeType();
                switch($mimetype){
                    case 'image/png':
                        $this->sourcehandle = imagecreatefrompng($filename);
                        break;
                    case 'image/gif':
                        $this->sourcehandle = imagecreatefromgif($filename);
                        break;
                    case 'image/jpeg':
                        $this->sourcehandle = imagecreatefromjpeg($filename);
                        break;
                    default: throw new ImageResizerException('Format not supported', -1);
                }
            }catch(ImageInfoException $e){
                throw new ImageResizerException($e->getMessage(), $e->getCode());
            }
        }
        
        public function coverResize($w, $h, $newname = null){
            $dx = $dy = 0;
            $dw = $w;
            $dh = $h;
            $sw = $this->sw;
            $sh = $this->sh;
            
            $sratio = $sw / $sh;
            $dratio = $dw / $dh;
            
            if($sratio >= $dratio){
                $nsw = $dratio * $sh;
                $sx = round(($sw - $nsw) / 2);
                $sy = 0;
                $sw = $nsw;
            }else{
                $nsh = $sw / $dratio;
                $sx = 0;
                $sy = round(($sh - $nsh) / 2);
                $sh = $nsh;
            }
            
            $desthandle = imagecreatetruecolor($w, $h);
            
            if(!imagecopyresampled($desthandle, $this->sourcehandle, $dx, $dy, $sx, $sy, $dw, $dh, $sw, $sh)){
                throw new ImageResizerException('Cannot Resize', -1);
            }
            
            if($newname){
                $filename = $newname;
            }else{
                $filename = $this->filename;
            }
            
            switch($this->imageinfo->getMimeType()){
                case 'image/png':
                    imagepng($desthandle, $filename, 9);
                    break;
                case 'image/gif':
                    imagegif($desthandle, $filename);
                    break;
                case 'image/jpeg':
                    imagejpeg($desthandle, $filename, 100);
                    break;
                default: throw new ImageResizerException('Format not supported', -1);
            }
        }
        
        public function containResize($w, $h, $newname = null){
            $dw = $w;
            $dh = $h;
            $sx = $sy = 0;
            $sw = $this->sw;
            $sh = $this->sh;
            
            $sratio = $sw / $sh;
            $dratio = $dw / $dh;
            
            if($sratio >= $dratio){
                $dh = $w / $sratio;
                $dx = 0;
                $dy = ($h - $dh) / 2;
            }else{
                $dw = $sratio * $h;
                $dx = ($w - $dw) / 2;
                $dy = 0;
            }
            
            $desthandle = imagecreatetruecolor($w, $h);
            
            #allocate white color
            $background = imagecolorallocatealpha($desthandle, 0xff, 0xff, 0xff, 0);
            
            if($background === false){
                throw new ImageResizerException('Cannot allocate color', -1); 
            }
            
            imagefill($desthandle, 0, 0, $background);
            
            if(!imagecopyresampled($desthandle, $this->sourcehandle, $dx, $dy, $sx, $sy, $dw, $dh, $sw, $sh)){
                throw new ImageResizerException('Cannot Resize', -1);
            }
            
            if($newname){
                $filename = $newname;
            }else{
                $filename = $this->filename;
            }
            
            switch($this->imageinfo->getMimeType()){
                case 'image/png':
                    imagepng($desthandle, $filename, 9);
                    break;
                case 'image/gif':
                    imagegif($desthandle, $filename);
                    break;
                case 'image/jpeg':
                    imagejpeg($desthandle, $filename, 100);
                    break;
                default: throw new ImageResizerException('Format not supported', -1);
            }
        }
        
        public function free(){
            imagedestroy($this->sourcehandle);
        }
    }