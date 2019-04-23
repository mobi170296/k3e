<?php
    namespace App\Models;
    
    class Pagination{
        public $current, $itemstotal, $params = [], $itemsperpage;
        public $total;
        public function __construct($c, $t, $p, $i = 10) {
            $this->current = $c;
            $this->itemstotal = $t;
            $this->params = $p;
            $this->itemsperpage = $i;
            $this->total = ceil($t/$i);
        }
    }