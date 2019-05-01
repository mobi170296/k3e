<?php
    namespace App\Controllers;
    use Core\Controller;
    
    use Library\Image\ImageResizer;
    use Library\Image\ImageResizerException;
    
    class imgController extends Controller{
        public function thumbnail($url, $w = 300, $h = 300){
            if(!is_numeric($w) || !is_numeric($h) || $w > 2000 || $h > 2000){
                return $this->View->RenderContent('INVALID');
            }
            
            $path = k3_ROOT . DS . 'public' . DS . str_replace('/', DS, $url);
            
            header('content-type: image/png');
            
            try{
                $imageresizer = new ImageResizer($path);
                
                header('content: image/png');
                
                $imageresizer->containResizeBuffer($w, $h);
                
            }catch(ImageResizerException $e){
                
            }
        }
    }