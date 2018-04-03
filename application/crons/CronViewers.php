<?php

include "CronAbstract.php";

class Cron extends CronAbstract{
    
    private $port = '1111';
    private $host = '37.59.5.5';
    private $username = 'admin';
    private $password = 'p4ssw0rd';
    
    
    public function run(){
        for($i = 0; $i < 4; $i++){
            $time_start = microtime(true);
            
            $streams = $this->getActiveStreams();
            foreach($streams->data->children() as $stream){
                $viewers = $this->getStreamViewers((string)$stream, null);
                $this->setViewersNumber((string)$stream, $viewers);
            }
            
            $time_end = microtime(true);
            echo "SCRIPT TIME: ".($time_end - $time_start)." seconds / STREAMS: ".count($streams->data->children())."\n";
            
            sleep(15);
        }
    }
    
    public function getData($parFunctionName, $parUrlParams){
        $parUrlParams['appInst'] = 'live';
        $parUrlParams['auser'] = $this->username;
        $parUrlParams['apswd'] = $this->password;
        
        $url = 'http://'.$this->host.':'.$this->port;
        $url.= '/admin/'.$parFunctionName;
        $url.= '?'.http_build_query($parUrlParams);
        
        return simplexml_load_file($url);
    }
    
    public function getActiveStreams(){
        return $this->getData('getLiveStreams');
    }
    
    public function getStreamViewers($parStreamId){
        $urlParams = array('stream' => $parStreamId);
        $xml = $this->getData('getLiveStreamStats', $urlParams);
        return count($xml->data->subscribers->children());
    }

    private function setViewersNumber($code, $value){
        $data['viewers'] = $value;
        $where['code = ?'] = $code;
        
        if(substr($code, 0, 3) == 'pub')
            $this->objDB->update('shows_public', $data, $where);
        elseif(substr($code, 0, 3) == 'prv')
            $this->objDB->update('shows_private', $data, $where);
        elseif(substr($code, 0, 3) == 'gru')
            $this->objDB->update('shows_group', $data, $where);
    }
    
}

$objCron = new Cron();
$objCron->run();

?>