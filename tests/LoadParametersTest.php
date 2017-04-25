<?php

class LoadParametersTest extends PHPUnit_Framework_TestCase {
	public function testLoadWikiNameWithoutProject() {
		global $wiki_name;
		load_wiki_name(array(
			'lang' => 'es'
		));
		$this->assertEquals('eswiki', $wiki_name);
	}

	public function testLoadWikiNameWithoutLanguage() {
		global $wiki_name;
		load_wiki_name(array(
			'project' => 'wikinews'
		));
		$this->assertEquals('enwikinews', $wiki_name);
	}

	public function testLoadWikiNameWithLanguageAndProject() {
		global $wiki_name;
		load_wiki_name(array(
			'lang' => 'hi',
			'project' => 'wikiversity'
		));
		$this->assertEquals('hiwikiversity', $wiki_name);
	}

	public function testLoadWikiNameWithDatabase() {
		global $wiki_name;
		load_wiki_name(array(
			'db' => 'sourceswiki'
		));
		$this->assertEquals('sourceswiki', $wiki_name);
	}
}
