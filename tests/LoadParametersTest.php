<?php

class LoadParametersTest extends PHPUnit_Framework_TestCase {
	public function testLoadWikiNameWithoutProject() {
		global $wiki_name;
		load_wiki_name(array(
			'lang' => 'es',
		));
		$this->assertEquals('eswiki', $wiki_name);
	}

	public function testLoadWikiNameWikiProject() {
		global $wiki_name;
		load_wiki_name(array(
			'lang' => 'en',
			'project' => 'wikipedia',
		));
		$this->assertEquals('enwiki', $wiki_name);
	}
}
