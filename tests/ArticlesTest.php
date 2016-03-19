<?php

class ArticlesTest extends PHPUnit_Framework_TestCase {
	public function testQueryByTitle() {
		load_parameters(array(
			'article_titles' => array("'Main_Page'", "'Straße'", "'Ch'ol'"),
		));
		$expected = "
            SELECT page_id, page_title, page_namespace FROM page
            WHERE page_title IN ('Main_Page','Straße','Ch\'ol')
            AND page_namespace IN (0,1,2,3,4,5,10,11,118,119)
        ";

		$query = make_articles_query();
		$this->assertEquals(strip($expected), strip($query));
	}

	public function testQueryById() {
		load_parameters(array(
			'article_ids' => array(1, 3, 7),
		));
		$expected = "
            SELECT page_id, page_title, page_namespace FROM page
            WHERE page_id IN ('1','3','7')
            AND page_namespace IN (0,1,2,3,4,5,10,11,118,119)
        ";

		$query = make_articles_query();
		$this->assertEquals(strip($expected), strip($query));
	}
}
