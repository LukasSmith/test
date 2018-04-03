<?php

Zend_Loader::loadClass('Custom_Model');
Zend_Loader::loadClass('Dictionary_Container');

class Dictionary extends Custom_Model{

    public static $handler;
    
    //VARIABLES
    public $varMapper;
    public $varDictionaries;
    public $varPrefix = 'dict_';
    public $varSchema = 'dictionaries';
    
    public static function getHandler(){
        if(!isset(self::$handler))
            self::$handler = new Dictionary();
        return self::$handler;
    }
    
    public function __construct(){
        parent::__construct();
        $this->setDictionaries();
    }
    
    private function getDictionariesNames(){
        $select = $this->objDB->select()
            ->from(array('inf' => 'information_schema.tables'), array('name' => 'table_name'))
            ->where('inf.table_schema = ?', $this->varSchema)
            ->where('inf.table_type = ?', 'BASE TABLE');
        
        return $this->select($select);
    }
    
    public function setPrefix($parPrefix){
        $this->varPrefix = $parPrefix;
    }
    
    public function setSchema($parSchema){
        $this->varSchema = $parSchema;
    }
    
    private function setDictionaries(){
        $dictionaries = $this->getDictionariesNames();
        foreach($dictionaries as $value){
            $dictionary = $this->makeDictionary($value['name']);
            $this->varDictionaries[$value['name']] = $dictionary;
        }
    }
    
    private function getDictionaryData($parName){
        $select = $this->objDB->select()
            ->from(array('dct' => $this->varSchema.'.'.$parName), array('*'))
            ->order(array('dct.description ASC'));
        
        return $this->select($select);
    }
    
    private function makeMapper(&$parDictionary, &$parItem){
        $variables = array_keys(get_object_vars($parItem));
        foreach($variables as $variable){
            $value = strtolower($parItem->$variable);
            $parDictionary->varMapper[$variable][$value] = $parItem;
        }
    }
    
    private function makeDictionary($parName){
        $dictionary = new Dictionary_Container();
        $data = $this->getDictionaryData($parName);
        foreach($data as $key => $value){
            $dictionary->varItems[$key] = $this->makeItem($value);
            $this->makeMapper($dictionary, $dictionary->varItems[$key]);
        }
        
        return $dictionary;
    }
    
    private function makeItem($parData){
        $item = new stdClass();
        foreach($parData as $key => $value)
            $item->$key = $value;
        return $item;
    }
    
    public function __get($parName){
        $name = $this->varPrefix.$parName;
        return $this->varDictionaries[$name];
    }
    
    /*public function getCountries(){
        $select = $this->objDB->select()
            ->from(array('dictionaries.dict_countries'), array('id', 'code', 'description'))
            ->where('ghost = ?', 'FALSE');
        
        return $this->select($select);
    }
    
    public function getCategories(){
        $select = $this->objDB->select()
            ->from(array('dctc' => 'dictionaries.dict_categories'), array('id', 'description'))
            ->joinLeft(array('usrc' => 'users_categories'), 'usrc.id_dict_categories = dctc.id', array('models' => 'COUNT(*)'))
            ->group('dctc.id')
            ->group('dctc.description')
            ->where('dctc.ghost = ?', 'FALSE')
            ->order(array('dctc.description ASC'));
        
        return $this->select($select);
    }
    
    public function getCategory($id){
        $select = $this->objDB->select()
            ->from(array('dictionaries.dict_categories'), array('id', 'description'))
            ->where('ghost = ?', 'FALSE')
            ->where('id = ?', $id);
        
        return $this->select($select, false);
    }
    
    public function getShowsTypes(){
        $select = $this->objDB->select()
            ->from(array('dictionaries.dict_shows_types'), array('id', 'description'))
            ->where('ghost = ?', 'FALSE');
        
        return $this->select($select);
    }*/

}

?>