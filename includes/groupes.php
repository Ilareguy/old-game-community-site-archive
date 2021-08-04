<?php
/*
* Toutes les fonctions pour manipuler les groupes qui sont créés par
* les webmasters du site
*
* CRÉÉ LE 10 OCTOBRE 2011
*
*************************
*
* Contains the functions to work with the groups created by the
* webmasters
*
* CREATED ON OCTOBER 10, 2011
*/
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');

function getGroupe($id){
    /*
    * Retourne toutes les informations sur le groupe donné en paramètre
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT * FROM groupes WHERE id=? ');
    $req->execute(array($id));
    if($donnees = $req->fetch()){
        // Groupe trouvé
        $req->closeCursor();
        return $donnees;
    }
    
    // Pas trouvé
    $req->closeCursor();
    return false;
}

function getGroupes(){
    /*
    * Renvoie tous les groupes
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT id FROM groupes');
    $req->execute(array());
    
    $count = 0;
    $return = Array();
    
    while($donnees = $req->fetch()){
        $return[$count] = $donnees;
        $count ++;
    }
    
    $req->closeCursor();
    return $return;
}

function compteDansGroupe($idCompte, $idGroupe){
    /*
    * Vérifie si le compte donné fait partie du groupe donné
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT idCompte FROM comptes_groupes WHERE idCompte=? AND idGroupe=? ');
    $req->execute(array($idCompte, $idGroupe));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return true;
    }
    
    $req->closeCursor();
    return false;
}

function nbMembresInGroup($idGroupe){
    /*
    * Renvoie le nombre de membres qui sont dans le groupe
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT COUNT(*) AS nombre FROM comptes_groupes WHERE idGroupe=? ');
    $req->execute(array($idGroupe));
    
    $donnees = $req->fetch();
    
    $req->closeCursor();
    return $donnees['nombre'];
}

function getComptesDansGroupe($idGroupe){
    /*
    * Renvoie un Array contenant tous les IDs des comptes qui sont
    * dans le groupe
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $count = 0;
    $return = Array();
    
    $req = $bdd->prepare('SELECT idCompte FROM comptes_groupes WHERE idGroupe=? ');
    $req->execute(array($idGroupe));
    
    while($donnees = $req->fetch()){
        $return[$count] = $donnees['idCompte'];
        $count ++;
    }
    
    $req->closeCursor();
    return $return;
}

function creerGroupe($nom, $description = "", $freeToJoin = true, $publique = true, $couleur = "#ffffff", $IDDirigeant = -1, $deletable = 1, $priority = 1){
    /*
    * Crée un groupe et, si désiré, met un compte
    * comme dirigeant du groupe
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('INSERT INTO groupes (nom, couleur, publique, description, deletable, priority, applicable, idDirigeant)
        VALUES (:nom, :couleur, :publique, :description, :deletable, :priority, :applicable, :idDirigeant)');
    $req->execute(array(
        ':nom' => htmlspecialchars(stripslashes(trim($nom))),
        ':couleur' => $couleur,
        ':publique' => $publique,
        ':description' => htmlspecialchars(stripslashes(trim($description))),
        ':deletable' => $deletable,
        ':priority' => $priority,
        ':applicable' => $freeToJoin,
        ':idDirigeant' => $IDDirigeant
    ));
    
    return true;
}

function modifierGroupeDescription($idGroupe, $description){
    /**
    * Modifier la description du groupe
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('UPDATE groupes SET description=? WHERE id=?');
    
    return $req->execute(array(stripslashes(htmlentities($description)), $idGroupe));
}

function groupExists($id){
    /*
    * Vérfie si le groupe existe
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('SELECT id FROM groupes WHERE id=? ');
    $req->execute(array($id));
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return true;
    }
    
    $req->closeCursor();
    return false;
}

function joinGroup($idCompte, $idGroupe){
    /*
    * Ajoute le compte donné dans le groupe.
    * !!!La fonction ne fait aucune vérification!!!
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    if(!compteDansGroupe($idCompte, $idGroupe)){
        $req = $bdd->prepare('INSERT INTO comptes_groupes (idCompte, idGroupe) VALUES (:idCompte, :idGroupe)');
        $req->execute(array(
            ':idCompte' => $idCompte,
            ':idGroupe' => $idGroupe
        ));
        
        $req->closeCursor();
        return true;
    }
    return false;
}

function leaveGroup($idCompte, $idGroupe){
    /**
    * Retire un compte du groupe
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('DELETE FROM comptes_groupes WHERE idGroupe=? AND idCompte=? ');
    $req->execute(array($idGroupe, $idCompte));
    
    $req->closeCursor();
    return true;
}

function deleteGroup($id){
    /*
    * Supprime le groupe donné ainsi que tous ses droits
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('DELETE FROM comptes_groupes WHERE idGroupe=? ');
    $req->execute(array($id));
    
    $req = $bdd->prepare('DELETE FROM droits_groupes WHERE idGroupe=? ');
    $req->execute(array($id));
    
    $req = $bdd->prepare('DELETE FROM groupes WHERE id=? ');
    $req->execute(array($id));
    
    $req->closeCursor();
    return true;
}

function getNomGroupe($id){
    /**
    * Retourne le nom du groupe donné
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('SELECT nom FROM groupes WHERE id=? ');
    $req->execute(array($id));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return $donnees['nom'];
    }
    
    $req->closeCursor();
    return false;
}

function GetIconGroup($ID){
    /**
    * Retourne le nom de l'icône du groupe
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('SELECT icon FROM groupes WHERE ID=?');
    $req->execute(array($ID));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return $donnees['icon'];
    }
    
    return 'user.png';
}

function GetColorGroup($ID){
    /*
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('SELECT couleur FROM groupes WHERE ID=?');
    $req->execute(array($ID));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return $donnees['couleur'];
    }
    
    return '';
}

function modifierIconeGroupe($ID, $Icon){
    /**
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('UPDATE groupes SET icon=? WHERE id=?');
    $req->execute(array($Icon, $ID));
    
}
?>