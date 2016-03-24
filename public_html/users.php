<?php

require_once 'common.php';

function make_users_query() {
	global $username, $password, $db_name, $auth_db_name, $training_page_id,
		$sql_user_ids, $sql_usernames;

	$auth_db = get_auth_db();

	if (isset($sql_usernames)) {
	  $user_clause = "AND lu.user_name IN ($sql_usernames)";
	} elseif (isset($sql_user_ids)) {
	  $user_clause = "AND lu.user_id IN ($sql_user_ids)";
	}
	if (!isset($user_clause)) {
		return;
	}

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
	WHERE
	{$user_clause}
	";
	return $query;
}

if (php_sapi_name() !== 'cli') {
	echo_query_results(make_users_query());
}
