<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');
    
include(__ROOT__."includes/bddConnect.php");
include(__ROOT__."includes/verifyLogin.php");
require_once(__ROOT__."includes/news.php");
require_once(__ROOT__."includes/droits.php");
require_once(__ROOT__."includes/dates.php");
require_once(__ROOT__."includes/sys.php");

if(!$_SESSION['connecte']){
    header('Location: login.php?err=1');
}

if(IsMaintenance() && !droit($_SESSION['id'], 'IgnoreMaintenance'))
    header('Location: index.php?maintenance');

$currentpage = "administrativePages_news";

$viewSpecificNew = false;
$writeANew = false;

if(droit($_SESSION['id'], 'manipulateNews')){
    if(isset($_GET['e']) && newExists($_GET['e'])){
        /*
        * On veut voir une new en particulier
        */
        
        $viewSpecificNew = true;
        $new = getNew($_GET['e']);
    }
    
    if(isset($_GET['add'])){
        /*
        * On veut rédiger une new
        */
        
        $writeANew = true;
    }
    
    if(isset($_POST['add']) && isset($_POST['content']) && isset($_POST['title']) && isset($_POST['author'])){
        /*
        * On veut confimer l'ajout d'une new
        */
        
        ajouterNew($_POST['content'], $_POST['title'], $_SESSION['id'], $_POST['author']);
    }
    
    if(isset($_POST['edit']) && newExists($_POST['edit']) && isset($_POST['content']) && isset($_POST['title']) && isset($_POST['author'])){
        /*
        * On veut confirmer les modifications d'une new
        */
        
        editerNew($_POST['edit'], $_POST['content'], $_POST['title'], $_POST['author']);
    }
    
    if(isset($_GET['d'])){
        /*
        * On veut supprimer une nouvelle
        */
        
        supprimerNew($_GET['d']);
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Diamond Craft - Administrative Page - News</title>
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
    <script type="text/javascript" src="js/administrative_news.js"></script>
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
                    <h2 class="title">Administrative page - News</h2>
                    <p class="byline">Compose, Edit or Remove a News Post</p>
                    <?php
                    if(droit($_SESSION['id'], 'manipulateNews')){
                        $news = getAllNews();
                        ?>
                        <p class="description">
                            This page is used to manage the website news.<br />
                            You may compose, publish, edit or remove any of yours posts.<br />
                            By default, all posts have their "Author" field set to "The DiamondCraft Team", but you may place anything you want in there.<br />
                            Be aware of what you write; proper grammar, no foul language and an informative topic. This section is a privilege and can easily be revoked from all members.<br />
                            "HTML" is allowed in the content of your post, but keep in mind, BBCodes are available for your use as well.
                        </p>
                        <?php
                        if($viewSpecificNew){
                            ?>
                            <div id="backButton" style="margin: 10px;">Cancel</div>
                            <div id="deleteNewButton" style="margin: 10px; color: red;">Permanently remove this post</div>
                            <p class="byline">View or Edit a Post</p>
                            <form name="editNewForm" action="administrative_news.php" method="post">
                                <input type="hidden" id="idNew" name="edit" value="<?php echo $new['id']; ?>"/>
                                <p>Topic:</p>
                                <input type="text" value="<?php echo $new['title']; ?>" maxlength="100" name="title" style="width: 100%;"/>
                                <p><br />Author:</p>
                                <input type="text" value="<?php echo $new['postedBy']; ?>" maxlength="100" name="author" style="width: 100%;"/>
                                <p><br />Content:</p>
                                <textarea name="content" style="width: 100%; height: 300px;"><?php echo $new['content']; ?></textarea>
                            </form>
                            <div style="margin: 10px;" id="saveEditNewButton">Save Changes</div>
                            <?php
                        }else if($writeANew){
                            ?>
                            <div id="backButton" style="margin: 10px;">Cancel</div>
                            <p class="byline">Compose a new post</p>
                            <form name="addNewForm" action="administrative_news.php" method="post">
                                <input type="hidden" name="add" value="1"/>
                                <p>Topic:</p>
                                <input type="text" value="" maxlength="100" name="title" style="width: 100%;"/>
                                <p><br />Author:</p>
                                <input type="text" value="The Diamond Craft Team" maxlength="100" name="author" style="width: 100%;"/>
                                <p><br />Content:</p>
                                <textarea name="content" style="width: 100%; height: 300px;"></textarea>
                            </form>
                            <div style="margin: 10px;" id="saveNewButton">Publish</div>
                            <?php
                        }else{
                            ?>
                            <div id="addNewButton" style="margin: 10px;">Compose a new post</div>
                            <table class="tablePage">
                                <tr>
                                    <th style="width: 60%;">Topics</th>
                                    <th style="width: 30%">Posted</th>
                                    <th>Edit</th>
                                </tr>
                                <?php
                                for($i=0; $i<count($news); $i++){
                                    ?>
                                    <tr>
                                        <td style="text-align: center; font-weight: bold; font-size: 80%;">
                                            <a href="administrative_news.php?e=<?php echo $news[$i]['id']; ?>"><?php echo $news[$i]['title']; ?></a>
                                        </td>
                                        <td style="font-size: 80%;">
                                            <?php
                                            echo convertirDatePublicationEnString($news[$i]['timestamp'], 2) . ' ago';
                                            ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <img alt="Edit" style="cursor: pointer;" src="images/icons/edit.gif" onclick="JavaScript: window.location.href='administrative_news.php?e=<?php echo $news[$i]['id']; ?>'"/>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table>
                            <?php
                        }
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
