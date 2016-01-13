<?php
/*
|--------------------------------------------------------------------------
| Маршрутизация
|--------------------------------------------------------------------------
|
| Основной класс для работы с маршрутами
|
*/

class Router {
	private $registry;

	private $folder;
	private $controller;
	private $method;
	private $parameters;
	private $route;
	private $rules;
	private $local;
	private $names;
	
	public function __construct($registry) {
		$this->folder = null;
		$this->controller = null;
		$this->method = null;
		$this->parameters = null;

		$this->registry = $registry;
		$this->rules = include_once(APP_DIR . "routers.php");
		$this->route = new Route;
		$this->getRules();
	}

	public function getRules(){
		foreach($this->rules as $item){
			$this->route->addRegex($item['url'], $item['as']);
			if($this->route->check($this->registry->request->get['action'], $item['as'])){
				$this->local = $item['as'];
				$parameters[$item['as']] = $this->route->parse($this->registry->request->get['action'], $item['as']);
			}

			$this->names[$item["as"]] = [
				"name" => $item['as'],
				"rule" => $item['url'],
				"url" => $this->registry->request->get['action'],
				"parameters" => $parameters[$item['as']],
				"callback"	=> $item['callback'],
			];

			if(!empty($item['uses'])){
				$this->names[$item['as']]['controller'] = $this->getController($item['uses']);
				$this->names[$item['as']]['method'] = $this->getMethod($item['uses']);
				$this->names[$item['as']]['type'] = 'controller';
			} else {
				$this->names[$item['as']]['type'] = 'callback';
			}
		}
	}

	public function getController($uses){
		preg_match_all("/(.*)@(.*)/", $uses, $preg);
		return $preg[1][0];
	}

	public function getMethod($uses){
		preg_match_all("/(.*)@(.*)/", $uses, $preg);
		return $preg[2][0];
	}

	public function route($name = ""){
		if(empty($name)){
			return $this->names[$this->local];
		} else {
			return $this->names[$name];
		}
	}

	public function loadControler($controllerName, $actionName, $parameters = []){

		if(strpos($controllerName, ".")){
			$controller = explode(".", $controllerName);
			$this->controller = array_shift(array_reverse($controller));
				array_pop($controller);
			$this->folder = implode("/", $controller);
		} else {
			$this->controller = $controllerName;
		}
			$this->action = $actionName;

		if(empty($this->folder)){
			$controllerFile = APP_DIR . 'controllers/' . $this->controller . '.php';
		} else {
			$controllerFile = APP_DIR . 'controllers/' . $this->folder . '/' . $this->controller . '.php';
		}
		$controllerClass = $this->controller;
		
		if(is_readable($controllerFile)) {
			require_once($controllerFile);
			
			$controller = new $controllerClass($this->registry);
			
			if(is_callable(array($this->controller, $this->action))) {
				$this->action = $this->action;
			} else {
				$this->action = 'index';
			}
			
			if(empty($parameters)) {
				return call_user_func(array($controller, $this->action));
			} else {
				return call_user_func_array(array($controller, $this->action), $parameters);
			}
		}
		new Debug('Ошибка: Не удалось загрузить контроллер ' . $this->controller . '!');
	}

	public function make() {
		if($this->route()['type'] == 'callback'){
			
			return eval($this->route()['callback']);
		}
		return $this->loadControler($this->route()['controller'], $this->route()['method'], $this->route()['parameters']);
	}
}
?>
