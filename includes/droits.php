<?php
/*
* Liste des droits:
* moderatorForum_forumId : compte modérateur dans la section donnée;
* manipulateForums : droit d'accès à la page administrative_forums.php;
* manipulateAccounts : droit de voir la page administrative pour les comptes ET de modifier les comptes;
* manipulateNews : droit d'ajouter, supprimer et éditer des news;
* manipulatePolls : droit d'ajouter, supprimer, démarrer, stopper, éditer (etc.) les votes (Polls);
* manipulateGroups : droit de modifier/ajouter/supprimer des groupes
* bypassPrivateGroups : droit d'appliquer dans un groupe privé
* putIconOnPM : droit d'attribuer des icônes aux PM
* putCustomStringFromOnPM : droit de mettre un StringFrom personnalisé sur les PM
* gameServerCommand : droit d'utiliser toutes les commandes in-game depuis le site
* whitelistGameServerCommand : droit d'utiliser les boutons "white-list" depuis le site
*/
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');

function droit($idCompte, $droit){
    /*
    * Cette fonction vérifie si le compte donné en paramètre a le droit
    * donné également en paramètre. Elle s'exécute en plusieurs étapes
    */
    include(__ROOT__."includes/bddConnect.php");
    require_once(__ROOT__."includes/groupes.php");
    require_once(__ROOT__."includes/comptes.php");
    
    /*
    * ÉTAPE 1
    * On vérifie si le compte est webmaster
    */
    $req = $bdd->prepare('SELECT webmaster FROM comptes WHERE id=? ');
    $req->execute(array($idCompte));
    $donnees = $req->fetch();
    if($donnees['webmaster'] == 1){
        $req->closeCursor();
        return true;
    }

    /*
    * ÉTAPE 2
    * On vérifie pour les droits personnels du compte
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
    * ÉTAPE 3
    * On vérifie selon les groupes du compte
    */
    $groupes = getCompteGroupes($idCompte);
    for($i=0; $i<count($groupes); $i++){
        /*
        * Pour tous les groupes, on vérifie si le droit est entré dans la BDD.
        * S'il l'est, on vérifie sa valeur;
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
    * Si on arrive ici, c'est qu'on a rien trouvé. Par défaut, on retourne false
    */
    
    $req->closeCursor();
    return false;
}

function droitGroupe($idGroupe, $droit){
    /*
    * Vérifie si le groupe donné possède le droit
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
    * Modifier le droit donné pour le groupe donné
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    // On vérifie si le droit existe déjà
    $req = $bdd->prepare('SELECT valeur FROM droits_groupes WHERE idGroupe=? AND droit=?');
    $req->execute(array($idGroupe, $droit));
    if($donnees = $req->fetch()){
        
        // Le droit existe déjà
        if($donnees['valeur'] != $valeur){
            
            $req->closeCursor();
            $req = $bdd->prepare('UPDATE droits_groupes SET valeur=? WHERE idGroupe=? AND droit=?');
            $req->execute(array($valeur, $idGroupe, $droit));
            
            return true;
            
        }else
            return true; // Le droit était déjà comme on le souhaitait
        
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
    * Vérifie si le compte donné en paramètre a le droit de voir la section
    * du forum également donné en paramètre, en fonction de ses groupes.
    */
    include(__ROOT__."includes/bddConnect.php");
    require_once(__ROOT__."includes/groupes.php");
    require_once(__ROOT__."includes/comptes.php");
    require_once(__ROOT__."includes/forums.php");
    
    $forum = getForum($idForum);
    
    /*
    * ÉTAPE 1
    * On y va avec la valeur par défaut du forum
    */
    if($forum['voirDefaut'] == 1)
        return true;
    
    /*
    * ÉTAPE 2
    * On vérifie si le compte est webmaster
    */
    $req = $bdd->prepare('SELECT webmaster FROM comptes WHERE id=? ');
    $req->execute(array($idCompte));
    $donnees = $req->fetch();
    if($donnees['webmaster'] == 1){
        $req->closeCursor();
        return true;
    }

    /*
    * ÉTAPE 3
    * On vérifie selon les groupes du compte.
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