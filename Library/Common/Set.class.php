<?php
    namespace Library\Common;
    
    class Set{
        public $a = [];
        public function __construct(){
            $args = func_get_args();
            if(count($args)){
                if(gettype($args[0]) === 'array'){
                    foreach($args[0] as $e){
                        if(!in_array($e, $this->a)){
                            $this->a[] = $e;
                        }
                    }
                }else{
                    foreach($args as $e){
                        if(!in_array($e, $this->a)){
                            $this->a[] = $e;
                        }
                    }
                }
            }
        }
        public function contains($a){
            foreach($a as $e){
                if(!in_array($e, $this->a)){
                    return false;
                }
            }
            return true;
        }
        public function minus(Set $s){
            $result = new Set();
            foreach($this->a as $e){
                if(!in_array($e, $s->a)){
                    $result->add($e);
                }
            }
            return $result;
        }
        public function intersect(Set $s){
            $result = new Set();
            foreach($this->a as $e){
                if(in_array($e, $s->a)){
                    $result->add($e);
                }
            }
            return $result;
        }
        public function add($e){
            if(!in_array($e, $this->a)){
                $this->a[] = $e;
            }
        }
        public function isInteger(){
            foreach($this->a as $e){
                if(!is_int($e)){
                    return false;
                }
            }
            return true;
        }
    }