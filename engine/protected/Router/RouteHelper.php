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
        $url = $this->getRedirectURL($this->rule, $params);
        return redirect($url);
    }

    public function getRedirectURL($rule, $params = [])
    {
        foreach ($params as $key => $value) {
            $rule = preg_replace('/\{(['. $key .']+):(.*?)\}/', $value, $rule);
        }

        return $rule;
    }
}