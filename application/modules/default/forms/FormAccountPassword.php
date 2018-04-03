<?php

Zend_Loader::loadClass('Custom_Form');

class FormAccountPassword extends Custom_Form {

    public function __construct(){
        parent::__construct();

        $this->addElement('password', 'old_password', array(
            'required' => true
        ));
        
        $this->addElement('password', 'new_password', array(
            'required' => true
        ));
        
        $this->addElement('password', 'new_password_confirm', array(
            'validators' => array(
                array('identical', false, array('new_password'))
            ),
            'required' => true
        ));

    }
    
}
?>