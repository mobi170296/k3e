<?php
    namespace App\Models;
    
    class Pagination{
        public $current, $total, $params = [];
        public function __construct($c, $t, $p) {
            $this->current = $c;
            $this->total = $t;
            $this->params = $p;
        }
    }