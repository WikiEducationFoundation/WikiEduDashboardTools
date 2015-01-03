<?php

$settings = parse_ini_file("/data/project/wikiedudashboard/replica.my.cnf", true);

$hostname = "enwiki.labsdb";
$username = $settings['client']['user'];
$password = $settings['client']['password'];
$db_name = "enwiki_p";

$con=mysql_connect($hostname,$username,$password);
mysql_select_db($db_name,$con) or die ("Cannot connect the Database");
mysql_query("SET NAMES 'utf8'", $con);

$start = $_GET["start"];
$end = $_GET["end"];

$user_ids = $_GET["user_ids"];
$sql_user_ids = implode(',', $user_ids);

?>