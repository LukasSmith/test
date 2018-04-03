<?php

Zend_Loader::loadClass('Custom_Model');

class System extends Custom_Model{
    
    public static $handler;
    
    public static function getHandler(){
        if(!isset(self::$handler))
            self::$handler = new System();
        return self::$handler;
    }

    public function addAlert($parIdDictAlerts){
        $data = array(
            'session_id' => Zend_Session::getId(),
            'id_dict_alerts' => $parIdDictAlerts
        );
        
        if(!$this->objDB->insert('system_alerts', $data))
            throw new Exception('Error while adding alert');
        return true;
    }
    
    public function getAlerts(){
        $select = $this->objDB->select()
            ->from(array('ale' => 'system_alerts'), array('id'))
            ->join(array('dcta' => 'dict_alerts'), 'dcta.id = ale.id_dict_alerts', array('type', 'description'))
            ->where('ale.session_id = ?', Zend_Session::getId())
            ->where('ale.ghost = ?', 'FALSE');
        
        $alerts = $this->select($select);
        
        foreach($alerts as $alert)
            $this->objDB->update('system_alerts', array('ghost' => 'TRUE'), array('id = ?' => $alert['id']));

        return $alerts;
    }
    
}

?>