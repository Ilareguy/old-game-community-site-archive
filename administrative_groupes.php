<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');
    
include(__ROOT__."includes/bddConnect.php");
include(__ROOT__."includes/verifyLogin.php");
require_once(__ROOT__."includes/groupes.php");
require_once(__ROOT__."includes/droits.php");
require_once(__ROOT__."includes/sys.php");

if(!$_SESSION['connecte'])
    header('Location: login.php');

if(IsMaintenance() && !droit($_SESSION['id'], 'IgnoreMaintenance'))
    header('Location: index.php?maintenance');

$currentpage = "administrativePages_news";

if(droit($_SESSION['id'], 'manipulateGroups')){
    $viewSpecificGroup = false;

    if(isset($_POST['nom']) && isset($_POST['couleur']) && isset($_POST['visibility']) && isset($_POST['description'])){
        /*
        * Création d'un nouveau groupe
        */
        if(droit($_SESSION['id'], 'manipulateGroups') && $_POST['nom'] != "" && $_POST['couleur'] != '' && $_POST['visibility'] != ''){
            $IDDirigeant = -1;
            if(isset($_POST['IDDirigeant']))
                $IDDirigeant = $_POST['IDDirigeant'];
            creerGroupe($_POST['nom'], $_POST['description'], isset($_POST['access']), $_POST['visibility'], $_POST['couleur'], $IDDirigeant);
        }
    }

    if(isset($_GET['id']) && groupExists($_GET['id'])){
        $viewSpecificGroup = true;
    }
    
    if(isset($_POST['group']) && isset($_POST['edit'])){
        /**
        * Éditer un groupe
        */
        
        // Droits
        modifierDroitGroupe($_POST['group'], 'manipulateAccounts', ((isset($_POST['rights_manipulateAccounts'])) ? $_POST['rights_manipulateAccounts'] : 0));
        modifierDroitGroupe($_POST['group'], 'manipulatePolls', ((isset($_POST['rights_manipulatePolls'])) ? $_POST['rights_manipulatePolls'] : 0));
        modifierDroitGroupe($_POST['group'], 'manipulateNews', ((isset($_POST['rights_manipulateNews'])) ? $_POST['rights_manipulateNews'] : 0));
        modifierDroitGroupe($_POST['group'], 'manipulateForums', ((isset($_POST['rights_manipulateForum'])) ? $_POST['rights_manipulateForum'] : 0));
        modifierDroitGroupe($_POST['group'], 'manipulateGroups', ((isset($_POST['rights_manipulateGroups'])) ? $_POST['rights_manipulateGroups'] : 0));
        
        // Description
        modifierGroupeDescription($_POST['group'], $_POST['description']);
        
        // Icône
        $Icon = ((isset($_POST['icon']) && $_POST['icon'] != '0') ? $_POST['icon'] : GetIconGroup($_POST['group']));
        modifierIconeGroupe($_POST['group'], $Icon);
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Diamond Craft - Administrative Page - Groups</title>
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
    <script type="text/javascript" src="js/administrative_groups.js"></script>
    <script type="text/javascript" src="js/colorpicker.js"></script>
</head>
<body>
<?php
include(__ROOT__."subPages/header.php");
?>
<div id="page" class="container">
	<div id="page-bgtop">
		<div id="page-bgbtm">
            <div id="messageBoxes">
            </div>
			<div id="content">
				<div id="box1" class="box-style1">
                    <h2 class="title">
                        <?php
                        echo 'Administrative page - Groups';
                        ?>
                    </h2>
                    <p class="byline">Edit, remove & add groups</p>
                    <p class="description">
                        <strong>This is a private page.</strong><br />Use this page to manage groups.<br />
                        Please note, to edit groups permissions regarding the forum, see this page : <a href="administrative_forums.php">Forum Administration</a>
                    </p>
			    </div>
                <div id="box2" class="box-style2">
                    <?php
                    if(droit($_SESSION['id'], 'manipulateGroups')){
                        if($viewSpecificGroup){
                            // Un groupe spécifique
                            $groupe = getGroupe($_GET['id']);
                            ?>
                            <table style="width: 100%;">
                                <tr>
                                    <td><div style="margin-bottom: 6px;" id="backButton">Back</div></td>
                                </tr>
                            </table>
                            
                            <form id="mainFrm" method="POST" action="administrative_groupes.php">
                                <input type="hidden" value="<?php echo $groupe['id']; ?>" name="group"/>
                                <input type="hidden" value="true" name="edit"/>
                                <table class="tablePage">
                                    <tr>
                                        <th colspan="2">Group permissions</th>
                                    </tr>
                                    <tr>
                                        <td minitip="" title="Create, Edit, Delete, Ban & Unban Website Accounts<br /><span style='color: red; font-weight: bold;'>Be careful with that!</span>">Manipulate Accounts</td>
                                        <td>
                                            <select name="rights_manipulateAccounts">
                                                <?php
                                                if(droitGroupe($groupe['id'], 'manipulateAccounts')){
                                                    ?>
                                                    <option selected="selected" value="1">Yes</option>
                                                    <option value="0">No</option>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <option value="1">Yes</option>
                                                    <option selected="selected" value="0">No</option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td minitip="" title="Add, Edit, Delete and Finalize Website Polls">Manipulate Polls</td>
                                        <td>
                                            <select name="rights_manipulatePolls">
                                                <?php
                                                if(droitGroupe($groupe['id'], 'manipulatePolls')){
                                                    ?>
                                                    <option selected="selected" value="1">Yes</option>
                                                    <option value="0">No</option>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <option value="1">Yes</option>
                                                    <option selected="selected" value="0">No</option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td minitip="" title="Create, Edit and Delete Website News">Manipulate News</td>
                                        <td>
                                            <select name="rights_manipulateNews">
                                                <?php
                                                if(droitGroupe($groupe['id'], 'manipulateNews')){
                                                    ?>
                                                    <option selected="selected" value="1">Yes</option>
                                                    <option value="0">No</option>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <option value="1">Yes</option>
                                                    <option selected="selected" value="0">No</option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td minitip="" title="Create, Edit and Delete Forum Sections">Manipulate Forum</td>
                                        <td>
                                            <select name="rights_manipulateForum">
                                                <?php
                                                if(droitGroupe($groupe['id'], 'manipulateForums')){
                                                    ?>
                                                    <option selected="selected" value="1">Yes</option>
                                                    <option value="0">No</option>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <option value="1">Yes</option>
                                                    <option selected="selected" value="0">No</option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td minitip="" title="Create, Edit and Delete Groups">Manipulate Groups</td>
                                        <td>
                                            <select name="rights_manipulateGroups">
                                                <?php
                                                if(droitGroupe($groupe['id'], 'manipulateGroups')){
                                                    ?>
                                                    <option selected="selected" value="1">Yes</option>
                                                    <option value="0">No</option>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <option value="1">Yes</option>
                                                    <option selected="selected" value="0">No</option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                                <h4 style="margin-bottom: 0px; margin-top: 25px;">Icon:</h4>
                                <select name="icon">
                                    <option value="0" selected="selected">Do not change</option>
                                    <?php
                                    $dirname = __ROOT__.'images/icons/groups/';
                                    $dir = opendir($dirname);

                                    while($fichier = readdir($dir)){
                                        $Ext = strtolower(substr($fichier, (strrpos($fichier, '.') + 1)));
                                        if($fichier != '.' && $fichier != '..' && !is_dir($dirname.$fichier) && ($Ext == "gif" || $Ext == "jpg" || $Ext == "png" || $Ext == "jpeg")){
                                            echo '<option value="'.$fichier.'" style="background-image:url(images/icons/groups/'.$fichier.');background-repeat:no-repeat;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$fichier.'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <h4 style="margin-bottom: 0px; margin-top: 25px;">Description:</h4>
                                <textarea name="description" style="width: 554px; height: 200px; margin-top: 10px;"><?php echo $groupe['description']; ?></textarea>
                            </form>
                            <table style="width: 100%;">
                                <tr>
                                    <td><div style="margin-top: 6px;" id="saveButton">Save settings</div></td>
                                </tr>
                                <?php
                                if($groupe['deletable']){
                                    ?>
                                    <tr>
                                        <td><div style="margin-top: 6px; color: red;" id="deleteButton" idGroupe="<?php echo $_GET['id']; ?>">Delete this group</div></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table>
                            <?php
                        }else{
                            // Voir la liste des groupes et les options des groupes
                            ?>
                            <table style="width: 100%;">
                                <tr>
                                    <td><div style="margin-bottom: 6px;" id="creerGroupeButton">Create a group</div></td>
                                </tr>
                            </table>
                            
                            <div id="createGroupDialog" style="display: none;">
                                <form name="newGroupForm" method="post" action="administrative_groupes.php">
                                <table style="height: auto;" class="tablePage">
                                    <tr>
                                        <th colspan="2">New group informations</th>
                                    </tr>
                                    <tr>
                                        <td>Name: </td>
                                        <td><input name="nom" title="50 characters maximum" minitip=""  type="text" id="newGroupInput_name" maxlength="50"/></td>
                                    </tr>
                                    <tr>
                                        <td>Color: </td>
                                        <td><input name="couleur" type="text" id="newGroupInput_color" maxlength="7" class="colorPicker"/></td>
                                    </tr>
                                    <tr>
                                        <td>Visibility: </td>
                                        <td>
                                            <select name="visibility" title="Wether the group will be visible by other players or not" minitip="" id="newGroupInput_visibility">
                                                <option value="1">Public</option>
                                                <option value="0">Private</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Description: </td>
                                        <td>
                                            <textarea name="description" style="width: 200px; height: 100px; font-size: 70%;" id="newGroupInput_description" title="Can be set later" minitip="" ></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>IDDirigeant: </td>
                                        <td><input name="IDDirigeant" type="text" id="newGroupInput_IDDirigeant" title="This space is for whoever has direct access to the database<br><br>Leave this space empty if you don't know what it is used for" minitip="" /></td>
                                    </tr>
                                    <tr>
                                        <td>Free to join: </td>
                                        <td>
                                            <input name="access" type="checkbox" id="newGroupInput_access" minitip="" title="Wether players are able to join this group if they want or not"></checkbox>
                                        </td>
                                    </tr>
                                </table>
                                </form>
                                <div style="margin-top: 3px; margin-bottom: 3px; width: 100%;" id="newGroup_createButton">Create group!</div>
                            </div>
                            
                            <table class="tablePage">
                                <tr>
                                    <th>Name</th>
                                    <th>Color</th>
                                    <th>Visibility</th>
                                    <th>Free To Join</th>
                                </tr>
                                <?php
                                    /*
                                    * Une liste de tous les groupes.
                                    */
                                    $groups = getGroupes();
                                    for($i = 0; $i < count($groups); $i++){
                                        $groupe = getGroupe($groups[$i]['id']);
                                        ?>
                                        <tr>
                                            <td><a href="administrative_groupes.php?id=<?php echo $groups[$i]['id']; ?>"><?php echo $groupe['nom']; ?></a></td>
                                            <td style="width: 70px; background-color: <?php echo $groupe['couleur']; ?>;"></td>
                                            <td style="text-align: center;">
                                                <?php
                                                if($groupe['publique'] == '1')
                                                    echo 'Public';
                                                else
                                                    echo 'Private';
                                                ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php
                                                if($groupe['applicable'] == '1')
                                                    echo 'Yes';
                                                else
                                                    echo 'No';
                                                ?>
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
