<?php

// sour reader main library
// steve "mookie" kong
// http://ultramookie.com
//
// licensed under gplv3
// http://www.gnu.org/licenses/gpl-3.0.html

error_reporting(E_ERROR | E_PARSE);

$sitename = getSiteName();
$siteurl = getSiteUrl();

function showUpdateForm() {
	$ua = $_SERVER['HTTP_USER_AGENT'];
        echo "<form action=\"";
        echo $_SERVER['PHP_SELF'];
        echo "\"";
        echo " method=\"post\">";
	echo "<input type=\"text\" name=\"url\" />";
        echo "<input type=\"hidden\" name=\"checksubmit\" value=\"1\">";
        echo "<input type=\"submit\" name=\"submit\" value=\"shorten\" id=\"submitbutton1\">";
        echo "</form>";
}

function showRecent() {
	$numResults = 10;
	$urlLen = 60;

	$query = "select id, url from main order by creation desc limit $numResults";
	$status = mysql_query($query);
	$query = "select id, url, count from main order by count desc limit $numResults";
	$countresult = mysql_query($query);
	$query = "select id, url from main order by accessed desc limit $numResults";
	$accessresult = mysql_query($query);
	$query = "select id from main order by creation desc limit 1";
	$randresult = mysql_query($query);

	
	$numrows = mysql_num_rows($status);

	if ($numrows == 0) {
		print "no links yet!";
	} else {
		$siteurl = getSiteUrl();

		print "<b>random mooshu'd link:</b>";
       		$randrow = mysql_fetch_array($randresult);
		$randurlid = rand(1,$randrow['id']);
		$shortenedID = shortenUrlID($randurlid);
		print "<ul>";
		print "<li><b><a href=\"$siteurl/$shortenedID\">$siteurl/$shortenedID</a></b></li>";
		print "</ul>";

		print "<b>recently mooshu'd links:</b>";
		print "<ul>";
        	while ($row = mysql_fetch_array($status)) {
			$shortenedID = shortenUrlID($row['id']);
			$url = $row['url'];
			$suburl = substr($row['url'],0,$urlLen);
			print "<li><b><a href=\"$siteurl/$shortenedID\">$siteurl/$shortenedID</a></b>: <a href=\"$url\" rel=\"nofollow\">$suburl...</a></li>";
		}
		print "</ul>";
		print "<b>most accessed mooshu'd links:</b>";
		print "<ul>";
        	while ($row = mysql_fetch_array($countresult)) {
			$shortenedID = shortenUrlID($row['id']);
			$url = $row['url'];
			$suburl = substr($row['url'],0,$urlLen);
			$count = $row['count'];
			print "<li><b><a href=\"$siteurl/$shortenedID\">$siteurl/$shortenedID</a></b> ($count): <a href=\"$url\" rel=\"nofollow\">$suburl...</a></li>";
		}
		print "</ul>";
		print "<b>recently accessed mooshu'd links:</b>";
		print "<ul>";
        	while ($row = mysql_fetch_array($accessresult)) {
			$shortenedID = shortenUrlID($row['id']);
			$url = $row['url'];
			$suburl = substr($row['url'],0,$urlLen);
			$count = $row['count'];
			print "<li><b><a href=\"$siteurl/$shortenedID\">$siteurl/$shortenedID</a></b>: <a href=\"$url\" rel=\"nofollow\">$suburl...</a></li>";
		}
		print "</ul>";
        }
}

function addEntry($title,$description,$pubDate,$link,$guid,$feed) {
	$title = mysql_real_escape_string($title);
	$description = mysql_real_escape_string($description);
	$link = mysql_real_escape_string($link);
	$guid = mysql_real_escape_string($guid);

	$query = "select guid,feedid from main where guid='$guid' and feedid='$feed'";
	$status = mysql_query($query);
	if (mysql_num_rows($status) >= 1) {
		echo "already have article $title!<br />";
	} else {
		$timestamp = strtotime($pubDate);
		$mypubDate = date('YmdHis',$timestamp);
		$query = "insert into main (title, description, pubDate, link, guid, feedid) values ('$title', '$description', '$mypubDate', '$link', '$guid', '$feed')";
		$result = mysql_query($query);
		print "adding entry $title (from $feed) <br />";
	}

}

function showLoginForm() {
	echo "<form action=\"";
	echo $_SERVER['PHP_SELF'];
	echo "\"";
	echo " method=\"post\">";
	echo "user: <input type=\"text\" name=\"user\"><br />";
	echo "pass: <input type=\"password\" name=\"pass\"><br />";
	echo "<input type=\"hidden\" name=\"checksubmit\" value=\"1\">";
	echo "<input type=\"submit\" name=\"submit\" value=\"login\">";
	echo "</form>";
	echo "<hr />";
	echo "<a href='forgot.php'>forgot password</a>";
}

function getSecret() {
        $query = "select secret from user limit 1";
        $result = mysql_query($query);

        $row = mysql_fetch_array($result);

        return($row['secret']);
}

function getCookie() {
        $query = "select cookie from user limit 1";
        $result = mysql_query($query);

        $row = mysql_fetch_array($result);

        return($row['cookie']);
}

function checkCookie() {
	$secret = getSecret();
	$cookie = $_COOKIE['sourreader'];
	$user = $_COOKIE['user'];
	$storedcookie = getCookie();

	$loggedin = 0;

	$test = sha1($user . $secret);

	if ( (strlen($cookie) > 0) && ($cookie == $storedcookie) && ($cookie == $test) ) {
		$loggedin = 1;
	}

	return $loggedin;
}

function getUserName() {
	if(checkCookie()) {
		$name = $_COOKIE['user'];
	} else {
		$name = "not logged in";
	}
	return $name;
}

function getEmail() {
        $query = "select email from user limit 1";
        $result = mysql_query($query);

        $row = mysql_fetch_array($result);

        return($row['email']);
}

function getUser() {
        $query = "select name from user limit 1";
        $result = mysql_query($query);

        $row = mysql_fetch_array($result);

        return($row['name']);
}

function getSiteName() {
	$query = "select name from site limit 1";
	$result = mysql_query($query);

	$row = mysql_fetch_array($result);

	return($row['name']);
}

function getSiteUrl() {
	$query = "select url from site limit 1";
	$result = mysql_query($query);

	$row = mysql_fetch_array($result);

	return("http://" . $row['url']);
}

function getRawSiteURl() {
	$query = "select url from site limit 1";
	$result = mysql_query($query);

	$row = mysql_fetch_array($result);

	return($row['url']);
}

function setLoginCookie($user) {
		$secret = getSecret();
                $login = sha1($user . $secret);
                $expiry = time()+60*60*24*30;
		setcookie('user',$user,"$expiry");
                setcookie('sourreader',$login,"$expiry");

	        $query = "update user set cookie='$login' where name='$user'";
        	$result = mysql_query($query);
}

function killCookie() {
	if(checkCookie()) {
		$expiry = time() - 4800;
		setcookie('user','',"$expiry");
		setcookie('sourreader','',"$expiry");
	}
}

function checkLogin($user,$pass) {
        $salt = substr("$user",0,2);
        $epass = crypt($pass,$salt);

	$query = "select * from user where name='$user' and pass='$epass'";
	$result = mysql_query($query);

	if (mysql_num_rows($result)==1) {
		return 0;
	} else {
		return 1;
	}
}

function showAddform() {
	echo "<form action=\"";
	echo $_SERVER['PHP_SELF'];
	echo "\"";
	echo " method=\"post\">";
	echo "user: <input type=\"text\" name=\"user\"><br />";
	echo "email: <input type=\"text\" name=\"email\"><br />";
	echo "password: <input type=\"password\" name=\"pass1\"><br />";
	echo "password (again): <input type=\"password\" name=\"pass2\"><br />";
	echo "<input type=\"hidden\" name=\"checksubmit\" value=\"1\">";
	echo "<input type=\"submit\" name=\"submit\" value=\"install\">";
	echo "</form>";
}

function showSettingsform() {
	echo "<p><b>general site settings:</b></p>";
	echo "<form action=\"";
	echo $_SERVER['PHP_SELF'];
	echo "\"";
	echo " method=\"post\">";
        echo "user: <input type=\"text\" name=\"user\"><br />";
        echo "pass: <input type=\"password\" name=\"pass\"><br />";
	echo "add category: <input type=\"text\" name=\"category\" \"><br />";
	echo "<input type=\"hidden\" name=\"checksubmit\" value=\"1\">";
	echo "<input type=\"submit\" name=\"submit\" value=\"update\">";
	echo "</form>";
}

function showFeedsform() {
	echo "<p><b>add feed:</b></p>";
	echo "<form action=\"";
	echo $_SERVER['PHP_SELF'];
	echo "\"";
	echo " method=\"post\">";
	echo "feed name: <input type=\"text\" name=\"site\" \"><br />";
	echo "feed url: <input type=\"text\" name=\"url\" \"><br />";
	echo "<input type=\"hidden\" name=\"checksubmit\" value=\"1\">";
	echo "<input type=\"submit\" name=\"submit\" value=\"update\">";
	echo "</form>";
}

function showForgotform() {
        echo "Please enter the following information to reset your password: <br />";
        echo "<form action=\"";
        echo $_SERVER['PHP_SELF'];
        echo "\"";
        echo " method=\"post\">";
        echo "user: <input type=\"text\" name=\"user\"><br />";
        echo "email: <input type=\"text\" name=\"email\"><br />";
        echo "<input type=\"hidden\" name=\"checksubmit\" value=\"1\">";
        echo "<input type=\"submit\" name=\"submit\" value=\"Reset Password\">";
        echo "</form>";
}


function generateCode($length=16) {
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
		$code .= $chars[mt_rand(0,$clen)];
        }
        return $code;
}

function showPasswordChangeform() {
	$username = getUserName();
	echo "changing password for ";
	echo $username;
	echo "<form action=\"";
	echo $_SERVER['PHP_SELF'];
	echo "\"";
	echo " method=\"post\">";
	echo "old pass: <input type=\"password\" name=\"oldpass\"><br />";
	echo "new pass: <input type=\"password\" name=\"newpass1\"><br />";
	echo "new pass (again): <input type=\"password\" name=\"newpass2\"><br />";
	echo "<input type=\"hidden\" name=\"checksubmit\" value=\"1\">";
	echo "<input type=\"submit\" name=\"submit\">";
	echo "</form>";
}

function changePass($user,$pass) {
	$email = getEmail();
        $salt = substr("$email",0,2);
        $epass = crypt($pass,$salt);

	$query = "update user set pass='$epass' where name='$user'";
	$result = mysql_query($query);

	echo "password has been updated!";
}

function changeSettings($site,$url,$numberIndex) {

        $site = mysql_real_escape_string($site);
        $url = mysql_real_escape_string($url);

	$query = "update site set name='$site', url='$url' limit 1";
	$result = mysql_query($query);

	echo "your settings have been updated!";

}

function addFeed($site,$url) {

        $site = mysql_real_escape_string($site);
        $url = mysql_real_escape_string($url);

	$query = "select url from feeds where url='$url'";
	$status = mysql_query($query);
	if (mysql_num_rows($status) >= 1) {
		echo "already subscribed to $url!";
	} else {
		$query = "insert into feeds (feedname,feedurl,feedcat) values ('$site', '$url', '1')";
		print "$query<br />";
		$result = mysql_query($query);
		echo "$site has been added!";
	}
}

function addUser($user,$email,$pass) {
        $salt = substr("$email",0,2);
        $epass = crypt($pass,$salt);

	$query = "select * from user";
	$status = mysql_query($query);

	if (mysql_num_rows($status) >= 1) {
		echo "already installed!";
	} else {
		$user = mysql_real_escape_string($user);
		$email = mysql_real_escape_string($email);
		$pass = mysql_real_escape_string($pass);
		
		$query = "create table user ( name varchar(30) NOT NULL, email varchar(30) NOT NULL, pass varchar(30) NOT NULL, secret varchar(6), cookie varchar(300) )";
		$status = mysql_query($query);

		$query = "create table main ( id int NOT NULL AUTO_INCREMENT, entrytime DATETIME NOT NULL, title varchar(1024) NOT NULL, description varchar(50000), pubDate varchar(1024), link varchar(1024) NOT NULL, guid varchar(1024) NOT NULL, status varchar(2), feedid int NOT NULL, PRIMARY KEY (id)); ";
		$status = mysql_query($query);
		
		$query = "create table feeds ( feedid int NOT NULL AUTO_INCREMENT, feedname varchar(1024) NOT NULL, feedurl varchar(1024) NOT NULL, feedcat int NOT NULL, PRIMARY KEY(feedid) ); ";
		$status = mysql_query($query);
		
		$query = "create table categories ( catid int NOT NULL AUTO_INCREMENT, catname varchar(1024) NOT NULL, PRIMARY KEY(catid)); ";
		$status = mysql_query($query);
	
		$query = "insert into categories (catname) values ('general')";
		$status = mysql_query($query);
		
		$secret = generateCode();
	
		$query = "insert into user (name,email,pass,secret) values ('$user','$email','$epass','$secret')";
		$status = mysql_query($query);
	
		echo "sour reader installed!  thanks!";
	}
}

function sendRandomPass($email,$func) {
        $pass = generateCode();
	$salt = substr("$email",0,2);
	$epass = crypt($pass,$salt);

	$email = mysql_real_escape_string($email);
	
	$to = "$email";
	$from = "From: enter@something.here.or.else.tld";
	$subject = "password";
	$body = "hi, your password is $pass. please login using your email address and the password.  feel free to change your password at anytime.";
	if (mail($to, $subject, $body, $from)) {
		if ((strcmp($func,"new")) == 0) {
			$query = "insert into user (email,pass) values ('$email','$epass')";
			$status = mysql_query($query);
		} else if ((strcmp($func,"lost")) == 0) {
			$query = "update user set pass='$epass' where email='$email'";
			$status = mysql_query($query);
		} else {
			echo "nothing to do!";
		}

		echo "<p>Your new password has been sent!  <a href='login.php'>login</a> after you receive your password.</p>";
	} else {
		echo("<p>Message delivery failed...</p>");
	}
}

