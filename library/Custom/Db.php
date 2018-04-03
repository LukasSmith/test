<?php

Zend_Loader::loadClass('Zend_Db');

class Custom_Db extends Zend_Db {
    
    public static $handler;
    
    public static function getHandler($adapter = null, $config = null){
        if(!isset(self::$handler))
            self::$handler = self::factory($adapter, $config);
        return self::$handler;
    }

}

?>
