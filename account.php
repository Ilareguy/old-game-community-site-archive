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
require_once(__ROOT__."includes/bbcodes.php");

if(!$_SESSION['connecte']){
    header('Location: login.php?err=1');
}

if(IsMaintenance() && !droit($_SESSION['id'], 'IgnoreMaintenance'))
    header('Location: index.php?maintenance');

// Liste des pages
$PAGES = array(
    "BASIC_INFO" =>     1,
    "GROUPS_TOWNS" =>   2,
    "ADMINISTRATION" => 3
);

$CurrentSubPage = ((isset($_GET['p']) && $_GET['p'] > 0 && $_GET['p'] <= count($PAGES)) ? 
                   $_GET['p'] :
                   $PAGES["BASIC_INFO"]);

$currentPage = "account";
$User;
$UserFacebook;

if(!isset($_GET['id']))
    $onMyPage = true;
else{
    if($_GET['id'] == $_SESSION['id'])
        $onMyPage = true;
    else{
        $User = getCompte($_GET['id']);
        if($User === false)
            $onMyPage = true;
        else
            $onMyPage = false;
    }
}
if($onMyPage){
    $User = getCompte($_SESSION['id']);
    $UserFacebook = $facebook;
}else{
    $currentpage = "someoneaccount";
    $UserFacebook = CompteGetFacebookObject($User['id']);
}

$CanAdministrateProfile = droit($_SESSION['id'], 'manipulateAccounts');
$CanEditProfile = ($onMyPage || $CanAdministrateProfile);
    
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
    $successString .= "Welcome, " . $User['pseudo'] . " !";
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
            <div id="messageBoxes">
            <?php
            if($successbox)
                echo '<div class="successBox"><p>'.$successString.'</p></div>';
            if($errorbox)
                echo '<div class="errorBox"><p>'.$errorString.'</p></div>';
            if($warningbox)
                echo '<div class="warningBox"><p>'.$warningString.'</p></div>';
            ?>
            </div>
			<div id="content">
                <h1 style="color:white;"><?php echo $User['pseudo']; echo EnglishApostropheS($User['pseudo']); ?> profile</h1>
				<div id="box1" class="box-style1">
                    
                    <?php
                    
                    switch($CurrentSubPage){
                        
                        case $PAGES["GROUPS_TOWNS"]:
                            break;
                        
                        case $PAGES["ADMINISTRATION"]:
                            break;
                            
                        default:
                            
                            ?>
                            <p style="float:left;"><img style="width:50px;" src="subPages/MinecraftSkinRender.php?user=<?php echo $User['pseudo']; ?>"/></p>
                    
                            <table>
                            
                                <tr>
                                    <td style="width:100px;">Username: </td>
                                    <td><?php echo bbcodes_player("{player}" . $User['pseudo'] . "{/player}"); ?></td>
                                </tr>
                                <?php
                                if($User['webmaster']){
                                    ?>
                                    <tr>
                                        <td colspan="2"><?php echo $User['pseudo'] ?> is a webmaster</td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                
                                <?php
                                if($User['banned']){
                                    ?>
                                    <tr>
                                        <td colspan="2"><?php echo $User['pseudo'] ?> is banned</td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                
                                <tr>
                                    <?php
                                    if($User['IsNameVisible']){
                                        ?>
                                        <td>Full Name:</td>
                                        <td><?php echo $User['firstName'] . ' ' . $User['lastName']; ?></td>
                                        <?php
                                    }else{
                                        ?>
                                        <td colspan="2"><?php echo $User['pseudo'] ?> chooses not to display <?php echo EnglishGenderHis($User['Gender']); ?> real name.</td>
                                        <?php
                                    }
                                    ?>
                                </tr>
                                
                                <tr>
                                    <?php
                                    if($User['IsMainEmailVisible']){
                                        ?>
                                        <td>Email:</td>
                                        <td><a href="mailto:<?php echo $User['email']; ?>"><?php echo $User['email']; ?></a></td>
                                        <?php
                                    }else{
                                        ?>
                                        <td colspan="2"><?php echo $User['pseudo'] ?> chooses not to display <?php echo EnglishGenderHis($User['Gender']); ?> e-mail address.</td>
                                        <?php
                                    }
                                    ?>
                                </tr>
                                
                            </table>
                            
                            <?php
                            if($User['Description'] != ""){
                            ?>
                                <div style="clear:both;height:40px;"></div>
                                <p style="white-space:pre-line;font-style:italic;"><?php echo $User['Description']; ?></p>
                            <?php
                            }
                            ?>
                            
                            <div style="clear:both;height:40px;"></div>
                            
                            <?php
                            if(CompteLinkedToFacebook($User['id']) && $UserFacebook !== false){
                                // Compte lié à Facebook; on affiche
                                
                                try{
                                    $UserFacebookProfile = $UserFacebook->api('/me', 'GET');
                                    $ProfilePicSrc = "http://graph.facebook.com/$UserFacebookProfile[id]/picture?type=large";
                                }catch(Exception $e){
                                    
                                    $ProfilePicSrc = "http://graph.facebook.com/1/picture?type=large";
                                    
                                    ?>
                                    <p>[There was an error fetching <?php echo $User['pseudo']; echo EnglishApostropheS($User['pseudo']); ?> Facebook info]</p>
                                    <?php
                                }
                                
                                ?>
                                <p class="byline"><?php echo $User['pseudo']; ?> has linked <?php echo EnglishGenderHis($User['Gender']); ?> profile with Facebook</p>
                                <table>
                                    <tr>
                                        <td>
                                            <img src="<?php echo $ProfilePicSrc; ?>" />
                                        </td>
                                        <td style="vertical-align:top;">
                                            <a target="_blank" href="https:www.facebook.com/<?php echo $User['FacebookUsername'] ?>"><input type="button" value="See me on Facebook!"/></a>
                                        </td>
                                    </tr>
                                </table>
                                <?php
                            }else{
                                ?>
                                <p class="byline"><?php echo $User['pseudo']; ?> has not linked <?php echo EnglishGenderHis($User['Gender']); ?> profile with Facebook</p>
                                <p><img src="http://graph.facebook.com/1/picture?type=large" /></p>
                                <?php
                            }
                            
                            break;
                            
                    }
                    
                    ?>
                    
                    
                    
			    </div>
			</div>
            <div id="sidebar">
                <div id="box3" class="box-style2">
                    <h3>Sections</h3>
                    <div class="items_menu">
                        <a href="?p=<?php echo $PAGES["BASIC_INFO"]; ?>"><div class="<?php echo (($CurrentSubPage == $PAGES["BASIC_INFO"]) ? 'current' : ''); ?> item"><img class="icon" alt="" src="images/icons/ContactCard.png"/> <p>Profile</p></div></a>
                        <!--<a href="?p=<?php echo $PAGES["GROUPS_TOWNS"]; ?>"><div class="<?php echo (($CurrentSubPage == $PAGES["GROUPS_TOWNS"]) ? 'current' : ''); ?> item"><img class="icon" alt="" src="images/icons/Facebook32.png"/> <p>Groups & Towns</p></div></a>-->
                        <?php
                        if($CanEditProfile){
                        ?>
                        <a href="editaccount.php?id=<?php echo $User['id']; ?>"><div class="item"><img class="icon" alt="" src="images/icons/Pencil.png"/> <p>Edit Profile</p></div></a>
                        <?php
                        }
                        ?>
                    </div>
                    
                    <?php
                    if($CanAdministrateProfile){
                    ?>
                    <p><br /></p>
                    <h3>Game Server Actions</h3>
                    <div class="items_menu">
                        <a data-success-message="Added <?php echo $User['pseudo']; ?> to the whitelist" data-send-server-command="whitelist add <?php echo $User['pseudo']; ?>" href="#"><div class="item"><img class="icon" alt="" src="images/icons/icon_heart.png"/> <p>Whitelist <?php echo $User['pseudo']; ?></p></div></a>
                        <a data-success-message="Removed <?php echo $User['pseudo']; ?> from the whitelist" data-send-server-command="whitelist remove <?php echo $User['pseudo']; ?>" href="#"><div class="item"><img class="icon" alt="" src="images/icons/Delete.png"/> <p>Remove <?php echo $User['pseudo']; ?> from whitelist</p></div></a>
                        <a data-success-message="Banned <?php echo $User['pseudo']; ?> from the game server" data-send-server-command="ban <?php echo $User['pseudo']; ?>" href="#"><div class="item"><img class="icon" alt="" src="images/icons/Delete.png"/> <p>Ban <?php echo $User['pseudo']; ?> (game server only)</p></div></a>
                        <a data-success-message="Pardoned <?php echo $User['pseudo']; ?>" data-send-server-command="pardon <?php echo $User['pseudo']; ?>" href="#"><div class="item"><img class="icon" alt="" src="images/icons/Emoticon.png"/> <p>Pardon <?php echo $User['pseudo']; ?> (game server only)</p></div></a>
                    </div>
                    <?php
                    }
                    ?>
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
