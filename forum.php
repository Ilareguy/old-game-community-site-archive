<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');
    
include(__ROOT__."includes/bddConnect.php");
include(__ROOT__."includes/verifyLogin.php");
require_once(__ROOT__."includes/forums.php");
require_once(__ROOT__."includes/dates.php");
require_once(__ROOT__."includes/comptes.php");
require_once(__ROOT__."includes/droits.php");
require_once(__ROOT__."includes/bbcodes.php");
require_once(__ROOT__."includes/sys.php");

if(!$_SESSION['connecte']){
    header('Location: login.php?err=1');
}

if(IsMaintenance() && !droit($_SESSION['id'], 'IgnoreMaintenance'))
    header('Location: index.php?maintenance');

$currentPage = "forum";

$inSpecificThread = false;
$createThreadError = false;
$inSpecificSection = false;

$successbox = false;
$errorbox = false;
$warningbox = false;
$errorString = "";
$successString = "";
$warningString = "";
$accountBanned = compteIsBanned($_SESSION['id']);
if($accountBanned){
    $errorString .= "Your account is muted/banned and cannot create any thread or reply to any thread.";
    $errorbox = true;
}

if(isset($_GET['s'])){
    // On veut voir une section spécifique
    $forum = getForum($_GET['s']);
    if($forum !== false && droitVoirForum($_SESSION['id'], $_GET['s'])){
        $inSpecificSection = true;
    }
}

if(isset($_GET['t'])){
    // On veut voir un thread spécifique
    $t = getForumThread($_GET['t']);
    if(forumThreadExists($t['id']) && droitVoirForum($_SESSION['id'], $t['idForum']))
        $inSpecificThread = true;
}

if(isset($_POST['actionCreateThread']) && isset($_POST['idForum']) && isset($_POST['nom']) && isset($_POST['message']) && $_SESSION['connecte'] && !$accountBanned){
    // On veut créer un thread
    if($_POST['idForum'] != -1 && forumExists($_POST['idForum']) && droitVoirForum($_SESSION['id'], $_POST['idForum']) && $_POST['nom'] != "" && $_POST['message'] != ""){
        // Pour nos amis qui voudraient déjouer la sécurité ;)
        
        forumCreateThread($_SESSION['id'], $_POST['nom'], $_POST['message'], $_POST['idForum']);
        
        // On indique au navigateur de charger la page "forum.php" (la page actuelle) pour que
        // si le visiteur appuie sur Actualiser, le formulaire ne sera pas renvoyé
        header('Location: forum.php');
    }
}

if(isset($_POST['actionPostAnswer']) && isset($_POST['message']) && isset($_POST['thread']) && $_SESSION['connecte'] && !$accountBanned){
    // On veut poster une réponse dans un thread
    $t = getForumThread($_POST['thread']);
    if(droitVoirForum($_SESSION['id'], $t['idForum']) && forumThreadExists($_POST['thread']) && $_POST['message'] != ""){
        // Tout est OK
        forumThreadPostAnswer($_POST['message'], $_POST['thread'], $_SESSION['id']);
        
        // On indique au navigateur de charger la page "forum.php" (la page actuelle) pour que
        // si le visiteur appuie sur Actualiser, le formulaire ne sera pas renvoyé
        header('Location: forum.php?t=' . $_POST['thread']);
    }
    // else, pas le droit
}

if(isset($_GET['r_all']) && forumThreadExists($_GET['r_all'])){
    // On veut supprimer un thread au complet
    $t = getForumThread($_GET['r_all']);
    if(droit($_SESSION['id'], 'moderatorForum_' . $t['idForum']))
        forumRemoveThread($_GET['r_all']);
}

if(isset($_GET['r'])){
    // On veut supprimer un message d'un thread
    $m = forumThreadGetMessage($_GET['r']);
    $t = getForumThread($m['idThread']);
    if(droit($_SESSION['id'], 'moderatorForum_' . $t['idForum']))
        forumRemoveMessage($_GET['r']);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Diamond Craft - Forum</title>
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
    <script type="text/javascript" src="js/forum.js"></script>
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
                    <?php
                    if(!$_SESSION['connecte']){
                        ?>
                        <h2 class="title">Forum</h2>
                        <p class="byline">Please <a href="login.php">login</a> to see the forum</p>
                        <?php
                    }else if(!$inSpecificSection && !$inSpecificThread){
                        // Liste des sections
                        ?>
                        <h2 class="title">Forum</h2>
                        <h3>Listed here are all the sections you can access</h3>
                        
                        <?php
                        
                        $forums = getForums();
                        $count = count($forums);
                        for($i = 0; $i < $count; $i++){
                            
                            $forum = getForum($forums[$i]['id']);
                            $threads = getForumThreads($forum['id']);
                            if(droitVoirForum($_SESSION['id'], $forums[$i]['id'])){
                                
                                ?>
                                <div class="forumSection" onclick="javascript: window.location = 'forum.php?s=<?php echo $forums[$i]['id']; ?>';">
                                    <h3 style="margin-left: 6px;"><?php echo $forum['nom']; ?></h3>
                                    <?php
                                    $lastMessage = forumGetLastMessage($forums[$i]['id']);
                                    if($lastMessage === false){
                                        echo 'Be the first<br />to post your answer!';
                                    }else{
                                        echo 'Last post: ' . convertirDatePublicationEnString($lastMessage['time'], 1) . ' ago<br />by ' . forumGetDisplayNameFromMessage($lastMessage);
                                    }
                                    ?>
                                </div>
                                <?php
                                
                            }
                            
                        }
                        
                    }else if(!$inSpecificThread && $inSpecificSection){
                        ?>
                        <h2 class="title">Forum</h2>
                        <h3><?php echo $forum['nom']; ?></h3>
                        
                        <div style="display:none;" id="createThreadDialog">
                            <form id="createThreadForm" action="forum.php" method="post">
                                <input type="hidden" value="1" name="actionCreateThread" />
                                <input type="hidden" value="-1" id="idForum" name="idForum" />
                                <table class="tablePage">
                                    <tr>
                                        <th colspan="2">Thread specifications</th>
                                    </tr>
                                    <tr>
                                        <td class="leftCell">Thread name:</td>
                                        <td class="rightCell">
                                            <input type="text" maxlength="70" name="nom" class="inputText"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="leftCell" style="vertical-align: top;">Your message:</td>
                                        <td class="rightCell">
                                            <textarea class="inputText" name="message" style="height: 320px; font-weight: bold; font-size: 70%; text-indent: 0px;"></textarea>
                                        </td>
                                    </tr>
                                </table>
                                <div id="createThreadButton" style="width: 120px; margin: 12px;">Create!</div>
                            </form>
                        </div>
                        <?php
                        if(droitVoirForum($_SESSION['id'], $forum['id'])){
                            // Si le compte a le droit de voir cette section...
                            $threads = getForumThreads($forum['id']);
                            ?>
                            <table class="tablePage forumTable">
                                <tr>
                                    <th style="background-color: #<?php echo $forum['couleur']; ?>; width: 18px;"></th>
                                    <th style="width: 230px;">Threads</th>
                                    <th style="width: 40px;">Posts</th>
                                    <th>Last post</th>
                                </tr>
                                <?php
                                for($j = 0; $j < count($threads); $j++){
                                    $thread = getForumThread($threads[$j]['id']);
                                    if($thread !== false){
                                        // S'il y a au moins un thread dans la section
                                        $thread['creator'] = forumGetThreadCreatorPseudo($thread['id']);
                                        $lastAnswer = forumThreadGetLastAnswer($thread['id']);
                                        ?>
                                        <tr <?php echo (($thread['couleur'] != "#" || $thread['couleur'] != "") ? 'style="background-color:#' . $thread['couleur'] . '"' : ''); ?>>
                                            <td style="vertical-align: middle;">
                                                <?php
                                                if(droit($_SESSION['id'], 'moderatorForum_' . $forum['id'])){
                                                    echo '<img alt="Manage" src="images/icons/edit.gif" style="cursor: pointer;" onclick="JavaScript: loadModeratorPopup_'.$forum['id'].'('.$thread['id'].');"/>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                // L'icône
                                                if($thread['icon'] != "" && $thread['locked'] != 1)
                                                    echo '<img alt="" class="icon" src="images/icons/forum/'.$thread['icon'].'"/>';
                                                if($thread['locked'] == 1)
                                                    echo '<img alt="" title="This thread is locked" class="icon" src="images/icons/forum/lock.png"/>';
                                                
                                                // Le label
                                                if($thread['label'] != "")
                                                    echo '<span class="threadLabel" style="color: #'.$thread['labelColor'].';">[ '.$thread['label'].' ]</span>';
                                                ?>
                                                <a href="forum.php?t=<?php echo $thread['id']; ?>"><?php echo $thread['nom']; ?></a><br />
                                                <span class="threadBy">By <?php echo$thread['creator']; ?></span>
                                            </td>
                                            <td><?php echo threadGetNbAnswers($thread['id']) - 1; ?></td>
                                            <td>
                                                <?php
                                                echo convertirDatePublicationEnString($lastAnswer['time'], 2).' ago<br />';
                                                echo '<span class="threadBy">By ' . forumGetDisplayNameFromMessage($lastAnswer) . '</span>';
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }else{
                                        ?>
                                        <tr>
                                            <td colspan="4" style="text-align: center;">There is no thread in this section</td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </table>
                            <?php
                            if(!$accountBanned)
                                echo '<input type="button" value="Create a thread in this section" style="margin: 8px;" onclick="JavaScript: editIdForumForm(' . $forum['id'] . ');"/>';
                            ?>
                            
                            <div style="margin-bottom: 40px;"></div>
                            
                            <?php
                            // Le popup qui servira aux modérateurs
                            if(droit($_SESSION['id'], 'moderatorForum_' . $forum['id'])){
                                ?>
                                <div id="moderatorPopup_<?php echo $forum['id']; ?>" style="display: none;">
                                    <div id="moderatorPopup_<?php echo $forum['id']; ?>_content">
                                    </div>
                                    <table style="margin-bottom: 10px;">
                                        <tr>
                                            <td>
                                                <div id="moderatorPopup_<?php echo $forum['id']; ?>_submitButton">Save changes</div>
                                            </td>
                                            <td>
                                                <div id="moderatorPopup_<?php echo $forum['id']; ?>_cancelButton">Cancel</div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <script type="text/javascript">
                                    function loadModeratorPopup_<?php echo $forum['id']; ?>(idThread){
                                        $("#moderatorPopup_<?php echo $forum['id']; ?>_content").html('<p style="text-align:center;"><img alt="" src="images/icons/loading.gif"/><br />Loading...</p>');
                                        $("#moderatorPopup_<?php echo $forum['id']; ?>").dialog("open");
                                        $.ajax({
                                            cache: false,
                                            url: 'subPages/forum/moderatorsForumPopup.php?t='+idThread,
                                            success: function(data){
                                                $("#moderatorPopup_<?php echo $forum['id']; ?>_content").html(data);
                                                $("#moderatorPopup_<?php echo $forum['id']; ?>_content").find('.colorPicker').each(function(){
                                                    var $this = $(this);
                                                    $this.ColorPicker({
                                                        onChange: function(hsb, hex, rgb) {
                                                            $this.attr('value', '#'+hex);
                                                        }
                                                    });
                                                });
                                            }
                                        });
                                    }
                                    
                                    $("#moderatorPopup_<?php echo $forum['id']; ?>").dialog({
                                        width: 420,
                                        height: 472,
                                        title: "Forum section: <?php echo $forum['nom']; ?>",
                                        autoOpen: false,
                                        modal: true,
                                        resizable: false
                                    });
                                    $("#moderatorPopup_<?php echo $forum['id']; ?>_cancelButton").button();
                                    $("#moderatorPopup_<?php echo $forum['id']; ?>_cancelButton").click(function (){
                                        $("#moderatorPopup_<?php echo $forum['id']; ?>").dialog("close");
                                    });
                                    
                                    $("#moderatorPopup_<?php echo $forum['id']; ?>_submitButton").button();
                                    $("#moderatorPopup_<?php echo $forum['id']; ?>_submitButton").click(function (){
                                        // Envoie du formulaire avec Ajax
                                        var locked = 0;
                                        
                                        if(document.getElementById("moderatorPopupForm_locked").checked)
                                            locked = 1;
                                        var iconSelectedIndex = 0;
                                        for(var i=0; i<document.forms["moderatorPopupForm"].icon.length; i++){
                                            if(document.forms["moderatorPopupForm"].icon[i].checked){
                                                iconSelectedIndex = i;
                                                i = document.forms["moderatorPopupForm"].icon.length;
                                            }
                                        }
                                        $.ajax({
                                            cache: false,
                                            url: 'subPages/forum/moderatorsForumPopup.php',
                                            data: {
                                                label : document.getElementById("moderatorPopupForm_label").value, 
                                                color : document.getElementById("moderatorPopupForm_color").value,
                                                labelColor : document.getElementById("moderatorPopupForm_labelColor").value,
                                                thread : document.getElementById("moderatorPopupForm_thread").value,
                                                icon : document.forms["moderatorPopupForm"].icon[iconSelectedIndex].value,
                                                locked : locked
                                            },
                                            type: "POST",
                                            success: function(data){
                                                window.location.reload();
                                            }
                                        });
                                    });
                                </script>
                                <?php
                            }
                        }
                    }else if($inSpecificThread){
                        // Dans un thread spécifique
                        $thread = getForumThread($_GET['t']);
                        if($thread !== false){
                            if(isset($_GET['page']))
                                $messages = forumThreadGetMessages($_GET['t'], 25, (($_GET['page'] - 1) * 25));
                            else
                                $messages = forumThreadGetMessages($_GET['t'], 25);
                            
                            $thread['creator']['pseudo'] = forumGetThreadCreatorPseudo($_GET['t']);
                            $nbThreadsAnswers = threadGetNbAnswers($_GET['t']);
                            $nbPages = 1;
                            if($nbThreadsAnswers > 25)
                                $nbPages = ($nbThreadsAnswers / 25) + 1;
                            
                            // Le dialog pour éditer les messages
                            ?>
                            <div id="messageEditPopup">
                                <div id="messageEditPopup_content">
                                </div>
                                <div style="margin-top: 10px;" id="messageEditPopup_saveButton">Save changes</div>
                            </div>
                            
                            <h2 class="title">Forum</h2>
                            <input type="button" value="< back" onclick="javascript:window.location='forum.php?s=<?php echo $thread['idForum']; ?>';"/>
                            <p class="byline"><?php echo $thread['nom']; ?></p>
                            <h3>By <?php echo $thread['creator']['pseudo']; ?></h3>
                            <?php
                            if((int)$nbPages > 1){
                                echo '<br />Pages : ';
                                for($i = 1; $i < $nbPages; $i++){
                                    $current = 1;
                                    if(isset($_GET['page']))
                                        $current = $_GET['page'];
                                    if($current == $i)
                                        echo '<strong><a href="forum.php?t=' . $_GET['t'] . '&page=' . $i . '">' . $i . '</a></strong> ';
                                    else
                                        echo '<a href="forum.php?t=' . $_GET['t'] . '&page=' . $i . '">' . $i . '</a> ';
                                        
                                }
                            }
                            ?>
                            <table class="tablePage forumThreadMessageTable">
                                <tr>
                                    <th>Messages</th>
                                </tr>
                                <?php
                                for($i = 0; $i < count($messages); $i++){
                                    $messages[$i]['editions'] = forumGetMessagesEdits($messages[$i]['id']);
                                    ?>
                                    <tr>
                                        <td>
                                            <?php
                                            echo '<h3 style="font-size:130%;margin-left:3px;">' . forumGetDisplayNameFromMessage($messages[$i]) . ' says:</h3><div class="forumMessage"><p>'.smileys(bbcodes($messages[$i]['message'], true, false)).'</p></div>';
                                            echo '<br />';
                                            if(droit($_SESSION['id'], 'moderatorForum_' . $thread['idForum'])){
                                                /*
                                                * Ici se trouvent les liens pour les modérateurs de cette section:
                                                * Éditer un message;
                                                * Supprimer un message;
                                                * Supprimer un thread complet;
                                                */
                                                
                                                // Éditer
                                                echo '[<a href="#" onclick="JavaScript: openEditMessageDialog('.$messages[$i]['id'].', '.$thread['id'].'); return false;">Edit</a>] ';
                                                
                                                $currentPageNumber = 1;
                                                if(isset($_GET['page']))
                                                    $currentPageNumber = $_GET['page'];
                                                if($i > 0 || $currentPageNumber > 1){
                                                    // Supprimer message
                                                    echo '[<a href="#" onclick="JavaScript: removeMessage('.$messages[$i]['id'].', '.$thread['id'].'); return false;">Remove</a>]';
                                                }
                                                if($i == 0 && $currentPageNumber == 1){ // Si c'est le premier message
                                                    // Supprimer un thread
                                                    echo ' [<a href="#" onclick="JavaScript: removeEntireThread('.$thread['id'].'); return false;">Remove entire thread</a>]';
                                                }
                                            }else{
                                                // Pas de droit de modération. Mais on peut modifier nos propres messages
                                                if($messages[$i]['idCompte'] == $_SESSION['id'])
                                                    echo '[<a href="#" onclick="JavaScript: openEditMessageDialog('.$messages[$i]['id'].', '.$thread['idForum'].'); return false;">Edit</a>] ';
                                            }
                                                
                                            echo '<div class="threadBy">Posted '.convertirDatePublicationEnString($messages[$i]['time'], 3).' ago, 
                                                by ' . forumGetDisplayNameFromMessage($messages[$i]) . '</div>';
                                            if($messages[$i]['editions'] !== false){
                                                for($j = 0; $j<count($messages[$i]['editions']); $j++){
                                                    echo '<div class="threadBy">Edited '.convertirDatePublicationEnString($messages[$i]['editions'][$j]['timestamp'], 3).' ago, 
                                                        by ' . forumGetDisplayNameFromMessage($messages[$i]['editions'][$j]) . '</div>';
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table>
                            
                            <?php
                            if($thread['locked'] != 1 && !$accountBanned){
                                ?>
                                <div style="margin: 4px; margin-top: 30px;" id="postAnswerSectionButton">Reply</div>
                                <div id="postAnswerSection" style="display: none;">
                                    <form id="postAnswerForm" action="forum.php" method="post">
                                        <input type="hidden" id="postAnswerSectionVisible" value="0"/>
                                        <input type="hidden" name="thread" value="<?php echo $_GET['t']; ?>"/>
                                        <input type="hidden" name="actionPostAnswer" value="1"/>
                                        <table class="tablePage forumThreadMessageTable">
                                            <tr>
                                                <th>Your message</th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <textarea name="message" style="width: 95%; height: 200px; margin: 0 auto; margin: 10px;"></textarea>
                                                </td>
                                            </tr>
                                        </table>
                                        <table>
                                            <tr>
                                                <td><div id="postAnswerButton" style="margin: 4px;">Submit</div></td>
                                                <td><div id="BBcodesInfosButton" style="margin: 4px;">BBcodes</div></td>
                                                <td><div id="smileysInfosButton" style="margin: 4px;">Smileys</div></td>
                                            </tr>
                                        </table>
                                    </form>
                                </div>
                                <?php
                            }
                            if((int)$nbPages > 1){
                                echo '<br />Pages : ';
                                for($i = 1; $i < $nbPages; $i++){
                                    $current = 1;
                                    if(isset($_GET['page']))
                                        $current = $_GET['page'];
                                    if($current == $i)
                                        echo '<strong><a href="forum.php?t=' . $_GET['t'] . '&page=' . $i . '">' . $i . '</a></strong> ';
                                    else
                                        echo '<a href="forum.php?t=' . $_GET['t'] . '&page=' . $i . '">' . $i . '</a> ';
                                        
                                }
                            }
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
