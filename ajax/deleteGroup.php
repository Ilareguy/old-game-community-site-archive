<?php
/*
* Nécéssite une variable au minimum pour fonctionner (de type POST) :
* 1 - id -> l'ID du groupe à supprimer
************************************
* Renvoie deux résultats possibles :
* 1 - erreur
* 0 - succès
*/
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(dirname(__FILE__)) . '/');
include(__ROOT__.'includes/bddConnect.php');
require_once(__ROOT__."includes/groupes.php");
require_once(__ROOT__.'includes/droits.php');

if(isset($_POST['id']) && droit($_SESSION['id'], 'manipulateGroups')){
    deleteGroup($_POST['id']);
    echo '0';
}else{
    echo '1';
}

?>