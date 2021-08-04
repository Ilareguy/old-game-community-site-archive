<?php
/*
* Contient toutes les fonctions nécéssaires pour gérer les forums
* (section, droits par défaut et plus).
*
* CRÉÉ LE 3 NOVEMBRE 2011
**********************************************
* Functions to manage forums (rights, privacy settings, sections, etc.).
*
* CREATED ON NOVEMBER 3, 2011
*/

function getForums(){
    /*
    * Le nom dit tout: renvoie un array contenant toutes les sections des forums
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT id FROM forums ORDER BY priorityOrder DESC, nom');
    $req->execute(array());
    
    $count = 0;
    $return = Array();
    
    while($donnees = $req->fetch()){
        $return[$count] = $donnees;
        $count ++;
    }
    
    $req->closeCursor();
    return $return;
}

function getForumThreads($idForum){
    /*
    * Renvoie tous les threads de la section donnée en paramètre
    */
    include(__ROOT__."includes/bddConnect.php");
    if(!function_exists('getForumThreads_custom_sort')){
        function getForumThreads_custom_sort($a,$b) {
            if($a['LastPostTimestamp'] == $b['LastPostTimestamp'])
                return 0;
            
            return ($a['LastPostTimestamp'] > $b['LastPostTimestamp']) ? -1 : 1;
        }
    }
    
    $return = Array();
    $count = 0;
    
    $req = $bdd->prepare('SELECT id FROM forums_threads WHERE idForum=? ');
    $req->execute(array($idForum));
    
    while($donnees = $req->fetch()){
    
        $return[$count] = $donnees;
        $req2 = $bdd->prepare('SELECT MAX(time) as Timestamp FROM forums_messages WHERE idThread=?');
        $req2->execute(array($donnees['id']));
        if($donnees2 = $req2->fetch())
            $return[$count]['LastPostTimestamp'] = $donnees2['Timestamp'];
        else
            $return[$count]['LastPostTimestamp'] = 0;
        
        $count++;
        
    }
    
    $req->closeCursor();
    if($count == 0)
        return false;
    
    usort($return, "getForumThreads_custom_sort");
     
    return $return;
}

function getForumThread($id){
    /*
    * Renvoie toutes les informations sous forme d'un Array sur
    * le thread demandé
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT * FROM forums_threads WHERE id=? ');
    $req->execute(array($id));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return $donnees;
    }
    
    $req->closeCursor();
    return false;
}

function threadGetNbAnswers($idThread){
    /*
    * Renvoie le nombre de réponses dans le thread
    * donné en paramètre
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT COUNT(*) AS nb FROM forums_messages WHERE idThread=? ');
    $req->execute(array($idThread));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return $donnees['nb'];
    }
    
    $req->closeCursor();
    return false;
}

function forumGetLastThread($idForum){
    /**
    * Retourne l'ID du dernier thread dans la section
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('SELECT id FROM forums_threads WHERE idForum=? ORDER BY timestamp DESC');
    $req->execute(array($idForum));
    
    
    if($donnees = $req->fetch()){
        
        $req->closeCursor();
        return $donnees['id'];
        
    }
    
    $req->closeCursor();
    return false;
    
}

function forumGetLastMessage($IDForum){
    /*
    * Retourne le dernier message posté dans la section donnée
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    // On sélectionne tous les thread de la section
    $threads = array();
    $CurrentCount = 0;
    $req = $bdd->prepare('SELECT id FROM forums_threads WHERE idForum=?');
    $req->execute(array($IDForum));
    
    while($donnees = $req->fetch()){
        $threads[$CurrentCount] = $donnees['id'];
        $CurrentCount++;
    }
    $req->closeCursor();
    
    // On sélectionne le plus récent message de tous ces threads
    $messages = array();
    for($i = 0; $i < count($threads); $i++){
    
        $req = $bdd->prepare('SELECT * FROM forums_messages WHERE idThread=? ORDER BY time DESC');
        $req->execute(array($threads[$i]));
        
        if($donnees = $req->fetch()){
            $messages[count($messages)] = $donnees;
        }
        
        $req->closeCursor();
    
    }
    
    // On retourne le message le plus ancien
    $count = count($messages);
    if($count == 0)
        return false;
    $latest = $messages[0];
    for($i = 0; $i < $count; $i++){
        if($messages[$i]['time'] > $latest['time'])
            $latest = $messages[$i];
    }
    
    return $latest;
    
}

function forumThreadGetLastAnswer($idThread){
    /*
    * Renvoie les informations sur le dernier message du thread donné
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT * FROM forums_messages WHERE idThread=? ORDER BY id DESC ');
    $req->execute(array($idThread));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return $donnees;
    }
    
    $req->closeCursor();
    return false;
}

function forumThreadGetFirstAnswer($idThread){
    /*
    * Renvoie les informations sur le dernier message du thread donné
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT * FROM forums_messages WHERE idThread=? ORDER BY id ');
    $req->execute(array($idThread));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return $donnees;
    }
    
    $req->closeCursor();
    return false;
}

function getForumNbThreads($idForum){
    /**
    * Retourne le nombre de threads dans la section
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('SELECT COUNT(*) AS nb FROM forums_threads WHERE idForum=? ');
    $req->execute(array($idForum));
    
    if($donnees = $req->fetch()){
        
        $req->closeCursor();
        return $donnees['nb'];
        
    }
    
    $req->closeCursor();
    return false;
}

function getForumNbPosts($idForum){
    /**
    * Retourne le nombre de messages dans la section
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $threads = getForumThreads($idForum);
    $count = count($threads);
    $nb = 0;
    
    for($i = 0; $i < $count; $i++){
        
        $req = $bdd->prepare('SELECT COUNT(*) AS nb FROM forums_messages WHERE idThread=? ');
        $req->execute(array($threads[$i]['id']));
        
        if($donnees = $req->fetch()){
            $nb += $donnees['nb'];
        }
        
        $req->closeCursor();
        
    }
    
    return $nb;
    
}

function forumGetThreadCreator($idThread){
    /*
    * Renvoie l'id et le pseudo du compte qui a créé le thread
    * donné en paramètre
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $return = Array();
    $found = false;
    
    $req = $bdd->prepare('SELECT idCompte FROM forums_messages WHERE idThread=? ');
    $req->execute(array($idThread));
    
    if($donnees = $req->fetch()){
        $return['id'] = $donnees['idCompte'];
        
        $req = $bdd->prepare('SELECT pseudo FROM comptes WHERE id=? ');
        $req->execute(array($donnees['idCompte']));
        if($donnees = $req->fetch()){
            $found = true;
            $return['pseudo'] = $donnees['pseudo'];
        }
    }
    
    $req->closeCursor();
    
    if($found)
        return $return;
    return false;
}

function forumGetThreadCreatorPseudo($idThread){
    /*
    * Renvoie l'id et le pseudo du compte qui a créé le thread
    * donné en paramètre
    */
    include(__ROOT__."includes/bddConnect.php");
    
    // On récupère le dernier message
    $msg = forumThreadGetFirstAnswer($idThread);
    // On détermine le bon pseudo à afficher
    return forumGetDisplayNameFromMessage($msg);
}

function getForum($id){
    /*
    * Renvoie toutes les informations sous forme d'un Array sur
    * le forum demandé. S'il n'est pas trouvé, la fonction renvoie false.
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT * FROM forums WHERE id=? ');
    $req->execute(array($id));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return $donnees;
    }
    
    $req->closeCursor();
    return false;
}

function forumExists($idForum){
    /*
    * Enregistre le nom du forum donné en paramètre
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT id FROM forums WHERE id=? ');
    $req->execute(array($idForum));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return true;
    }
    
    $req->closeCursor();
    return false;
}

function forumThreadExists($idForum){
    /*
    * Enregistre le nom du forum donné en paramètre
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT id FROM forums_threads WHERE id=? ');
    $req->execute(array($idForum));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return true;
    }
    
    $req->closeCursor();
    return false;
}

function forumSaveName($idForum, $nom){
    /*
    * Enregistre le nom du forum donné en paramètre
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('UPDATE forums SET nom=? WHERE id=? ');
    $req->execute(array(htmlspecialchars(stripslashes($nom)), $idForum));
    
    $req->closeCursor();
    return true;
}

function forumSaveColor($idForum, $couleur){
    /*
    * Enregistre la couleur du forum donné en paramètre
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $couleur = str_replace("#", "", $couleur);
    $req = $bdd->prepare('UPDATE forums SET couleur=? WHERE id=? ');
    $req->execute(array($couleur, $idForum));
    
    $req->closeCursor();
    return true;
}

function forumDefaultVisibility($idForum, $visibility){
    /*
    * Enregistre la visibilité par défaut du forum donné en paramètre
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('UPDATE forums SET voirDefaut=? WHERE id=? ');
    $req->execute(array($visibility, $idForum));
    
    $req->closeCursor();
    return true;
}

function forumEditGroupVisibility($idForum, $idGroupe, $visibility){
    /*
    */
    include(__ROOT__."includes/bddConnect.php");
    
    if($visibility == 1 || $visibility == 2){
        $req = $bdd->prepare('DELETE FROM forums_groupes_visualisations WHERE idGroupe=? AND idForum=? ');
        $req->execute(array($idGroupe, $idForum));
        $req = $bdd->prepare('INSERT INTO forums_groupes_visualisations (idGroupe, idForum) VALUES (:idGroupe, :idForum) ');
        $req->execute(array(
            ":idGroupe" => $idGroupe,
            ":idForum" => $idForum
        ));
        if($visibility == 2){
            // Modérateur!
            $req = $bdd->prepare('DELETE FROM droits_groupes WHERE idGroupe=? AND droit=? ');
            $req->execute(array($idGroupe, 'moderatorForum_'.$idForum));
            $req = $bdd->prepare('INSERT INTO droits_groupes (idGroupe, droit, valeur) 
            VALUES (:idGroupe, \'moderatorForum_' . $idForum . '\', \'1\')');
            $req->execute(array(
                ':idGroupe' => $idGroupe
            ));
        }
    }else{
        $req = $bdd->prepare('DELETE FROM forums_groupes_visualisations WHERE idGroupe=? AND idForum=? ');
        $req->execute(array($idGroupe, $idForum));
        $req = $bdd->prepare('DELETE FROM droits_groupes WHERE idGroupe=? AND droit=? ');
        $req->execute(array($idGroupe, 'moderatorForum_'.$idForum));
    }
    
    $req->closeCursor();
    return true;
}

function forumCreateSection($nom, $couleur = "#18709e", $voirDefaut = 1){
    /*
    * Crée une section dans le forum
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $couleur = str_replace("#", "", $couleur);
    if($nom == "")
        return false;
    
    $req = $bdd->prepare('INSERT INTO forums (nom, couleur, voirDefaut) VALUES (:nom, :couleur, :voirDefaut) ');
    $req->execute(array(
        ":nom" => htmlspecialchars(stripslashes($nom)),
        ":couleur" => $couleur,
        ":voirDefaut" => $voirDefaut
    ));
    
    $req->closeCursor();
    return true;
}

function forumDeleteSection($id){
    /*
    * Supprime une section du forum
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('DELETE FROM forums WHERE id=?  ');
    $req->execute(array($id));
    
    $req = $bdd->prepare('SELECT id FROM forums_threads WHERE idForum=? ');
    $req->execute(array($id));
    while($donnees = $req->fetch()){
        $req2 = $bdd->prepare('DELETE FROM forums_messages WHERE idThread=? ');
        $req2->execute(array($donnees['id']));
        $req2->closeCursor();
    }
    $req = $bdd->prepare('DELETE FROM forums_threads WHERE idForum=? ');
    $req->execute(array($id));
    
    $req = $bdd->prepare('DELETE FROM droits_groupes WHERE droit=? ');
    $req->execute(array('moderatorForum_'.$id));
    
    $req->closeCursor();
    return true;
}

function forumCreateThread($idCompte, $titre, $message, $idForum, $couleur = "#"){
    /*
    */
    include(__ROOT__."includes/bddConnect.php");
    
    // Insertion du thread
    $req = $bdd->prepare('INSERT INTO forums_threads (nom, couleur, timestamp, idForum) 
    VALUES (:nom, :couleur, :timestamp, :idForum) ');
    $req->execute(array(
        ":nom" => htmlspecialchars(stripslashes($titre)),
        ":couleur" => $couleur,
        ":timestamp" => time(),
        ":idForum" => $idForum
    ));
    
    // On récupère l'id du nouveau thread
    $req = $bdd->prepare('SELECT MAX(id) AS id FROM forums_threads WHERE idForum=? ');
    $req->execute(array($idForum));
    $idThread = $req->fetch();
    
    // Insertion du message
    $req = $bdd->prepare('INSERT INTO forums_messages (idCompte, time, message, idThread) 
    VALUES (:idCompte, :time, :message, :idThread) ');
    $req->execute(array(
        ":idCompte" => $idCompte,
        ":time" => time(),
        ":message" => htmlspecialchars(stripslashes($message)),
        ":idThread" => $idThread['id']
    ));
    
    $req->closeCursor();
    return true;
}

function forumThreadGetMessages($idThread, $nb = 25, $from = 0){
    /*
    * Renvoie un array contenant les messages d'un thread spécifique.
    * Deux paramètres optionels:
    * $nb = le nombre de message;
    * $from = nombre de messages à ignorer;
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $count = 0;
    $countIgnore = 0;
    $return = Array();
    
    $req = $bdd->prepare('SELECT * FROM forums_messages WHERE idThread=? ');
    $req->execute(array($idThread));
    while($count < $nb && $donnees = $req->fetch()){
        if($countIgnore < $from){
            $countIgnore++;
        }else{
            $return[$count] = $donnees;
            $count++;
        }
    }
    
    $req->closeCursor();
    return $return;
}

function forumThreadGetMessage($ID){
    /*
     * Retourne toutes les informations sur le message demandé
     */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('SELECT * FROM forums_messages WHERE id=?');
    $req->execute(array($ID));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return $donnees;
    }
    
    $req->closeCursor();
    return false;
}

function forumGetMessagesEdits($idMessage){
    /*
    * Renvoie une liste qui contient les informations sur
    * les éditions sur le message donné en paramètre
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $count = 0;
    $return = Array();
    
    $req = $bdd->prepare('SELECT * FROM forums_messages_edit WHERE idMessage=? ');
    $req->execute(array($idMessage));
    while($donnees = $req->fetch()){
        $return[$count] = $donnees;
        $count++;
    }
    
    $req->closeCursor();
    
    if($count == 0)
        return false;
    return $return;
}

function forumGetMessage(){
    /*
    */
    include(__ROOT__."includes/bddConnect.php");

    $req = $bdd->prepare('SELECT * FROM forums_messages WHERE id=? ');
    $req->execute(array($idMessage));
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return $donnees;
    }

    $req->closeCursor();
    return false;
}

function forumThreadPostAnswer($message, $thread, $idCompte){
    /*
    * Entre un message dans le thread donné en paramètre
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('INSERT INTO forums_messages (idCompte, message, time, idThread) 
        VALUES (:idCompte, :message, :time, :idThread)');
    $req->execute(array(
        ":idCompte" => $idCompte,
        ":message" => htmlspecialchars(stripslashes($message)),
        ":time" => time(),
        ":idThread" => $thread
    ));
    
    $req->closeCursor();
    return true;
}

function forumThreadEditColor($id, $color){
    /*
    * Modifie la couleur du thread donné en paramètre
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $color = str_replace("#", "", $color);
    
    if($color == "")
        $color = "#";
        
    $req = $bdd->prepare('UPDATE forums_threads SET couleur=? WHERE id=? ');
    $req->execute(array($color, $id));
    
    $req->closeCursor();
    return true;
}

function forumThreadEditLabelColor($id, $color){
    /*
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $color = str_replace("#", "", $color);
    
    if($color == "")
        $color = "default";
    if($color == "default")
        $color = "deeefa";
        
    $req = $bdd->prepare('UPDATE forums_threads SET labelColor=? WHERE id=? ');
    $req->execute(array($color, $id));
    
    $req->closeCursor();
    return true;
}

function forumThreadEditLabel($id, $label){
    /*
    */
    include(__ROOT__."includes/bddConnect.php");
        
    $req = $bdd->prepare('UPDATE forums_threads SET label=? WHERE id=? ');
    $req->execute(array(htmlspecialchars(stripslashes($label)), $id));
    
    $req->closeCursor();
    return true;
}

function forumThreadLock($id){
    /*
    */
    include(__ROOT__."includes/bddConnect.php");
        
    $req = $bdd->prepare('UPDATE forums_threads SET locked=\'1\' WHERE id=? ');
    $req->execute(array($id));
    
    $req->closeCursor();
    return true;
}

function forumThreadUnlock($id){
    /*
    */
    include(__ROOT__."includes/bddConnect.php");
        
    $req = $bdd->prepare('UPDATE forums_threads SET locked=\'0\' WHERE id=? ');
    $req->execute(array($id));
    
    $req->closeCursor();
    return true;
}

function forumThreadChangeIcon($id, $icon){
    /*
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('UPDATE forums_threads SET icon=? WHERE id=? ');
    $req->execute(array($icon, $id));
    
    $req->closeCursor();
    return true;
}

function forumEditMessage($idMessage, $newMessage, $idCompte){
    /*
    * Éditer un message du forum
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('UPDATE forums_messages SET message=? WHERE id=? ');
    //$newMessage = htmlentities($newMessage);
    $req->execute(array(htmlspecialchars(stripslashes($newMessage)), $idMessage));
    
    $req = $bdd->prepare('DELETE FROM forums_messages_edit WHERE idCompte=? AND idMessage=? ');
    $req->execute(array($idCompte, $idMessage));
    
    $req = $bdd->prepare('INSERT INTO forums_messages_edit (idCompte, idMessage, timestamp) 
        VALUES (:idCompte, :idMessage, :timestamp)');
    $req->execute(array(
        ":idCompte" => $idCompte,
        ":idMessage" => $idMessage,
        ":timestamp" => time()
    ));
    
    $req->closeCursor();
    return true;
}

function forumRemoveThread($id){
    /*
    * Supprime un thread au complet
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('DELETE FROM forums_threads WHERE id=? ');
    $req->execute(array($id));
    
    $req = $bdd->prepare('DELETE FROM forums_messages WHERE idThread=? ');
    $req->execute(array($id));
    
    $req->closeCursor();
    return true;
}

function forumRemoveMessage($id){
    /*
    * Supprime un message du forum
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('DELETE FROM forums_messages_edit WHERE idMessage=? ');
    $req->execute(array($id));
    
    $req = $bdd->prepare('DELETE FROM forums_messages WHERE id=? ');
    $req->execute(array($id));
    
    $req->closeCursor();
    return true;
}

function forumGetDisplayNameFromMessage($message){
    /*
     * Retourne le nom formatté pour le message donné.
     * @$message est un tableau qui doit contenir au moins:
     * ['idCompte'], ['DeletedUserPseudo']
     */
    require_once(__ROOT__.'includes/comptes.php');
    require_once(__ROOT__.'includes/bbcodes.php');
    
    $Pseudo = getPseudo($message['idCompte']);
    if($Pseudo == "" || $Pseudo === false)
        $Pseudo = $message['DeletedUserPseudo'];
    else
        $Pseudo = bbcodes_player('{player}' . $Pseudo . '{/player}');
    return $Pseudo;
}

?>