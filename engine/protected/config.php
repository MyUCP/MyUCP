<?php
/*
* MyUCP
*/

class Config {
	private $data = [];
	
	public function __construct() {
		if(is_readable('./configs/main.php')) {
			$config = require('./configs/main.php');
			$this->data = array_merge($this->data, $config);
			$this->loadConfigs();
			return true;
		}
		new Debug('Ошибка: Не удалось загрузить файл конфигурации!');
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
	
	public function loadConfigs(){
		$configs = scandir("./configs");
		array_shift($configs);
		array_shift($configs);

		foreach($configs as $item){
			if($item != "main.php" && $item != "autoload_classes.php"){
				$a .= $item;
				if(is_readable('./configs/'. $item)) {
					$config = require_once('./configs/'. $item);
					$configName = substr($item, 0, -4);
					$this->data[$configName] = (object) $config;
				}
				new Debug('Ошибка: Не удалось загрузить дополнительный файл конфигурации!');
			}
		}
		return true;
	}
}
?>
