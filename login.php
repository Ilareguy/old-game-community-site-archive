<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');
    
include(__ROOT__."includes/bddConnect.php");
require_once(__ROOT__."includes/comptes.php");
require_once(__ROOT__."includes/sys.php");
require_once(__ROOT__."includes/droits.php");

if(IsMaintenance() && !droit($_SESSION['id'], 'IgnoreMaintenance'))
    header('Location: index.php?maintenance');

$currentPage = "login";
$loginSuccess = false;
$loginError = false;

$successbox = false;
$errorbox = false;
$warningbox = false;
$errorString = "";
$successString = "";
$warningString = "";

if(isset($_GET['err'])){
    switch($_GET['err']){
        case "1":
            $warningbox = true;
            $warningString .= "Please login first";
        break;
        case "2":
            $errorBox = true;
            $errorString .= "Bad login";
        break;
    }
}

if(isset($_GET['logout'])){
    // Effacement des cookies
    setcookie('remind', 'false', 0);
    setcookie('username', '', 0);
    setcookie('pwd', '', 0);
    
    $_SESSION['connecte'] = false;
}

if(isset($_POST['login']) && isset($_POST['pwd']) && $_SESSION['connecte'] == false){
    // On veut se connecter
    $idCompte = login($_POST['login'], $_POST['pwd']);
    
    if($idCompte !== false){
        if(isset($_POST['remind'])){
            // Signifie qu'on a coché la boîte pour rester connecter
            setcookie('remind', 'true', (time()+60*60*24*10));
            setcookie('username', $_POST['login'], (time()+60*60*24*10));
            setcookie('pwd', $_POST['pwd'], (time()+60*60*24*10));
        }else{
            setcookie('remind', 'false', (time()+60*60*24*10));
            setcookie('username', '', (time() - 3600));
            setcookie('pwd', '', (time() - 3600));
        }
        header('Location: account.php?login=1');
    }else{
        $errorString .= "Bad login";
        $loginError = true;
        $errorbox = true;
    }
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
    
    <script type="text/javascript" src="js/header.js"></script>
    <script type="text/javascript" src="js/global.js"></script>
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
                    <?php
                    if(!$_SESSION['connecte']){
                        ?>
                        <h2 class="title">Log into Diamond Craft</h2>
                        <p class="byline">share, create, build and socialize...</p>
                        <?php
                        if(isset($_GET['logout'])){
                            echo "<p style=\"color:green;\">You successfully logged out</p>";
                        }
                        ?>
                        <h3>Enter your information below</h3>
                        <form method="post" action="login.php">
                            <table>
                                <tr>
                                    <td style="width: 200px;">Username or e-mail:</td>
                                    <td>
                                        <input style="width: 200px; height: 24px; text-indent: 10px; font-weight: bold;" type="text" name="login" maxlength="50"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Password:</td>
                                    <td>
                                        <input style="width: 200px; height: 24px; text-indent: 10px;" type="password" name="pwd" maxlength="30"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="checkbox_remind">Keep me logged in:</label></td>
                                    <td>
                                        <?php
                                        if(isset($_COOKIE['remind'])){
                                            if($_COOKIE['remind'] == 'true')
                                                echo '<input id="checkbox_remind" type="checkbox" checked="checked" name="remind"/>';
                                            else
                                                echo '<input id="checkbox_remind" type="checkbox" name="remind"/>';
                                        }else
                                            echo '<input id="checkbox_remind" type="checkbox" name="remind"/>';
                                        ?>
                                        <div style="display: inline-block; width: 16px; height: 16px; background-image: url('images/icons/warning16.png');" minitiptitle="Warning" minitip="Please note: in order for the website to remember you, a cookie will be saved to your computer containing your password."></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <input type="submit" value="Login" style="width: 100%; font-weight: bold;"/>
                                    </td>
                                </tr>
                            </table>
                        </form>
                        <?php
                    }
                    ?>
			    </div>
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
