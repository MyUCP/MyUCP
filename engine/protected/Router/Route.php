<?php
/*
|--------------------------------------------------------------------------
| Маршрутизация
|--------------------------------------------------------------------------
|
| Класс для разбора правил и передеачи их в другие параметры
|
*/

class Route extends Router {

	private $routeParams = [];
	private $regex;
	private $route;

	public function __construct() { }

	public function addRegex($route, $key){
		$regex = '/' . preg_replace('/\//', '\/', $route) .  '/';
	    if(preg_match_all('/\{([a-z]+):(.*?)\}/', $route, $preg)){
	      for($i = 0; $i < count($preg[0]); $i++){
	        $this->routeParams[] = $preg[1][$i];
	        $regex = str_replace($preg[0][$i], '(' . $preg[2][$i] . ')', $regex);
	      }
	    }
		$this->regex[$key] = $regex;
		$this->route[$key] = $route;
	}

	public function parse($route, $key) {
	   	if(preg_match($this->regex[$key], $route, $preg)){
	     	if(count($preg) > 1){
	       		return $this->regexResultsToParams($preg);
	     	}
	   	}
	    return [];
	}
	  
	private function regexResultsToParams($preg) {
	    $params = [];
	   	for($i = 1; $i < count($preg); $i++){
	    	$params[$this->routeParams[$i - 1]] = $preg[$i];
	   	}
	   	unset($this->routeParams);
	   	return $params;
	}

	public function check($route, $key) {
		return preg_match($this->regex[$key], $route);
 	}
}