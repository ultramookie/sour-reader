<?php

include_once("header.php");
include_once("db.php");
include_once("sourlib.php");

$feedid = $_GET['feedid'];

$feedid = mysql_real_escape_string($feedid);
if (checkCookie()) {
	deleteFeed($feedid);
} else {
        echo "please <a href='login.php'>login</a> in order to delete entries!";
}	

include_once("footer.php");
?>

