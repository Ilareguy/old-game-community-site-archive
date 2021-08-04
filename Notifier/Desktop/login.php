<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(dirname(__FILE__)) . '/');
require_once "includes/comptes.php";

if(isset($_GET['ps']) && isset($_GET['pw'])){
    // "ps" pour pseudo, "pw" pour password
    if(login($_GET['ps'], $_GET['pw']))
        echo '1';
    else
        echo '0';
}
?>