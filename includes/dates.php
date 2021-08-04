<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');

function convertirDatePublicationEnString($timestamp, $combien = 5){
    /*
    * Pour les timestamps: on calcule en semaines, jours, heures, minutes et secondes
    * le nombre de temps écoulé depuis la publication de la nouvelle
    * VERSION PLUS COURTE: elle affiche un maximum de deux nombres. Ex: "1 semaine et 3 jours"
    */
    $publicationText = "";
    $besoinVirgule = false;
    $ans = 0; $mois = 0; $semaines = 0; $jours = 0; $heures = 0; $minutes = 0; $secondes = 0;
    $difference = time() - $timestamp;
    $ans = floor($difference / 31536000);
    $countNombresAffiches = 0;
    if($ans > 0){
        $difference -= ($ans * 31536000);
        $publicationText .= $ans;
        if($ans > 1)
            $publicationText .= " years";
        else
            $publicationText .= " year";
        
        $besoinVirgule = true;
        $countNombresAffiches ++;
    }
    $mois = floor($difference / 2419200);
    if($mois > 0 && $countNombresAffiches < $combien){
        $difference -= ($mois * 2419200);
        if($countNombresAffiches == ($combien - 1) && $combien == 1)
            $publicationText .= " " . $mois;
        else if($countNombresAffiches == ($combien - 1) && $combien > 1)
            $publicationText .= " and " . $mois;
        else if($besoinVirgule)
            $publicationText .= ", " . $mois;
        else
            $publicationText .= $mois;
            
        if($mois > 1)
            $publicationText .= " months";
        else
            $publicationText .= " month";
            
        $besoinVirgule = true;
        $countNombresAffiches++;
    }
    $semaines = floor($difference / 604800);
    if($semaines > 0 && $countNombresAffiches < $combien){
        $difference -= ($semaines * 604800);
        if($countNombresAffiches == ($combien - 1) && $combien == 1)
            $publicationText .= " " . $semaines;
        else if($countNombresAffiches == ($combien - 1) && $combien > 1)
            $publicationText .= " and " . $semaines;
        else if($besoinVirgule)
            $publicationText .= ", " . $semaines;
        else
            $publicationText .= $semaines;
            
        if($semaines > 1)
            $publicationText .= " weeks";
        else
            $publicationText .= " week";
            
        $besoinVirgule = true;
        $countNombresAffiches++;
    }
    $jours = floor($difference / 86400);
    if($jours > 0 && $countNombresAffiches < $combien){
        $difference -= ($jours * 86400);
        if($countNombresAffiches == ($combien - 1) && $combien == 1)
            $publicationText .= " " . $jours;
        else if($countNombresAffiches == ($combien - 1) && $combien > 1)
            $publicationText .= " and " . $jours;
        else if($besoinVirgule)
            $publicationText .= ", " . $jours;
        else
            $publicationText .= $jours;
            
        if($jours > 1)
            $publicationText .= " days";
        else
            $publicationText .= " day";
            
        $besoinVirgule = true;
        $countNombresAffiches++;
    }
    $heures = floor($difference / 3600);
    if($heures > 0 && $countNombresAffiches < $combien){
        $difference -= ($heures * 3600);
        if($countNombresAffiches == ($combien - 1) && $combien == 1)
            $publicationText .= " " . $heures;
        else if($countNombresAffiches == ($combien - 1) && $combien > 1)
            $publicationText .= " and " . $heures;
        else if($besoinVirgule)
            $publicationText .= ", " . $heures;
        else
            $publicationText .= $heures;
        if($heures > 1)
            $publicationText .= " hours";
        else
            $publicationText .= " hour";
            
        $besoinVirgule = true;
        $countNombresAffiches++;
    }
    $minutes = floor($difference / 60);
    if($minutes > 0 && $countNombresAffiches < $combien){
        $difference -= ($minutes * 60);
        if($countNombresAffiches == ($combien - 1) && $combien == 1)
            $publicationText .= " " . $minutes;
        else if($countNombresAffiches == ($combien - 1) && $combien > 1)
            $publicationText .= " and " . $minutes;
        else if($besoinVirgule)
            $publicationText .= ", " . $minutes;
        else
            $publicationText .= $minutes;
        if($minutes > 1)
            $publicationText .= " minutes";
        else
            $publicationText .= " minute";
            
        $besoinVirgule = true;
        $countNombresAffiches++;
    }
    $secondes = $difference;
    if($countNombresAffiches < $combien){
        if($countNombresAffiches == ($combien - 1) && $combien == 1)
            $publicationText .= " " . $secondes;
        else if($countNombresAffiches == ($combien - 1) && $combien >1)
            $publicationText .= " and " . $secondes;
        else if($besoinVirgule)
            $publicationText .= " and " . $secondes;
        else
            $publicationText .= $secondes;
                
            if($secondes > 1)
                $publicationText .= " seconds";
            else
                $publicationText .= " second";
    }
            
    return $publicationText;
}
?>