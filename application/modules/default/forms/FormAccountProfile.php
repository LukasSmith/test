<?php

Zend_Loader::loadClass('Custom_Form');

class FormAccountProfile extends Custom_Form {

    public function __construct($parUserProfile){
        parent::__construct();
        

        $builds = $this->objDictionary->builds->get();
        $zodiac = $this->objDictionary->zodiac->get();
        $genders = $this->objDictionary->genders->get();
        $countries = $this->objDictionary->countries->get();
        $cup_sizes = $this->objDictionary->cup_sizes->get();
        $eye_colors = $this->objDictionary->eye_colors->get();
        $ethnicities = $this->objDictionary->ethnicities->get();
        $hair_colors = $this->objDictionary->hair_colors->get();
        $pubic_hairs = $this->objDictionary->pubic_hairs->get();
        $sexual_preferences = $this->objDictionary->sexual_preferences->get();

        $this->addElement('text', 'username', array(
            'value' => $parUserProfile['nick']
        ));
        
        $this->addElement('text', 'email', array(
            'value' => $parUserProfile['email']
        ));
        
        $this->addElement('select', 'country', array(
            'multiOptions' => $this->getOptions($countries, 'id', 'description'),
            'value' => $parUserProfile['id_dict_countries']
        ));
        
        $this->addElement('select', 'gender', array(
            'multiOptions' => $this->getOptions($genders, 'id', 'description'),
            'value' => $parUserProfile['id_dict_genders']
        ));
        
        $this->addElement('select', 'sexual_preference', array(
            'multiOptions' => $this->getOptions($sexual_preferences, 'id', 'description'),
            'value' => $parUserProfile['id_dict_sexual_preferences']
        ));
        
        $this->addElement('select', 'zodiac', array(
            'multiOptions' => $this->getOptions($zodiac, 'id', 'description'),
            'value' => $parUserProfile['id_dict_zodiac']
        ));
        
        $this->addElement('select', 'hair_color', array(
            'multiOptions' => $this->getOptions($hair_colors, 'id', 'description'),
            'value' => $parUserProfile['id_dict_hair_colors']
        ));
        
        $this->addElement('select', 'eye_color', array(
            'multiOptions' => $this->getOptions($eye_colors, 'id', 'description'),
            'value' => $parUserProfile['id_dict_eye_colors']
        ));
        
        $this->addElement('select', 'build', array(
            'multiOptions' => $this->getOptions($builds, 'id', 'description'),
            'value' => $parUserProfile['id_dict_builds']
        ));
        
        $this->addElement('select', 'ethnicity', array(
            'multiOptions' => $this->getOptions($ethnicities, 'id', 'description'),
            'value' => $parUserProfile['id_dict_ethnicities']
        ));
        
        $this->addElement('select', 'cup_size', array(
            'multiOptions' => $this->getOptions($cup_sizes, 'id', 'description'),
            'value' => $parUserProfile['id_dict_cup_sizes']
        ));
        
        $this->addElement('select', 'pubic_hair', array(
            'multiOptions' => $this->getOptions($pubic_hairs, 'id', 'description'),
            'value' => $parUserProfile['id_dict_pubic_hairs']
        ));

    }
    
}
?>