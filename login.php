<?php
include_once("db.php");
include_once("sourlib.php");

if (stripslashes($_POST['checksubmit'])) {
        $user = stripslashes($_POST['user']);
        $pass  = stripslashes($_POST['pass']);

	$logincheck = checkLogin($user,$pass);

	if ($logincheck == 0) {
		setLoginCookie($user);
	}
}

include_once("header.php");

if (!(stripslashes($_POST['checksubmit']))) {
	showLoginform();
} else {
	if ($logincheck == 0) {
		echo "thanks for logging in $user!<br /><b>start <a href=\"index.php\">reading</a>!</b>";
	} else {
		echo "login failed.  try again.";
	}
}

?>

<?php
	include_once("footer.php");
?>

