<?php
/*
* Comprend les fonctions pour manipuler les comptes
* (connexion, inscription, etc.)
*
* CRÉÉ LE 10 OCTOBRE 2011
*
*************************
*
* Contains the functions to handle the website accounts
* (login, subscription, etc.)
*
* CREATED ON OCTOBER 10, 2011
*/
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');

/*
 * Includes
 */
require_once(__ROOT__.'includes/strings.php');
require_once(__ROOT__.'includes/groupes.php');

function login($login, $pwd){
    /*
    * Connexion
    */
    include(__ROOT__."includes/bddConnect.php");
    $login = trim($login);
    $pwd = PasswordCrypt($pwd);
    
    // Premier essaie, on vérifie s'il essaie de se connecter avec son pseudo
    $req = $bdd->prepare('SELECT id FROM comptes WHERE pseudo=? AND BINARY pwd=? ');
    $req->execute(array($login, $pwd));
    if($donnees = $req->fetch()){
        // SESSIONS ICI
        $_SESSION['id'] = $donnees['id'];
        $_SESSION['connecte'] = true;
        // Mise à jour des données de connexion
        $req = $bdd->prepare('UPDATE comptes SET lastLogin=?, lastLoginIP=? WHERE id=? ');
        $req->execute(array(time(), $_SERVER['REMOTE_ADDR'], $donnees['id']));
        // Terminé
        $req->closeCursor();
        return $donnees['id'];
    }
    
    // Deuxième essaie, on vérifie s'il essaie de se connecter avec son email
    $req = $bdd->prepare('SELECT id FROM comptes WHERE email=? AND BINARY pwd=? ');
    $req->execute(array($login, $pwd));
    if($donnees = $req->fetch()){
        // SESSION ICI
        $_SESSION['id'] = $donnees['id'];
        $_SESSION['connecte'] = true;
        // Mise à jour des données de connexion
        $req = $bdd->prepare('UPDATE comptes SET lastLogin=?, lastLoginIP=? WHERE id=? ');
        $req->execute(array(time(), $_SERVER['REMOTE_ADDR'], $donnees['id']));
        // Terminé
        $req->closeCursor();
        return $donnees['id'];
    }
    
    // Si on arrive ici, c'est qu'on a trouvé aucune entré avec les identifiants donnés
    $req->closeCursor();
    return false;
}

function getCompteGroupes($idCompte){
    if(!function_exists("sortByPriority")){
        function sortByPriority($a, $b){
            return strnatcmp($a['priority'], $b['priority']);
        }
    }
    /*
    * Retourne un Array contenant tous les groupes du compte donné en paramètre.
    * L'array est classé selon l'ordre de priorité des groupes (DESC)
    */
    include(__ROOT__."includes/bddConnect.php");
    require_once(__ROOT__."includes/groupes.php");
    
    $count = 0;
    $groupes = Array();
    
    $req = $bdd->prepare('SELECT idGroupe FROM comptes_groupes WHERE idCompte=? ');
    $req->execute(array($idCompte));
    while($donnees = $req->fetch()){
        $groupes[$count] = getGroupe($donnees['idGroupe']);
        $count ++;
    }
    
    $req->closeCursor();
    if($count > 0){
        // On les classe selon ordre de priorité
        usort($groupes, 'sortByPriority');
        return array_reverse($groupes);
    }
    return false;
}

function getCompte($id){
    /*
    * Retourne toutes les informations sur le compte donné
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $returnArray = Array();
    
    $req = $bdd->prepare('SELECT * FROM comptes WHERE id=? ');
    $req->execute(array($id));
    if($donnees = $req->fetch()){
        $returnArray = $donnees;
        $returnArray['groupes'] = getCompteGroupes($id);
        $returnArray['Description'] = utf8_encode($donnees['Description']);
        
        $req->closeCursor();
        return $returnArray;
    }
    
    $req->closeCursor();
    return false;
}

function compteChangerPwd($idCompte, $nouveauPwd){
    /*
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('UPDATE comptes SET pwd=? WHERE id=? ');
    $req->execute(array(PasswordCrypt($nouveauPwd), $idCompte));
    
    $req->closeCursor();
    return true;
}

function getPseudo($idCompte){
    /*
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT pseudo FROM comptes WHERE id=? ');
    $req->execute(array($idCompte));
    
    $donnees = $req->fetch();
    
    $req->closeCursor();
    return $donnees['pseudo'];
}

function compteIdExists($ID){
	/*
	 * Vérifie si l'ID existe
	 */
	include(__ROOT__.'includes/bddConnect.php');
	
	$req = $bdd->prepare('SELECT id FROM comptes WHERE id=?');
	$req->execute(array($ID));
	
	if($donnees = $req->fetch()){
		$req->closeCursor();
		return true;
	}
	
	$req->closeCursor();
	return false;
}

function comptePseudoExists($pseudo){
    /*
    * Vérifie si un pseudo existe
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT id FROM comptes WHERE pseudo=? ');
    $req->execute(array($pseudo));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return true;
    }
    
    $req->closeCursor();
    return false;
}

function creerCompte($pseudo, $pwd, $email, $firstName, $lastName){
    /*
    * Entre un nouveau compte dans la BDd
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $pseudo = trim($pseudo);
    $email = trim($email);
    $firstName = trim($firstName);
    $lastName = trim($lastName);
    
    $req = $bdd->prepare('INSERT INTO comptes (pseudo, pwd, email, lastName, firstName) VALUES 
        (:pseudo, :pwd, :email, :lastName, :firstName)');
    $req->execute(array(
        ":pseudo" => $pseudo,
        ":pwd" => PasswordCrypt($pwd),
        ":email" => $email,
        ":lastName" => $lastName,
        ":firstName" => $firstName
    ));
    
    // On ajoute le nouveau compte dans le groupe par défaut
    $req = $bdd->prepare('SELECT id FROM comptes WHERE email=? ORDER BY id DESC');
    $req->execute(array($email));
    $donnees = $req->fetch();
    $req = $bdd->prepare('INSERT INTO comptes_groupes (idCompte, idGroupe) VALUES (:idCompte, :idGroupe) ');
    $req->execute(array(
        ":idCompte" => $donnees['id'],
        ":idGroupe" => 1
    ));
    
    $req->closeCursor();
    return true;
}

function compteEmailExists($email){
    /*
    * Vérifie si un email existe
    */
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT id FROM comptes WHERE email=? ');
    $req->execute(array($email));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return true;
    }
    
    $req->closeCursor();
    return false;
}

function getIdLastNewSeen($idCompte){
    /*
    * Retourne l'ID de la dernière nouvelle que le compte
    * donné en paramètre a vu
    */
    
    include(__ROOT__."includes/bddConnect.php");
    
    $req = $bdd->prepare('SELECT idLastNewSeen FROM comptes WHERE id=? ');
    $req->execute(array($idCompte));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return $donnees['idLastNewSeen'];
    }
    
    $req->closeCursor();
    return false;
}

function getNbNewNews($idCompte){
	/*
	* Vérifie la BDD s'il y a une news posté que le compte
	* n'a pas encore vu
	*/
	include(__ROOT__."includes/bddConnect.php");
	require_once(__ROOT__."includes/news.php");
	
	$idLastNewSeen = getIdLastNewSeen($idCompte);
	
	$req = $bdd->prepare('SELECT COUNT(*) AS nb FROM news WHERE id > ?');
	$req->execute(array($idLastNewSeen));
	$donnees = $req->fetch();
	
	$req->closeCursor();
	return $donnees['nb'];
}

function updateIDLastNewSeen($idCompte){
    /**
    * Update l'ID de la dernière nouvelle lue par le compte donné.
    * La nouvelle valeur sera l'ID de la dernière nouvelle
    */
    include(__ROOT__.'includes/bddConnect.php');
    require_once(__ROOT__.'includes/news.php');
    
    
    $req = $bdd->prepare('UPDATE comptes SET idLastNewSeen=? WHERE id=? ');
    $req->execute(array(getIdLastNews(), $idCompte));
    
    return true;
}

function searchAccounts($query, $searchEmail = true, $searchFirstName = false, $searchLastName = false){
    /**
    * Retourne un array content l'ID de tous les comptes
    * trouvés, selon $q
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $sql = "SELECT DISTINCT(id) FROM comptes WHERE pseudo LIKE '%" . $query . "%' ";
    $return = Array();
    $count = 0;
    
    if($searchEmail){
        $sql .= " OR email LIKE '%" . $query . "%'";
    }
    
    if($searchFirstName){
        $sql .= " OR firstName LIKE '%" . $query . "%'";
    }
    
    if($searchLastName){
        $sql .= " OR lastName LIKE '%" . $query . "%'";
    }
    
    $req = $bdd->prepare($sql);
    $req->execute(array());
    
    while($donnees = $req->fetch()){
        $return[$count] = $donnees['id'];
        $count++;
    }
    
    $req->closeCursor();
    return $return;
}

function compteChangerPseudo($id, $pseudo){
    /*
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('UPDATE comptes SET pseudo=? WHERE id=? ');
    $req->execute(array($pseudo, $id));
    
    $req->closeCursor();
    return true;
}

function compteChangerEmail($id, $email){
    /*
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    if(!preg_match("#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#", $email))
        return false;
    
    $req = $bdd->prepare('UPDATE comptes SET email=? WHERE id=? ');
    $req->execute(array($email, $id));
    
    $req->closeCursor();
    return true;
}

function compteChangerFirstName($id, $firstName){
    /*
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $firstName = trim($firstName);
    $firstName = str_replace('"', '', $firstName);
    $firstName = str_replace("'", "", $firstName);
    
    $req = $bdd->prepare('UPDATE comptes SET firstName=? WHERE id=? ');
    $req->execute(array($firstName, $id));
    
    $req->closeCursor();
    return true;
}

function compteChangerLastName($id, $lastName){
    /*
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $lastName = trim($lastName);
    $lastName = str_replace('"', '', $lastName);
    $lastName = str_replace("'", "", $lastName);
    
    $req = $bdd->prepare('UPDATE comptes SET lastName=? WHERE id=? ');
    $req->execute(array($lastName, $id));
    
    $req->closeCursor();
    return true;
}

function compteBan($id){
    /*
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('UPDATE comptes SET banned=\'1\' WHERE id=? ');
    $req->execute(array($id));
    
    $req->closeCursor();
    return true;
}

function compteUnban($id){
    /*
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('UPDATE comptes SET banned=\'0\' WHERE id=? ');
    $req->execute(array($id));
    
    $req->closeCursor();
    return true;
}

function compteIsBanned($id){
    /*
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('SELECT banned FROM comptes WHERE id=? ');
    $req->execute(array($id));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        if($donnees['banned'] == 1)
            return true;
        return false;
    }
    
    $req->closeCursor();
    return false;
}

function getLastConnexions($combien = 20){
    /**
    * Retourne l'ID des $combien derniers comptes qui se sont
    * connectés
    */
    include(__ROOT__.'includes/bddConnect.php');
    $return = Array();
    $count = 0;
    
    $req = $bdd->prepare('SELECT id FROM comptes WHERE lastLogin>\'0\' ORDER BY lastLogin DESC');
    $req->execute(array());
    
    while($count < $combien && $donnees = $req->fetch()){
        $return[$count] = $donnees['id'];
        $count++;
    }
    
    $req->closeCursor();
    return $return;
}

function getIDFromPseudo($pseudo){
    /*
    * Retourne l'ID du compte dont le pseudo est $pseudo.
    * Retourne 0 si aucun compte n'est trouvé
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('SELECT id FROM comptes WHERE pseudo=? ');
    $req->execute(array($pseudo));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return $donnees['id'];
    }
    
    $req->closeCursor();
    return 0;
}

function GetTotalRegisteredMembers(){
    /*
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('SELECT COUNT(*) AS nb FROM comptes');
    $req->execute(array());
    
    $donnees = $req->fetch();
    
    return $donnees['nb'];
}

function GetTotalBannedMembers(){
    /*
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('SELECT COUNT(*) AS nb FROM comptes WHERE banned=\'1\'');
    $req->execute(array());
    
    $donnees = $req->fetch();
    
    return $donnees['nb'];
}

function GetLatestRegisteredMembers($combien = 5){
    /*
    * Retourne l'ID des $combien derniers membres enregistrés au site
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $return = array();
    $count = 0;
    $req = $bdd->prepare('SELECT id FROM comptes ORDER BY id DESC');
    $req->execute(array());
    
    while($count < $combien && $donnees = $req->fetch()){
        $return[$count] = $donnees['id'];
        $count ++;
    }
    
    $req->closeCursor();
    return $return;
    
}

function IsMainEmailVisible($IDCompte, $Value = 0){
    /*
    * Retourne true s'il est visible; false sinon.
    * Si $Value = true ou false, alors la méthode UPDATE
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    if($Value === 0){
        // GET
        $req = $bdd->prepare('SELECT IsMainEmailVisible FROM comptes WHERE id=?');
        $req->execute(array($IDCompte));
        
        $donnees = $req->fetch();
        $req->closeCursor();
        if($donnees['IsMainEmailVisible'] == '1')
            return true;
        return false;
    }else if($Value === true || $Value === false){
        // UPDATE
        $Int = ($Value == true ? 1 : 0);
        
        $req = $bdd->prepare('UPDATE comptes SET IsMainEmailVisible=? WHERE id=?');
        $req->execute(array($Int, $IDCompte));
    }
}

function IsFullNameVisible($IDCompte, $Value = 0){
    /*
    * Retourne true s'il est visible; false sinon.
    * Si $Value = true ou false, alors la méthode UPDATE
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    if($Value === 0){
        // GET
        $req = $bdd->prepare('SELECT IsNameVisible FROM comptes WHERE id=?');
        $req->execute(array($IDCompte));
        
        $donnees = $req->fetch();
        $req->closeCursor();
        if($donnees['IsNameVisible'] == '1')
            return true;
        return false;
    }else if($Value === true || $Value === false){
        // UPDATE
        $Int = ($Value == true ? 1 : 0);
        
        $req = $bdd->prepare('UPDATE comptes SET IsNameVisible=? WHERE id=?');
        $req->execute(array($Int, $IDCompte));
    }
}

function GetStrongestGroup($IDCompte){
    /**
    * Retourne l'ID du groupe du compte donné avec la priorité
    * la plus élevée
    */
    include(__ROOT__.'includes/bddConnect.php');
    
    $groupes = getCompteGroupes($IDCompte);
    $HighestPriorityID = 0;
    $HighestPriority = 0;
    $Count = count($groupes);
    for($i = 0; $i < $Count; $i++){
        if($groupes[$i]['priority'] > $HighestPriority && $groupes[$i]['priority'] > 0){
            $HighestPriorityID = $groupes[$i]['id'];
            $HighestPriority = $groupes[$i]['priority'];
        }
    }
    
    return $HighestPriorityID;
}

function GetNameColor($IDCompte){
    /*
    * Retourne le code de couleur qui correspond au
    * groupe le plus "élevé" du joueur donné.
    */
    include(__ROOT__.'includes/bddConnect.php');
    require_once(__ROOT__.'includes/groupes.php');

    return GetColorGroup(GetStrongestGroup($IDCompte));
}

function CompteGetFacebookAccessToken($UserID){
    /*
     * Retourne l'AccessToken pour interagir avec le compte
     * Facebook de l'utilisateur.
     */
    include(__ROOT__.'includes/bddConnect.php');
     
    $req = $bdd->prepare('SELECT FacebookAccessToken FROM comptes WHERE ID=?');
    $req->execute(array($UserID));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return $donnees['FacebookAccessToken'];
    }
    
    // Utilisateur invalide
    $req->closeCursor();
    return false;
}

function CompteGetCryptPassword($UserID){
    /*
     * Retourne le mot de passe encrypté du compte
     */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('SELECT pwd FROM comptes WHERE ID=?');
    $req->execute(array($UserID));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return $donnees['pwd'];
    }
    
    $req->closeCursor();
    return false;
}

function CompteGetFacebookUsername($UserID){
    /*
     */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('SELECT FacebookUsername FROM comptes WHERE id=?');
    $req->execute(array($UserID));
    
    if($donnees = $req->fetch()){
        $req->closeCursor();
        return $donnees['FacebookUsername'];
    }
    
    $req->closeCursor();
    return false;
}

function CompteSetFacebookUsername($UserID, $FacebookUsername){
    /*
     */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('UPDATE comptes SET FacebookUsername=? WHERE id=?');
    $req->execute(array(stripslashes(trim($FacebookUsername)), $UserID));
    
    return true;
}

function CompteLinkedToFacebook($UserID){
    /*
     * Retourne un AccesToken si le compte a 'linké' son compte Diamond Craft
     * avec son compte Facebook; retourne False sinon.
     */
    include(__ROOT__.'includes/bddConnect.php');
    
    $AccessToken = CompteGetFacebookAccessToken($UserID);
    if($AccessToken === false)
        return false;
    if($AccessToken == "")
        return false;
    
    return $AccessToken;
    
}

function CompteGetFacebookObject($UserID){
    /*
     * Retourne l'object Facebook pour interagir avec
     * le compte donné.
     */
    require_once(__ROOT__.'includes/social.php');
     
    $AT = CompteLinkedToFacebook($UserID);
    if($AT === false)
       return false;
       
    $NewFacebook = new Facebook(array(
      'appId'  => $API_KEY,
      'secret' => $SECRET,
      'fileUpload' => true, 
      'cookie' => true,
    ));
    $NewFacebook->setAccessToken($AT);
    
    return $NewFacebook;
}

function CompteUpdateFacebookAccessToken($UserID, $AT){
    /*
     * Change l'AccessToken pour le compte donné.
     */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('UPDATE comptes SET FacebookAccessToken=? WHERE ID=?');
    $req->execute(array($AT, $UserID));
    
    return true;
}

function CompteUpdateNotifySetting($UserID, $NotifySetting, $Value){
    /*
     * Met à jour un setting pour les notifications du compte donné.
     */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare("UPDATE comptes SET $NotifySetting=? WHERE id=?");
    $req->execute(array(($Value === true || $Value === '1' || $Value === 1), $UserID));
    
    return true;
}

function CompteChangerSexe($UserID, $Gender){
    /*
     */
    include(__ROOT__.'includes/bddConnect.php');
    require_once(__ROOT__.'includes/Global.php');
    
    global $GENDER_MALE;
    global $GENDER_FEMALE;
    
    $req = $bdd->prepare("UPDATE comptes SET Gender=? WHERE id=?");
    $req->execute(array((($Gender == $GENDER_MALE) ? true : false), $UserID));
    
    return true;
}

function CompteSetDescription($UserID, $Description){
    /*
     */
    include(__ROOT__.'includes/bddConnect.php');
    
    $req = $bdd->prepare('UPDATE comptes SET Description=? WHERE id=?');
    $req->execute(array(trim(stripslashes(utf8_decode(htmlspecialchars($Description)))), $UserID));
    
    return true;
}

function SupprimerCompte($UserID){
	/*
	 * Supprime un compte de la base de données.
	 * La fonction prend soin de supprimer toutes ses instances dans
	 * toutes les tables.
	 */
	include(__ROOT__.'includes/bddConnect.php');
	
	$Pseudo = getPseudo($UserID);
	
	// Suppression de la table principale
	$req = $bdd->prepare('DELETE FROM comptes WHERE ID=?');
	$req->execute(array($UserID));
	
	// Suppression des informations sur ses/son groupe(s)
	$req = $bdd->prepare('DELETE FROM comptes_groupes WHERE idCompte=?');
	$req->execute(array($UserID));
	
	// Les droits directs du comptes
	$req = $bdd->prepare('DELETE FROM droits_comptes WHERE idCompte=?');
	$req->execute(array($UserID));
	
	// Les messages des forums
	$req = $bdd->prepare('UPDATE forums_messages SET idCompte=0, DeletedUserPseudo=? WHERE idCompte=?');
	$req->execute(array($Pseudo, $UserID));
	
	// Les éditions de messages dans les forums
	$req = $bdd->prepare('DELETE FROM forums_messages_edit WHERE idCompte=?');
	$req->execute(array($UserID));
	
	// Les messages privés
	$req = $bdd->prepare('DELETE FROM pm WHERE IDCompteTo=?');
	$req->execute(array($UserID));
	$req = $bdd->prepare('UPDATE pm SET StringFrom=? WHERE IDCompteFrom=?');
	$req->execute(array($Pseudo, $UserID));
	
	// Packages VIP
	$req = $bdd->prepare('DELETE FROM vip_active_packages WHERE IDCompte=?');
	$req->execute(array($UserID));
}

function compteWhitelisted($UserID){
	/*
	 * Retourne vrai si un compte a été whitelisté depuis le site web
	 */
	include(__ROOT__.'includes/bddConnect.php');
	
	$req = $bdd->prepare('SELECT Whitelisted FROM comptes WHERE id=?');
	$req->execute(array($UserID));
	
	if($donnees = $req->fetch()){
		$req->closeCursor();
		return $donnees['Whitelisted'];
	}
	
	$req->closeCursor();
	return false;
}
?>
