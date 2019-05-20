<?php

//$feed = implode(file('http://feeds.mashable.com/Mashable'));
//$xml = simplexml_load_string($feed);
//$json = json_encode($xml);
//$array = json_decode($json,TRUE);


$feed = new DOMDocument();
$url = file_get_contents('http://feeds.mashable.com/Mashable');
$feed->load('http://feeds.mashable.com/Mashable');
$json = array();
/*$json['title'] = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('title')->item(0)->firstChild->nodeValue;
$json['description'] = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('description')->item(0)->firstChild->nodeValue;
$json['link'] = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('link')->item(0)->firstChild->nodeValue;*/
$items = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('item');

$json['item'] = array();
$i = 0;

foreach($items as $key => $item) {
  $title = $item->getElementsByTagName('title')->item(0)->firstChild->nodeValue;
  $link = $item->getElementsByTagName('link')->item(0)->firstChild->nodeValue;
  $description = $item->getElementsByTagName('description')->item(0)->firstChild->nodeValue;
  $pubDate = $item->getElementsByTagName('pubDate')->item(0)->firstChild->nodeValue;
  $guid = $item->getElementsByTagName('guid')->item(0)->firstChild->nodeValue;

  $json['item'][$key]['title'] = $title;
  $json['item'][$key]['link'] = $link;
//  $json['item'][$key]['description'] = $description;
//  $json['item'][$key]['pubdate'] = $pubDate;
  $json['item'][$key]['guid'] = $guid;


  echo "<h1><a href='$link' target='_blank'>$title</a></h1><p><a href='index.php?url=$link' target='_blank'>Guardar</a></p><p>$description</p>";
}

//print_r($json);

?>