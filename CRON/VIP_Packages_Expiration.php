<?php

$f = fopen('/home/diamond/domains/diamondcraft.org/public_html/CRON/history.txt', 'a');

$tab = "    ";
$error = "";

$String = "VIP Packages actualisés (" . date('D, d M Y H:i:s') . " : " . time() . "): ";
if($error == ""){
    $String .= PHP_EOL . $tab . "Aucune erreur.";
}else{
    $String .= PHP_EOL . $tab . "Erreur(s):" . PHP_EOL . $error;
}
$String .= PHP_EOL . "----------------------------------------------------------";

fwrite($f, $String . PHP_EOL);
fclose($f);

?>