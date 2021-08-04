<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');
    
include(__ROOT__."includes/bddConnect.php");
require_once(__ROOT__."includes/comptes.php");
require_once(__ROOT__."includes/sys.php");

if(IsMaintenance() && !droit($_SESSION['id'], 'IgnoreMaintenance'))
    header('Location: index.php?maintenance');

$currentPage = "subscribe";
$inscriptionTerminee = false;

$showerrorbox = false;
$errorString = "";
$errorStringNeedsBR = false;
$subscribeError = false;
$subscribeDone = false;
$postedPseudo = "";
$postedEmail = "";
$postedFirstName = "";
$postedLastName = "";

function addErrorString($_string){
    global $showerrorbox, $errorString, $errorStringNeedsBR;
    
    if(!$showerrorbox)
        $showerrorbox = true;
    
    if($errorStringNeedsBR)
        $errorString .= '<br />'.$_string;
    else{
        $errorString .= $_string;
        $errorStringNeedsBR = true;
    }
}

if(isset($_POST['pseudo']) && isset($_POST['pwd']) && isset($_POST['pwdconfirm']) && isset($_POST['email']) && isset($_POST['firstName']) && isset($_POST['lastName'])){
    // On demande à valider le formulaire; On vérifie tous les champs
    
    $postedPseudo = $_POST['pseudo'];
    $postedEmail = $_POST['email'];
    $postedFirstName = $_POST['firstName'];
    $postedLastName = $_POST['lastName'];
    
    if(strlen(htmlentities($_POST['pseudo'])) > 20){
        // Pseudo trop long
        addErrorString("The nickname you took is too long");
        $subscribeError = true;
    }
    if(!preg_match("/^[A-Za-z0-9_]+$/", $_POST['pseudo'])){
        // Alphanumerique
        addErrorString("The nickname must contain alphanumeric characters only (including underscore (\"_\") and excluding accents)");
        $subscribeError = true;
    }
    if(strlen($_POST['pseudo']) < 4){
        // Pseudo trop court
        addErrorString("The nickname must contain at least 4 characters");
        $subscribeError = true;
    }
    if(strlen($_POST['pwd']) > 30){
        // Mot de passe trop long
        addErrorString("The password is too long");
        $subscribeError = true;
    }
    if(strlen($_POST['pwd']) < 5){
        // Mot de passe trop court
        addErrorString("The password must contain at least 5 characters");
        $subscribeError = true;
    }
    if($_POST['pwd'] != $_POST['pwdconfirm']){
        // Mots de passe correspondent pas
        addErrorString("The password and confirmation don't match");
        $subscribeError = true;
    }
    if(strlen(htmlentities($_POST['firstName'])) > 30){
        // Prenom trop long
        addErrorString("The first name box can contain a maximum of 30 characters");
        $subscribeError = true;
    }
    if(strlen(htmlentities($_POST['lastName'])) > 50){
        addErrorString("The last name box can contain a maximum of 50 characters");
        $subscribeError = true;
    }
    if(!preg_match("#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#", $_POST['email'])){
        // Email incorrect
        addErrorString("Invalid e-mail");
        $subscribeError = true;
    }
    if(comptePseudoExists(htmlentities($_POST['pseudo']))){
        // Pseudo déjà pris
        addErrorString("This nickname is already taken. Please chose another one");
        $subscribeError = true;
    }
    if(compteEmailExists($_POST['email'])){
        // Email déjà utilisé
        addErrorString("This e-mail is already in use. Please chose another one");
        $subscribeError = true;
    }
    
    if(!$subscribeError){
        $resultatCreation = creerCompte(htmlentities(ucwords(strtolower($_POST['pseudo']))), $_POST['pwd'], 
            $_POST['email'], htmlentities(ucwords(strtolower($_POST['firstName']))), htmlentities(ucwords(strtolower($_POST['lastName']))));
        if($resultatCreation === true)
            $subscribeDone = true;
        else
            addErrorString($resultatCreation);
    }
}

if($_SESSION['connecte'])
    addErrorString("Please logout first");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <link href="default.css?v=1" rel="stylesheet" type="text/css" media="all" />
    <link href="jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css" media="all" />
    <style type="text/css">
    @import "layout.css";
    </style>
    <!--[if IE 6]><link href="ie6fix.css" rel="stylesheet" type="text/css" /><![endif]-->
    <!--[if IE 7]><link href="ie6fix.css" rel="stylesheet" type="text/css" /><![endif]-->
    <script type="text/javascript" src="js/jquery.js"></script>
    
    <script type="text/javascript" src="js/header.js"></script>
</head>
<body>
<?php
require_once(__ROOT__."subPages/header.php");
?>
<div id="page" class="container">
	<div id="page-bgtop">
		<div id="page-bgbtm">
            <?php
            if($showerrorbox)
                echo '<div class="errorBox"><p>'.$errorString.'</p></div>';
            ?>
			<div id="content">
                <?php
                if($_SESSION['connecte']){
                    ?>
                    <div id="box1" class="box-style1">
                    <h2 class="title">Diamond Craft Registration</h2>
					<p class="byline">Please logout first.</p>
                    </div>
                    <?php
                }else if(!$subscribeDone){
                    // Page d'inscriptions
                    ?>
                    <div id="box1" class="box-style1">
                        <h2 class="title">Diamond Craft Registration</h2>
                        <p class="byline">It's totally free, easy and fast.</p>
                        <h3>Please fill in all blank fields</h3>
                        <form action="subscribe.php" method="post">
                            <table class="tablePage">
                                <tr>
                                    <th style="width: 190px;">Personal Details</th>
                                    <th>Values</th>
                                </tr>
                                <tr>
                                    <td class="leftCell">Minecraft username:</td>
                                    <td class="rightCell">
                                        <input type="text" value="<?php echo $postedPseudo; ?>" name="pseudo" maxlength="20" class="inputText" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="leftCell">E-mail:</td>
                                    <td class="rightCell"><input type="text" value="<?php echo $postedEmail; ?>" name="email" maxlength="50" class="inputText" /></td>
                                </tr>
                                <tr>
                                    <td class="leftCell">Password (<b>not</b> the same as your Minecraft account):</td>
                                    <td class="rightCell"><input type="password" name="pwd" class="inputText" maxlength="30" /></td>
                                </tr>
                                <tr>
                                    <td class="leftCell">Password (confirm):</td>
                                    <td class="rightCell"><input type="password" name="pwdconfirm" maxlength="30" class="inputText" /></td>
                                </tr>
                                <tr>
                                    <td class="leftCell">First Name:</td>
                                    <td class="rightCell"><input type="text" value="<?php echo $postedFirstName; ?>" name="firstName" maxlength="30" class="inputText" /></td>
                                </tr>
                                <tr>
                                    <td class="leftCell">Last Name:</td>
                                    <td class="rightCell"><input type="text" value="<?php echo $postedLastName; ?>" name="lastName" maxlength="50" class="inputText" /></td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="text-align: center;"><input type="submit" value="Register" style="width: 300px;" /></td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <?php
                }else{
                    // Inscription terminée
                    ?>
                    <div id="box1" class="box-style1">
                    <h2 class="title">Done !</h2>
					<p class="byline">Your registration to Diamond Craft is now completed.</p>
                    <p>
                        Click <a href="login.php">here</a> to login!
                    </p>
                    </div>
                    <?php
                }
                ?>
			</div>
			<div id="sidebar">
			</div>
		</div>
	</div>
</div>
<div id="footer" class="container">
	<p>Diamond Craft. Website designed by <a href="mailto:totoila@totoila.com">Anthony Ilareguy (Totoila)</a>. Hosted by <a href="http://www.milsuitefx.com" target="_blank">Milsuite FX</a></p>
</div>
</body>
</html>
