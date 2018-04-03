<?php

Zend_Loader::loadClass('Custom_Model');

class Authentication extends Custom_Model{
    
    public static $handler;
    
    public static function getHandler(){
        if(!isset(self::$handler))
            self::$handler = new Authentication();
        return self::$handler;
    }
    
    public function login($params){
        $select = $this->objDB->select()
            ->from(array('usr' => 'users'), array('id', 'nick', 'email'))
            ->where('usr.password = ?', md5($params['password']))
            ->where('usr.email = ?', $params['email']);
        
        if(!$userData = $this->select($select, false))
            return false;
        
        $objSession = $this->getSession();
        $objSession->userData = $userData;
        
        return true;
    }
    
    public function logout(){
        Zend_Session::namespaceUnset('authentication');
    }
    
    private function getSession(){
        return new Zend_Session_Namespace('authentication');
    }
    
    public function getStatus(){
        $objSession = $this->getSession();
        if($objSession->userData['id'] > 0)
            return true;
        return false;
    }
    
    public function getIdUsers(){
        $objSession = $this->getSession();
        if($objSession->userData['id'] > 0)
            return $objSession->userData['id'];
        return false;
    }
    
    public function getUserData(){
        $objSession = $this->getSession();
        if($objSession->userData['id'] > 0)
            return $objSession->userData;
        return false;
    }
 
}

?>