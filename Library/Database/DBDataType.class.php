<?php
    namespace Library\Database;
    
    interface DBDataType{
        public function toValue();
        public function SqlValue();
    }