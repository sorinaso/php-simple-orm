<?php
require_once("../SimpleORM.php");
require_once("TestUnit.php");


$table_sql = array();
$table_sql[] = 'DROP TABLE IF EXISTS test';
$table_sql[] = 
		'CREATE TABLE test (
		id INT NOT NULL AUTO_INCREMENT,
		PRIMARY KEY(id),
		name varchar(60) NOT NULL,
		last_name varchar(60) NOT NULL,
		age int
) ENGINE=MyISAM DEFAULT CHARSET=utf8';

$dbh = Connection::instance();
foreach($table_sql as $sql) {
	$dbh->query($sql);
}
Connection::$sql_logger = 'STDOUT';

require_once(dirname(__FILE__) . '/simpletest/autorun.php');

class SimpleORMTest extends UnitTestCase {
	protected function clean_table($table) {
		Connection::instance()->query("DELETE FROM $table");
	}

	protected function fetch_rows($sql) {
		return Connection::instance()->query($sql)->fetch_rows();
	}
}

?>
