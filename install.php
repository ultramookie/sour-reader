<html>
<head>
<title>mooshu installation</title>
</head>
<body>
<h2>mooshu installation.</h2>
be sure you have moved config-example.php to config.php and changed all the right values.<br />
this is a one step installation process.<br />
please fill out the following information:<br /><br />
<?php

include_once("db.php");
include_once("mooshulib.php");

if ((stripslashes(!$_POST['checksubmit']))) {
	showAddform();
} else {
	$user = stripslashes($_POST['user']);
	$email = stripslashes($_POST['email']);
	$newpass1 = stripslashes($_POST['pass1']);
	$newpass2 = stripslashes($_POST['pass2']);
	$site = strip_tags($_POST['site']);
	$url = stripslashes($_POST['url']);

	if ((strcmp($newpass1,$newpass2)) == 0) {
       		addUser($user,$email,$newpass1,$site,$url);
       	} else {
               	echo "either your password was typed wrong or your new passwords did not match.  <a href='". $_SERVER['PHP_SELF'] . "'>try again</a>";
       	}
}

?>
</body>
</html>
