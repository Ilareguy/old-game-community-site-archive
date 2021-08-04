<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');
    
include(__ROOT__."includes/bddConnect.php");
include(__ROOT__."includes/verifyLogin.php");
require_once(__ROOT__."includes/dates.php");
require_once(__ROOT__."includes/PM.php");
require_once(__ROOT__."includes/comptes.php");
require_once(__ROOT__."includes/droits.php");
require_once(__ROOT__."includes/bbcodes.php");
require_once(__ROOT__."includes/sys.php");

if(!$_SESSION['connecte']){
    header('Location: login.php?err=1');
}

if(IsMaintenance() && !droit($_SESSION['id'], 'IgnoreMaintenance'))
    header('Location: index.php?maintenance');

$currentPage = "PM";

$viewSpecificMessage = false;
$composeMessage = false;

$successbox = false;
$errorbox = false;
$warningbox = false;
$errorString = "";
$successString = "";
$warningString = "";

if(isset($_GET['id'])){
    $message = getPrivateMessage($_GET['id']);
    if($message['IDCompteTo'] == $_SESSION['id']){
        $viewSpecificMessage = true;
        markPMAsRead($message['ID']);
    }else{
        $errorbox = true;
        $errorString .= "This Private Message isn't yours";
    }
}

if(isset($_GET['delete'])){
    // Deleter un PM
    $message = getPrivateMessage($_GET['delete']);
    if($message !== false){
        if($_SESSION['id'] == $message['IDCompteTo']){
            deletePM($message['ID']);
            $successbox = true;
            $successString = "Private Message deleted";
        }else{
            $errorbox = true;
            $errorString = "This Private Message isn't yours";
        }
    }
}

if(isset($_GET['compose'])){
    $RE = false; $TR = false; $message;
    if(isset($_GET['TR'])){
        $message = getPrivateMessage($_GET['compose']);
        if($message === false){
            // Message inexistant; composition normale
        }else if($message['StringFrom'] != ''){
            // Impossible de TR ce message; composition normale
        }else if($_SESSION['id'] == $message['IDCompteTo']){
            $TR = true;
        }else{
            // Pas notre message; composition normale
        }
    }else{
        $message = getPrivateMessage($_GET['compose']);
        if($message === false){
            // Message n'existe pas; composition normale
        }else if($message['StringFrom'] != ''){
            // Impossible de répondre à ce message; composition normale
        }else if($message['IDCompteTo'] == $_SESSION['id']){
            $RE = true;
        }else{
            // Pas notre message; composition normale
        }
    }
    
    $composeMessage = true;
}

if(isset($_POST['to']) && isset($_POST['title']) && isset($_POST['message'])){
    // Envoyer un message
    $recipients = explode(',', $_POST['to']);
    $countSuccess = 0;
    foreach($recipients as $name){
        $name = trim($name);
        $idTo = getIDFromPseudo($name);
        if($idTo != 0){
            sendPrivateMessage($_SESSION['id'], $idTo, $_POST['title'], $_POST['message']);
            $countSuccess++;
        }
    }
    
    if($countSuccess == 1){
        $successbox = true;
        $successString = "Your message was successfully sent!";
    }else if($countSuccess > 1){
        $successbox = true;
        $successString = $countSuccess . " messages were successfully sent!";
    }
    else{
        $errorbox = true;
        $errorString = "No message was sent. The recipient was not found";
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Diamond Craft - Inbox</title>
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
    <script type="text/javascript" src="js/PM.js"></script>
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
            
            <div id="BBcodesInfos" style="display: none;">
                <?php require_once(__ROOT__."subPages/BBcodesInfos.php"); ?>
            </div>
            
            <div id="smileysInfos" style="display: none;">
                <?php require_once(__ROOT__."subPages/smileysInfos.php"); ?>
            </div>
            
			<div id="content">
				<div id="box1" class="box-style1">
                    <h2 class="title">Inbox</h2>
                    <?php
                    if($viewSpecificMessage){
                        // Voir un message
                        //  $message
                        $pseudoEnvoie = "";
                        if($message['StringFrom'] == '')
                            $pseudoEnvoie = getPseudo($message['IDCompteFrom']);
                        else
                            $pseudoEnvoie = $message['StringFrom'];
                        echo '<input type="hidden" value="' . $message['ID'] . '" id="IDPM"/>';
                        
                        if($pseudoEnvoie == ''){
                            ?>
                            <h3>Sent by [Deleted Member]<br /><?php echo convertirDatePublicationEnString($message['Timestamp'], 3); ?> ago</h3>
                            <?php
                        }else{
                            if($message['StringFrom'] == ''){
                                ?>
                                <h3>Sent by <a href="account.php?id=<?php echo $message['IDCompteFrom'] ?>"><?php echo $pseudoEnvoie; ?></a><br /><?php echo convertirDatePublicationEnString($message['Timestamp'], 3); ?> ago</h3>
                                <?php
                            }else{
                                ?>
                                <h3>Sent by <?php echo $pseudoEnvoie; ?><br /><?php echo convertirDatePublicationEnString($message['Timestamp'], 3); ?> ago</h3>
                                <?php
                            }
                        }
                        ?>
                        <div class="backButton">Back to list</div>
                        <p><br /></p>
                        <h2><?php echo htmlspecialchars($message['Title']); ?></h2>
                        <div style="white-space: pre-line;">
                            <p>
                                <?php echo BBCodes(smileys(htmlspecialchars($message['Message']))); ?>
                            </p>
                        </div>
                        <p><br /><br /></p>
                        <div id="replyButton">Reply</div>
                        <div id="trButton">Transfer Message</div>
                        <div id="deleteButton" style="color: red;">Delete message</div>
                        <div class="backButton">Back to list</div>
                        <?php
                    }else if($composeMessage){
                        // Page d'envoie/transfère/réponse de message
                        ?>
                        <h3>Compose</h3>
                        <?php
                        $messageValue = "";
                        $pseudoTo = "";
                        $title = "";
                        
                        if($TR){
                            $pseudoFrom = getPseudo($message['IDCompteFrom']);
                            $messageValue = "{bold}Tranferred message : <{player}" . $pseudoFrom . "{/player}>{/bold}
-----------------------------------------------
" . $message['Message'];
                            $title = $message['Title'];
                            if($title[0] == 'T' && $title[1] == 'R' && $title[2] == ':'){}
                            else
                                $title = "TR: " . $title;
                        }else if($RE){
                            $pseudoTo = getPseudo($message['IDCompteFrom']);
                            $title = $message['Title'];
                            if($title[0] == 'R' && $title[1] == 'E' && $title[2] == ':'){}
                            else
                                $title = "RE: " . $title;
                        }
                        
                        ?>
                        <form method="POST" action="PM.php" name="sendPMForm">
                            <p>To (separated by a comma)</p>
                            <input type="text" id="toInput" maxlength="200" name="to" style="width: 100%;" value="<?php echo $pseudoTo; ?>"/>
                            <p><br />Title</p>
                            <input type="text" id="titleInput" maxlength="80" name="title" style="width: 100%;" value="<?php echo $title; ?>"/>
                            <p><br />Message</p>
                            <textarea name="message" id="messageInput" style="width: 100%; height: 200px; resize: vertical;"><?php echo $messageValue; ?></textarea>
                        </form>
                        <div id="sendButton">Send</div>
                        <?php
                    }else{
                        // Voir la liste de nos messages
                        $messages;
                        $nbNouveauxMessages = getCompteNewPMCount($_SESSION['id']);
                        if(isset($_GET['page']))
                            $messages = getPrivateMessages($_SESSION['id'], (($_GET['page'] - 1) * 25), ((($_GET['page'] - 1) * 25) + 25));
                        else
                            $messages = getPrivateMessages($_SESSION['id']);
                            
                        $nbPages = ($nbNouveauxMessages / 25) + 1;
                        
                        if((int)$nbPages > 1){
                            echo '<br />Pages : ';
                            for($i = 1; $i < $nbPages; $i++){
                                $current = 1;
                                if(isset($_GET['page']))
                                    $current = $_GET['page'];
                                if($current == $i)
                                    echo '<strong><a href="PM.php?page=' . $i . '">' . $i . '</a></strong> ';
                                else
                                    echo '<a href="PM.php?page=' . $i . '">' . $i . '</a> ';
                                    
                            }
                        }
                        if($nbNouveauxMessages == 0)
                            echo '<h3>No new messages</h3>';
                        else if($nbNouveauxMessages == 1)
                            echo '<h3>You have <strong style="color: green;">1</strong> new message</h3>';
                        else
                            echo '<h3>You have <strong style="color: green;">' . $nbNouveauxMessages . '</strong> new messages</h3>';
                        
                        ?>
                        <div id="composePMButton" style="margin-top: 10px; margin-bottom: 10px;">Compose</div>
                        <table class="tablePage center">
                            <tr>
                                <th style="width:150px;">Sender</th>
                                <th>Subject</th>
                            </tr>
                            <?php
                            $found = false;
                            for($i = 0; $i < count($messages); $i++){
                                $found = true;
                                $message = getPrivateMessage($messages[$i]);
                                $pseudo = "";
                                if($message['StringFrom'] == "")
                                    $pseudo = getPseudo($message['IDCompteFrom']);
                                else
                                    $pseudo = $message['StringFrom'];
                                if($message['Read'] == 0)
                                    echo '<tr style="background-color:#20252c;">';
                                else
                                    echo '<tr>';
                                    if($pseudo == "")
                                        echo '<td>Deleted Member</td>';
                                    else{
                                        if($message['StringFrom'] == '')
                                            echo '<td><a href="account.php?id=' . $message['IDCompteFrom'] . '">' . bbcodes_player('{player}' . $pseudo . '{/player}') . '</a></td>';
                                        else
                                            echo '<td>'. $pseudo . '</td>';
                                    }
                                    
                                    echo '<td class="left"><a href="PM.php?id=' . $messages[$i] . '">' . htmlspecialchars($message['Title']) . '</a><br /><span class="date">' . convertirDatePublicationEnString($message['Timestamp'], 1) . ' ago</span></td>';
                                echo '</tr>';
                            }
                            if(!$found)
                                echo '<tr><td colspan="3">No Private Message</td></tr>';
                            ?>
                        </table>
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
