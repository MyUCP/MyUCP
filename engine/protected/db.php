<?php
/*
* MyUCP
* File Version 4.0
* Date: 30.03.2015
* Developed by Maksa988
*/

class DB {
	private $driver;
	
	public function __construct($driver, $hostname, $username, $password, $database, $type) {
		$class = $driver . 'Driver';
		if(is_readable(ENGINE_DIR . 'protected/database/' . $driver . '.php')) {
			require_once(ENGINE_DIR . 'protected/database/' . $driver . '.php');
		} else {
			exit('Ошибка: Не удалось загрузить драйвер базы данных ' . $driver . '!');
		}
		$this->driver = new $class($hostname, $username, $password, $database, $type);
	}
		
  	public function query($sql) {
		return $this->driver->query($sql);
  	}
	
	public function escape($value) {
		return $this->driver->escape($value);
	}
	
  	public function countAffected() {
		return $this->driver->countAffected();
  	}

  	public function getLastId() {
		return $this->driver->getLastId();
  	}
  	public function getCount() {
		return $this->driver->getCount();
  	}
}
?>
