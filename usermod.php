<?php

include_once("header.php");
include_once("db.php");
include_once("mooshulib.php");

if (!(stripslashes($_POST['checksubmit']))) {
	showPasswordChangeform();
} else {
	$username = getUserName();
        $user = $username;
        $pass  = stripslashes($_POST['oldpass']);
	$newpass1 = stripslashes($_POST['newpass1']);
	$newpass2 = stripslashes($_POST['newpass2']);

	$logincheck = checkLogin($user,$pass);

	if (($logincheck == 0) && ((strcmp($newpass1,$newpass2)) == 0)) {
		changePass($user,$newpass1);
		echo "thanks for logging in $user!";
	} else {
		echo "either your password was typed wrong or your new passwords did not match.  <a href='". $_SERVER['PHP_SELF'] . "'>try again</a>";
	}
}

?>

<?php
	include_once("footer.php");
?>

