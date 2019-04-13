<?php
    namespace Library\Common;
    
    class Generator{
        #example ffa89c08-8ad8-40a1-b1bd-40d0b65fbdef
        # 8 - 4 - 4 - 4 - 12
        #@Test OK
        public static function guid(){
            $guid = '';
            $hyphen = [8, 13, 18, 23];
            $chars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'];
            for($x = 0; $x < 32; $x++){
                if(in_array($x, $hyphen)){
                    $guid .= '-';
                }else{
                    $guid .= $chars[rand(0, 15)];
                }
            }
            return $guid;
        }
    }