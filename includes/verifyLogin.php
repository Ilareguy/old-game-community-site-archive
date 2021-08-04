<?php
/**
* Vérification si on doit se connecter,
*/

if(!defined('__ROOT__'))
    define('__ROOT__', dirname(dirname(__FILE__)) . '/');
    
require_once(__ROOT__.'includes/comptes.php');
require_once(__ROOT__.'includes/sys.php');
require_once(__ROOT__.'includes/droits.php');

if(!$_SESSION['connecte']){
    // On est pas connecté
    if(isset($_COOKIE['remind']) && $_COOKIE['remind'] == 'true' && isset($_COOKIE['username']) && isset($_COOKIE['pwd'])){
        // On se connecte
        $_SESSION['connecte'] = login(htmlentities($_COOKIE['username']), htmlentities($_COOKIE['pwd']));
    }
}
?>