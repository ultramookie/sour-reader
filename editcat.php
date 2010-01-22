<?php

include_once("header.php");
include_once("db.php");
include_once("sourlib.php");

$catid = $_GET['catid'];

if ( (!$_POST['checksubmit']) && (checkCookie()) ) {
	showEditCatform($catid);
} else if (checkCookie()) {
	$catid = $_POST['catidp'];
	$catname = $_POST['catname'];

	changeCatName($catid,$catname);

} else {
	echo "please <a href='login.php'>login</a> in order to change the site settings!";
}	

include_once("footer.php");
?>

