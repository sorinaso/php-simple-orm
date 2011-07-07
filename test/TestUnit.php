<?php
class TestUnit {
	protected function validate($expression) {
		$this->fail("The expression is false");
	}

	protected function assert_equal($val, $val2) {
		if($val != $val2) {
			$this->fail("$val expected but $val2 was");
		}
	}
	
	private function fail($expr) {
		throw new Exception($expr);
	}
}
?>
