<?php

Zend_Loader::loadClass('Custom_Controller_Action');

Zend_Loader::loadClass('Show');
//Zend_Loader::loadClass('Credits');
Zend_Loader::loadClass('Gallery');
Zend_Loader::loadClass('FormAccountProfile');
Zend_Loader::loadClass('FormAccountPassword');
Zend_Loader::loadClass('FormAccountBuycredits');

class AccountController extends Custom_Controller_Action {
	
    private $objShow;
    //private $objCredits;
    private $objGallery;
    
	public function init() {
		parent::init();           
        
        $this->objShow = new Show();
        //$this->objCredits = new Credits();
        $this->objGallery = new Gallery();
	}
    
	function profileAction(){
        $userProfile = $this->objUser->getProfile($this->varUserData['id']);
        $formProfile = new FormAccountProfile($userProfile);
        $formPassword = new FormAccountPassword();
        
        if($this->_request->isPost()){
            if(isset($this->varParams['profileSubmit_x'])){
                if($formProfile->validate($this->_request->getPost())){
                    $functionParams = array(
                        'id_users' => $this->varUserData['id'],
                        'id_dict_countries' => $formProfile->getValue('country'),
                        'id_dict_genders' => $formProfile->getValue('gender'),
                        'id_dict_sexual_preferences' => $formProfile->getValue('sexual_preference'),
                        'id_dict_zodiac' => $formProfile->getValue('zodiac'),
                        'id_dict_hair_colors' => $formProfile->getValue('hair_color'),
                        'id_dict_eye_colors' => $formProfile->getValue('eye_color'),
                        'id_dict_builds' => $formProfile->getValue('build'),
                        'id_dict_ethnicities' => $formProfile->getValue('ethnicity'),
                        'id_dict_cup_sizes' => $formProfile->getValue('cup_size'),
                        'id_dict_pubic_hairs' => $formProfile->getValue('pubic_hair')
                    );

                    $this->objUser->setProfile($functionParams);
                }
            }elseif(isset($this->varParams['passwordSubmit_x'])){
                if($formPassword->validate($this->_request->getPost())){
                    $oldPassword = $formPassword->getValue('old_password');
                    $newPassword = $formPassword->getValue('new_password');
                    
                    if(!$this->objUser->setPassword($this->varUserData['id'], $oldPassword, $newPassword))
                        $this->objSystem->addAlert(1);
                }
            }
        }
        
        $this->view->forms = array(
            'profile' => $formProfile,
            'password' => $formPassword
        );
	}
    
    function galleryAction(){
        if(!empty($this->varParams['mainimage'])){
            $this->objGallery->setMainImage($this->varUserData['id'], $this->varParams['mainimage']);
        }elseif(!empty($this->varParams['delete'])){
            $mainImage = $this->objGallery->getMainImage($this->varUserData['id']);
            if($mainImage['filename'] != $this->varParams['delete']){
                $this->objGallery->delImage($this->varUserData['id'], $this->varParams['delete']);
            }else{
                $this->objSystem->addAlert(1);
            }
        }
        
        $this->view->data = array(
            'images' => $this->objGallery->getImages($this->varUserData['id'])
        );
	}
    
    function transactionsAction(){
        $this->view->data = array(
            'transactions' => $this->objCredits->getTransactions($this->varUserData['id'], $this->varParams['page'])
        );
	}
    
    function categoriesAction(){
        if($this->_request->isPost() && isset($this->varParams['categories'])){
            $this->objUser->clrCategories($this->varUserData['id']);
            foreach($this->varParams['categories'] as $idDictCategories)
                $this->objUser->addCategory($this->varUserData['id'], $idDictCategories);
        }
        
        $this->view->data = array(
            'categories' => $this->objUser->getCategories($this->varUserData['id'])
        );
	}
    
    function buycreditsAction(){
        $formPayment = new FormAccountBuycredits();
        
        if($this->_request->isPost()){
            if($formPayment->validate($this->_request->getPost())){
                if($formPayment->getValue('operator') == 1)
                    $this->_redirect('/payments/paypalpayment/package/'.$formPayment->getValue('package'));
                $this->objSystem->addAlert(1);
            }
        }
        
        $this->view->forms = array(
            'payment' => $formPayment
        );
    }
    
}
?>