<?php
	include_once("header.php");

	$feedid = $_GET['feedid'];

if (checkCookie()) {
}

	showFeed($feedid);
	
	include_once("footer.php");
?>

