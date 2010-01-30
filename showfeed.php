<?php
	include_once("header.php");

	$feedid = $_GET['feedid'];
	$feedid = mysql_real_escape_string($feedid);
	$action = $_GET['action'];
	$action = mysql_real_escape_string($action);
	$catid = $_GET['catid'];
	$catid = mysql_real_escape_string($catid);


	if (checkCookie()) {

		printFeedbar($feedid,$action,$catid);	
		
		showFeed($feedid,$action,$catid);
	
		printFeedbar($feedid,$action,$catid);	
	}

	
	include_once("footer.php");
?>

