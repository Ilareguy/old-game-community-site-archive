<div id="sidebar">
    <div id="box3" class="box-style2">
		<?php /* 
        <h2 class="title">Notifications</h2>
		<div class="items_menu">
			<?php
			/* Affichage des notifications de plusieurs types *//*
			$HasNotif = false;
			
			// Nouveaux messages
			$PMs = GetUnreadPMsInfo($_SESSION['id']);
			$Count = count($PMs);
			for($i = 0; $i < $Count; $i++){
				?>
				<a href="#"><div id="notif-id-newpm-<?php echo $PMs[$i]['ID']; ?>" data-overbox-width="200" data-overbox-height="175" data-overbox-anchor="l" data-overbox-group="Notification" data-overbox-target="notif-newpm-<?php echo $PMs[$i]['ID']; ?>" class="item"><img class="icon" alt="" src="images/icons/icon_mail.png"/> <p>New PM from <?php echo getPseudo($PMs[$i]['IDCompteFrom']) ?></p></div></a>
				<div id="notif-newpm-<?php echo $PMs[$i]['ID']; ?>">
					<div class="items_menu">
						<h4 style="text-align:center;"><?php pecho($PMs[$i]['Title'], 30); ?></h4>
						<input type="button" onclick="window.location='PM.php?id=<?php echo $PMs[$i]['ID']; ?>'" value="Read"/>
						<input type="button" onclick="ExecuteNotificationAction('action=<?php echo $NOTIFICATION_ACTION_MARK_PM_AS_READ; ?>&id=<?php echo $PMs[$i]['ID']; ?>');RemoveNotification($('#notif-id-newpm-<?php echo $PMs[$i]['ID']; ?>'));" value="Mark as 'Read'"/>
						<input type="button" onclick="if(confirm('Are you sure you want to delete this Private Message?')){ExecuteNotificationAction('action=<?php echo $NOTIFICATION_ACTION_DELETE_PM; ?>&id=<?php echo $PMs[$i]['ID']; ?>');RemoveNotification($('#notif-id-newpm-<?php echo $PMs[$i]['ID']; ?>'));}" value="Delete"/>
					</div>
				</div>
				<?php
				$HasNotif = true;
			}
			?>
		</div>
        <?php
        if(!$HasNotif){
            echo '<h4>&nbsp;You have no notification</h4>';
        } */
        ?>
        <h2 class="title">Game Server Info</h2>
        <div class="AjaxGameServerInfo"><p style="text-align: center;">Loading...</p></div>
    </div>
    <div id="box4" class="box-style2">
        <h2 class="title">Quick Links</h2>
        <ul class="style1">
            <li><a href="forum.php?t=6">Report a bug</a></li>
            <li><a href="forum.php?t=7">Tell us what you think about the new website!</a></li>
            <li><a href="forum.php?t=8">Have an idea for the website?</a></li>
            <li><a href="forum.php?t=14">Need to be whitelisted?</a></li>
        </ul>
    </div>
</div>