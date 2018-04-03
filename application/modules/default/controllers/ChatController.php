<?php

Zend_Loader::loadClass('Show');
Zend_Loader::loadClass('Model');
Zend_Loader::loadClass('Credits');
Zend_Loader::loadClass('Custom_Controller_Action');

class ChatController extends Custom_Controller_Action {
    
    private $objShow;
    private $objModel;
    private $objCredits;
    
	public function init() {
		parent::init();
        
        $this->objShow = new Show();
        $this->objModel = new Model();
        $this->objCredits = new Credits();
	}
    
    function transmissionAction(){
        if($show = $this->objShow->getPublic(array('id_users' => $this->varUserData['id']))){
            $this->view->private = $this->objShow->getAvailable($show['id'], 1);
            $this->view->group = $this->objShow->getAvailable($show['id'], 2);
        }
        
    }
    
    function viewerAction(){
        if($user = $this->objUser->getUserByNick($this->varParams['nick'])){
            $functionParams = array(
                'id_users' => $user['id']
            );
            
            if(!$model = $this->objModel->getModel($functionParams))
                $this->_redirect('/index/index');

            if(!$show = $this->objShow->getShow($functionParams))
                $this->_redirect('/index/index');
            
            $functionParams = array(
                'id_shows' => $show['id'],
                'id_dict_shows_types' => 1
            );
            
            //$this->view->private = $this->objShow->getAvailableShow($functionParams);
            
            $functionParams = array(
                'id_shows' => $show['id'],
                'id_dict_shows_types' => 2
            );
            
            //$this->view->group = $this->objShow->getAvailableShow($functionParams);

            $this->view->model = $model;
            $this->view->show = $show;
        }else{
            $this->_redirect('/index/index');
        }
    }
}
?>