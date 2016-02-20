<?php

require_once 'common.php';

$auth_hostname = "centralauth.labsdb";
$auth_db_name = "centralauth_p";

$auth_db = new mysqli($auth_hostname, $username, $password, $auth_db_name);
if ($auth_db->connect_errno > 0) {
  die ("Cannot connect to CentralAuth database");
}
$auth_db->set_charset('utf8');

$query = "
SELECT DISTINCT
  lu.user_id as id,
  gu.gu_name as wiki_id,
  gu.gu_id as global_id,
  IF(rv.rev_user_text IS NULL, FALSE, TRUE) as trained
FROM $auth_db_name.globaluser gu
JOIN $db_name.user lu
ON lu.user_name = gu.gu_name
LEFT JOIN $db_name.revision rv
ON rv.rev_page = $training_page_id AND rv.rev_user_text = gu.gu_name
WHERE lu.user_id IN ($sql_user_ids)
";

echo_query_results($query);
