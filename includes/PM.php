<?php
/*
* Les méthodes pour travailler avec les messages
* privés.
* **********************************************
* Par Anthony Ilareguy
* Créé le 17 Avril 2012
*/

if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');

function getPrivateMessage($ID){
    /*
    * Retourne les informations sur le pm
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('SELECT * FROM pm WHERE ID=? ');
    $req->execute(array($ID));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return $donnees;
    }
    
    $req->closeCursor();
    return false;
}

function getPrivateMessages($IDCompte, $from = 0, $to = 30){
    /*
    * Retourne l'ID des messages de l'utilisateur donné
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('SELECT ID FROM pm WHERE IDCompteTo=? ORDER BY Timestamp DESC, ID DESC LIMIT ' . $from . ', ' . $to);
    $req->execute(array($IDCompte));
    
    $count = 0;
    $return = Array();
    
    while($donnees = $req->fetch()){
        $return[$count] = $donnees['ID'];
        $count++;
    }
    
    $req->closeCursor();
    return $return;
}

function getCompteNewPMCount($IDCompte){
    /*
    * Retourne le nombre de nouveaux messages
    * du compte donné
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('SELECT COUNT(*) AS nb FROM pm WHERE IDCompteTo=? AND `Read`=0 ');
    $req->execute(array($IDCompte));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return $donnees['nb'];
    }
    
    $req->closeCursor();
    return 0;
}

function sendPrivateMessage($IDCompteFrom, $IDCompteTo, $title, $message, $specialIcon = "", $StringFrom = ""){
    /*
    * Envoyer un PM.
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('INSERT INTO pm (IDCompteFrom, IDCompteTo, Message, Title, Timestamp, SpecialIcon, StringFrom) VALUES (:IDCompteFrom, :IDCompteTo, :Message, :Title, :Timestamp, :SpecialIcon, :StringFrom)');
    $req->execute(array(
        ":IDCompteFrom" => $IDCompteFrom,
        ":IDCompteTo" => $IDCompteTo,
        ":Message" => stripslashes($message),
        ":Title" => stripslashes($title),
        ":Timestamp" => time(),
        ":SpecialIcon" => $specialIcon,
        ":StringFrom" => $StringFrom
    ));
    
    return true;
}

function markPMAsRead($ID){
    /*
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('UPDATE pm SET `Read`=\'1\' WHERE ID=? ');
    $req->execute(array($ID));
    
    return true;
}

function markPMAsUnread($ID){
    /*
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('UPDATE pm SET `Read`=\'0\' WHERE ID=? ');
    $req->execute(array($ID));
    
    return true;
}

function deletePM($ID){
    /*
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('DELETE FROM pm WHERE ID=? ');
    $req->execute(array($ID));
    
    return true;
}

function GetUnreadPMsInfo($UserID){
	/*
	 * Retourne les informations de base sur les messages non-lus de l'utilisateur donné.
	 * (Toutes les informations excepté le contenu du message)
	 */
	include(__ROOT__.'includes/bddConnect.php');
	require_once(__ROOT__.'includes/strings.php');
	
	$Return = array();
	$Count = 0;
	
	$req = $bdd->prepare('SELECT IDCompteFrom, IDCompteTo, Title, Timestamp, ID, StringFrom FROM pm WHERE `Read`=0 AND IDCompteTo=?');
	$req->execute(array($UserID));
	
	while($donnees = $req->fetch()){
		$Return[$Count] = $donnees;
		$Count++;
	}
	
	$req->closeCursor();
	return $Return;
}

function GetPMRecipientID($ID){
	/*
	 * Retourne l'ID du compte de destination du PM donné
	 */
	include(__ROOT__.'includes/bddConnect.php');
	
	$req = $bdd->prepare('SELECT IDCompteTo FROM PM WHERE ID=?');
	$req->execute(array($ID));
	
	if($donnees = $req->fetch()){
		$req->closeCursor();
		return $donnees['IDCompteTo'];
	}
	
	$req->closeCursor();
	return 0;
}
?>