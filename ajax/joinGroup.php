<?php
/*
* Nécéssite une variable au minimum pour fonctionner (de type POST) :
* 1 - id -> l'ID du groupe à joindre
************************************
* Renvoie deux résultats possibles :
* 1 - erreur
* 0 - succès
*/

if(!defined('__ROOT__'))
    define('__ROOT__', dirname(dirname(__FILE__)) . '/');
require_once(__ROOT__."includes/groupes.php");
require_once(__ROOT__.'includes/droits.php');

if(isset($_POST['id'])){
    $groupe = getGroupe($_POST['id']);
    if($groupe === false)
        $groupe = getGroupe(1);
}else
    $groupe = getGroupe(1);

if(droit($_SESSION['id'], 'bypassPrivateGroups') || ($groupe['appliquable'] == 1 && $groupe['publique'] == 1)){
    joinGroup($_SESSION['id'], $_POST['id']);
    echo '0';
}else{
    echo '1';
}
?>