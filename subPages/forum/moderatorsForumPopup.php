<?php

/*
* Cette page est ce que contiendra le popup sur la page Forums 
* lorsqu'aucun thread n'est sélectionné.
* Elle servira aux modérateurs pour supprimer, changer la couleur, le label, 
* bloquer ou débloquer les threads d'une section spécifique.
*
* Certains éléments doivent avoir un ID spécifique, ce qui permettra
* au JavaScript de fonctionner correctement:
* input "color": moderatorPopupForm_color ;
* input "label": moderatorPopupForm_label ;
* input "locked" (de type checkbox): moderatorPopupForm_locked ;
* input "thread" (de type hidden): moderatorPopupForm_thread ;
*/

if(!defined('__ROOT__'))
    define('__ROOT__', dirname(dirname(dirname(__FILE__))) . '/');

include(__ROOT__."includes/bddConnect.php");
require_once(__ROOT__."includes/forums.php");
require_once(__ROOT__."includes/droits.php");

if(isset($_POST['thread']) && isset($_POST['color']) && isset($_POST['label']) && isset($_POST['locked']) && isset($_POST['labelColor']) && isset($_POST['icon'])){
    // On veut enregistrer les informations sur le thread
    $t = getForumThread($_POST['thread']);
    if(droit($_SESSION['id'], 'moderatorForum_' . $t['idForum'])){
        // Si on a tous les droits nécessaires
        forumThreadEditColor($_POST['thread'], $_POST['color']);
        forumThreadEditLabelColor($_POST['thread'], $_POST['labelColor']);
        forumThreadEditLabel($_POST['thread'], $_POST['label']);
        if($_POST['locked'] == 1)
            forumThreadLock($_POST['thread']);
        else
            forumThreadUnlock($_POST['thread']);
        
        if($_POST['icon'] != "doNotChange"){
            if($_POST['icon'] == "none")
                forumThreadChangeIcon($_POST['thread'], "");
            else
                forumThreadChangeIcon($_POST['thread'], $_POST['icon']);
        }
    }
}

if(isset($_GET['t'])){
    if(forumThreadExists($_GET['t'])){
        $thread = getForumThread($_GET['t']);
        ?>
        <form name="moderatorPopupForm" id="moderatorPopupForm" action="forum.php" method="post">
            <input type="hidden" name="thread" id="moderatorPopupForm_thread" value="<?php echo $thread['id']; ?>"/>
            <table class="tablePage">
                <tr>
                    <th>Options</th>
                    <th>Values</th>
                </tr>
                <tr>
                    <td class="leftCell">Special color:</td>
                    <td class="rightCell">
                        <input type="text" class="colorPicker inputText" name="color" value="<?php echo $thread['couleur']; ?>" maxlength="9" id="moderatorPopupForm_color"/>
                    </td>
                </tr>
                <tr>
                    <td class="leftCell">Label:</td>
                    <td class="rightCell">
                        <input type="text" name="label" value="<?php echo $thread['label']; ?>" class="inputText" maxlength="30" id="moderatorPopupForm_label"/>
                    </td>
                </tr>
                <tr>
                    <td class="leftCell">Label color:</td>
                    <td class="rightCell">
                        <?php
                        if($thread['labelColor'] == "deeefa")
                            $thread['labelColor'] = "default";
                        ?>
                        <input id="moderatorPopupForm_labelColor" value="<?php echo $thread['labelColor']; ?>" class="colorPicker inputText" type="text" maxlength="9"/>
                    </td>
                </tr>
                <tr>
                    <td class="leftCell">Icon:</td>
                    <td>
                        <div style="height: 200px; overflow: scroll;">
                            <input type="radio" name="icon" value="doNotChange" id="doNotChange" checked="checked"/><label for="doNotChange">Do not change<br /></label>
                            <input type="radio" name="icon" value="none" id="none"/><label for="none">None<br /></label>
                            <?php
                            $dirname = __ROOT__.'images/icons/forum/';
                            $dir = opendir($dirname);

                            while($fichier = readdir($dir)){
                                if($fichier != '.' && $fichier != '..' && !is_dir($dirname.$fichier) && (substr($fichier, -3) == "gif" || substr($fichier, -3) == "jpg" || substr($fichier, -3) == "png" 
                                || substr($fichier, -4) == "jpeg" || substr($fichier, -3) == "PNG" || substr($fichier, -3) == "GIF" 
                                || substr($fichier, -3) == "JPG")){
                                    echo '<input type="radio" name="icon" value="'.$fichier.'" id="'.$fichier.'" /><label for="'.$fichier.'"><img class="icon" src="images/icons/forum/'.$fichier.'" alt=""/><br /></label>';
                                }
                            }

                            closedir($dir);
                            ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="leftCell">Locked:</td>
                    <td class="rightCell">
                        <input type="checkbox" id="moderatorPopupForm_locked" name="locked" <?php if($thread['locked'] == 1){ echo 'checked="checked"'; } ?>/>
                    </td>
                </tr>
            </table>
        </form>
        <?php
    }
}
?>
