<?php
        include_once("header.php");
?>

<?php

include_once("db.php");
include_once("sourlib.php");

if ((stripslashes(!$_POST['checksubmit'])) && (checkCookie()) ) {
	showSettingsform();
} else if (checkCookie()) {
	$purgedays = $_POST['purgedays'];
	changeSettings($purgedays);
} else {
	echo "please <a href='login.php'>login</a> in order to change the site settings!";
}

?>
</body>
</html>
