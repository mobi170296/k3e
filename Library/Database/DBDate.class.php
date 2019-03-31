<?php
    namespace Library\Database;
    
    class DBDate implements DBDataType{
        public $year, $month, $day;
        
        public function __construct($day, $month, $year){
            $this->day = $day;
            $this->month = $month;
            $this->year = $year;
        }
        
        public function getYear() {
            return $this->year;
        }

        public function getMonth() {
            return $this->month;
        }

        public function getDay() {
            return $this->day;
        }
        
        public function isValid(){
            return checkdate($this->month, $this->day, $this->year);
        }
        
        public function toValue(){
            return "'{$this->year}-{$this->month}-{$this->day}'";
        }
        
        public function __toString(){
            return "'{$this->year}-{$this->month}-{$this->day}'";
        }
        
        public static function parse($date){
            if(preg_match('/^(?<year>\d{4})-(?<month>\d{2})-(?<day>\d{2})$/', $date, $match)){
                return new DBDate($match['day'], $match['month'], $match['year']);
            }else{
                return null;
            }
        }
    }