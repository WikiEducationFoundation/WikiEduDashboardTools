<?php

class RevisionsByUserTest extends PHPUnit_Framework_TestCase {
	public function testQueryByTitle() {
		load_parameters(array(
			'start' => '20130101',
			'end' => '20170203',
			'usernames' => array("Admin", "北岛", "Bak'un"),
		));
		$expected = "
			SELECT g.page_id, g.page_title, g.page_namespace,
			  c.rev_id, c.rev_timestamp, c.rev_user_text, c.rev_user,
			  case when ct.ct_tag_id IS NULL then 'false' else 'true' end as system,
			  case when c.rev_parent_id = 0 then 'true' else 'false' end as new_article,
			  CAST(c.rev_len AS SIGNED) - CAST(IFNULL(p.rev_len, 0) AS SIGNED) AS byte_change
			FROM revision_userindex c
			LEFT JOIN revision_userindex p ON p.rev_id = c.rev_parent_id
			INNER JOIN page g ON g.page_id = c.rev_page
			LEFT JOIN change_tag_def ctd
				ON ctd.ctd_name IN ($tags)
			LEFT JOIN change_tag ct
				ON ct.ct_rev_id = c.rev_id
				AND ct.ct_tag_id = ctd.ctd_id
			WHERE g.page_namespace IN (0,1,2,3,4,5,10,11,118,119)
			AND c.rev_user_text IN ('Admin','北岛','Bak\'un')
			AND c.rev_timestamp BETWEEN '20130101' AND '20170203'
		  ";
		$query = make_revisions_by_user_query();
		$this->assertEquals(strip($expected), strip($query));
	}
}
