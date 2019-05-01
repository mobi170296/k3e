<?php
    namespace Library\Common;
    
    class Text{
        public $text;
        public function __construct($text){
            $this->text = $text;
        }
        
        public static function trim($str){
            $str = rtrim($str);
            $str = ltrim($str);
            $str = preg_replace('/\s{2,}/', ' ', $str);
            return $str;
        }
        
        public static function getWords($str){
            return explode(' ', self::trim($str));
        }
    }