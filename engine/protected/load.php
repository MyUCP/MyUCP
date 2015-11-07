<?php
/*
* MyUCP
* File Version 4.0
* Date: 30.03.2015
* Developed by Maksa988
*/

class Load {
	private $registry;

	public function __construct($registry) {
		$this->registry = $registry;
	}
	

	public function viewLoad($name, $vars = array()){

		$file = THEME_DIR . $name . '.php';
		if(is_readable($file)){
			extract($vars);
	  		$content = include($file);
			
	  		return $content;
		}
		exit('Ошибка: Не удалось загрузить шаблон ' . $name . '!');
	}
	
	public function view($view = array(), $vars = array()) {
		foreach($view as $item) {
			$this->viewLoad($item, $vars);
		}
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
