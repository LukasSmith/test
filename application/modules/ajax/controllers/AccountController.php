<?php

Zend_Loader::loadClass('Gallery');
Zend_Loader::loadClass('Custom_Controller_Ajax');

class Ajax_AccountController extends Custom_Controller_Ajax {

    private $objGallery;
    
	public function init() {
		parent::init();           

        $this->objGallery = new Gallery();
	}
    
    function addimageAction(){
        if(isset($GLOBALS["HTTP_RAW_POST_DATA"])){
            $fileName = md5($this->varUserData['nick'].time()).".jpg";
            $fileDire = $this->objGallery->getDirectory($this->varUserData['nick']);
            if(file_put_contents($fileDire."/".$fileName, $GLOBALS["HTTP_RAW_POST_DATA"])){
                if($this->objGallery->addImage($this->varUserData['id'], $fileName))
                    $this->objGallery->createThumbnail($fileDire.'/'.$fileName);
            }
        }
    }
    
}
?>