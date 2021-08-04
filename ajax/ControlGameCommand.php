<?php
/*
* Similaire à "SendServerCommand.php", mais cette page est utilisée pour
* les commandes 'stop', 'start' et 'restart'.
*/
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(dirname(__FILE__)) . '/');
session_start();
    
require_once(__ROOT__.'includes/droits.php');
    
$OK = false;
$Command;
if(isset($_GET['command'])){
    $Command = $_GET['command'];
    $OK = true;
}else if(isset($_POST['command'])){
    $Command = $_POST['command'];
    $OK = true;
}

if(!isset($_SESSION['id'])){
    echo 'Il manque l\'ID de la session';
}else if(!droit($_SESSION['id'], 'SendGameServerCommand')){
    echo 'Pas le droit pour : SendGameServerCommand';
}

if($OK){
          
    $curl = curl_init();

    $curlOptions = array(
        CURLOPT_URL => 'https://game.kerpluncgaming.com/includes/process.php?gamecontrol=true&noheader=true&mode=' . $Command . '&gid=13121',
        CURLOPT_POST => false,
        CURLOPT_HEADER => FALSE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false
    );
    
    curl_setopt_array ( $curl, $curlOptions );
    $return = curl_exec($curl);
    
    if($return === false){
        trigger_error('Erreur curl : ' . curl_error($curl), E_USER_WARNING);
    }
    
    echo $return;
    
    curl_close($curl);
    
}else{
    echo 'Erreur';
}

?>