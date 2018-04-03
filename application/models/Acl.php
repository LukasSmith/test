<?php

Zend_Loader::loadClass('Custom_Model');

class Acl extends Custom_Model{
    
    public static $handler;
    
    public static function getHandler(){
        if(!isset(self::$handler))
            self::$handler = new Acl();
        return self::$handler;
    }
    
    public function checkGuestPermissions($params){
        $params = $this->validateParams($params, 'module', 'controller', 'action');
        
        $select = $this->objDB->select()
            ->from(array('dctc' => 'dictionaries.dict_controllers'), array())
            ->join(array('dctm' => 'dictionaries.dict_modules'), 'dctm.id = dctc.id_dict_modules', array())
            ->join(array('dcta' => 'dictionaries.dict_actions'), 'dcta.id_dict_controllers = dctc.id', array('id'))
            ->where('dcta.is_authentication_required = ?', 'FALSE')
            ->where('dctc.name = ?', $params['controller'])
            ->where('dcta.name = ?', $params['action'])
            ->where('dctm.name = ?', $params['module']);
        
        if(!$this->select($select, false))
            return false;
        return true;
    }
    
    public function checkPermissions($params){
        $params = $this->validateParams($params, 'module', 'controller', 'action');
        
        $select = $this->objDB->select()
            ->from(array('dctc' => 'dictionaries.dict_controllers'), array())
            ->join(array('dctm' => 'dictionaries.dict_modules'), 'dctm.id = dctc.id_dict_modules', array())
            ->join(array('dcta' => 'dictionaries.dict_actions'), 'dcta.id_dict_controllers = dctc.id', array('id'))
            ->where('dcta.is_authentication_required = ?', 'TRUE')
            ->where('dctc.name = ?', $params['controller'])
            ->where('dcta.name = ?', $params['action'])
            ->where('dctm.name = ?', $params['module']);
        
        if(!$this->select($select, false))
            return false;
        return true;
    }
    
}

?>