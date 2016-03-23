<?php

require_once 'common.php';

function make_user_id_query() {
	global $user_name;
	$query = "
	SELECT user_id
	FROM user WHERE user_name = $user_name
	";
	return $query;
}

if (php_sapi_name() !== 'cli' ) {
	echo_query_results(make_user_id_query());
}
