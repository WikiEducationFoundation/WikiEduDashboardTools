<?php

require_once __DIR__ . '/../common.php';
require_once __DIR__ . '/../articles.php';
require_once __DIR__ . '/../revisions.php';
require_once __DIR__ . '/../users.php';
require_once __DIR__ . '/../user_id.php';
require_once __DIR__ . '/../revisions_by_user_id.php';

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
