<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(dirname(__FILE__)) . '/');

/*
* Fonctions en rapport avec les informations générales du site
* (Site en maintenance, Paypal Sandbox ou non, etc.)
*/

function IsMaintenance(){
    /*
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('SELECT Value FROM system WHERE Field=?');
    $req->execute(array('Maintenance'));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        if($donnees['Value'] == '1' || strtolower($donnees['Value']) == 'true')
            return true;
        return false;
    }
    
    $req->closeCursor();
    return false;
}

function IsPaypalSandbox(){
    /*
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('SELECT Value FROM system WHERE Field=?');
    $req->execute(array('PaypalSandbox'));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        if($donnees['Value'] == '1' || strtolower($donnees['Value']) == 'true')
            return true;
        return false;
    }
    
    $req->closeCursor();
    return false;
}

?>