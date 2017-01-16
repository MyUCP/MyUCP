<?php
/*
|--------------------------------------------------------------------------
| Запросы
|--------------------------------------------------------------------------
*/
class Request {
	public $get = array();
	public $post = array();
	public $cookie = array();
	public $files = array();
	public $server = array();
	
	public function __construct() {
		$_GET = $this->clean($_GET);
		$_POST = $this->clean($_POST);
		$_REQUEST = $this->clean($_REQUEST);
		$_COOKIE = $this->clean($_COOKIE);
		$_FILES = $this->clean($_FILES);
		$_SERVER = $this->clean($_SERVER);
		
		$this->get = $_GET;
		$this->post = $_POST;
		$this->request = $_REQUEST;
		$this->cookie = $_COOKIE;
		$this->files = $_FILES;
		$this->server = $_SERVER;
	}
	
  	private static function clean($data) {
		if (is_array($data)) {
	  		foreach ($data as $key => $value) {
				unset($data[$key]);
				$data[self::clean($key)] = self::clean($value);
	  		}
		} else {
	  		$data = htmlspecialchars($data, ENT_COMPAT);
		}
		return $data;
	}

	public static function get($name) {
		return $_GET[$name];
	}

	public static function post($name) {
		return $_POST[$name];
	}

	public static function cookie($name, $value = null, $time = null) {
		return cookie($name, $value, $time);
	}

	public static function file($name) {
		return new File($name);
	}
}
?>
