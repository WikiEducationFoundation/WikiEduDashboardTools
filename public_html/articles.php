<?php

require_once 'common.php';

function clean_title(&$title, $key) {
  #FIXME: There's no reason for titles to be quoted in the URL parameters.
  $title = substr($title, 1, strlen($title)-2);
  $title = urldecode($title);
  return $title;
}

function make_articles_query() {
	global $namespaces, $sql_article_ids, $sql_article_titles;

	$article_key = '';
	$sql_article_keys = '';
	if(isset($sql_article_titles)) {
	  $article_key = 'page_title';
	  $sql_article_keys = $sql_article_titles;
	} elseif(isset($sql_article_ids)) {
	  $article_key = 'page_id';
	  $sql_article_keys = $sql_article_ids;
	}

	$query = "
	SELECT page_id, page_title, page_namespace, page_is_redirect FROM page
	WHERE $article_key IN ($sql_article_keys)
	AND page_namespace IN ($namespaces)
	";

	return $query;
}

if (php_sapi_name() !== 'cli' ) {
	echo_query_results(make_articles_query());
}
