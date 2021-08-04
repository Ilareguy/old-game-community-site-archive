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
require_once(__ROOT__."includes/social.php");

if(!$_SESSION['connecte']){
    header('Location: login.php?err=1');
}

if(IsMaintenance() && !droit($_SESSION['id'], 'IgnoreMaintenance'))
    header('Location: index.php?maintenance');

$currentPage = "editaccount";

$errorString = "";
$successString = "";
$warningString = "";
function AddSuccess($msg){
    global $successString;
    $successString .= "<div class=\"successBox\"><p>$msg</p></div>";
}
function AddWarning($msg){
    global $warningString;
    $warningString .= "<div class=\"warningBox\"><p>$msg</p></div>";
}
function AddError($msg){
    global $errorString;
    $errorString .= "<div class=\"errorBox\"><p>$msg</p></div>";
}

// Les informations sur l'utilisateur
$User = getCompte($_SESSION['id']);

// Liste des pages
$PAGES = array(
    "BASIC_INFO" => 1,
    "FACEBOOK" => 2,
    "PASSWORD" => 3,
    "NOTIFICATIONS" => 4
);

$CurrentSubPage = ((isset($_GET['p']) && $_GET['p'] > 0 && $_GET['p'] <= count($PAGES)) ? 
                   $_GET['p'] :
                   $PAGES["BASIC_INFO"]);

// Changement du mot de passe
if(isset($_POST['action']) && $_POST['action'] == 'edit_password' && isset($_POST['oldpwd']) && isset($_POST['pwd']) && isset($_POST['pwdconfirm'])){
    $CurrentSubPage = $PAGES['PASSWORD'];
    if($_POST['oldpwd'] != "" && $_POST['pwd'] != "" && $_POST['pwdconfirm'] != ""){
        $okayToChangePwd = true;
        if(PasswordCrypt($_POST['oldpwd']) != CompteGetCryptPassword($User['id'])){
            // Ancien mot de passe entré pas bon
            $okayToChangePwd = false;
            AddError('The old password is wrong');
        }
        else if($_POST['pwdconfirm'] != $_POST['pwd']){
            // Confirmation pas bonne
            $okayToChangePwd = false;
            AddError('The confirmation password does not match the password.');
        }
        else if(strlen($_POST['pwd']) > 30){
            // Mot de passe trop long
            $okayToChangePwd = false;
            AddError('Your password is too long (max. 30 characters).');
        }
        
        // Si tout est OK ...
        if($okayToChangePwd){
            $compteNeedsReload = true;
            compteChangerPwd($User['id'], $_POST['pwd']);
            
            AddSuccess('The password has been changed.');
        }
    }
}

// Changements dans les infos de base
if(isset($_POST['action']) && $_POST['action'] == 'change_basic_info' && 
    isset($_POST['FirstName']) && trim($_POST['FirstName']) != "" &&
    isset($_POST['LastName']) && trim($_POST['LastName']) != "" &&
    isset($_POST['Email']) && trim($_POST['Email']) != "" && 
    isset($_POST['Gender']) && ($_POST['Gender'] == '1' || $_POST['Gender'] == '0') &&
    isset($_POST['Description'])){
    
    // Changer les informations de base
    IsMainEmailVisible($User['id'], isset($_POST['ShowEmail']));
    IsFullNameVisible($User['id'], isset($_POST['ShowName']));
    compteChangerFirstName($User['id'], $_POST['FirstName']);
    compteChangerLastName($User['id'], $_POST['LastName']);
    CompteChangerSexe($User['id'], $_POST['Gender']);
    CompteSetDescription($User['id'], $_POST['Description']);
    if(!compteChangerEmail($User['id'], $_POST['Email']))
        AddError('Invalid e-mail');
    
    // Rechargement
    $User = getCompte($User['id']);
    
    // Message de succès
    AddSuccess('Settings saved');
    
}

// Changements dans les notifications
if(isset($_POST['action']) && $_POST['action'] == "edit_notifications"){
    CompteUpdateNotifySetting($User['id'], $NOTIFY_ON_PM, isset($_POST[$NOTIFY_ON_PM]));
    CompteUpdateNotifySetting($User['id'], $NOTIFY_ON_NEWS_POST, isset($_POST[$NOTIFY_ON_NEWS_POST]));
    CompteUpdateNotifySetting($User['id'], $NOTIFY_ON_TOWN_JOIN, isset($_POST[$NOTIFY_ON_TOWN_JOIN]));
    
    // Rechargement
    $User = getCompte($User['id']);
    
    // Message de succès
    AddSuccess('Settings saved');
    
    // Affichage de la bonne page
    $CurrentSubPage = $PAGES["NOTIFICATIONS"];
}

// Unlink Facebook
if(isset($_GET['facebookunlink'])){
    CompteUpdateFacebookAccessToken($User['id'], '');
    CompteSetFacebookUsername($User['id'], '');
    
    // Redirection
    header('Location: editaccount.php?p=' . $PAGES['FACEBOOK']);
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
    table td{text-align:right;width:300px;padding-bottom:25px;vertical-align:top;}
    table td:first-child{text-align:left;}
    </style>
    <!--[if IE 6]><link href="ie6fix.css" rel="stylesheet" type="text/css" /><![endif]-->
    <!--[if IE 7]><link href="ie6fix.css" rel="stylesheet" type="text/css" /><![endif]-->
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/global.js"></script>
    <script type="text/javascript" src="js/header.js"></script>
    <script type="text/javascript" src="js/account.js"></script>
</head>
<body>
<?php
require_once(__ROOT__."subPages/header.php");
?>
<div id="page" class="container">
	<div id="page-bgtop">
		<div id="page-bgbtm">
            <?php
            echo $successString;
            echo $errorString;
            echo $warningString;
            ?>
			<div id="content">
				<div id="box1" class="box-style1">
                    <h1>Edit your account</h1>
                    
                    <?php
                    switch($CurrentSubPage){
                        
                        case $PAGES["FACEBOOK"]:
                            
                            /*
                             * FACEBOOK SETTINGS
                             */
                            ?><h2><img style="position:relative;top:8px;" alt="" src="images/icons/Facebook32.png"/> Facebook Settings</h2><?php
                            if($ACCOUNT_LINKED_TO_FACEBOOK){
                                
                                ?>
                                <h3>Your Diamond Craft account is linked with you Facebook account!</h3>
                                <?php
                                try{
                                
                                    $user_profile = $facebook->api('/me','GET');
                                    $user_id = $facebook->getUser();
                                    echo "Your Diamond Craft account is linked with:<br />";
                                    echo "<img alt=\"\" src=\"http://graph.facebook.com/$user_id/picture\" style=\"position:Relative;top:18px;margin-right:10px;\"/>";
                                    echo "<b><a href=\"http://www.facebook.com/$user_id\" target=\"_blank\">" . $user_profile['name'] . "</a></b>";
                                    echo "<p><br /></p>";
                                    echo "<input type=\"button\" value=\"Share this on your wall!\" data-post-facebook=\"\" data-post-caption=\"Come join me on Diamond Craft!\" data-post-name=\"I linked my Diamond Craft account with Facebook!\"/>";
                                    echo "<input type=\"button\" value=\"Unlink my Facebook profile\" onclick=\"if(confirm('Are you sure you want to unlink your Diamond Craft account with Facebook?')){window.location='editaccount.php?facebookunlink=1&p=" . $PAGES['FACEBOOK'] . "';}\"/>";
                                    
                                }catch(Exception $e){
                                    ?>
                                    <p style="color:red;">An error occured while trying to fetch your Facebook information.</p>
                                    <p>This is probably because you removed Diamond Craft access to your profile on Facebook.</p>
                                    <?php
                                    // On 'un-link' les profiles
                                    CompteUpdateFacebookAccessToken($_SESSION['id'], '');
                                }
                                
                            }else{
                                
                                ?>
                                <h3>Your Diamond Craft account has not yet been linked with your Facebook account</h3>
                                <p>To do so, simply log into Facebook and authorize Diamond Craft to interact with your account:</p>
                                <p style="text-align:center;"><a href="#" data-facebook-login=""><img alt="Login with Facebook" src="images/FacebookLogin.png"/></a></p>
                                <p>This will enable you to share news, links, profiles and more on Facebook with one single click!</p>
                                <?php
                                
                            }
                            
                            break;
                            
                        case $PAGES["PASSWORD"]:
                            
                            ?>
                            <h2><img style="position:relative;top:8px;" alt="" src="images/icons/Pen.png"/> Change your password</h2>
                            <form action="editaccount.php" method="post">
                                <input type="hidden" name="action" value="edit_password"/>
                                <p>Please enter your current password:</p>
                                <input name="oldpwd" name="old" type="password" maxlength="100" />
                                <p><br /></p>
                                <p>Choose a new password:</p>
                                <input name="pwd" type="password" maxlength="100" />
                                <p><br /></p>
                                <p>Confirm the new password:</p>
                                <input name="pwdconfirm" type="password" maxlength="100" />
                                <p><br /></p>
                                <input type="submit" value="Change password"/>
                            </form>
                            <?php
                            
                            break;
                            
                        case $PAGES["NOTIFICATIONS"]:
                            
                            ?>
                            <h2><img style="position:relative;top:8px;" alt="" src="images/icons/Notification.png"/> Notifications settings</h2>
                            <form method="post" action="editaccount.php">
                                <input type="hidden" name="action" value="edit_notifications"/>
                                <h3>Send me an e-mail to "<b><?php echo $User['email']; ?></b>" when:</h3>
                                <ul>
                                    <li>
                                        <input type="checkbox" <?php echo (($User['NotifyOnPM']) ? 'checked="checked"' : ""); ?> name="<?php echo $NOTIFY_ON_PM; ?>" id="PM"/> <label for="PM">I receive a Private Message</label>
                                    </li>
                                    <li>
                                        <input type="checkbox" <?php echo (($User['NotifyOnNewsPost']) ? 'checked="checked"' : ""); ?> name="<?php echo $NOTIFY_ON_NEWS_POST; ?>" id="News"/> <label for="News">A News Post has been posted</label>
                                    </li>
                                    <li>
                                        <input type="checkbox" <?php echo (($User['NotifyOnTownJoin']) ? 'checked="checked"' : ""); ?> name="<?php echo $NOTIFY_ON_TOWN_JOIN; ?>" id="TownAccept"/> <label for="TownAccept">My request for joining a town has been approved</label>
                                    </li>
                                    <li>
                                        <input checked="checked" type="checkbox" disabled="disabled"/> A forum thread I am subscribed to gets a new reply
                                    </li>
                                </ul>
                                <input type="submit" value="Save"/>
                            </form>
                            <?php
                            
                            break;
                            
                        default:
                            
                            // GENERAL SETTINGS
                            ?>
                            <h2><img style="position:relative;top:8px;" alt="" src="images/icons/User32.png"/> Basic info</h2>
                            <form method="post" action="editaccount.php">
                                <input type="hidden" name="action" value="change_basic_info"/>
                                <table>
                                    <tr>
                                        <td style="width:290px;">
                                            <label for="FirstName">First Name:</label><br />
                                            <input id="FirstName" type="text" name="FirstName" value="<?php echo $User['firstName']; ?>"/>
                                        </td>
                                        <td>
                                            <label for="LastName">Last Name:</label><br />
                                            <input id="LastName" type="text" name="LastName" value="<?php echo $User['lastName']; ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width:290px;">
                                            <label for="Email">Main E-mail:</label><br />
                                            <input id="Email" type="text" name="Email" value="<?php echo $User['email']; ?>"/>
                                        </td>
                                        <td>
                                            <h3>Privacy</h3>
                                            <label for="ShowName">Make my name visible to everyone</label>
                                            <input type="checkbox" id="ShowName" name="ShowName" <?php if($User['IsNameVisible']){echo 'checked="checked"';} ?>/>
                                            <br />
                                            <label for="ShowEmail">Make my e-mail visible to everyone</label>
                                            <input type="checkbox" id="ShowEmail" name="ShowEmail" <?php if($User['IsMainEmailVisible']){echo 'checked="checked"';} ?>/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="Gender">Gender:</label><br />
                                            <select id="Gender" name="Gender">
                                                <option <?php echo (($User['Gender'] == $GENDER_MALE) ? 'selected="selected"' : ''); ?> value="<?php echo $GENDER_MALE; ?>">Male</option>
                                                <option <?php echo (($User['Gender'] == $GENDER_FEMALE) ? 'selected="selected"' : ''); ?> value="<?php echo $GENDER_FEMALE; ?>">Female</option>
                                            </select>
                                        </td>
                                        <td>
                                            <label for="Description">Small Description:</label><br />
                                            <textarea style="resize:none;width:240px;height:100px;" id="Description" name="Description" maxlength="200"><?php echo $User['Description']; ?></textarea>
                                        </td>
                                    </tr>
                                </table>
                                
                                <input type="submit" value="Save"/>
                            </form>
                            <?php
                            
                            break;
                            
                    }
                    ?>
                    
			    </div>
			</div>
            <div id="sidebar">
                <div id="box3" class="box-style2">
                    <h2 class="title">Settings</h2>
                    <div class="items_menu">
                        <a href="?p=<?php echo $PAGES["BASIC_INFO"]; ?>"><div class="<?php echo (($CurrentSubPage == $PAGES["BASIC_INFO"]) ? 'current' : ''); ?> item"><img class="icon" alt="" src="images/icons/User32.png"/> <p>Basic info</p></div></a>
                        <a href="?p=<?php echo $PAGES["FACEBOOK"]; ?>"><div class="<?php echo (($CurrentSubPage == $PAGES["FACEBOOK"]) ? 'current' : ''); ?> item"><img class="icon" alt="" src="images/icons/Facebook32.png"/> <p>Facebook</p></div></a>
                        <a href="?p=<?php echo $PAGES["PASSWORD"]; ?>"><div class="<?php echo (($CurrentSubPage == $PAGES["PASSWORD"]) ? 'current' : ''); ?> item"><img class="icon" alt="" src="images/icons/Pen.png"/> <p>Change password</p></div></a>
                        <a href="?p=<?php echo $PAGES["NOTIFICATIONS"]; ?>"><div class="<?php echo (($CurrentSubPage == $PAGES["NOTIFICATIONS"]) ? 'current' : ''); ?> item"><img class="icon" alt="" src="images/icons/Notification.png"/> <p>Notifications</p></div></a>
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>
<div id="footer" class="container">
	<p>Diamond Craft. Website designed by <a href="mailto:totoila@totoila.com">Anthony Ilareguy (Totoila)</a>. Hosted by <a href="http://www.milsuitefx.com" target="_blank">Milsuite FX</a></p>
</div>
</body>
</html>
