<?php

error_reporting(E_ALL^E_NOTICE^E_WARNING|E_STRICT);
date_default_timezone_set('Europe/Warsaw');
ini_set('display_errors', 1);

set_error_handler('customErrorHandler');

define('APPLICATION_ENV', 'production');

set_include_path('.'.PATH_SEPARATOR.getcwd()
                    .PATH_SEPARATOR.getcwd().'/library/'
                    .PATH_SEPARATOR.getcwd().'/application/'
                    .PATH_SEPARATOR.getcwd().'/application/models/'
                    .PATH_SEPARATOR.getcwd().'/application/modules/default/forms/'
                    .PATH_SEPARATOR.get_include_path());

try{        

    include "Zend/Loader.php";
    
    Zend_Loader::loadClass('Custom_Db');
    Zend_Loader::loadClass('Zend_Layout');
    Zend_Loader::loadClass('Zend_Session');
    Zend_Loader::loadClass('Zend_Registry');
    Zend_Loader::loadClass('Zend_Config_Ini');
    Zend_Loader::loadClass('Zend_Controller_Front');
	    
    $objMainConfig = new Zend_Config_Ini('application/configs/config.ini', APPLICATION_ENV);
    $objDB = Custom_Db::getHandler($objMainConfig->db->adapter, $objMainConfig->db->params);  
    $objDB->query('SET search_path TO public, dictionaries;');
    
    Zend_Registry::getInstance()->set('objDB', $objDB);
    Zend_Registry::getInstance()->set('objMainConfig', $objMainConfig);
    
    Zend_Session::start();
    Zend_Layout::startMvc();
    
    $frontController = Zend_Controller_Front::getInstance();
    $frontController->throwExceptions(true);
    //$frontController->setParam('prefixDefaultModule', true);
    $frontController->setControllerDirectory($objMainConfig->path->controllers->toArray());
    $frontController->setDefaultModule('default');
    $frontController->setModuleControllerDirectoryName('controllers');
    $frontController->addModuleDirectory('application/modules');
    $frontController->setParam('useDefaultControllerAlways', true);
    $frontController->setDefaultControllerName('index');
    $frontController->dispatch();
    
}catch(Exception $e){
    echo $e->getMessage();
    echo $e->getTraceAsString();
    error_log(date('Y-m-d H:i:s').' : '.$e->getMessage()."\nTRACE:".$e->getTraceAsString()."\n", 3, 'application/logs/core.log');
    //header('Location: /error/error');
}

function debug($message){
    error_log(date('Y-m-d H:i:s').' : '.$message."\n", 3, 'application/logs/core.log');
}

function customErrorHandler($errno, $errstr, $errfile, $errline){
    $error = ">>>>>>>>>>>>>>>>>>>>\n";
    $error.= "DATE: ".date('Y-m-d H:i:s')."\n";
    $error.= "ERROR NO: ".str_replace("\n", " ", $errno)."\n";
    $error.= "ERROR: ".str_replace("\n", " ", $errstr)."\n";
    $error.= "FILE: ".str_replace("\n", " ", $errfile)."\n";
    $error.= "LINE: ".str_replace("\n", " ", $errline)."\n";
    $error.= "<<<<<<<<<<<<<<<<<<<<\n";

    error_log($error."\n", 3, "application/logs/errors.log");
}

?>