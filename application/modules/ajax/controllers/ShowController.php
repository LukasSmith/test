<?php

Zend_Loader::loadClass('Show');
Zend_Loader::loadClass('Credits');
Zend_Loader::loadClass('Custom_Controller_Ajax');

class Ajax_ShowController extends Custom_Controller_Ajax {
    
    private $objShow;
    private $objCredits;
    
	public function init() {
		parent::init();

        $this->objShow = new Show();
        $this->objCredits = new Credits();
	}
    
    function getshowAction(){
        if(!$show = $this->objShow->getPublic(array('nick' => $this->varParams['nick'])))
            throw new Exception('No active show', 1);

        $this->addResponse('show', $show);
        
        if($private = $this->objShow->getPrivate($show['id'])){
            if(!$this->objShow->getPrivateParticipant($this->varUserData['id']))
                throw new Exception('No permissions', 1);

            $this->addResponse('messages', $this->objShow->getPrivateMessages($private['id']));
        }elseif($group = $this->objShow->getGroup($show['id'])){
            if(!$this->objShow->getGroupParticipant($this->varUserData['id']))
                throw new Exception('No permissions', 1);

            $this->addResponse('messages', $this->objShow->getGroupMessages($group['id']));
        }else{
            $this->addResponse('messages', $this->objShow->getPublicMessages($show['id']));
            $this->addResponse('grouprequests', $this->objShow->getGroupRequests($show['id']));
            $this->addResponse('grouprequestscredits', (integer)$this->objShow->getGroupRequestsCredits($show['id']));
        }
    }
       
    function addmessageAction(){
        if(!$show = $this->objShow->getPublic(array('nick' => $this->varParams['nick'])))
            throw new Exception('No active show', 1);
        
        if($private = $this->objShow->getPrivate($show['id'])){
            if(!$this->objShow->getPrivateParticipant($this->varUserData['id']))
                throw new Exception('No permissions', 1);
            
            $this->objShow->addPrivateMessage($this->varParams['message'], $private['id'], $this->varUserData['id'], 1);
        }elseif($group = $this->objShow->getGroup($show['id'])){
            if(!$this->objShow->getGroupParticipant($this->varUserData['id']))
                throw new Exception('No permissions', 1);
            
            $this->objShow->addGroupMessage($this->varParams['message'], $group['id'], $this->varUserData['id'], 1);
        }else{
            $this->objShow->addPublicMessage($this->varParams['message'], $show['id'], 1, $this->varUserData['id']);
        }
    }

    function addprivateAction(){
        if(!$show = $this->objShow->getPublic(array('nick' => $this->varParams['nick'])))
            throw new Exception('No active show', 1);
        
        if(!$show['is_private_enabled'])
            throw new Exception('Private show is not available', 1);
        
        if($request = $this->objShow->getPrivateRequest($this->varUserData['id'], $show['id']))
            throw new Exception('Request already added', 1);
        
        if($private = $this->objShow->getPrivate($show['id']))
            throw new Exception('Private show is in progress', 1);
        
        if($group = $this->objShow->getGroup($show['id']))
            throw new Exception('Group show is in progress', 1);
        
        $balance = $this->objCredits->getBalance($this->varUserData['id']);
        if($balance['balance'] < $available['credits'])
            throw new Exception('Not enough credits', 1);
        
        $this->objShow->addPrivateRequest($available['credits'], $this->varUserData['id'], $show['id']);
        $this->objCredits->addTransaction($this->varUserData['id'], $available['credits'], 2, 2, 2);
    }
    
    function addgroupAction(){
        if(!$show = $this->objShow->getPublic(array('nick' => $this->varParams['nick'])))
            throw new Exception('No active show', 1);
        
        if(!$show['is_group_enabled'])
            throw new Exception('Group show is not available', 1);
        
        if($private = $this->objShow->getPrivate($show['id']))
            throw new Exception('Private show is in progress', 1);
        
        if($group = $this->objShow->getGroup($show['id']))
            throw new Exception('Group show is in progress', 1);
        
        $balance = $this->objCredits->getBalance($this->varUserData['id']);
        if($balance['balance'] < 10)
            throw new Exception('Not enough credits', 1);
        
        $this->objShow->addGroupRequest(10, $this->varUserData['id'], $show['id']);
        $this->objCredits->addTransaction($this->varUserData['id'], 10, 2, 3, 2);
        $this->objShow->addPublicMessage('joined group show (10 credits)', $show['id'], 4, $this->varUserData['id']);
    }
    
    function addtipAction(){
        if(!$show = $this->objShow->getPublic(array('nick' => $this->varParams['nick'])))
            throw new Exception('No active show', 1);

        $balance = $this->objCredits->getBalance($this->varUserData['id']);
        if($balance['balance'] < 10)
            throw new Exception('Not enough credits', 1);
        
        $this->objCredits->addTransaction($this->varUserData['id'], 10, 2, 6, 2);
        $this->objCredits->addTransaction($show['id_users'], 10, 1, 7, 2);
        
        if($private = $this->objShow->getPrivate($show['id'])){
            if(!$this->objShow->getPrivateParticipant($this->varUserData['id']))
                throw new Exception('No permissions', 1);
            
            $this->objShow->addPrivateMessage('10 credits tip', $private['id'], $this->varUserData['id'], 3);
        }elseif($group = $this->objShow->getGroup($show['id'])){
            if(!$this->objShow->getGroupParticipant($this->varUserData['id']))
                throw new Exception('No permissions', 1);
            
            $this->objShow->addGroupMessage('10 credits tip', $group['id'], $this->varUserData['id'], 3);
        }else{
            $this->objShow->addPublicMessage('10 credits tip', $show['id'], 3, $this->varUserData['id']);
        }
    }

}
?>