<?php
/*
 * Pour manipuler/utiliser le système de villes/groupes.
 *
 * Débuté le 11 Décembre 2012
 */

if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');
    
function CreateTown($IdCompte, $Name){
    /*
     * Crée une ville dont le nom est @$Name, appartenant au joueur @$IdCompte
     */
    include(__ROOT__.'includes/bddConnect.php');
    
    //
    
}

function GetTown($ID){
    /*
     * Retourne toutes les informations sur une ville
     */
    
    //
    
}

function TownNameExists($Name){
}

function TownIdExists($ID){
}

function RequestJoinTown($IdPlayer, $IdTown){
}

function RejectTownRequest($IdRequest){
}

function AcceptTownRequest($IdRequest){
}

function CancelTownRequest($IdRequest){
}

function PlayerIsInTown($IdPlayer, $IdTown){
}

function PlayerHasRequestedToJoinTown($IdPlayer, $IdTown){
}

function TownAddPlayer($IdTown, $IdPlayer){
}

function TownRemovePlayer($IdPlayer, $IdTown){
}

function TownUpdatePlayerStatus($IdPlayer, $IdTown, $NewStatus){
}

function TownPlayerGetStatus($IdPlayer, $IdTown){
}
    
?>