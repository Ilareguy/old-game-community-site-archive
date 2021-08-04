<?php
session_start();
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(dirname(dirname(__FILE__))) . '/');
require_once __ROOT__."includes/comptes.php";
require_once __ROOT__."includes/VIP.php";

echo '<login>';
if(isset($_GET['login']) && isset($_GET['password'])){
    if(login($_GET['login'], $_GET['password'])){
        // Succès
        echo '<result>1</result>';
        
        // On vérifie s'il est VIP
        $ID = getIDFromPseudo($_GET['login']);
        echo '<IsVIP>' . (IsVIP($ID) ? '1' : '0') . '</IsVIP>';
        
    }else{
        // Mauvais identifiants
        echo '<result>0</result>';
    }
}
echo '</login>';

?>