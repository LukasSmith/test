<?php

Zend_Loader::loadClass('Acl');
Zend_Loader::loadClass('User');
Zend_Loader::loadClass('Dictionary');
Zend_Loader::loadClass('Authentication');

abstract class Custom_Controller_Ajax extends Zend_Controller_Action {

    //VARIABLES
    protected $varAlerts;
    protected $varParams;
    protected $varUserData;
    protected $varResponse;
    
    //OBJECTS
    protected $objDB;
    protected $objAcl;
    protected $objUser;
    protected $objConfig;
    protected $objMainConfig;
    protected $objDictionary;
    protected $objAuthentication;

    public function init(){
        $this->objAcl = Acl::getHandler();
        $this->objUser = User::getHandler();
        $this->objDB = Custom_Db::getHandler();
        $this->objDictionary = Dictionary::getHandler();
        $this->objAuthentication = Authentication::getHandler();
        $this->objMainConfig = Zend_Registry::getInstance()->get('objMainConfig');
        $this->objConfig = new Zend_Config_Ini($this->objMainConfig->path->configs->ajax, APPLICATION_ENV);
        
        $this->varParams = $this->_request->getParams();
        $this->varUserData = $this->objAuthentication->getUserData();
        $this->varResponse['handling'] = $this->_request->getActionName();
    }
    
    public function preDispatch(){
        $this->view->setScriptPath($this->objConfig->path->templates);
        
        debug('PARAMS IN Ajax -> preDispatch(): '.serialize($this->varParams));
        
        $functionParams = array(
            'module' => $this->_request->getModuleName(),
            'action' => $this->_request->getActionName(),
            'controller' => $this->_request->getControllerName()
        );
        
        if(!$this->objAcl->checkGuestPermissions($functionParams)){
            if(!$this->objAuthentication->getStatus()){
                $this->addResponse('status', false);
                $this->addAlert(1, 'No permissions');
                $this->returnJSON();
            }
            
            if(!$this->objAcl->checkPermissions($functionParams)){
                $this->addResponse('status', false);
                $this->addAlert(1, 'No permissions');
                $this->returnJSON();
            }
        }
        
        $this->objDB->beginTransaction();
    }
    
    public function dispatch($action){
        try{
            $this->addResponse('status', true);
            parent::dispatch($action);
        }catch(Exception $e){
            $this->objDB->rollBack();
            $this->addAlert($e->getCode(), $e->getMessage());
            debug('EXCEPTION: '.$e->getMessage());
            $this->addResponse('status', false);
            $this->returnJSON();
        }
    }
    
    public function returnJSON(){
        exit(json_encode(array(
            'alerts' => $this->varAlerts,
            'response' => $this->varResponse
        )));
    }
    
    public function returnString($response = null){
        exit($response);
    }
    
    public function postDispatch(){
        $this->objDB->commit();
        debug('RESPONSE IN Ajax -> preDispatch(): '.serialize(array('alerts' => $this->varAlerts,'response' => $this->varResponse)));
        $this->returnJSON();
    }
    
    public function addResponse($key, $value){
        $this->varResponse[$key] = $value;
    }
    
    public function addAlert($code, $message){        
        $this->varAlerts[] = array(
            'code' => $code, 
            'message' => $message, 
            'isVisible' => ($code == 1)
        );
    }
    
}

?>