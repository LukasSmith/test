<?php

Zend_Loader::loadClass('Custom_Model');

class User extends Custom_Model{
    
    public static $handler;
    
    public static function getHandler(){
        if(!isset(self::$handler))
            self::$handler = new User();
        return self::$handler;
    }
 
    public function addUser($params){
        $params = $this->validateParams($params, 'nick', 'email', 'password');
        
        $data = array(
            'nick' => $params['nick'],
            'email' => $params['email'],
            'password' => md5($params['password'])
        );
        
        $this->objDB->insert('users', $data);
        $id_users = $this->objDB->lastInsertId('users', 'id');
        
        $data = array(
            'id_users' => $id_users
        );
        
        $this->objDB->insert('users_profiles', $data);
        
        $data = array(
            'balance' => 1000,
            'id_users' => $id_users
        );
        
        $this->objDB->insert('credits', $data);
        
        return $id_users;
    }
    
    public function getUserByNick($nick){
        $params = $this->validateParams(array('nick' => $nick), 'nick');
        
        $select = $this->objDB->select()
            ->from(array('usr' => 'users'), array('id', 'nick', 'email'))
            ->where('usr.nick = ?', $params['nick']);
        
        return $this->select($select, false);
    }
    
    public function getUser($params){
        if(!empty($params['id_users'])){
            $select = $this->objDB->select()
                ->from(array('usr' => 'users'), array('*'))
                ->where('usr.id = ?', $params['id_users'])
                ->where('usr.ghost = FALSE');
            return $this->select($select, false);
        }elseif(!empty($params['nick'])){
            $select = $this->objDB->select()
                ->from(array('usr' => 'users'), array('*'))
                ->where('usr.nick = ?', $params['nick'])
                ->where('usr.ghost = FALSE');
            return $this->select($select, false);
        }
    }
    
    public function getProfile($parIdUsers){
        $params = $this->validateParamsList($parIdUsers);
        
        $select = $this->objDB->select()
            ->from(array('usr' => 'users'), array('*'))
            ->join(array('usrp' => 'users_profiles'), 'usrp.id_users = usr.id', array('*'))
            ->where('usr.id = ?', $params[0])
            ->where('usr.ghost = FALSE');
        
        return $this->select($select, false);
    }
    
    public function setProfile($parParams){
        $params = $this->validateParams($parParams, 'id_users');

        $data = array(
            'id_dict_countries' => $params['id_dict_countries'],
            'id_dict_genders' => $params['id_dict_genders'],
            'id_dict_sexual_preferences' => $params['id_dict_sexual_preferences'],
            'id_dict_zodiac' => $params['id_dict_zodiac'],
            'id_dict_hair_colors' => $params['id_dict_hair_colors'],
            'id_dict_eye_colors' => $params['id_dict_eye_colors'],
            'id_dict_builds' => $params['id_dict_builds'],
            'id_dict_ethnicities' => $params['id_dict_ethnicities'],
            'id_dict_cup_sizes' => $params['id_dict_cup_sizes'],
            'id_dict_pubic_hairs' => $params['id_dict_pubic_hairs']
        );

        $this->objDB->update('users_profiles', $data, array('id_users = ?' => $params['id_users']));
    }
    
    public function setPassword($parIdUsers, $parOldPassword, $parNewPassword){
        $params = $this->validateParamsList($parIdUsers, $parOldPassword, $parNewPassword);
        $where = array('id = ?' => $params[0], 'password = ?' => md5($params[1]));
        return $this->objDB->update('users', array('password' => md5($params[2])), $where);
    }
    
    public function getBroadcastingUsers($parPageNumber){
        $select = $this->objDB->select()
            ->from(array('usr' => 'users'), array('*', 'is_new' => new Zend_Db_Expr("usr.added > now() - '14 days'::interval")))
            ->join(array('usri' => 'users_images'), 'usri.id_users = usr.id', array('filename'))
            ->join(array('shw' => 'shows_public'), 'shw.id_users = usr.id', array())
            ->where('shw.stop_datetime IS NULL')
            ->where('usri.is_main_image = TRUE')
            ->where('usr.ghost = FALSE')
            ->order(array('usr.id ASC'));

        $result = $this->select($select);
        return $this->paginate($result, $parPageNumber, 15, 11);
    }
    
    public function getBroadcastingNewUsers($parPageNumber){
        $select = $this->objDB->select()
            ->from(array('usr' => 'users'), array('*'))
            ->join(array('usri' => 'users_images'), 'usri.id_users = usr.id', array('filename'))
            ->join(array('shw' => 'shows_public'), 'shw.id_users = usr.id', array())
            ->where("usr.added > now() - '14 days'::interval")
            ->where('shw.stop_datetime IS NULL')
            ->where('usri.is_main_image = TRUE')
            ->where('usr.ghost = FALSE')
            ->order(array('usr.id ASC'));
        
        $result = $this->select($select);
        return $this->paginate($result, $parPageNumber, 15, 11);
    }
    
    public function getBroadcastingCategoryUsers($id_dict_categories, $parPageNumber){
        $select = $this->objDB->select()
            ->from(array('usr' => 'users'), array('*', 'is_new' => new Zend_Db_Expr("usr.added > now() - '14 days'::interval")))
            ->join(array('usri' => 'users_images'), 'usri.id_users = usr.id', array('filename'))
            ->join(array('cat' => 'users_categories'), 'cat.id_users = usr.id', array())
            ->join(array('shw' => 'shows_public'), 'shw.id_users = usr.id', array())
            ->where('cat.id_dict_categories = ?', $id_dict_categories)
            ->where('shw.stop_datetime IS NULL')
            ->where('usri.is_main_image = TRUE')
            ->where('usr.ghost = FALSE')
            ->order(array('usr.id ASC'));
        
        $result = $this->select($select);
        return $this->paginate($result, $parPageNumber, 15, 11);
    }
    
    public function getBroadcastingCategoryUsersNumber($id_dict_categories){
        $select = $this->objDB->select()
            ->from(array('usr' => 'users'), array('users' => 'COUNT(*)'))
            ->join(array('usri' => 'users_images'), 'usri.id_users = usr.id', array())
            ->join(array('cat' => 'users_categories'), 'cat.id_users = usr.id', array())
            ->join(array('shw' => 'shows_public'), 'shw.id_users = usr.id', array())
            ->where('cat.id_dict_categories = ?', $id_dict_categories)
            ->where('shw.stop_datetime IS NULL')
            ->where('usri.is_main_image = TRUE')
            ->where('usr.ghost = FALSE');
        
        $result = $this->select($select, false);
        return $result['users'];
    }
    
    public function getBroadcastingTopUsers($parPageNumber){
        $select = $this->objDB->select()
            ->from(array('usr' => 'users'), array('*', 'is_new' => new Zend_Db_Expr("usr.added > now() - '14 days'::interval")))
            ->join(array('usri' => 'users_images'), 'usri.id_users = usr.id', array('filename'))
            ->join(array('shw' => 'shows_public'), 'shw.id_users = usr.id', array())
            ->where('shw.stop_datetime IS NULL')
            ->where('usri.is_main_image = TRUE')
            ->where('usr.ghost = FALSE')
            ->order(array('shw.viewers DESC', 'usr.id'));
        
        $result = $this->select($select);
        return $this->paginate($result, $parPageNumber, 15, 11);
    }
    
    public function addCategory($parIdUsers, $parIdDictCategories){
        $params = $this->validateParamsList($parIdUsers, $parIdDictCategories);

        $data = array(
            'id_users' => $params[0],
            'id_dict_categories' => $params[1]
        );
        
        $this->objDB->insert('users_categories', $data);
        return $this->objDB->lastInsertId('users_categories', 'id');
    }
    
    public function getCategories($parIdUsers){
        $params = $this->validateParamsList($parIdUsers);
        
        $select = $this->objDB->select()->distinct()
            ->from(array('dctc' => 'dict_categories'), array('id', 'description'))
            ->joinLeft(array('usrc' => 'users_categories'), 'usrc.id_dict_categories = dctc.id', array('id_users_categories' => 'id'))
            ->where('usrc.id_users = ? OR id_users IS NULL', $params[0])
            ->order(array('dctc.description ASC'));
        
        return $this->select($select);
    }
    
    public function clrCategories($id_users){
        $params = $this->validateParamsList($id_users);
        $this->objDB->delete('users_categories', array('id_users = ?' => $params[0]));
    }
    
}

?>