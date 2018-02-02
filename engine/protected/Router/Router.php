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
	private $folder;
	private $controller;
	private $method;
	private $parameters;
	private $route;
	private $rules = [];
	private $local;
	private $names;
	private $url = "/";
    private $group;

	public static $verbs = ['GET', 'POST'];
	
	public function __construct() {
		$this->folder = null;
		$this->controller = null;
		$this->method = null;
		$this->parameters = null;
		$this->url = (!empty(registry()->request->get['r'])) ? registry()->request->get['r'] : "/";

		$this->loadRules();
		$this->route = new Route(registry());
		$this->getRules();

        $this->checkUrl();
	}

	public function initGroup($group)
    {
        return new RouteGroup($group, $this->url, $this->group);
    }

    public function checkUrl() {
        foreach ($this->names as $item) {
            if($this->route->check($this->url, $item['name'], $item['http_method'])){
                $this->local = $item['name'];
                return $this;
            }
        }
    }

	public function getRules(){

		foreach($this->rules as $item){

			if(empty($item['method']))
				$item['method'] = "any";

            if($item['group'] == "prefix")
                $item['url'] = $item['group_prefix']."/".$item['url'];
                $item['as'] = $item['group_prefix'].$item['as'];

            $this->route->addRegex($item['url'], $item['as']);
            $parameters[$item['as']] = $this->route->parse($this->url, $item['as']);

            if($item['group'] != "domain") {
                $params = $parameters[$item['as']];
            } else {
                $params = array_merge($item['group_parameters'], $parameters[$item['as']]);
            }

            $this->names[$item['as']]["http_method"] = $item['method'];
            $this->names[$item['as']]["name"] = $item['as'];
            $this->names[$item['as']]["rule"] = $item['url'];
            $this->names[$item['as']]["url"] = $this->url;
            $this->names[$item['as']]["parameters"] = $params;
            $this->names[$item['as']]["callback"] = $item['callback'];
            $this->names[$item['as']]["models"] = $item['models'];

            if(!empty($item['uses'])){
                $this->names[$item['as']]['controller'] = $this->getController($item['uses']);
                $this->names[$item['as']]['method'] = $this->getMethod($item['uses']);
                $this->names[$item['as']]['type'] = 'controller';
            } else {
                $this->names[$item['as']]['type'] = 'callback';
            }
		}

		return $this;
	}

	public function loadRules() {
		return require_once(APP_DIR . "routers.php");
	}

	public function getController($uses){
		preg_match_all("/(.*)@(.*)/", $uses, $preg);
		return $preg[1][0];
	}

	public function getMethod($uses){
		preg_match_all("/(.*)@(.*)/", $uses, $preg);
		return $preg[2][0];
	}

	public function route($name = null){
		if(empty($name)){
			return $this->names[$this->local];
		} else {
			return $this->names[$name];
		}
	}

	public function loadController($controllerName, $actionName, $parameters = [], $models = []){

		if(!empty($this->local)) {
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
				
				$controller = new $controllerClass(registry());
				
				if(is_callable(array($this->controller, $this->action))) {
					$this->action = $this->action;
				} else {
					return new Debug('Ошибка: Не удалось загрузить указанный метод ' . $this->action . '!');
				}

				if(!empty($models)) {
                    call_user_func_array("model", $models);
                }

				if(empty($parameters)) {
					return call_user_func(array($controller, $this->action));
				} else {
					return call_user_func_array(array($controller, $this->action), $parameters);
				}
			}

			return new Debug('Ошибка: Не удалось загрузить контроллер ' . $this->controller . '!');
		} else {
			return new HttpException(404, "Страница не найдена");
		}
	}

	public function make() {
		if($this->route()['callback']) {
			$callback = $this->route()['callback'];
			return registry()->response->output(call_user_func_array($callback, $this->route()['parameters']));
		}

		return $this->loadController(
		    $this->route()['controller'],
            $this->route()['method'],
            $this->route()['parameters'],
            $this->route()['models']
        );
	}

	public function getIndexLastRules() {
		return count($this->rules) - 1;
	}

	public function group($rule, $function)
    {
        $this->initGroup([
            "rule"  =>  $rule,
            "callback" =>   $function,
        ], $this->group);

        return $this;
    }

	public function post($url, $parameters = null, $callback = null) {

		if(is_array($parameters)) {
			$name = (!empty($parameters['as'])) ? $parameters['as'] : base64_encode($url)."post";
			$uses = (!empty($parameters['uses'])) ? $parameters['uses'] : null;
		} else {
			$name = base64_encode($url)."post";

			if(gettype($parameters) == string) {
				$uses = (!empty($parameters)) ? $parameters : null;
			} else {
				$callback = $parameters;
			}
		}

		$url = (!empty($url)) ? $url : "/";
        $this->rules[] = [
            "method"	=>	"post",
            "url"	=>	$url,
            "as"	=>	$name,
            "uses" => $uses,
            "callback" => $callback,
            "group" =>  $this->group['type'],
            "group_params" => $this->group['parameters'],
            "group_prefix" => $this->group['prefix'],
        ];

		return $this;
	}

	public function any($url, $parameters = null, $callback = null) {
		if(is_array($parameters)) {
			$name = (!empty($parameters['as'])) ? $parameters['as'] : base64_encode($url)."any";
			$uses = (!empty($parameters['uses'])) ? $parameters['uses'] : null;
		} else {
			$name = base64_encode($url)."any";

			if(gettype($parameters) == string) {
				$uses = (!empty($parameters)) ? $parameters : null;
			} else {
				$callback = $parameters;
			}
		}

		$url = (!empty($url)) ? $url : "/";

		$this->rules[] = [
		    "method"	=>	"any",
            "url"	=>	$url,
            "as"	=>	$name,
            "uses" => $uses,
            "callback" => $callback,
            "group" =>  $this->group['type'],
            "group_params" => $this->group['parameters'],
            "group_prefix" => $this->group['prefix'],
        ];

		return $this;
	}

	public function get($url, $parameters = null, $callback = null) {

		if(is_array($parameters)) {
			$name = (!empty($parameters['as'])) ? $parameters['as'] : base64_encode($url)."get";
			$uses = (!empty($parameters['uses'])) ? $parameters['uses'] : null;
		} else {
			$name = base64_encode($url)."get";

			if(gettype($parameters) == string) {
				$uses = (!empty($parameters)) ? $parameters : null;
			} else {
				$callback = $parameters;
			}
		}

		$url = (!empty($url)) ? $url : "/";
        $this->rules[] = [
            "method"	=>	"get",
            "url"	=>	$url,
            "as"	=>	$name,
            "uses" => $uses,
            "callback" => $callback,
            "group" =>  $this->group['type'],
            "group_params" => $this->group['parameters'],
            "group_prefix" => $this->group['prefix'],
        ];

		return $this;
	}

	public function name($name) {
		
		$this->rules[$this->getIndexLastRules()]['as']	=	$name;

		return $this;
	}

	public function uses($uses) {

		$this->rules[$this->getIndexLastRules()]['uses']	=	$uses;

		return $this;
	}

	public function model() {
        foreach (func_get_args() as $modelName) {
            $this->rules[$this->getIndexLastRules()]['models'][] = $modelName;
        }
    }

    public function alias($url)
	{
		$this->rules[] = $this->rules[$this->getIndexLastRules()];

		$this->rules[$this->getIndexLastRules()]['url'] = $url;
        $this->rules[$this->getIndexLastRules()]['as'] = base64_encode($url);

		return $this;
	}
}