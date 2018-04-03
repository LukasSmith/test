<?php

Zend_Loader::loadClass('Show');
//Zend_Loader::loadClass('Credits');
Zend_Loader::loadClass('Custom_Controller_Action');

class ShowController extends Custom_Controller_Action {
    
    private $objShow;
    //private $objCredits;
    
	public function init() {
		parent::init();

        $this->objShow = new Show();
        //$this->objCredits = new Credits();
	}
    
    function publicAction(){
        if(!$user = $this->objUser->getUser(array('nick' => $this->varParams['nick'])))
            $this->_redirect('/index/index');
        
        debug('MODEL SHOW: '.serialize($user));
            
        if(!$show = $this->objShow->getPublic(array('id_users' => $user['id'])))
            $this->_redirect('/index/index');

        debug('PUBLIC SHOW: '.serialize($show));
        
        $this->view->data = array(
            'user' => $user,
            'show' => $show,
        );
    }
    
    function privateAction(){

    }
    
    function groupAction(){

    }
    
}
?>