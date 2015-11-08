<?php
/*
* MyUCP
*/

class Load {
	private $registry;

	public function __construct($registry) {
		$this->registry = $registry;
	}
	
	public function model($name){
		$modelClass = $name . 'Model';
		$modelPath = APP_DIR . 'models/' . $name . '.php';
		
		if(is_readable($modelPath)){
			require_once($modelPath);
			if(class_exists($modelClass)){
				$this->registry->$modelClass = new $modelClass($this->registry);
				return true;
			}
		}
		exit('Ошибка: Не удалось загрузить модель ' . $name . '!');
	}
	
	public function library($name){
		$libClass = $name . 'Library';
		$libPath = ENGINE_DIR . 'lib/' . $name . '.php';
		
		if(is_readable($libPath)){
			require_once($libPath);
			return true;
		}
		exit('Ошибка: Не удалось загрузить библиотеку ' . $name . '!');
	}
}
?>
