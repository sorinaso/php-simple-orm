<?php
require_once('test_helper.php');
require_once(dirname(__FILE__) . '/simpletest/autorun.php');

class TestOfConnection extends UnitTestCase {
	function __construct() {
		$this->con = new Connection();
	}

	public function database_name() {
		$this->assertEqual("test", Connection::database_name());	
	}

	public function test_unique_connection() {
		$con1 = Connection::instance();
		$con2 = Connection::instance();
		$con1 = $con1->query("SELECT CONNECTION_ID()")->fetch_rows();
		$con2 = $con2->query("SELECT CONNECTION_ID()")->fetch_rows();
		$this->assertEqual($con1[0]["CONNECTION_ID()"],$con2[0]["CONNECTION_ID()"]);
	}

	public function test_select() {
		$this->con->query("SELECT * FROM test");
	}	

	public function test_insert() {
		$this->con->query("INSERT INTO test VALUES(1,'Brasil')");
		$this->con->query("SELECT * FROM test");
		$rows = $this->con->fetch_rows();
		$this->assertEqual(sizeof($rows), 1);
		$this->assertEqual($rows[0]["name"], "Brasil");
	}

	public function test_update() {
		$this->con->query("UPDATE test set name='Argentina' where id=1");
		$this->con->query("SELECT * FROM test");
		$rows = $this->con->fetch_rows();
		$this->assertEqual(sizeof($rows), 1);
		$this->assertEqual($rows[0]["name"], "Argentina");
	}

	public function test_delete() {
		$this->con->query("delete from test");
		$this->assertEqual($this->con->count_table_rows("test"), 0);
	}
}
?>
