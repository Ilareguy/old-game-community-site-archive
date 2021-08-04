<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');
    
include(__ROOT__."includes/bddConnect.php");
include(__ROOT__."includes/verifyLogin.php");
require_once(__ROOT__."includes/comptes.php");
require_once(__ROOT__."includes/bbcodes.php");
require_once(__ROOT__."includes/groupes.php");

$currentPage = "homepage";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Welcome to Diamond Craft!</title>
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
<body class="homepage">
<?php
include("subPages/header.php");
?>
<div id="page" class="container">
	<div id="page-bgtop">
		<div id="page-bgbtm">
            <?php
            if(IsMaintenance()){
                echo '<div class="warningBox"><p>The website is down for maintenance. Some features are not available. Please come back later!</p></div>';
            }
            ?>
			<div id="two-columns">
				<div id="column1">
					<h2>Welcome to<br />Diamond Craft!</h2>
					<p class="text1">Minecraft Multiplayer Server</p>
					<p class="text2"></p>
				</div>
				<div id="column2">
					<div class="image-style1"></div>
				</div>
			</div>
			<div id="three-columns">
				<div id="columnA">
					<!--<div class="image-style2 image-style2a"><img src="images/multiplay.png" alt="" /><span></span></div>-->
                    <!--<h3 style="color: red; font-size: 130%;">Hey, you!</h3>
					<p>
                        <b>We need your help!</b><br />
                        As you can see, the server is down. The package we paid for has reached an end.
                        Every 6 months, we have to pay $180 <b>from our own pockets</b>.
                        If you'd like to see the server back online as soon as possible,
                        please consider donating a (very) small amount of money to help us!
                    </p>
					<p class="link1"><a href="VIP.php">I'll help !</a></p>-->
				</div>
				<div id="content">
                    <h1>Diamond Craft</h1>
                    <p>
                        "Diamond Craft" is currently offline.<br />
                        Please see the <a href="news.php">News</a> section to get further information.
                    </p>
                    <!--<p>
                        Presently, "Diamond Craft" is a 50-player-slot dedicated server for MineCraft. The server is hosted 
                        by "<strong><a target="_blank" href="https://www.beastnode.com/">BeastNode</a></strong>", and it is 
                        based out in the bustling city of New York.<br />
                        The "Diamond Craft" community is composed and based off a group of friendly, outgoing and down-to-earth 
                        players; were open to new ideas, suggestions and of course, salute new players with a cordial sense 
                        of respect.
                    </p>-->
                    <p>
                        You're more than welcome, and in fact, encouraged to register through our website, participate in our 
                        forum and get involved within the confines of the "Diamond Craft" community.
                    </p>
                    <p>
                        The server is fresh and pristine, anticipating new players such as yourself to join in on the fun! 
                        So what are you waiting for? Take off your shoes, hang up your jacket and make yourself at home!
                    </p>
                    <p>
                        The server is currently being managed by <strong><?php echo bbcodes_player("{player}J-Rex{/player}"); ?></strong>, the founder
                        of the<br />
                        community, and <strong><?php echo bbcodes_player("{player}Totoila{/player}"); ?></strong><br />
                        <strong>(<a href="team.php">Who are we?</a>)</strong>.
                    </p>
                    <p>
                        Welcome to "Diamond Craft"!
                    </p>
                </div>
                <div id="columnB">
					<h3>Minecraft Server</h3>
                    <h1>Offline</h1>
					<!--<p>Make yourself at home, join the family!</p>
                    <h1>198.12.123.133</h1>-->
					<!-- <p class="link1"><a href="#">Learn More</a></p> -->
					<h3>Mumble Server</h3>
                    <h1>Offline</h1>
                    <!--<h1>184.154.31.51:63340</h1>
                    <p>Password: <strong>jqxxmfqenr</strong></p>-->
					<!-- <p class="link1"><a href="#">Learn More</a></p> -->
				</div>
				<div class="clearfix">&nbsp;</div>
			</div>
        </div>
	</div>
</div>
<div id="footer" class="container">
	<p>Diamond Craft. Website designed by <a href="mailto:totoila@totoila.com">Anthony Ilareguy (Totoila)</a>. Hosted by <a href="http://www.milsuitefx.com" target="_blank">Milsuite FX</a></p>
</div>
</body>
</html>
