<?php

function mysql_escape_mimic($inp) {
  if(is_array($inp))
      return array_map(__METHOD__, $inp);
  if(!empty($inp) && is_string($inp)) {
    return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
  }
  return $inp;
}

function clean_title(&$title, $key) {
  $title = substr($title, 1, strlen($title)-2);
  $title = urldecode($title);
  $title = mysql_escape_mimic($title);
  $title = "'" . $title . "'";
  return $title;
}

include 'common.php';

$query = <<<EOD
SELECT page_title FROM page
WHERE page_title IN ($sql_article_titles)
AND page_namespace = 0
EOD;

$sqlcode = mysql_query($query);

$jsonObj= array();
while($result=mysql_fetch_object($sqlcode))
{
  $jsonObj[] = $result;
}

echo '{ "success": true, "data": ' . json_encode($jsonObj) . ' }';

?>
