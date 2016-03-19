<?php

require_once 'common.php';

function make_users_query() {
	global $username, $password, $db_name, $training_page_id, $sql_user_ids;

	$auth_db = get_auth_db();

	$query = "
	SELECT DISTINCT
	  lu.user_id as id,
	  gu.gu_name as wiki_id,
	  gu.gu_id as global_id,
	  IF(rv.rev_user_text IS NULL, FALSE, TRUE) as trained
	FROM $auth_db_name.globaluser gu
	JOIN $db_name.user lu
	ON lu.user_name = gu.gu_name
	LEFT JOIN $db_name.revision_userindex rv
	ON rv.rev_page = $training_page_id AND rv.rev_user_text = gu.gu_name
	WHERE lu.user_id IN ($sql_user_ids)
	";
	return $query;
}

if (php_sapi_name() !== 'cli') {
	echo_query_results(make_users_query());
}
