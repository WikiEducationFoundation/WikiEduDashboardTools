<?php

include 'common.php';

$query = <<<EOD
SELECT g.page_id, g.page_title, g.page_namespace,
  c.rev_id, c.rev_timestamp, c.rev_user_text, c.rev_user,
  case when c.rev_parent_id = 0 then 'true' else 'false' end as new_article,
  CAST(c.rev_len AS SIGNED) - CAST(IFNULL(p.rev_len, 0) AS SIGNED) AS byte_change
FROM revision_userindex c
LEFT JOIN revision_userindex p ON p.rev_id = c.rev_parent_id
INNER JOIN page g ON g.page_id = c.rev_page
WHERE g.page_namespace IN (0,2)
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