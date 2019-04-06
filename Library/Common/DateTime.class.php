<?php
    namespace Library\Common;
    class DateTime{
        public $year, $month, $day, $hour, $minute, $second;
        public function __construct($year, $month, $day, $hour = 0, $minute = 0, $second = 0){
            $this->year= $year;
            $this->month = $month;
            $this->day = $day;
            $this->hour = $hour;
            $this->minute = $minute;
            $this->second = $second;
        }
        public function getDateString(){
            return $this->day . '/' . $this->month . '/'. $this->year;
        }
        public function getTimeString(){
            return $this->hour . ':' . $this->minute . ':' . $this->second;
        }
        public function __toString(){
            return $this->getDateString() . ' ' . $this->getTimeString();
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

        public function getSecond() {
            return $this->second;
        }

        public function setYear($year) {
            $this->year = $year;
            return $this;
        }

        public function setMonth($month) {
            $this->month = $month;
            return $this;
        }

        public function setDay($day) {
            $this->day = $day;
            return $this;
        }

        public function setHour($hour) {
            $this->hour = $hour;
            return $this;
        }

        public function setMinute($minute) {
            $this->minute = $minute;
            return $this;
        }

        public function setSecond($second) {
            $this->second = $second;
            return $this;
        }
        
        public static function parse($s){
            if(preg_match('/^(?<year>\d{4})-(?<month>\d{1,2})-(?<day>\d{1,2}) (?<hour>\d{1,2}):(?<minute>\d{1,2}):(?<second>\d{1,2})$/', $s, $match)){
                return new DateTime($match['year'], $match['month'], $match['day'], $match['hour'], $match['minute'], $match['second']);
            }elseif(preg_match('/^(?<year>\d{4})-(?<month>\d{1,2})-(?<day>\d{1,2})$/', $s, $match)){
                return new DateTime($match['year'], $match['month'], $match['day']);
            }else{
                return null;
            }
        }
    }