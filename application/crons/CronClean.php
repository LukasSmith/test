<?php

include "CronAbstract.php";

class Cron extends CronAbstract{
    
    public function run(){
        for($i = 0; $i < 3; $i++){
            $time_start = microtime(true);
            
            $select = $this->objDB->select()
                ->from(array('shw' => 'shows_public'), array('*'))
                ->where('shw.ping_datetime < ?', date('Y-m-d H:i:s', time() - 10))
                ->where('shw.stop_datetime IS NULL')
                ->where('shw.ghost = ?', 'FALSE');

            $shows = $this->select($select);

            foreach($shows as $show){
                $this->objDB->beginTransaction();

                $this->objDB->update('shows_public', array('stop_datetime' => date('c')), array('id = ?' => $show['id']));

                $select = $this->objDB->select()
                    ->from(array('shwp' => 'shows_private'), array('*'))
                    ->where('shwp.id_shows_public = ?', $show['id'])
                    ->where('shwp.stop_datetime IS NULL')
                    ->where('shwp.ghost = FALSE');

                if($private = $this->select($select, false)){
                    $this->objDB->update('shows_private', array('stop_datetime' => date('c')), array('id = ?' => $private['id']));

                    if(strtotime($private['end_datetime']) > time()){
                        $select = $this->objDB->select()
                            ->from(array('shwpp' => 'shows_private_participants'), array('*'))
                            ->join(array('shwpr' => 'shows_private_requests'), 'shwpr.id = shwpp.id_shows_private_requests', array('id_users', 'credits'))
                            ->where('shwpp.id_shows_private = ?', $private['id'])
                            ->where('shwpp.ghost = FALSE');

                        $participants = $this->select($select);

                        foreach($participants as $participant){
                            $select = $this->objDB->select()
                                ->from(array('crd' => 'credits'), array('*'))
                                ->where('crd.id_users = ?', $participant['id_users'])
                                ->where('crd.ghost = ?', 'FALSE');

                            $balance = $this->select($select, false);

                            $data = array(
                                'id_dict_transactions_sort' => 5, 
                                'id_dict_transactions_types' => 1,
                                'id_dict_transactions_statuses' => 2,
                                'credits' => $participant['credits'],
                                'id_users' => $participant['id_users']
                            );

                            $this->objDB->insert('credits_transactions', $data);

                            //$this->objDB->update('credits', array('balance' => $balance['balance'] + $participant['credits']), array('id = ?' => $balance['id']));
                            $this->objDB->update('shows_private_participants', array('is_charge_returned' => true), array('id = ?' => $participant['id']));
                        }
                    }
                }

                $select = $this->objDB->select()
                    ->from(array('shwg' => 'shows_group'), array('*'))
                    ->where('shwg.id_shows_public = ?', $show['id'])
                    ->where('shwg.stop_datetime IS NULL')
                    ->where('shwg.ghost = FALSE');

                if($group = $this->select($select, false)){
                    $this->objDB->update('shows_group', array('stop_datetime' => date('c')), array('id = ?' => $group['id']));

                    if(strtotime($group['end_datetime']) > time()){
                        $select = $this->objDB->select()
                            ->from(array('shwgp' => 'shows_group_participants'), array('*'))
                            ->join(array('shwgr' => 'shows_group_requests'), 'shwgr.id = shwgp.id_shows_group_requests', array('id_users', 'credits'))
                            ->where('shwgp.id_shows_group = ?', $group['id'])
                            ->where('shwgp.ghost = FALSE');

                        $participants = $this->select($select);

                        foreach($participants as $participant){
                            $select = $this->objDB->select()
                                ->from(array('crd' => 'credits'), array('*'))
                                ->where('crd.id_users = ?', $participant['id_users'])
                                ->where('crd.ghost = ?', 'FALSE');

                            $balance = $this->select($select, false);

                            $data = array(
                                'id_dict_transactions_sort' => 5, 
                                'id_dict_transactions_types' => 1,
                                'id_dict_transactions_statuses' => 2,
                                'credits' => $participant['credits'],
                                'id_users' => $participant['id_users']
                            );

                            $this->objDB->insert('credits_transactions', $data);

                            //$this->objDB->update('credits', array('balance' => $balance['balance'] + $participant['credits']), array('id = ?' => $balance['id']));
                            $this->objDB->update('shows_group_participants', array('is_charge_returned' => true), array('id = ?' => $participant['id']));
                        }
                    }
                }

                $select = $this->objDB->select()
                    ->from(array('shwpr' => 'shows_private_requests'), array('*'))
                    ->where('shwpr.id_shows_public = ?', $show['id'])
                    ->where('shwpr.is_rejected = FALSE')
                    ->where('shwpr.is_active = TRUE')
                    ->where('shwpr.ghost = FALSE');

                $requests = $this->select($select);
                foreach($requests as $request){
                    $this->objDB->update('shows_private_requests', array('is_active' => 'FALSE'), array('id = ?' => $request['id']));

                    $select = $this->objDB->select()
                        ->from(array('crd' => 'credits'), array('*'))
                        ->where('crd.id_users = ?', $request['id_users'])
                        ->where('crd.ghost = ?', 'FALSE');

                    $balance = $this->select($select, false);

                    $data = array(
                        'id_dict_transactions_sort' => 4, 
                        'id_dict_transactions_types' => 1,
                        'id_dict_transactions_statuses' => 2,
                        'credits' => $request['credits'],
                        'id_users' => $request['id_users']
                    );

                    $this->objDB->insert('credits_transactions', $data);

                    //$this->objDB->update('credits', array('balance' => $balance['balance'] + $request['credits']), array('id = ?' => $balance['id']));
                }

                $select = $this->objDB->select()
                    ->from(array('shwgr' => 'shows_group_requests'), array('*'))
                    ->where('shwgr.id_shows_public = ?', $show['id'])
                    ->where('shwgr.is_rejected = FALSE')
                    ->where('shwgr.is_active = TRUE')
                    ->where('shwgr.ghost = FALSE');

                $requests = $this->select($select);
                foreach($requests as $request){
                    $this->objDB->update('shows_group_requests', array('is_active' => 'FALSE'), array('id = ?' => $request['id']));

                    $select = $this->objDB->select()
                        ->from(array('crd' => 'credits'), array('*'))
                        ->where('crd.id_users = ?', $request['id_users'])
                        ->where('crd.ghost = ?', 'FALSE');

                    $balance = $this->select($select, false);

                    $data = array(
                        'id_dict_transactions_sort' => 5, 
                        'id_dict_transactions_types' => 1,
                        'id_dict_transactions_statuses' => 2,
                        'credits' => $request['credits'],
                        'id_users' => $request['id_users']
                    );

                    $this->objDB->insert('credits_transactions', $data);

                    //$this->objDB->update('credits', array('balance' => $balance['balance'] + $request['credits']), array('id = ?' => $balance['id']));
                }

                $this->objDB->commit();
            }
            
            $time_end = microtime(true);
            echo "SCRIPT TIME: ".($time_end - $time_start)." seconds / SHOWS: ".count($shows)."\n";
            
            sleep(20);
        }
    }
    
}

$objCron = new Cron();
$objCron->run();

?>