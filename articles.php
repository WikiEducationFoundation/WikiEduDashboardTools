<?php

$settings = parse_ini_file("/data/project/wikiedudashboard/replica.my.cnf", true);

$hostname = "enwiki.labsdb";
$username = $settings['client']['user'];
$password = $settings['client']['password'];
$db_name = "enwiki_p";

$con=mysql_connect($hostname,$username,$password);
mysql_select_db($db_name,$con) or die ("Cannot connect the Database");
mysql_query("SET NAMES 'utf8'", $con);

$start = $_GET["start"];
$end = $_GET["end"];

$user_ids = $_GET["user_ids"];
$sql_user_ids = implode(',', $user_ids);

$query = <<<EOD
SELECT  page_id, page_title
FROM  page
WHERE page_id IN
  (
  SELECT DISTINCT rev_page
  FROM    revision_userindex
  WHERE   rev_user_text IN ($sql_user_ids)
  AND     rev_timestamp BETWEEN "$start" AND "$end"
  )
AND page_namespace = 0
AND NOT page_is_redirect
EOD;

$sqlcode = mysql_query($query);

$jsonObj= array();
while($result=mysql_fetch_object($sqlcode))
{
  $jsonObj[] = $result;
}

echo '{ "success": true, "data": ' . json_encode($jsonObj) . ' }';

?>