<?php

Zend_Loader::loadClass('Custom_Form');

class FormAuthenticationRegister extends Custom_Form {

    public function __construct(){
        parent::__construct();
        
        $this->addElement('text', 'username', array(
            'required' => true
        ));
        
        $this->addElement('text', 'email', array(
            'required' => true
        ));
        
        $this->addElement('text', 'confirm_email', array(
            'required' => true
        ));
        
        $this->addElement('password', 'password', array(
            'required' => true
        ));
        
        $this->addElement('password', 'confirm_password', array(
            'required' => true
        ));

    }
    
}
?>