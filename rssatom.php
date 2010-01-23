<?php
include_once("db.php");
include_once("sourlib.php");

// For clean errors on xml parsing
libxml_use_internal_errors(true);

$ns = array
(
        'content' => 'http://purl.org/rss/1.0/modules/content/'
); 

$query = "select feedid, feedurl from feeds order by feedid";
$status = mysql_query($query);

while($row = mysql_fetch_array($status))
{
	$url = $row['feedurl'];
	$id = $row['feedid'];

        $session = curl_init();
        curl_setopt ( $session, CURLOPT_URL, $url );
        curl_setopt ( $session, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt ( $session, CURLOPT_CONNECTTIMEOUT, 2 );
	curl_setopt ( $session, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec ( $session );
        curl_close( $session );
        
	$xml = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
      
      	if (!$xml) {
		print "<hr />$url is not a valid rss or atom feed!<hr />";
	} elseif ($xml->channel->item) {
		foreach ($xml->channel->item as $result) {
			$title = $result->title;
			$description = $result->description;
			$pubDate =  $result->pubDate;
			$link =  $result->link;
			$guid = $result->guid;
			$content = $result->children($ns['content']);
			if ($content) {
				$description = $content;
			}
			addEntry($title,$description,$pubDate,$link,$guid,$id);
		}
	} elseif ($xml->entry) {
		foreach ($xml->entry as $result) {
			$title = $result->title;
			$description = $result->summary;
			$pubDate = $result->updated;
			$link = $result->link['href'];
			$guid = $result->id;
			$content = $result->content;
			addEntry($title,$description,$pubDate,$link,$guid,$id);
		}
	} else {
		print "<hr />$url is not a valid rss or atom feed!<hr />";
	}


}

?>
