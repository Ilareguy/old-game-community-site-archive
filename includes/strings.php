<?php

if(!defined('__ROOT__'))
    define('__ROOT__', dirname(dirname(__FILE__)) . '/');

function PasswordCrypt($Password){
    /*
    * Cette fonctione retourne une string encryptée du $Password
    */
    
    return crypt($Password, '$1$lps354l2$');
    
}

function EnglishApostropheS($Str){
    echo ((strtolower($Str[(strlen($Str) - 1)]) == 's') ? "'" : "'s");
}

function EnglishGenderHis($Gender){
    if($Gender == 1)
        return 'his';
    return 'her';
}

function PasswordCryptUsersInDatabase(){
    /*
    * À utiliser seulement lors de l'application de la mise-à-jour
    * qui encrypte les mots de passe. La fonctione encrypte le mot 
    * de passe des tous les utilisateurs dans la base de données.
    */
    
    /*include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('SELECT pwd, id FROM comptes');
    $req->execute(array());
    
    while($donnees = $req->fetch()){
        
        $NewPwd = PasswordCrypt($donnees['pwd']);
        
        $req2 = $bdd->prepare('UPDATE comptes SET pwd=? WHERE id=?');
        $req2->execute(array($NewPwd, $donnees['id']));
        
        echo 'ID = ' . $donnees['id'] . ' encrypté pour : ' . $NewPwd . '<br />';
        
    }*/
}

function pecho($string, $length = 0, $endDots = true){
    /*
    * Fonction "intelligente" pour afficher une partie d'un String.
    * "pecho" pour partial echo.
    * La fonction s'assure de bien afficher le nombre de caractères souhaités
    * en prenant compte des entités HTML ("&eacute;" pour "é", par exemple).
    */
    if($length <= 0)
        echo $string;
    else if($length >= strlen(html_entity_decode($string))){
        echo $string;
    }else{
        $stringPos = 0;
        for($i = 0; $i < $length; $i++){
            if($string[$stringPos] == "&"){
                // Si on tombe sur un "&", il y a des chances 
                // que ce soit une entité HTML
                $tempString = substr($string, $stringPos, ($stringPos + 7));
                $tempStringDecoded = html_entity_decode($tempString);
                if($tempString == $tempStringDecoded){
                    // Pas un entité HTML, on affiche normalement
                    echo $string[$stringPos];
                    $stringPos++;
                }else{
                    // Entité HTML
                    $tempStringToEcho = htmlspecialchars($tempStringDecoded[0]);
                    echo $tempStringToEcho;
                    $stringPos += strlen($tempStringToEcho);
                }
            }else{
                echo $string[$stringPos];
                $stringPos++;
            }
        }
        if($endDots)
            echo ' [...]';
    }
}

function utf8_decode_array($array){
    $return = $array;
    foreach($array as $key => $v){
        if(is_string($v))
            $v = utf8_decode($v);
        $return[$key] = $v;
    }
    return $return;
}

function utf8_encode_array($array){
    $return = $array;
    foreach($array as $key => $v){
        if(is_string($v))
            $v = utf8_encode($v);
        $return[$key] = $v;
    }
    return $return;
}

function SecureHTML($str, 
                    $AllowedTags = array("a", "p", "h1", "h2", "h3", "h4", "h5", "h6", "strong", "i", "span", "div", "br", 
                                         "hr", "img", "ul", "ol", "li", "b", "em"),
                    $AllowedAttributes = array("style", "class", "src", "alt", "title", "width", "height")){
    /*
    * La fonction retourne une String transformée
    * avec seulement les tags et attributs valides
    * donnés.
    * La fonction fait comme strip_tags(), mais elle
    * fonctionne mieux (moins de problèmes) et elle
    * gère les attributs.
    */
    
    function SecureHTML__IsTagValid($tag, $AllowedTags){
        
        $Count = count($AllowedTags);
        for($i = 0; $i < $Count; $i++){
            if($tag == $AllowedTags[$i])
                return true;
        }
        
        return false;
        
    }
    
    function SecureHTML__IsAttributeValid($Attribute, $AllowedAttributes){
        
        $Count = count($AllowedAttributes);
        for($i = 0; $i < $Count; $i++){
            if($Attribute == $AllowedAttributes[$i])
                return true;
        }
        
        return false;
        
    }
    
    $StrLen = strlen($str);
    $IsInTagDefinition = false;     /* Définit si oui ou non on est dans un tag 
                                    (e.g.: dans <a*> (à la position de l'étoile))*/
    $CompleteTag = "";
    $TagDefinitionBegin = 0; $TagDefinitionEnd = 0;
    $TagFound = false;
    $IsInQuote = false;
    $IsInTag = false;
    
    for($i = 0; $i < $StrLen; $i++){
        
        if(!$IsInTag && $str[$i] == '<'){
            // Début du tag
            $IsInTagDefinition = true;
            $TagDefinitionBegin = $i;
        }else if(!$IsInQuote && $str[$i] == '>'){
            $IsInTagDefinition = false;
            $TagDefinitionEnd = $i;
            $TagFound = true;
            $CompleteTag .= $str[$i];
        }else if(!$IsInQuote && $str[$i] == '"'){
            $IsInQuote = true;
        }else if($IsInQuote && $str[$i] == '"' && $str[($i - 1)] != "\\"){
            $IsInQuote = false;
        }
        
        if($IsInTagDefinition)
            $CompleteTag .= $str[$i];
            
        if($TagFound){
            // Si on entre ici, c'est qu'on vient d'identifier un tag
            $CompleteTag = trim($CompleteTag);
            $Tag = preg_filter("/[^A-Z^a-z^0-9 ]/", "", $CompleteTag);
            $Tag = explode(' ', $Tag);
            $Tag = trim($Tag[0]); // On a trouvé le tag
            
            if(!SecureHTML__IsTagValid($Tag, $AllowedTags)){
                // On doit enlever le tag au complet
                $CompleteTag = str_replace("<", "&lt;", $CompleteTag);
                $CompleteTag = str_replace(">", "&gt;", $CompleteTag);
                $str = substr_replace($str, $CompleteTag, $TagDefinitionBegin, (($TagDefinitionEnd - $TagDefinitionBegin) + 1));
                $str = str_ireplace("</$Tag>", "&lt;/$Tag&gt;", $str);
                $StrLen = strlen($str);
                $i = $TagDefinitionBegin;
            }else{
            
                // On vérifie les attributs du tag
                $_StrLen = strlen($CompleteTag);
                $IsInAttribute = false;
                $CompleteAttribute = "";
                $IsInAttributeQuote = false;
                $AttributeFound = false;
                $AttributeStart = 0;
                $FirstSpacePassed = false;
                
                for($j = 0; $j < $_StrLen; $j++){
                    
                    if($FirstSpacePassed){
                        if(!$IsInAttribute && $CompleteTag[$j] != ' ' && $CompleteTag[$j] != '<' && 
                            $CompleteTag[$j] != '>' && $CompleteTag[($j - 1)] != '<'){
                            $IsInAttribute = true; // On a trouvé le commencement d'un attribut
                            $AttributeStart = $j;
                        }else if($IsInAttribute && !$IsInAttributeQuote && $CompleteTag[$j] == '"'){
                            $IsInAttributeQuote = true; // On a trouvé le commencement d'une quote et on est dans un attribut
                        }else if($IsInAttribute && $IsInAttributeQuote && $CompleteTag[$j] == '"' && $CompleteTag[($j - 1)] != "\\"){
                            $IsInAttributeQuote = false; // On a trouvé la fin d'un attribut
                            $AttributeFound = true;
                            $CompleteAttribute .= $CompleteTag[$j];
                        }
                        
                        if($IsInAttribute)
                            $CompleteAttribute .= $CompleteTag[$j];
                    }else if($CompleteTag[$j] == ' '){
                        $FirstSpacePassed = true;
                    }
                        
                    
                    if($AttributeFound){
                        // On a terminé d'analyser un attribut; on le traite
                        $Attribute = substr($CompleteAttribute, 0, (strpos($CompleteAttribute, '=')));
                        
                        if(!SecureHTML__IsAttributeValid($Attribute, $AllowedAttributes)){
                            // Attribut non valide; on l'enlève
                            $StrToClear = substr($str, ($TagDefinitionBegin + $AttributeStart - 1), strlen($CompleteAttribute));
                            
                            $str = str_ireplace($StrToClear, '', $str);
                            $StrLen = strlen($str);
                            
                            $CompleteTag = str_ireplace($StrToClear, '', $CompleteTag);
                            $_StrLen = strlen($CompleteTag);
                            
                            $j = 0; // On remet le curseur au début
                            $i -= (strlen($StrToClear));
                        }
                        
                        $IsInAttribute = false;
                        $CompleteAttribute = "";
                        $IsInAttributeQuote = false;
                        $AttributeFound = false;
                        $AttributeStart = 0;
                    }
                }
                
            }
            
            $CompleteTag = "";
            $TagDefinitionBegin = 0; $TagDefinitionEnd = 0;
            $TagFound = false;
            $IsInQuote = false;
            $IsInTag = false;
        }
        
    }

    return $str;
}

?>