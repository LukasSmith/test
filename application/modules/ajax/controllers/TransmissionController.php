<?php

Zend_Loader::loadClass('Show');
Zend_Loader::loadClass('Credits');
Zend_Loader::loadClass('Custom_Controller_Ajax');

class Ajax_TransmissionController extends Custom_Controller_Ajax {
	
    private $objShow;
    private $objCredits;
    
    public function init() {
		parent::init();           
        
        $this->objShow = new Show();
        $this->objCredits = new Credits();
	}
    
	function addtransmissionAction(){
        if($this->objShow->getPublic(array('id_users' => $this->varUserData['id'])))
            throw new Exception('Show already started', 1);
        
        $publicCode = $this->objShow->getPublicCode();
        $this->objShow->addPublic($publicCode, $this->varUserData['id']);
        
        $this->addResponse('code', $publicCode);
	}
    
    function endtransmissionAction(){
        if(!$show = $this->objShow->getPublic(array('id_users' => $this->varUserData['id'])))
            throw new Exception('No active show', 1);
        
        $this->objShow->setPublicStop($show['id']);
        
        if($private = $this->objShow->getPrivate($show['id'])){
            $this->objShow->setPrivateStop($private['id']);
            if(strtotime($private['end_datetime']) > time()){
                $participants = $this->objShow->getPrivateParticipants($private['id']);
                foreach($participants as $participant){
                    $this->objCredits->addTransaction($participant['id_users'], $participant['credits'], 1, 4, 2);
                    $this->objShow->setPrivateChargeReturned($participant['id']);
                }
            }
        }elseif($group = $this->objShow->getGroup($show['id'])){
            $this->objShow->setGroupStop($group['id']);
            if(strtotime($private['end_datetime']) > time()){
                $participants = $this->objShow->getGroupParticipants($private['id']);
                foreach($participants as $participant){
                    $this->objCredits->addTransaction($participant['id_users'], $participant['credits'], 1, 5, 2);
                    $this->objShow->setGroupChargeReturned($participant['id']);
                }
            }
        }
        
        $requests = $this->objShow->getPrivateRequests($show['id']);
        foreach($requests as $request){
            $this->objShow->setPrivateRequestInactive($request['id']);
            $this->objCredits->addTransaction($request['id_users'], $request['credits'], 1, 4, 2);
        }
        
        $requests = $this->objShow->getGroupRequests($show['id']);
        foreach($requests as $request){
            $this->objShow->setGroupRequestInactive($request['id']);
            $this->objCredits->addTransaction($request['id_users'], $request['credits'], 1, 5, 2);
        }
    }
    
    function addmessageAction(){
        if(!$show = $this->objShow->getPublic(array('id_users' => $this->varUserData['id'])))
            throw new Exception('No active show', 1);
        
        if($private = $this->objShow->getPrivate($show['id'])){
            $this->objShow->addPrivateMessage($this->varParams['message'], $private['id'], $this->varUserData['id'], 2);
        }elseif($group = $this->objShow->getGroup($show['id'])){
            $this->objShow->addGroupMessage($this->varParams['message'], $group['id'], $this->varUserData['id'], 2);
        }else{
            $this->objShow->addPublicMessage($this->varParams['message'], $show['id'], 2, $this->varUserData['id']);
        }
    }
    
    function gettransmissionAction(){
        if(!$show = $this->objShow->getPublic(array('id_users' => $this->varUserData['id'])))
            throw new Exception('No active show');
        
        $this->objShow->setPublicPing($show['id']);
        
        if($private = $this->objShow->getPrivate($show['id'])){
            $this->addResponse('type', 'private');
            $this->addResponse('viewers', $private['viewers']);
            $this->addResponse('messages', $this->objShow->getPrivateMessages($private['id']));

            if(strtotime($private['end_datetime']) < time())
                $this->objShow->setPrivateStop($private['id']);
        }elseif($group = $this->objShow->getGroup($show['id'])){
            $this->addResponse('type', 'group');
            $this->addResponse('viewers', $group['viewers']);
            $this->addResponse('messages', $this->objShow->getGroupMessages($group['id']));

            if(strtotime($group['end_datetime']) < time())
                $this->objShow->setGroupStop($group['id']);
        }else{
            $this->addResponse('type', 'public');
            $this->addResponse('viewers', $show['viewers']);
            $this->addResponse('messages', $this->objShow->getPublicMessages($show['id']));
        }

        $this->addResponse('grouprequests', $this->objShow->getGroupRequests($show['id']));
        $this->addResponse('privaterequests', $this->objShow->getPrivateRequests($show['id']));
    }
    
    function setprivaterejectedAction(){
        if(!$show = $this->objShow->getPublic(array('id_users' => $this->varUserData['id'])))
            throw new Exception('No active show', 1);
        
        if(!$user = $this->objUser->getUserByNick($this->varParams['nick']))
            throw new Exception('User does not exist', 1);
        
        if(!$request = $this->objShow->getPrivateRequest($user['id'], $show['id']))
            throw new Exception('Private request does not exist', 1);
        
        $this->objShow->setPrivateRequestRejected($request['id']);
        $this->objCredits->addTransaction($request['id_users'], $request['credits'], 1, 4, 2);
    }
    
    function setgrouprejectedAction(){
        if(!$show = $this->objShow->getPublic(array('id_users' => $this->varUserData['id'])))
            throw new Exception('No active show', 1);
        
        if(!$user = $this->objUser->getUserByNick($this->varParams['nick']))
            throw new Exception('User does not exist', 1);
        
        if(!$request = $this->objShow->getGroupRequest($user['id'], $show['id']))
            throw new Exception('Private request does not exist', 1);

        $this->objShow->setGroupRequestRejected($request['id']);
        $this->objCredits->addTransaction($request['id_users'], $request['credits'], 1, 5, 2);
    }
    
    function setprivateAction(){
        if(!$show = $this->objShow->getPublic(array('id_users' => $this->varUserData['id'])))
            throw new Exception('No active show', 1);
        
        if(!$show['is_private_enabled'])
            throw new Exception('Private show is not available', 1);
            
        if(!$user = $this->objUser->getUserByNick($this->varParams['nick']))
            throw new Exception('User does not exist', 1);
        
        if(!$request = $this->objShow->getPrivateRequest($user['id'], $show['id']))
            throw new Exception('Private request does not exist', 1);
        
        $end_datetime = date('Y-m-d H:i:s', time() + $available['duration'] * 60);
        $id_shows_private = $this->objShow->addPrivate($this->objShow->getPrivateCode(), $show['id'], $end_datetime);
        
        $this->objShow->addPrivateParticipant($id_shows_private, $request['id']);

        $requests = $this->objShow->getPrivateRequests($show['id']);
        foreach($requests as $request){
            $this->objShow->setPrivateRequestInactive($request['id']);
            if($request['id_users'] != $user['id'])
                $this->objCredits->addTransaction($request['id_users'], $request['credits'], 1, 4, 2);
        }
    }
    
    function setgroupAction(){
        if(!$show = $this->objShow->getPublic(array('id_users' => $this->varUserData['id'])))
            throw new Exception('No active show', 1);
        
        if(!$show['is_group_enabled'])
            throw new Exception('Group show is not available', 1);
        
        if($this->objShow->getGroupRequestsCredits($show['id']) < $available['credits'])
            throw new Exception('Number of credits is not enough', 1);
    
        $end_datetime = date('Y-m-d H:i:s', time() + $available['duration'] * 60);
        $id_shows_group = $this->objShow->addGroup($this->objShow->getGroupCode(), $show['id'], $end_datetime);
        
        $requests = $this->objShow->getGroupRequests($show['id']);
        foreach($requests as $request){
            $this->objShow->addGroupParticipant($id_shows_group, $request['id']);
            $this->objShow->setGroupRequestInactive($request['id']);
        }
    }
    
    function setpublicAction(){
        if(!$show = $this->objShow->getPublic(array('id_users' => $this->varUserData['id'])))
            throw new Exception('No active show', 1);
        
        if($private = $this->objShow->getPrivate($show['id'])){
            $this->objShow->setPrivateStop($private['id']);
            if(strtotime($private['end_datetime']) > time()){
                $participants = $this->objShow->getPrivateParticipants($private['id']);
                foreach($participants as $participant){
                    $this->objShow->setPrivateChargeReturned($participant['id']);
                    $this->objCredits->addTransaction($participant['id_users'], $participant['credits'], 1, 4, 2);
                }
            }
        }elseif($group = $this->objShow->getGroup($show['id'])){
            $this->objShow->setGroupStop($group['id']);
            if(strtotime($group['end_datetime']) > time()){
                $participants = $this->objShow->getGroupParticipants($group['id']);
                foreach($participants as $participant){
                    $this->objShow->setGroupChargeReturned($participant['id']);
                    $this->objCredits->addTransaction($participant['id_users'], $participant['credits'], 1, 5, 2);
                }
            }
        }
    }
    
    function enableprivateAction(){
        if(!$show = $this->objShow->getPublic(array('id_users' => $this->varUserData['id'])))
            throw new Exception('No active show', 1);
        
        if($show['is_private_enabled'])
            throw new Exception('Private show is available', 1);
        
        if(!preg_match('/^\d+$/', $this->varParams['credits']))
            throw new Exception('Credits not integer', 1);
        
        if(!preg_match('/^\d+$/', $this->varParams['duration']))
            throw new Exception('Duration not integer', 1);
        
        if(empty($this->varParams['description']))
            throw new Exception('Description is empty', 1);
        
        $functionParams = array(
            'id_shows' => $show['id'],
            'credits' => $this->varParams['credits'],
            'duration' => $this->varParams['duration'],
            'description' => $this->varParams['description']
        );

        $this->objShow->addAvailable(1, $functionParams);
    }
    
    function enablegroupAction(){
        if(!$show = $this->objShow->getPublic(array('id_users' => $this->varUserData['id'])))
            throw new Exception('No active show', 1);
        
        if($show['is_group_enabled'])
            throw new Exception('Group show is available', 1);
        
        if(!preg_match('/^\d+$/', $this->varParams['credits']))
            throw new Exception('Credits not integer', 1);
        
        if(!preg_match('/^\d+$/', $this->varParams['duration']))
            throw new Exception('Duration not integer', 1);
        
        if(empty($this->varParams['description']))
            throw new Exception('Description is empty', 1);
        
        $functionParams = array(
            'id_shows' => $show['id'],
            'credits' => $this->varParams['credits'],
            'duration' => $this->varParams['duration'],
            'description' => $this->varParams['description']
        );

        $this->objShow->addAvailable(2, $functionParams);
    }
    
    function disableprivateAction(){
        if(!$show = $this->objShow->getPublic(array('id_users' => $this->varUserData['id'])))
            throw new Exception('No active show', 1);
        
        if(!$show['is_private_enabled'])
            throw new Exception('Private show is not available', 1);
        
        if($private = $this->objShow->getPrivate($show['id']))
            throw new Exception('Private show is started', 1);
        
        $this->objShow->delAvailable($show['id'], 1);
    }
    
    function disablegroupAction(){
        if(!$show = $this->objShow->getPublic(array('id_users' => $this->varUserData['id'])))
            throw new Exception('No active show', 1);
        
        if(!$show['is_group_enabled'])
            throw new Exception('Group show is not available', 1);
        
        if($group = $this->objShow->getGroup($show['id']))
            throw new Exception('Group show is started', 1);
        
        $this->objShow->delAvailable($show['id'], 2);
    }
    
    function setstatusAction(){
        if(!$show = $this->objShow->getPublic(array('id_users' => $this->varUserData['id'])))
            throw new Exception('No active show', 1);

        $this->objShow->setStatus($show['id'], $this->varParams['status']);
    }

}

?>