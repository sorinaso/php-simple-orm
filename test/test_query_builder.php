<?php
require_once('test_helper.php');

class TestOfQueryBuilder extends UnitTestCase {
	public function test_select() {
		$qb = new QueryBuilder("test");
		$qb->order("field1");
		$this->assertEqual("SELECT * FROM test ORDER BY field1", $qb->to_s());

		$qb->limit(23);
		$this->assertEqual("SELECT * FROM test ORDER BY field1 LIMIT 23", $qb->to_s());

		$qb->group("field2");
		$this->assertEqual(
		"SELECT * FROM test GROUP BY field2 ORDER BY field1 LIMIT 23",
		$qb->to_s());

		$qb->select('field1,field2');
		$this->assertEqual(
		"SELECT field1,field2 FROM test GROUP BY field2 ORDER BY field1 LIMIT 23",
		$qb->to_s());
	}	
	public function test_insert() {
		$qb = new QueryBuilder("test");
		$qb->insert(array('field1' => 'value1', 'field2' => 'value2'));
		$this->assertEqual(
		"INSERT INTO test set field1 = 'value1',field2 = 'value2'",
		$qb);
	}
	public function test_update() {
		$qb = new QueryBuilder("test");
		$qb->where("id='1'");
		$qb->update(array('field1' => 'value1', 'field2' => 'value2'));
		$this->assertEqual(
		"UPDATE test SET field1 = 'value1',field2 = 'value2' WHERE id='1'",
		$qb);
	}

	public function test_delete() {
		$qb = new QueryBuilder("test");
		$qb->where("id='1'");
		$qb->delete();
		$this->assertEqual("DELETE FROM test WHERE id='1'", $qb);
	}
}
?>
