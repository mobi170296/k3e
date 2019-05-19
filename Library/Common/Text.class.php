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
        
        public static function toASCII($text){
            $patterns = [
                'a' => 'á à ả ã ạ ă ắ ằ ẳ ẵ ặ â ấ ầ ẩ ẫ ậ',
                'd' => 'đ',
                'e' => 'é è ẻ ẽ ẹ ê ế ề ể ễ ệ',
                'i' => 'í ì ỉ ĩ ị',
                'o' => 'ó ò ỏ õ ọ ơ ớ ờ ở ỡ ợ ô ố ồ ổ ỗ ộ',
                'u' => 'ú ù ủ ũ ụ ư ứ ừ ử ữ ự',
                'y' => 'ý ỳ ỷ ỹ ỵ'
            ];
            
            foreach($patterns as $c => $cs){
                $patterns[$c] = str_replace(' ', '|', $cs);
            }
            
            foreach($patterns as $c => $cs){
                $text = preg_replace('/' . mb_strtoupper($cs) . '/', mb_strtoupper($c), $text);
                $text = preg_replace('/' . $cs . '/', $c, $text);
            }
            
            return $text;
        }
        
        public static function toSeqASCII($text, $separater = '-'){
            $text = trim($text, $separater);
            $text = self::trim($text);
            $text = str_replace(' ', $separater, $text);
            $text = preg_replace('/-+/', '-', $text);
            $text = mb_strtolower($text);
            return self::toASCII($text);
        }
        
        public static function htmlentities($s){
            return htmlentities($s);
        }
    }