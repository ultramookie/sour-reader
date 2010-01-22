<?php
        include_once("header.php");
?>

<?php

include_once("db.php");
include_once("sourlib.php");

$feedid = $_GET['feedid'];
$feedid = mysql_real_escape_string($feedid);

if ((stripslashes(!$_POST['checksubmit'])) && (checkCookie()) ) {
	showFeedEditform($feedid);
} else if (checkCookie()) {
	$feedidp = stripslashes($_POST['feedidp']);
	$feedname = stripslashes($_POST['feedname']);
	$feedurl = stripslashes($_POST['feedurl']);
	$catid = stripslashes($_POST['catid']);

	changeFeed($feedidp,$feedname,$feedurl,$catid);
} else {
	echo "please <a href='login.php'>login</a> in order to change the site settings!";
}

?>
</body>
</html>
