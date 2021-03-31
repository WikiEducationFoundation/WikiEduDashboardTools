<?php

function get_db() {
	global $username, $password, $db_name, $wiki_name;

	$settings = parse_ini_file("/data/project/wikiedudashboard/replica.my.cnf", true);

	$hostname = "{$wiki_name}.web.db.svc.wikimedia.cloud";
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
	global $language, $project, $database, $wiki_name;

	$language = empty($query_array["lang"]) ? "en" : $query_array["lang"];
	$project = empty($query_array["project"]) ? "wikipedia" : $query_array["project"];
	$database = empty($query_array["db"]) ? "" : $query_array["db"];

	// Abort if we received garbage.
	if ( !preg_match('/^[a-z_]+$/', $language )
	  || !preg_match('/^[a-z]+$/', $project )
	  || !preg_match('/^[a-z_]*$/', $database )
	) {
	  exit;
	}

	$project_map = array(
	  'wikibooks' => 'wikibooks',
	  'wikinews' => 'wikinews',
	  'wikipedia' => 'wiki',
	  'wikiquote' => 'wikiquote',
	  'wikisource' => 'wikisource',
	  'wikiversity' => 'wikiversity',
	  'wikivoyage' => 'wikivoyage',
	  'wiktionary' => 'wiktionary',
	);
	$short_project = $project_map[$project];

	// We set database name directly if received as a valid parameter
	if ( !empty($database) ) {
	  $wiki_name = $database;
	} else {
	  $wiki_name = $language . $short_project;
	}
}

function load_special_namespaces() {
	global $database, $project, $namespaces;

	// If special namespaces have to be tracked for a wiki,
	// we do so by appending the special namespaces
	$special_namespaces = array(
		// Special explicit DB names
		'wikidatawiki' => '120,122,146', // Property, Query, Lexeme edits
		'commonswiki' => '6', // Files edits

		// Projects
		'wiktionary' => '100,106', // Appendix, Rhymes edits
		'wikisource' => '100,102,104,106,114', // Portal, Author, Page, Index, Translation edits
		'wikibooks' => '102,110', // Cookbook, Wikijunior edits
		'wikiversity' => '100,102,104,106' // School, Portal, Topics, Collections edits
	);

	// Use special database names if specified explicitly or
	// use project to determine the content namespaces.
	$key = empty($database) ? $project : $database;
	if(isset($special_namespaces[$key])) {
		$namespaces = $namespaces . ',' . $special_namespaces[$key];
	}
}

function load_parameters($query_array) {
	global $namespaces, $start, $end, $tags, $sql_usernames,
		$user_name, $sql_article_titles, $sql_article_ids, $sql_rev_ids,
		$training_page_id;

	# FIXME: namespaces are different for each lang+project
	$namespaces = '0,1,2,3,4,5,10,11,118,119';

	load_special_namespaces();

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

	if(isset($query_array["post_article_titles"])) {
	  $article_titles = $query_array["post_article_titles"];
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
		echo '{ "success": false, "data": [], "query": "' . $query . '" }';
		return;
	}

	$jsonData = json_encode($result->fetch_all(MYSQLI_ASSOC));

	echo '{ "success": true, "data": ' . $jsonData . ' }';
}

// Main.
if (php_sapi_name() !== 'cli') {
	global $db;
	if ($_SERVER["REQUEST_METHOD"] === "POST") {
		load_wiki_name($_POST);
		$db = get_db();
		load_parameters($_POST);
	}
	else {
		load_wiki_name($_GET);
		$db = get_db();
		load_parameters($_GET);
	}
	// Control flow continues in endpoint module.
}
