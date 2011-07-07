<?php
class Validations {
		public static function array_valid_keys($array,$valid_keys, $msg) {
		foreach($array as $k => $v)
			static::fail_if(!array_key_exists($k, $valid_keys), $msg);
	}	

	public static function array_has_keys($array, $must_has, $msg) {
		if(is_array($must_has)) {
			foreach($must_has as $k)
				static::fail_if(!array_key_exists($k, $array),$msg);
		} elseif(is_string($must_has)) {
				static::fail_if(!array_key_exists($must_has, $array),$msg);
		}	else {
			throw new ArgumentError("Bad argument" . $must_has);
		}
	}

	public static function array_hasnt_keys($array, $not_must_has, $msg) {
		if(is_array($not_must_has)) {
			foreach($not_must_has as $k)
				static::fail_if(array_key_exists($k, $array),$msg);
		} elseif(is_string($not_must_has)) {
				static::fail_if(array_key_exists($not_must_has, $array),$msg);
		}	else {
			throw new ArgumentError("Bad argument" . $not_must_has);
		}
	}

	public static function is_hash(&$array, $msg)
	{
		static::fail_if(!is_array($array),$msg);

		$keys = array_keys($array);
		static::fail_if(!is_string($keys[0]), $msg);
	}

	public static function string_not_blank($str, $msg) {
		static::fail_if(!isset($str),$msg);
		$str = trim($str);
		static::fail_if(empty($str), $msg);
	}
	
	public static function not_null($var, $msg) {
		static::fail_if($var === null, $msg);		
	}

	private static function fail_if($cond, $msg) {
		if($cond) {
			throw new ValidationError($msg);		
		}
	}
}
?>
