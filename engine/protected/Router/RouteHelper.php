<?php

class RouteHelper
{
    public $http_method;
    public $name;
    public $url;
    public $rule;
    public $parameters;
    public $callback;
    public $type;
    public $controller;
    public $method;
    public $models;

    public function __construct($name = null)
    {
        $route = registry()->router->route($name);

        if(is_null($route)) {
            throw new Debug("Маршрута \"$name\" не существует");
        }

        $this->http_method = $route['http_method'];
        $this->name = $route['name'];
        $this->url = $route['url'];
        $this->rule = $route['rule'];
        $this->parameters = $route['parameters'];
        $this->callback = $route['callback'];
        $this->controller = $route['controller'];
        $this->method = $route['method'];
        $this->models = $route['models'];

        return $this;
    }

    public function redirect($params = [])
    {
        $url = $this->getRedirectURL($params);
        return redirect($url);
    }

    public function getRedirectURL($params = [])
    {
        $url = $this->rule;

        if(!empty($params)) {
            foreach ($params as $key => $value) {
                $url = preg_replace('/\{(['. $key .']+):(.*?)\}/', $value, $this->rule);
            }
        }

        return $url;
    }
}