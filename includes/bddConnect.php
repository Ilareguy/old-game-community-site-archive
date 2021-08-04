<?php

if(!defined('ERROR_INI_SET')){
    if(substr($_SERVER['HTTP_HOST'], -3, 3) == 'org')
        ini_set('error_reporting', 0);
    else
        ini_set('error_reporting', E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
    define('ERROR_INI_SET', 1);
}

require_once('_BDDCONNECT.class.php');

if(!isset($_SESSION)){
    session_start();
	
    if(!isset($_SESSION['connecte'])){
        $_SESSION['connecte'] = false;
    }
	if(!isset($_SESSION['id'])){
        $_SESSION['id'] = -1;
    }
	/*require_once(__ROOT__.'includes/comptes.php');
	if($_SESSION['id'] > 0 || !compteIdExists($_SESSION['id'])){
		// Compte inexistant!
		$_SESSION['connecte'] = false;
		$_SESSION['id'] = -1;
    }*/
	
}

if(!isset($bdd)){
    if(!_BDDCONNECT::$initd){
        _BDDCONNECT::init();
    }
    $bdd = _BDDCONNECT::$bdd;
}
?>