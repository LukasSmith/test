<?php
chdir(__DIR__);
error_reporting(E_ALL^E_NOTICE^E_WARNING|E_STRICT);
date_default_timezone_set('Europe/Warsaw');

set_include_path('.'.PATH_SEPARATOR.getcwd()
                    .PATH_SEPARATOR.getcwd().'/../../'
                    .PATH_SEPARATOR.getcwd().'/../../library/'
                    .PATH_SEPARATOR.getcwd().'/../../application/models/'
                    .PATH_SEPARATOR.get_include_path());

include "Zend/Loader.php";
    
Zend_Loader::loadClass('Custom_Db');
Zend_Loader::loadClass('Zend_Config_Ini');

class CronAbstract{
    
    public $objDB;
    public $objConfig;
    
    public function __construct(){
        $this->objConfig = new Zend_Config_Ini('application/configs/config.ini', 'production');
        $this->objDB = Custom_Db::getHandler($this->objConfig->db->adapter, $this->objConfig->db->params);  
        $this->objDB->query('SET search_path TO public, dictionaries;');
    }

    protected function select($select, $fetchAll = true){
        $query = $this->objDB->query($select);
        if($fetchAll === true)
            return $query->fetchAll();
        return $query->fetch();
    }
    
}

?>
