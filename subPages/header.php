<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');

require_once(__ROOT__."includes/droits.php");
require_once(__ROOT__."includes/groupes.php");
require_once(__ROOT__.'includes/PM.php');
require_once(__ROOT__.'includes/news.php');
require_once(__ROOT__.'includes/comptes.php');
require_once(__ROOT__.'includes/sys.php');
require_once(__ROOT__.'includes/social.php');
if(!isset($currentPage))
    $currentPage = "";

$IsMaintenance = IsMaintenance();

if($_SESSION['connecte']){
    ?>
    <div id="fb-root"></div>
    <script type="text/javascript">
        window.fbAsyncInit = function() {
            FB.init({
                appId      : <?php echo $API_KEY; ?>, // App ID from the App Dashboard
                channelUrl : 'www.diamondcraft.org/fbchannel.php', // Channel File for x-domain communication
                status     : false, // check the login status upon init?
                cookie     : true, // set sessions cookies to allow your server to access the session?
                xfbml      : true  // parse XFBML tags on this page?
            });
            
            <?php
            $AccessToken = CompteLinkedToFacebook($_SESSION['id']);
            if($AccessToken === false){
                // Pas linké avec Facebook
                ?>
                $('[data-facebook-login]').click(function(){
                    FB.login(function(response) {
                    if (response.authResponse) {
                        // Succès
                        $.ajax({
                            url: 'ajax/FacebookLoginPing.php?accesstoken=' + FB.getAuthResponse()['accessToken'],
                            success: function(data){
                                // Puis on actualise la page
                                window.location = window.location;
                            },
                            error: function(){
                            }
                        });
                    }
                    });
                });
                <?php
            }else{
                ?>FB_GO();<?php
            }
            ?>
        };
        
        (function(d, debug){
            var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement('script'); js.id = id; js.async = true;
            js.src = "//connect.facebook.net/en_US/all" + (debug ? "/debug" : "") + ".js";
            ref.parentNode.insertBefore(js, ref);
        }(document, false));
    </script>
    
    <div style="display: none;" id="specialPagesBox">
        <h5 style="letter-spacing: -1px; font-size: 80%; margin: 3px; margin-bottom: 30px;">
            Listed here are all features available to registered members.
        </h5>
        <table style="width:100%;">
            <?php
            if(droit($_SESSION['id'], 'manipulateAccounts')){
                ?>
                <tr>
                    <td>
                        <div href="administrative_accounts.php"><p><img alt="" src="images/icons/fugue/user--pencil.png" /></p></div>
                    </td>
                    <td>Manage accounts</td>
                </tr>
                <?php
            }
            
            if(droit($_SESSION['id'], 'manipulateForums')){
                ?>
                <tr>
                    <td>
                        <div href="administrative_forums.php"><p><img alt="" src="images/icons/fugue/book-open-text.png" /></p></div>
                    </td>
                    <td>Manage forums</td>
                </tr>
                <?php
            }
            
            if(droit($_SESSION['id'], 'manipulateGroups')){
                ?>
                <tr>
                    <td>
                        <div href="administrative_groupes.php"><p><img alt="" src="images/icons/fugue/users.png" /></p></div>
                    </td>
                    <td>Manage groups</td>
                </tr>
                <?php
            }
            
            if(droit($_SESSION['id'], 'manipulateNews')){
                ?>
                <tr>
                    <td>
                        <div href="administrative_news.php"><p><img alt="" src="images/icons/fugue/newspaper--arrow.png" /></p></div>
                    </td>
                    <td>Edit News Posts</td>
                </tr>
                <tr>
                    <td>
                        <div href="administrative_news.php?add=1"><p><img alt="" src="images/icons/fugue/newspaper--plus.png" /></p></div>
                    </td>
                    <td>Create News Post</td>
                </tr>
                <?php
            }
            
            if(droit($_SESSION['id'], 'gameServerCommand')){
                ?>
                <tr>
                    <td>
                        <div href="administrative_serverControl.php"><p><img alt="" src="images/icons/fugue/application-terminal.png" /></p></div>
                    </td>
                    <td>Game Server Control</td>
                </tr>
                <?php
            }
            
            // Boutons communs
            ?>
            <tr>
                <td>
                    <div href="team.php"><p><img alt="" src="images/icons/fugue/user-silhouette.png" /></p></div>
                </td>
                <td>About us</td>
            </tr>
            <tr>
                <td>
                    <div href="VIP.php"><p><img alt="" src="images/icons/fugue/user-business.png" /></p></div>
                </td>
                <td>VIP</td>
            </tr>
            <tr>
                <td>
                    <div target="_blank" href="http://www.rexxie.ca/"><p><img alt="" src="images/icons/fugue/dummy-happy.png" /></p></div>
                </td>
                <td>Jayrex's Blog</td>
            </tr>
        </table>
        <?php
        
        /*if(droit($_SESSION['id'], 'manipulatePolls')){
            ?>
            <p id="specialPages_Pollers_viewCurrentPolls">View current polls</p>
            <p id="specialPages_Pollers_createPoll">Create a poll</p>
            <?php
        }*/
        ?>
    </div>
    <?php
}
?>

<div id="header" class="container">
	<div id="logo">
		<h1><a href="index.php"><img style="background-image: url(images/headerIMG_teal.png);" alt="Diamond Craft" src="images/banner.png"/></a></h1>
		<p style="text-align: center;">One block at a time...</p>
	</div>
	<div id="menu">
		<ul>
            <?php
            if($currentPage == "homepage")
                // Page Homepage
                echo '<li id="header_homepage" class="active"><a href="index.php" title=""><span>Homepage</span></a></li>';
            else
                echo '<li id="header_homepage"><a href="index.php" title=""><span>Homepage</span></a></li>';
            
            if(!$_SESSION['connecte'] && !$IsMaintenance){
                // Page Login
                if($currentPage == "login")
                    echo '<li id="header_login" class="active"><a href="login.php" title=""><span>Login</span></a></li>';
                else
                    echo '<li id="header_login"><a href="login.php" title=""><span>Login</span></a></li>';
                
                // Page Inscription
                if($currentPage == "subscribe")
                    echo '<li id="header_subscribe" class="active"><a href="subscribe.php" title=""><span>Register</span></a></li>';
                else
                    echo '<li id="header_subscribe"><a href="subscribe.php" accesskey="2" title=""><span>Register</span></a></li>';
            }else if($_SESSION['connecte'] && $IsMaintenance && !droit($_SESSION['id'], 'IgnoreMaintenance')){
                echo '<li id="header_logout"><a href="login.php?logout=1" title=""><span>Logout</span></a></li>';
            }else if(!$IsMaintenance){
                // Page My account
                if($currentPage == "account")
                    echo '<li id="header_account" class="active"><a href="account.php" title=""><span>My Account</span></a></li>';
                else
                    echo '<li id="header_account"><a href="account.php" accesskey="2" title=""><span>My Account</span></a></li>';
                    
                // Page Messages Privés
                if($currentPage == "PM")
                    echo '<li id="header_PM" class="active"><a href="PM.php" title=""><span>Inbox</span></a></li>';
                else{
                    if(getCompteNewPMCount($_SESSION['id']) > 0)
                        echo '<li class="mustBlink" id="header_PM"><a href="PM.php" title=""><span>Inbox</span></a></li>';
                    else
                        echo '<li id="header_PM"><a href="PM.php" title=""><span>Inbox</span></a></li>';
                }
                
                
                // Page Forum
                if($currentPage == "forum")
                    echo '<li id="header_forum" class="active"><a href="forum.php" " title=""><span>Forum</span></a></li>';
                else
                    echo '<li id="header_forum"><a href="forum.php" title=""><span>Forum</span></a></li>';
                    
                // Page News
                $IDLastNewSeen = getIdLastNewSeen($_SESSION['id']);
                if($currentPage == "news")
                    echo '<li id="header_news" class="active"><a href="news.php" " title=""><span>News</span></a></li>';
                else{
                    if($IDLastNewSeen < getIdLastNews())
                        echo '<li class="mustBlink" id="header_news"><a href="news.php" title=""><span>News</span></a></li>';
                    else
                        echo '<li id="header_news"><a href="news.php" title=""><span>News</span></a></li>';
                }
                
                // Page VIP
                if($currentPage == "VIP")
                    echo '<li id="header_VIP" class="active"><a href="VIP.php"><span>VIP</span></a></li>';
                else
                    echo '<li id="header_VIP"><a href="VIP.php"><span>VIP</span></a></li>';
                }
            
            echo '<li id="header_store"><a href="http://www.zazzle.ca/diamondcraft" target="_blank"><span>Store</span></a></li>';
            
            if($_SESSION['connecte']){ ?><li id="header_memberfeatures"><a href="#" id="specialPagesBox_open"><span>Member Features</span></a></li> <?php } ?>
		</ul>
	</div>
</div>