<?php
/*
 * Appeler cette page en cas de connexion à Facebook avec l'AccessToken comme
 * paramètre GET['accesstoken']
 */
session_start();
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(dirname(__FILE__)) . '/');
require_once(__ROOT__."includes/social.php");
require_once(__ROOT__."includes/comptes.php");

if(isset($_SESSION['id']) && isset($_GET['accesstoken'])){
    
    // Obtention d'un nouvel Access Token
    $facebook->setAccessToken($_GET['accesstoken']);
    $NewAccessToken = $facebook->getExtendedAccessToken();
    
    // Mise à jour des informations du compte
    CompteUpdateFacebookAccessToken($_SESSION['id'], $NewAccessToken);
    if($NewAccessToken !== false)
        CompteSetFacebookUsername($_SESSION['id'], $facebook->getUser());
    else
        CompteSetFacebookUsername($_SESSION['id'], '');
    
}
?>