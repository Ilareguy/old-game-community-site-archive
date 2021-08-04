<?php
/*
* Toutes les fonctions pour manipuler les status VIP et les récompenses.
*/
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(dirname(__FILE__)) . '/');
    
function IsVIP($IDCompte){
    /*
    * Retourne True si le compte est VIP; False sinon.
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('SELECT IDPackage FROM vip_active_packages WHERE IDCompte=?');
    $req->execute(array($IDCompte));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return true;
    }
    
    $req->closeCursor();
    return false;
}

function GetVIPPackages(){
    /*
    * Retourne une liste de tous les ID des packages
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $Return = array();
    $CurrentCount = 0;
    
    $req = $bdd->prepare('SELECT ID FROM vip_packages');
    $req->execute(array());
    
    while($donnees = $req->fetch()){
        $Return[$CurrentCount] = $donnees['ID'];
        $CurrentCount++;
    }
    
    $req->closeCursor();
    return $Return;
}

?>