<?php
class QueryBuilder {
	private $connection;
	private $operation = 'SELECT';
	private $table;
	private $select = '*';
	private $joins;
	private $order;
	private $limit;
	private $offset;
	private $group;
	private $having;

	// for where
	private $where;
	private $where_values = array();

	// for insert/update
	private $data;
	private $sequence;

	function __construct($table) {
		$this->table = $table;		
	}

	public function where($where) {
		$this->where = $where;
		return $this;
	}

	public function order($order) {
		$this->order = $order;
		return $this;
	}

	public function group($group) {
		$this->group = $group;
		return $this;
	}

	public function having($having) {
		$this->having = $having;
		return $this;
	}

	public function limit($limit)
	{
		$this->limit = intval($limit);
		return $this;
	}

	public function offset($offset)
	{
		$this->offset = intval($offset);
		return $this;
	}

	public function select($select)
	{
		$this->operation = 'SELECT';
		$this->select = $select;
		return $this;
	}

	public function joins($joins)
	{
		$this->joins = $joins;
		return $this;
	}

	public function __toString()
	{
		return $this->to_s();
	}

	public function to_s()
	{
		$func = 'build_' . strtolower($this->operation);
		return $this->$func();
	}

	public function insert($hash)
	{
		Validations::is_hash($hash,'Inserting requires a hash.');

		$this->operation = 'INSERT';
		$this->data = $hash;

		return $this;
	}

	public function update($hash)
	{
		Validations::is_hash($hash, 'Updating requires a hash.');

		$this->operation = 'UPDATE';
		$this->data = $hash;
		return $this;
	}

	public function delete()
	{
		$this->operation = 'DELETE';
		return $this;
	}
	
	private function build_delete()
	{
		$sql = "DELETE FROM $this->table";

		if ($this->where)
			$sql .= " WHERE $this->where";

		return $sql;
	}

	private function build_insert()
	{
		$sql =	"INSERT INTO $this->table set ";
		$values = array();
		foreach ($this->data as $key => $value)
			$values[] = "$key = '$value'";

		return $sql . implode(',', $values);
	}

	private function build_select()
	{
		$sql = "SELECT $this->select FROM $this->table";

		if ($this->joins)
			$sql .= ' ' . $this->joins;

		if ($this->where)
			$sql .= " WHERE $this->where";

		if ($this->group)
			$sql .= " GROUP BY $this->group";

		if ($this->having)
			$sql .= " HAVING $this->having";

		if ($this->order)
			$sql .= " ORDER BY $this->order";

		if ($this->limit && $this->offset)
			$sql .= " LIMIT $this->offset, $this->limit";
		elseif ($this->limit) {
			$sql .= " LIMIT $this->limit";
		}
		return $sql;
	}

	private function build_update()
	{
		$sql = "UPDATE $this->table SET ";

		foreach ($this->data as $key => $value)
			$values[] = "$key = '$value'";

		$sql .= implode(',', $values);

		if ($this->where)
			$sql .= " WHERE $this->where";

		return $sql;
	}
}
?>
