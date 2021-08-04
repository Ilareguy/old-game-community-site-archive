<?php
/**
* Ce package offre une couleur bleue au texte du joueur
*/
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(dirname(dirname(__FILE__))) . '/');

require_once(__ROOT__.'includes/comptes.php');
require_once(__ROOT__.'includes/GameServer.php');

class VIPPackage_1 extends VIPPackage{
    
    protected $pID = 1;
    public function ID(){
        return 1;
    }
    
    public function Apply($ID){
        
        $Username = getPseudo($ID);
        
        // Application de la couleur au joueur
        SendServerCommand('vip ' . $Username);
        
        // Message général
        SendServerCommand('say ' . $Username . ' is now a VIP!'); // for three months!
        
        // On enregistre dans la BDD
        self::RegisterPlayerAsVIP($ID, 1, 7889231); // 7889231 secondes, soit trois mois
        
    }
    
}

?>