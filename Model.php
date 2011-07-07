<?php
/**
 * Esta clase representa un modelo en la base de datos.
 */
class Model {
	/**
	 * Attributos del modelo.
	 *
	 * @var array
	 */
	private $_attributes = null;

	/**
	 *	 Nombres de los atributos sucios.
	 *
	 * @var unknown
	 */
	private $_dirty = array();

	/**
	 * Nombre de la tabla a la que pertenece el modelo.
	 *
	 * @static
	 * @var string
	 */
	static $table_name = null;

	private $valid_find_conditions = 
		array('where','order','group','having','limit');

	public $errors = array();

	private $_new_record = true;
	/**
	 * Constructor.
	 *
	 * @param array $attributes Atributos del registro
	 * @param unknown $new_record = false Presente en la base de datos?
	 * @return void
	 */
	public function __construct(array $attributes,$new_record = true) {
		try {
			if(!$new_record) {
				Validations::array_has_keys(
				$attributes, static::$primary_key, "This object doesn't has primary key");
				$this->_pk = $attributes[static::$primary_key];	
			}

			if(isset($attributes[static::$primary_key]))
				unset($attributes[static::$primary_key]);

			$this->_attributes = $attributes;
			$this->_new_record = $new_record;
		} catch(ValidationError $e) {
			var_dump($this);throw $e;
		}
	}

	/**
	 * Realiza una busqueda.
	 *
	 * @param string $conditions condiciones de la consulta.
	 * @return array Lista de objetos modelo.
	 */
	public static function find($conditions) {
		Validations::array_valid_keys($conditions, $this->valid_find_conditions);
	}

	public function assign_attribute($name, $value) {
		$this->_attributes[$name] = $value;
		$this->set_dirty_flag($name);
	}

	/**
	 * Nos dice si el registro esta "sucio" osea si se cambio
	 * con respecto a la base de datos.
	 *
	 * @return boolean Esta sucio?
	 */
	public function is_dirty() { !empty($this->_dirty); }

	/**
	 * Marca un atributo como sucio(cambiado con respecto a la base de datos).
	 *
	 * @param string $name Nombre del atributo cambiado.
	 * @return void
	 */
	public function set_dirty_flag($name) {
		if(!array_key_exists($name, $this->_dirty)) {
			$this->_dirty[] = $name;
		}
	}

	/**
	 * Interceptor de gets busca en los atributos del modelo, 
	 * si el modelo no tiene el atributo que se pide devuelve
	 * la propiedad del objeto.
	 *
	 * @param unknown $property propiedad a obtener
	 * @return void
	 */
	public function __get($property){
		return array_key_exists($property, $this->_attributes) ?
		$this->_attributes[$property] : $this->$property;
	}

	/**
	 * Interceptor de setter si existe el atirbuto en el modelo
	 * lo setea sino setea la propiedad en el objeto.
	 *
	 * @param unknown $property Nombre de la propiedad a setear.
	 * @param unknown $value Valor a setear.
	 * @return void
	 */
	public function __set($property, $value) {
		if(array_key_exists($property, static::table()->column_names)) {
			$this->assign_attribute($property,$value);
			$this->set_dirty_flag($property);
		} else {
			$this->$property = $value;
		}
	}

	public function pk() { return $this->_pk; }

	private function error($field, $error) {
		$this->errors[$field] = $error;		
	}

	private function clean_errors() { $this->errors =array(); }

	public function validate() { }

	private function _validate() {
		$this->clean_errors();
		$this->validate();
		return empty($this->errors);
	}

	public function is_valid() { return $this->_validate(); }

	public function is_new_record() { return $this->_new_record; }

	public function get_attributes() { return $this->_attributes; }
	
	public function dirty_attributes() {
		if (!$this->__dirty)
		return null;

		$dirty = array_intersect_key($this->_attributes,$this->__dirty);
		return !empty($dirty) ? $dirty : null;
	}

	public function create($attributes) {
		$m = new Model($attributes);
		$m->save();
	}

	public function save($validate=true) {
		if($validate && !$this->is_valid()) { return false; }

		if($this->is_new_record()) {
			static::table()->insert($this->_attributes);
			$this->_new_record = false;
		}	else {
			if($this->is_dirty())
				static::table()->update($this->dirty_attributes());
		}

		return true;
	}

	private function &table() {
		return Table::load(get_class($this));
	}
}
?>
