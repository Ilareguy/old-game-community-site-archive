<?php
/*
* Les fonctions pour transformer les textes
*/
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');

function bbcodes($stringToTransform, $bbcodes = true){
    include(__ROOT__."includes/bddConnect.php");
    require_once(__ROOT__."includes/news.php");
    require_once(__ROOT__.'includes/comptes.php');
    
    $string = $stringToTransform;
    if($bbcodes){
        
        // BBCodes plus complexes
        if(verifierFermeturesBBcodes($string, 'quote')){
            /*
            * {quote author="" date=""}{/quote}
            */
            $innerBalise = "quote";
            $count = substr_count($string, '{/' . $innerBalise . '}');
            $lastPositionStart = 0;
            for($i = 0; $i < $count; $i++){
                $pos = stripos($string, '{' . $innerBalise . '', $lastPositionStart);
                $lastPositionStart = $pos + 1;
                $sub = htmlspecialchars_decode(substr($string, $pos));
                
                // Paramètres
                $author = htmlspecialchars(getBBCodeParam($sub, 'author'));
                $date = htmlspecialchars(getBBCodeParam($sub, 'date'));
                $value = getBBCodeValue($sub, $innerBalise);
                
                // On trouve la position du début de la balise d'ouverture
                $beginPos = strpos($string, '{' . $innerBalise);
                $str1 = substr($string, 0, $beginPos);
                
                // Et le reste du string, tout de suite après la balise de fermeture
                $endPos = strpos($string, '{/' . $innerBalise . '}') + strlen('{/' . $innerBalise . '}');
                $str3 = substr($string, $endPos);
                
                // $str2 est notre transformation
                $str2 = '</p><div style="border: 1px solid #5e8796; padding: 8px; margin-bottom: 10px;">';
                if($date == '')
                    $date = 'Not Specified';
                if($author == '')
                    $author = 'Not Specified';
                else{
                    $id = getIDFromPseudo($author);
                    if($id > 0){
                        $temp = $author;
                        $author = '<a href="account.php?id=' . $id . '">' . $temp . '</a>';
                    }
                }
                $str2 .= '<p><span style="font-size: 200%;">&ldquo;&rdquo;</span><br><strong>Author : </strong>' . $author . '<br><strong>Date : </strong>' . $date . '</p>';
                $str2 .= '<p>&ldquo;<i>' . $value . '</i>&rdquo;</p>';
                $str2 .= '</div><p>';
                
                $string = $str1.$str2.$str3;
            }
        }
        
        if(verifierFermeturesBBcodes($string, 'smiley')){
            /*
            * {smiley}source{/smiley}
            */
            $innerBalise = "smiley";
            $count = substr_count($string, '{/' . $innerBalise . '}');
            $lastPositionStart = 0;
            for($i = 0; $i < $count; $i++){
                $pos = stripos($string, '{' . $innerBalise . '', $lastPositionStart);
                $lastPositionStart = $pos + 1;
                $sub = substr($string, $pos);
                
                // Paramètres
                $value = getBBCodeValue($sub, $innerBalise);
                
                // On trouve la position du début de la balise d'ouverture
                $beginPos = strpos($string, '{' . $innerBalise);
                $str1 = substr($string, 0, $beginPos);
                
                // Et le reste du string, tout de suite après la balise de fermeture
                $endPos = strpos($string, '{/' . $innerBalise . '}') + strlen('{/' . $innerBalise . '}');
                $str3 = substr($string, $endPos);
                
                // $str2 est notre transformation
                $str2 = '<img alt="" src="' . trim($value) . '" style="max-width: 83px; max-height: 83px;"/>';
                
                $string = $str1.$str2.$str3;
            }
        }
        
        if(verifierFermeturesBBcodes($string, 'youtube')){
            /*
            * {youtube}source{/youtube}
            */
            $innerBalise = "youtube";
            $count = substr_count($string, '{/' . $innerBalise . '}');
            $lastPositionStart = 0;
            for($i = 0; $i < $count; $i++){
                $pos = stripos($string, '{' . $innerBalise . '', $lastPositionStart);
                $lastPositionStart = $pos + 1;
                $sub = substr($string, $pos);
                
                // Paramètres
                $value = getBBCodeValue($sub, $innerBalise);
                
                // On trouve la position du début de la balise d'ouverture
                $beginPos = strpos($string, '{' . $innerBalise);
                $str1 = substr($string, 0, $beginPos);
                
                // Et le reste du string, tout de suite après la balise de fermeture
                $endPos = strpos($string, '{/' . $innerBalise . '}') + strlen('{/' . $innerBalise . '}');
                $str3 = substr($string, $endPos);
                
                // $str2 est notre transformation
                // Pour {youtube} spécifique, il nous faut trouver l'ID de la vidéo
                $VideoID = substr($value, (strrpos($value, 'v=') + 2), 11);
                $str2 = '<iframe type="text/html" width="540" style="margin-bottom: 20px;" height="390" src="http://www.youtube.com/embed/' . $VideoID . '?autoplay=0&origin=http://www.diamondcraft.org" frameborder="0" ></iframe>';
                
                $string = $str1.$str2.$str3;
            }
        }
        
        if(verifierFermeturesBBcodes($string, 'player')){
            $string = bbcodes_player($string);
        }
        
        if(verifierFermeturesBBcodes($string, 'picture')){
            /*
            * {picture}URL{/picture}
            */
            $innerBalise = "picture";
            $count = substr_count($string, '{/' . $innerBalise . '}');
            $lastPositionStart = 0;
            for($i = 0; $i < $count; $i++){
                $pos = stripos($string, '{' . $innerBalise . '', $lastPositionStart);
                $lastPositionStart = $pos + 1;
                $sub = substr($string, $pos);
                
                // Paramètres
                $value = getBBCodeValue($sub, $innerBalise);
                
                // On trouve la position du début de la balise d'ouverture
                $beginPos = strpos($string, '{' . $innerBalise);
                $str1 = substr($string, 0, $beginPos);
                
                // Et le reste du string, tout de suite après la balise de fermeture
                $endPos = strpos($string, '{/' . $innerBalise . '}') + strlen('{/' . $innerBalise . '}');
                $str3 = substr($string, $endPos);
                
                // $str2 est notre transformation
                $str2 = '</p><p style="text-align:center;"><img style="max-width:535px;" src="' . trim($value) . '" /></p><p>';
                
                $string = $str1.$str2.$str3;
            }
        }
        
        /*
        * BBcodes de base: bold, souligné, italic, white, brown, red, green
        */
        if(verifierFermeturesBBcodes($string, 'bold')){
            $string = str_replace('{bold}', '<strong>', $string);  $string = str_replace('{/bold}', '</strong>', $string);
        }else{
             $string = str_replace('{bold}', '', $string);  $string = str_replace('{/bold}', '', $string);
        }
        if(verifierFermeturesBBcodes($string, 'italic')){
            $string = str_replace('{italic}', '<i>', $string);  $string = str_replace('{/italic}', '</i>', $string);
        }else{
             $string = str_replace('{italic}', '', $string);  $string = str_replace('{/italic}', '', $string);
        }
        if(verifierFermeturesBBcodes($string, 'underline')){
             $string = str_replace('{underline}', '<span style="text-decoration: underline;">', $string);  $string = str_replace('{/underline}', '</span>', $string);
        }else{
             $string = str_replace('{underline}', '', $string);  $string = str_replace('{/underline}', '', $string);
        }
        if(verifierFermeturesBBcodes($string, 'white')){
             $string = str_replace('{white}', '<span style="color: white;">', $string);  $string = str_replace('{/white}', '</span>', $string);
        }else{
             $string = str_replace('{white}', '', $string);  $string = str_replace('{/white}', '', $string);
        }
        if(verifierFermeturesBBcodes($string, 'brown')){
             $string = str_replace('{brown}', '<span style="color: #845a00;">', $string);  $string = str_replace('{/brown}', '</span>', $string);
        }else{
             $string = str_replace('{brown}', '', $string);  $string = str_replace('{/brown}', '', $string);
        }
        if(verifierFermeturesBBcodes($string, 'red')){
             $string = str_replace('{red}', '<span style="color: #b72400;">', $string);  $string = str_replace('{/red}', '</span>', $string);
        }else{
             $string = str_replace('{red}', '', $string);  $string = str_replace('{/red}', '', $string);
        }
        if(verifierFermeturesBBcodes($string, 'green')){
             $string = str_replace('{green}', '<span style="color: #037e00;">', $string);  $string = str_replace('{/green}', '</span>', $string);
        }else{
             $string = str_replace('{green}', '', $string);  $string = str_replace('{/green}', '', $string);
        }
        
        $string = str_replace('|', '&ensp;', $string);
        
    }
    
    return $string;
}

function bbcodes_player($string){
    /*
    * {player}pseudo{/player}
    */
    $innerBalise = "player";
    $count = substr_count($string, '{/' . $innerBalise . '}');
    $lastPositionStart = 0;
    for($i = 0; $i < $count; $i++){
        $pos = stripos($string, '{' . $innerBalise . '', $lastPositionStart);
        $lastPositionStart = $pos + 1;
        $sub = substr($string, $pos);
        
        // Paramètres
        $value = getBBCodeValue($sub, $innerBalise);
        $value = trim($value);
        
        // On trouve la position du début de la balise d'ouverture
        $beginPos = strpos($string, '{' . $innerBalise);
        $str1 = substr($string, 0, $beginPos);
        
        // Et le reste du string, tout de suite après la balise de fermeture
        $endPos = strpos($string, '{/' . $innerBalise . '}') + strlen('{/' . $innerBalise . '}');
        $str3 = substr($string, $endPos);
        
        // $str2 est notre transformation
        $id = getIDFromPseudo($value);
        $idGroupe = GetStrongestGroup($id);
        if($id == 0)
            $str2 = $value;
        else{
            $Color = GetNameColor($id);
            $str2 = '<a href="account.php?id=' . $id . '" style="' . (($Color == '') ? '' : 'color:' . $Color . ';') . ';">' . 
                '<img alt="" class="username_icon" src="images/icons/groups/' . GetIconGroup($idGroupe) . '"/>' .
                $value . '</a>';
        }
        
        $string = $str1.$str2.$str3;
    }
    
    return $string;
}

function smileys($string){
    /*
    * Formatte les textes spéciaux par des smileys
    */
    $string = str_replace(':a', '<img alt="" class="smiley" src="images/smileys/ange.png"/>', $string);
    $string = str_replace(':pirate:', '<img alt="" class="smiley" src="images/smileys/pirate.png"/>', $string);
    $string = str_replace('oO', '<img alt="" class="smiley" src="images/smileys/blink.gif"/>', $string);
    $string = str_replace('Oo', '<img alt="" class="smiley" src="images/smileys/blink.gif"/>', $string);
    $string = str_replace('o_O', '<img alt="" class="smiley" src="images/smileys/blink.gif"/>', $string);
    $string = str_replace('O_o', '<img alt="" class="smiley" src="images/smileys/blink.gif"/>', $string);
    $string = str_replace(';)', '<img alt="" class="smiley" src="images/smileys/clin.png"/>', $string);
    $string = str_replace(':devil:', '<img alt="" class="smiley" src="images/smileys/diable.png"/>', $string);
    $string = str_replace(':D', '<img alt="" class="smiley" src="images/smileys/heureux.png"/>', $string);
    $string = str_replace('^^', '<img alt="" class="smiley" src="images/smileys/hihi.png"/>', $string);
    $string = str_replace(':o', '<img alt="" class="smiley" src="images/smileys/huh.png"/>', $string);
    $string = str_replace(':P', '<img alt="" class="smiley" src="images/smileys/langue.png"/>', $string);
    $string = str_replace(':p', '<img alt="" class="smiley" src="images/smileys/langue.png"/>', $string);
    $string = str_replace(':mage:', '<img alt="" class="smiley" src="images/smileys/magicien.png"/>', $string);
    $string = str_replace('è_é', '<img alt="" class="smiley" src="images/smileys/mechant.png"/>', $string);
    $string = str_replace(':ninja:', '<img alt="" class="smiley" src="images/smileys/ninja.png"/>', $string);
    $string = str_replace('>_<', '<img alt="" class="smiley" src="images/smileys/pinch.png"/>', $string);
    $string = str_replace(':lol:', '<img alt="" class="smiley" src="images/smileys/rire.gif"/>', $string);
    $string = str_replace(':3', '<img alt="" class="smiley" src="images/smileys/rouge.png"/>', $string);
    $string = str_replace(':-*', '<img alt="" class="smiley" src="images/smileys/siffle.png"/>', $string);
    $string = str_replace(':)', '<img alt="" class="smiley" src="images/smileys/smile.png"/>', $string);
    $string = str_replace(':sun:', '<img alt="" class="smiley" src="images/smileys/soleil.png"/>', $string);
    $string = str_replace(':(', '<img alt="" class="smiley" src="images/smileys/triste.png"/>', $string);
    $string = str_replace(':s', '<img alt="" class="smiley" src="images/smileys/unsure.gif"/>', $string);
    $string = str_replace(':O', '<img alt="" class="smiley" src="images/smileys/waw.png"/>', $string);
    $string = str_replace(':zorro:', '<img alt="" class="smiley" src="images/smileys/zorro.png"/>', $string);
    
    return $string;
}

function verifierFermeturesBBcodes($string, $balise){
    /*
    * Fonction qui vérifie si les bbcodes ont bien étés fermés pour
    * le bbcode donné, dans le string donné. La fonction prend en compte
    * les balises qui contienent des paramètres.
    * Si oui, revoie true, sinon false
    */
    $debut = '{'.$balise;
    $debutCount = 0;
    $fin = '{/'.$balise.'}';
    $finCount = substr_count($string, $fin);
    
    // On trouve les balises de début
    $length = strlen($string);
    $lengthDebut = strlen($debut);
    $inParamValue = false;
    for($i = 0; $i < $length; $i++){
        if(substr($string, $i, $lengthDebut) == $debut){
            $temp = substr($string, $i);
            $tempLength = strlen($temp);
            for($j = 0; $j < $tempLength; $j++){
                $j1 = $j - 1;
                if($j1 < 0) $j1 = 0;
                
                if($temp[$j] == '"' && $temp[$j1] != '\\')
                    $inParamValue = !$inParamValue;
                
                if(!$inParamValue && $temp[$j] == '}'){
                    $debutCount++;
                    break;
                }
            }
        }
    }
    
    //echo '<br>$debutCount = ' . $debutCount . '<br>$finCount = ' . $finCount . '<br>';
    
    if($debutCount == $finCount){
        return true;
    }
    return false;
}

function getBBCodeParam($string, $param){
    /*
    * Retourne la valeur d'un paramètre dans le BBCode donné.
    * Ex.: pour {citation auteur="Totoila"}:
    * echo getBBCodeParam('{citation auteur="Totoila"}', 'auteur'); // outputs Totoila;
    */
    
    // On recherche d'abord le BBCode d'ouverture
    $string = stristr($string, '{');
    if($string === FALSE)
        return "";
    $string = stristr($string, '}', true);
    if($string === FALSE)
        return "";
    
    $string = substr($string, 1, strlen($string));
    
    // On trouve le paramètre
    $i = 0; $found = false; $length = strlen($string); $lengthParam = strlen($param); $inParamValue = false;
    while(!$found){
        if($string[$i] == "\"" && $string[($i - 1)] != "\\"){
            $inParamValue = !$inParamValue;
        }
        
        if(!$inParamValue){
            $tempString = substr($string, $i, $lengthParam);
            if($tempString == $param){
                $found = true;
                $string = substr($string, $i);
                $inParamValue = false;
                $return = "";
                for($j = 0; $j < strlen($string); $j++){
                    if($inParamValue && $string[$j] == '"' && $string[($j - 1)] != '\\'){
                        return $return;
                    }else if($string[$j] == '"' && $string[($j - 1)] != '\\'){
                        $inParamValue = true;
                    }else if($inParamValue)
                        $return .= $string[$j];
                }
            }
        }
        
        $i++;
        if($i == $length)
            $found = true;
    }
    
    return "";
}

function getBBCodeValue($string, $balise){
    /*
    * Retourne la valeur dans la balise
    */
    $sub = substr($string, 0, stripos($string, '{/' . $balise . '}')); // Supprime tout l'excédent incluant la balise de fermeture
    $len = strlen($sub); $inParamValue = false;
    for($j = 0; $j < $len; $j++){
        $j1 = $j - 1;
        if($j1 < 0) $j1 = 0;
        
        if($sub[$j] == '"' && $sub[$j1] != '\\')
            $inParamValue = !$inParamValue;
        
        if(!$inParamValue){
            if($sub[$j] == '}'){ // Fin de la balise d'ouverture
                $sub[$j] = '';
                break;
            }
        }
        
        $sub[$j] = '';
    }
    
    return $sub;
}

function BBCodesGetPosFinBaliseOuverture($sub){
    /*
    * Renvoie la position du '}', signifiant la fermeture
    * de la première balise
    */
    
    $return = 0;
    $inParamValue = false;
    for($j = 0; $j < strlen($sub); $j++){
        $j1 = $j - 1;
        if($j1 < 0) $j1 = 0;
        
        if($sub[$j] == '"' && $sub[$j1] != '\\')
            $inParamValue = !$inParamValue;
        
        if(!$inParamValue && $sub[$j] == '}'){
            return $j;
        }
    }
    
    // Aucune position trouvée
    return false;
}
?>