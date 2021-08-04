<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');
    
include(__ROOT__."includes/bddConnect.php");
include(__ROOT__."includes/verifyLogin.php");
require_once(__ROOT__."includes/forums.php");
require_once(__ROOT__."includes/droits.php");
require_once(__ROOT__."includes/groupes.php");
require_once(__ROOT__."includes/sys.php");

if(!$_SESSION['connecte']){
    header('Location: login.php?err=1');
}

if(IsMaintenance() && !droit($_SESSION['id'], 'IgnoreMaintenance'))
    header('Location: index.php?maintenance');

$currentpage = "administrativePages_forums";

if(droit($_SESSION['id'], 'manipulateForums')){

    $forums = Array();
    $forum = Array();

    $viewSpecificForum = false;

    if(isset($_GET['id'])){
        $forum = getForum($_GET['id']);
        if(forumExists($_GET['id']))
            $viewSpecificForum = true;
    }

    if(isset($_POST['saveSettings'])){
        /*
        * On veut enregistrer les modifications sur une section du forum
        */
        if(forumExists($_POST['id'])){
            if(isset($_POST['nom']))
                forumSaveName($_POST['id'], $_POST['nom']);
            if(isset($_POST['couleur']))
                forumSaveColor($_POST['id'], $_POST['couleur']);
            if(isset($_POST['visibilityDefault']))
                forumDefaultVisibility($_POST['id'], $_POST['visibilityDefault']);
            
            // Groupes
            $groupes = getGroupes();
            for($i=0; $i<count($groupes); $i++){
                if(isset($_POST['visibilityGroup'.$groupes[$i]['id']]))
                    forumEditGroupVisibility($_POST['id'], $groupes[$i]['id'], $_POST['visibilityGroup'.$groupes[$i]['id']]);
            }
        }
    }

    if(isset($_POST['createSection'])){
        /*
        * Signifie qu'on veut créer une section au forum
        */
        if(isset($_POST['couleur']) && isset($_POST['nom']) && isset($_POST['visibilityDefault'])){
            // Totues les informations sont présente
            forumCreateSection($_POST['nom'], $_POST['couleur'], $_POST['visibilityDefault']);
        }
    }
    
    if(isset($_GET['d'])){
        /*
        * On veut supprimer une section
        */
        forumDeleteSection($_GET['d']);
    }

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Diamond Craft - Administrative Page - Forums</title>
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
    <script type="text/javascript" src="js/colorpicker.js"></script>
    <script type="text/javascript" src="js/global.js"></script>
    <script type="text/javascript" src="js/administrative_forums.js"></script>
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
                    <h2 class="title">
                        <?php
                        echo 'Administrative page - Forums';
                        ?>
                    </h2>
                    <p class="byline">Manage forum sections and rights.</p>
                    <?php
                    if(droit($_SESSION['id'], 'manipulateForums')){
                        ?>
                        <div id="addForumSectionDialog">
                            <form name="createForm" method="post" action="administrative_forums.php">
                                <input type="hidden" name="createSection" value="1"/>
                                <table class="tablePage">
                                    <tr>
                                        <th>Options</th>
                                        <th>Values</th>
                                    </tr>
                                    <tr>
                                        <td class="leftCell">Name:</td>
                                        <td class="rightCell">
                                            <input class="inputText" name="nom" value="" type="text" maxlength="50" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="leftCell">Color:</td>
                                        <td class="rightCell">
                                            <input type="text" name="couleur" value="#18709e" class="colorPicker inputText" maxlength="9"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="leftCell">Default visibility:</td>
                                        <td class="rightCell">
                                            <select name="visibilityDefault">
                                                <option value="1" selected="selected">Can view</option>
                                                <option value="0">Can't view</option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                                <p class="description">
                                    The name can contain a maximum of 50 characters;<br />
                                    <br />
                                    In the color field, you must enter a color code (#18709e by default, which is <span style="color: #18709e;">this color</span>). There is a Windows freeware that 
                                    lets you pick a color and it gives you the code. It's called "LaBoiteACouleurs" (<a href="http://pourpre.com/colorbox/download.php?lang=en" target="_blank">download</a>);<br />
                                    <br />
                                    The default visibility makes it possible to access this section for specifics groups only.<br />
                                    If you want every members to be able to see it, put "Can view". In the other case, put it at "Can't view" and edit the section's visibility options when you finished 
                                    creating it.
                                </p>
                                <div id="createButtonConfirm">Create!</div>
                            </form>
                        </div>
                        <p class="description">
                            <strong>This is a private page.</strong><br />Use this page to manage the forum. You can add, edit & delete sections but also edit the privacy options.<br />
                            You can make some sections private to some groups, for example.
                        </p>
                        <?php
                        if($viewSpecificForum){
                            ?>
                            <div id="backButton" style="width: 100px; height: 33px; margin: 10px;">Back</div>
                            <div id="deleteButton" style="color: red;">Delete this section and all of its content</div>
                            <form name="forumForm" action="administrative_forums.php" method="post">
                                <input type="hidden" name="saveSettings" value="1"/>
                                <input type="hidden" id="id" name="id" value="<?php echo $forum['id']; ?>"/>
                                <table class="tablePage">
                                    <tr>
                                        <th>Forum options</th>
                                        <th>Values</th>
                                    </tr>
                                    <tr>
                                        <td class="leftCell">Name:</td>
                                        <td class="rightCell">
                                            <input type="text" name="nom" class="inputText" maxlength="50" value="<?php echo $forum['nom']; ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="leftCell">Color code:</td>
                                        <td class="rightCell">
                                            <input type="text" name="couleur" maxlength="9" value="<?php echo $forum['couleur']; ?>" class="colorPicker inputText"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="leftCell">Default visibility:</td>
                                        <td class="rightCell">
                                            <select name="visibilityDefault">
                                                <?php
                                                if($forum['voirDefaut'] == 1)
                                                    echo '<option value="1" selected="selected">Can view</option><option value="0">Can\'t view</option>';
                                                else
                                                    echo '<option value="1">Can view</option><option selected="selected" value="0">Can\'t view</option>';
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                                <p><br /></p>
                                <p class="description">
                                    Allow the visibility of this section for certain groups only.
                                </p>
                                <table class="tablePage">
                                    <tr>
                                        <th style="width: 300px;">Groups</th>
                                        <th>Special access</th>
                                    </tr>
                                    <?php
                                    $groupes = getGroupes();
                                    for($i=0; $i<count($groupes); $i++){
                                        $groupes[$i] = getGroupe($groupes[$i]['id']);
                                        ?>
                                        <tr>
                                            <td class="leftCell"><?php echo $groupes[$i]['nom']; ?></td>
                                            <td class="rightCell">
                                                <select name="visibilityGroup<?php echo $groupes[$i]['id']; ?>">
                                                    <?php
                                                    if(droitGroupe($groupes[$i]['id'], 'moderatorForum_'.$forum['id']))
                                                        echo '<option value="1">Can view</option><option value="2" selected="selected">Moderator</option><option value="0">Section\'s default value</option>';
                                                    elseif(droitGroupeVoirForum($groupes[$i]['id'], $forum['id']))
                                                        echo '<option value="1" selected="selected">Can view</option><option value="2">Moderator</option><option value="0">Section\'s default value</option>';
                                                    else
                                                        echo '<option value="1">Can view</option><option value="2">Moderator</option><option selected="selected" value="0">Section\'s default value</option>';
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                                <div id="saveSettingsButton" style="width: 200px; height: 33px; margin-top: 10px;">Save settings</div>
                            </form>
                            <?php
                        }else{
                            $forums = getForums();
                            ?>
                            <p><br />List of all the created sections</p>
                            <ul>
                                <?php
                                for($i=0; $i<count($forums); $i++){
                                    $forums[$i] = getForum($forums[$i]['id']);
                                    echo '<li style="color: #'.$forums[$i]['couleur'].';"><a href="administrative_forums.php?id='.$forums[$i]['id'].'">'.$forums[$i]['nom'].'</a></li>';
                                }
                                ?>
                            </ul>
                            <div id="createButton">Add a section</div>
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
