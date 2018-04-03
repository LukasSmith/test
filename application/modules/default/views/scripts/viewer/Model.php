<?php

Zend_Loader::loadClass('Zend_Paginator');
Zend_Loader::loadClass('Zend_Paginator_Adapter_Array');

class Custom_Model {
    
    public $objDB;
    
    public function __construct(){
        $this->objDB = Custom_Db::getHandler();
    }

    protected function select($select, $fetchAll = true){
        $query = $this->objDB->query($select);
        if($fetchAll === true)
            return $query->fetchAll();
        return $query->fetch();
    }
    
    protected function selectSQL($sql, $sqlParams = null, $fetchAll = true){
        $query = $this->objDB->query($sql, $sqlParams);
        if($fetchAll === true)
            return $query->fetchAll();
        return $query->fetch();
    }

    protected function paginate($parData, $parPageNumber, $parPageItems, $parPageRange = 11){
        $objPaginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($parData)); 
        $objPaginator->setCurrentPageNumber($parPageNumber);
        $objPaginator->setItemCountPerPage($parPageItems);
        $objPaginator->setPageRange($parPageRange);
        
        return $objPaginator;
    }
    
    public function validateParams(){
        $functionParams = func_get_args();
        
        $functionParams[0] = $this->filterParams($functionParams[0]);
        foreach(array_slice($functionParams, 1) as $param){
            if(!strlen($functionParams[0][$param]) > 0)
                throw new Exception('Missing param: '.$param);
        }
        
        return $functionParams[0];
    }
    
    public function validateParamsList(){
        $params = $this->filterParams(func_get_args());
        foreach($params as $key => $value)
            if(!strlen($params[$key]) > 0)
                throw new Exception('Missing param: '.$key);
        return $params;
    }
    
    public function filterParams($params){
        foreach($params as $key => $value){
            if(is_array($value)){
                $params[$key] = $this->filterParams($params[$key]);
            }elseif(!strlen($value) > 0){
                $params[$key] = null;
            }   
        }
        
        return $params;
    }
    
    public function setGhost($table, $value, $column = 'id', $ghost = true){
        $this->objDB->update($table, array('ghost' => $ghost), array($column.' = ?' => $value));
    }
    
}

?>
