<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');

require_once "includes/comptes.php";

if(isset($_GET['ps']) && isset($_GET['pw']) && isset($_GET['r'])){
    // "ps" pour pseudo, "pw" pour password, "r" pour redirect
    if(login($_GET['ps'], $_GET['pw']))
        header('Location: ' . $_GET['r']);
    else
        header('Location: login.php?err=2');
}
?>