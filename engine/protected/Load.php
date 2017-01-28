<?php
/*
* MyUCP
*/

class Load {
	private $registry;

	public function __construct($registry) {
		$this->registry = $registry;
	}
	
	public function model(){
		$names = func_get_args();
		foreach($names[0] as $name){
			$modelClass = $name . 'Model';
			$modelPath = APP_DIR . 'models/' . $name . '.php';

			if(is_readable($modelPath)){
				require_once($modelPath);
				if(class_exists($modelClass)){
					$this->registry->$modelClass = new $modelClass($this->registry);
					$this->registry
						 ->$modelClass
						 ->table($this->registry->$modelClass->table);
				}
			} else {
				new Debug('Ошибка: Не удалось загрузить модель ' . $name . '!');
			}
		}

		return true;
	}
	
	public function library(){
		$names = func_get_args();
		foreach($names[0] as $name){
			$libClass = $name . 'Library';
			$libPath = ENGINE_DIR . 'lib/' . $name . '.php';
			
			if(is_readable($libPath)){
				require_once($libPath);
			} else {
				new Debug('Ошибка: Не удалось загрузить библиотеку ' . $name . '!');
			}
		}

		return true;
	}

	public function inject(){
		$names = func_get_args();
		foreach($names[0] as $name){
			if(class_exists($name)){
				$this->registry->$name = new $name($this->registry);
			}
		}

		return true;
	}
}