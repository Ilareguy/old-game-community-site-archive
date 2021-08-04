<?php
session_start();
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(dirname(dirname(__FILE__))) . '/');
require_once __ROOT__."includes/comptes.php";
require_once __ROOT__."includes/PM.php";
require_once __ROOT__."includes/VIP.php";

echo '<update>';

if(isset($_GET['login']) && isset($_GET['password'])){
    if(login($_GET['login'], $_GET['password'])){
        // Succès
        echo '<login>1</login>';
        
        // On vérifie s'il est VIP
        $ID = getIDFromPseudo($_GET['login']);
        $IsVIP = IsVIP($ID);
        echo '<IsVIP>' . ($IsVIP ? '1' : '0') . '</IsVIP>';
        
        if($IsVIP){
            // On est VIP; on peut continuer
            
            // News
            echo '<News>' . getNbNewNews($ID) . '</News>';
            
            // Private Messages
            echo '<PM>' . getCompteNewPMCount($ID) . '</PM>';
        }
        
    }else{
        // Mauvais identifiants
        echo '<login>0</login>';
    }
}

echo '</update>';
?>
