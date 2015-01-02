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
SELECT g.page_id, g.page_title,
  c.rev_id, c.rev_timestamp, c.rev_user_text,
  CAST(c.rev_len AS SIGNED) - CAST(IFNULL(p.rev_len, 0) AS SIGNED) AS byte_change
FROM revision_userindex c
LEFT JOIN revision_userindex p ON p.rev_id = c.rev_parent_id
INNER JOIN page g ON g.page_id = c.rev_page
WHERE g.page_namespace IN (0)
AND c.rev_user_text IN ($sql_user_ids)
AND c.rev_timestamp BETWEEN "$start" AND "$end"
EOD;

$sqlcode = mysql_query($query);

$jsonObj= array();
while($result=mysql_fetch_object($sqlcode))
{
  $jsonObj[] = $result;
}

echo '{ "success": true, "data": ' . json_encode($jsonObj) . ' }';

?>