<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');
    
include(__ROOT__."includes/bddConnect.php");
include(__ROOT__."includes/verifyLogin.php");
require_once(__ROOT__."includes/droits.php");
require_once(__ROOT__."includes/sys.php");

if(!$_SESSION['connecte']){
    header('Location: login.php?err=1');
}

if(IsMaintenance() && !droit($_SESSION['id'], 'IgnoreMaintenance'))
    header('Location: index.php?maintenance');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Diamond Craft - Administrative Page - Game Server</title>
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
    <script type="text/javascript" src="js/jconfirm.js"></script>
    <script type="text/javascript" src="js/server_command.php"></script>
    <script type="text/javascript">
        $(function(){
            $('#BCBtn').button().click(function(){
                var $this = $(this);
                $this.button('disable');
                SendCommandToServer(('say ' + $('#BCTxt').val()), function(){
                    $this.button('enable');
                    $('#BCTxt').val('');
                });
            });
            
            $('#CustomCMDBtn').button().click(function(){
                var $this = $(this);
                $this.button('disable');
                SendCommandToServer($('#CustomCMDTxt').val(), function(){
                    $this.button('enable');
                    $('#CustomCMDTxt').val('');
                });
            });
        });
    </script>
    <style type="text/css">
        .button{margin-bottom: 20px; width: 100%;}
    </style>
</head>
<body>
<?php
require_once(__ROOT__."subPages/header.php");
?>
<div id="page" class="container">
	<div id="page-bgtop">
		<div id="page-bgbtm">
			<div id="content">
				<div id="box1" class="box-style1">
                    <h2 class="title">Administrative page - Game Server Control</h2>
                    <p class="byline">Control the game server directly from the website</p>
                    <?php
                    if(droit($_SESSION['id'], 'whitelistGameServerCommand')){
                        $news = getAllNews();
                        ?>
                        <div class="button" data-server-command="time set 0">Make it day</div>
                        <div class="button" data-server-command="time set 18000">Make it night</div>
                        <div class="button" data-server-command="toggledownfall">Toggle Down Fall</div>
                        <br />
                        <br />
                        <input type="text" placeholder="Message to broadcast" style="width:100%;margin-bottom:3px;" id="BCTxt"/>
                        <div id="BCBtn" class="button">Broadcast this message</div>
                        <br />
                        <br />
                        <input type="text" placeholder="Custom Command" style="width:100%;margin-bottom:3px;" id="CustomCMDTxt"/>
                        <div id="CustomCMDBtn" class="button">Send this custom command</div>
                        <?php
                    }
                    ?>
			    </div>
                <div id="box2" class="box-style2">
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
