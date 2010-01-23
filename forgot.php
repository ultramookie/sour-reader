<?php

include_once("header.php");
include_once("db.php");
include_once("sourlib.php");

if (!(stripslashes($_POST['checksubmit']))) {
	showForgotform();
} else if ((stripslashes($_POST['checksubmit']))) {
	$email = getEmail();
	$user = getUser();
	$postemail = stripslashes($_POST['email']);
	$postuser = stripslashes($_POST['user']);

	if ( ( (strcmp($email,$postemail)) == 0) && ( (strcmp($user,$postuser)) == 0) ) {
		sendRandomPass($email,"lost");
	} else {
		echo "things didn't match.  <a href=\"forgot.php\">try again</a>!";
	}
}

?>

<?php
	include_once("footer.php");
?>

