<?php
class Table {
	private static $cache = array();
	public $class_name;
	public $pk;
	public $columns = array();
	public $column_names = array();
	public $table_name;
		
	function __construct($model_class_name) {
		Validations::string_not_blank($model_class_name::$table_name,
						"La clase $model_class_name no tiene tabla seteada");
		$this->table_name = $model_class_name::$table_name;
		$this->class_name = $model_class_name;
		$this->load_metadata();
	}

	public static function load($model_class_name) 	{
	if (!isset(self::$cache[$model_class_name])) {
		self::$cache[$model_class_name] = new Table($model_class_name);
	}

		return self::$cache[$model_class_name];
	}


	public function insert(&$data) {
		$q = new QueryBuilder($this->table_name);
		$sql = $q->insert($data);
		Connection::instance()->query($sql);
		$data[$this->pk] = Connection::instance()->get_last_insert_id();
	}

	public function update(&$data, $where) {
		$q = new QueryBuilder($this->table_name);
		$sql = $q->update($data)->where($where);
		Connection::instance()->query($sql);
	}

	public function delete($where) {
		$q = new QueryBuilder($this->table_name);
		$sql = $q->delete()->where($where);
		Connection::instance()->query($sql);
	}

	public function find($options) {
		$sql = new QueryBuilder($this->table_name);
		
		if(isset($options['where']))		
			$sql->where($options['where']);
		if(isset($options['order']))		
			$sql->order($options['order']);

		if(isset($options['group']))		
			$sql->	group($options['group']);

		if(isset($options['having']))		
			$sql->having($options['having']);

		if(isset($options['limit']))		
			$sql->limit($options['limit']);

		Connection::instance()->query($sql);
		$list = array();

		while($row =Connection::instance()->fetch_row()) {
			$list[] = new $this->class_name($row,false);
		}

		return $list;
	}

	/*** PRIVADAS ***/

	private function load_metadata() {
		$rows = Connection::instance()
		->query("SHOW COLUMNS FROM $this->table_name")
		->fetch_rows();

		foreach($rows as $row) {
			$this->columns[$row['Field']] = Column::build_from_row($row);	
			$col = $this->columns[$row['Field']];
			if($col->is_pk())
				$this->pk = $col->name;
		}
		
		Validations::not_null($this->pk,"The table $this->table_name hasn't a primary key.");
		$this->column_names = array_keys($this->columns);
	}
}

?>
