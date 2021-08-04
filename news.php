<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');
    
include(__ROOT__."includes/bddConnect.php");
include(__ROOT__."includes/verifyLogin.php");
require_once(__ROOT__."includes/news.php");
require_once(__ROOT__."includes/bbcodes.php");
require_once(__ROOT__."includes/comptes.php");
require_once(__ROOT__."includes/dates.php");
require_once(__ROOT__."includes/sys.php");

if(IsMaintenance() && !droit($_SESSION['id'], 'IgnoreMaintenance'))
    header('Location: index.php?maintenance');

$currentPage = "news";
$loginSuccess = false;
$loginError = false;

$successbox = false;
$errorbox = false;
$warningbox = false;
$errorString = "";
$successString = "";
$warningString = "";

if($_SESSION['connecte']){
    // Mise à jour de l'ID de la dernière nouvelle lue
    updateIDLastNewSeen($_SESSION['id']);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Diamond Craft - News</title>
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
    <script type="text/javascript" src="js/news.js"></script>
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
                    <h2 class="title">News</h2>
                    <?php
                    $news = getNews(10);
                    for($i=0; $i<count($news); $i++){
                        ?>
                        <p class="byline"><?php echo $news[$i]['title']; ?>, by <?php echo $news[$i]['postedBy']; ?></p>
                        <div style="padding: 10px; border: 1px solid white; white-space: pre-line;">
                            <p>
                                <?php echo smileys(bbcodes($news[$i]['content'], true)); ?>
                            </p>
                            <p class="description">
                                <br />
                                This post has been first published by <a href="account.php?id=<?php echo $news[$i]['idCompte']; ?>"><?php echo getPseudo($news[$i]['idCompte']); ?></a>, 
                                <?php echo convertirDatePublicationEnString($news[$i]['timestamp']); ?> ago.
                            </p>
                        </div>
                        <div style="margin-bottom: 90px;"></div>
                        <?php
                    }
                    ?>
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
