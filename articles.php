<?php

include 'common.php';

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