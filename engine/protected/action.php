<?php
/*
* MyUCP
* File Version 4.0
* Date: 30.03.2015
* Developed by Maksa988
*/

class Action {
	private $registry;

	private $folder;
	private $controller;
	private $method;
	private $args;
	
	public function __construct($registry)
	{
		$this->registry = $registry;
	}
	
	public function make($action) {
		$this->folder = null;
		$this->controller = null;
		$this->method = null;
		$this->args = null;
		
		$action = preg_replace("/[^\w\d\s\/]/", '', $action);
		$parts = explode('/', $action);
		$parts = array_filter($parts);
		
		foreach($parts as $item) {
			$fullpath = APP_DIR . 'controllers' . $this->folder . '/' . $item;
			if(is_dir($fullpath)) {
				$this->folder .= '/' . $item;
				array_shift($parts);
				continue;
			}
			elseif(is_file($fullpath . '.php')) {
				$this->controller = $item;
				array_shift($parts);
				break;
			} else break;
		}
		
		// Проверка папки
		if(empty($this->folder)) {
			$this->folder = 'main';
		}

		// Проверка контроллера
		if(empty($this->controller)) {
			$this->controller = 'index';
		}
		
		// Получения метода
		if($c = array_shift($parts)) {
			$this->method = $c;
		} else {
			$this->method = 'index';
		}
		
		// Получение аргументов
		if(isset($parts[0])) {
			$this->args = $parts;
		}
	}
	
	public function go($commonEnable = false) {
		$controllerFile = APP_DIR . 'controllers/' . $this->folder . '/' . $this->controller . '.php';
		$controllerClass = $this->controller . 'Controller';
		
		if($this->folder != "common" || $commonEnable == true) { // Защита директории"common"
			if(is_readable($controllerFile)) {
				require_once($controllerFile);
				
				$controller = new $controllerClass($this->registry);
				
				if(is_callable(array($controller, $this->method))) {
					$this->method = $this->method;
				} else {
					$this->method = 'index';
				}
				
				if(empty($this->args)) {
					return call_user_func(array($controller, $this->method));
				} else {
					return call_user_func_array(array($controller, $this->method), $this->args);
				}
			}
		}
		exit('Ошибка: Не удалось загрузить контроллер ' . $this->controller . '!');
	}
}
?>
