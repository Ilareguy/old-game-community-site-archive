<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');
    
include(__ROOT__."includes/bddConnect.php");
include(__ROOT__."includes/verifyLogin.php");
require_once(__ROOT__."includes/comptes.php");
require_once(__ROOT__."includes/droits.php");
require_once(__ROOT__.'includes/dates.php');
require_once(__ROOT__.'includes/strings.php');
require_once(__ROOT__."includes/sys.php");

if(!$_SESSION['connecte']){
    header('Location: login.php?err=1');
}

if(IsMaintenance() && !droit($_SESSION['id'], 'IgnoreMaintenance'))
    header('Location: index.php?maintenance');

$currentPage = "account";

if(!isset($_GET['id']))
    $onMyPage = true;
else{
    if($_GET['id'] == $_SESSION['id'])
        $onMyPage = true;
    else{
        $compte = getCompte($_GET['id']);
        if($compte === false)
            $onMyPage = true;
        else
            $onMyPage = false;
    }
}
if($onMyPage)
    $compte = getCompte($_SESSION['id']);
else
    $currentpage = "someoneaccount";
    
$successbox = false;
$errorbox = false;
$warningbox = false;
$errorString = "";
$errorStringNeedsBR = false;
$successString = "";
$successStringNeedsBR = false;
$warningString = "";

if(isset($_GET['login'])){
    $successbox = true;
    $successString .= "Welcome, " . $compte['pseudo'] . " !";
}

// Les droits
if(!$onMyPage)
    $editFirstName = droit($_SESSION['id'], 'editerFirstNameAutresComptes');
else
    $editFirstName = true;
    
if(!$onMyPage)
    $editLastName = droit($_SESSION['id'], 'editerLastNameAutresComptes');
else
    $editLastName = true;
    
// Changements du compte
$compteNeedsReload = false;
    
// Changement des visibilités
if(isset($_POST['save'])){
    // Main Email
    if($onMyPage)
        IsMainEmailVisible($compte['id'], (isset($_POST['IsMainEmailVisibleCheck'])));
    
    // First Name & Last Name
    if($onMyPage || ($editLastName && $editFirstName))
        IsFullNameVisible($compte['id'], (isset($_POST['IsNameVisibleCheck'])));
}

// First Name & Last Name
if($editFirstName){
    if(isset($_POST['firstName'])){
        compteChangerFirstName($compte['id'], $_POST['firstName']);
        
        $successbox = true;
        $compteNeedsReload = true;
        if($successStringNeedsBR)
            $successString .= "<br />The first name has been saved.";
        else{
            $successString .= "The first name has been saved.";
            $successStringNeedsBR = true;
        }
    }
}
if($editLastName){
    if(isset($_POST['lastName'])){
        compteChangerLastName($compte['id'], $_POST['lastName']);
        
        $successbox = true;
        $compteNeedsReload = true;
        if($successStringNeedsBR)
            $successString .= "<br />The last name has been saved.";
        else{
            $successStringNeedsBR = true;
            $successString .= "The last name has been saved.";
        }
    }
}

// Changement du mot de passe
if($onMyPage && isset($_POST['oldpwd']) && isset($_POST['pwd']) && isset($_POST['pwdconfirm'])){
    if($_POST['oldpwd'] != "" && $_POST['pwd'] != "" && $_POST['pwdconfirm'] != ""){
        $okayToChangePwd = true;
        if(PasswordCrypt($_POST['oldpwd']) != $compte['pwd']){
            // Ancien mot de passe entré pas bon
            $okayToChangePwd = false;
            if(!$errorStringNeedsBR){
                $errorString .= "The old password is wrong.";
                $errorStringNeedsBR = true;
            }else
                $errorString .= "<br />The old password is wrong.";
            
            $errorbox = true;
        }
        else if($_POST['pwdconfirm'] != $_POST['pwd']){
            // Confirmation pas bonne
            $okayToChangePwd = false;
            if(!$errorStringNeedsBR){
                $errorString .= "The confirmation password does not match the password.";
                $errorStringNeedsBR = true;
            }else
                $errorString .= "<br />The confirmation password does not match the password.";
            
            $errorbox = true;
        }
        else if(strlen($_POST['pwd']) > 30){
            // Mot de passe trop long
            $okayToChangePwd = false;
            if(!$errorStringNeedsBR){
                $errorString .= "Your password is too long (max. 30 characters).";
                $errorStringNeedsBR = true;
            }else
                $errorString .= "<br />Your password is too long (max. 30 characters).";
            
            $errorbox = true;
        }
        
        // Si tout est OK ...
        if($okayToChangePwd){
            $compteNeedsReload = true;
            compteChangerPwd($_SESSION['id'], $_POST['pwd']);
            
            if(!$successStringNeedsBR){
                $successString .= "The password has been changed.";
                $successStringNeedsBR = true;
            }else
                $successString .= "<br />The password has been changed.";
        }
    }
}

if($editLastName || $editFirstName)
    $saveButton = true;
else
    $saveButton = false;

if($compteNeedsReload){
    if(!$onMyPage)
        $compte = getCompte($_GET['id']);
    else
        $compte = getCompte($_SESSION['id']);
}

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
    
    <script type="text/javascript" src="js/global.js"></script>
    <script type="text/javascript" src="js/header.js"></script>
    <script type="text/javascript" src="js/account.js"></script>
    <script type="text/javascript" src="js/server_command.php"></script>
</head>
<body>
<?php
require_once(__ROOT__."subPages/header.php");
?>
<div id="page" class="container">
	<div id="page-bgtop">
		<div id="page-bgbtm">
            <?php
            if($successbox)
                echo '<div class="successBox"><p>'.$successString.'</p></div>';
            if($errorbox)
                echo '<div class="errorBox"><p>'.$errorString.'</p></div>';
            if($warningbox)
                echo '<div class="warningBox"><p>'.$warningString.'</p></div>';
            ?>
			<div id="content">
				<div id="box1" class="box-style1">
                    <h2 class="title">
                        <?php
                        if($onMyPage)
                            echo 'Your profile';
                        else
                            echo 'Profile';
                        ?>
                    </h2>
                    <p class="byline"><?php echo $compte['pseudo']; ?></p>
                    <?php
                    if($onMyPage){
                        ?><div id="logoutButton" style="display: none; color: red;">Logout</div><?php
                    }
                    
                    if(droit($_SESSION['id'], 'whitelistGameServerCommand')){
                        ?><div data-server-command="whitelist add <?php echo $compte['pseudo']; ?>">White-list <?php echo $compte['pseudo']; ?></div><?php
                    }
                    if(droit($_SESSION['id'], 'gameServerCommand')){
                        ?><br /><br /><?php
                        ?><div style="color: red;" id="BanButton" data-server-command="ban <?php echo $compte['pseudo']; ?>">BAN <?php echo $compte['pseudo']; ?></div><?php
                        ?><div style="color: green;" id="UnbanButton" data-server-command="pardon <?php echo $compte['pseudo']; ?>">Unban <?php echo $compte['pseudo']; ?></div><?php
                    }
                    ?>
                    <h3>General Information</h3>
                    <form action="account.php" method="post">
                        <input type="hidden" value="true" name="save"/>
                        <table class="tablePage">
                            <tr>
                                <th colspan="2">Account Details</th>
                            </tr>
                            <tr>
                                <td class="leftCell" style="width: 40%;">Login</td>
                                <td class="rightCell"><?php echo $compte['pseudo']; ?></td>
                            </tr>
                            <?php if($compte['IsMainEmailVisible'] || $onMyPage){ ?>
                                <tr>
                                    <td class="leftCell">E-mail</td>
                                    <td class="rightCell">
                                        <?php
                                        echo $compte['email'];
                                        if($onMyPage){
                                            $CheckedString = "";
                                            if($compte['IsMainEmailVisible'])
                                                $CheckedString = 'checked="checked"';
                                            echo '&nbsp;&nbsp;&nbsp;<input type="checkbox" name="IsMainEmailVisibleCheck" id="IsMainEmailVisibleCheck" ' . $CheckedString . '"/><label for="IsMainEmailVisibleCheck">Visible</label>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            
                            <?php if($compte['IsNameVisible'] || ($editFirstName && $editLastName) || $onMyPage){ ?>
                                <?php if($onMyPage){ ?>
                                    <tr>
                                        <td colspan="2" style="text-align: center;">
                                            <?php
                                            $CheckedString = "";
                                            if($compte['IsNameVisible'])
                                                $CheckedString = 'checked="checked"';
                                            echo '<input type="checkbox" name="IsNameVisibleCheck" ' . $CheckedString . '" id="IsNameVisibleCheck"/> <label for="IsNameVisibleCheck">Make my full name visible to everyone</label>';
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td class="leftCell">First name</td>
                                    <td class="rightCell">
                                        <?php
                                        if($editFirstName){
                                            echo '<input maxlength="30" type="text" name="firstName" value="'.$compte['firstName'].'" class="inputText" />';
                                        }else{
                                            echo $compte['firstName'];
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="leftCell">Last name</td>
                                    <td class="rightCell">
                                        <?php
                                        if($editLastName){
                                            echo '<input maxlength="50" type="text" name="lastName" value="'.$compte['lastName'].'" class="inputText" />';
                                        }else{
                                            echo $compte['lastName'];
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td class="leftCell">Last login</td>
                                <td class="rightCell">
                                    <?php
                                    if($compte['lastLogin'] == 0)
                                        echo 'Never';
                                    else
                                        echo convertirDatePublicationEnString($compte['lastLogin'], 2) . ' ago';
                                    ?>
                                </td>
                            </tr>
                            <?php
                            if($compte['webmaster'] == 1){
                                ?>
                                <tr>
                                    <td colspan="2" style="text-align: center; font-weight: bold;"><?php echo $compte['pseudo']; ?> is a webmaster</td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                        
                        <?php
                        if($onMyPage){
                            ?>
                            <table class="tablePage">
                                <tr>
                                    <th colspan="2">Change your password</th>
                                </tr>
                                <tr>
                                    <td style="width: 40%;" class="leftCell">Old password</td>
                                    <td class="rightCell">
                                        <input type="password" name="oldpwd" maxlength="30" class="inputText" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="leftCell">New password</td>
                                    <td class="rightCell">
                                        <input type="password" name="pwd" maxlength="30" class="inputText" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="leftCell">New password (confirmation)</td>
                                    <td class="rightCell">
                                        <input type="password" name="pwdconfirm" maxlength="30" class="inputText" />
                                    </td>
                                </tr>
                            </table>
                            <input type="submit" value="Save changes" style="width: 100%;" />
                            <?php
                        }
                        ?>
                    </form>
                    
                    <div style="margin-top: 30px;"></div>
                    
                    <table class="tablePage">
                        <tr>
                            <?php
                            $groupsStr = "";
                            $groupsStr .= $compte['pseudo'];
                            if($groupsStr[strlen($groupsStr) - 1] == 's')
                                $groupsStr .= "' Groups";
                            else
                                $groupsStr .= "'s Groups";
                            ?>
                            <th><?php echo $groupsStr; ?></th>
                        </tr>
                        <?php
                        if($compte['groupes'] !== false){
                            for($i=0; $i<count($compte['groupes']); $i++){
                                echo '<tr>';
                                    echo '<td class="centerCell"><a style="text-decoration: none;" href="groupe.php?id='.$compte['groupes'][$i]['id'].'"><span style="color: '.$compte['groupes'][$i]['couleur'].';">'.$compte['groupes'][$i]['nom'].'</span></a></td>';
                                echo '</tr>';
                            }
                        }else{
                            echo '<tr><td class="centerCell">'.$compte['pseudo'].' currently is in no group.</td></tr>';
                        }
                        ?>
                    </table>
			    </div>
			</div>
            <?php
            include('subPages/sidebar.php');
            ?>
		</div>
	</div>
</div>
<div id="footer" class="container">
	<p>Diamond Craft. Website designed by <a href="mailto:totoila@totoila.com">Anthony Ilareguy (Totoila)</a>. Hosted by <a href="http://www.milsuitefx.com" target="_blank">Milsuite FX</a></p>
</div>
</body>
</html>
