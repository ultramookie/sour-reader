<?php
	include_once("header.php");

	$action = $_GET['action'];
	$feedid = $_GET['feedid'];
	$feedid = mysql_real_escape_string($feedid);

	if (checkCookie()) {
		if ( preg_match("/^markread$/",$action) ) {
			markFeedRead($feedid);	
		}
		showFeedsFrontPage();
	} else {
		echo "this is <a href=\"http://github.com/ultramookie/sour-reader\">sour reader</a> written by steve \"<a href=\"http://ultramookie.com\">mookie</a>\" kong</a>";
	}

	include_once("footer.php");
?>

