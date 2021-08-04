<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(dirname(__FILE__)) . '/');
require_once(__ROOT__.'includes/GameServer.php');

/*$Info = GameServerInfo();

if($Info === false){

    ?>
    <div style="text-align: center;">
        <p><strong>The server is either offline or there was a problem while contacting it.</strong></p>
    </div>
    <?php
    
}else{

    ?>
    <div style="text-align: center;">
        <p><strong><?php echo $Info['motd']; ?></strong></p>
        <p>
            <?php echo $Info['Players']; ?>/<?php echo $Info['MaxPlayers']; ?> players online
        </p>
    </div>
    <?php

}*/
?>

<div style="text-align: center;">
    <p><strong>Diamond Craft is offline.</strong></p>
</div>