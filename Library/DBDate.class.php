<?php
    namespace Library;
    
    class DBDate implements DBDataType{
        public $year, $month, $day;
        
        public function __construct($day, $month, $year){
            $this->day = $day;
            $this->month = $month;
            $this->year = $year;
        }
        
        public function isValid(){
            return checkdate($this->month, $this->day, $this->year);
        }
        
        public function toValue(){
            return "'{$this->year}-{$this->month}-{$this->day}'";
        }
    }