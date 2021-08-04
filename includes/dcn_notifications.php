<?php
/*
* Comprend les fonctions qui seront appelИs pour 
* manipuler les notifications qui fonctionnent 
* avec Diamond Craft Notifier (DCN)
*
* CRии LE 24 OCTOBRE 2011
*
*************************
*
*/
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');

if(!function_exists("DCN_getNbNewNews")){
    function DCN_getNbNewNews($idCompte){
        /*
        * VИrifie la BDD s'il y a une news postИ dont
        * le compte donnИ en paramХtre n'a pas ИtИ notifiИ
        * par Diamond Craft Notifier
        */
        include(__ROOT__."includes/bddConnect.php");
        require_once(__ROOT__."includes/news.php");
        
        $idLastNew = getIdLastNews();
        
        $req = $bdd->prepare('SELECT idNews FROM dcn_newsnotifications WHERE idCompte=? ');
        $req->execute(array($idCompte));
        
        if($donnees = $req->fetch()){
            $idLastNewNotified = $donnees['idNews'];
            $req = $bdd->prepare('UPDATE dcn_newsnotifications SET idNews=? WHERE idCompte=? ');
            $req->execute(array($idLastNew, $idCompte));
        }else{
            $idLastNewNotified = 0;
            // Pas encore dans la BDD, alors on l'entre
            $req = $bdd->prepare('INSERT INTO dcn_newsnotifications (idCompte, idNews) VALUES (:idCompte, :idNews)');
            $req->execute(array(
                ":idCompte" => $idCompte,
                ":idNews" => $idLastNew
            ));
        }
        
        $req = $bdd->prepare('SELECT COUNT(*) AS nb FROM news WHERE id > ?');
        $req->execute(array($idLastNewNotified));
        $donnees = $req->fetch();
        
        $req->closeCursor();
        return $donnees['nb'];
    }
}
?>