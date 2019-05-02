<?php
    namespace Library\Database;
    
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
        
        public function getYear() {
            return $this->year;
        }

        public function getMonth() {
            return $this->month;
        }

        public function getDay() {
            return $this->day;
        }

        public function getHour() {
            return $this->hour;
        }

        public function getMinute() {
            return $this->minute;
        }

        public function getSeconds() {
            return $this->seconds;
        }

                
        public function toValue(){
            return "'{$this->year}-{$this->month}-{$this->day} {$this->hour}:{$this->minute}:{$this->seconds}'";
        }
        public function SqlValue(){
            return "'{$this->year}-{$this->month}-{$this->day} {$this->hour}:{$this->minute}:{$this->seconds}'";
        }
        public function __toString(){
            return "'{$this->year}-{$this->month}-{$this->day} {$this->hour}:{$this->minute}:{$this->seconds}'";
        }
        
        public static function parse($s){
            if(preg_match('/^(?<year>\d{4})-(?<month>\d{1,2})-(?<day>\d{1,2}) (?<hour>\d{1,2}):(?<minute>\d{1,2}):(?<second>\d{1,2})$/', $s, $match)){
                return new DBDateTime($match['day'], $match['month'], $match['year'], $match['hour'], $match['minute'], $match['second']);
            }elseif(preg_match('/^(?<year>\d{4})-(?<month>\d{1,2})-(?<day>\d{1,2})$/', $s, $match)){
                return new DBDateTime($match['day'], $match['month'], $match['year']);
            }else{
                return null;
            }
        }
        
        public static function parseGHNDateTime($s){
            $match = [];
            if(preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})\+(\d{2}):(\d{2})$/', $s, $match)){
                return new DBDateTime($match[3], $match[2], $match[1], $match[4], $match[5], $match[6]);
            }else{
                return null;
            }
        }
    }