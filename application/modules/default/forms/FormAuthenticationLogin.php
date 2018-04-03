<?php

Zend_Loader::loadClass('Custom_Form');

class FormAuthenticationLogin extends Custom_Form {

    public function __construct(){
        parent::__construct();

        $this->addElement('text', 'email', array(
            'required' => true
        ));
        
        $this->addElement('password', 'password', array(
            'required' => true
        ));

    }
    
}
?>