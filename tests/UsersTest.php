<?php

class UserIdTest extends PHPUnit_Framework_TestCase {
	public function testQuery() {
		load_parameters(array(
			'user_name' => 'S\'traße',
		));
		$expected = "
			SELECT user_id
			FROM user WHERE user_name = 'S\'traße'
        ";

		$query = make_user_id_query();
		$this->assertEquals(strip($expected), strip($query));
	}
}
