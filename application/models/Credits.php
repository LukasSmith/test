<?php

Zend_Loader::loadClass('Custom_Model');

class Credits extends Custom_Model{

    public function getBalance($id_users){
        $params = $this->validateParamsList($id_users);
        
        $select = $this->objDB->select()
            ->from(array('crd' => 'credits'), array('*'))
            ->where('crd.id_users = ?', $params[0])
            ->where('crd.ghost = ?', 'FALSE');

        return $this->select($select, false);
    }
    
    public function getTransactions($parIdUsers, $parPageNumber){
        $params = $this->validateParamsList($parIdUsers);
        
        $select = $this->objDB->select()
            ->from(array('crdt' => 'credits_transactions'), array('added', 'id', 'credits', 'balance_before', 'balance_after', 'description'))
            ->join(array('dctt' => 'dict_transactions_types'), 'dctt.id = crdt.id_dict_transactions_types', array('type' => 'description'))
            ->join(array('dcts' => 'dict_transactions_sort'), 'dcts.id = crdt.id_dict_transactions_sort', array('sort' => 'description'))
            ->join(array('dctst' => 'dict_transactions_statuses'), 'dctst.id = crdt.id_dict_transactions_statuses', array('status' => 'description'))
            ->where('crdt.id_users = ?', $params[0])
            ->where('crdt.ghost = ?', 'FALSE')
            ->order(array('crdt.id DESC'));

        $result = $this->select($select);
        return $this->paginate($result, $parPageNumber, 15, 11);
    }
    
    public function addTransaction($parIdUsers, $parCredits, $parIdDictTransactionsTypes, $parIdDictTransactionsSort, $parIdDictTransactionsStatuses){
        $params = $this->validateParamsList($parIdUsers, $parCredits, $parIdDictTransactionsTypes, $parIdDictTransactionsSort, $parIdDictTransactionsStatuses);
        
        $data = array(
            'credits' => $params[1],
            'id_users' => $params[0], 
            'id_dict_transactions_sort' => $params[3], 
            'id_dict_transactions_types' => $params[2], 
            'id_dict_transactions_statuses' => $params[4]
        );
        
        $this->objDB->insert('credits_transactions', $data);  
        return $this->objDB->lastInsertId('credits_transactions', 'id');
    }
    
    public function addTransactionPayPal($parIdUsers, $parIdDictPackages, $parToken, $parCorrelation, $parErrors, $parVersion, $parBuild){
        $params = $this->validateParamsList($parIdUsers, $parIdDictPackages, $parToken, $parCorrelation, $parVersion, $parBuild);
        
        $data = array(
            'id_users' => $params[0], 
            'id_dict_packages' => $params[1], 
            'token' => $params[2], 
            'correlation' => $params[3], 
            'errors' => $parErrors, 
            'version' => $params[4], 
            'build' => $params[5]
        );
        
        $this->objDB->insert('credits_transactions_paypal', $data);  
        return $this->objDB->lastInsertId('credits_transactions_paypal', 'id');
    }
    
    public function getTransactionPayPal($parIdUsers, $parToken){
        $params = $this->validateParamsList($parIdUsers, $parToken);
        
        $select = $this->objDB->select()
            ->from(array('crdtp' => 'credits_transactions_paypal'), array('*'))
            ->join(array('dctp' => 'dict_packages'), 'dctp.id = crdtp.id_dict_packages', array('credits'))
            ->where('crdtp.id_users = ?', $params[0])
            ->where('crdtp.token = ?', $params[1])
            ->where('crdtp.ghost = ?', 'FALSE');

        return $this->select($select, false);
    }
    
    public function updTransactionPayPal($parIdCreditsTransactionsPaypal, $parIdUsers, $varIdCreditsTransactions, $parPayer, $parPayerid, $parPayerFirstname, $parPayerLastname, $parPayerAddress){
        $params = $this->validateParamsList($parIdCreditsTransactionsPaypal, $parIdUsers, $varIdCreditsTransactions, $parPayer, $parPayerid, $parPayerFirstname, $parPayerLastname, $parPayerAddress);
        
        $data = array(
            'id_credits_transactions' => $params[2], 
            'payer' => $params[3], 
            'payerid' => $params[4], 
            'payerfirstname' => $params[5], 
            'payerlastname' => $params[6], 
            'payeraddress' => $params[7]
        );
        
        $this->objDB->update('credits_transactions_paypal', $data, array('id = ?' => $params[0], 'id_users = ?' => $params[1]));  
    }
    
    /*public function addPrivateReturn($id_users, $credits){
        $params = $this->validateParamsList($id_users, $credits);
        
        $data = array(
            'credits' => $params[1],
            'id_users' => $params[0], 
            'id_dict_transactions_sort' => 1, 
            'id_dict_transactions_types' => 4, 
            'id_dict_transactions_statuses' => 2
        );
        
        $this->objDB->insert('credits_transactions', $data);       
        return $this->objDB->lastInsertId('credits_transactions', 'id');
    }
    
    public function addGroupReturn($id_users, $credits){
        $params = $this->validateParamsList($id_users, $credits);
        
        $data = array(
            'credits' => $params[1],
            'id_users' => $params[0], 
            'id_dict_transactions_sort' => 1, 
            'id_dict_transactions_types' => 5, 
            'id_dict_transactions_statuses' => 2
        );
        
        $this->objDB->insert('credits_transactions', $data);
        return $this->objDB->lastInsertId('credits_transactions', 'id');
    }*/
    
}

?>