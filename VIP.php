<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');
    
include("includes/bddConnect.php");
include("includes/verifyLogin.php");
include("includes/VIP.php");
require_once('includes/VIP_Packages/VIPPackage.class.php');
require_once(__ROOT__."includes/sys.php");

if(!$_SESSION['connecte']){
    header('Location: login.php?err=1');
}

if(IsMaintenance() && !droit($_SESSION['id'], 'IgnoreMaintenance'))
    header('Location: index.php?maintenance');

$successbox = false;
$errorbox = false;
$warningbox = false;
$errorString = "";
$successString = "";
$warningString = "";

$currentPage = "VIP";

// Sandbox ?
$IsPaypalSandbox = isset($_GET['sandbox']) ? true : IsPaypalSandbox();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Diamond Craft - VIP</title>
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
    
    <script type="text/javascript" src="js/colorpicker.js"></script>
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
			<div id="wide-content">
				<div id="box1" class="box-style1">
                    <?php if(isset($_GET['success'])){ ?>
                        <div class="successBox">
                            <h3 style="color: black;">Thanks a lot!</h3>
                            <p>If you see this message, it is most likely because you've purchased a VIP package. <strong>Congratulations! You are now a VIP!</strong></p>
                            <p>The money you used for that package will help us a lot keeping the server alive and make it more powerful!</p>
                            <p>If you have any problem with the package, please let us know. You can send us a private message (Totoila or J-Rex), or feel free to <a style="color: blue;" href="mailto:minecraft@totoila.com">e-mail us</a>.</p>
                        </div>
                    <?php } ?>
                    <?php
                    // STATUS VIP
                    $IsVIP = IsVIP($_SESSION['id']);
                    ?>
					<h2 class="title">My VIP Status</h2>
                    <?php
                    if($IsVIP){
                        ?>
                        <p class="byline">You have one or more active VIP packages</p>
                        <div class="image-style4 image-style4a"><img src="images/VIP.png" width="190" height="145" alt="" /><span></span></div>
                        <h3>How to get a package</h3>
                        <p>You can purchase a VIP Package for just a few dollars. To do so, you will need a PayPal account.
                        All of the funds collected from your purchase will be used towards maintaining and upgrading the Diamond Craft community.</p>
                        <p>Below, you will find a list of VIP Packages available for purchase.</p>
                        <?php
                    }else{
                        ?>
                        <p class="byline">You don't have any active VIP package</p>
                        <div class="image-style4 image-style4a"><img src="images/VIP_BlackWhite.png" width="190" height="145" alt="" /><span></span></div>
                        <h3>How to get a package</h3>
                        <p>You can purchase a VIP Package for just a few dollars. To do so, you will need a PayPal account.
                        All of the funds collected from your purchase will be used towards maintaining and upgrading the Diamond Craft community.</p>
                        <p>Below, you will find a list of VIP Packages available for purchase.</p>
                        <?php
                    }
                    ?>
				</div>
				<div id="box2" class="box-style2">
					<h2 class="title">Available VIP Packages</h2>
                    <div class="warningBox">
                        <h4>Notice</h4>
                        <p>For the package to take effect, your username on the website must be the same as your Minecraft account.</p>
                        <p>Payments will be sent to <strong>Jordan Ilareguy</strong>, aka <strong>J-Rex</strong></p>
                    </div>
                    <p><br /></p>
                        
                    <?php
                    $Pkgs = GetVIPPackages();
                    for($i = 0; $i < count($Pkgs); $i++){
                        
                        if($i > 0)
                            echo '<div style="height: 30px;"></div>';
                        $Pkg = VIPPackage::DynamicNew($Pkgs[$i]);
                        echo '<h3>' . $Pkg->Name() . '</h3>';
                        ?><div class="image-style4 image-style4a"><img src="<?php echo $Pkg->GetPhoto(); ?>" width="190" height="145" alt="" /><span></span></div><?php
                        ?><p style="white-space: pre-line;"><?php echo $Pkg->Description(); ?></p>
                        
                        <?php if($IsPaypalSandbox){ ?>
                        <form name="_xclick" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
                        <?php }else{ ?>
                        <form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                        <?php } ?>
                            <input type="hidden" name="cmd" value="_xclick">
                            <?php if($IsPaypalSandbox) { ?>
                            <input type="hidden" name="business" value="dc_1343678499_biz@totoila.com">
                            <?php }else{ ?>
                            <input type="hidden" name="business" value="jordan.ilareguy@me.com">
                            <?php } ?>
                            <input type="hidden" name="currency_code" value="CAD">
                            <input type="hidden" name="item_name" value="<?php echo 'VIP Package : ' . $Pkg->Name(); ?>">
                            <input type="hidden" name="item_number" value="<?php echo $Pkg->ID(); ?>">
                            <input type="hidden" name="amount" value="<?php echo $Pkg->Price(); ?>">
                            <input type="hidden" name="return" value="http://www.diamondcraft.org/VIP.php?success=1">
                            <input type="hidden" name="cancel_return" value="http://www.diamondcraft.org/VIP.php?cancel=1">
                            <?php if($IsPaypalSandbox){ ?>
                            <input type="hidden" name="notify_url" value="http://www.diamondcraft.org/Paypal/IPNs.php?sandbox=1&IDPackage=<?php echo $Pkg->ID(); ?>&IDCompte=<?php echo $_SESSION['id']; ?>">
                            <?php }else{ ?>
                            <input type="hidden" name="notify_url" value="http://www.diamondcraft.org/Paypal/IPN.php?IDPackage=<?php echo $Pkg->ID(); ?>&IDCompte=<?php echo $_SESSION['id']; ?>">
                            <?php } ?>
                            <input type="image" src="http://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
                        </form>
                        <?php
                        ?><div style="clear: both;"></div><?php
                        
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
