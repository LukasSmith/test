<?php

Zend_Loader::loadClass('Custom_Controller_Action');

Zend_Loader::loadClass('FormAuthenticationLogin');
Zend_Loader::loadClass('FormAuthenticationRegister');

class AuthenticationController extends Custom_Controller_Action {
    
    public function init() {
        parent::init();
    }
    
    function loginAction() {
        $formLogin = new FormAuthenticationLogin();
        
        if($this->_request->isPost()){
            if($formLogin->validate($this->_request->getPost()))
                $functionParams = array(
                    'email' => $formLogin->getValue('email'),
                    'password' => $formLogin->getValue('password')
                );
            
                $this->objAuthentication->login($functionParams);
        }
        
        $this->_redirect('/index/index');
    }
    
    function logoutAction() {
        $this->objAuthentication->logout();
        $this->_redirect('/authentication/login');
    }
    
    function registerAction() {
        $formRegister = new FormAuthenticationRegister();
        
        if($this->_request->isPost()){
            if($formRegister->validate($this->_request->getPost())){
                $functionParams = array(
                    'email' => $formRegister->getValue('email'),
                    'nick' => $formRegister->getValue('username'),
                    'password' => $formRegister->getValue('password')
                );
                
                if($this->objUser->addUser($functionParams))
                    $this->_redirect('/authentication/login');
            }
        }
        
        $categories = $this->objDictionary->categories->get();
        foreach($categories as $key => $category)
            $categories[$key]->broadcastingUsers = $this->objUser->getBroadcastingCategoryUsersNumber($category->id);
        
        $this->view->data = array(
            'categories' => $categories,
            'broadcastingTopUsers' => $this->objUser->getBroadcastingTopUsers()
        );
        
        $this->view->forms = array(
            'register' => $formRegister
        );
    }
    
}
?>