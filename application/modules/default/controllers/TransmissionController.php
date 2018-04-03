<?php

Zend_Loader::loadClass('Show');
//Zend_Loader::loadClass('Credits');
Zend_Loader::loadClass('Gallery');
Zend_Loader::loadClass('Custom_Controller_Action');

class TransmissionController extends Custom_Controller_Action {
    
    private $objShow;
    //private $objCredits;
    private $objGallery;
    
	public function init() {
		parent::init();           
        
        $this->objShow = new Show();
        //$this->objCredits = new Credits();
        $this->objGallery = new Gallery();
	}
    
    function indexAction(){
        if($show = $this->objShow->getPublic(array('id_users' => $this->varUserData['id']))){
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
    }

}
?>