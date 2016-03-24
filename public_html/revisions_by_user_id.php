<?php
require_once 'common.php';
require_once 'revisions.php';

if (php_sapi_name() !== 'cli' ) {
	echo_query_results(make_revisions_by_user_query());
}
