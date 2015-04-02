<?php

include 'common.php';

if(isset($_GET["user_ids"])) {
  $user_ids = $_GET["user_ids"];
  $sql_user_ids = implode(',', $user_ids);
}

$auth_hostname = "centralauth.labsdb";
$auth_db_name = "centralauth_p";

$auth_con=mysql_connect($auth_hostname, $username, $password);
mysql_select_db($auth_db_name, $auth_con) or die ("Cannot connect to auth database");
mysql_query("SET NAMES 'utf8'", $con);

$query = <<<EOD
SELECT DISTINCT
  lu.user_id as id,
  gu.gu_name as wiki_id,
  gu.gu_id as global_id,
  IF(rv.rev_user_text IS NULL, FALSE, TRUE) as trained
FROM $auth_db_name.globaluser gu
JOIN $db_name.user lu
ON lu.user_name = gu.gu_name
LEFT JOIN $db_name.revision rv
ON rv.rev_page = 36892501 AND rv.rev_user_text = gu.gu_name
WHERE lu.user_id IN ($sql_user_ids)
EOD;

$sqlcode = mysql_query($query);

$jsonObj= array();
while($result=mysql_fetch_object($sqlcode))
{
  $jsonObj[] = $result;
}

echo '{ "success": true, "data": ' . json_encode($jsonObj) . ' }';

?>
