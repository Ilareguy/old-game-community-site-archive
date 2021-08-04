<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(__FILE__) . '/');
    
include(__ROOT__."includes/bddConnect.php");

$currentPage = "whatsup";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Diamond Craft - What's up</title>
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
</head>
<body>
<?php
include("subPages/header.php");
?>
<div id="page" class="container">
	<div id="page-bgtop">
		<div id="page-bgbtm">
			<div id="wide-content" class="box-style1">
				<h2 class="title">What's up with the website?</h2>
				<p class="byline">By Totoila</p>
				<div class="image-style4 image-style4a"><img src="images/pics10.jpg" width="190" height="145" alt="" /><span></span></div>
				<p>
                    I have been taking a lot of my free time of the past 6 or 7 months to build a website that is easy-to-use, useful and hopefully fun.<br />
                    <br />
                    Since this website was not built using a free or paid software like a "<strong><a href="http://en.wikipedia.org/wiki/Content_management_system" target="_BLANK">Content Management System (CMS)</a></strong>", which is not as
                    powerful as actual programming languages, I will be able to add, remove and complety customize the website. Some of the features that will eventually come to life on this website are <strong>Poll System</strong>, 
                    <strong>Automatic Screenshot of the Week System</strong>, <strong>Interactive Menus, Animations</strong> and way, way more!
                </p>
                <p>
                    Please note : in order to navigate trough the website, you will have to re-create your account. Also, all the news and the forums threads from the old website are lost. This means we are starting back
                    from the beginning, which is exciting!
                </p>
                <p>
                    Now let me introduce you to the new features...
                </p>
                <p><br /></p>
                <h2 class="title">New Forum</h2>
                <p>
                    The forum has been programmed from scratch. It is very flexible (easy to manage) and has some powerful features in it.<br />
                    The administrators are able to give <strong>Moderation Rights</strong> to some players or groups of players. These players 
                    will be able to : 
                </p>
                <ul>
					<li>Edit forum threads and all of their messages</li>
                    <li>Delete forum threads and their messages</li>
                    <li>Edit/add/remove a label (and its color) for a specific thread. This is useful when you want to create a "Warning" thread, or a "post-it" or anything else you can imagine</li>
                    <li>Edit/add/remove an icon for a specific thread</li>
                    <li>Lock and unlock threads</li>
				</ul>
                <p>
                    Administrators are able to create <strong>sections</strong> that are, for example, visible only for a certain group of players.<br />
                    <strong>You would like to have a private forum for the members of your town? That is possible.</strong>
                </p>
                <p>
                    <img alt="" src="images/icons/dpick.png" style="width: 14px; height: 14px;"/> The forum will eventually be compatible with <strong>Diamond Craft Notifier</strong> (currently in developpement), an actual desktop application to keep in touch when new message are added.
                </p>
                <p><br /></p>
                <h2 class="title">News System</h2>
                <p>
                    The News system is fairly the same as the old website.<br />
                    However, administrators can give rights to some players/groups of players so they can themselves <strong>add, edit and remove news</strong>!
                </p>
                <p>
                    <img alt="" src="images/icons/dpick.png" style="width: 14px; height: 14px;"/> The News System will eventually be compatible with <strong>Diamond Craft Notifier</strong> (currently in developpement), an actual desktop application to keep in touch when news are published.
                </p>
                <p><br /></p>
                <h2 class="title">Groups System</h2>
                <p>
                    Groups are mostly used for rights/permissions purposes.<br />
                    <br />
                    You would like to have a group for the members of your town? That is possible.<br />
                    Is it a private group? Can anybody apply to this group? What color represents your group?
                </p>
                <p><br /></p>
                <h2 class="title">BBCodes and Smileys!</h2>
                <p>
                    Those features let you put smileys into your messages, <span style="color: red;">c</span><span style="color: blue;">o</span><span style="color: purple;">l</span><span style="color: orange;">o</span>r<span style="color: green;">s</span>, <b>bold texts</b>, <span style="text-decoration: underline;">underlined texts</span>, <span style="font-size: 200%;">big texts</span>... <img alt="" src="images/smileys/clin.png"/>
                </p>
                <p><br /></p>
                <h2 class="title">What are youwaiting for?</h2>
                <p style="font-size: 120%;">
                    <a href="subscribe.php">Subscribe now</a> and get over the forum, let us know what you think!
                </p>
                <p style="font-size: 90%; color: green;">
                    <i>~Totoila</i>
                </p>
			</div>
		</div>
	</div>
</div>
<div id="footer" class="container">
	<p>Diamond Craft. Website designed by <a href="mailto:totoila@totoila.com">Anthony Ilareguy (Totoila)</a>. Hosted by <a href="http://www.milsuitefx.com" target="_blank">Milsuite FX</a></p>
</div>
</body>
</html>
