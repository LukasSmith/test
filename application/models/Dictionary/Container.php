<?php

class Dictionary_Container{
    
    public $varItems;
    public $varMapper;
    
    public function get(){
        return $this->varItems;
    }
    
    public function __call($method, $params){
        if(array_key_exists($method, $this->varMapper))
            return $this->varMapper[$method][$params[0]];
        throw new Exception('Missing param: '.$method);
    }
    
}

?>
