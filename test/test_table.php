<?php
require_once('test_helper.php');

class Test extends Model {
	static $table_name = 'test';
	static $primary_key = 'id';
}

class TestOfTable extends SimpleORMTest {
	public function test_load() {
		$t 	= Table::load('Test');
		$t2 	= Table::load('Test');
		$this->assertReference($t, $t2);
	}

	public function test_pk() {
		$this->assertEqual('id', Table::load('Test')->pk);
	}

	public function test_get_columns() {
		$cols 	= Table::load('Test')->columns;
		$col_rows = Connection::instance()->query("SHOW COLUMNS FROM test")->fetch_rows();
		$i=0;
		foreach($cols as $col) {
			$this->assertColumn($col_rows[$i], $col);
			$i++;
		}
	}

	public function test_insert() {
		$t 	= Table::load('Test');

		$this->clean_table('test');
		$data = array('name' => 'juan');
		$t->insert($data);
		$this->assertEqual(Connection::$last_sql, "INSERT INTO test set name = 'juan'");

		$this->clean_table('test');
		$data = array('name' => 'juan', 'last_name' => 'perez', 'age' => 6);
		$t->insert($data);
		$this->assertEqual(Connection::$last_sql,"INSERT INTO test set name = 'juan',last_name = 'perez',age = '6'");
	}

	public function test_update() {
		$t 	= Table::load('Test');
		$data = array('last_name' => 'gomez');
		$t->update($data, 'id=2');
		$this->assertEqual(Connection::$last_sql,"UPDATE test SET last_name = 'gomez' WHERE id=2");
	}

	public function test_delete() {
		$t 	= Table::load('Test');
		$data = array('last_name' => 'gomez');
		$t->delete('id=2');
		$this->assertEqual(Connection::$last_sql,"DELETE FROM test WHERE id=2");
	}

	public function test_find() {
		$t 	= Table::load('Test');
		$this->clean_table('test');

		$data = array('name' => 'juan', 'last_name' => 'perez', 'age' => 6);
		$t->insert($data);
		$data = array('name' => 'jose', 'last_name' => 'perez', 'age' => 6);
		$t->insert($data);

		$res = $t->find(array('where' => "name='jacinto'"));
		$this->assertTrue(empty($res));
		$res = $t->find(array('where' => "last_name='perez'"));
		$this->assertEqual(sizeof($res), 2);
	}

	private function assertColumn($e, $o) {
		$this->assertEqual($e['Field'], $o->name);
		$this->assertEqual($e['Type'], $o->type);
		$this->assertEqual($e['Null'] == 'YES', $o->is_nulleable());
		$this->assertEqual($e['Key'] == 'PRI', $o->is_pk());
		$def = $e['Default'] == "NULL" ? null : $e['Default'];
		$this->assertEqual($e['Default'], $def);
		$this->assertEqual($e['Extra'], $o->extra);
	}
}
?>
