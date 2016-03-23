<?php

function get_db() {
	global $username, $password, $db_name, $wiki_name;

	$settings = parse_ini_file("/data/project/wikiedudashboard/replica.my.cnf", true);

	$hostname = "{$wiki_name}.labsdb";
	$username = $settings['client']['user'];
	$password = $settings['client']['password'];
	$db_name = "{$wiki_name}_p";

	$db = new mysqli($hostname, $username, $password, $db_name);
	if ($db->connect_errno > 0) {
	  die ("Cannot connect to database");
	}
	$db->set_charset('utf8');
	return $db;
}

function get_auth_db() {
	global $username, $password, $db_name, $auth_db_name;

	$auth_hostname = "centralauth.labsdb";
	$auth_db_name = "centralauth_p";

	$auth_db = new mysqli($auth_hostname, $username, $password, $auth_db_name);
	if ($auth_db->connect_errno > 0) {
	  die ("Cannot connect to CentralAuth database");
	}
	$auth_db->set_charset('utf8');
	return $auth_db;
}

function escape_and_quote($str) {
  global $db;
  return "'{$db->escape_string($str)}'";
}

// db-escape a list and join with commas
function escape_implode($args) {
  return implode(',', array_map('escape_and_quote', $args));
}

function load_wiki_name($query_array) {
	global $language, $project, $wiki_name;

	$language = empty($query_array["lang"])? "en" : $query_array["lang"];
	$project = empty($query_array["project"])? "wikipedia" : $query_array["project"];

	// Abort if we received garbage.
	if ( !preg_match('/^[a-z]+$/', $language )
		|| !preg_match('/^[a-z]+$/', $project )
	) {
		exit;
	}

	$project_map = array(
	  'wikibooks' => 'wikibooks',
	  'wikidata' => 'wikidata',
	  'wikinews' => 'wikinews',
	  'wikipedia' => 'wiki',
	  'wikiquote' => 'wikiquote',
	  'wikisource' => 'wikisource',
	  'wikiversity' => 'wikiversity',
	  'wikivoyage' => 'wikivoyage',
	  'wiktionary' => 'wiktionary',
	);
	$short_project = $project_map[$project];
	$wiki_name = $language . $short_project;
}

function load_parameters($query_array) {
	global $namespaces, $start, $end, $tags, $sql_user_ids, $sql_usernames,
		$user_name, $sql_article_titles, $sql_article_ids, $sql_rev_ids,
		$training_page_id;

	# FIXME: namespaces are different for each lang+project
	$namespaces = '0,1,2,3,4,5,10,11,118,119';

	if(isset($query_array["start"])) {
	  $start = escape_and_quote($query_array["start"]);
	}
	if(isset($query_array["end"])) {
	  $end = escape_and_quote($query_array["end"]);
	}

	if(isset($query_array["oauth_tags"])) {
	  $tags = escape_implode($query_array["oauth_tags"]);
	} else {
	  $tags = 'NULL';
	}

	if(isset($query_array["user_ids"])) {
	  $sql_user_ids = escape_implode($query_array["user_ids"]);
	}

	if(isset($query_array["usernames"])) {
	  $sql_usernames = escape_implode($query_array["usernames"]);
	}

	if(isset($query_array["user_name"])) {
	  $user_name = escape_and_quote($query_array["user_name"]);
	}

	if(isset($query_array["article_titles"])) {
	  $article_titles = $query_array["article_titles"];
	  array_walk($article_titles, 'clean_title');
	  $sql_article_titles = escape_implode($article_titles);
	}

	if(isset($query_array["article_ids"])) {
	  $sql_article_ids = escape_implode($query_array["article_ids"]);
	}

	if(isset($query_array["revision_ids"])) {
	  $sql_rev_ids = escape_implode($query_array["revision_ids"]);
	}

	if(isset($query_array["training_page_id"])) {
	  $training_page_id = escape_and_quote($query_array["training_page_id"]);
	} else {
	  $training_page_id = 36892501;
	}
}

function echo_query_results($query) {
	global $db;
	$result = $db->query($query);

	if ($result === false) {
		echo '{ "success": false, "data": [] }';
		return;
	}

	$jsonData = json_encode($result->fetch_all(MYSQLI_ASSOC));

	echo '{ "success": true, "data": ' . $jsonData . ' }';
}

// Main.
if (php_sapi_name() !== 'cli') {
	global $db;
	load_wiki_name($_GET);
	$db = get_db();
	load_parameters($_GET);
	// Control flow continues in endpoint module.
}
