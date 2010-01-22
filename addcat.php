<?php
        include_once("header.php");
?>

<?php

include_once("db.php");
include_once("sourlib.php");

if ((stripslashes(!$_POST['checksubmit'])) && (checkCookie()) ) {
	showCatform();
} else if (checkCookie()) {
	$cat = stripslashes($_POST['cat']);

	addCat($cat);
} else {
	echo "please <a href='login.php'>login</a> in order to change the site settings!";
}

?>
</body>
</html>
