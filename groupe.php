<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');
    
include(__ROOT__."includes/bddConnect.php");
require_once(__ROOT__."includes/comptes.php");
require_once(__ROOT__."includes/groupes.php");
require_once(__ROOT__.'includes/droits.php');
require_once(__ROOT__."includes/sys.php");

if(!$_SESSION['connecte']){
    header('Location: login.php?err=1');
}

if(IsMaintenance() && !droit($_SESSION['id'], 'IgnoreMaintenance'))
    header('Location: index.php?maintenance');

$currentpage = "groupe";
    
$successbox = false;
$errorbox = false;
$warningbox = false;
$errorString = "";
$errorStringNeedsBR = false;
$successString = "";
$successStringNeedsBR = false;
$warningString = "";

if(isset($_GET['id'])){
    $groupe = getGroupe($_GET['id']);
    if($groupe === false)
        $groupe = getGroupe(1);
}elseif(isset($_POST['id'])){
    $groupe = getGroupe($_POST['id']);
    if($groupe === false)
        $groupe = getGroupe(1);
}else
    $groupe = getGroupe(1);
$compteDansGroupe = compteDansGroupe($_SESSION['id'], $groupe['id']);

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
    <script type="text/javascript" src="js/groups.js"></script>
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
				<div id="box1" class="box-style1">
                    <h2 class="title">
                        <?php
                        echo 'Group';
                        ?>
                    </h2>
                    <p class="byline"><?php echo $groupe['nom']; ?></p>
                    <?php
                        if($groupe['publique'] == 1)
                            echo '<h3>'.$groupe['description'].'</h3>';
                    ?>
			    </div>
                <div id="box2" class="box-style2">
                    <?php
                    if($groupe['publique']!= 1){
                        echo '<h2 class="title">Unfortunately, this group is listed as "Private" and cannot be seen.</h2>';
                        ?>
                        <table class="tablePage">
                            <tr>
                                <td style="width: 42%;" class="leftCell">Name:</td>
                                <td class="rightCell"><?php echo $groupe['nom']; ?></td>
                            </tr>
                            <tr>
                                <td class="leftCell">Visibility:</td>
                                <td class="rightCell">Private</td>
                            </tr>
                        </table>
                        <div style="margin-top: 30px;"></div>
                        <?php
                    }else{
                        ?>
                        <h2 class="title">Informations</h2>
                        <table class="tablePage">
                            <tr>
                                <th colspan="2">Informations</th>
                            </tr>
                            <tr>
                                <td style="width: 42%;" class="leftCell">Name:</td>
                                <td class="rightCell"><?php echo $groupe['nom']; ?></td>
                            </tr>
                            <tr>
                                <td class="leftCell">Visibility:</td>
                                <td class="rightCell">
                                    <?php
                                    if($groupe['publique'] != 1)
                                        echo 'Private';
                                    else
                                        echo 'Public';
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="leftCell">Color:</td>
                                <td class="rightCell"><div style="height: 20px; width: 100px; background-color: <?php echo $groupe['couleur']; ?>;"></div></td>
                            </tr>
                            <?php
                            if($compteDansGroupe){
                                // Le visiteur fait partie du groupe
                                ?>
                                <tr>
                                    <td class="leftCell">Am I in this group?</td>
                                    <td class="rightCell">Yes</td>
                                </tr>
                                <tr>
                                    <td class="leftCell">Can peoples apply to this group?</td>
                                    <td class="rightCell">
                                        <?php
                                        if($groupe['applicable'] != 1)
                                            echo 'No';
                                        else
                                            echo 'Yes';
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }else{
                                // Le visiteur ne fait pas partie du groupe
                                ?>
                                <tr>
                                    <td class="leftCell">Am I in this group ?</td>
                                    <td class="rightCell">No</td>
                                </tr>
                                <tr>
                                    <td class="leftCell">Can I apply to this group ?</td>
                                    <td class="rightCell">
                                        <?php
                                        if($groupe['applicable'] != 1)
                                            echo 'No';
                                        else
                                            echo 'Yes';
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            <tr>
                                <td class="leftCell">Number of members:</td>
                                <td class="rightCell"><?php echo nbMembresInGroup($groupe['id']); ?></td>
                            </tr>
                            <tr>
                                <td class="leftCell">Members:</td>
                                <td class="rightCell">
                                    <?php
                                    $comptes = getComptesDansGroupe($groupe['id']);
                                    for($i=0; $i<count($comptes); $i++){
                                        if($i == 0)
                                            echo '<a href="account.php?id='.$comptes[$i].'">'.getPseudo($comptes[$i]).'</a>';
                                        else
                                            echo ', <a href="account.php?id='.$comptes[$i].'">'.getPseudo($comptes[$i]).'</a>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>
                        <ul class="style1">
                            <?php
                            if(!$compteDansGroupe && $groupe['applicable'] == 1){
                                ?>
                                <li><a class="joinGroupLink" href="#" idGroupe="<?php echo $groupe['id']; ?>">Join this group!</a></li>
                                <?php
                            }
                            ?>
                        </ul>
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
