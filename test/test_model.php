<?php
require_once('test_helper.php');

class Test extends Model {
	static $table_name = 'test';
	static $primary_key = 'id';
}


class TestOfModel extends UnitTestCase {
	function tearDown() {
		$this->clean_table();
	}

	public function test_find() {
		$m = new Test(array('name' => 'jose'));
		$this->assertNull($m->pk());
		$this->assertTrue($m->is_new_record());
		$m = new Test(array('id' => 5,'name' => 'jose'), false);
		$this->assertNotNull($m->pk());
		$this->assertFalse($m->is_new_record());
			
	}

	public function test_construct() {
		echo "test construct...\n";
		$m = new Test(array('id' => 5,'name' => 'jose'));
		$this->assertNull($m->pk());
		$this->assertTrue($m->is_new_record());
		$m = new Test(array('id' => 5,'name' => 'jose'), false);
		$this->assertNotNull($m->pk());
		$this->assertFalse($m->is_new_record());
	}

	public function test_save() {
		echo "test_save\n";
		echo "insert 'jose'...\n";
		$m = new Test(array('id' => 5,'name' => 'jose'));
		$this->assertTrue($m->is_new_record());
		$m->save();		
		$rows = Connection::instance()->query('SELECT * FROM test')->fetch_rows();
		$this->assertEqual(sizeof($rows), 1);
		$this->assertEqual($rows[0], array('id' => 1,'name' => 'jose',
						'last_name' => '', 'age' => NULL));
		$this->assertFalse($m->is_new_record());
		echo "update 'jose' a 'pepe'...\n";
		$m->name = 'pepe';
		$m->age = 4;
var_dump($m->get_changed_attributes());
		$m->save();
		$rows = Connection::instance()->query('SELECT * FROM test')->fetch_rows();
		$this->assertEqual(sizeof($rows), 1);
		$this->assertEqual($rows[0], array('id' => 1,'name' => 'pepe',
						'last_name' => '', 'age' => 4));
	}

	private function clean_table() {
		Connection::instance()->query('DELETE FROM test');
	}
}
