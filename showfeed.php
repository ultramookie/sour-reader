<?php
	include_once("header.php");

	$feedid = $_GET['feedid'];

	if (checkCookie()) {
		echo "<a href=\"index.php?action=markread&feedid=$feedid\">mark read</a> | <a href=\"editfeed.php?feedid=$feedid\">edit feed</a> | <a href=\"deletefeed.php?feedid=$feedid\">unsubscribe</a><hr />";
	}

	showFeed($feedid);
	
	include_once("footer.php");
?>

