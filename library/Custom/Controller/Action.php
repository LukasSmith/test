<?php

Zend_Loader::loadClass('Acl');
Zend_Loader::loadClass('Menu');
Zend_Loader::loadClass('User');
Zend_Loader::loadClass('System');
Zend_Loader::loadClass('Credits');
Zend_Loader::loadClass('Dictionary');
Zend_Loader::loadClass('Authentication');

abstract class Custom_Controller_Action extends Zend_Controller_Action {

    protected $varParams;
    protected $varUserData;
    
    protected $objDB;
    protected $objAcl;
    protected $objMenu;
    protected $objUser;
    protected $objSystem;
    protected $objConfig;
    protected $objCredits;
    protected $objMainConfig;
    protected $objDictionary;
    protected $objAuthentication;

    public function init(){
        $this->objCredits = new Credits();
        $this->objMenu = new Menu($this->_request);
        $this->objMainConfig = Zend_Registry::getInstance()->get('objMainConfig');
        $this->objConfig = new Zend_Config_Ini($this->objMainConfig->path->configs->default, APPLICATION_ENV);
        
        $this->objAcl = Acl::getHandler();
        $this->objUser = User::getHandler();
        $this->objDB = Custom_Db::getHandler();
        $this->objSystem = System::getHandler();
        $this->objDictionary = Dictionary::getHandler();
        $this->objAuthentication = Authentication::getHandler();
        
        $this->varParams = $this->_request->getParams();
        $this->varUserData = $this->objAuthentication->getUserData();
    }
    
    public function preDispatch(){
        $this->view->setScriptPath($this->objConfig->path->templates);

        $functionParams = array(
            'module' => $this->_request->getModuleName(),
            'action' => $this->_request->getActionName(),
            'controller' => $this->_request->getControllerName()
        );

        if(!$this->objAcl->checkGuestPermissions($functionParams)){
            if(!$this->objAuthentication->getStatus())
                $this->_redirect('/authentication/login');
            if(!$this->objAcl->checkPermissions($functionParams))
                $this->_redirect('/authentication/login');
        }
    }
    
    public function postDispatch(){
        if(!empty($this->varUserData['id']))
            $credits = $this->objCredits->getBalance($this->varUserData['id']);
        
        $this->view->system = array(
            'title' => 'CAMSLAY v1.000',
            'urlParams' => $this->_request->getUserParams(),
            'loginStatus' => $this->objAuthentication->getStatus(),
            'userData' => $this->objAuthentication->getUserData(),
            'systemAlerts' => $this->objSystem->getAlerts(),
            'mainMenu' => $this->objMenu->getMainMenu(),
            'subMenu' => $this->objMenu->getSubMenu(),
            'credits' => $credits
        );
    }
    
}

?>