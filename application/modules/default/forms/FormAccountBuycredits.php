<?php

Zend_Loader::loadClass('Custom_Form');

class FormAccountBuycredits extends Custom_Form {

    public function __construct(){
        parent::__construct();

        $packages = $this->objDictionary->packages->get();
        $operators = $this->objDictionary->operators->get();
        
        $this->addElement('select', 'package', array(
            'multiOptions' => $this->getOptions($packages, 'id', 'description'),
            'required' => true
        ));
        
        $this->addElement('select', 'operator', array(
            'multiOptions' => $this->getOptions($operators, 'id', 'description'),
            'required' => true
        ));

    }
    
}
?>