<?php

Zend_Loader::loadClass('Custom_Controller_Action');

class IndexController extends Custom_Controller_Action {
	
	function indexAction(){
        $categories = $this->objDictionary->categories->get();
        foreach($categories as $key => $category)
            $categories[$key]->broadcastingUsers = $this->objUser->getBroadcastingCategoryUsersNumber($category->id);
        
        $this->view->data = array(
            'categories' => $categories,
            'broadcastingUsers' => $this->objUser->getBroadcastingUsers($this->varParams['page']),
            'broadcastingTopUsers' => $this->objUser->getBroadcastingTopUsers(1)
        );
	}
    
    function newAction() {
        $categories = $this->objDictionary->categories->get();
        foreach($categories as $key => $category)
            $categories[$key]->broadcastingUsers = $this->objUser->getBroadcastingCategoryUsersNumber($category->id);
        
        $this->view->data = array(
            'categories' => $categories,
            'broadcastingUsers' => $this->objUser->getBroadcastingNewUsers($this->varParams['page']),
            'broadcastingTopUsers' => $this->objUser->getBroadcastingTopUsers()
        );
	}
    
    function topAction() {
        $categories = $this->objDictionary->categories->get();
        foreach($categories as $key => $category)
            $categories[$key]->broadcastingUsers = $this->objUser->getBroadcastingCategoryUsersNumber($category->id);
        
        $this->view->data = array(
            'categories' => $categories,
            'broadcastingUsers' => $this->objUser->getBroadcastingTopUsers($this->varParams['page']),
            'broadcastingTopUsers' => $this->objUser->getBroadcastingTopUsers()
        );
	}
	
    function categoryAction() {
        $categories = $this->objDictionary->categories->get();
        foreach($categories as $key => $category)
            $categories[$key]->broadcastingUsers = $this->objUser->getBroadcastingCategoryUsersNumber($category->id);
        
        $this->view->data = array(
            'categories' => $categories,
            'broadcastingTopUsers' => $this->objUser->getBroadcastingTopUsers(),
            'category' => $this->objDictionary->categories->id($this->varParams['id']),
            'broadcastingUsers' => $this->objUser->getBroadcastingCategoryUsers($this->varParams['id'], $this->varParams['page'])
        );
	}
    
}
?>