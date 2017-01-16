<?php

class Cookie {

	private $name;
	private $value;
	private $hashedValue;
	private $time;

	public function __construct($name, $value = null, $time = null) {

		$this->name = $name;
		$this->value = $value;
		$this->hashedValue = $_COOKIE[$name];

		if($value != null) {
			$this->time = $time = ($time != null) ? $time : time() + 3600;
			$this->hashedValue = $value = $this->encodeValue($value, config()->app_key);

			return setcookie($name, $value, $time);
		}

		return $this;
	}

	public function unHashed() {
		return $this->decodeValue($this->hashedValue, config()->app_key);
	}

	public function getValue() {
		return $this->unHashed();
	}

	public function forever() {
		if($this->value != null) {
			$this->hashedValue = $this->encodeValue($this->value, config()->app_key);

			return setcookie($this->name, $this->hashedValue, time() + 157680000);
		}
	}

	private function encodeValue($source, $key) {
	    $res = '';   
	    for ($i = 0; $i < strlen($source); $i++ ) {
	        $char = ord($source[$i]);
	        if (strlen($key) > 0) {
	            $char = ord($key[ $i % strlen($key)]) ^ $char;
	        }
	        $res = $res . strtolower(substr("0".dechex( $char ),-2));
	    }
	    return $res;
	}

	function decodeValue($source, $key) {
	    $res = '';   
	    for ($i = 0; $i < strlen($source); $i+=2) {
	        $char = hexdec(substr($source,$i,2));
	        if (strlen($key) > 0) {
	            $char = ord($key[ ($i>>1) % strlen($key)]) ^ $char;
	        }
	        $res = $res . chr($char);
	    }
	    return $res;
	}
}
