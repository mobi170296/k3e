<?php
    namespace Core;
    class ContentView extends View{
        public function __construct($content){
            
            $this->bodyContent = $content;
        }
        public function render(){
            echo $this->bodyContent;
        }
}