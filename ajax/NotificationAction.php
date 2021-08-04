<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(dirname(__FILE__)) . '/');
    
include(__ROOT__."includes/bddConnect.php");
include(__ROOT__."includes/verifyLogin.php");
require_once(__ROOT__.'includes/Global.php');
require_once(__ROOT__.'includes/PM.php');
require_once(__ROOT__.'includes/comptes.php');

if($_SESSION['connecte'] && isset($_GET['action'])){
    
	switch($_GET['action']){
		case $NOTIFICATION_ACTION_MARK_PM_AS_READ:
			if(isset($_GET['id']) && (GetPMRecipientID($_GET['id']) == $_SESSION['id'])){
				markPMAsRead($_GET['id']);
				echo 'DONE';
			}
			break;
			
		case $NOTIFICATION_ACTION_DELETE_PM:
			if(isset($_GET['id']) && (GetPMRecipientID($_GET['id']) == $_SESSION['id'])){
				deletePM($_GET['id']);
				echo 'DONE';
			}
			break;
	}
	
}
?>