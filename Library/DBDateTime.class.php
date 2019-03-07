<?php
    namespace Library;
    class DBDateTime implements DBDataType{
        public $year, $month, $day, $hour, $minute, $seconds;
        public function __construct($day, $month, $year, $hour=0, $minute=0, $seconds=0){
            $this->year = $year;
            $this->month = $month;
            $this->day = $day;
            $this->hour = $hour;
            $this->minute = $minute;
            $this->seconds = $seconds;
        }
        
        public function toValue(){
            return "'{$this->year}-{$this->month}-{$this->day} {$this->hour}:{$this->minute}:{$this->seconds}'";
        }
    }