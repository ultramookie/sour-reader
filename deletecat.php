<?php

include_once("header.php");
include_once("db.php");
include_once("sourlib.php");

$catid = $_GET['catid'];

if (checkCookie()) {
	deleteCat($catid);
} else {
        echo "please <a href='login.php'>login</a> in order to delete entries!";
}	

include_once("footer.php");
?>

