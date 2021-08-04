<?php
ini_set('log_errors', true);
ini_set('error_log', dirname(__FILE__).'/ipn_errors.log');

if(!defined('__ROOT__'))
    define('__ROOT__', dirname(dirname(__FILE__)) . '/');
require_once(__ROOT__.'includes/sys.php');
require_once(__ROOT__.'includes/ipnlistener.php');
require_once(__ROOT__.'includes/VIP_Packages/VIPPackage.class.php');
require_once(__ROOT__.'includes/groupes.php');
require_once(__ROOT__.'includes/comptes.php');

$listener = new IpnListener();
$listener->use_sandbox = isset($_GET['sandbox']) ? true : IsPaypalSandbox();

// try to process the IPN POST
try {
    $listener->requirePostMethod();
    $verified = $listener->processIpn();
} catch (Exception $e) {
    error_log($e->getMessage());
    exit(0);
}

// TODO: Handle IPN Response here
if ($verified) {

    //mail('totoila@totoila.com', 'Valid IPN', $listener->getTextReport());
    $IDPackage = $_POST['item_number'];
    $IDCompte = isset($_GET['IDCompte']) ? $_GET['IDCompte'] : false;
    if($IDCompte === false){
        // Erreur, il nous manque l'ID du compte...
        return;
    }
    
    $Package = VIPPackage::DynamicNew($IDPackage);
    $Package->Apply($IDCompte);
    joinGroup($IDCompte, 2);            // Groupe #2, VIP
    
    // Envoie des courriels
    $PID = $Package->ID();
    $PName = $Package->Name();
    $PDesc = $Package->Description();
    $PPrice = $Package->Price();
    $Report = $listener->getTextReport();
    $Report = str_replace("\n", "<br />", $Report);
    $Player = getCompte($IDCompte);
    $CourrielMessage = <<<EOD
<html>
<head>
<title>A player has purchased a package</title>
<style type="text/css">body{font-family: Calibri, Arial, serif;}</style>
</head>
<body>
<h2>A player has purchased a package !</h2><br />
<br />
<h4>Package Information:</h4><br />
<br />
ID: $PID<br />
Name: $PName<br />
Price: \$$PPrice CAD<br />
Description: $PDesc<br />
--------------------------------------------<br />
<h4>Player's Information:</h4><br />
<br />
ID: $Player[id]<br />
Username: $Player[pseudo]<br />
Full Name: $Player[firstName] $Player[lastName]<br />
--------------------------------------------<br />
<h4>IPN Listener Text Report:</h4><br />
<br />
$Report<br />
<br />
</body>
EOD;
    
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    $headers .= 'To: Anthony Ilareguy <totoila@totoila.com>, Jordan Ilareguy <jordan.ilareguy@me.com>' . "\r\n";
    $headers .= 'From: Diamond Craft <diamondcraft@diamondcraft.org>' . "\r\n";
    
    mail('totoila@totoila.com, jordan.ilareguy@me.com', 'Diamond Craft: VIP Package Purchase', $CourrielMessage, $headers);
    
} else {
    //error_log('Unverified');
    //mail('totoila@totoila.com', 'Invalid IPN', $listener->getTextReport());
}

?>