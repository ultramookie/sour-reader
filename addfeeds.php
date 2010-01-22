<?php
        include_once("header.php");
?>

<?php

include_once("db.php");
include_once("sourlib.php");

if ((stripslashes(!$_POST['checksubmit'])) && (checkCookie()) ) {
	showFeedsform();
} else if (checkCookie()) {
	$site = stripslashes($_POST['site']);
	$url = stripslashes($_POST['url']);

	addFeed($site,$url);
} else {
	echo "please <a href='login.php'>login</a> in order to change the site settings!";
}

?>
</body>
</html>
