<?php
	include_once("header.php");

	$feedid = $_GET['feedid'];
	$feedid = mysql_real_escape_string($feedid);
	$action = $_GET['action'];
	$action = mysql_real_escape_string($action);

	if (checkCookie()) {
		echo "<p class=\"menusecond\"><a href=\"index.php?action=markread&feedid=$feedid\">mark read</a> | <a href=\"showfeed.php?action=read&feedid=$feedid\">show read</a> | <a href=\"editfeed.php?feedid=$feedid\">edit feed</a> | <a href=\"deletefeed.php?feedid=$feedid\">unsubscribe</a></p>";
	
		showFeed($feedid,$action);

		echo "<p class=\"menusecond\"><a href=\"index.php?action=markread&feedid=$feedid\">mark read</a> | <a href=\"showfeed.php?action=read&feedid=$feedid\">show read</a> | <a href=\"editfeed.php?feedid=$feedid\">edit feed</a> | <a href=\"deletefeed.php?feedid=$feedid\">unsubscribe</a></p>";
	}

	
	include_once("footer.php");
?>

