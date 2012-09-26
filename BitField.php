<?php

/**
 * based on the BitField described in http://stackoverflow.com/questions/5319475/bitmask-in-php-for-settings
 */
class EtuDev_Util_BitField {
	const DEFAULT_VALUE = 0;

	private $value;

	public function __construct($value = null) {
		$this->load($value);
	}

	public function load($value = null) {
		$this->value = (int) (is_numeric($value) ? $value : static::DEFAULT_VALUE);
	}

	public function getValue() {
		return $this->value;
	}

	public function get($n) {
		return ($this->value & (1 << $n)) != 0;
	}

	public function set($n, $new = true) {
		$this->value = ($this->value & ~(1 << $n)) | ($new << $n);
	}

	public function clear($n) {
		$this->set($n, false);
	}

	public function applyMask($mask) {
		$this->value = $this->value & (int) $mask;
	}
}
