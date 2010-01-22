<?php

// For clean errors on xml parsing
libxml_use_internal_errors(true);

function addEntry($title,$description,$pubDate,$link,$guid,$feed) {
	print "adding entry $title (from $feed) <br />";
}

$fh = fopen("/home/ultramookie/images.brokedot.com/list.txt", "r");

while(true)
{
	$line = fgets($fh);
	if($line == null)break;

        $session = curl_init();
        curl_setopt ( $session, CURLOPT_URL, $line );
        curl_setopt ( $session, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt ( $session, CURLOPT_CONNECTTIMEOUT, 2 );
	curl_setopt ( $session, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec ( $session );
        curl_close( $session );
        
	$xml = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
      
      	if (!$xml) {
		print "<hr />$line is not a valid rss or atom feed!<hr />";
	} elseif ($xml->channel->item) {
		foreach ($xml->channel->item as $result) {
			$title = $result->title;
			$description = $result->description;
			$pubDate =  $result->pubDate;
			$link =  $result->link;
			$guid = $result->guid;
			addEntry($title,$description,$pubDate,$link,$guid,$line);
		}
	} elseif ($xml->entry) {
		foreach ($xml->entry as $result) {
			$title = $result->title;
			$description = $result->summary;
			$pubDate = $result->updated;
			$link = $result->link;
			$guid = $result->id;
			$content = $result->content;
			addEntry($title,$description,$pubDate,$link,$guid,$line);
		}
	} else {
		print "<hr />$line is not a valid rss or atom feed!<hr />";
	}


}

fclose($fh);
