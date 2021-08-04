<?php
/*
* Les fonctions qui permettent de poster des nouvelles, afficher des nouvelles et toutes les autres
* fonctions conçernant les nouvelles
*/
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');

function getNbNews(){
    /*
    * Retourne le nombre de nouvelles
    */
    
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT COUNT(*) AS nb FROM news');
    $req->execute(array());
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return $donnees['nb'];
    }
    
    $req->closeCursor();
    return false;
}

function getIdLastNews(){
    /*
    * Retourne l'ID de la dernière nouvelle
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT MAX(id) AS id FROM news');
    $req->execute(array());
    
    $donnees = $req->fetch();
    $req->closeCursor();
    return $donnees['id'];
}

function getNew($id){
    /*
    */
    
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT * FROM news WHERE id=? ');
    $req->execute(array($id));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return $donnees;
    }
    
    $req->closeCursor();
    return false;
}

function getNews($combien = 10){
    /*
    */
    
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT * FROM news ORDER BY timestamp DESC');
    $req->execute(array());
    
    $nouvelles = Array();
    $count = 0;
    
    while(($donnees = $req->fetch()) && ($count < $combien)){
        $nouvelles[$count] = $donnees;
        $count++;
    }
    
    $req->closeCursor();
    return $nouvelles;
}

function getAllNews(){
    /*
    */
    
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT * FROM news ORDER BY timestamp DESC, id DESC');
    $req->execute(array());
    
    $count = 0;
    $nouvelles = Array();
    
    while($donnees = $req->fetch()){
        $nouvelles[$count] = $donnees;
        $count++;
    }
    
    $req->closeCursor();
    return $nouvelles;
}

function supprimerNew($id){
    /*
    * Fonction qui supprime la nouvelle de la BDD donnée
    * en paramètre
    */
    
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('DELETE FROM news WHERE id=? ');
    $req->execute(array($id));
    
    $req->closeCursor();
    return true;
}

function editerNew($id, $content, $title, $postedBy = "The Diamond Craft Team"){
    /*
    * Fonction pour éditer une nouvelle
    */
    include(__ROOT__."includes/bddConnect.php");
    $req = $bdd->prepare('UPDATE news SET postedBy=?, content=?, title=? WHERE id=? ');
    $req->execute(array(htmlspecialchars(stripslashes($postedBy)), stripslashes($content), htmlspecialchars(stripslashes($title)), $id));
    
    $req->closeCursor();
    return true;
}

function ajouterNew($content, $title, $idCompte = 0, $postedBy = "The Diamond Craft Team"){
    /*
    * Fonction pour ajouter une nouvelle
    */
    include(__ROOT__."includes/bddConnect.php");
    $req = $bdd->prepare('INSERT INTO news (content, title, postedBy, timestamp, idCompte) 
    VALUES (:content, :title, :postedBy, :timestamp, :idCompte)');
    $req->execute(array(
        ":content" => stripslashes($content),
        ":title" => htmlspecialchars(stripslashes($title)),
        ":postedBy" => htmlspecialchars(stripslashes($postedBy)),
        ":timestamp" => time(),
        ":idCompte" => $idCompte
    ));
    
    $req->closeCursor();
    return true;
}

function newExists($id){
    /*
    * Vérifie si une new existe
    */
    
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT id FROM news WHERE id=? ');
    $req->execute(array($id));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return true;
    }
    
    $req->closeCursor();
    return false;
}
?>