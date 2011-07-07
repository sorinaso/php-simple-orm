<?php
class Column {
	public $name;
	public $type;
	public $nulleable;
	public $key;
	public $default;
	public $extra;

	public static function build_from_row($row) {
		$ret = new Column();
		$ret->name 			= $row['Field'];
		$ret->type 			= $row['Type'];
		$ret->nulleable 	= $row['Null'];
		$ret->key 				= $row['Key'];
		$ret->default 		= $row['Default'] == "NULL" ? null : $row['Default'];
		$ret->extra 			= $row['Extra'];

		return $ret;
	}

	public function is_pk() { 	return $this->key == 'PRI'; 	}

	public function is_nulleable() { return $this->nulleable == 'YES'; }
}
?>
