<?php
/*
* Liste des droits:
* moderatorForum_forumId : compte mod�rateur dans la section donn�e;
* manipulateForums : droit d'acc�s � la page administrative_forums.php;
* manipulateAccounts : droit de voir la page administrative pour les comptes ET de modifier les comptes;
* manipulateNews : droit d'ajouter, supprimer et �diter des news;
* manipulatePolls : droit d'ajouter, supprimer, d�marrer, stopper, �diter (etc.) les votes (Polls);
* manipulateGroups : droit de modifier/ajouter/supprimer des groupes
* bypassPrivateGroups : droit d'appliquer dans un groupe priv�
* putIconOnPM : droit d'attribuer des ic�nes aux PM
* putCustomStringFromOnPM : droit de mettre un StringFrom personnalis� sur les PM
* gameServerCommand : droit d'utiliser toutes les commandes in-game depuis le site
* whitelistGameServerCommand : droit d'utiliser les boutons "white-list" depuis le site
*/
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');

function droit($idCompte, $droit){
    /*
    * Cette fonction v�rifie si le compte donn� en param�tre a le droit
    * donn� �galement en param�tre. Elle s'ex�cute en plusieurs �tapes
    */
    include(__ROOT__."includes/bddConnect.php");
    require_once(__ROOT__."includes/groupes.php");
    require_once(__ROOT__."includes/comptes.php");
    
    /*
    * �TAPE 1
    * On v�rifie si le compte est webmaster
    */
    $req = $bdd->prepare('SELECT webmaster FROM comptes WHERE id=? ');
    $req->execute(array($idCompte));
    $donnees = $req->fetch();
    if($donnees['webmaster'] == 1){
        $req->closeCursor();
        return true;
    }

    /*
    * �TAPE 2
    * On v�rifie pour les droits personnels du compte
    */
    $req = $bdd->prepare('SELECT valeur FROM droits_comptes WHERE idCompte=? AND droit=? ');
    $req->execute(array($idCompte, $droit));
    if($donnees = $req->fetch()){
        $req->closeCursor();
        if($donnees['valeur'] == 1)
            return true;
        return false;
    }
    
    /*
    * �TAPE 3
    * On v�rifie selon les groupes du compte
    */
    $groupes = getCompteGroupes($idCompte);
    for($i=0; $i<count($groupes); $i++){
        /*
        * Pour tous les groupes, on v�rifie si le droit est entr� dans la BDD.
        * S'il l'est, on v�rifie sa valeur;
        * S'il ne l'est pas, on continue dans la fonction
        */
        $req = $bdd->prepare('SELECT valeur FROM droits_groupes WHERE idGroupe=? AND droit=? ');
        $req->execute(array($groupes[$i]['id'], $droit));
        if($donnees = $req->fetch()){
            $req->closeCursor();
            if($donnees['valeur'] == 1)
                return true;
            return false;
        }
    }
    
    /*
    * Si on arrive ici, c'est qu'on a rien trouv�. Par d�faut, on retourne false
    */
    
    $req->closeCursor();
    return false;
}

function droitGroupe($idGroupe, $droit){
    /*
    * V�rifie si le groupe donn� poss�de le droit
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT valeur FROM droits_groupes WHERE idGroupe=? AND droit=? ');
    $req->execute(array($idGroupe, $droit));
    if($donnees = $req->fetch()){
        $req->closeCursor();
        if($donnees['valeur'] == '1')
            return true;
        return false;
    }
    
    $req->closeCursor();
    return false;
}

function modifierDroitGroupe($idGroupe, $droit, $valeur){
    /**
    * Modifier le droit donn� pour le groupe donn�
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    // On v�rifie si le droit existe d�j�
    $req = $bdd->prepare('SELECT valeur FROM droits_groupes WHERE idGroupe=? AND droit=?');
    $req->execute(array($idGroupe, $droit));
    if($donnees = $req->fetch()){
        
        // Le droit existe d�j�
        if($donnees['valeur'] != $valeur){
            
            $req->closeCursor();
            $req = $bdd->prepare('UPDATE droits_groupes SET valeur=? WHERE idGroupe=? AND droit=?');
            $req->execute(array($valeur, $idGroupe, $droit));
            
            return true;
            
        }else
            return true; // Le droit �tait d�j� comme on le souhaitait
        
    }else{
        
        // Le droit n'existait pas; on l'entre
        $req = $bdd->prepare('INSERT INTO droits_groupes (idGroupe, droit, valeur) VALUES (:idGroupe, :droit, :valeur)');
        $req->execute(array(
            ":idGroupe" => $idGroupe,
            ":droit" => $droit,
            ":valeur" => $valeur
        ));
        
        return true;
        
    }
    
    return false;
}

function droitVoirForum($idCompte, $idForum){
    /*
    * V�rifie si le compte donn� en param�tre a le droit de voir la section
    * du forum �galement donn� en param�tre, en fonction de ses groupes.
    */
    include(__ROOT__."includes/bddConnect.php");
    require_once(__ROOT__."includes/groupes.php");
    require_once(__ROOT__."includes/comptes.php");
    require_once(__ROOT__."includes/forums.php");
    
    $forum = getForum($idForum);
    
    /*
    * �TAPE 1
    * On y va avec la valeur par d�faut du forum
    */
    if($forum['voirDefaut'] == 1)
        return true;
    
    /*
    * �TAPE 2
    * On v�rifie si le compte est webmaster
    */
    $req = $bdd->prepare('SELECT webmaster FROM comptes WHERE id=? ');
    $req->execute(array($idCompte));
    $donnees = $req->fetch();
    if($donnees['webmaster'] == 1){
        $req->closeCursor();
        return true;
    }

    /*
    * �TAPE 3
    * On v�rifie selon les groupes du compte.
    */
    $groupes = getCompteGroupes($idCompte);
    $droitTrouve = false;
    for($i=0; $i<count($groupes); $i++){
        if(droitGroupeVoirForum($groupes[$i]['id'], $idForum))
           $droitTrouve = true; 
    }
    if($droitTrouve)
        return true;
    
    return false;
}

function droitGroupeVoirForum($idGroupe, $idForum){
    /*
    */
    include(__ROOT__."includes/bddConnect.php");

    $req = $bdd->prepare('SELECT idGroupe FROM forums_groupes_visualisations WHERE idGroupe=? AND idForum=? ');
    $req->execute(array($idGroupe, $idForum));
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return true;
    }
    
    $req->closeCursor();
    return false;
}

function droitTown($IdPlayer, $Droit){
}

?>