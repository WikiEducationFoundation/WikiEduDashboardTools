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

$article_key = '';
$sql_article_keys = '';
if(isset($sql_article_titles)) {
  $article_key = 'page_title';
  $sql_article_keys = $sql_article_titles;
} else if(isset($sql_article_ids)) {
  $article_key = 'page_id';
  $sql_article_keys = $sql_article_ids;
}

$query = <<<EOD
SELECT page_id, page_title, page_namespace FROM page
WHERE $article_key IN ($sql_article_keys)
AND page_namespace IN ($namespaces)
EOD;

$sqlcode = mysql_query($query);

$jsonObj= array();
while($result=mysql_fetch_object($sqlcode))
{
  $jsonObj[] = $result;
}

echo '{ "success": true, "data": ' . json_encode($jsonObj) . ' }';

?>
