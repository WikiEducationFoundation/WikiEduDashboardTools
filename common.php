<?php

# FIXME: namespaces are different for each lang+project
$namespaces = '0,1,2,3,4,5,10,11,118,119';

$language = empty($_GET["lang"])? "en" : $_GET["lang"];
$project = empty($_GET["project"])? "wikipedia" : $_GET["project"];

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

$con=mysql_connect($hostname,$username,$password);
mysql_select_db($db_name,$con) or die ("Cannot connect to database");
mysql_query("SET NAMES 'utf8'", $con);

// // Confirmed working, should test with other endpoints
// $con=new mysqli($hostname,$username,$password,$db_name);
// if ($con->connect_errno) { echo "Cannot connect the Database"; }
// $con->query("SET NAMES 'utf8'", $con);
// $sqlcode = $con->query($query);
// $jsonObj= array();
// while($result=$sqlcode->fetch_assoc())
// {
//   $jsonObj[] = $result;
// }

if(isset($_GET["start"])) {
  $start = $_GET["start"];
}
if(isset($_GET["end"])) {
  $end = $_GET["end"];
}

if(isset($_GET["oauth_tags"])) {
  $tags = implode(',', $_GET["oauth_tags"]);
} else {
  $tags = 'NULL';
}

if(isset($_GET["user_ids"])) {
  $user_ids = $_GET["user_ids"];
  $sql_user_ids = implode(',', $user_ids);
}

if(isset($_GET["article_titles"])) {
  $article_titles = $_GET["article_titles"];
  array_walk($article_titles, 'clean_title');
  $sql_article_titles = implode(',', $article_titles);
}

if(isset($_GET["article_ids"])) {
  $article_ids = $_GET["article_ids"];
  $sql_article_ids = implode(',', $article_ids);
}

if(isset($_GET["revision_ids"])) {
  $revision_ids = $_GET["revision_ids"];
  $sql_rev_ids = implode(',', $revision_ids);
}

?>
