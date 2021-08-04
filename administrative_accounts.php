<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');
    
include(__ROOT__."includes/bddConnect.php");
include(__ROOT__."includes/verifyLogin.php");
require_once(__ROOT__."includes/droits.php");
require_once(__ROOT__."includes/dates.php");
require_once(__ROOT__.'includes/groupes.php');
require_once(__ROOT__."includes/sys.php");

if(!$_SESSION['connecte']){
    header('Location: login.php?err=1');
}

if(IsMaintenance() && !droit($_SESSION['id'], 'IgnoreMaintenance'))
    header('Location: index.php?maintenance');

$currentpage = "administrativePages_accounts";

$viewSpecificAccount = false;
$viewSearchResult = false;
$error = false;
$errorString = "";
$info = false;
$infoString = "";

if(droit($_SESSION['id'], 'manipulateAccounts')){
    if(isset($_GET['id'])){
        $account = getCompte($_GET['id']);
        if($account !== false)
            $viewSpecificAccount = true;
    }elseif(isset($_POST['id'])){
        $account = getCompte($_POST['id']);
        if($account !== false)
            $viewSpecificAccount = true;
    }
    
    if(isset($_POST['id']) && isset($_POST['save'])){
        // Pseudo
        if(isset($_POST['pseudo']) && $_POST['pseudo'] != ""){
            $account['pseudo'] = $_POST['pseudo'];
            compteChangerPseudo($account['id'], $account['pseudo']);
        }
        
        // Email
        if(isset($_POST['email']) && $_POST['email'] != ""){
            $account['email'] = $_POST['email'];
            compteChangerEmail($account['id'], $account['email']);
        }
        
        // First Name
        if(isset($_POST['firstName']) && $_POST['firstName'] != ""){
            $account['firstName'] = $_POST['firstName'];
            compteChangerFirstName($account['id'], $account['firstName']);
        }
        
        // Last name
        if(isset($_POST['lastName']) && $_POST['lastName'] != ""){
            $account['lastName'] = $_POST['lastName'];
            compteChangerLastName($account['id'], $account['lastName']);
        }
        
        // Password
        if(isset($_POST['pwd']) && $_POST['pwd'] != ""){
            $account['pwd'] = $_POST['pwd'];
            compteChangerPwd($account['id'], $account['pwd']);
        }
        
        // Status du compte
        if(isset($_POST['status']) && ($_POST['status'] == '1' || $_POST['status'] == '0')){
            /*
            * 1 = bannis
            * 0 = normal
            */
            $account['banned'] = $_POST['status'];
            if($_POST['status'] == '1')
                compteBan($account['id']);
            else
                compteUnban($account['id']);
        }
        
        // Groupes
        $allGroupes = getGroupes();
        for($i = 0; $i < count($allGroupes); $i++){
            if(isset($_POST['group' . $allGroupes[$i]['id']]))
                joinGroup($account['id'], $allGroupes[$i]['id']);
            else
                leaveGroup($account['id'], $allGroupes[$i]['id']);
        }
    }
    
    if(isset($_GET['q']) && $_GET['q'] != ""){
        // Recherche
        if(!preg_match("/^[A-Za-z0-9_ ]+$/", $_GET['q'])){
            $error = true;
            if($errorString != "")
                $errorString .= "<br />";
            $errorString .= "Invalid characters in the query. Please use alphanumeric characters only.";
        }else{
            if(strpos($_GET['q'], ' ') !== false){
                $info = true;
                if($infoString != "")
                    $infoString .= "<br />";
                $infoString .= "Putting spaces in the query can significantly reduce search results";
            }
            $viewSearchResult = true;
            $accounts = searchAccounts($_GET['q'], true, true, true);
        }
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Diamond Craft - Administrative Page - Accounts</title>
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
    <script type="text/javascript" src="js/administrative_accounts.js"></script>
    <script type="text/javascript" src="js/server_command.php"></script>
</head>
<body>
<?php
require_once(__ROOT__."subPages/header.php");
?>
<div id="page" class="container">
	<div id="page-bgtop">
		<div id="page-bgbtm">
            <div id="messages">
                <?php
                if($error)
                    echo '<div class="errorBox"><p>' . $errorString . '</p></div>';
                if($info)
                    echo '<div class="informationBox"><p>' . $infoString . '</p></div>';
                ?>
            </div>
			<div id="content">
				<div id="box1" class="box-style1">
                    <h2 class="title">
                        <?php
                        echo 'Administrative page - Accounts';
                        ?>
                    </h2>
                    <p class="byline">Manage accounts.</p>
                    <?php
                    if(droit($_SESSION['id'], 'manipulateAccounts')){
                        ?>
                        <p class="description">
                            This private page is used to manage website accounts.<br />
                            You can edit specific accounts permissions, put the account in a group and more.
                        </p>
                        <?php
                        if($viewSpecificAccount){
                            // Voir un compte spécifique
                            $groupes = getCompteGroupes($account['id']);
                            ?>
                            <div id="backButton" style="margin-bottom: 10px;">Back</div>
                            <form name="accountForm" action="administrative_accounts.php" method="POST">
                                <input type="hidden" name="id" value="<?php echo $account['id']; ?>"/>
                                <input type="hidden" name="save" value="1"/>
                                <table class="tablePage" style="text-align: center;">
                                    <tr>
                                        <th>Item</th>
                                        <th>Value</th>
                                    </tr>
                                    <tr>
                                        <td>Nickname</td>
                                        <td>
                                            <input name="pseudo" type="text" maxlength="20" value="<?php echo $account['pseudo']; ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Last Login</td>
                                        <td>
                                            <?php
                                            if($account['lastLogin'] == 0)
                                                echo 'Never';
                                            else
                                                echo convertirDatePublicationEnString($account['lastLogin']) . ' ago';
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Last Login IP</td>
                                        <td>
                                            <?php echo $account['lastLoginIP']; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>First Name</td>
                                        <td>
                                            <input name="firstName" type="text" maxlength="30" value="<?php echo $account['firstName']; ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Last Name</td>
                                        <td>
                                            <input name="lastName" type="text" maxlength="50" value="<?php echo $account['lastName']; ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td>
                                            <input name="email" type="text" maxlength="30" value="<?php echo $account['email']; ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Password</td>
                                        <td>
                                            <input name="pwd" type="text" maxlength="30" value=""/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Account Status</td>
                                        <td>
                                            <select name="status">
                                                <?php
                                                if($account['banned'] == 1){
                                                    echo '<option value="1" selected="selected">Banned</option>';
                                                    echo '<option value="0">Normal</option>';
                                                }else{
                                                    echo '<option value="1">Banned</option>';
                                                    echo '<option value="0" selected="selected">Normal</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top;">
                                            Groups
                                        </td>
                                        <td style="text-align: left;">
                                            <?php
                                            function _inGroup($id, $groups){
                                                
                                                for($i = 0; $i < count($groups); $i++){
                                                    if($id['id'] == $groups[$i]['id'])
                                                        return true;
                                                }
                                                return false;
                                            }
                                            $allGroupes = getGroupes();
                                            for($i = 0; $i < count($allGroupes); $i++){
                                                if($i > 0)
                                                    echo '<br />';
                                                if(_inGroup($allGroupes[$i], $groupes))
                                                    echo '<input type="checkbox" name="group' . $allGroupes[$i]['id'] . '" checked="checked" id="groupCheckbox' . $allGroupes[$i]['id'] . '"/><label for="groupCheckbox' . $allGroupes[$i]['id'] . '">' . getNomGroupe($allGroupes[$i]['id']) . '</label>';
                                                else
                                                    echo '<input type="checkbox" name="group' . $allGroupes[$i]['id'] . '" id="groupCheckbox' . $allGroupes[$i]['id'] . '"/><label for="groupCheckbox' . $allGroupes[$i]['id'] . '">' . getNomGroupe($allGroupes[$i]['id']) . '</label>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                                <div id="AddToWhitelistButton" username="<?php echo $account['pseudo']; ?>">White-list <?php echo $account['pseudo']; ?></div>
                                <div id="saveButton" style="margin-top: 10px;">Save changes</div>
                            </form>
                            <?php
                        }elseif($viewSearchResult){
                            ?>
                            <p class="byline">Search Result for "<?php echo $_GET['q']; ?>"</p>
                            <div id="backButton" style="margin-bottom: 10px;">Back</div>
                            <table class="tablePage">
                                <tr>
                                    <th>Nickname</th>
                                    <th>Email</th>
                                    <th>Full name</th>
                                </tr>
                            <?php
                            for($i = 0; $i < count($accounts); $i++){
                                $compte = getCompte($accounts[$i]);
                                echo '<tr style="text-align: center;">';
                                echo '<td><a href="administrative_accounts.php?id=' . $compte['id'] . '">' . $compte['pseudo'] . '</a></td>';
                                echo '<td><a href="mailto:' . $compte['email'] . '" minitip="" title="Send an e-mail to ' . $compte['firstName'] . ' ' . $compte['lastName'] . '">' . $compte['email'] . '</a></td>';
                                echo '<td>' . $compte['firstName'] . ' ' . $compte['lastName'] . '</td>';
                                echo '</tr>';
                            }
                            ?>
                            </table>
                            <?php
                        }else{
                            // Page de recherche
                            ?>
                            <form method="get" action="administrative_accounts.php">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td>Search an account : </td>
                                        <td>
                                            <input name="q" style="width: 300px" type="text" minitip="1" title="The website will search for account matching what you enter with first name, last name, nickname or email"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <input style="width: 100%;" type="submit" value="Search"/>
                                        </td>
                                    </tr>
                                </table>
                            </form>
                            <br class="clear"/>
                            <p class="description">
                                20 last login
                            </p>
                            <table class="tablePage">
                                <?php
                                $last = getLastConnexions();
                                for($i = 0; $i < count($last); $i++){
                                    $compte = getCompte($last[$i]);
                                    echo '<tr>';
                                        echo '<td><a href="administrative_accounts.php?id=' . $compte['id'] . '">' . $compte['pseudo'] . '</a> (' . $compte['firstName'] . ' ' .
                                            $compte['lastName'] . ') <<a href="mailto:' . $compte['email'] . '">' . $compte['email'] . '</a>></td>';
                                        echo '<td>' . convertirDatePublicationEnString($compte['lastLogin'], 1) . ' ago</td>';
                                    echo '</tr>';
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
            <div id="sidebar">
                <div id="box3" class="box-style2">
                    <h2 class="title">Stats</h2>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 75%;">Total registered members</td>
                            <td style="text-align: right;"><?php echo GetTotalRegisteredMembers(); ?></td>
                        </tr>
                        <tr>
                            <td style="width: 75%;">Total banned members</td>
                            <td style="text-align: right;"><?php echo GetTotalBannedMembers(); ?></td>
                        </tr>
                    </table>
                    <p style="position: relative; left: 2px;">Latest registered members</p>
                    <ul>
                        <?php
                        $players = GetLatestRegisteredMembers(20);
                        for($i = 0; $i < count($players); $i++){
                            
                            $player = getCompte($players[$i]);
                            echo '<li><a href="administrative_accounts.php?id=' . $player['id'] . '">' . $player['pseudo'] . '</a></li>';
                            
                        }
                        ?>
                    </ul>
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
