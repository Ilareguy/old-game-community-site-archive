<?php

/*
*/

if(!defined('__ROOT__'))
    define('__ROOT__', dirname(dirname(dirname(__FILE__))) . '/');
    
include(__ROOT__."includes/bddConnect.php");
require_once(__ROOT__."includes/forums.php");
require_once(__ROOT__."includes/droits.php");

if(isset($_POST['thread']) && isset($_POST['message'])){

    $t = getForumThread($_POST['thread']);
    $message = forumThreadGetMessage($_POST['message']);
    
    if($message['idCompte'] == $_SESSION['id'] || droit($_SESSION['id'], 'moderatorForum_' . $t['idForum'])){
        // Si le compte a bien le droit d'éditer les messages
        // de cette section
        
        if(isset($_POST['saveMessage']) && isset($_POST['newMessage'])){
            forumEditMessage($_POST['message'], $_POST['newMessage'], $_SESSION['id']);
        }
        
        if($message !== false){
            ?>
            <form id="moderatorsEditMessageForm" name="moderatorsEditMessageForm" action="" method="post">
                <table class="tablePage">
                    <tr>
                        <th>Message</th>
                    </tr>
                    <tr>
                        <td>
                            <textarea id="moderatorsEditMessageForm_message" style="width: 93%; height: 200px; margin: 10px; font-weight: bold; font-size: 80%;"><?php echo $message['message']; ?></textarea>
                            <input type="hidden" id="moderatorsEditMessageForm_idThread" value="<?php echo $_POST['thread']; ?>"/>
                            <input type="hidden" name="moderatorsEditMessageForm_idMessage" id="moderatorsEditMessageForm_idMessage" value="<?php echo htmlentities($_POST['message']); ?>"/>
                        </td>
                    </tr>
                </table>
            </form>
            <?php
        }
    }

}
?>