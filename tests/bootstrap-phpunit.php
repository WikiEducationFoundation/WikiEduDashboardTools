<?php

require_once __DIR__ . '/../public_html/common.php';
require_once __DIR__ . '/../public_html/articles.php';
require_once __DIR__ . '/../public_html/revisions.php';
require_once __DIR__ . '/../public_html/users.php';
require_once __DIR__ . '/../public_html/revisions_by_user_id.php';

class MockDbEscape {
	public function escape_string($str) {
		return mysql_escape_string($str);
	}
}

function strip($str) {
	return preg_replace('/^\s*$|^\s+/m', '', $str);
}

global $db;
$db = new MockDbEscape();
