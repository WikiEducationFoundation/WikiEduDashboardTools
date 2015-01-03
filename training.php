<?php

include 'common.php';

$query = <<<EOD
SELECT  rev_user_text
FROM  revision
WHERE rev_page = 36892501
AND   rev_user_text IN ($sql_user_ids)
GROUP by rev_user_text
EOD;

$sqlcode = mysql_query($query);

$jsonObj= array();
while($result=mysql_fetch_object($sqlcode))
{
  $jsonObj[] = $result;
}

echo '{ "success": true, "data": ' . json_encode($jsonObj) . ' }';

?>