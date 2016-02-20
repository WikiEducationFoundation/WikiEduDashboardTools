<?php

# FIXME: namespaces are different for each lang+project
$namespaces = '0,1,2,3,4,5,10,11,118,119';

$language = empty($_GET["lang"])? "en" : $_GET["lang"];
$project = empty($_GET["project"])? "wikipedia" : $_GET["project"];

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

function escape_and_quote($str) {
  global $db;
  return "'{$db->escape_string($str)}'";
}

// db-escape a list and join with commas
function escape_implode($args) {
  global $db;
  return implode(',', array_map('escape_and_quote', $args));
}

if(isset($_GET["start"])) {
  $start = escape_and_quote($_GET["start"]);
}
if(isset($_GET["end"])) {
  $end = escape_and_quote($_GET["end"]);
}

if(isset($_GET["oauth_tags"])) {
  $tags = escape_implode($_GET["oauth_tags"]);
} else {
  $tags = 'NULL';
}

if(isset($_GET["user_ids"])) {
  $sql_user_ids = escape_implode($_GET["user_ids"]);
}

if(isset($_GET["usernames"])) {
  $sql_usernames = escape_implode($_GET["usernames"]);
}

if(isset($_GET["user_name"])) {
  $user_name = escape_and_quote($_GET["user_name"]);
}

if(isset($_GET["article_titles"])) {
  $article_titles = $_GET["article_titles"];
  array_walk($article_titles, 'clean_title');
  $sql_article_titles = escape_implode($article_titles);
}

if(isset($_GET["article_ids"])) {
  $sql_article_ids = escape_implode($_GET["article_ids"]);
}

if(isset($_GET["revision_ids"])) {
  $sql_rev_ids = escape_implode($_GET["revision_ids"]);
}

if(isset($_GET["training_page_id"])) {
  $training_page_id = escape_and_quote($_GET["training_page_id"]);
} else {
  $training_page_id = 36892501;
}

function echo_query_results($query) {
	global $db;
	$result = $db->query($query);

	$jsonData = json_encode($result->fetch_all(MYSQLI_ASSOC));

	echo '{ "success": true, "data": ' . $jsonData . ' }';
}
