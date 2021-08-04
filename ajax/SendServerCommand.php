<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(dirname(__FILE__)) . '/');
session_start();
    
require_once(__ROOT__.'includes/droits.php');
require_once(__ROOT__.'includes/GameServer.php');
    
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
    SendServerCommand($Command);
}else{
    echo 'Erreur';
}

?>