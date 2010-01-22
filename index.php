<?php
	include_once("header.php");

	$action = $_GET['action'];
	$feedid = $_GET['feedid'];
	$feedid = mysql_real_escape_string($feedid);

	if (checkCookie()) {
		if ( preg_match("/^markread$/",$action) ) {
			markFeedRead($feedid);	
		}
	}

	showFeedsFrontPage();
	
	include_once("footer.php");
?>

