<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');

require_once(__ROOT__."includes/comptes.php");
require_once(__ROOT__."includes/dcn_notifications.php");
require_once(__ROOT__."includes/news.php");

if(isset($_GET['ps']) && isset($_GET['pw'])){
    // "ps" pour pseudo, "pw" pour password
    $idCompte = login($_GET['ps'], $_GET['pw']);
    if($idCompte !== false){
        // Login correct
        echo DCN_getNbNewNews($idCompte);
    }else{
        echo '-1';
    }
}
?>