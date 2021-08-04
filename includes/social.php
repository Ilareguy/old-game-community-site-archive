<?php
/*
 * Fonctions Facebook
 */
 
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');
require_once(__ROOT__.'includes/Facebook/facebook.php');
require_once(__ROOT__.'includes/comptes.php');
require_once(__ROOT__.'includes/Global.php');

if(isset($_SESSION['id']))
    $ACCESS_TOKEN = CompteLinkedToFacebook($_SESSION['id']);
if($ACCESS_TOKEN !== false && $ACCESS_TOKEN != '')
    $ACCOUNT_LINKED_TO_FACEBOOK = true;

$facebook = new Facebook(array(
  'appId'  => $API_KEY,
  'secret' => $SECRET,
  'fileUpload' => true, 
  'cookie' => true,
));

if($ACCOUNT_LINKED_TO_FACEBOOK)
    $facebook->setAccessToken($ACCESS_TOKEN);

?>