<?php
/*
* MyUCP
* File Version 4.0
* Date: 30.03.2015
* Developed by Maksa988
*/

class Config {
	private $data = array();
	
	public function __construct() {
		if(is_readable(ENGINE_DIR . 'configs/main.php')) {
			require_once(ENGINE_DIR . 'configs/main.php');
			$this->data = array_merge($this->data, $config);
			$this->loadConfigs($config['configs']);
			return true;
		}
		exit('Ошибка: Не удалось загрузить файл конфигурации!');
	}
	
	public function __set($key, $val){
		$this->data[$key] = $val;
	}
	
	public function __get($key){
		if(isset($this->data[$key])){
			return $this->data[$key];
		}
		return false;
	}
	
	public function loadConfigs($list){
		foreach($list as $config){
			if(is_readable(ENGINE_DIR . 'configs/'. $config .'.php')) {
				require_once(ENGINE_DIR . 'configs/'. $config .'.php');
				$this->data[$config] = (object) $$config;
				return true;
			}
			exit('Ошибка: Не удалось загрузить дополнительный файл конфигурации!');
		}
	}
}
?>
