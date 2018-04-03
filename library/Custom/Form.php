<?php

Zend_Loader::loadClass('System');
Zend_Loader::loadClass('Dictionary');

Zend_Loader::loadClass('Zend_Form');
Zend_Loader::loadClass('Zend_Form_Element_File');
Zend_Loader::loadClass('Zend_Form_Element_Text');
Zend_Loader::loadClass('Zend_Form_Element_Textarea');
Zend_Loader::loadClass('Zend_Form_Element_Password');
Zend_Loader::loadClass('Zend_Form_Element_Radio');
Zend_Loader::loadClass('Zend_Form_Element_Submit');
Zend_Loader::loadClass('Zend_Form_Element_Select');
Zend_Loader::loadClass('Zend_Form_Element_MultiCheckbox');

class Custom_Form extends Zend_Form {
    
    protected $objSystem;
    protected $objDictionary;
    
    public function __construct($options = null) {
        parent::__construct($options);
        
        $this->objSystem = System::getHandler();
        $this->objDictionary = Dictionary::getHandler();
    }

    public function validate($data){
        if($this->isValid($data))
            return true;

        $this->objSystem->addAlert(1);
        
        return false;
    }
    
    public function getItem($name, $label = null){
        $element = $this->getElement($name);
        $messages = $element->getMessages();
        $errors = $element->getErrors();
        
        if($element instanceof Zend_Form_Element_Text){
            $result = '<div class="input">'.trim($element->renderViewHelper()).'</div>';
        }elseif($element instanceof Zend_Form_Element_Password){
            $result = '<div class="input">'.trim($element->renderViewHelper()).'</div>';
        }elseif($element instanceof Zend_Form_Element_Select){
            $result = '<div class="select">'.trim($element->renderViewHelper()).'</div>';
        }
        
        if(!empty($errors)){     
            $result = '<div class="error">'.$result.'</div>';
            //$result.= '<div class="errors">';
            //foreach($messages as $message)
                //$result.= trim($message);
            //$result.= '</div>';
        }
        
        return $result;
    }
    
    public function getOptions($params, $value, $description, $firstEmpty = true){
        if($firstEmpty === true)
            $result[''] = '-----';
        foreach($params as $param)
            $result[$param->id] = $param->description;
        return (array)$result;
    }

}

?>




<option value="Dominant">Dominant</option>
<option value="Domme">Domme</option>
<option value="Switch">Switch</option>
<option value="submissive">submissive</option>
<option value="Master">Master</option>
<option value="Mistress">Mistress</option>
<option value="slave">slave</option>
<option value="kajira">kajira</option>
<option value="kajirus">kajirus</option>
<option value="Top">Top</option>
<option value="Bottom">Bottom</option>
<option value="Sadist">Sadist</option>
<option value="Masochist">Masochist</option>
<option value="Sadomasochist">Sadomasochist</option>
<option value="Kinkster">Kinkster</option>
<option value="Fetishist">Fetishist</option>
<option value="Swinger">Swinger</option>
<option value="Hedonist">Hedonist</option>
<option value="Exhibitionist">Exhibitionist</option>
<option value="Voyeur">Voyeur</option>
<option value="Sensualist">Sensualist</option>
<option value="Princess">Princess</option>
<option value="Slut">Slut</option>
<option value="Doll">Doll</option>
<option value="sissy">sissy</option>
<option value="Rigger">Rigger</option>
<option value="Rope Top">Rope Top</option>
<option value="Rope Bottom">Rope Bottom</option>
<option value="Rope Bunny">Rope Bunny</option>
<option value="Spanko">Spanko</option>
<option value="Spanker">Spanker</option>
<option value="Spankee">Spankee</option>
<option value="Furry">Furry</option>
<option value="Leather Man">Leather Man</option>
<option value="Leather Woman">Leather Woman</option>
<option value="Leather Daddy">Leather Daddy</option>
<option value="Leather Top">Leather Top</option>
<option value="Leather bottom">Leather bottom</option>
<option value="Leather boy">Leather boy</option>
<option value="Leather girl">Leather girl</option>
<option value="Leather Boi">Leather Boi</option>
<option value="Bootblack">Bootblack</option>
<option value="Primal">Primal</option>
<option value="Primal Predator">Primal Predator</option>
<option value="Primal Prey">Primal Prey</option>
<option value="Bull">Bull</option>
<option value="cuckold">cuckold</option>
<option value="cuckquean">cuckquean</option>
<option value="Ageplayer">Ageplayer</option>
<option value="Daddy">Daddy</option>
<option value="Mommy">Mommy</option>
<option value="Big">Big</option>
<option value="Middle">Middle</option>
<option value="little">little</option>
<option value="brat">brat</option>
<option value="babygirl">babygirl</option>
<option value="babyboy">babyboy</option>
<option value="pet">pet</option>
<option value="kitten">kitten</option>
<option value="pup">pup</option>
<option value="pony">pony</option>
<option value="Evolving">Evolving</option>
<option value="Exploring">Exploring</option>
<option value="Vanilla">Vanilla</option>
<option value="Undecided">Undecided</option>

