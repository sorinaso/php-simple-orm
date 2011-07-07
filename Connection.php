<?php
class Connection {
	public $dbresult = null;

	public static $cfg;

	private static $db;

	private static $instance;
	static public $last_sql;
	static public $sql_logger;

	function __construct() {
		if(!isset(static::$db)) {

			static::$db = mysql_connect(static::$cfg['host'], 
										static::$cfg['username'], 
										static::$cfg['password']);

			mysql_select_db(static::$cfg['database']);
		}		
	}

	public static function connection() {
		return static::$db;
	}

	public static function database_name() {
		return static::$cfg['database'];
	}

	public static function instance() {
		if(!isset(static::$instance)) {
			
			static::$cfg = array(
			'host' 			=> 'localhost',
			'username' 	=> 'root',
			'password' 	=> '123456',
			'database' 	=> 'test');

			static::$instance = new Connection();		
		}
		
		return static::$instance;
	}

	public function fetch_objects($class_name) {
		$ret = array();
		while($row = mysql_fetch_assoc( $this->dbResult)) {
			$ret[] = new $class_name($row,true);
		}
		return $ret;
	}

	public function count_table_rows($table_name) {
		$res = $this->query("select count(*) as c from $table_name")->fetch_rows();			
		return (int)$res[0]['c'];
	}

	public function fetch_rows() {
		$ret = array();
		while($row = mysql_fetch_assoc( $this->dbResult )) {
			$ret[] = $row;
		}
		return $ret;
	}

	public function fetch_row() {
		return mysql_fetch_assoc( $this->dbResult );
	}
	
	public function get_last_insert_id() {
		return mysql_insert_id(static::connection());		
	}

	public function query($sql) {

		$this->dbResult = mysql_query($sql);

		if(!$this->dbResult) {
			throw new DBError("query: $sql\nerror:". mysql_error());
		}

		static::$last_sql = $sql;
		if(static::$sql_logger == 'STDOUT') { echo "$sql\n"; }

		return $this;
	}

	public function print_res() {
		print_r($this->dbResult);
	}
}
?>
