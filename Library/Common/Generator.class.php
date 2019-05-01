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
        public static function name($mc, $l){
            $name = [];
            $a1 = range(ord('A'), ord('Z'));
            $a2 = range(ord('a'), ord('z'));
            $a = array_merge($a1, $a2);
            for($i=0; $i<$l; $i++){
                $p = '';
                $pl = rand()%$mc + 1;
                for($j=0; $j<$pl; $j++){
                    $p .= chr($a[rand()%count($a)]);
                }
                $name[] = $p;
            }
            return implode(' ', $name);
        }
        public static function number(){
            
        }
        
        public static function orderCode(){
            #Format: {random(6) - paddingright}{microtime(14) - paddingleft}
            return str_pad(rand()%1e6, 6, '0', STR_PAD_RIGHT) . str_pad(round(microtime(true)*1e3), 14, '0', STR_PAD_LEFT);
        }
        
        public static function transactionReference(){
            return 'K' . round(microtime(true) * 1e3) . str_pad((rand()%1e6), 6, '0', STR_PAD_LEFT);
        }
    }